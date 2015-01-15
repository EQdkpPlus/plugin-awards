<?php
/*	Project:	EQdkp-Plus
 *	Package:	Awards Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'awards');

$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path.'common.php');


/*+----------------------------------------------------------------------------
  | awards_manage_assignments
  +--------------------------------------------------------------------------*/
class awards_manage_assignments extends page_generic
{
	/**
	  * Constructor
	  */
	public function __construct(){
		if (!$this->pm->check('awards', PLUGIN_INSTALLED))
			message_die($this->user->lang('aw_plugin_not_installed'));
		
		$this->user->check_auth('a_awards_manage');
		
		$handler = array(
			'save'		=> array('process' => 'save', 'check' => 'a_awards_manage', 'csrf' => true),
			'aid'		=> array('process' => 'edit', 'check' => 'a_awards_manage'),
		);
		parent::__construct(false, $handler, array('manage_assignments', 'name'), null, 'selected_ids[]');
		$this->process();
	}


	/**
	  * Save
	  * save the assignment
	  */
	public function save(){
		$id				= $this->in->get('aid', 0);
		$intAssDate		= $this->in->get('date', 0);
		$intAchID		= $this->in->get('achievment', 0);
		
		$fltAchDKP		= $this->pdh->get('awards_achievements', 'dkp', array($intAchID));
		$strAchName		= $this->user->lang('aw_achievement').': '.$this->pdh->get('awards_achievements', 'name', array($intAchID));
		$intAchEventID	= $this->pdh->get('awards_achievements', 'event_id', array($intAchID));
		
		//check form correct filled
		foreach($this->in->getArray('members','int') as $member) {
			$intUserID[] = (int) $member;
		}
		if(empty($intUserID)){ $missing[] = $this->user->lang('members'); }
		if(!empty($missing)){ return; }
		
		
		if ($id){ //update Assignment
			$strAdjGK = $this->pdh->get('awards_assignments', 'adj_group_key', array($id));
			$arrAdjID = $this->pdh->put('adjustment', 'update_adjustment', array($strAdjGK, $fltAchDKP, $strAchName, $intUserID, $intAchEventID, 0, $intAssDate));
			
			if($arrAdjID[0]){
				$this->pdh->process_hook_queue();
				$strAdjGK  = $this->pdh->get('adjustment', 'group_key', array($arrAdjID[1]));
				$strAdjID = serialize($arrAdjID);
				
				if ($this->pdh->put('awards_assignments', 'update', array($id, $intAssDate, $intAchID, $strAdjID, $strAdjGK))){
					$blnResult = true;
				
				} else { $blnResult = false; /* DELETE or BACKUP if add_assignment failed */ }
			} else { $blnResult = false; }
			
		} else { //add Assignment
			$arrAdjID = $this->pdh->put('adjustment', 'add_adjustment', array($fltAchDKP, $strAchName, $intUserID, $intAchEventID, 0, $intAssDate));
			
			if($arrAdjID > 0){
				$this->pdh->process_hook_queue();
				$strAdjGK = $this->pdh->get('adjustment', 'group_key', array($arrAdjID[0]));
				$strAdjID = serialize($arrAdjID);
				
				if ($this->pdh->put('awards_assignments', 'add', array($intAssDate, $intAchID, $strAdjID, $strAdjGK))){
					$blnResult = true;
				
				} else { $blnResult = false; $this->pdh->put('adjustment', 'delete_adjustments_by_group_key', array($strAdjGK)); }
			} else { $blnResult = false; }
		}
		
		//output Message
		if ($blnResult){
			$this->pdh->process_hook_queue();
			
			foreach($intUserID as $userid) $arrusernames[] = $this->pdh->get('member', 'name', array($userid));
			$this->core->message(sprintf( $this->user->lang('aw_assign_success'), $strAchName, implode(', ',$arrusernames) ), $this->user->lang('success'), 'green');
		} else {
			$this->core->message(sprintf( $this->user->lang('aw_assign_nosuccess'), $strAchName ), $this->user->lang('error'), 'red');
		}
		
		$this->display();
	}


