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

/*+----------------------------------------------------------------------------
  | awards_pageobject
  +--------------------------------------------------------------------------*/
class awards_pageobject extends pageobject
{
	/**
	  * Constructor
	  */
	public function __construct(){
		// plugin installed?
		if (!$this->pm->check('awards', PLUGIN_INSTALLED))
		  message_die($this->user->lang('aw_plugin_not_installed'));

		$this->user->check_auth('u_awards_view');

		$handler = array(
			'page' => array('process' => 'display'),
		);
		parent::__construct(false, $handler);
		$this->process();
	}


	/**
	  * Display
	  * display all achievements
	  */
	public function display(){
		$arrAchIDs		= $this->pdh->get('awards_achievements', 'id_list');
		$intViewerID	= $this->user->id;
		
		$intAP		= $awReachedCounter = $blnUserReached = 0;
		$list_order = $allAwards = array();
		$strLayout	= 'default';	# USE OTHER DEFAULT-LAYOUT BY REPLACE default TO minimalist
		$awReached	= 'reached';
		
		$arrUserSettings = $this->pdh->get('user', 'plugin_settings', array($intViewerID));
		if(isset($arrUserSettings['aw_layout']) && is_string($arrUserSettings['aw_layout'])) $strLayout = $arrUserSettings['aw_layout'];
		
		//sorting -- award by latest date -- rebuild array to read in multiple loops
		foreach($arrAchIDs as $intAchID){
			$award = $this->awards->award($intAchID, true);
			if(isset($award['member_r'])){
				$list_order[$award['id']] = $award['date'];
				$intAP += $award['points']; $awReachedCounter++;
				
			}else{ $list_order[$award['id']] = false; }
		}
		arsort($list_order);
		foreach($list_order as $key => $value) $allAwards[] = $key;
		
		//split $allAwards for pagination
		$intPage = $this->in->get('page', 0);
		$arrUserSettings['aw_pagination'] = (isset($arrUserSettings['aw_pagination']))? $arrUserSettings['aw_pagination'] : 25;
		$allAwardsCount = count($allAwards);
		$allAwards = array_slice($allAwards, $intPage * $arrUserSettings['aw_pagination'], $arrUserSettings['aw_pagination']);
		
		
		//start the loops
		foreach($allAwards as $intAchID){
			$award		= $this->awards->award($intAchID, true);
			$strAchIcon = $this->awards->build_icon($intAchID, $award['icon'], unserialize($award['icon_colors']));
			
			if($awReached == 'reached' && !isset($award['member_r'])) $awReached = 'unreached';
			$blnUserReached = (isset($award['member_r'][$intViewerID]))? true : false;
			
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
				'USER_R'	=> $blnUserReached,
			));
			
			//build the members
			if(isset($award['member_r']))
				foreach($award['member_r'] as $intUserID => $arrMembers){
					$this->tpl->assign_block_vars('award.users', array(
						'ID'		=> $intUserID,
						'USER'		=> $this->pdh->geth('user', 'name', array($intUserID, '', '', true)),
						'REACHED'	=> 'reached',
					));
					foreach($arrMembers as $intMemberID => $intMemberDate){
						if($strLayout == 'minimalist' && !isset($intMemberDate)) continue;
						$this->tpl->assign_block_vars('award.users.members', array(
							'MEMBER'	=> $this->pdh->get('member', 'name_decorated', array($intMemberID, 15)),
							'DATE'		=> ($intMemberDate)? '- '.$this->time->user_date($intMemberDate) : $this->user->lang('aw_member_unreached'),
						));
					}
				}
			if(isset($award['member_u']) && $strLayout != 'minimalist')
				foreach($award['member_u'] as $intUserID => $arrMembers){
					$this->tpl->assign_block_vars('award.users', array(
						'ID'		=> $intUserID,
						'USER'		=> $this->pdh->geth('user', 'name', array($intUserID, '', '', true)),
						'REACHED'	=> 'unreached',
					));
					foreach($arrMembers as $intMemberID => $intMemberDate){
						$this->tpl->assign_block_vars('award.users.members', array(
							'MEMBER'	=> $this->pdh->get('member', 'name_decorated', array($intMemberID, 15)),
							'DATE'		=> $this->user->lang('aw_member_unreached'),
						));
					}
				}
		}
		
		$this->tpl->assign_vars(array(
			'AP'				=> $intAP,
			'LAYOUT'			=> $strLayout,
			'USER_PROFILE_LINK' => $this->routing->build('User', $this->pdh->get('user', 'name', array($intViewerID)), 'u'.$intViewerID).'#3e55cad42',
			'S_AW_MANAGE'		=> $this->user->check_auth('a_awards_manage', false),
			'S_AW_ADD'			=> $this->user->check_auth('a_awards_add', false),
			'PAGINATION'		=> generate_pagination($this->strPath.$this->SID, $allAwardsCount, $arrUserSettings['aw_pagination'], $intPage, 'page'),
		));
		$this->tpl->add_js('
			$("#aw_progress").progressbar({
				value: '.$awReachedCounter.',
				max: '.count($arrAchIDs).',
			});
			$(".progress-label").text("'.$awReachedCounter.' / '.count($arrAchIDs).'");
		', 'docready');
		
		
		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars([
			'page_title'    => $this->user->lang('awards'),
			'template_path' => $this->pm->get_data('awards', 'template_path'),
			'template_file' => 'awards.html',
			'page_path'		=> [
				['title'=>$this->user->lang('awards').': '.$this->user->lang('aw_all_guild_achievements'), 'url'=>' '],
			],
			'display'       => true,
		]);

	}
}
?>