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

if (!defined('EQDKP_INC')){
  header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
  | awards_portal_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('awards_portal_hook')){
  class awards_portal_hook extends gen_class
  {

	/**
      * Portal
      * portal hook
      */
	public function portal(){
		if($this->user->check_auth("u_awards_view", false)){
			$this->tpl->css_file($this->root_path.'plugins/awards/templates/base_template/awards.css');
			
			if($this->user->is_signedin()){
				$intUserID = $this->user->id;
				$arrUserSettings = $this->pdh->get('user', 'plugin_settings', array($intUserID));
				if(isset($arrUserSettings['aw_show_hook']) && $arrUserSettings['aw_show_hook']){
					$arrMemberIDs = $this->pdh->get('member', 'connection_id', array($intUserID));
					$arrAchIDs = $arrAssDates = array();
					$intAP = $floatDKP = 0;
					
					//fetch all AchIDs which we got
					foreach($arrMemberIDs as $intMemberID){
						$arrAssIDs = $this->pdh->get('awards_library', 'ids_of_member', array($intMemberID));
						foreach($arrAssIDs as $intAssID){
							$arrAchIDs[$intAssID] = $intAchID = $this->pdh->get('awards_assignments', 'achievement_id', array($intAssID));
							$arrAssDates[$intAssID] = $this->pdh->get('awards_assignments', 'date', array($intAssID));
							$intAP	  += $this->pdh->get('awards_achievements', 'points', array($intAchID));
							$floatDKP += $this->pdh->get('awards_achievements', 'dkp', array($intAchID));
						}
					}
					
					//build output
					$output = '
						<div class="awards-tooltip-container hiddenSmartphone">
							<a class="awards-tooltip-trigger hand tooltip-trigger" data-tooltip="awards-tooltip">
								<i class="fa fa-mortar-board fa-lg"></i> <span>'.$this->user->lang('awards').'</span>
							</a>
							<ul id="awards-tooltip" class="dropdown-menu" role="menu">
								<li class="aw-tt-action-bar"> 
									<div class="floatLeft">
										<i class="fa fa-bookmark-o" title="'.$this->user->lang('aw_tt_reached_ap').'"></i>'.$intAP.'&nbsp;
										<i class="fa fa-trophy fa-lg" title="'.$this->user->lang('aw_tt_reached_dkp').'"></i>'.$floatDKP.'
									</div>
									<div class="floatRight">
										<span class="hand" onclick="window.location=\''.$this->routing->build('User', $this->pdh->get('user', 'name', array($intUserID)), 'u'.$intUserID).'#2384ece2c'.'\'">'.$this->user->lang('aw_tt_my_awards').'</span> • 
										<span class="hand" onclick="window.location=\''.$this->controller_path.'Settings/'.$this->SID.'\'"><i class="fa fa-cog fa-lg"></i></span>
									</div>
									<div class="clear"></div>
								</li>
								<li class="tooltip-divider"></li>
								<li class="award-tt-list">
									<ul>
					';
					
					//build the dynamic part (awards-list)
					arsort($arrAssDates); $foo = 0;
					foreach($arrAssDates as $intAssID => $intAssDate){
						if($foo >= 5) break;
						$award = $this->pdh->get('awards_achievements', 'data', array($arrAchIDs[$intAssID]) );
						$award['member_id'] = $this->pdh->get('awards_library', 'member_id', array($intAssID));
						$award['date'] = $arrAssDates[$intAssID];
						
						$output .= '
							<li class="aw-tt-'.$award['id'].'">
								<span class="aw-tt-title">'.$this->user->multilangValue($award['name']).'</span>
								<span class="aw-tt-date">'.$this->time->user_date($award['date']).'</span>
								<span class="aw-tt-member">'.$this->pdh->get('member', 'html_name', array($award['member_id'])).'</span>
							</li>
						';
						
						$foo++;
					}
					
					
					$output .= '
									</ul>
								</li>
								<li class="tooltip-divider"></li>
								<li class="aw-tt-action-bar-btm"> <span class="hand" onclick="window.location=\''.$this->controller_path.'Awards/'.$this->SID.'\'">'.$this->user->lang('aw_tt_all_awards').'</span></li>
							</ul>
						</div>
					';
					$this->tpl->assign_block_vars('personal_area_addition', array(
						'TEXT' => $output,
					));
				}
			}
		}
	}
  }
} 
?>