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
		public function __construct(){
			/*
			$this->defaults['active']		= true;
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= true;
			$this->defaults['delay']		= false;
			$this->defaults['ajax']			= false;
			$this->defaults['extern']		= true;
			$this->defaults['path']			= '/plugins/awards/cronjob/';
			$this->defaults['description']	= 'Plugin: Awards';
			*/
		}

		public function run(){
			$arrAchIDs = $this->pdh->get('awards_achievements', 'id_list');
			
			foreach($arrAchIDs as $intAchID){
				$strAchModule = $this->pdh->get('awards_achievements', 'module', array($intAchID));
				
				$test = fopen('cron_test.txt', 'w'); 
				fclose($test);
				
				if(!empty($strAchModule)){
					//require($this->toor_path.'/plugins/awards/cronjob/module/'.$strAchModule.'.class.php');
					//do anything
				}
			}
		}
	}
}
?>