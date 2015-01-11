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
  | awards_add_award
  +--------------------------------------------------------------------------*/
class awards_add_award extends page_generic
{
	/**
	  * Constructor
	  */
	public function __construct(){
		// plugin installed?
		if (!$this->pm->check('awards', PLUGIN_INSTALLED))
		  message_die($this->user->lang('aw_plugin_not_installed'));
		
		$this->user->check_auth('a_awards_manage');
		
		$handler = array(
			'save'		=> array('process' => 'save', 'check' => 'a_awards_add', 'csrf' => true),
			'update'	=> array('process' => 'update', 'check' => 'a_awards_add', 'csrf' => true),
			'aid'		=> array('process' => 'edit', 'check' => 'a_awards_add'),
		);
		parent::__construct(false, $handler, array('add_award', 'name'), null, 'selected_ids[]');
		$this->process();
	}




/*$strName, $strDescription, $intSortID, $intActive, $intSpecial, $intValue,
						$strImage, $arrImageColors, $strAdjustment, $intAdjustmentValue
*/

	/**
	  * Save
	  * save the new award
	  */
	public function save(){
		$id = $this->in->get('aid', 0);
		$strName = $this->in->get('name');
		$strDescription = $this->in->get('description');
		$intSortID = $this->in->get('sort_id', 99999999);
		$intActive = $this->in->get('active_state', 1);
		$intSpecial = $this->in->get('special_state', 1);
		$intValue = $this->in->get('value', 10);
		$strImage = $this->in->get('icon', 'default.svg');
		#$arrImageColors = $this->in->getArray('image_colors', '');
		$arrImageColors = array();
		$strAdjustment = $this->in->get('adjustment');
		$intAdjustmentValue = $this->in->get('adjustment_value', 0);
		#$strDescription = $this->in->get('description', '', 'raw'); // TinyMCE	
		
		d($id);
		
		if ($strName == "" ) {
			$this->core->message($this->user->lang('name'), $this->user->lang('missing_values'), 'red');
			$this->edit();
			return;
		}
		
		if ($id){
			$blnResult = $this->pdh->put('awards_achievements', 'update', array(
				$id, $strName, $strDescription, $intSortID, $intActive, $intSpecial, $intValue,
				$strImage, $arrImageColors, $strAdjustment, $intAdjustmentValue
			));
		} else {
			$blnResult = $this->pdh->put('awards_achievements', 'add', array(
				$strName, $strDescription, $intActive, $intSpecial, $intValue,
				$strImage, $arrImageColors, $strAdjustment, $intAdjustmentValue
			));
		}
		
		if ($blnResult){
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('aw_add_success'), $this->user->lang('success'), 'green');
		} else {
			$this->core->message($this->user->lang('aw_add_nosuccess'), $this->user->lang('error'), 'red');
		}
		
