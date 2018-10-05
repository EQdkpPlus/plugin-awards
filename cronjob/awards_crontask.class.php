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
	header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
  | awards_crontask
  +--------------------------------------------------------------------------*/
if ( !class_exists( "awards_crontask" ) ) {
	class awards_crontask extends crontask {
		public function __construct(){
			register('pm');
		}


		public function run(){
			$arrAchIDs	  = $this->pdh->get('awards_achievements', 'id_list');
			
			foreach($arrAchIDs as $intAchID){
				if( $this->pdh->get('awards_achievements', 'active', array($intAchID)) ){
					$arrMemberIDs			= $this->pdh->get('member', 'id_list');
					$arrAchModules			= unserialize($this->pdh->get('awards_achievements', 'module', array($intAchID)));
					$arrAchModuleSettings	= unserialize($this->pdh->get('awards_achievements', 'module_set', array($intAchID)));
					$arrAchModuleConditions	= $arrAchModules['conditions'];
					$arrAchModules			= array_slice($arrAchModules, 1);
					
					if($arrAchModuleConditions == 'all'){
						foreach($arrAchModules as $strAchModule){
							include_once($this->root_path.'plugins/awards/cronjob/modules/'.$strAchModule.'_cronmodule.class.php');
							$strModuleClass	= $strAchModule.'_cronmodule';
							$objModule		= new $strModuleClass($arrAchModuleSettings[$strAchModule]);
							$arrMemberIDs	= $objModule->cron_process($intAchID, $arrMemberIDs);
							
							if(!empty($arrMemberIDs) && is_array($arrMemberIDs))
								$arrMemberIDs = array_unique($arrMemberIDs);
							else break;
						}
						if(!empty($arrMemberIDs) && is_array($arrMemberIDs)) $this->awards->add_assignment($intAchID, $arrMemberIDs);
						
						
					}elseif($arrAchModuleConditions == 'any'){
						foreach($arrAchModules as $strAchModule){
							include_once($this->root_path.'plugins/awards/cronjob/modules/'.$strAchModule.'_cronmodule.class.php');
							$strModuleClass	= $strAchModule.'_cronmodule';
							$objModule		= new $strModuleClass($arrAchModuleSettings[$strAchModule]);
							$arrMemberIDs	= $objModule->cron_process($intAchID, $arrMemberIDs);
							
							if(!empty($arrMemberIDs) && is_array($arrMemberIDs)) $this->awards->add_assignment($intAchID, $arrMemberIDs);
						}
						
						
					}else{ continue; }
				}
			}
		}



	}
}
?>
