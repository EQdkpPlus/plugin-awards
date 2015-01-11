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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_w_awards_achievements
  +--------------------------------------------------------------------------*/
if(!class_exists('pdh_w_awards_achievements')) {
  class pdh_w_awards_achievements extends pdh_w_generic
  {

	private $arrLogLang = array(
		'id'			=> "{L_ID}",
		'name'			=> "{L_NAME}",
		'description'	=> "{L_DESCRIPTION}",
		'published'		=> "{L_PUBLISHED}",
		'sort_id'		=> "{L_SORTATION}",		
	);



	/**
	  * Delete all selected Awards
	  */
	public function delete($id) {
		$arrAwards = $this->pdh->get('awards_achievements', 'id_list_for_category', array($id));
		if (isset($arrMedia[0]) && count($arrAwards)){
			foreach($arrAwards[0] as $intAwardID){
				$this->pdh->put('awards_achievements', 'delete', array($intAwardID));
			}
		}
		
		$this->delete_recursiv(intval($id));
		
		$this->pdh->enqueue_hook('articles_update');
		$this->pdh->enqueue_hook('awards_achievements_update');
		return true;
	}
		
	private function delete_recursiv($intAwardID){
		$arrOldData = $this->pdh->get('awards_achievements', 'data', array($intAwardID));
		$this->db->prepare("DELETE FROM __awards_achievements WHERE id =?")->execute($intAwardID);
		
		$log_action = $this->logs->diff(false, $arrOldData, $this->arrLogLang);
		$this->log_insert("action_award_deleted", $log_action, $intAwardID, $arrOldData["name"],  1, 'awards');
		
		return true;
	}
	
	
	/**
	  * Add Award
	  */
	public function add($strName, $strDescription, $intActive, $intSpecial, $intValue,
						$strImage, $arrImageColors, $strAdjustment, $intAdjustmentValue){
		// Parse TinyMC Code of 'Description'
		#$strDescription = $this->bbcode->replace_shorttags($strDescription);
		#$strDescription = $this->embedly->parseString($strDescription);
		
		$arrQuery  = array(
			'name' 				=> $strName,
			'description'		=> $strDescription,
			'sort_id'			=> 99999999,
			'active'			=> $intActive,
			'special'			=> $intSpecial,
			'value'				=> $intValue,
			'image' 			=> $strImage,
			'image_colors'		=> serialize($arrImageColors),
			'adjustment' 		=> $strAdjustment,
			'adjustment_value'	=> $intAdjustmentValue,
		);
		
		$objQuery = $this->db->prepare("INSERT INTO __awards_achievements :p")->set($arrQuery)->execute();
		
		if ($objQuery){
			$id = $objQuery->insertId;
			$log_action = $this->logs->diff(false, $arrQuery, $this->arrLogLang);
			$this->log_insert("action_award_added", $log_action, $id, $arrQuery["name"], 1, 'awards');
			
			$this->pdh->enqueue_hook('awards_achievements_update');
			return $id;
		}
		
		return false;
	}
		
		
  } //end class
} //end if class not exists
?>