		$this->display();
	}
	
	
	/**
	  * Update
	  * update the awards
	  */
	public function update(){
		
		$arrSortables = $this->in->getArray('sortCategories', 'int');
		$arrSortablesFlipped = array_flip($arrSortables);
	
		$arrSpecial = $this->in->getArray('special', 'int');
		foreach($arrPublished as $key => $val){
			$this->pdh->put('mediacenter_categories', 'update_sortandpublished', array($key, (int)$arrSortablesFlipped[$key], (int)$val));
		}
		$this->pdh->process_hook_queue();
		$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('success'), 'green');
	}









	/**
	  * Edit
	  * edit award
	  */
	public function edit(){
		$id = $this->in->get('aid', 0);
		
		// verfügbare Adjustment Module für den Cron
		$arrAdjDropdown = array(
			0 => '',
			1 => $this->user->lang('aw_cron_module_1'),
			2 => $this->user->lang('aw_cron_module_2')
		);
		
		if ($id){
			$this->tpl->assign_vars(array(
				'NAME' 				=> $this->pdh->get('awards_achievements', 'name', array($id)),
				'R_ACTIVE_STATE'	=> new hradio('active_state', array('options' => array(1 => $this->user->lang('yes'), 0 => $this->user->lang('no')), 'value' => $this->pdh->get('awards_achievements', 'active', array($id)))),
				'R_SPECIAL_STATE'	=> new hradio('special_state', array('options' => array(1 => $this->user->lang('published'), 0 => $this->user->lang('not_published')), 'value' => $this->pdh->get('awards_achievements', 'special', array($id)))),
				'DESCRIPTION'		=> $this->pdh->get('awards_achievements', 'description', array($id)),
				'SPINNER_VALUE' 	=> new hspinner('value', array('value' =>  ($this->pdh->get('awards_achievements', 'value', array($id))), 'max'  => 99999, 'min'  => 0,'step' => 5,'onlyinteger' => true)),
				'DD_ADJ_MODULE' 	=> new hdropdown('adjustment', array('options' => $arrAdjDropdown, 'value' => $this->pdh->get('awards_achievements', 'adjustment', array($id)))),
				'SPINNER_ADJ_VALUE'	=> new hspinner('adjustment_value', array('value' =>  ($this->pdh->get('awards_achievements', 'adjustment_value', array($id))), 'max'  => 99999, 'min'  => 0, 'step' => 5, 'onlyinteger' => true)),
			));
		} else {
			$this->tpl->assign_vars(array(
				'NAME' 				=> '',
				'R_ACTIVE_STATE'	=> new hradio('active_state', array('options' => array(1 => $this->user->lang('yes'), 0 => $this->user->lang('no')), 'value' => 1)),
				'R_SPECIAL_STATE'	=> new hradio('special_state', array('options' => array(1 => $this->user->lang('published'), 0 => $this->user->lang('not_published')), 'value' => 1)),
				'DESCRIPTION'		=> '',
				'SPINNER_VALUE' 	=> new hspinner('value', array('value' =>  10, 'max'  => 99999, 'min'  => 0, 'step' => 5, 'onlyinteger' => true)),	
				'DD_ADJ_MODULE' 	=> new hdropdown('adjustment', array('options' => $arrAdjDropdown, 'value' => NULL)),
				'SPINNER_ADJ_VALUE'	=> new hspinner('adjustment_value', array('value' =>  0, 'max'  => 99999, 'min'  => 0, 'step' => 5, 'onlyinteger' => true)),	
			));
		}

		//Get Icons
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
		
		//Image Uploader
		$this->jquery->fileBrowser('all', 'image', $this->pfh->FolderPath('images','awards', 'absolute'), array('title' => $this->user->lang('aw_upload_icon'), 'onclosejs' => '$(\'#eventSubmBtn\').click();'));

		$this->tpl->assign_vars(array(
			'AID' => $id,
		));
		
		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'		=> (($id) ? $this->user->lang('aw_add_award').': '.$this->pdh->get('awards_achievements', 'name', array($id)) : $this->user->lang('aw_add_award')),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/add_award_edit.html',
			'display'			=> true)
		);
	}


	/**
	  * Delete
	  * delete selected awards
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
	  * Display
	  * display all awards
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
			'name'					=> 'hptt_aw_admin_add_award',
			'table_main_sub'		=> '%intAwardID%',
			'table_subs'			=> array('%intAwardID%', '%intAwardID%'),
			'page_ref'				=> 'add_award.php',
			'show_numbers'			=> false,
			'show_select_boxes'		=> true,
			'selectboxes_checkall'	=> true,
			'show_detail_twink'		=> false,
			'table_sort_dir'		=> 'asc',
			'table_sort_col'		=> 0,
			'table_presets'			=> array(
				array('name' => 'awards_achievements_sort_id', 'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
				array('name' => 'awards_achievements_active', 'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
				array('name' => 'awards_achievements_special', 'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
				array('name' => 'awards_achievements_name', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_achievements_description', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_achievements_value', 'sort' => true, 'th_add' => 'width="20"', 'td_add' => ''),
			),
		);
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->root_path.'plugins/awards/admin/add_award.php', '%link_url_suffix%' => ''));
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');
		
		$item_count = count($view_list);
		
		$this->confirm_delete($this->user->lang('aw_confirm_delete_award'));

		$this->tpl->assign_vars(array(
			'AWARD_LIST'		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix,null,1,null,false, array('awards_achievements', 'checkbox_check')),
			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count())
		);

		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('aw_manage_awards'),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/add_award.html',
			'display'			=> true)
		);
	}






/*	public function save(){
		$award = $this->get_post();
		if($this->in->get('a',0)) {*/
			/*if (!empty($award['name']) == true){
				$intActive = (isset($award['active']) && (int)$award['active']) ? 1 : 0;
				$intSpecial = (isset($award['special']) && (int)$award['special']) ? 1 : 0;
				// pdh->put
			} else { $retu = false }*/
/*			$retu = $this->pdh->put('awards', 'update_award', array($this->in->get('a',0), $award['name'], $award['description'], $intActive, $intSpecial, $award['value'], $award['image'], $award['image_colors'], $award['adjustment'], $award['adjustment_value']));
		} else {
			$retu = $this->pdh->put('awards', 'add_award', array($adj['value'], $adj['reason'], $adj['members'], $adj['event'], $adj['raid_id'], $adj['date']));	
		}
		
		if($retu) {
			$message = array('title' => $this->user->lang('save_suc'), 'text' => $award['name'].$this->user->lang('aw_add_success'), 'color' => 'green');
		} else {
			$message = array('title' => $this->user->lang('save_nosuc'), 'text' => $award['name'].$this->user->lang('aw_add_nosuccess'), 'color' => 'red');
		}
		
		$this->display($message);
	}

	public function delete() {
		$ids = array();
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $s_id)
			{
				$new_ids = $this->pdh->get('awardst', 'ids_of_group_key', array($this->pdh->get('awards', 'group_key', array($s_id))));
				$ids = array_merge($ids, $new_ids);
			}
		} else {
			$ids = $this->pdh->get('awards', 'ids_of_group_key', array($this->in->get('selected_ids','')));
		}
		
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
	}*/

    // -- EQDKP ---------------------------------------------------------------
}
registry::register('awards_add_award');

?>