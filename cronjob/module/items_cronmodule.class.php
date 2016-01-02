<?php
/*	Project:	EQdkp-Plus
 *	Package:	Awards Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
	header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
  | awards_cronmodule
  +--------------------------------------------------------------------------*/
if (!class_exists("items_cronmodule")) {
	class items_cronmodule extends gen_class {
		public function __construct(){}


		public $requiredItems = 25;	


		public function run($intAchID, $arrMemberIDs){
			if($arrMemberIDs){
				$this->get_data($intAchID);
				
				$returnMemberIDs = array();
				foreach($arrMemberIDs as $intMemberID){
					$arrItems = $this->pdh->get('item', 'itemids4memberid', array($intMemberID));
					
					if(count($arrItems) >= $this->requiredItems)
						$returnMemberIDs[] = $intMemberID;
				}
				
				if($returnMemberIDs) return $returnMemberIDs;
				return false;
			}
			return false;
		}

		//fetch module settings of award
		public function get_data($intAchID){
			$strModuleData = unserialize( $this->pdh->get('awards_achievements', 'module_set', array($intAchID)) );
			$this->requiredItems = (isset($strModuleData['items']))? $strModuleData['items'] : 25;
		}
	}
}
?>