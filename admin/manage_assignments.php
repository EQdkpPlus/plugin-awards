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
  | awards_manage_awards
  +--------------------------------------------------------------------------*/
class awards_manage_assignments extends page_generic
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
			#'save' => array('process' => 'save', 'check' => 'a_awards_manage', 'csrf' => true),
		);
		parent::__construct(false, $handler, array('manage_assignments', 'name'), null, 'selected_ids[]');
		$this->process();
	}
	

	/**
	  * Display
	  * display the page
	  */
	public function display(){
		$view_list = $this->pdh->get('awards_assignments', 'id_list', array());
		$hptt_page_settings = array(
			'name'					=> 'hptt_aw_admin_manage_awards',
			'table_main_sub'		=> '%intAssignmentID%',
			'table_subs'			=> array('%intAssignmentID%', '%intAssignmentID%'),
			'page_ref'				=> 'manage_assignments.php',
			'show_numbers'			=> true,
			'show_select_boxes'		=> true,
			'selectboxes_checkall'	=> true,
			'show_detail_twink'		=> false,
			'table_sort_dir'		=> 'asc',
			'table_sort_col'		=> 0,
			'table_presets'			=> array(
				array('name' => 'awards_assignments_date', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_assignments_user', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'awards_assignments_name', 'sort' => true, 'th_add' => '', 'td_add' => ''),
			),
		);
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->root_path.'plugins/awards/admin/manage_assignments.php', '%link_url_suffix%' => ''));
		$page_suffix = '&amp;start='.$this->in->get('start', 0);
		$sort_suffix = '?sort='.$this->in->get('sort');
		
		$item_count = count($view_list);
		
		$this->confirm_delete($this->user->lang('aw_confirm_delete_assignment'));

		$this->tpl->assign_vars(array(
			'AWARDS_LIST'		=> $hptt->get_html_table($this->in->get('sort'), $page_suffix,null,1,null,false, array('awards_assignments', 'checkbox_check')),
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