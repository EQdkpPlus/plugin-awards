<?php
/*	Project:	EQdkp-Plus
 *	Package:	Awards  Plugin
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
			#'get_table'		=> array('process' => 'set_cookie', 'check' => 'u_awards_view'),
		);
		parent::__construct(false, $handler);
		$this->process();
	}

public $xAwardperRow = 1; // Gibt an wieviele Erfolge pro Reihe angezeigt werden sollen

	/**
	  * Display
	  * display all achievements
	  */
	public function display(){
		
		//fetch all members
		$arrAllMemberIDs = $this->pdh->get('member', 'id_list');
		
		
		//fetch all Assignments
		$arrLibAssIDs = $this->pdh->get('awards_library', 'id_list');
		
		foreach($arrLibAssIDs as $intLibAssID){
			$arrLibAchIDs[$intLibAssID] = $this->pdh->get('awards_library', 'achievement_id', array($intLibAssID));
		}
		
		//merge Awards
		$arrLibAchIDs = array_unique($arrLibAchIDs);
		
		//rewrite array to read the achievement table later
		$arrLibAssIDs = array_keys($arrLibAchIDs);
		
		//prüfe wieviele erfolge existieren /zähle sie
		//gehe in schleife 1 --"für die reihen"
		//gehe in schleife 2 führe aus so viel wie "proReihe" angegeben sind --"für die spalten"
		//rechne in schleife 2 das ergebnis von allen gezählten erfolgen um +1 --"benötigt für:
		// die berechnung wieviele pro reihe/spalte und welchen Erflg wir aus der library lesen"
		$allAwards = count($arrLibAchIDs);
		$award_counter = 0;
		
		while($award_counter < $allAwards){
			$this->tpl->assign_block_vars('awards_row', array());
			
			do{
				#d($arrLibAssIDs[$award_counter]);
				
				//parse the date 
				$strAchDate	= $this->time->user_date( $this->pdh->get('awards_library', 'date', array($arrLibAssIDs[$award_counter])) );
				
				//fetch from awards_achievements
				$intAchID = $this->pdh->get('awards_library', 'achievement_id', array($arrLibAssIDs[$award_counter]));
				
				$strAchName = $this->user->multilangValue( $this->pdh->get('awards_achievements', 'name', array($intAchID)) );
				$strAchDesc = $this->user->multilangValue( $this->pdh->get('awards_achievements', 'description', array($intAchID)) );
				$strAchIcon = $this->pdh->get('awards_achievements', 'icon', array($intAchID));
				$icon_folder = $this->pfh->FolderPath('images', 'awards');
				if( file_exists($icon_folder.$strAchIcon) ){
					$strAchIcon = $icon_folder.$strAchIcon;
				} else {
					$strAchIcon = 'plugins/awards/images/'.$strAchIcon;
				}
				
				if( pathinfo($strAchIcon, PATHINFO_EXTENSION) == "svg"){
					$strAchIcon = file_get_contents($strAchIcon);
					
					// build the CSS Code for each SVG
					$arrAchIconColors = unserialize( $this->pdh->get('awards_achievements', 'icon_colors', array($intAchID)) );
					$icon_color_step = 1;
					$strAchIconCSS = '';
					foreach($arrAchIconColors as $strAchIconColor){
						$strAchIconCSS .= '.award.ac-'.$intAchID.' .ac-icon svg g:nth-child('.$icon_color_step.'){fill: '.$strAchIconColor.';}';
						$icon_color_step++;
					}
					$this->tpl->add_css($strAchIconCSS);
				} else {
					$strAchIcon = '<img src="'.$strAchIcon.'" />';
				}
				
				$blnAchActive  = $this->pdh->get('awards_achievements', 'active', array($intAchID));
				$blnAchSpecial = $this->pdh->get('awards_achievements', 'special', array($intAchID));
				$intAchPoints  = $this->pdh->get('awards_achievements', 'points', array($intAchID));
				$intAchDKP     = $this->pdh->get('awards_achievements', 'dkp', array($intAchID));
				if($intAchDKP < 0){
					$blnAchDKP = 1;
				} elseif($intAchDKP > 0){
					$blnAchDKP = 2;
				} else {
					$blnAchDKP = 0;
				}
				
				
				$this->tpl->assign_block_vars('awards_row.award', array(
					'ID'		=> $intAchID,
					'TITLE'		=> $strAchName,
					'DESC'		=> $strAchDesc,
					'DATE'		=> $strAchDate,
					'ICON'	=> $strAchIcon,
					'ACTIVE'	=> $blnAchActive,
					'SPECIAL'	=> $blnAchSpecial,
					'AP'		=> $intAchPoints,
					'DKP'		=> $intAchDKP,
					'DKP_ACTIVE' => $blnAchDKP,
				));
				
				// ----------------------------------------
				// Begin of Member List
				$arrAdjMembers = array();
				$strAdjGK = $this->pdh->get('awards_library', 'adj_group_key', array($arrLibAssIDs[$award_counter]));
				$arrAdjIDs = $this->pdh->get('adjustment', 'ids_of_group_key', array($strAdjGK));
				foreach($arrAdjIDs as $intAdjID){
					$arrAdjMembers[] = $this->pdh->get('adjustment', 'member', array($intAdjID));
				}
				
				// parse all members who un/reached the award
				$arrAllUnreachedMember = array_diff($arrAllMemberIDs, $arrAdjMembers);
				
				foreach($arrAdjMembers as $intAdjMember){
					$arrAllReached[] = $this->pdh->get('member', 'memberlink_decorated', array($intAdjMember));
				}
				foreach($arrAllUnreachedMember as $intUnreached){
					$arrAllUnreached[] = $this->pdh->get('member', 'name_decorated', array($intUnreached));
				}
				
				
				for($member_count = 0; $member_count < count($arrAllMemberIDs); $member_count++){
					$this->tpl->assign_block_vars('awards_row.award.members', array(
						'MEMBER_REACHED'		=> $arrAllReached[$member_count],
						'MEMBER_UNREACHED'		=> $arrAllUnreached[$member_count],
					));
				}
				
				unset($arrAllUnreached);
				unset($arrAllReached);
				// ----------------------------------------
				$award_counter ++;
			}while($award_counter < $this->xAwardperRow);
		}
		
		
		// Generate the 'unreached' list
		$arrAllAchIDs = $this->pdh->get('awards_achievements', 'id_list');
		$arrAchIDs = array_diff($arrAllAchIDs, $arrLibAchIDs);
		
		$allUnreached = count($arrAchIDs);
		$unreached_counter = 1;
		
		while($unreached_counter <= $allUnreached){
			$this->tpl->assign_block_vars('unreached_row', array());
			while($unreached_counter <= $this->xAwardperRow){
				
				$strAchName = $this->user->multilangValue( $this->pdh->get('awards_achievements', 'name', array($arrAchIDs[$unreached_counter])) );
				$strAchDesc = $this->user->multilangValue( $this->pdh->get('awards_achievements', 'description', array($arrAchIDs[$unreached_counter])) );
				$strAchIcon = $this->pdh->get('awards_achievements', 'icon', array($arrAchIDs[$unreached_counter]));
				$icon_folder = $this->pfh->FolderPath('images', 'awards');
				if( file_exists($icon_folder.$strAchIcon) ){
					$strAchIcon = $icon_folder.$strAchIcon;
				} else {
					$strAchIcon = 'plugins/awards/images/'.$strAchIcon;
				}
				
				if( pathinfo($strAchIcon, PATHINFO_EXTENSION) == "svg"){
					$strAchIcon = file_get_contents($strAchIcon);
					
					// build the CSS Code for each SVG
					$arrAchIconColors = unserialize( $this->pdh->get('awards_achievements', 'icon_colors', array($intAchID)) );
					$icon_color_step = 1;
					$strAchIconCSS = '';
					foreach($arrAchIconColors as $strAchIconColor){
						$strAchIconCSS .= '.award.ac-'.$intAchID.' .ac-icon svg g:nth-child('.$icon_color_step.'){fill: '.$strAchIconColor.';}';
						$icon_color_step++;
					}
					$this->tpl->add_css($strAchIconCSS);
				} else {
					$strAchIcon = '<img src="'.$strAchIcon.'" />';
				}
				
				$blnAchActive  = $this->pdh->get('awards_achievements', 'active', array($arrAchIDs[$unreached_counter]));
				$blnAchSpecial = $this->pdh->get('awards_achievements', 'special', array($arrAchIDs[$unreached_counter]));
				$intAchPoints  = $this->pdh->get('awards_achievements', 'points', array($arrAchIDs[$unreached_counter]));
				$intAchDKP     = $this->pdh->get('awards_achievements', 'dkp', array($arrAchIDs[$unreached_counter]));
				if($intAchDKP < 0){
					$blnAchDKP = 1;
				} elseif($intAchDKP > 0){
					$blnAchDKP = 2;
				} else {
					$blnAchDKP = 0;
				}
				
				
				$this->tpl->assign_block_vars('unreached_row.award', array(
					'ID'		=> $arrAchIDs[$unreached_counter],
					'TITLE'		=> $strAchName,
					'DESC'		=> $strAchDesc,
					'ICON'	=> $strAchIcon,
					'ACTIVE'	=> $blnAchActive,
					'SPECIAL'	=> $blnAchSpecial,
					'AP'		=> $intAchPoints,
					'DKP'		=> $intAchDKP,
					'DKP_ACTIVE' => $blnAchDKP,
				));
				
			$unreached_counter ++;
			}
		}
		
		
		$this->tpl->assign_vars(array(
			'PROGRESS'			=> '',
		));
		
		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'    => $this->user->lang('awards'),
			'template_path' => $this->pm->get_data('awards', 'template_path'),
			'template_file' => 'awards.html',
			'display'       => true
		));

	}
}
?>