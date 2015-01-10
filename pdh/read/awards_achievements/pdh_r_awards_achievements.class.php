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
		'awards_achievements_id' 				=> array('id', array('%intAwardID%'), array()),
		'awards_achievements_name' 				=> array('name', array('%intAwardID%'), array()),
		'awards_achievements_description'		=> array('description', array('%intAwardID%'), array()),
		'awards_achievements_sort_id'			=> array('sort_id', array('%intAwardID%'), array()),
		'awards_achievements_active' 			=> array('active', array('%intAwardID%'), array()),
		'awards_achievements_special' 			=> array('special', array('%intAwardID%'), array()),
		'awards_achievements_value' 			=> array('value', array('%intAwardID%'), array()),
		'awards_achievements_image' 			=> array('image', array('%intAwardID%'), array()),
		'awards_achievements_image_colors'  	=> array('image_colors', array('%intAwardID%'), array()),
		'awards_achievements_adjustment' 		=> array('adjustment', array('%intAwardID%'), array()),
		'awards_achievements_adjustment_value'  => array('adjustment_value', array('%intAwardID%'), array()),
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
						'id'			=> (int)$drow['id'],
						'name'			=> $drow['name'],
						'description'			=> $drow['description'],
						'sort_id'			=> (int)$drow['sort_id'],
						'active'			=> (int)$drow['active'],
						'special'			=> (int)$drow['special'],
						'value'			=> (int)$drow['value'],
						'image'			=> $drow['image'],
						'image_colors'			=> $drow['image_colors'],
						'adjustment'			=> $drow['adjustment'],
						'adjustment_value'			=> (int)$drow['adjustment_value'],

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
		public function get_data($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID];
			}
			return false;
		}

		/**
		 * Returns id for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype id
		 */
		 public function get_id($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['id'];
			}
			return false;
		}

		/**
		 * Returns name for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype name
		 */
		 public function get_name($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['name'];
			}
			return false;
		}
		
		public function get_html_name($intAwardID, $strLink, $strSuffix){
			return '<a href="'.$this->root_path.'plugins/awards/admin/add_award.php'.$this->SID.'&aid='.$intAwardID.'">'.$this->get_name($intAwardID).'</a>';
		}

		/**
		 * Returns description for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype description
		 */
		 public function get_description($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['description'];
			}
			return false;
		}

		/**
		 * Returns sort_id for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype sort_id
		 */
		 public function get_sort_id($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['sort_id'];
			}
			return false;
		}
		
		public function get_html_sort_id($intCategoryID){
			return '<span class="ui-icon ui-icon-arrowthick-2-n-s" title="'.$this->user->lang('dragndrop').'"></span><input type="hidden" name="sortCategories[]" value="'.$intCategoryID.'"/>';
		}

		/**
		 * Returns active for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype active
		 */
		 public function get_active($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['active'];
			}
			return false;
		}
		
		public function get_html_active($intAwardID){
			if ($this->get_active($intAwardID)){
				$strImage = '<div><i class="fa fa-check-square-o fa-lg icon-color-green activeToggleTrigger" style="cursor: pointer;"></i></div><input type="hidden" class="active_cb" name="active['.$intAwardID.']" value="1"/></div>';
			} else {
				$strImage = '<div><i class="fa fa-square-o fa-lg icon-color-red activeToggleTrigger" style="cursor: pointer;"></i></div><input type="hidden" class="active_cb" name="active['.$intAwardID.']" value="0"/></div>';
			}
			return $strImage;
		}

		/**
		 * Returns special for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype special
		 */
		 public function get_special($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['special'];
			}
			return false;
		}
		
		public function get_html_special($intAwardID){
			if ($this->get_special($intAwardID)){
				$strImage = '<div><div class="eye eyeToggleTrigger"></div><input type="hidden" class="special_cb" name="special['.$intAwardID.']" value="1"/></div>';
			} else {
				$strImage = '<div><div class="eye-gray eyeToggleTrigger"></div><input type="hidden" class="special_cb" name="special['.$intAwardID.']" value="0"/></div>';
			}
			return $strImage;
		}

		/**
		 * Returns value for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype value
		 */
		 public function get_value($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['value'];
			}
			return false;
		}
		
		public function get_html_value($intAwardID, $strLink, $strSuffix){
			return $this->get_value($intAwardID).' <span class="adminicon" />';
		}

		/**
		 * Returns image for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype image
		 */
		 public function get_image($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['image'];
			}
			return false;
		}

		/**
		 * Returns image_colors for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype image_colors
		 */
		 public function get_image_colors($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['image_colors'];
			}
			return false;
		}

		/**
		 * Returns adjustment for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype adjustment
		 */
		 public function get_adjustment($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['adjustment'];
			}
			return false;
		}

		/**
		 * Returns adjustment_value for $intAwardID
		 * @param integer $intAwardID
		 * @return multitype adjustment_value
		 */
		 public function get_adjustment_value($intAwardID){
			if (isset($this->awards_achievements[$intAwardID])){
				return $this->awards_achievements[$intAwardID]['adjustment_value'];
			}
			return false;
		}
		
		
		
		
		
		
		
		
		
		
		public function get_checkbox_check($intAwardID){
			//if ($intCategoryID == 1) return false;
			return true;
		}

	}//end class
}//end if
?>