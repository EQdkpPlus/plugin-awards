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
			$arrAchIDs = $this->pdh->get('awards_achievements', 'id_list');
			
			foreach($arrAchIDs as $intAchID){
				if( $this->pdh->get('awards_achievements', 'active', array($intAchID)) ){
					$strAchModule = $this->pdh->get('awards_achievements', 'module', array($intAchID));
					
					if(!empty($strAchModule)){
						require($this->root_path.'plugins/awards/cronjob/module/'.$strAchModule.'_cronmodule.class.php');
						$module = registry::register($strAchModule.'_cronmodule');
						
						$arrMemberIDs = $module->run($intAchID);
						if($arrMemberIDs){
							$this->addAward($intAchID, $arrMemberIDs);
						}
					}
				}
			}
		}


		public function addAward($intAchID, $arrMemberIDs){
			// check if member has already this award
			$arrAdjIDs = $this->pdh->get('adjustment','adjsofeventid', array( $this->pdh->get('awards_achievements', 'event_id', array($intAchID)) ));
			$arrAdjMemberIDs = array();
			foreach($arrAdjIDs as $intAdjID)
				$arrAdjMemberIDs[] = $this->pdh->get('adjustment', 'member', array($intAdjID));
			
			$arrMemberIDs = array_diff($arrMemberIDs, $arrAdjMemberIDs);
			
			// fetch Achievement Data
			$intDate		= $this->time->fromformat($this->in->get('date', '1.1.1970'), 1);
			$fltAchDKP		= $this->pdh->get('awards_achievements', 'dkp', array($intAchID));
			$arrAchName		= unserialize( $this->pdh->get('awards_achievements', 'name', array($intAchID)) );
			$strAchName		= $this->user->lang('aw_achievement').': '.$arrAchName[$this->config->get('default_lang')];
			$intAchEventID	= $this->pdh->get('awards_achievements', 'event_id', array($intAchID));
			
			// add Award to Member
			$arrAdjIDs = $this->pdh->put('adjustment', 'add_adjustment', array($fltAchDKP, $strAchName, $arrMemberIDs, $intAchEventID, 0, $intDate));
			if($arrAdjIDs){
				$this->pdh->process_hook_queue();
				$strAdjGK = $this->pdh->get('adjustment', 'group_key', array($arrAdjIDs['0']));
				
				foreach($arrAdjIDs as $intAdjID)
					$this->pdh->put('awards_assignments', 'add', array($intDate, $intAchID, $intAdjID, $strAdjGK));
				
				$this->pdh->process_hook_queue();
			}
			
			// add Notifications
			$arrUserIDs = array();
			foreach($arrMemberIDs as $intMemberID)
				$arrUserIDs[] = $this->pdh->get('member', 'user', array($intAdjUserID));
			
			$this->ntfy->add('awards_new_award', $intAchID, "Plugin: ".$this->user->lang('awards'), $this->routing->build('Awards', false, false, true, true), array_unique($arrUserIDs));	
			
		}
	}
}
?>