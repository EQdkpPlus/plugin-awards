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

class wow_cronmodule extends cronmodules {
	static public $language = array(
		'german'	=> array(
			'title'				=> 'World of Warcraft',
			'filter'			=> 'Filter nach',
			'filter_achievement'=> 'Erfolg [x] erhalten',
			'filter_chartitle'	=> 'Titel [x] erhalten',
			'filter_achpoints'	=> '[x] Erfolgspunkte',
			'filter_honorkills'	=> '[x] Ehrenhafte Siege',
			'achievement'		=> 'Erfolg',
			'achievement_help'	=> 'Bsp.: 9060 für Level 100;<br>wowhead.com/achievement=9060',
			'chartitle'			=> 'Titel',
			'chartitle_help'	=> 'Bsp.: 143 für Jenkins;<br>wowhead.com/title=143',
			'honorkills'		=> 'Ehrenhafte Siege',
			'achpoints'			=> 'Erfolgspunkte',
		),
		'english'	=> array(
			'title'				=> 'World of Warcraft',
			'filter'			=> 'Filter by',
			'filter_achievement'=> 'Achievement [x] reached',
			'filter_chartitle'	=> 'Title [x] reached',
			'filter_achpoints'	=> '[x] Achievement Points',
			'filter_honorkills'	=> '[x] Honorable Kills',
			'achievement'		=> 'Achievement',
			'achievement_help'	=> 'Ex.: 9060 for Level 100;<br>wowhead.com/achievement=9060',
			'chartitle'			=> 'Title',
			'chartitle_help'	=> 'Ex.: 143 for Jenkins;<br>wowhead.com/title=143',
			'honorkills'		=> 'Honorable Kills',
			'achpoints'			=> 'Achievement Points',
		),
	);
	protected $settings = array(
		'filter'		=> ['achievement'],
		'achievement'	=> '',
		'chartitle'		=> '',
		'achpoints'		=> 10000,
		'honorkills'	=> 100,
	);
	
	static public function check_requirements(){
		if(register('game')->get_game() == 'wow' && register('config')->get('game_importer_apikey') != '') return true;
		return false;
	}
	
	public function cron_process($intAchID, $arrMemberIDs){
		if($this->config->get('game_importer_apikey') != '' && $this->config->get('servername') != ''){
			$this->game->new_object('bnet_armory', 'armory', array(unsanitize($this->config->get('uc_server_loc')), $this->config->get('uc_data_lang')));
			
			$arrMembers = array();
			foreach($arrMemberIDs as $intMemberID){
				$strMemberName		= unsanitize($this->pdh->get('member', 'name', array($intMemberID)));
				$strMemberServer	= unsanitize($this->pdh->get('member', 'profile_field', array($intMemberID, 'servername')));
				$strServerName		= ($strMemberServer != '') ? $strMemberServer : unsanitize($this->config->get('servername'));
				$arrCharData		= $this->game->obj['armory']->character($strMemberName, $strServerName);
				
				if(!isset($arrCharData['status'])){
					
					// ToDo: Here we should make an AND or OR switch with a checkbox
					// And splitable achieves/titles like: split the achievements and titles by ; or any else
					if(in_array('achievement', $this->settings['filter']) && !empty($this->settings['achievement']) && is_numeric($this->settings['achievement'])){
						
						if(in_array($this->settings['achievement'], $arrCharData['achievements']['achievementsCompleted'])) $arrMembers[] = $intMemberID;
					}
					
					if(in_array('chartitle', $this->settings['filter']) && !empty($this->settings['chartitle']) && is_numeric($this->settings['chartitle'])){
						foreach($arrCharData['titles'] as $arrTitle){
							if($arrTitle['id'] == $this->settings['chartitle']) $arrMembers[] = $intMemberID;
						}
					}
					
					if(in_array('achpoints', $this->settings['filter'])){
						if($arrCharData['achievementPoints'] >= $this->settings['achpoints']) $arrMembers[] = $intMemberID;
					}
					
					if(in_array('honorkills', $this->settings['filter'])){
						if($arrCharData['totalHonorableKills'] >= $this->settings['honorkills']) $arrMembers[] = $intMemberID;
					}
				}
			}
			
			$arrMembers = array_unique($arrMembers);
		}
		
		return $arrMembers;
	}
	
