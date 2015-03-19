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


	/**
	  * Save
	  * save the achievement
	  */
	public function save(){	
		$id 				= $this->in->get('aid', 0);
		
		$hmultilangName = new htextmultilang('name');
		$hmultilangDesc = new htextareamultilang('description');
		$strAchName			= $hmultilangName->_inpval();
		$strAchDescription	= $hmultilangDesc->_inpval();
		
		$intAchSortID		= $this->in->get('sort_id', 99999999);
		$blnAchActive		= $this->in->get('active_state', 1);
		$blnAchSpecial		= $this->in->get('special_state', 1);
		$intAchPoints		= $this->in->get('points', 10);
		$fltAchDKP			= $this->in->get('dkp', 0);
		$intEventID			= $this->in->get('event', 'int');
		$strAchIcon			= $this->in->get('icon', 'default.svg');
		$arrAchIconColors	= array();
		$strAchModule		= $this->in->get('module');
		$strAchModuleSet	= $this->in->get('module_set');
		
		for($i=1; $i<=5; $i++) $arrAchIconColors[] = $this->in->get('icon_layer_'.$i);
		$arrAchIconColors = serialize($arrAchIconColors);
		
		// parse module settings
		if(!empty($strAchModuleSet)){
			$strAchModuleSet = str_replace(' ', '', $strAchModuleSet);
			$strAchModuleSet = serialize( explode(',',$strAchModuleSet) );
		}
		
		if ($id){ //update Achievement
			$blnResult = $this->pdh->put('awards_achievements', 'update', array($id, $strAchName, $strAchDescription, $intAchSortID, $blnAchActive, $blnAchSpecial, $intAchPoints, $fltAchDKP, $strAchIcon, $arrAchIconColors, $strAchModule, $strAchModuleSet, $intEventID));
			
		} else { //add Achievement
			$blnResult = $this->pdh->put('awards_achievements', 'add', array($strAchName, $strAchDescription, $blnAchActive, $blnAchSpecial, $intAchPoints, $fltAchDKP, $strAchIcon, $arrAchIconColors, $strAchModule, $strAchModuleSet, $intEventID));
		}
		
		//output Message
		if ($blnResult){
			$this->pdh->process_hook_queue();
			$this->core->message(sprintf( $this->user->lang('aw_add_success'), $this->user->multilangValue($strAchName) ), $this->user->lang('success'), 'green');
		} else {
			$this->core->message(sprintf( $this->user->lang('aw_add_nosuccess'), $this->user->multilangValue($strAchName) ), $this->user->lang('error'), 'red');
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
		
		// fetch Cronjob Modules
		$modules = array( NULL => $this->user->lang('aw_cronmodule_disabled') );
		$module_folder = opendir($this->root_path.'plugins/awards/cronjob/module');
		while (false !== ($module = readdir($module_folder))) {
			if (substr($module, -21) == '_cronmodule.class.php'){
				$module_name = substr($module, 0, -21);
				$modules[$module_name] = $this->user->lang('aw_cronmodule_'.$module_name);
			}
		}
		$arrModuleDropdown = $modules;
		
		// parse module settings
		$strModuleSet = $this->pdh->get('awards_achievements', 'module_set', array($id));
		if(!empty($strModuleSet))
			$strModuleSet = implode(',', unserialize($strModuleSet));
		
		// fetch events
		$arrEvents = array();
		$arrEventIDs = $this->pdh->get('event', 'id_list');
		foreach($arrEventIDs as $eid) {
			$arrEvents[$eid] = $this->pdh->get('event', 'name', array($eid));
		}
		
		// fetch colorpicker settings
		$arrAchIconColors = $this->pdh->get('awards_achievements', 'icon_colors', array($id));
		$arrAchIconColors = unserialize($arrAchIconColors);
		
		
		if ($id){
			$this->tpl->assign_vars(array(
				'ML_NAME'			=> new htextmultilang('name', array('value' => unserialize($this->pdh->get('awards_achievements', 'name', array($id))), 'size' => 30, 'required' => true)),
				'ML_DESCRIPTION'	=> new htextareamultilang('description', array('value' => unserialize($this->pdh->get('awards_achievements', 'description', array($id))), 'rows' => '3', 'cols' => '50')),
				'R_ACTIVE_STATE'	=> new hradio('active_state', array('options' => array(1 => $this->user->lang('yes'), 0 => $this->user->lang('no')), 'value' => $this->pdh->get('awards_achievements', 'active', array($id)))),
				'R_SPECIAL_STATE'	=> new hradio('special_state', array('options' => array(0 => $this->user->lang('published'), 1 => $this->user->lang('not_published')), 'value' => $this->pdh->get('awards_achievements', 'special', array($id)))),
				'SPINNER_POINTS' 	=> new hspinner('points', array('value' => $this->pdh->get('awards_achievements', 'points', array($id)), 'max'  => 100000, 'min'  => 0, 'step' => 5, 'onlyinteger' => true)),
				'SPINNER_DKP'		=> new hspinner('dkp', array('value' =>  ($this->pdh->get('awards_achievements', 'dkp', array($id))), 'max'  => 100000, 'min'  => -100000, 'step' => 5)),
				'DD_MODULE'			=> new hdropdown('module', array('options' => $arrModuleDropdown, 'value' => $this->pdh->get('awards_achievements', 'module', array($id)))),
				'DD_EVENT'			=> new hdropdown('event', array('options' => $arrEvents, 'value' => $this->pdh->get('awards_achievements', 'event_id', array($id)))),
				'MODULE_SET'		=> (!empty($strModuleSet))? $strModuleSet : '',
				'CP_ICON_LAYER_1'	=> $this->jquery->colorpicker('icon_layer_1', ($arrAchIconColors[0])?:'#000000'),
				'CP_ICON_LAYER_2'	=> $this->jquery->colorpicker('icon_layer_2', ($arrAchIconColors[1])?:'#FFFFFF'),
				'CP_ICON_LAYER_3'	=> $this->jquery->colorpicker('icon_layer_3', ($arrAchIconColors[2])?:'#FFFFFF'),
				'CP_ICON_LAYER_4'	=> $this->jquery->colorpicker('icon_layer_4', ($arrAchIconColors[3])?:'#FFFFFF'),
				'CP_ICON_LAYER_5'	=> $this->jquery->colorpicker('icon_layer_5', ($arrAchIconColors[4])?:'#FFFFFF'),
			));
		} else {
			$this->tpl->assign_vars(array(
				'ML_NAME'			=> new htextmultilang('name', array('size' => 30, 'required' => true)),
				'ML_DESCRIPTION'	=> new htextareamultilang('description', array('rows' => '3', 'cols' => '50')),
				'R_ACTIVE_STATE'	=> new hradio('active_state', array('options' => array(1 => $this->user->lang('yes'), 0 => $this->user->lang('no')), 'value' => 1)),
				'R_SPECIAL_STATE'	=> new hradio('special_state', array('options' => array(0 => $this->user->lang('published'), 1 => $this->user->lang('not_published')), 'value' => 0)),
				'SPINNER_POINTS'	=> new hspinner('points', array('value' =>  10, 'max'  => 100000, 'min'  => 0, 'step' => 5, 'onlyinteger' => true)),	
				'SPINNER_DKP'		=> new hspinner('dkp', array('value' =>  0, 'max'  => 100000, 'min'  => -100000, 'step' => 5)),
				'DD_MODULE'			=> new hdropdown('module', array('options' => $arrModuleDropdown, 'value' => NULL)),
				'DD_EVENT'			=> new hdropdown('event', array('options' => $arrEvents, 'value' => '')),
				'MODULE_SET'		=> '',
				'CP_ICON_LAYER_1'	=> $this->jquery->colorpicker('icon_layer_1', '#000000'),
				'CP_ICON_LAYER_2'	=> $this->jquery->colorpicker('icon_layer_2', '#FFFFFF'),
				'CP_ICON_LAYER_3'	=> $this->jquery->colorpicker('icon_layer_3', '#FFFFFF'),
				'CP_ICON_LAYER_4'	=> $this->jquery->colorpicker('icon_layer_4', '#FFFFFF'),
				'CP_ICON_LAYER_5'	=> $this->jquery->colorpicker('icon_layer_5', '#FFFFFF'),
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
		
		if ($id) $strAchIcon = $this->pdh->get('awards_achievements', 'icon', array($id));
		
		while($i<$fields)
		{
			$this->tpl->assign_block_vars('files_row', array());
			$this->tpl->assign_var('ICONS', true);
			$b = $i+6;
			
			for($i; $i<$b; $i++){
			$icon = (isset($icons[$i])) ? $icons[$i] : '';
			$this->tpl->assign_block_vars('files_row.fields', array(
					'NAME'		=> pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION),
					'CHECKED'	=> (isset($strAchIcon) AND pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION) == $strAchIcon) ? ' checked="checked"' : '',
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
			'page_title'		=> (($id) ? $this->user->lang('aw_add_achievement').': '.$this->user->multilangValue($this->pdh->get('awards_achievements', 'name', array($id))) : $this->user->lang('aw_add_achievement')),
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
				array('name' => 'awards_achievements_points',  'sort' => true, 'th_add' => 'width="20"', 'td_add' => 'style="text-align:right"'),
				array('name' => 'awards_achievements_dkp',  'sort' => true, 'th_add' => 'width="20"', 'td_add' => 'style="text-align:right"'),
				array('name' => 'awards_achievements_module',  'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
				array('name' => 'awards_achievements_module_set',  'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
			),
		);
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->root_path.'plugins/awards/admin/manage_achievements.php', '%link_url_suffix%' => ''));
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');
		
		$item_count = count($view_list);
		$strfootertext = sprintf($this->user->lang('aw_listachiev_footcount'), $item_count, $this->user->data['user_alimit']);
		
		$this->confirm_delete($this->user->lang('aw_confirm_delete_achievement'));

		$this->tpl->assign_vars(array(
			'ACHIEVEMENTS_LIST'	=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $this->user->data['user_alimit'], $strfootertext),
			'PAGINATION'		=> generate_pagination('manage_achievements.php'.$sort_suffix, $item_count, $this->user->data['user_alimit'], $this->in->get('start', 0)),
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


}
registry::register('awards_manage_achievements');

?>