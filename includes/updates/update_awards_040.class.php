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
if(!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');
if(!class_exists('update_awards_040')){
	class update_awards_040 extends sql_update_task {
		public $author		= 'Asitara';
		public $version		= '0.4.0';
		public $name		= 'Awards 0.4.0 Update';
		public $type		= 'plugin_update';
		public $plugin_path	= 'awards';
		
		public function __construct(){
			parent::__construct();
			
			// init language
			$this->langs = array(
				'english' => array(
					'update_awards_040'	=> 'Awards 0.4.0 Update Package',
					'update_function'	=> 'Change old Module Settings',
				),
				'german' => array(
					'update_awards_040'	=> 'Awards 0.4.0 Update Paket',
					'update_function'	=> 'Ändere alte Modul Einstellungen',
				),
			);
		}
		
		public function update_function(){
			$arrNewSettings = array();
			
			//fetch old settings
			$objQuery = $this->db->query('SELECT id, module_set FROM __awards_achievements');
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$arrOldSettings[(int)$drow['id']] = $drow['module_set'];
				}
			}
			
			//parse old & new settings
			foreach($arrOldSettings as $intAchID => $strSettings){
				$arrSettings = unserialize($strSettings);
				
				if(!empty($arrSettings)){
					foreach($arrSettings as $strModule => $strSetting){
						
						switch($strModule){
							case 'raids':
								$arrSetting = array('raids' => $strSetting, 'event' => array());
								break;
							case 'items':
								$arrSetting = array('items'	=> $strSetting, 'filter' => 0, 'pool' => array(), 'gameid' => '');
								break;
							case 'dkp':
								$arrSetting = array('dkp' => $strSetting, 'mdkp' => array());
								break;
							default:
								$arrSetting = array();
								break;
						}
						
						if(!isset($arrNewSettings[$intAchID])){
							$arrNewSettings[$intAchID] = array($strModule => $arrSetting);
						}else{
							$arrNewSettings[$intAchID][$strModule] = $arrSetting;
						}
					}
				}
			}
			
			//run SQL querys
			foreach($arrNewSettings as $intAchID => $arrSettings){
				$objQuery = $this->db->prepare("UPDATE __awards_achievements :p WHERE id=?")->set(array(
					'module_set' => serialize($arrSettings)
				))->execute($intAchID);
				
				if($objQuery) $this->pdh->enqueue_hook('awards_achievements_update');
			}
			
			$this->pdh->process_hook_queue();
			return true;
		}
	}
}
?>