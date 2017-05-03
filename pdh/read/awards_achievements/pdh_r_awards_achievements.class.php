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
		'awards_achievements_dkp'  				=> array('dkp', array('%intAchievementID%'), array()),
		'awards_achievements_icon' 				=> array('icon', array('%intAchievementID%'), array()),
		'awards_achievements_icon_colors'  		=> array('icon_colors', array('%intAchievementID%'), array()),
		'awards_achievements_module' 			=> array('module', array('%intAchievementID%'), array()),
		'awards_achievements_module_set' 		=> array('module_set', array('%intAchievementID%'), array()),
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

			$objQuery = $this->db->query('SELECT * FROM __awards_achievements ORDER BY sort_id ASC');
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
						'dkp'				=> (float)$drow['dkp'],
						'icon'				=> $drow['icon'],
						'icon_colors'		=> $drow['icon_colors'],
						'module'			=> $drow['module'],
						'module_set'		=> $drow['module_set'],
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
			if($this->user->check_auth('a_awards_add')){
				return '<a href="'.$this->root_path.'plugins/awards/admin/manage_achievements.php'.$this->SID.'&aid='.$intAchievementID.'"><strong>'.$this->user->multilangValue($this->get_name($intAchievementID)).'</strong></a>';
			}
			return '<strong>'.$this->user->multilangValue($this->get_name($intAchievementID)).'</strong>';
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
		
		public function get_html_description($intAchievementID){
			return $this->user->multilangValue($this->get_description($intAchievementID));
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
		
		public function get_html_sort_id($intAchievementID){
			return '<span class="ui-icon ui-icon-arrowthick-2-n-s" title="'.$this->user->lang('dragndrop').'"></span>
					<input type="hidden" name="sort_ids['.$intAchievementID.']" value="'.$this->get_sort_id($intAchievementID).'"/>';
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
			if($this->get_active($intAchievementID)){
				return '<i class="fa aw_toggle_active enabled" style="cursor: pointer;"></i>
						<input type="hidden" class="active_cb" name="'.$intAchievementID.'" value="1"/>';
			} else {
				return '<i class="fa aw_toggle_active disabled" style="cursor: pointer;"></i>
						<input type="hidden" class="active_cb" name="'.$intAchievementID.'" value="0"/>';
			}
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
			if($this->get_special($intAchievementID)){
				return '<i class="fa aw_toggle_special enabled" data-aid="'.$intAchievementID.'"></i>
						<input type="hidden" class="special_cb" name="'.$intAchievementID.'" value="1"/>';
			} else {
				return '<i class="fa aw_toggle_special disabled" data-aid="'.$intAchievementID.'"></i>
						<input type="hidden" class="special_cb" name="'.$intAchievementID.'" value="0"/>';
			}
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
			return $this->get_points($intAchievementID).' <i class="fa fa-bookmark-o"></i>';
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
		
		public function get_html_dkp($intAchievementID){
			return $this->get_dkp($intAchievementID).' <i class="fa fa-trophy fa-lg" />';
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

		public function get_html_icon($intAchievementID){
			$strAchIcon = $this->get_icon($intAchievementID);
			$arrAchIconColors = unserialize($this->get_icon_colors($intAchievementID));
			
			$icon_folder = $this->pfh->FolderPath('images', 'awards');
			if( file_exists($icon_folder.$strAchIcon) ){
				$strAchIcon = $this->pfh->FolderPath('images', 'awards', 'absolute').$strAchIcon;
			} else {
				$strAchIcon = $this->root_path.'plugins/awards/images/'.$strAchIcon;
			}
			
			if( pathinfo($strAchIcon, PATHINFO_EXTENSION) == "svg"){
				$strAchIcon		= '
					<div class="aw-'.$intAchievementID.'">
						'.file_get_contents($strAchIcon).'
						<div class="icon_colors" style="display: none;">
				';
				$icon_color_step = 1;
				foreach($arrAchIconColors as $strAchIconColor){
					$strAchIcon	.= '<i class="color-'.$icon_color_step.'">'.$strAchIconColor.'</i>';
					$icon_color_step++;
				}
				$strAchIcon	.= '</div></div>';
				
			} else {
				$strAchIcon = '<img src="'.$strAchIcon.'" style="height: 28px; width: 28px; margin: -4px 0px;" />';
			}
			
			return $strAchIcon;
		}

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
		 * Returns module_set for $intAchievementID
		 * @param integer $intAchievementID
		 * @return multitype module_set
		 */
		public function get_module_set($intAchievementID){
			if (isset($this->awards_achievements[$intAchievementID])){
				return $this->awards_achievements[$intAchievementID]['module_set'];
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

		public function get_checkbox_check($intAchievementID){
			return true;
		}

	}//end class
}//end if
?>