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

abstract class cronmodules extends gen_class {
	public function __construct($arrSettings=''){
		if(!empty($arrSettings) && is_array($arrSettings)) $this->settings = $arrSettings;
	}
	
	abstract public function cron_process($intAchID, $arrMemberIDs);
	
	static public function check_requirements(){
		return true;
	}
	
	final protected function lang($strLangCode){
		$arrLanguage	= static::$language;
		$strUserLang	= $this->user->lang_name;
		$strDefaultLang	= $this->config->get('default_lang');
		
		if(array_key_exists($strUserLang, $arrLanguage)){
			return $arrLanguage[$strUserLang][$strLangCode];
		}else{
			return $arrLanguage[$strDefaultLang][$strLangCode];
		}
	}
	
	final protected function parse_settings($jsonSettings){
		$arrSettings		= json_decode($jsonSettings, true);
		
		foreach($arrSettings as $key => $val){
			if(substr($key, -2) == '[]') $key = substr($key, 0, mb_strlen($key, 'UTF-8') - 2);
			$this->settings[$key] = $val;
		}
	}
}
?>