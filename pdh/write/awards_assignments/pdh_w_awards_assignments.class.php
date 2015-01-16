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
	  * Delete all selected Assignments
	  */
	public function delete($id) {
		$arrAssignments = $this->pdh->get('awards_assignments', 'id_list_for_category', array($id));
		if (isset($arrMedia[0]) && count($arrAssignments)){
			foreach($arrAssignments[0] as $intAssignmentID){
				$this->pdh->put('awards_assignments', 'delete', array($intAssignmentID));
			}
		}
		
		$this->delete_recursiv(intval($id));
		
		$this->pdh->enqueue_hook('articles_update');
		$this->pdh->enqueue_hook('awards_assignments_update');
		return true;
	}
		
	private function delete_recursiv($intAssignmentID){
		$arrOldData = $this->pdh->get('awards_assignments', 'data', array($intAssignmentID));
		$this->db->prepare("DELETE FROM __awards_assignments WHERE id =?")->execute($intAssignmentID);
		
		$log_action = $this->logs->diff(false, $arrOldData, $this->arrLogLang);
		$this->log_insert("action_assignment_deleted", $log_action, $intAssignmentID, $arrOldData["name"],  1, 'awards');
		
		return true;
	}
	
	
	/**
	  * Add a Assignment
	  */
	public function add($intAssDate=false, $intAchID, $arrAdjID, $strAdjGK){
		$intAssDate = ($intAssDate) ? $intAssDate : $this->time->time;
		$arrQuery  = array(
			'date' 				=> $intAssDate,
			'achievement_id'	=> $intAchID,
			'adj_id'			=> $arrAdjID,
			'adj_group_key'		=> $strAdjGK,
		);
		
		$objQuery = $this->db->prepare("INSERT INTO __awards_assignments :p")->set($arrQuery)->execute();
		
		if ($objQuery){
			$id = $objQuery->insertId;
			#$log_action = $this->logs->diff(false, $arrQuery, $this->arrLogLang);
			#$this->log_insert("action_assignment_added", $log_action, $id, $arrQuery["name"], 1, 'awards');
			
			$this->pdh->enqueue_hook('awards_assignments_update');
			return $id;
		}
		return false;
	}
	
	
	/**
	  * Update a Assignment
	  */
	public function update($id, $intAssDate=false, $intAchID, $arrAdjID, $strAdjGK){
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
			
			#$log_action = $this->logs->diff($arrOldData, $arrQuery, $this->arrLogLang, array('description' => 1), true);
			#$this->log_insert("action_assignment_updated", $log_action, $id, $arrOldData["name"], 1, 'awards');
			
			return $id;
		}
		
		return false;
	}
	
	
	/**
	  * Update a Assignment
	  */
	public function backup_old_adjustments($intAssID){
		
		$arrOldAdjIDs		= unserialize($this->pdh->get('awards_assignments', 'date', array($intAssID)));
		$intOldAdjGK		= $this->pdh->get('awards_assignments', 'adj_group_key', array($intAssID));
		$intOldAssDate		= $this->pdh->get('awards_assignments', 'date', array($intAssID));
		
		// müssen zurück und so als Funktions Parameter übergeben werden
		$intOldAdjEventID	= $this->pdh->get('adjustment', 'event', array($arrOldAdjIDs[0]));
		$fltOldAdjDKP		= $this->pdh->get('adjustment', 'value', array($arrOldAdjIDs[0]));
		$arrOldAdjUserIDs	= unserialize($this->pdh->get('adjustment', 'member', array($arrOldAdjIDs[0])));
		
		foreach($arrOldAdjIDs as $intOldAdjID){
			$objQuery = $this->db->prepare("INSERT INTO __awards_assignments :p")->set(array(
				'adjustment_id'				=> $intOldAdjID,
				'adjustment_value'			=> $fltOldAdjDKP,
				'adjustment_date'			=> $intOldAssDate,
				'member_id'					=> $arrOldAdjUserIDs[$intOldAdjID],
				'event_id'					=> $intOldAdjEventID,
				'adjustment_reason'			=> $reason,
				'raid_id'					=> 0,
				'adjustment_group_key'		=> $intOldAdjGK,
				'adjustment_added_by'		=> $this->admin_user
			))->execute();
			
			$objQuery = $this->db->prepare("INSERT INTO __awards_assignments :p")->set($arrQuery)->execute();
		}
	}
	
	/*
	$arrOldAdjUserIDs = unserialize($this->pdh->get('adjustment', 'member', array($arrOldAdjIDs[0])));
	$intOldAdjEventID = $this->pdh->get('adjustment', 'event', array($arrOldAdjIDs[0]));
	$intOldAssDate	  = $this->pdh->get('awards_assignments', 'date', array($intAssID));
	$fltOldAdjDKP	  = $this->pdh->get('adjustment', 'value', array($arrOldAdjIDs[0]));
	foreach($arrOldAdjIDs as $readID)
		$strOldAdjName[] = $this->pdh->get('adjustment', 'reason', array($arrOldAdjIDs[0]));
	*/


	
  } //end class
} //end if class not exists
?>