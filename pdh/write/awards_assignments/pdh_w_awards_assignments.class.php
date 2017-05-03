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
  | pdh_w_awards_assignments
  +--------------------------------------------------------------------------*/
if(!class_exists('pdh_w_awards_assignments')) {
  class pdh_w_awards_assignments extends pdh_w_generic
  {
	private $arrLogLang = array(
			'id'				=> '{L_ID}',
			'date'				=> '{L_DATE}',
			'achievement_id'	=> '{L_AW_ACHIEVEMENT}',
			'adj_id'			=> '{L_AW_ADJ_ID}',
			'adj_group_key'		=> '{L_AW_ADJ_GK}',
	);


	/**
	  * Add Assignments
	  */
	public function add($intAchID, $arrAdjIDs, $strAdjGK, $intDate=false){
		if(!empty($arrAdjIDs) && !is_array($arrAdjIDs)) $arrAdjIDs = array($arrAdjIDs);
		$intDate = ($intDate) ? $intDate : $this->time->time;
		$strMembers = '';
		$ids = array();
		
		foreach($arrAdjIDs as $intAdjID){
			$arrQuery  = array(
				'date' 				=> $intDate,
				'achievement_id'	=> $intAchID,
				'adj_id'			=> $intAdjID,
				'adj_group_key'		=> $strAdjGK,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __awards_assignments :p")->set($arrQuery)->execute();
			
			if(!$objQuery){ return false; }
			$ids[] = $objQuery->insertId;
			$strMembers .= $this->pdh->get('adjustment', 'member_name', array($intAdjID)).', ';
		}
		
		$strAchName	= $this->parse4log($this->pdh->get('awards_achievements', 'name', array($intAchID)));
		$log_action = array(
			'{L_AW_ACHIEVEMENT}' => $strAchName,
			'{L_DATE}'			 => $this->time->date('Y-m-d H:i', $intDate),
			'{L_MEMBER}'		 => $strMembers,
		);
		
		$this->log_insert('action_assignment_added', $log_action, $intAchID, $strAchName, 1, 'awards');
		
		$this->pdh->enqueue_hook('awards_assignments_update', $ids);
		$this->pdh->enqueue_hook('awards_library_update');
		return $ids;
	}


	/**
	  * Update Assignments by $arrAdjID
	  */
	public function update($arrAdjIDs, $intAchID, $strAdjGK, $intDate=false){
		if(!is_array($arrAdjIDs)) $arrAdjIDs = array($arrAdjIDs);
		$intDate = ($intDate) ? $intDate : $this->time->time;
		$arrIDs		= array();
		$arrQuery	= array(
			'date' 				=> $intDate,
			'achievement_id'	=> $intAchID,
			'adj_id'			=> 0,
			'adj_group_key'		=> $strAdjGK,
		);
		
		foreach($arrAdjIDs as $key => $intAdjID){
			if($key == 0 || $intAdjID == 0) continue;
			$arrQuery['adj_id'] = $intAdjID;
			
			//check if we have to add a new assignment
			$intAssID = $this->pdh->get('awards_assignments', 'id_of_aid', array($intAdjID));
			if($intAssID){
				$adj_exist = $this->pdh->get('adjustment', 'reason', array($intAdjID));
				if(!$adj_exist){
					$objQuery = $this->db->prepare("DELETE FROM __awards_assignments WHERE id = ?;")->execute($intAssID);
					$handle2return = false;
				}else{
					unset($adj_exist);
					$objQuery = $this->db->prepare("UPDATE __awards_assignments :p WHERE id=?")->set($arrQuery)->execute($intAssID);
					$handle2return = true;
				}
			}else{
				$objQuery = $this->db->prepare("INSERT INTO __awards_assignments :p")->set($arrQuery)->execute();
				$handle2return = true;
			}
			
			if($objQuery){
				if($handle2return) $arrIDs[] = ($intAssID)? $intAssID : $objQuery->insertId;
				
				$strAchName	= $this->parse4log($this->pdh->get('awards_achievements', 'name', array($intAchID)));
				$log_action = array(
					'{L_AW_ACHIEVEMENT}' => $strAchName,
					'{L_DATE}'			 => $this->time->date('Y-m-d H:i', $intDate),
					#'{L_MEMBER}'		 => $strMembers,
				);
				
				$this->log_insert('action_assignment_updated', $log_action, $intAchID, $strAchName, 1, 'awards');
			}else{ return false; }
		}
		
		if($arrIDs){
			$this->pdh->enqueue_hook('awards_assignments_update', $arrIDs);
			$this->pdh->enqueue_hook('awards_library_update');
			return $arrIDs;
		}
		return false;
	}


	/*public function update($id, $intAssDate=false, $intAchID, $arrAdjID, $strAdjGK){
		$intAssDate = ($intAssDate) ? $intAssDate : $this->time->time;
		$arrQuery = array(
			'date' 				=> $intAssDate,
			'achievement_id'	=> $intAchID,
			'adj_id'			=> $arrAdjID,
			'adj_group_key'		=> $strAdjGK,
		);
		
		$arrOldData = $this->pdh->get('awards_assignments', 'data', array($id));
		
		$objQuery = $this->db->prepare("UPDATE __awards_assignments :p WHERE id=?")->set($arrQuery)->execute($id);
		
		if ($objQuery){
			$this->pdh->enqueue_hook('awards_assignments_update');
			
			$log_action = $this->logs->diff($arrOldData, $arrQuery, $this->arrLogLang, array('description' => 1), true);
			$this->log_insert("action_assignment_updated", $log_action, $id, $arrOldData["achievement_id"], 1, 'awards');
			
			return $id;
		}
		
		return false;
	}*/


	/**
	 * Delete Assignments
	 */
	public function delete($id){
		if($this->db->prepare("DELETE FROM __awards_assignments WHERE id = ?;")->execute($id)){
			
			$strAchName = $this->pdh->get('awards_achievements', 'name', array($id));
			
			$log_action = array(
				'{L_AW_ACHIEVEMENT}' => $strAchName,
				'{L_DATE}'			 => $this->time->date('Y-m-d H:i', $this->pdh->get('awards_assignments', 'date', array($id))),
				'{L_MEMBER}'		 => $this->pdh->get('adjustment', 'member_name', array($id)),
			);
			
			$this->log_insert('action_assignment_deleted', $log_action, $id, $strAchName, 1, 'awards');
			
			$this->pdh->enqueue_hook('awards_assignments_update', $id);
			$this->pdh->enqueue_hook('awards_library_update');
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