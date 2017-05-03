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
include_once(registry::get_const('root_path').'plugins/awards/cronjob/modules/cronmodules.aclass.php');

/*+----------------------------------------------------------------------------
  | awards_cronmodule
  +--------------------------------------------------------------------------*/
class dkp_cronmodule extends cronmodules {
	static public $language = array(
		'german'	=> array(
			'title'			=> 'Charakter erhielt über [x] DKP',
			'dkp'			=> 'DKP',
			'mdkp'			=> 'Multidkp-Konten',
		),
		'english'	=> array(
			'title'			=> 'Character reached over [x] DKP',
			'dkp'			=> 'DKP',
			'mdkp'			=> 'Multidkp accounts',
		)
	);
	protected $settings = array(
		'dkp'	=> 1000,
		'mdkp'	=> [1],
	);
	
	static public function check_requirements(){
		if(register('config')->get('enable_points')) return true;
		return false;
	}
	
	public function cron_process($intAchID, $arrMemberIDs){
		$arrMdkpIDs	= (empty($this->settings['mdkp']))? $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list'))) : $this->settings['mdkp'];
		
		$arrCountMemberIDs = array();
		foreach($arrMemberIDs as $intMemberID){
			foreach($arrMdkpIDs as $intMdkpID){
				$intCurrentPoints = $this->pdh->get('points', 'current_history', array($intMemberID, $intMultiDKP));
				if($intCurrentPoints > 0) $arrCountMemberIDs[$intMemberID] = $arrCountMemberIDs[$intMemberID] + $intCurrentPoints;
			}
		}
		
		$arrMemberIDs = array();
		foreach($arrCountMemberIDs as $intMemberID => $intCurrentPoints){
			if($intCurrentPoints >= $this->settings['dkp']) $arrMemberIDs[] = $intMemberID;
		}
		
		return $arrMemberIDs;
	}
	
	public function display_settings($jsonSettings){
		$this->parse_settings($jsonSettings);
		
		$hash_dkp	= substr(md5(__CLASS__.'dkp'), 0, 5);
		$hash_mdkp	= substr(md5(__CLASS__.'mdkp'), 0, 5);
		
		$all_mdkps			= $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list')));
		$hspinner_dkp		= (new hspinner('dkp', ['id'=>$hash_dkp, 'value' => $this->settings['dkp'], 'size' => 10, 'min' => 0, 'step' => 100, 'returnJS' => true]))->output();
		$hmultiselect_mdkp	= (new hmultiselect('mdkp', ['id'=>$hash_mdkp, 'options' => $all_mdkps, 'value' => $this->settings['mdkp'], 'returnJS' => true]))->output();
		
		$htmlout = '<fieldset class="settings">
			<legend>'.$this->lang('title').'</legend>
			<dl>
				<dt><label>'.$this->lang('dkp').'</label></dt>
				<dd>'.$hspinner_dkp.'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->lang('mdkp').'</label></dt>
				<dd>'.$hmultiselect_mdkp.'</dd>
			</dl>
		</fieldset>';
		
		return $htmlout;
	}
}
?>