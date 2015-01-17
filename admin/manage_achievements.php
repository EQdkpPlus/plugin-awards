<?php
/*	Project:	EQdkp-Plus
 *	Package:	Awards  Plugin
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
  | awards_manage_achievements
  +--------------------------------------------------------------------------*/
class awards_manage_achievements extends page_generic
{
	/**
	  * Constructor
	  */
	public function __construct(){
		if (!$this->pm->check('awards', PLUGIN_INSTALLED))
			message_die($this->user->lang('aw_plugin_not_installed'));
		
		$this->user->check_auth('a_awards_manage');
		
		$handler = array(
			'save'		=> array('process' => 'save', 'check' => 'a_awards_add', 'csrf' => true),
			'aid'		=> array('process' => 'edit', 'check' => 'a_awards_add'),
		);
		parent::__construct(false, $handler, array('manage_achievements', 'name'), null, 'selected_ids[]');
		$this->process();
	}

private $hmultilangName = '';
private $hmultilangDesc = '';

	private function init_hmultilang($id = false){
		if($id){
			$this->hmultilangName = new htextmultilang('name', array('value' => $this->user->multilangValue(unserialize($this->pdh->get('awards_achievements', 'name', array($id)))), 'size' => 30, 'required' => true));
			$this->hmultilangDesc = new htextareamultilang('description', array('value' => $this->user->multilangValue(unserialize($this->pdh->get('awards_achievements', 'description', array($id)))), 'rows' => '3', 'cols' => '50'));
			return true;
		} else {
			$this->hmultilangName = new htextmultilang('name', array('size' => 30, 'required' => true));
			$this->hmultilangDesc = new htextareamultilang('description', array('rows' => '3', 'cols' => '50'));
			return false;
		}
	}


	/**
	  * Save
	  * save the achievement
	  */
	public function save(){	
		$id 				= $this->in->get('aid', 0);
		
		$arrAchName			= $this->hmultilangName->_inpval();
	d($arrAchName);
	
		/*$arrAchName			= $this->in->getArray('name');
		$strAchName			= serialize($arrAchName);
		$test = $this->hmultilang()->_inpval();*/
	
		$intAchSortID		= $this->in->get('sort_id', 99999999);
		$blnAchActive		= $this->in->get('active_state', 1);
		$blnAchSpecial		= $this->in->get('special_state', 1);
		$intAchPoints		= $this->in->get('points', 10);
		$strAchIcon			= $this->in->get('icon', 'default.svg');
		$arrAchIconColors	= array();
		$strAchModule		= $this->in->get('module');
		$fltAchDKP			= $this->in->get('dkp', 0);
		$intMDKP			= $this->in->getArray('mdkp2event', 'int');
		
		
		if ($id){ //update Achievement
			$intEventID = $this->pdh->get('awards_achievements', 'event_id', array($id));
			if($this->pdh->put('event', 'update_event', array($intEventID, $strAchName, 0, ''))){
				if($this->pdh->put('multidkp', 'add_multidkp2event', array($intEventID, $intMDKP))){
					$blnResult = $this->pdh->put('awards_achievements', 'update', array($id, $strAchName, $strAchDescription, $intAchSortID, $blnAchActive, $blnAchSpecial, $intAchPoints, $strAchIcon, $arrAchIconColors, $strAchModule, $fltAchDKP, $intEventID));
				
				} else { $blnResult = false; }
			} else { $blnResult = false; }
		
		} else { //add Achievement
			$intEventID = $this->pdh->put('event', 'add_event', array($strAchName, 0, ''));
			if($intEventID > 0){
				if($this->pdh->put('multidkp', 'add_multidkp2event', array($intEventID, $intMDKP))){
					$blnResult = $this->pdh->put('awards_achievements', 'add', array($strAchName, $strAchDescription, $blnAchActive, $blnAchSpecial, $intAchPoints, $strAchIcon, $arrAchIconColors, $strAchModule, $fltAchDKP, $intEventID));
				
				} else { $blnResult = false; $this->pdh->put('event', 'delete_event', array($intEventID)); }
			} else { $blnResult = false; }
		}
		
		//output Message
		if ($blnResult){
			$this->pdh->process_hook_queue();
			$this->core->message(sprintf( $this->user->lang('aw_add_success'), $strAchName ), $this->user->lang('success'), 'green');
		} else {
			$this->core->message(sprintf( $this->user->lang('aw_add_nosuccess'), $strAchName ), $this->user->lang('error'), 'red');
		}
		
		$this->display();
	}


