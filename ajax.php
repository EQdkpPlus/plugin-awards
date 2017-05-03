<?php
/*	Project:	EQdkp-Plus
 *	Package:	Awards  Plugin
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

$eqdkp_root_path = './../../';
include_once($eqdkp_root_path.'common.php');

/*+----------------------------------------------------------------------------
  | awards_ajax_handling
  +--------------------------------------------------------------------------*/
class AjaxAwards extends page_generic {
	/**
	 * Constructor
	 */
	public function __construct(){
		if(!$this->user->check_auth('a_awards_add', false)) $this->display();
		register("pm");
		
		$handler = array(
			'active'	=> array('process' => 'set_active', 'csrf' => true),
			'special'	=> array('process' => 'set_special', 'csrf' => true),
			'sort'		=> array('process' => 'set_sort_ids', 'csrf' => true),
			'module'	=> array('process' => 'module_settings', 'csrf' => true),
		);
		parent::__construct(false, $handler);
		$this->process();
	}


	/**
	 * set Active
	 */
	public function set_active(){
		$intAchID		= $this->in->get('id', 0);
		$blnAchActive	= $this->in->get('value', 1);
		
		if($intAchID > 0){
			if( $this->pdh->put('awards_achievements', 'set_active', array($intAchID, $blnAchActive)) ){
				$this->pdh->process_hook_queue();
				
				die(json_encode([
					'error' => 0,
					'return' => ''
				]));
			}
		}
		
		die(json_encode([
			'error' => 1,
			'return' => 'Error: Cannot change state'
		]));
	}


	/**
	 * set Special
	 */
	public function set_special(){
		$intAchID		= $this->in->get('id', 0);
		$blnAchSpecial	= $this->in->get('value', 0);
		
		if($intAchID > 0){
			if( $this->pdh->put('awards_achievements', 'set_special', array($intAchID, $blnAchSpecial)) ){
				$this->pdh->process_hook_queue();
				
				die(json_encode([
					'error' => 0,
					'return' => ''
				]));
			}
		}
		
		die(json_encode([
			'error' => 1,
			'return' => 'Error: Cannot change state'
		]));
	}


	/**
	 * sort Awards
	 */
	public function set_sort_ids(){
		$arrSortIDs		= $this->in->getArray('sort_ids');
		$intAchSortID	= 1;
		
		if(count($arrSortIDs)){
			foreach($arrSortIDs as $intAchID => $old_sort_id){
				$this->pdh->put('awards_achievements', 'set_sort_id', array($intAchID, $intAchSortID));
				$intAchSortID++;
			}
			$this->pdh->process_hook_queue();
			
			die(json_encode([
				'error' => 0,
				'return' => $this->user->lang('success')
			]));
		}
		
		die(json_encode([
			'error' => 1,
			'return' => $this->user->lang('error')
		]));
	}


	/**
	 * Get Module Settings
	 */
	public function module_settings(){
		$strModuleName		= $this->in->get('module_name', '');
		$jsonModuleSettings	= unsanitize($this->in->get('module_settings', ''));
		
		if(!empty($strModuleName)){
			include_once $this->root_path.'plugins/awards/cronjob/modules/'.$strModuleName.'_cronmodule.class.php';
			$strModuleClass	= $strModuleName.'_cronmodule';
			
			if(class_exists($strModuleClass)){
				die(json_encode([
					'error' => 0,
					'return' => (new $strModuleClass)->display_settings($jsonModuleSettings)
				]));
			}
		}
		
		die(json_encode([
			'error' => 1,
			'return' => $this->user->lang('aw_module_load_error')
		]));
	}


	/**
	 * Fallback if request method not found or auth failed
	 */
	public function display(){
		die(json_encode([
			'error' => 1,
			'return' => 'Error: Bad Request'
		]));
	}
}
registry::register('AjaxAwards');
?>