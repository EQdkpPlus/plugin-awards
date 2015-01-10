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
			#'update'	=> array('process' => 'update', 'check' => 'a_awards_add', 'csrf' => true),
			'aid'		=> array('process' => 'edit', 'check' => 'a_awards_add'),
		);
		parent::__construct(false, $handler, array('add_award', 'name'), null, 'selected_ids[]');
		$this->process();
	}









/*
	public function edit($message=false) {
		//fetch raids for select
		$raids = array(0 => '');
		$raidids = $this->pdh->sort($this->pdh->get('raid', 'id_list'), 'raid', 'date', 'desc');
		foreach($raidids as $id) {
			$raids[$id] = '#ID:'.$id.' - '.$this->pdh->get('event', 'name', array($this->pdh->get('raid', 'event', array($id)))).' '.date('d.m.y', $this->pdh->get('raid', 'date', array($id)));
		}

		//fetch events for select
		$events = array();
		$event_ids = $this->pdh->get('event', 'id_list');
		foreach($event_ids as $id) {
			$events[$id] = $this->pdh->get('event', 'name', array($id));
		}
		if($message) {
			$this->core->messages($message);
			$adj = $this->get_post(true);
		} elseif($this->in->get('a',0)) {
			$grp_key = $this->pdh->get('adjustment', 'group_key', array($this->in->get('a',0)));
			$ids = $this->pdh->get('adjustment', 'ids_of_group_key', array($grp_key));
			foreach($ids as $id)
			{
				$adj['members'][] = $this->pdh->get('adjustment', 'member', array($id));
			}
			$adj['reason'] = $this->pdh->get('adjustment', 'reason', array($id));
			$adj['value'] = $this->pdh->get('adjustment', 'value', array($id));
			$adj['date'] = $this->pdh->get('adjustment', 'date', array($id));
			$adj['raid_id'] = $this->pdh->get('adjustment', 'raid_id', array($id));
			$adj['event'] = $this->pdh->get('adjustment', 'event', array($id));
			
			//Add additional members
			if (count($adj['members']) > 0){
				$arrIDList = array_keys($members);
				$blnResort = false;
				foreach($adj['members'] as $member_id){
					if (!isset($members[$member_id])) {
						$arrIDList[] = $member_id;
						$blnResort = true;
					}
				}
				if ($blnResort) $members = $this->pdh->aget('member', 'name', 0, array($this->pdh->sort($arrIDList, 'member', 'name', 'asc')));
			}
		}

		//fetch adjustment-reasons
		$adjustment_reasons = $this->pdh->aget('adjustment', 'reason', 0, array($this->pdh->get('adjustment', 'id_list')));
		$this->jquery->Autocomplete('reason', array_unique($adjustment_reasons));
		$this->confirm_delete($this->user->lang('confirm_delete_adjustment')."<br />".((isset($adj['reason'])) ? $adj['reason'] : ''), '', true);
		
		$this->tpl->assign_vars(array(
			'GRP_KEY'		=> (isset($grp_key)) ? $grp_key : '',
			'REASON'		=> (isset($adj['reason'])) ? $adj['reason'] : '',
			'RAID'			=> new hdropdown('raid_id', array('options' => $raids, 'value' => ((isset($adj['raid_id'])) ? $adj['raid_id'] : ''))),
			'MEMBERS'		=> $this->jquery->MultiSelect('members', $members, ((isset($adj['members'])) ? $adj['members'] : ''), array('width' => 350, 'filter' => true)),
			'DATE'			=> $this->jquery->Calendar('date', $this->time->user_date(((isset($adj['date'])) ? $adj['date'] : $this->time->time), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
			'VALUE'			=> (isset($adj['value'])) ? $adj['value'] : '',
			'EVENT'			=> new hdropdown('event', array('options' => $events, 'value' => ((isset($adj['event'])) ? $adj['event'] : ''))),
		));

		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'		=> (($id) ? $this->user->lang('aw_add_award').': '.$this->pdh->get('awards_achievements', 'name', array($id)) : $this->user->lang('aw_add_award')),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/add_award_edit.html',
			'display'			=> true)
		);
	}

*/















	public function edit(){
		$id = $this->in->get('aid', 0);
		
		$arrCronDropdown = array(
			0 => '',
			1 => $this->user->lang('aw_cron_module_1'),
			2 => $this->user->lang('aw_cron_module_2')
		);
		
		$this->tpl->assign_vars(array(
			'NAME'		=> (isset($adj['reason'])) ? $adj['reason'] : '',
			'DESCRIPTION'		=> (isset($adj['reason'])) ? $adj['reason'] : '',
			
			
			'RAID'			=> new hdropdown('raid_id', array('options' => $raids, 'value' => ((isset($adj['raid_id'])) ? $adj['raid_id'] : ''))),
			'MEMBERS'		=> $this->jquery->MultiSelect('members', $members, ((isset($adj['members'])) ? $adj['members'] : ''), array('width' => 350, 'filter' => true)),
			'DATE'			=> $this->jquery->Calendar('date', $this->time->user_date(((isset($adj['date'])) ? $adj['date'] : $this->time->time), true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
			'VALUE'			=> (isset($adj['value'])) ? $adj['value'] : '',
			'EVENT'			=> new hdropdown('event', array('options' => $events, 'value' => ((isset($adj['event'])) ? $adj['event'] : ''))),
		));
			
			
			
		
		
		$this->jquery->Tab_header('article_category-tabs');
		$this->jquery->Tab_header('category-permission-tabs');
		$editor = register('tinyMCE');
		$editor->editor_normal(array(
			'relative_urls'	=> false,
			'link_list'		=> true,
			'readmore'		=> false,
		));
		
		$arrAwardIDs = $this->pdh->sort($this->pdh->get('awards_achievements', 'id_list', array()), 'awards_achievements', 'sort_id', 'asc');
		$arrCategories['0'] = '--';
		foreach($arrAwardIDs as $aid){
			$arrAward[$aid] = $this->pdh->get('awards_achievements', 'name_prefix', array($aid)).$this->pdh->get('awards_achievements', 'name', array($aid));
		}
		$arrAggregation = $arrCategories;
		unset($arrAggregation[0]);
		if ($id){
			unset($arrCategories[$id]);
			$this->tpl->assign_vars(array(
				'DESCRIPTION'		=> $this->pdh->get('mediacenter_categories', 'description', array($id)),
				'NAME' 				=> $this->pdh->get('mediacenter_categories', 'name', array($id)),
				'ALIAS'				=> $this->pdh->get('mediacenter_categories', 'alias', array($id)),					
				'PER_PAGE'			=> $this->pdh->get('mediacenter_categories', 'per_page', array($id)),
				'DD_PARENT' 		=> new hdropdown('parent', array('js'=>'onchange="renew_all_permissions();"', 'options' => $arrCategories, 'value' => $this->pdh->get('mediacenter_categories', 'parent', array($id)))),
				'DD_PUBLISHED_STATE'=> new hradio('default_published_state]', array('options' => array(0 => $this->user->lang('not_published'), 1 => $this->user->lang('published')), 'value' => $this->pdh->get('mediacenter_categories', 'default_published_state', array($id)))),
				'R_NOTIFY_UNPUBLISHED' => new hradio('notify_unpublished', array('value' => ($this->pdh->get('mediacenter_categories', 'notify_on_onpublished', array($id))))),
				'R_COMMENTS'		=> new hradio('allow_comments', array('value' => ($this->pdh->get('mediacenter_categories', 'allow_comments', array($id))))),
				'R_VOTING'			=> new hradio('allow_voting', array('value' => ($this->pdh->get('mediacenter_categories', 'allow_voting', array($id))))),
				'DD_LAYOUT_TYPE' 	=> new hdropdown('layout', array('options' => $this->user->lang('mc_layout_types'), 'value' => $this->pdh->get('mediacenter_categories', 'layout', array($id)))),
				'DD_MEDIA_TYPE' 	=> new hmultiselect('types', array('options' => $this->user->lang('mc_types'), 'value' => $this->pdh->get('mediacenter_categories', 'types', array($id)))),
				'R_PUBLISHED'		=> new hradio('published', array('value' =>  ($this->pdh->get('mediacenter_categories', 'published', array($id))))),
				'SPINNER_PER_PAGE'	=> new hspinner('per_page', array('value' =>  ($this->pdh->get('mediacenter_categories', 'per_page', array($id))), 'max'  => 50, 'min'  => 5,'step' => 5,'onlyinteger' => true)),
			));
			
		} else {
			
			$this->tpl->assign_vars(array(
				'PER_PAGE' => 25,	
				'DD_PARENT' => new hdropdown('parent', array('js'=>'onchange="renew_all_permissions();"', 'options' => $arrCategories, 'value' => 0)),
				'DD_PUBLISHED_STATE'=> new hradio('default_published_state]', array('options' => array(0 => $this->user->lang('not_published'), 1 => $this->user->lang('published')), 'value' => 1)),
				'R_NOTIFY_UNPUBLISHED' => new hradio('notify_unpublished', array('value' => 0)),
				'R_COMMENTS'		=> new hradio('allow_comments', array('value' => 1)),
				'DD_LAYOUT_TYPE' 	=> new hdropdown('layout', array('options' => $this->user->lang('mc_layout_types'), 'value' => 0)),
				'DD_MEDIA_TYPE' 	=> new hmultiselect('types', array('options' => $this->user->lang('mc_types'), 'value' => array(0,1,2))),
				'R_PUBLISHED'		=> new hradio('published', array('value' =>  1)),
				'R_VOTING'			=> new hradio('allow_voting', array('value' => 1)),
				'SPINNER_PER_PAGE'	=> new hspinner('per_page', array('value' => $this->config->get('per_page', 'mediacenter'), 'max'  => 50, 'min'  => 5,'step' => 5,'onlyinteger' => true)),	
			));
		}

		$this->tpl->assign_vars(array(
			'AID' => $id,
		));
		$this->core->set_vars(array(
			'page_title'		=> (($id) ? $this->user->lang('aw_add_award').': '.$this->pdh->get('awards_achievements', 'name', array($id)) : $this->user->lang('aw_add_award')),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/add_award_edit.html',
			'display'			=> true)
		);
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