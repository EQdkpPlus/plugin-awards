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
	header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
  | awards_cronmodule
  +--------------------------------------------------------------------------*/
if (!class_exists("cron_module_1_cronmodule")) {
	class cron_module_1_cronmodule extends gen_class {
		public function __construct(){  }


// needed raids for an assignment
public $requiredRaids = 25;

		public function run($intAchID){
			$member_ids = $this->pdh->get('member', 'id_list');
			foreach($member_ids as $intMemberID){
				$arrRaidsOfMember = $this->pdh->get('raid', 'raidids4memberid', array($intMemberID));
				
				if(count($arrRaidsOfMember) >= $this->requiredRaids)
					$arrMemberIDs[] = $intMemberID;
			}
			
			if($arrMemberIDs) return $arrMemberIDs;
			return false;
		}	
	}
}
?>