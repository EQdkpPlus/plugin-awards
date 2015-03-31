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
if (!class_exists("birthday_cronmodule")) {
	class birthday_cronmodule extends gen_class {
		public function __construct(){}


		public function run($intAchID, $arrMemberIDs){
			if($arrMemberIDs){
				
				$returnMemberIDs = array();
				$arrUserIDs		 = $this->pdh->get('member', 'userid', array($arrMemberIDs));
				
				
				$arrUserIDs = array_unique($arrUserIDs);
				foreach($arrUserIDs as $intUserID){
					$intUserBirthday = $this->pdh->get('user', 'birthday', array($intUserID));
					
					if( $this->birthday_istoday($intUserBirthday) )
						$returnMemberIDs[] = $this->pdh->get('user', 'mainchar', array($intUserID));
				}
				
				if($returnMemberIDs) return $returnMemberIDs;
				return false;
			}
			return false;
		}

		private function birthday_istoday($intUserBirthday){
			$birthday	= $this->time->getdate($intUserBirthday);
			$today		= $this->time->getdate();
			
			if($birthday['mon'] == $today['mon'] && $today['mday'] == $birthday['mday']) return true;
			return false;
		}
	}
}
?>