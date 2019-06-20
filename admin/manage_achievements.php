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
		parent::__construct(false, $handler, array('awards_achievements', 'html_name'), null, 'selected_ids[]');
		$this->process();
	}


	/**
	  * Save
	  * save the achievement
	  */
	public function save(){
		$id 				= $this->in->get('aid', 0);
		
		$strAchName			= (new htextmultilang('name'))->_inpval();
		$strAchDescription	= (new htextareamultilang('description'))->_inpval();
		
		$intAchSortID		= $this->in->get('sort_id', PHP_INT_MAX);
		$blnAchActive		= $this->in->get('active_state', 1);
		$blnAchSpecial		= $this->in->get('special_state', 1);
		$intAchPoints		= $this->in->get('points', 10);
		$fltAchDKP			= $this->in->get('dkp', 0);
		$intEventID			= $this->in->get('event', 'int');
		$strAchIcon			= $this->in->get('icon', 'default.png');
		$strModuleCond		= $this->in->get('module_cond');
		
		
		$arrAchModules	 		= array('conditions' => ($strModuleCond)?: 'disable');
		$arrAchModuleSettings	= array();
		foreach($this->in->getArray('module', 'raw') as $module){
			$arrAchModules[] = $module['name'];
			
			$module_settings	= array();
			$module['settings'] = json_decode($module['settings'], true);
			foreach($module['settings'] as $key => $val){
				if(substr($key, -2) == '[]'){
					$key = substr($key, 0, mb_strlen($key, 'UTF-8') - 2);
					$module_settings[$key] = (is_array($val))? $val : array($val);
				}else{
					$module_settings[$key] = $val;
				}
			}
			
			$arrAchModuleSettings[$module['name']] = (!empty($module_settings))? $module_settings : '';
		}
		$strAchModules	 = serialize($arrAchModules);
		$strAchModuleSettings = serialize($arrAchModuleSettings);
		
		
		$arrAchIconColors	= array();
		for($i=1; $i<=5; $i++) $arrAchIconColors[] = $this->in->get('icon_layer_'.$i);
		$arrAchIconColors = serialize($arrAchIconColors);
		
		
		if ($id){ //update Achievement
			$blnResult = $this->pdh->put('awards_achievements', 'update', array($id, $strAchName, $strAchDescription, $intAchSortID, $blnAchActive, $blnAchSpecial, $intAchPoints, $fltAchDKP, $strAchIcon, $arrAchIconColors, $strAchModules, $strAchModuleSettings, $intEventID));
			
		} else { //add Achievement
			$blnResult = $this->pdh->put('awards_achievements', 'add', array($strAchName, $strAchDescription, $intAchSortID, $blnAchActive, $blnAchSpecial, $intAchPoints, $fltAchDKP, $strAchIcon, $arrAchIconColors, $strAchModules, $strAchModuleSettings, $intEventID));
		}
		
		//output Message
		if ($blnResult){
			$this->pdh->process_hook_queue();
			$this->core->message(sprintf( $this->user->lang(($id)?'aw_upd_success':'aw_add_success'), $this->user->multilangValue($strAchName) ), $this->user->lang('success'), 'green');
		} else {
			$this->core->message(sprintf( $this->user->lang(($id)?'aw_upd_nosuccess':'aw_add_nosuccess'), $this->user->multilangValue($strAchName) ), $this->user->lang('error'), 'red');
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
		
		// fetch events
		$arrEvents = array();
		$arrEventIDs = $this->pdh->get('event', 'id_list');
		foreach($arrEventIDs as $eid) {
			$arrEvents[$eid] = $this->pdh->get('event', 'name', array($eid));
		}
		
		// fetch colorpicker settings
		$arrAchIconColors = $this->pdh->get('awards_achievements', 'icon_colors', array($id));
		$arrAchIconColors = unserialize($arrAchIconColors);
		
		// fetch Cronjob Modules
		$arrAllModules = array('choose_option' => $this->user->lang('aw_module_choose_option'));
		$module_folder = opendir($this->root_path.'plugins/awards/cronjob/modules');
		while(false !== ($module = readdir($module_folder))){
			if(substr($module, -21) == '_cronmodule.class.php'){
				include_once $this->root_path.'plugins/awards/cronjob/modules/'.$module;
				$module_name		= substr($module, 0, -21);
				$module_class_name	= $module_name.'_cronmodule';
				
				if(!$module_class_name::check_requirements()) continue;
				$module_lang		= $module_class_name::$language;
				$use_lang			= (array_key_exists($this->user->lang_name, $module_lang))? $this->user->lang_name : $this->config->get('default_lang');
				
				$arrAllModules[$module_name] = $module_lang[$use_lang]['title'];
				
				$this->tpl->assign_block_vars('ref_modules_row', array(
					'NAME'		=> $module_name,
					'TITLE'		=> $module_lang[$use_lang]['title'],
					'SETTINGS'	=> method_exists($module_class_name, 'display_settings'),
				));
			}
		}
		
		// fetch Award Cronjob Modules
		$arrAchModules		  = ($id)? unserialize($this->pdh->get('awards_achievements', 'module', array($id))) : array(0, '');
		$arrAchModuleSettings = ($id)? unserialize($this->pdh->get('awards_achievements', 'module_set', array($id))) : array();
		
		$strModuleCondition = ($id)? $arrAchModules['conditions'] : 'disable';
		$arrModuleCondtions = array(
			'disable' => $this->user->lang('no'),
			'all'	  => $this->user->lang('aw_module_all'),
			'any'	  => $this->user->lang('aw_module_any'),
		);
		
		$arrDisableModules = array('choose_option');
		foreach(array_slice($arrAchModules, 1) as $module_name){
			if(empty($module_name)) break;
			$module_class_name	= $module_name.'_cronmodule';
			
			if(!$module_class_name::check_requirements()) continue;
			$module_lang		= $module_class_name::$language;
			$use_lang			= (array_key_exists($this->user->lang_name, $module_lang))? $this->user->lang_name : $this->config->get('default_lang');
			$blnSettings		= method_exists($module_class_name, 'display_settings');
			if(is_array($arrAchModuleSettings[$module_name])) $module_settings = sanitize(json_encode($arrAchModuleSettings[$module_name]));
			
			$arrDisableModules[$module_name] = $module_name;
			
			$this->tpl->assign_block_vars('module_row', array(
				'NAME'		=> $module_name,
				'TITLE'		=> $module_lang[$use_lang]['title'],
				'SETTINGS'	=> ($blnSettings)? $module_settings : '',
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
		
		if($id) $strAchIcon = $this->pdh->get('awards_achievements', 'icon', array($id));
		else	$strAchIcon = 'default.png';
		
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
		$this->jquery->fileBrowser('all', 'image', $this->pfh->FolderPath('images','awards', 'absolute'), array('title' => $this->user->lang('aw_upload_icon'), 'onclosejs' => 'location.reload();'));
		
		
		$this->tpl->assign_vars(array(
			'AID'				=> $id,
			'SORT_ID'			=> ($id > 0)? $this->pdh->get('awards_achievements', 'sort_id', array($id)) : count($this->pdh->get('awards_achievements', 'id_list'))+1,
			'ML_NAME'			=> (new htextmultilang('name', array('value' => ($id)? unserialize($this->pdh->get('awards_achievements', 'name', array($id))) : '', 'size' => 30, 'required' => true)))->output(),
			'ML_DESCRIPTION'	=> (new htextareamultilang('description', array('value' => ($id)? unserialize($this->pdh->get('awards_achievements', 'description', array($id))) : '', 'rows' => '3', 'cols' => '50')))->output(),
			'R_ACTIVE_STATE'	=> (new hradio('active_state', array('options' => array(1 => $this->user->lang('yes'), 0 => $this->user->lang('no')), 'value' => ($id)? $this->pdh->get('awards_achievements', 'active', array($id)) : 1)))->output(),
			'R_SPECIAL_STATE'	=> (new hradio('special_state', array('options' => array(1 => $this->user->lang('yes'), 0 => $this->user->lang('no')), 'value' => ($id)? $this->pdh->get('awards_achievements', 'special', array($id)) : 0)))->output(),
			'SPINNER_POINTS' 	=> (new hspinner('points', array('value' => ($id)? $this->pdh->get('awards_achievements', 'points', array($id)) : 10, 'max'  => 100000, 'min'  => 0, 'step' => 5, 'onlyinteger' => true)))->output(),
			'SPINNER_DKP'		=> (new hspinner('dkp', array('value' => ($id)? $this->pdh->get('awards_achievements', 'dkp', array($id)) : 0, 'max'  => 100000, 'min'  => -100000, 'step' => 5)))->output(),
			'DD_EVENT'			=> (new hdropdown('event', array('options' => $arrEvents, 'value' => ($id)? $this->pdh->get('awards_achievements', 'event_id', array($id)) : '')))->output(),
			'DD_MODULE_COND'	=> (new hdropdown('module_cond', array('options' => $arrModuleCondtions, 'value' => $strModuleCondition)))->output(),
			'DD_MODULES'		=> (new hdropdown('modules', array('options' => $arrAllModules, 'value' => 'choose_option', 'todisable' => $arrDisableModules, 'class' => 'module_show')))->output(),
			'CP_ICON_LAYER_1'	=> $this->jquery->colorpicker('icon_layer_1', ($arrAchIconColors[0])?:'#FFFFFF'),
			'CP_ICON_LAYER_2'	=> $this->jquery->colorpicker('icon_layer_2', ($arrAchIconColors[1])?:'#000000'),
			'CP_ICON_LAYER_3'	=> $this->jquery->colorpicker('icon_layer_3', ($arrAchIconColors[2])?:'#000000'),
			'CP_ICON_LAYER_4'	=> $this->jquery->colorpicker('icon_layer_4', ($arrAchIconColors[3])?:'#000000'),
			'CP_ICON_LAYER_5'	=> $this->jquery->colorpicker('icon_layer_5', ($arrAchIconColors[4])?:'#000000'),
		));
		
		$strPageTitle = (($id) ? $this->user->lang('aw_edit_achievement').': '.$this->user->multilangValue($this->pdh->get('awards_achievements', 'name', array($id))) : $this->user->lang('aw_add_achievement'));
		
		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars([
			'page_title'		=> $strPageTitle,
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/manage_achievements_edit.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('awards').': '.$this->user->lang('aw_manage_achievements'), 'url'=>$this->root_path.'plugins/awards/admin/manage_achievements.php'.$this->SID],
				['title'=>$strPageTitle, 'url'=>' '],
			],
			'display'			=> true,
		]);
	}


	/**
	  * Display
	  * display all achievements
	  */
	public function display() {
		$arrUserSettings = $this->pdh->get('user', 'plugin_settings', array($this->user->id));
		$arrUserSettings['aw_admin_pagination'] = (isset($arrUserSettings['aw_admin_pagination']))? $arrUserSettings['aw_admin_pagination'] : 100;
		
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
				array('name' => 'awards_achievements_points',  'sort' => true, 'th_add' => 'width="40"', 'td_add' => 'style="text-align:right"'),
				array('name' => 'awards_achievements_dkp',  'sort' => true, 'th_add' => 'width="20"', 'td_add' => 'style="text-align:right"'),
			),
		);
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->root_path.'plugins/awards/admin/manage_achievements.php', '%link_url_suffix%' => ''));
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');
		
		$item_count = count($view_list);
		$strfootertext = sprintf($this->user->lang('aw_listachiev_footcount'), $item_count, $arrUserSettings['aw_admin_pagination']);
		
		$this->confirm_delete($this->user->lang('aw_confirm_delete_achievement'));

		$this->tpl->assign_vars([
			'ACHIEVEMENTS_LIST'	=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $this->in->get('start', 0), $arrUserSettings['aw_admin_pagination'], $strfootertext),
			'PAGINATION'		=> generate_pagination('manage_achievements.php'.$sort_suffix, $item_count, $arrUserSettings['aw_admin_pagination'], $this->in->get('start', 0)),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count()
		]);

		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars([
			'page_title'		=> $this->user->lang('aw_manage_achievements'),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/manage_achievements.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('awards').': '.$this->user->lang('aw_manage_achievements'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}


}
registry::register('awards_manage_achievements');
?>