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
	
/*	// Dont use it now, we use a hardcoded variable while the development and testing
	private function set_cookie(){
		//dont set cookies if we dont have a cookie-name or cookie-path
		$cname = register('config')->get('cookie_name');
		$cpath = register('config')->get('cookie_path');
		if(empty($cname) || empty($cpath)) return;
		setcookie( $cname . '_awards', 1, 604800, $cpath, register('config')->get('cookie_domain'));
	}
*/



public $xAwardperRow = 1; // Gibt an wieviele Erfolge pro Reihe angezeigt werden sollen



	/**
	  * Display
	  * display all achievements
	  */
	public function display(){
		
		
		//fetch all Assignments
		$arrLibAssIDs = $this->pdh->get('awards_library', 'id_list');
		
		foreach($arrLibAssIDs as $intLibAssID){
			$arrLibAchIDs[$intLibAssID] = $this->pdh->get('awards_library', 'achievement_id', array($intLibAssID));
		}
		
		//merge Awards
		$arrLibAchIDs = array_unique($arrLibAchIDs);
		#$arrLibAchIDs = array_values($arrLibAchIDs);
		$arrLibAssIDs = array_keys($arrLibAchIDs);
		#d($arrLibAssIDs);
		
		
		
		/* // Das Läuft ...
		foreach($arrLibAchIDs as $intLibAchID){
			#$this->tpl->assign_block_vars('awards_row', array());
			$this->tpl->assign_var('ACTIVE', true);
			
			$this->tpl->assign_block_vars('awards', array(
				'NAME'		=> 'Name',
				'DESC'		=> 'Beschreibung',
			));
		}
		*/
		
		
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
				/* // Please uncommit it later when the SVGs are fixed
				if( pathinfo($strAchIcon, PATHINFO_EXTENSION) == "svg"){
					$strAchIcon = file_get_contents($strAchIcon);
				} else {
					$strAchIcon = '<img src="'.$strAchIcon.'" />';
				}*/
				
				$blnAchActive  = $this->pdh->get('awards_achievements', 'active', array($intAchID));
				$blnAchSpecial = $this->pdh->get('awards_achievements', 'special', array($intAchID));
				$strAchPoints  = $this->pdh->get('awards_achievements', 'points', array($intAchID));
				$strAchDKP     = $this->pdh->get('awards_achievements', 'dkp', array($intAchID));
				
				$this->tpl->assign_block_vars('awards_row.award', array(
					'ID'		=> $intAchID,
					'TITLE'		=> $strAchName,
					'DESC'		=> $strAchDesc,
					'DATE'		=> $strAchDate,
					'ICON_URL'	=> $strAchIcon,
					'ACTIVE'	=> $blnAchActive,
					'SPECIAL'	=> $blnAchSpecial,
					'AP'		=> $strAchPoints,
					'DKP'		=> $strAchDKP,
				));
				
				$award_counter;
				
				$award_counter ++;
			}while($award_counter < $this->xAwardperRow);
		}
		
		
		$this->tpl->assign_vars(array(
			'AW_TITLE'			=> '',
			'AW_COUNT'			=> '',
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