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
  | awards_crontask
  +--------------------------------------------------------------------------*/
if ( !class_exists( "awards_crontask" ) ) {
	class awards_crontask extends crontask {
		public function __construct(){  }


		public function run(){
			$arrAchIDs	  = $this->pdh->get('awards_achievements', 'id_list');
			
			foreach($arrAchIDs as $intAchID){
				if( $this->pdh->get('awards_achievements', 'active', array($intAchID)) ){
					$arrMemberIDs = $this->pdh->get('member', 'id_list');
					$arrAchModule = unserialize($this->pdh->get('awards_achievements', 'module', array($intAchID)));
					$arrAchModuleConditions = $arrAchModule['conditions'];
					$arrAchModule = array_slice($arrAchModule, 1);
					
					if($arrAchModuleConditions == 'all'){
						foreach($arrAchModule as $strAchModule){
							if(!class_exists($strAchModule.'_cronmodule'))
								if((include $this->root_path.'plugins/awards/cronjob/module/'.$strAchModule.'_cronmodule.class.php') == false) continue;
							
							$module = registry::register($strAchModule.'_cronmodule');
							$arrMemberIDs = $module->run($intAchID, $arrMemberIDs);
							if($arrMemberIDs) $arrMemberIDs = array_unique($arrMemberIDs);
						}
						$this->awards->add_assignment($intAchID, $arrMemberIDs);
						
						
					}elseif($arrAchModuleConditions == 'any'){
						foreach($arrAchModule as $strAchModule){
							if(!class_exists($strAchModule.'_cronmodule'))
								if((include $this->root_path.'plugins/awards/cronjob/module/'.$strAchModule.'_cronmodule.class.php') == false) continue;
							
							$module = registry::register($strAchModule.'_cronmodule');
							$arrMemberIDs = $module->run($intAchID, $arrMemberIDs);
							
							if($arrMemberIDs) $this->awards->add_assignment($intAchID, $arrMemberIDs);
						}
						
						
					}else{ continue; }
				}
			}
		}



	}
}
?>