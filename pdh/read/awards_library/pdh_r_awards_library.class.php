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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_r_awards_library
  +--------------------------------------------------------------------------*/
if ( !class_exists( "pdh_r_awards_library" ) ) {
	class pdh_r_awards_library extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $default_lang = 'english';
	public $awards_library = NULL;

	public $hooks = array(
		'awards_library_update',
	);

	public $presets = array(
		'awards_library_id'				=> array('id', array('%intLibraryID%'), array()),
		'awards_library_date' 			=> array('date', array('%intLibraryID%'), array()),
		'awards_library_achievement_id' => array('achievement_id', array('%intLibraryID%'), array()),
		'awards_library_adj_id' 		=> array('adj_id', array('%intLibraryID%'), array()),
		'awards_library_adj_group_key'  => array('adj_group_key', array('%intLibraryID%'), array()),
	);

	public function reset(){
			$this->pdc->del('pdh_awards_library_table');

			$this->awards_library = NULL;
	}

	public function init(){
			$this->awards_library	= $this->pdc->get('pdh_awards_library_table');

			if($this->awards_library !== NULL){
				return true;
			}

			$objQuery = $this->db->query('
				SELECT *
				FROM __awards_assignments
				LEFT JOIN __adjustments ON __awards_assignments.adj_id = __adjustments.adjustment_id
				ORDER BY __awards_assignments.date DESC;
			');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					//TODO: Check if id Column is available
					$this->awards_library[(int)$drow['id']] = array(
						'id'				=> (int)$drow['id'],
						'date'				=> (int)$drow['date'],
						'achievement_id'	=> (int)$drow['achievement_id'],
						'adj_id'			=> $drow['adj_id'],
						'adj_group_key'		=> $drow['adj_group_key'],
						
						'adjustment_id'			=> (int)$drow['adjustment_id'],
						'adjustment_value'		=> $drow['adjustment_value'],
						'adjustment_date'		=> (int)$drow['adjustment_date'],
						'member_id'				=> (int)$drow['member_id'],
						'adjustment_reason'		=> $drow['adjustment_reason'],
						'adjustment_added_by'	=> $drow['adjustment_added_by'],
						'adjustment_updated_by' => $drow['adjustment_updated_by'],
						'adjustment_group_key'	=> $drow['adjustment_group_key'],
						'event_id'				=> (int)$drow['event_id'],
						'raid_id'				=> (int)$drow['raid_id'],
					);
				}

				$this->pdc->put('pdh_awards_library_table', $this->awards_library, null);
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */
		public function get_id_list(){
			if ($this->awards_library === null) return array();
			return array_keys($this->awards_library);
		}

		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */
		public function get_data($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID];
			}
			return false;
		}

		/**
		 * Returns id for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype id
		 */
		 public function get_id($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['id'];
			}
			return false;
		}

		/**
		 * Returns date for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype date
		 */
		 public function get_date($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['date'];
			}
			return false;
		}

		/**
		 * Returns achievement_id for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype achievement_id
		 */
		 public function get_achievement_id($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['achievement_id'];
			}
			return false;
		}

		/**
		 * Returns adj_id for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype adj_id
		 */
		 public function get_adj_id($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['adj_id'];
			}
			return false;
		}

		/**
		 * Returns adj_group_key for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype adj_group_key
		 */
		 public function get_adj_group_key($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['adj_group_key'];
			}
			return false;
		}

		/**
		 * Returns adjustment_id for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype adjustment_id
		 */
		 public function get_adjustment_id($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['adjustment_id'];
			}
			return false;
		}

		/**
		 * Returns adjustment_value for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype adjustment_value
		 */
		 public function get_adjustment_value($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['adjustment_value'];
			}
			return false;
		}

		/**
		 * Returns adjustment_date for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype adjustment_date
		 */
		 public function get_adjustment_date($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['adjustment_date'];
			}
			return false;
		}

		/**
		 * Returns member_id for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype member_id
		 */
		 public function get_member_id($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['member_id'];
			}
			return false;
		}

		/**
		 * Returns adjustment_reason for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype adjustment_reason
		 */
		 public function get_adjustment_reason($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['adjustment_reason'];
			}
			return false;
		}


		/**
		 * Returns adjustment_added_by for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype adjustment_added_by
		 */
		 public function get_adjustment_added_by($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['adjustment_added_by'];
			}
			return false;
		}

		/**
		 * Returns adjustment_updated_by for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype adjustment_updated_by
		 */
		 public function get_adjustment_updated_by($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['adjustment_updated_by'];
			}
			return false;
		}


		/**
		 * Returns adjustment_group_key for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype adjustment_group_key
		 */
		 public function get_adjustment_group_key($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['adjustment_group_key'];
			}
			return false;
		}

		/**
		 * Returns event_id for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype event_id
		 */
		 public function get_event_id($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['event_id'];
			}
			return false;
		}

		/**
		 * Returns raid_id for $intLibraryID
		 * @param integer $intLibraryID
		 * @return multitype raid_id
		 */
		 public function get_raid_id($intLibraryID){
			if (isset($this->awards_library[$intLibraryID])){
				return $this->awards_library[$intLibraryID]['raid_id'];
			}
			return false;
		}


