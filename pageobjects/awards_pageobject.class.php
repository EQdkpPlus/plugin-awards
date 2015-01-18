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


// Gibt an wieviele Erfolge pro Reihe angezeigt werden sollen
public $xAwardperRow = 2;



	/**
	  * Display
	  * display all achievements
	  */
	public function display(){
		
		
		//fetch all Assignments
		$arrLibAssIDs = $this->pdh->get('awards_library', 'id_list');
		
		foreach($arrLibAssIDs as $intLibAssID){
			$arrLibAchIDs[] = $this->pdh->get('awards_library', 'achievement_id', array($intLibAssID));
		}
		$arrLibAchIDs = array_unique($arrLibAchIDs);	// die erhaltenen Awards als Award ID
		
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
		$award_counter = 1;
		
		while($award_counter <= $allAwards){
			$this->tpl->assign_block_vars('awards_row', array());
			
			do{
				
				$this->tpl->assign_block_vars('awards_row.award', array(
					'NAME'		=> 'Name',
					'DESC'		=> 'Beschreibung',
				));
				
				$award_counter;
				d($award_counter);
				
				$award_counter ++;
			}while($award_counter <= $this->xAwardperRow);
			d('#');
		}
		
		
		// ------------------------------------------------
/*		$num = count($icons);
		$fields = (ceil($num/6))*6;
		$i = 0;
		
		if ($id) $strAchIcon = $this->pdh->get('awards_achievements', 'icon', array($id));
		
		while($i <= $fields){
			$this->tpl->assign_block_vars('files_row', array());
			$this->tpl->assign_var('ICONS', true);
			$b = $i+6;
			
			for($i; $i<$b; $i++){
			$icon = (isset($icons[$i])) ? $icons[$i] : '';
			$this->tpl->assign_block_vars('files_row.fields', array(
					'NAME'		=> pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION),
					'CHECKED'	=> (isset($strAchIcon) AND pathinfo($icon, PATHINFO_FILENAME).'.'.pathinfo($icon, PATHINFO_EXTENSION) == $strAchIcon) ? ' checked="checked"' : '',
					'IMAGE'		=> "<img src='".$icon."' alt='".$icon."' width='48px' style='eventicon' />",
					'CHECKBOX'	=> ($i < $num) ? true : false)
				);
			}
		}*/
		
		
		
		
		
		
		
		
		
		
		
				


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