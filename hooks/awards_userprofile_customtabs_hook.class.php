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
  | awards_userprofile_customtabs_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('awards_userprofile_customtabs_hook')){
  class awards_userprofile_customtabs_hook extends gen_class
  {

	/**
	  * userprofile_customtabs
	  * display the achievement tab
	  */
	public function userprofile_customtabs($intUserID){
		if($this->user->check_auths(array('u_awards_view', 'a_awards_manage'), 'OR', false)){
			$arrAchIDs		= $this->pdh->get('awards_achievements', 'id_list');
			$intUserID		= $intUserID['user_id'];
			$intViewerID	= $this->user->id;
			$allAwards		= array();
			$intAP			= $awReachedCounter = 0;
			$awReached		= 'reached';
			$content		= '';
			
			
			//sorting -- newest date = up, false = unreached
			foreach($arrAchIDs as $intAchID){
				$award = $this->awards->award($intAchID, $intUserID);
				if(isset($award['member_r'][$intUserID])){
					$allAwards[$award['id']] = $award['date'];
					$intAP += $award['points']; $awReachedCounter++;
					
				}else{ $allAwards[$award['id']] = false; }
			}
			arsort($allAwards);
			
			//split $allAwards for pagination
			$intPage = $this->in->get('page', 0);
			$arrUserSettings = $this->pdh->get('user', 'plugin_settings', array($intViewerID));
			$arrUserSettings['aw_pagination'] = (isset($arrUserSettings['aw_pagination']))? $arrUserSettings['aw_pagination'] : 25;
			$allAwardsCount = count($allAwards);
			$allAwards = array_slice($allAwards, $intPage * $arrUserSettings['aw_pagination'], $arrUserSettings['aw_pagination'], true);
			
			
			foreach($allAwards as $intAchID => $status){
				$award = $this->awards->award($intAchID, $intUserID);
				
				$strAchIcon = $this->awards->build_icon($intAchID, $award['icon'], unserialize($award['icon_colors']));
				
				if(!isset($award['member_r'][$intViewerID])) $awReached = 'unreached';
				
				$this->tpl->assign_block_vars('award', array(
					'ID'		=> $intAchID,
					'TITLE'		=> $this->user->multilangValue($award['name']),
					'DESC'		=> $this->user->multilangValue($award['desc']),
					'DATE'		=> ($award['date'])? $this->time->user_date($award['date']) : '',
					'ICON'		=> $strAchIcon,
					'ACTIVE'	=> $award['active'],
					'SPECIAL'	=> $award['special'],
					'AP'		=> $award['points'],
					'DKP'		=> $award['dkp'],
					'REACHED'	=> $awReached,
					'USER_R'	=> (isset($award['member_r'][$intViewerID]))? true : false,
				));
				
				//build the members
				if(isset($award['member_r'][$intUserID]))
					foreach($award['member_r'][$intUserID] as $intMemberID => $intMemberDate){
						$this->tpl->assign_block_vars('award.members', array(
							'MEMBER'	=> $this->pdh->get('member', 'name_decorated', array($intMemberID, 15)),
							'DATE'		=> ($intMemberDate)? '- '.$this->time->user_date($intMemberDate) : $this->user->lang('aw_member_unreached'),
						));
					}
				if(isset($award['member_u'][$intUserID]))
					foreach($award['member_u'][$intUserID] as $intMemberID => $intMemberDate){
						$this->tpl->assign_block_vars('award.members', array(
							'MEMBER'	=> $this->pdh->get('member', 'name_decorated', array($intMemberID, 15)),
							'DATE'		=> $this->user->lang('aw_member_unreached'),
						));
					}
				
				
			}
			
			
			
			$this->tpl->add_js('
				$("#my_aw_progress").progressbar({
					value: '.$awReachedCounter.',
					max: '.$allAwardsCount.',
				});
				$(".progress-label").text("'.$awReachedCounter.' / '.$allAwardsCount.'");
				$("#achievement-points").text("'.$intAP.'");
			', 'docready');
			
			$template_file = file_get_contents($this->root_path.$this->pm->get_data('awards', 'template_path').'base_template/user_awards.html');
			$content = $this->tpl->compileString($template_file, array(
				'AP'			=> $intAP,
				'PAGINATION'	=> generate_pagination($this->strPath.$this->SID, $allAwardsCount, $arrUserSettings['aw_pagination'], $intPage, 'page'),
			));
			
		}else{ $content = $this->user->lang('aw_no_permission'); }
		
		//-----------------------------------------------------------------------------------
			$output = array(
				'title'   => $this->user->lang('aw_customtab_title'),
				'content' => $content,
			);
			return $output;
	}


  } //end class
} //end if class not exists
?>