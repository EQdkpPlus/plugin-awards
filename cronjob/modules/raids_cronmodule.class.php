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
class raids_cronmodule extends cronmodules {
	static public $language = array(
		'german'	=> array(
			'title'			=> 'Charakter hat an [x] Raids teilgenommen',
			'raids'			=> 'Raids',
			'event'			=> 'Ereignis',
		),
		'english'	=> array(
			'title'			=> 'Character was on [x] Raids participated',
			'raids'			=> 'Raids',
			'event'			=> 'Event',
		),
	);
	protected $settings = array(
		'raids'	=> 25,
		'event'	=> [],
	);
	
	public function cron_process($intAchID, $arrMemberIDs){
		$arrEventIDs	= (empty($this->settings['event']))? $this->pdh->get('event', 'id_list') : $this->settings['event'];
		$arrAllRaidIDs	= $this->pdh->aget('raid', 'raidids4eventid', 0, array($arrEventIDs));
		
		$arrCountMemberIDs = array();
		foreach($arrAllRaidIDs as $arrRaidIDs){
			foreach($arrRaidIDs as $intRaidID){
				foreach($this->pdh->get('raid', 'raid_attendees', array($intRaidID)) as $intRaidMemberID){
					if(isset($arrMemberIDs[$intRaidMemberID])) $arrCountMemberIDs[$intRaidMemberID] = $arrCountMemberIDs[$intRaidMemberID] + 1;
				}
			}
		}
		
		$arrMemberIDs = array();
		foreach($arrCountMemberIDs as $intMemberID => $intRaidCounter){
			if($intRaidCounter >= $this->settings['raids']) $arrMemberIDs[] = $intMemberID;
		}
		
		return $arrMemberIDs;
	}
	
	public function display_settings($jsonSettings){
		$this->parse_settings($jsonSettings);
		
		$hash_raids	= substr(md5(__CLASS__.'raids'), 0, 5);
		$hash_event	= substr(md5(__CLASS__.'event'), 0, 5);
		
		$all_events			= $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
		$hspinner_raids		= (new hspinner('raids', ['id'=>$hash_raids, 'value' => $this->settings['raids'], 'size' => 5, 'min' => 0, 'max' => 10000, 'step' => 5, 'returnJS' => true]))->output();
		$hmultiselect_event	= (new hmultiselect('event', ['id'=>$hash_event, 'options' => $all_events, 'value' => $this->settings['event'], 'width' => 240, 'returnJS' => true]))->output();
		
		$htmlout = '<fieldset class="settings">
			<legend>'.$this->lang('title').'</legend>
			<dl>
				<dt><label>'.$this->lang('raids').'</label></dt>
				<dd>'.$hspinner_raids.'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->lang('event').'</label></dt>
				<dd>'.$hmultiselect_event.'</dd>
			</dl>
		</fieldset>';
		
		return $htmlout;
	}
}
?>