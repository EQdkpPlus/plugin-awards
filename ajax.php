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
		$this->user->check_auth('a_awards_add');
		register("pm");
		
		$handler = array(
			'active'	=> array('process' => 'set_active'),
			'special'	=> array('process' => 'set_special'),
			'sort'		=> array('process' => 'set_sort_ids'),
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
				
				die(json_encode(array(
					'error_code' => 0,
					'error' => $this->user->lang('success')
				)));
			}
		}
		
		die(json_encode(array(
			'error_code' => 1,
			'error' => $this->user->lang('error')
		)));
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
				
				die(json_encode(array(
					'error_code' => 0,
					'error' => $this->user->lang('success')
				)));
			}
		}
		
		die(json_encode(array(
			'error_code' => 1,
			'error' => $this->user->lang('error')
		)));
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
			
			die(json_encode(array(
				'error_code' => 0,
				'error' => $this->user->lang('success')
			)));
		}
		
		die(json_encode(array(
			'error_code' => 1,
			'error' => $this->user->lang('error')
		)));
	}


	public function display(){
		die(json_encode(array(
			'error_code' => 1,
			'error' => $this->user->lang('error')
		)));
	}
}
registry::register('AjaxAwards');
?>