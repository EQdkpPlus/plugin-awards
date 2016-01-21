<?php
/*	Project:	EQdkp-Plus
 *	Package:	Awards Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
  | awards_manage_cronmodules
  +--------------------------------------------------------------------------*/
class awards_manage_cronmodules extends page_generic
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
		parent::__construct(false, $handler, array('manage_cronmodules', 'name'), null, 'selected_ids[]');
		$this->process();
	}


	/**
	  * Save
	  * save the assignment
	  */
	public function save(){
		
	}


	/**
	  * Delete
	  * delete selected assignments
	  */
	public function delete(){
		
	}


	/**
	  * Edit Page
	  * display edit page
	  */	
	public function edit(){
		
		
		// Aufgrund der notwendigkeit, parameter auf einer bereits bestehenden Seite zurückzuerhalten
		// sollte dies via AJAX geschehen
		// AJAX ruft entsprechend diesen codeabschnitt ab, hierbei wird geprüft ob cronjob anhand übermittelter id/name
		// Optionen unterstützt und lässt diesen output zu, oder erzeugt erst garkein output und kein options button
		$this->tpl->assign_var('DATA', '$htmlout');
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('raidevent_raid_guests'),
			'header_format'		=> 'simple',
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/cronmodule.html',
			'display'			=> true
		));
	}


	/**
	  * Display
	  * display main page
	  */
	public function display(){
		
		$this->timekeeper->del_cron('awards');
		
		$this->timekeeper->add_cron('awards', array('extern' => true, 'ajax' => false, 'delay' => false, 'repeat' => true, 'repeat_type' => 'daily', 'active' => true, 'path' => '/plugins/awards/cronjob/', 'editable' => true, 'description'	=> 'Plugin: Awards'));
		
		
		/*
		include $this->root_path.'plugins/awards/cronjob/module/wow_cronmodule.class.php';
		$m = new wow_cronmodule(3, array('opt' => 74));
		var_dump($m->requirements());
		*/
		
		
		
		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('aw_manage_cronmodules'),
			'template_path'		=> $this->pm->get_data('awards', 'template_path'),
			'template_file'		=> 'admin/manage_cronmodules.html',
			'display'			=> true)
		);
	}


}
registry::register('awards_manage_cronmodules');

?>