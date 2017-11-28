<?php
/*	Project:	EQdkp-Plus
 *	Package:	Awards Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2017 EQdkp-Plus Developer Team
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
		$intAssID		= $this->in->get('aid', 0);
		$intAssDate		= $this->time->fromformat($this->in->get('date', '1.1.1970'), 1);
		$intAchID		= $this->in->get('achievment', 0);
		
		$blnAchActive	= $this->pdh->get('awards_achievements', 'active', array($intAchID));
		$fltAchDKP		= $this->pdh->get('awards_achievements', 'dkp', array($intAchID));
		$arrAchName		= unserialize( $this->pdh->get('awards_achievements', 'name', array($intAchID)) );
		$strAchName		= $this->user->lang('aw_achievement').': '.$arrAchName[$this->config->get('default_lang')];
		$intAchEventID	= $this->pdh->get('awards_achievements', 'event_id', array($intAchID));
		
		//check form correct filled
		foreach($this->in->getArray('members','int') as $member) {
			$arrAdjUserIDs[] = (int)$member;
		}
		if(empty($arrAdjUserIDs)) $missing[] = $this->user->lang('members');
		if(!empty($missing)) return;
		
		if($intAssID){ //update Assignment
			$strAdjGK = $this->pdh->get('awards_assignments', 'adj_group_key', array($intAssID));
			
			$arrAdjIDs = $this->pdh->put('adjustment', 'update_adjustment', array($strAdjGK, $fltAchDKP, $strAchName, $arrAdjUserIDs, $intAchEventID, 0, $intAssDate, false, false));
			if($arrAdjIDs){
				$arrAssIDs = $this->pdh->put('awards_assignments', 'update', array($arrAdjIDs, $intAchID, $strAdjGK, $intAssDate));
				if($arrAssIDs){ $this->pdh->process_hook_queue(); $blnResult = true; }
				else{ $this->pdh->put('adjustment', 'delete_adjustments_by_group_key', array($strAdjGK)); }
				
			}else{ $blnResult = false; }
			
		}else{ //add Assignment
			$arrAssIDs = $this->awards->add_assignment($intAchID, $arrAdjUserIDs, $intAssDate);
			if($arrAssIDs){ $blnResult = true; }
			else{ $blnResult = false; }
		}
		
		//output Message
		if ($blnResult){
			foreach($arrAdjUserIDs as $userid) $arrusernames[] = $this->pdh->get('member', 'name', array($userid));
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
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $intAssID) {
				$strAdjGK = $this->pdh->get('awards_assignments', 'adj_group_key', array($intAssID));
				$arrAssIDs = $this->pdh->get('awards_assignments', 'ids_of_adj_group_key', array($strAdjGK));
				
				if($this->pdh->put('adjustment', 'delete_adjustments_by_group_key', array($strAdjGK)))
					foreach($arrAssIDs as $intAssignID)
						$retu = $this->pdh->put('awards_assignments', 'delete', array($intAssignID));
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
		$intAssID		= $this->in->get('aid', 0);
		$intAssDate		= $this->pdh->get('awards_assignments', 'date', [$intAssID]);
		
		//fetch achievements for select
		$achievements			= array();
		$achievement_ids		= $this->pdh->get('awards_achievements', 'id_list');
		foreach($achievement_ids as $aid)
			$achievements[$aid]	= $this->user->multilangValue( $this->pdh->get('awards_achievements', 'name', [$aid]) );
		
		//pre_select achievement for select
		$achievement = $this->pdh->get('awards_assignments', 'achievement_id', [$intAssID]);
		
		//fetch members for select
		$members = $this->pdh->aget('member', 'name', 0, [$this->pdh->sort($this->pdh->get('member', 'id_list', [false, true, false]), 'member', 'name', 'asc')]);
		
		//pre_select members for select
		$strAdjGK  = $this->pdh->get('awards_assignments', 'adj_group_key', [$intAssID]);
		$arrAdjIDs = $this->pdh->get('adjustment', 'ids_of_group_key', [$strAdjGK]);
		foreach($arrAdjIDs as $intAdjID)
			$arrAdjUserIDs[] = $this->pdh->get('adjustment', 'member', [$intAdjID]);
		
		
		$this->tpl->assign_vars([
			'AID' => $intAssID,
			'DD_ACHIEVEMENT' => (new hdropdown('achievment', array('options' => $achievements, 'value' => (isset($achievement) ? $achievement : ''), 'name', array($intAssID))))->output(),
			'DATE'			 => $this->jquery->Calendar('date', $this->time->user_date( (is_int($intAssDate) ? $intAssDate : $this->time->time), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
			'MEMBERS'		 => $this->jquery->MultiSelect('members', $members, ((isset($arrAdjUserIDs)) ? $arrAdjUserIDs : ''), array('width' => 350, 'filter' => true)),
		]);
		
		$strPageTitle = (($intAssID) ? $this->user->lang('aw_edit_assignment') : $this->user->lang('aw_add_assignment'));
		
		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars([
			'page_title'		=> $strPageTitle,
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/manage_assignments_edit.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('awards').': '.$this->user->lang('aw_manage_assignments'), 'url'=>$this->root_path.'plugins/awards/admin/manage_assignments.php'.$this->SID],
				['title'=>$strPageTitle, 'url'=>' '],
			],
			'display'			=> true
		]);
	}


	/**
	  * Display
	  * display main page
	  */
	public function display(){
		$arrUserSettings = $this->pdh->get('user', 'plugin_settings', array($this->user->id));
		$arrUserSettings['aw_admin_pagination'] = (isset($arrUserSettings['aw_admin_pagination']))? $arrUserSettings['aw_admin_pagination'] : 100;
		
		$view_list = $this->pdh->aget('awards_assignments', 'adj_group_key', 0, array($this->pdh->get('awards_assignments', 'id_list', array())));
		$view_list = array_flip($view_list);
		
		$hptt_page_settings = array(
			'name'					=> 'hptt_aw_admin_manage_awards',
			'table_main_sub'		=> '%intAssignmentID%',
			'table_subs'			=> array('%intAssignmentID%', '%link_url%', '%link_url_suffix%'),
			'page_ref'				=> 'manage_assignments.php',
			'show_numbers'			=> true,
			'show_select_boxes'		=> true,
			'selectboxes_checkall'	=> true,
			'show_detail_twink'		=> false,
			'table_sort_dir'		=> 'desc',
			'table_sort_col'		=> 0,
			'table_presets'			=> array(
				array('name' => 'awards_assignments_date',		'sort' => true, 'th_add' => 'width="40"', 'td_add' => 'style="text-align:center"'),
				array('name' => 'awards_assignments_name',		'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_assignments_m4agk4aid', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_assignments_points',	'sort' => true, 'th_add' => 'width="20"', 'td_add' => 'style="text-align:right"'),
				array('name' => 'awards_assignments_dkp',		'sort' => true, 'th_add' => 'width="20"', 'td_add' => 'style="text-align:right"'),
			),
		);
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->root_path.'plugins/awards/admin/manage_assignments.php', '%link_url_suffix%' => ''));
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');
		
		//footer
		$item_count = count($view_list);
		$strfootertext = sprintf($this->user->lang('aw_listassign_footcount'), $item_count, $arrUserSettings['aw_admin_pagination']);
		
		$this->confirm_delete($this->user->lang('aw_confirm_delete_assignment'));

		$this->tpl->assign_vars(array(
			'ASSIGNMENTS_LIST'	=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $arrUserSettings['aw_admin_pagination'], $strfootertext),
			'PAGINATION'		=> generate_pagination('manage_assignments.php'.$sort_suffix, $item_count, $arrUserSettings['aw_admin_pagination'], $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count())
		);
	
	// -- EQDKP ---------------------------------------------------------------
	$this->core->set_vars([
			'page_title'		=> $this->user->lang('aw_manage_assignments'),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/manage_assignments.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('awards').': '.$this->user->lang('aw_manage_assignments'), 'url'=>' '],
			],
			'display'			=> true,
		]);
	}


}
registry::register('awards_manage_assignments');

?>