/*+----------------------------------------------------------------------------
  | 	EXTENDED REQUESTS	--- UNDER CONSTRUCTION ---
  +--------------------------------------------------------------------------*/

		/**
		 * Get assignment_ids of $member_id
		 * @param integer $member_id
		 * @return multitype assignment_ids
		 */
		public function get_ids_of_member($member_id){
			$assignment_ids = array();
			if (is_array($this->awards_library)){
				foreach($this->awards_library as $id => $details){
					if($details['member_id'] == $member_id){
						$assignment_ids[] = $id;
					}
				}
			}
			return $assignment_ids;
		}


		/**
		 * Return the member_ids for $achievement_id
		 * @param integer $achievement_id
		 * @return multitype member_ids
		 */
		public function get_member_of_award($id){
			$member_ids = array();
			if (is_array($this->awards_library)){
				foreach($this->awards_library as $key => $value){
					if($value['achievement_id'] == $id){
						$member_ids[] = $this->awards_library[$key]['member_id'];
					}
				}
				$member_ids = array_unique($member_ids);
			}
			return $member_ids;
		}


		/**
		 * Check if the member has already this award
		 * @param integer $intAchID
		 * @param integer $intMemberID
		 * @return boolean
		 */
		public function get_member_has_award($intAchID, $intMemberID){
			if (is_array($this->awards_library)){
				foreach($this->awards_library as $awards){
					if($awards['achievement_id'] == $intAchID && $awards['member_id'] == $intMemberID){
						return true;
					}
				}
			}
			return false;
		}


		/**
		 * Get the date of member by award
		 * @param integer $intAchID
		 * @param integer $intMemberID
		 * @return integer: date
		 */
		public function get_member_date_by_award($intAchID, $intMemberID){
			if (is_array($this->awards_library)){
				foreach($this->awards_library as $awards){
					if($awards['achievement_id'] == $intAchID && $awards['member_id'] == $intMemberID){
						return $awards['date'];
					}
				}
			}
			return NULL;
		}


		/**
		 * Get the earliest date for $achievement_id
		 * @param integer $achievement_id
		 * @return integer: earliest date
		 */
		public function get_earliest_date_of_award($id){
			$dates = array();
			$earliest_date = PHP_INT_MAX;
			
			if(is_array($this->awards_library)){
				foreach($this->awards_library as $key => $value){
					if($value['achievement_id'] == $id){
						$dates[] = (int)$this->awards_library[$key]['date'];
					}
				}
				if(isset($dates[0])){
					foreach($dates as $date){
						$earliest_date = ($date < $earliest_date) ? $date : $earliest_date;
					}
					return $earliest_date;
				}
			}
			return NULL;
		}


	}//end class
}//end if
?>