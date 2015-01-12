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
  | pdh_r_awards_assignments
  +--------------------------------------------------------------------------*/
if ( !class_exists( "pdh_r_awards_assignments" ) ) {
	class pdh_r_awards_assignments extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $default_lang = 'english';
	public $awards_assignments = null;

	public $hooks = array(
		'awards_assignments_update',
	);

	public $presets = array(
		'awards_assignments_id'				=> array('id', array('%intAssignmentID%'), array()),
		'awards_assignments_date' 			=> array('date', array('%intAssignmentID%'), array()),
		'awards_assignments_achievement_id' => array('achievement_id', array('%intAssignmentID%'), array()),
		'awards_assignments_adj_id' 		=> array('adj_id', array('%intAssignmentID%'), array()),
		'awards_assignments_adj_group_key'  => array('adj_group_key', array('%intAssignmentID%'), array()),
	);

	public function reset(){
			$this->pdc->del('pdh_awards_assignments_table');

			$this->awards_assignments = NULL;
	}

	public function init(){
			$this->awards_assignments	= $this->pdc->get('pdh_awards_assignments_table');

			if($this->awards_assignments !== NULL){
				return true;
			}

			$objQuery = $this->db->query('SELECT * FROM __awards_assignments');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					//TODO: Check if id Column is available
					$this->awards_assignments[(int)$drow['id']] = array(
						'id'				=> (int)$drow['id'],
						'date'				=> (int)$drow['date'],
						'achievement_id'	=> (int)$drow['achievement_id'],
						'adj_id'			=> (int)$drow['adj_id'],
						'adj_group_key'		=> $drow['adj_group_key'],

					);
				}

				$this->pdc->put('pdh_awards_assignments_table', $this->awards_assignments, null);
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */
		public function get_id_list(){
			if ($this->awards_assignments === null) return array();
			return array_keys($this->awards_assignments);
		}

		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */
		public function get_data($intAssignmentID){
			if (isset($this->awards_assignments[$intAssignmentID])){
				return $this->awards_assignments[$intAssignmentID];
			}
			return false;
		}

		/**
		 * Returns id for $intAssignmentID
		 * @param integer $intAssignmentID
		 * @return multitype id
		 */
		 public function get_id($intAssignmentID){
			if (isset($this->awards_assignments[$intAssignmentID])){
				return $this->awards_assignments[$intAssignmentID]['id'];
			}
			return false;
		}

		/**
		 * Returns date for $intAssignmentID
		 * @param integer $intAssignmentID
		 * @return multitype date
		 */
		 public function get_date($intAssignmentID){
			if (isset($this->awards_assignments[$intAssignmentID])){
				return $this->awards_assignments[$intAssignmentID]['date'];
			}
			return false;
		}
		
		public function get_html_date($intAssignmentID) {
			return $this->time->user_date($this->get_date($intAssignmentID));
		}

		/**
		 * Returns achievement_id for $intAssignmentID
		 * @param integer $intAssignmentID
		 * @return multitype achievement_id
		 */
		 public function get_achievement_id($intAssignmentID){
			if (isset($this->awards_assignments[$intAssignmentID])){
				return $this->awards_assignments[$intAssignmentID]['achievement_id'];
			}
			return false;
		}

		/**
		 * Returns adj_id for $intAssignmentID
		 * @param integer $intAssignmentID
		 * @return multitype adj_id
		 */
		 public function get_adj_id($intAssignmentID){
			if (isset($this->awards_assignments[$intAssignmentID])){
				return $this->awards_assignments[$intAssignmentID]['adj_id'];
			}
			return false;
		}

		/**
		 * Returns adj_group_key for $intAssignmentID
		 * @param integer $intAssignmentID
		 * @return multitype adj_group_key
		 */
		 public function get_adj_group_key($intAssignmentID){
			if (isset($this->awards_assignments[$intAssignmentID])){
				return $this->awards_assignments[$intAssignmentID]['adj_group_key'];
			}
			return false;
		}
		
		public function get_checkbox_check($intAchievementID){
			return true;
		}

	}//end class
}//end if
?>