	/**
	  * Delete
	  * delete selected assignments
	  */
	public function delete(){
		$arrAdjID = array();
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $id) {
				$strAdjGK = $this->pdh->get('awards_assignments', 'adj_group_key', array($id));
				
				if($this->pdh->put('adjustment', 'delete_adjustments_by_group_key', array($strAdjGK)))
					$retu = $this->pdh->put('awards_assignments', 'delete', array($id));
			}
		}

		if(!empty($retu)) {
			$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => $this->user->lang('aw_del_assign'), 'color' => 'green');
			$this->core->messages($messages);
		}
		
		$this->pdh->process_hook_queue();
	}


	/**
	  * Edit Page
	  * display edit page
	  */	
	public function edit(){
		$id				= $this->in->get('aid', 0);
		$intAssDate		= $this->pdh->get('awards_assignments', 'date', array($id));
		
		//fetch achievements for select
		$achievements			= array();
		$achievement_ids		= $this->pdh->get('awards_achievements', 'id_list');
		foreach($achievement_ids as $aid) {
			$achievements[$aid] = $this->pdh->get('awards_achievements', 'name', array($aid));
		}
		
		//fetch members for select
		$members = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($this->pdh->get('member', 'id_list', array(false,true,false)), 'member', 'name', 'asc')));
		
		$this->tpl->assign_vars(array(
			'AID' => $id,
			'DD_ACHIEVEMENT' => new hdropdown('achievment', array('options' => $achievements, 'value' => ((isset($achievements)) ? $achievements : ''), 'name', array($id))),
			'DATE'			 => $this->jquery->Calendar('date', $this->time->user_date(((isset($intAssDate)) ? $intAssDate : $this->time->time), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
			'MEMBERS'		 => $this->jquery->MultiSelect('members', $members, ((isset($intUserID)) ? $intUserID : ''), array('width' => 350, 'filter' => true)),
		));
		
		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'		=> (($id) ? $this->user->lang('aw_add_assignment').': '.$this->user->lang('aw_add_assignment') : $this->user->lang('aw_add_assignment')),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/manage_assignments_edit.html',
			'display'			=> true)
		);
	}


	/**
	  * Display
	  * display main page
	  */
	public function display(){
		$view_list = $this->pdh->get('awards_assignments', 'id_list', array());
		$hptt_page_settings = array(
			'name'					=> 'hptt_aw_admin_manage_awards',
			'table_main_sub'		=> '%intAssignmentID%',
			'table_subs'			=> array('%intAssignmentID%', '%link_url%', '%link_url_suffix%'),
			'page_ref'				=> 'manage_assignments.php',
			'show_numbers'			=> false,
			'show_select_boxes'		=> true,
			'selectboxes_checkall'	=> true,
			'show_detail_twink'		=> false,
			'table_sort_dir'		=> 'asc',
			'table_sort_col'		=> 0,
			'table_presets'			=> array(
				array('name' => 'awards_assignments_id',			 'sort' => false, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_assignments_date',			 'sort' => false, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_assignments_achievement_id', 'sort' => false, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_assignments_adj_id',		 'sort' => false, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_assignments_adj_group_key',  'sort' => false, 'th_add' => '', 'td_add' => ''),
			),
		);
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->root_path.'plugins/awards/admin/manage_assignments.php', '%link_url_suffix%' => ''));
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');
		
		//footer
		$item_count = count($view_list);
		$strfootertext = sprintf($this->user->lang('listassign_footcount'), $adj_count, $this->user->data['user_alimit']);
		
		$this->confirm_delete($this->user->lang('aw_confirm_delete_assignment'));

		$this->tpl->assign_vars(array(
			'ASSIGNMENTS_LIST'	=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_alimit'], $strfootertext),
			'PAGINATION'		=> generate_pagination('manage_assignments.php'.$sort_suffix, $adj_count, $this->user->data['user_alimit'], $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count())
		);
	
	// -- EQDKP ---------------------------------------------------------------
	$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('aw_manage_assignments'),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/manage_assignments.html',
			'display'			=> true)
		);
	}


}
registry::register('awards_manage_assignments');

?>