	/**
	  * Delete
	  * delete selected achievements
	  */
	public function delete(){
		$retu = array();
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $id) {
				$pos[] = stripslashes($this->pdh->get('awards_achievements', 'name', array($id)));
				$intAchEventID = $this->pdh->get('awards_achievements', 'event_id', array($id));
				
				if($this->pdh->put('event', 'delete_event', array($intAchEventID)))
					$retu[$id] = $this->pdh->put('awards_achievements', 'delete', array($id));
			}
		}

		if(!empty($pos)) {
			$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $pos), 'color' => 'green');
			$this->core->messages($messages);
		}
		
		$this->pdh->process_hook_queue();
	}


	/**
	  * Edit Page
	  * display edit page
	  */
	public function edit(){
		$id = $this->in->get('aid', 0);
		$test = $this->init_hmultilang($id);
	d($test);
		
		$arrAchName			= $this->hmultilangName->_inpval();
	d($arrAchName);
		$arrAchDesc			= $this->hmultilangDesc->_inpval();
	d($arrAchDesc);
	
	
	$aaa = new htextmultilang('name', array('value' => $this->user->multilangValue(unserialize($this->pdh->get('awards_achievements', 'name', array($id)))), 'size' => 30, 'required' => true));
	
	$bbb = $aaa->_inpval();
	d($bbb);
		
		
		
		// Adjustment Module für den Cron
		$arrAdjDropdown = array(
			NULL => $this->user->lang('aw_cron_module_0'),
			'cron_module_1' => $this->user->lang('aw_cron_module_1'),
			'cron_module_2' => $this->user->lang('aw_cron_module_2')
		);
		
		if ($id){
			$this->tpl->assign_vars(array(
				'ML_NAME'			=> $aaa,#$this->hmultilangName,
				'ML_DESCRIPTION'	=> $this->hmultilangDesc,
				'R_ACTIVE_STATE'	=> new hradio('active_state', array('options' => array(1 => $this->user->lang('yes'), 0 => $this->user->lang('no')), 'value' => $this->pdh->get('awards_achievements', 'active', array($id)))),
				'R_SPECIAL_STATE'	=> new hradio('special_state', array('options' => array(1 => $this->user->lang('published'), 0 => $this->user->lang('not_published')), 'value' => $this->pdh->get('awards_achievements', 'special', array($id)))),
				'SPINNER_POINTS' 	=> new hspinner('points', array('value' =>  ($this->pdh->get('awards_achievements', 'points', array($id))), 'max'  => 99999, 'min'  => 0, 'step' => 5, 'onlyinteger' => true)),
				'SPINNER_DKP'		=> new hspinner('dkp', array('value' =>  ($this->pdh->get('awards_achievements', 'dkp', array($id))), 'max'  => 99999, 'min'  => -99999, 'step' => 5)),
				'DD_MODULE'			=> new hdropdown('module', array('options' => $arrAdjDropdown, 'value' => $this->pdh->get('awards_achievements', 'module', array($id)))),
				'MDKP2EVENT'		=> $this->jquery->Multiselect('mdkp2event', $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list'))), $event['mdkp2event']),
			));
		} else {
			$this->tpl->assign_vars(array(
				'ML_NAME'			=> $this->hmultilangName,
				'ML_DESCRIPTION'	=> $this->hmultilangDesc,
				'R_ACTIVE_STATE'	=> new hradio('active_state', array('options' => array(1 => $this->user->lang('yes'), 0 => $this->user->lang('no')), 'value' => 1)),
				'R_SPECIAL_STATE'	=> new hradio('special_state', array('options' => array(1 => $this->user->lang('published'), 0 => $this->user->lang('not_published')), 'value' => 1)),
				'SPINNER_POINTS'	=> new hspinner('points', array('value' =>  10, 'max'  => 99999, 'min'  => 0, 'step' => 5, 'onlyinteger' => true)),	
				'SPINNER_DKP'		=> new hspinner('dkp', array('value' =>  0, 'max'  => 99999, 'min'  => -99999, 'step' => 5)),
				'DD_MODULE'			=> new hdropdown('module', array('options' => $arrAdjDropdown, 'value' => NULL)),
				'MDKP2EVENT'		=> $this->jquery->Multiselect('mdkp2event', $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list'))), $event['mdkp2event']),
			));
		}

		// Get Icons
		$icon_folder = $this->root_path.'plugins/awards/images';
		$files = scandir($icon_folder);
		$ignorefiles = array('.', '..', 'index.html', '.tmb');
		
		$icons = array();
		foreach($files as $file) {
			if(!in_array($file, $ignorefiles)) $icons[] = $icon_folder.'/'.$file;
		}
		
		$icon_folder = $this->pfh->FolderPath('images', 'awards');
		$files = scandir($icon_folder);
		$ignorefiles = array('.', '..', 'index.html', '.tmb');
		
		foreach($files as $file) {
			if(!in_array($file, $ignorefiles)) $icons[] = $icon_folder.'/'.$file;
		}

		$num = count($icons);
		$fields = (ceil($num/6))*6;
		$i = 0;

		while($i<$fields)
		{
			$this->tpl->assign_block_vars('files_row', array());
			$this->tpl->assign_var('ICONS', true);
			$b = $i+6;
			
			for($i; $i<$b; $i++){
			$icon = (isset($icons[$i])) ? $icons[$i] : '';
			$this->tpl->assign_block_vars('files_row.fields', array(
					'NAME'		=> pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION),
					'CHECKED'	=> (isset($award['icon']) AND pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION) == $award['icon']) ? ' checked="checked"' : '',
					'IMAGE'		=> "<img src='".$icon."' alt='".$icon."' width='48px' style='eventicon' />",
					'CHECKBOX'	=> ($i < $num) ? true : false)
				);
			}
		}
		
		// Icon Upload
		$this->jquery->fileBrowser('all', 'image', $this->pfh->FolderPath('images','awards', 'absolute'), array('title' => $this->user->lang('aw_upload_icon'), 'onclosejs' => '$(\'#eventSubmBtn\').click();'));

		$this->tpl->assign_vars(array(
			'AID' => $id,
		));
		
		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'		=> (($id) ? $this->user->lang('aw_add_achievement').': '.$this->pdh->get('awards_achievements', 'name', array($id)) : $this->user->lang('aw_add_achievement')),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/manage_achievements_edit.html',
			'display'			=> true)
		);
	}


	/**
	  * Display
	  * display all achievements
	  */
	public function display() {
		$this->tpl->add_js("
			$(\"#article_categories-table tbody\").sortable({
				cancel: '.not-sortable, input, tr th.footer, th',
				cursor: 'pointer',
			});
		", "docready");
		
		$view_list = $this->pdh->get('awards_achievements', 'id_list', array());
		$hptt_page_settings = array(
			'name'					=> 'hptt_aw_admin_manage_achievements',
			'table_main_sub'		=> '%intAchievementID%',
			'table_subs'			=> array('%intAchievementID%', '%link_url%', '%link_url_suffix%'),
			'page_ref'				=> 'manage_achievements.php',
			'show_numbers'			=> false,
			'show_select_boxes'		=> true,
			'selectboxes_checkall'	=> true,
			'show_detail_twink'		=> false,
			'table_sort_dir'		=> 'asc',
			'table_sort_col'		=> 0,
			'table_presets'			=> array(
				array('name' => 'awards_achievements_sort_id', 'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
				array('name' => 'awards_achievements_active',  'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
				array('name' => 'awards_achievements_special', 'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
				array('name' => 'awards_achievements_icon',	   'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
				array('name' => 'awards_achievements_name',	   'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_achievements_description', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_achievements_points',  'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
				array('name' => 'awards_achievements_module',  'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
				array('name' => 'awards_achievements_dkp',  'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
			),
		);
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->root_path.'plugins/awards/admin/manage_achievements.php', '%link_url_suffix%' => ''));
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');
		
		$item_count = count($view_list);
		$strfootertext = sprintf($this->user->lang('listachiev_footcount'), $adj_count, $this->user->data['user_alimit']);
		
		$this->confirm_delete($this->user->lang('aw_confirm_delete_achievement'));

		$this->tpl->assign_vars(array(
			'ACHIEVEMENTS_LIST'	=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_alimit'], $strfootertext),
			'PAGINATION'		=> generate_pagination('manage_achievements.php'.$sort_suffix, $adj_count, $this->user->data['user_alimit'], $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count()
		));

		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('aw_manage_achievements'),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/manage_achievements.html',
			'display'			=> true
		));
	}






/* Spickzettel ...
		$retu = array();
		foreach($ids as $id) {
			$retu[$id] = $this->pdh->put('awards', 'delete_award', array($id));
		}
		foreach($retu as $id => $suc) {
			if($suc) {
				$pos[] = stripslashes($this->pdh->get('awards', 'name', array($id)));
			} else {
				$neg[] = stripslashes($this->pdh->get('awards', 'name', array($id)));
			}
		}
		
		if(!empty($pos)) {
			$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $pos), 'color' => 'green');
		}
		if(!empty($neg)) {
			$messages[] = array('title' => $this->user->lang('del_no_suc'), 'text' => implode(', ', $neg), 'color' => 'red');
		}
		$this->display($messages);
*/


}
registry::register('awards_manage_achievements');

?>