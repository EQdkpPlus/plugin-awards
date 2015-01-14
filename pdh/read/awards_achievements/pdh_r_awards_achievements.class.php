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
  | pdh_r_awards_achievements
  +--------------------------------------------------------------------------*/
if ( !class_exists( "pdh_r_awards_achievements" ) ) {
	class pdh_r_awards_achievements extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array();
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $default_lang = 'english';
	public $awards_achievements = null;

	public $hooks = array(
		'awards_achievements_update',
	);

	public $presets = array(
		'awards_achievements_id' 				=> array('id', array('%intAchievementID%'), array()),
		'awards_achievements_name' 				=> array('name', array('%intAchievementID%'), array()),
		'awards_achievements_description'		=> array('description', array('%intAchievementID%'), array()),
		'awards_achievements_sort_id'			=> array('sort_id', array('%intAchievementID%'), array()),
		'awards_achievements_active' 			=> array('active', array('%intAchievementID%'), array()),
		'awards_achievements_special' 			=> array('special', array('%intAchievementID%'), array()),
		'awards_achievements_points' 			=> array('points', array('%intAchievementID%'), array()),
		'awards_achievements_icon' 				=> array('icon', array('%intAchievementID%'), array()),
		'awards_achievements_icon_colors'  		=> array('icon_colors', array('%intAchievementID%'), array()),
		'awards_achievements_module' 			=> array('module', array('%intAchievementID%'), array()),
		'awards_achievements_dkp'  				=> array('dkp', array('%intAchievementID%'), array()),
		'awards_achievements_event_id'			=> array('event_id', array('%intAchievementID%'), array()),
	);

	public function reset(){
			$this->pdc->del('pdh_awards_achievements_table');

			$this->awards_achievements = NULL;
	}

	public function init(){
			$this->awards_achievements	= $this->pdc->get('pdh_awards_achievements_table');

			if($this->awards_achievements !== NULL){
				return true;
			}

			$objQuery = $this->db->query('SELECT * FROM __awards_achievements');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					//TODO: Check if id Column is available
					$this->awards_achievements[(int)$drow['id']] = array(
						'id'				=> (int)$drow['id'],
						'name'				=> $drow['name'],
						'description'		=> $drow['description'],
						'sort_id'			=> (int)$drow['sort_id'],
						'active'			=> (int)$drow['active'],
						'special'			=> (int)$drow['special'],
						'points'			=> (int)$drow['points'],
						'icon'				=> $drow['icon'],
						'icon_colors'		=> $drow['icon_colors'],
						'module'			=> $drow['module'],
						'dkp'				=> (float)$drow['dkp'],
						'event_id'			=> (int)$drow['event_id'],
					);
				}

				$this->pdc->put('pdh_awards_achievements_table', $this->awards_achievements, null);
			}

		}	//end init function

		/**
		 * @return multitype: List of all IDs
		 */
		public function get_id_list(){
			if ($this->awards_achievements === null) return array();
			return array_keys($this->awards_achievements);
		}

		/**
		 * Get all data of Element with $strID
		 * @return multitype: Array with all data
		 */
		public function get_data($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID];
			}
			return false;
		}

		/**
		 * Returns id for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype id
		 */
		 public function get_id($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['id'];
			}
			return false;
		}

		/**
		 * Returns name for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype name
		 */
		 public function get_name($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['name'];
			}
			return false;
		}
		
		public function get_html_name($intAchievementID){
			return '<a href="'.$this->root_path.'plugins/awards/admin/manage_achievements.php'.$this->SID.'&aid='.$intAchievementID.'"><strong>'.$this->get_name($intAchievementID).'</strong></a>';
		}

		/**
		 * Returns description for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype description
		 */
		 public function get_description($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['description'];
			}
			return false;
		}

		/**
		 * Returns sort_id for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype sort_id
		 */
		 public function get_sort_id($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['sort_id'];
			}
			return false;
		}
		
		public function get_html_sort_id($intCategoryID){
			return '<span class="ui-icon ui-icon-arrowthick-2-n-s" title="'.$this->user->lang('dragndrop').'"></span><input type="hidden" name="sortCategories[]" value="'.$intCategoryID.'"/>';
		}

		/**
		 * Returns active for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype active
		 */
		 public function get_active($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['active'];
			}
			return false;
		}
		
		public function get_html_active($intAchievementID){
			if ($this->get_active($intAchievementID)){
				$strImage = '<div><i class="fa fa-check-square-o fa-lg icon-color-green activeToggleTrigger" style="cursor: pointer;"></i></div><input type="hidden" class="active_cb" name="active['.$intAchievementID.']" value="1"/></div>';
			} else {
				$strImage = '<div><i class="fa fa-square-o fa-lg icon-color-red activeToggleTrigger" style="cursor: pointer;"></i></div><input type="hidden" class="active_cb" name="active['.$intAchievementID.']" value="0"/></div>';
			}
			return $strImage;
		}

		/**
		 * Returns special for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype special
		 */
		 public function get_special($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['special'];
			}
			return false;
		}
		
		public function get_html_special($intAchievementID){
			if ($this->get_special($intAchievementID)){
				$strImage = '<div><div class="eye eyeToggleTrigger"></div><input type="hidden" class="special_cb" name="special['.$intAchievementID.']" value="1"/></div>';
			} else {
				$strImage = '<div><div class="eye-gray eyeToggleTrigger"></div><input type="hidden" class="special_cb" name="special['.$intAchievementID.']" value="0"/></div>';
			}
			return $strImage;
		}

		/**
		 * Returns points for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype points
		 */
		 public function get_points($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['points'];
			}
			return false;
		}
		
		public function get_html_points($intAchievementID){
			return $this->get_points($intAchievementID).' <span class="adminicon" />';
		}

		/**
		 * Returns icon for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype icon
		 */
		 public function get_icon($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['icon'];
			}
			return false;
		}
		
		// hierfür bite in game.class.php nachschauen, abändern auf this->awards->decorate()
		/*public function get_html_icon($intAchievementID, $width=30){
			return $this->game->decorate('primary', $intAchievementID, array(), $width);
		}*/
		
		/*public function get_icon($event_id, $withpath=false){
			if($withpath) return $this->game->decorate('events', $event_id, array(), 0, true);
			return $this->events[$event_id]['icon'];
		}*/

		/**
		 * Returns icon_colors for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype icon_colors
		 */
		 public function get_icon_colors($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['icon_colors'];
			}
			return false;
		}

		/**
		 * Returns module for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype module
		 */
		 public function get_module($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['module'];
			}
			return false;
		}

		/**
		 * Returns dkp for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype dkp
		 */
		 public function get_dkp($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['dkp'];
			}
			return false;
		}
		
		/**
		 * Returns event_id for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype event_id
		 */
		 public function get_event_id($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['event_id'];
			}
			return false;
		}
		
		
		
		// Glaube das ist ein überbleibsel und wird nichtmehr verwendet, bin mir aber nicht sicher
		public function get_checkbox_check($intAchievementID){
			return true;
		}

	}//end class
}//end if
?>