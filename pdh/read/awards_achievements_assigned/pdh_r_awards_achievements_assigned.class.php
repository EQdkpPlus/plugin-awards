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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_r_awards_achievements_assigned
  +--------------------------------------------------------------------------*/
if ( !class_exists( "pdh_r_awards_achievements_assigned" ) ) {
	class pdh_r_awards_achievements_assigned extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $default_lang = 'english';
	public $awards_achievements_assigned = null;

	public $hooks = array(
		'awards_achievements_assigned_update',
	);

	public $presets = array(
		'awards_achievements_assigned_id' => array('id', array('%intAwardID%'), array()),
		'awards_achievements_assigned_date' => array('date', array('%intAwardID%'), array()),
		'awards_achievements_assigned_user_id' => array('user_id', array('%intAwardID%'), array()),
		'awards_achievements_assigned_award_id' => array('award_id', array('%intAwardID%'), array()),
	);

	public function reset(){
			$this->pdc->del('pdh_awards_achievements_assigned_table');

			$this->awards_achievements_assigned = NULL;
	}

	public function init(){
			$this->awards_achievements_assigned	= $this->pdc->get('pdh_awards_achievements_assigned_table');

			if($this->awards_achievements_assigned !== NULL){
				return true;
			}

			$objQuery = $this->db->query('SELECT * FROM __awards_achievements_assigned');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					//TODO: Check if id Column is available
					$this->awards_achievements_assigned[(int)$drow['id']] = array(
						'id'			=> (int)$drow['id'],
						'date'			=> (int)$drow['date'],
						'user_id'			=> (int)$drow['user_id'],
						'award_id'			=> (int)$drow['award_id'],

					);
				}

				$this->pdc->put('pdh_awards_achievements_assigned_table', $this->awards_achievements_assigned, null);
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */
		public function get_id_list(){
			if ($this->awards_achievements_assigned === null) return array();
			return array_keys($this->awards_achievements_assigned);
		}

		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */
		public function get_data($intAwardID){
			if (isset($this->awards_achievements_assigned[$intAwardID])){
				return $this->awards_achievements_assigned[$intAwardID];
			}
			return false;
		}

		/**
		 * Returns id for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype id
		 */
		 public function get_id($intAwardID){
			if (isset($this->awards_achievements_assigned[$intAwardID])){
				return $this->awards_achievements_assigned[$intAwardID]['id'];
			}
			return false;
		}

		/**
		 * Returns date for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype date
		 */
		 public function get_date($intAwardID){
			if (isset($this->awards_achievements_assigned[$intAwardID])){
				return $this->awards_achievements_assigned[$intAwardID]['date'];
			}
			return false;
		}

		/**
		 * Returns user_id for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype user_id
		 */
		 public function get_user_id($intAwardID){
			if (isset($this->awards_achievements_assigned[$intAwardID])){
				return $this->awards_achievements_assigned[$intAwardID]['user_id'];
			}
			return false;
		}

		/**
		 * Returns award_id for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype award_id
		 */
		 public function get_award_id($intAwardID){
			if (isset($this->awards_achievements_assigned[$intAwardID])){
				return $this->awards_achievements_assigned[$intAwardID]['award_id'];
			}
			return false;
		}

	}//end class
}//end if
?>