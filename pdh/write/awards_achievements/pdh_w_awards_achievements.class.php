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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_w_awards_achievements
  +--------------------------------------------------------------------------*/
if(!class_exists('pdh_w_awards_achievements')) {
  class pdh_w_awards_achievements extends pdh_w_generic {
	private $arrLogLang = array(
		'id'				=> "{L_ID}",
		'name'				=> "{L_NAME}",
		'description'		=> "{L_DESCRIPTION}",
		'sort_id'			=> "{L_AW_SORTATION}",
		'active'			=> "{L_ACTIVE}",
		'special'			=> "{L_AW_SPECIAL}",
		'points'			=> "{L_AW_POINTS}",
		'dkp' 				=> "{L_AW_DKP}",
		'icon'				=> "{L_ICON}",
		'icon_colors'		=> "{L_AW_ICON_COLORS}",
		'module'			=> "{L_AW_MODULE}",
		'module_set'		=> "{L_AW_MODULE_SETTINGS}",
		'event_id' 			=> "{L_EVENT}",
	);


	/**
	 * Add a Achievement
	 */
	public function add($strName, $strDescription, $intAchSortID, $blnActive, $blnSpecial,
						$intPoints, $fltDKP, $strIcon, $arrIconColors, $strModule, $strModuleSet, $intEventID){
		$arrQuery  = array(
			'name' 				=> $strName,
			'description'		=> $strDescription,
			'sort_id'			=> $intAchSortID,
			'active'			=> $blnActive,
			'special'			=> $blnSpecial,
			'points'			=> $intPoints,
			'dkp'				=> $fltDKP,
			'icon' 				=> $strIcon,
			'icon_colors'		=> $arrIconColors,
			'module' 			=> $strModule,
			'module_set' 		=> $strModuleSet,
			'event_id'			=> $intEventID,
		);
		
		$objQuery = $this->db->prepare("INSERT INTO __awards_achievements :p")->set($arrQuery)->execute();
		
		if ($objQuery){
			$id = $objQuery->insertId;
			
			$log_action = $this->logs->diff(false, $arrQuery, $this->arrLogLang);
			
			$log_action['{L_NAME}']				  = $this->parse4log($log_action['{L_NAME}']);
			$log_action['{L_DESCRIPTION}']		  = $this->parse4log($log_action['{L_DESCRIPTION}']);
			$log_action['{L_AW_ICON_COLORS}']	  = $this->parse4log($log_action['{L_AW_ICON_COLORS}']);
			$log_action['{L_AW_MODULE_SETTINGS}'] = $this->parse4log($log_action['{L_AW_MODULE_SETTINGS}']);
			$log_action['{L_EVENT}']			  = $this->pdh->get('event', 'name', array($log_action['{L_EVENT}']));
			
			$this->log_insert("action_achievement_added", $log_action, $id, $log_action['{L_NAME}'], 1, 'awards');
			
			$this->pdh->enqueue_hook('awards_achievements_update');
			return $id;
		}
		
		return false;
	}


	/**
	 * Update a Achievement
	 */
	public function update($id, $strName, $strDescription, $intSortID, $blnActive, $blnSpecial,
							$intPoints, $fltDKP, $strIcon, $arrIconColors, $strModule, $strModuleSet, $intEventID){
		$arrQuery = array(
			'name' 				=> $strName,
			'description'		=> $strDescription,
			'sort_id'			=> $intSortID,
			'active'			=> $blnActive,
			'special'			=> $blnSpecial,
			'points'			=> $intPoints,
			'dkp'				=> $fltDKP,
			'icon' 				=> $strIcon,
			'icon_colors'		=> $arrIconColors,
			'module' 			=> $strModule,
			'module_set' 		=> $strModuleSet,
			'event_id'			=> $intEventID,
		);
		
		$arrOldData = $this->pdh->get('awards_achievements', 'data', array($id));
		
		$objQuery = $this->db->prepare("UPDATE __awards_achievements :p WHERE id=?")->set($arrQuery)->execute($id);
		
		if ($objQuery){
			$arrOldData['name']		   = $this->parse4log($arrOldData['name']);
			$arrOldData['description'] = $this->parse4log($arrOldData['description']);
			$arrOldData['icon_colors'] = $this->parse4log($arrOldData['icon_colors']);
			$arrOldData['module_set']  = $this->parse4log($arrOldData['module_set']);
			$arrOldData['event_id']	   = $this->pdh->get('event', 'name', array($arrOldData['event_id']));
			
			$arrQuery['name']		 = $this->parse4log($arrQuery['name']);
			$arrQuery['description'] = $this->parse4log($arrQuery['description']);
			$arrQuery['icon_colors'] = $this->parse4log($arrQuery['icon_colors']);
			$arrQuery['module_set']	 = $this->parse4log($arrQuery['module_set']);
			$arrQuery['event_id']	 = $this->pdh->get('event', 'name', array($arrQuery['event_id']));
			
			$log_action = $this->logs->diff($arrOldData, $arrQuery, $this->arrLogLang, array('description' => 1), true);
			$this->log_insert("action_achievement_updated", $log_action, $id, $arrOldData['name'], 1, 'awards');
			
			$this->pdh->enqueue_hook('awards_achievements_update');
			return $id;
		}
		
		return false;
	}


	/**
	 * Delete Achievements
	 */
	public function delete($id){
		$arrOldData = $this->pdh->get('awards_achievements', 'data', array($id));
		
		if($this->db->prepare("DELETE FROM __awards_achievements WHERE id = ?;")->execute($id)){
			
			$arrOldData['name']		   = $this->parse4log($arrOldData['name']);
			$arrOldData['description'] = $this->parse4log($arrOldData['description']);
			$arrOldData['icon_colors'] = $this->parse4log($arrOldData['icon_colors']);
			$arrOldData['module_set']  = $this->parse4log($arrOldData['module_set']);
			$arrOldData['event_id']	   = $this->pdh->get('event', 'name', array($arrOldData['event_id']));
			
			$log_action = $this->logs->diff(false, $arrOldData, $this->arrLogLang);
			$this->log_insert('action_achievement_deleted', $log_action, $id, $arrOldData['name'], 1, 'awards');
			
			$this->pdh->enqueue_hook('awards_achievements_update', $id);
			return true;
		}
		return false;
	}


	/**
	 * Set in/active an Achievement
	 */
	public function set_active($id, $blnActive){
		$objQuery = $this->db->prepare("UPDATE __awards_achievements :p WHERE id=?")->set(array(
			'active' => $blnActive,
		))->execute($id);
		
		if($objQuery){
			$this->pdh->enqueue_hook('awards_achievements_update', $id);
			return true;
		}
		return false;
	}


	/**
	 * Set no/special an Achievement
	 */
	public function set_special($id, $blnSpecial){
		$objQuery = $this->db->prepare("UPDATE __awards_achievements :p WHERE id=?")->set(array(
			'special' => $blnSpecial,
		))->execute($id);
		
		if($objQuery){
			$this->pdh->enqueue_hook('awards_achievements_update', $id);
			return true;
		}
		return false;
	}


	/**
	 * Set sort_id
	 */
	public function set_sort_id($id, $intSortID){
		$objQuery = $this->db->prepare("UPDATE __awards_achievements :p WHERE id=?")->set(array(
			'sort_id' => $intSortID,
		))->execute($id);
		
		if($objQuery){
			$this->pdh->enqueue_hook('awards_achievements_update', $id);
			return true;
		}
		return false;
	}


	/* Parse serialized arrays for the log insertion */
	private function parse4log($var){
		$var = unserialize($var);
		if(!is_array($var)) return $var;
		
		$retu = '';
		foreach($var as $key => $value){
			$retu .= $key.' => '.$value.'<br />';
		}
		
		return $retu;
	}



  } //end class
} //end if class not exists
?>