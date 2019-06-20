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

if (!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
  | awards
  +--------------------------------------------------------------------------*/
class awards extends plugin_generic
{

	public $version    = '0.5.2';
	public $build      = '';
	public $copyright  = 'Asitara';
	public $vstatus    = 'Beta';

	protected static $apiLevel = 23;

	/**
	  * Constructor
	  * Initialize all informations for installing/uninstalling plugin
	  */
	public function __construct(){
		parent::__construct();

		$this->add_data(array (
			'name'              => 'Awards',
			'code'              => 'awards',
			'path'              => 'awards',
			'contact'           => 'support@assasinen.5cz.de',
			'template_path'     => 'plugins/awards/templates/',
			'icon'              => 'fa fa-list-alt',
			'version'           => $this->version,
			'author'            => $this->copyright,
			'description'       => $this->user->lang('awards_short_desc'),
			'long_description'  => $this->user->lang('awards_long_desc'),
			'homepage'          => 'https://eqdkp-plus.eu/',
			'manuallink'        => 'https://eqdkp-plus.eu/wiki/Plugin:_Awards',
			'plus_version'      => '2.3',
			'build'             => $this->build,
		));

		$this->add_dependency(array(
			'plus_version'      => '2.3'
		));

		// -- Register our permissions ------------------------
		// permissions: 'a'=admins, 'u'=user
		// ('a'/'u', Permission-Name, Enable? 'Y'/'N', Language string, array of user-group-ids that should have this permission)
		// Groups: 1 = Guests, 2 = Super-Admin, 3 = Admin, 4 = Member
		$this->add_permission('u', 'view', 'Y', $this->user->lang('view'), array(2,3,4));
		$this->add_permission('a', 'add', 'N', $this->user->lang('add'), array(2,3));
		$this->add_permission('a', 'manage', 'N', $this->user->lang('manage'), array(2,3));

		// -- PDH Modules -------------------------------------
		$this->add_pdh_read_module('awards_achievements');
		$this->add_pdh_read_module('awards_assignments');
		$this->add_pdh_read_module('awards_library');
		
		$this->add_pdh_write_module('awards_achievements');
		$this->add_pdh_write_module('awards_assignments');

		// -- Classes -----------------------------------------
		registry::add_class('awards_plugin', 'plugins/awards/classes/', 'awards');

		// -- Routing -----------------------------------------
		$this->routing->addRoute('Awards', 'awards', 'plugins/awards/pageobjects');

		// -- Hooks -------------------------------------------
		$this->add_hook('portal', 'awards_portal_hook', 'portal');
  		$this->add_hook('userprofile_customtabs', 'awards_userprofile_customtabs_hook', 'userprofile_customtabs');

		// -- Menu --------------------------------------------
		$this->add_menu('admin', $this->gen_admin_menu());
		$this->add_menu('main', $this->gen_main_menu());
		$this->add_menu('settings', $this->usersettings());

	}


	/**
	  * pre_install
	  * Define Pre Installation
	  */
	public function pre_install(){
		// include SQL and default configuration data for installation
		include($this->root_path.'plugins/awards/includes/sql.php');

		// define installation
		for ($i = 1; $i <= count($awardsSQL['install']); $i++)
		  $this->add_sql(SQL_INSTALL, $awardsSQL['install'][$i]);
	}


	/**
	  * post_install
	  * Define Post Installation
	  */
	public function post_install(){
		// install ntfy and cron
		$this->ntfy->addNotificationType('awards_new_award', 'notification_awards_new_award', 'awards', 0, 1, 0, NULL, 3, 'fa-gift');
		
		$this->cronjobs->add_cron(
			'awards', array(
				'extern'		=> true,
				'ajax'			=> true,
				'delay'			=> false,
				'repeat'		=> true,
				'repeat_type'	=> 'daily',
				'active'		=> true,
				'path'			=> '/plugins/awards/cronjob/',
				'editable'		=> true,
				'description'	=> 'Plugin: Awards'
			)
		);
	}


	/**
	  * pre_uninstall
	  * Define Pre Uninstall
	  */
	public function pre_uninstall(){
		$arrAssignmentIDs = $this->pdh->get('awards_assignments', 'id_list');
		foreach($arrAssignmentIDs as $intAssignmentID){
			$intAdjID = $this->pdh->get('awards_assignments', 'adj_id', array($intAssignmentID));
			$this->pdh->put('adjustment', 'delete_adjustment', array($intAdjID));
		}
		
		$this->ntfy->deleteNotificationType(array('awards_new_award'));
		$this->cronjobs->del_cron('awards');
	}


	/**
	  * post_uninstall
	  * Define Post Uninstall
	  */
	public function post_uninstall(){
		// include SQL data for uninstallation
		include($this->root_path.'plugins/awards/includes/sql.php');

		// define uninstallation
		for ($i = 1; $i <= count($awardsSQL['uninstall']); $i++)
		  $this->db->query($awardsSQL['uninstall'][$i]);
	}


	/**
	  * gen_admin_menu
	  * Generate the Admin Menu
	  */
	private function gen_admin_menu(){
		$admin_menu = array (array(
			'name' => $this->user->lang('awards'),
			'icon' => 'fa fa-mortar-board',
			1 => array(
				'link'  => 'plugins/awards/admin/manage_achievements.php'.$this->SID,
				'text'  => $this->user->lang('aw_manage_achievements'),
				'check' => 'a_awards_manage',
				'icon'  => 'fa-gift'
			),
			2 => array(
				'link'  => 'plugins/awards/admin/manage_assignments.php'.$this->SID,
				'text'  => $this->user->lang('aw_manage_assignments'),
				'check' => 'a_awards_manage',
				'icon'  => 'fa-list'
			),
		));
		
		return $admin_menu;
	}


	/**
	  * gen_main_menu
	  * Generate the Main Menu
	  */
	private function gen_main_menu(){
		$main_menu = array(
			1 => array (
				'link'	 => $this->routing->build('Awards', false, false, true, true),
				'text'	 => $this->user->lang('awards'),
				'check'	 => 'u_awards_view',
			),
    	);
		
		return $main_menu;
	}


	private function usersettings(){
		if (!$this->user->check_auth('u_awards_view', false)) return array();
		$settings = array(
			'awards' => array(
				'awards' => array(
					'aw_show_hook'	=> array(
						'type'		=> 'radio',
					),
					'aw_layout'	=> array(
						'type'	  => 'dropdown',
						'tolang'  => true,
						'options' => array(
							'default'	 => 'user_sett_f_aw_layout_default',
							'minimalist' => 'user_sett_f_aw_layout_minimalist',
						),
					),
					'aw_pagination' => array(
						'type'	  => 'spinner',
						'check'	  => 'u_awards_view',
						'size'	  => 5,
						'min'	  => 5,
						'step'	  => 5,
						'default' => 25
					),
					'aw_admin_pagination' => array(
						'type'	  => 'spinner',
						'check'	  => 'a_awards_manage',
						'size'	  => 5,
						'min'	  => 10,
						'step'	  => 10,
						'default' => 100
					),
				)
			),
		);
		return $settings;
	}

}
?>