	public function display_settings($jsonSettings){
		$this->parse_settings($jsonSettings);
		
		$hash_filter		= substr(md5(__CLASS__.'filter'), 0, 5);
		$hash_achievement	= substr(md5(__CLASS__.'achievement'), 0, 5);
		$hash_chartitle		= substr(md5(__CLASS__.'chartitle'), 0, 5);
		$hash_honorkills	= substr(md5(__CLASS__.'honorkills'), 0, 5);
		$hash_achpoints		= substr(md5(__CLASS__.'achpoints'), 0, 5);
		
		$filter_options	= array(
			'achievement' 	=> $this->lang('filter_achievement'),
			'chartitle'		=> $this->lang('filter_chartitle'),
			'achpoints'		=> $this->lang('filter_achpoints'),
			'honorkills'	=> $this->lang('filter_honorkills'),
		);
		$hcheckbox_filter	= (new hcheckbox('filter', ['id'=>$hash_filter, 'options' => $filter_options, 'value' => $this->settings['filter']]))->output();
		$htext_achievement	= (new htext('achievement', ['id'=>$hash_achievement, 'value' => $this->settings['achievement'], 'size' => 7]))->output();
		$htext_chartitle	= (new htext('chartitle', ['id'=>$hash_chartitle, 'value' => $this->settings['chartitle'], 'size' => 7]))->output();
		$htext_achpoints	= (new hspinner('achpoints', ['id'=>$hash_achpoints, 'value' => $this->settings['achpoints'], 'size' => 20, 'min' => 0, 'step' => 100, 'returnJS' => true]))->output();
		$htext_honorkills	= (new hspinner('honorkills', ['id'=>$hash_honorkills, 'value' => $this->settings['honorkills'], 'size' => 20, 'min' => 0, 'step' => 50, 'returnJS' => true]))->output();
		
		$htmlout = '<fieldset class="settings">
			<legend>'.$this->lang('title').'</legend>
			<dl>
				<dt><label>'.$this->lang('filter').'</label></dt>
				<dd>'.$hcheckbox_filter.'</dd>
			</dl>
			<div data-filter="'.$hash_filter.'_achievement">
				<legend>'.$this->lang('achievement').'</legend>
				<dl>
					<dt><label>'.$this->lang('achievement').'</label><br><span>'.$this->lang('achievement_help').'</span></dt>
					<dd>'.$htext_achievement.'</dd>
				</dl>
			</div>
			<div data-filter="'.$hash_filter.'_chartitle">
				<legend>'.$this->lang('chartitle').'</legend>
				<dl>
					<dt><label>'.$this->lang('chartitle').'</label><br><span>'.$this->lang('chartitle_help').'</span></dt>
					<dd>'.$htext_chartitle.'</dd>
				</dl>
			</div>
			<div data-filter="'.$hash_filter.'_achpoints">
				<legend>'.$this->lang('achpoints').'</legend>
				<dl>
					<dt><label>'.$this->lang('achpoints').'</label></dt>
					<dd>'.$htext_achpoints.'</dd>
				</dl>
			</div>
			<div data-filter="'.$hash_filter.'_honorkills">
				<legend>'.$this->lang('honorkills').'</legend>
				<dl>
					<dt><label>'.$this->lang('honorkills').'</label></dt>
					<dd>'.$htext_honorkills.'</dd>
				</dl>
			</div>
		</fieldset>
		<script type="text/javascript">
			$("#'.$hash_filter.'").parent().find(":input").each(function(){
				$(this).change(function(){
					handle_'.$hash_filter.'();
				});
			});
			
			function handle_'.$hash_filter.'(){
				var selector = \'div[data-filter^="'.$hash_filter.'_\';
				$(selector + \'"]\').each(function(){
					$(this).hide();
				});
				
				$("#'.$hash_filter.'").parent().find(":checked").each(function(){
					$(selector + $(this).val() + \'"]\').show();
				});
			}
			
			handle_'.$hash_filter.'();
		</script>';
		
		return $htmlout;
	}
}
?>