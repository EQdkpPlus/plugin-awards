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

/*+----------------------------------------------------------------------------
  | awards_cronmodule
  +--------------------------------------------------------------------------*/
class items_cronmodule extends cronmodules {
	static public $language = array(
		'german'	=> array(
			'title'			=> 'Charakter erhielt über [x] Items',
			'items'			=> 'Items',
			'filter'		=> 'Filter nach',
			'filter_0'		=> 'Itempool',
			'filter_1'		=> 'GameItem-ID',
			'pool'			=> 'Itempool',
			'gameid'		=> 'GameItem-ID',
		),
		'english'	=> array(
			'title'			=> 'Character reached over [x] Items',
			'items'			=> 'Items',
			'filter'		=> 'Filter by',
			'filter_0'		=> 'Itempool',
			'filter_1'		=> 'GameItem-ID',
			'pool'			=> 'Itempool',
			'gameid'		=> 'GameItem-ID',
		),
	);
	protected $settings = array(
		'items'		=> 25,
		'filter'	=> 0,
		'pool'		=> array(1),
		'gameid'	=> '',
	);
	
	public function cron_process($intAchID, $arrMemberIDs){
		return true;
	}
	
	public function display_settings($jsonSettings){
		$this->parse_settings($jsonSettings);
		
		$hash_items		= substr(md5(__CLASS__.'items'), 0, 5);
		$hash_filter	= substr(md5(__CLASS__.'filter'), 0, 5);
		$hash_pool		= substr(md5(__CLASS__.'pool'), 0, 5);
		$hash_gameid	= substr(md5(__CLASS__.'gameid'), 0, 5);
		
		$all_pools			= $this->pdh->aget('itempool', 'name', 0, array($this->pdh->get('itempool', 'id_list')));
		$hspinner_items		= new htext('items', array('id'=>$hash_items, 'value' => $this->settings['items'], 'size' => 5));
		$hradio_filter		= new hradio('filter', array('id'=>$hash_filter, 'options' => array(0 => $this->lang('filter_0'), 1 => $this->lang('filter_1')), 'default' => $this->settings['filter']));
		$hmultiselect_pool	= new hmultiselect('pool', array('id'=>$hash_pool, 'options' => $all_pools, 'value' => $this->settings['pool']));
		$htext_gameid		= new htext('gameid', array('id'=>$hash_gameid, 'size' => 20));
		
		$htmlout = '<fieldset class="settings mediumsettings">
			<legend>'.$this->lang('title').'</legend>
			<dl>
				<dt><label>'.$this->lang('items').'</label></dt>
				<dd>'.$hspinner_items.'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->lang('filter').'</label></dt>
				<dd>'.$hradio_filter.'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->lang('pool').'</label></dt>
				<dd>'.$hmultiselect_pool.'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->lang('gameid').'</label></dt>
				<dd>'.$htext_gameid.'</dd>
			</dl>
		</fieldset>
		<script type="text/javascript">
			$("#'.$hash_items.'").spinner({min: 0, max: 1000, step: 5});
			$("#'.$hash_pool.'").multiselect();
			$("#'.$hash_filter.' :input").change(function(event){
				if( $(this).val() == 0){
					$("#'.$hash_gameid.'").parent().parent().hide();
					$("#'.$hash_pool.'").parent().parent().show();
				}else{
					$("#'.$hash_pool.'").parent().parent().hide();
					$("#'.$hash_gameid.'").parent().parent().show();
				}
			});
			
			if( '.$this->settings['filter'].' == 0){
				$("#'.$hash_gameid.'").parent().parent().hide();
			}else{
				$("#'.$hash_pool.'").parent().parent().hide();
			}
		</script>';
		
		return $htmlout;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	public $requiredItems = 25;	


	public function run($intAchID, $arrMemberIDs){
		if($arrMemberIDs){
			$this->get_data($intAchID);
			
			$returnMemberIDs = array();
			foreach($arrMemberIDs as $intMemberID){
				$arrItems = $this->pdh->get('item', 'itemids4memberid', array($intMemberID));
				
				if(count($arrItems) >= $this->requiredItems)
					$returnMemberIDs[] = $intMemberID;
			}
			
			if($returnMemberIDs) return $returnMemberIDs;
			return false;
		}
		return false;
	}

	//fetch module settings of award
	public function get_data($intAchID){
		$strModuleData = unserialize( $this->pdh->get('awards_achievements', 'module_set', array($intAchID)) );
		$this->requiredItems = (isset($strModuleData['items']))? $strModuleData['items'] : 25;
	}
	
}
?>