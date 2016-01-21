<?php
/*	Project:	EQdkp-Plus
 *	Package:	Awards Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if(!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}
include_once(registry::get_const('root_path').'plugins/awards/cronjob/modules/cronmodules.aclass.php');

class wow_cronmodule extends cronmodules {
	static public $language = array(
		'german'	=> array(
			'title'			=> 'World of Warcraft',
		),
		'english'	=> array(
			'title'			=> 'World of Warcraft',
		),
	);
	
	static public function check_requirements(){
		if(register('game')->get_game() == 'wow') return true;
		else return false;
	}
	
	public function cron_process($intAchID, $arrMemberIDs){
		return true;
	}
	
	public function display_settings(){
		$htmlout = '<fieldset class="settings mediumsettings">
			<dl>
				<dt><label>My Label</label></dt>
				<dd>Test Settings Formular of WoW Cronmodule</dd>
			</dl>
		</fieldset>';
		
		return $htmlout;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>