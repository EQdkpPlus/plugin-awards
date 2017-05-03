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

if( !defined( 'EQDKP_INC' ) ) {
	die('Do not access this file directly.');
}
/*+----------------------------------------------------------------------------
  | awards_plugin
  +--------------------------------------------------------------------------*/
if(!class_exists('awards_plugin')){
	class awards_plugin extends gen_class {
		
		public function __construct(){ }


		/**
		 * Get all datas of an Award by ID
		 *
		 * @param integer $intAchID - the Award ID
		 * @param integer $show_member - the User ID to show only members of User
		 * @return multitype: Array with all data. Otherwise false if award not found.
		 */
		public function award($intAchID, $show_member=false){
			if(!(int)$intAchID) return false;
			if(!$arrAch = $this->pdh->get('awards_achievements', 'data', array($intAchID))) return false;
			$intAchDate = $this->pdh->get('awards_library', 'earliest_date_of_award', array($intAchID));
			$award = array();
			
			if($show_member){
				if($show_member > 0 && $show_member !== true){ $all_member = $this->pdh->get('member', 'connection_id', array($show_member)); }
				else{ $all_member = $this->pdh->get('member', 'id_list'); }
			
				$member_of_award = $this->pdh->get('awards_library', 'member_of_award', array($intAchID));
				
				foreach($all_member as $member){
					$intUserID = $this->pdh->get('member', 'userid', array($member));
					if( in_array($member, $member_of_award) || isset($award['member_r'][$intUserID]) ){
						$award['member_r'][$intUserID][$member] = $this->pdh->get('awards_library', 'member_date_by_award', array($intAchID, $member));
					}else{
						$award['member_u'][$intUserID][$member] = NULL;
					}
				}
			}
			
			//------------------------------------------
			$award = array_merge(array(
				'id'		 => $intAchID,
				'name'		 => $arrAch['name'],
				'desc'		 => $arrAch['description'],
				'date'		 => (is_int($intAchDate))? $intAchDate : NULL,
				'icon'		 => $arrAch['icon'],
				'icon_colors'=> $arrAch['icon_colors'],
				'active'	 => $arrAch['active'],
				'special'	 => $arrAch['special'],
				'points'	 => $arrAch['points'],
				'dkp'		 => $arrAch['dkp'],
			), $award);
			
			return $award;
		}


		/**
		 * Build the Award Icon with CSS
		 *
		 * @param integer $intAchID - the Award ID
		 * @param string $strAchIcon - the IconName
		 * @param array $arrAchIconColors - the IconColors
		 * @return string: SVG Code _or_ IMG HTML Code.
		 */
		public function build_icon($intAchID, $strAchIcon, $arrAchIconColors){
			$icon_folder = $this->pfh->FolderPath('images', 'awards');
			if( file_exists($icon_folder.$strAchIcon) ){
				$strAchIcon = $this->pfh->FolderPath('images', 'awards', 'absolute').$strAchIcon;
			} else {
				$strAchIcon = $this->env->link.'plugins/awards/images/'.$strAchIcon;
			}
			
			if( pathinfo($strAchIcon, PATHINFO_EXTENSION) == "svg"){
				$strAchIcon		= file_get_contents($strAchIcon);
				$strAchIconCSS	= '';
				
				// build the CSS Code for each SVG
				$icon_color_step = 1;
				foreach($arrAchIconColors as $strAchIconColor){
					if(!empty($strAchIconColor)) $strAchIconCSS .= '.award[data-id="'.$intAchID.'"] .ac-icon svg g:nth-child('.$icon_color_step.'){fill: '.$strAchIconColor.';}';
					$icon_color_step++;
				}
				$this->tpl->add_css($strAchIconCSS);
			} else {
				$strAchIcon = '<img src="'.$strAchIcon.'" />';
			}
			
			return $strAchIcon;
		}


		/**
		 * Assign an Award
		 * 
		 * @param integer $intAchID - the Award ID
		 * @param array $arrMemberIDs - the Members
		 * @param integer $intDate - the Date
		 * @return array: $arrAssIDs OR false if anything failed
		 */
		public function add_assignment($intAchID, $arrMemberIDs, $intDate=false){
			$intDate		= ($intDate) ? $intDate : $this->time->time;
			$arrAch			= $this->pdh->get('awards_achievements', 'data', array($intAchID));
			$arrAch['name'] = unserialize($arrAch['name']);
			$arrAch['name'] = $this->user->lang('aw_achievement').': '.$arrAch['name'][$this->config->get('default_lang')];
			
			//check the conditions
			if(!$arrAch['active']) return false;
			
			foreach($arrMemberIDs as $key => $intMemberID)
				if( $this->pdh->get('awards_library', 'member_has_award', array($intAchID, $intMemberID)) )
					unset($arrMemberIDs[$key]);
			
			//add Award
			if($arrMemberIDs){
				$arrAdjIDs = $this->pdh->put('adjustment', 'add_adjustment', array($arrAch['dkp'], $arrAch['name'], $arrMemberIDs, $arrAch['event_id'], 0, $intDate));
				if($arrAdjIDs){
					$this->pdh->process_hook_queue();
					$strAdjGK	= $this->pdh->get('adjustment', 'group_key', array($arrAdjIDs['0']));
					$arrAssIDs	= $this->pdh->put('awards_assignments', 'add', array($intAchID, $arrAdjIDs, $strAdjGK, $intDate));
				}else{ return false; }
			}else{ return false; }
			
			// add Notifications
			if($arrAssIDs){
				$this->pdh->process_hook_queue();
				
				$arrUserIDs = array();
				foreach($arrMemberIDs as $intMemberID)
					$arrUserIDs[] = $this->pdh->get('member', 'user', array($intMemberID));
				
				$this->ntfy->add('awards_new_award', $intAchID, "Plugin: ".$this->user->lang('awards'), $this->routing->build('Awards', false, false, true, true), array_unique($arrUserIDs));
				
				return $arrAssIDs;
			}else{ return false; }
		}



	} //end class
} //end if class not exists
?>