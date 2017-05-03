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
class article_cronmodule extends cronmodules {
	static public $language = array(
		'german'	=> array(
			'title'			=> 'Benutzer hat [x] Artikel verfasst',
			'articles'		=> 'Artikel',
			'category'		=> 'Kategorie',
		),
		'english'	=> array(
			'title'			=> 'User has written [x] Articles',
			'articles'		=> 'Articles',
			'category'		=> 'Category',
		),
	);
	protected $settings = array(
		'articles'	=> 5,
		'category'	=> [],
	);
	
	public function cron_process($intAchID, $arrMemberIDs){
		$arrCategoryIDs	= (empty($this->settings['category']))? $this->pdh->get('article_categories', 'id_list') : $this->settings['category'];
		
		$arrMemberIDs = array();
		foreach($this->pdh->get('articles', 'id_list') as $intArticleID){
			$intUserID		= $this->pdh->get('articles', 'user_id', array($intArticleID));
			$intCategoryID	= $this->pdh->get('articles', 'category', array($intArticleID));
			
			if(isset($arrCategoryIDs[$intCategoryID])){
				$intMainCharID		= $this->pdh->get('user', 'mainchar', array($intUserID));
				$arrTwinkCharIDs	= $this->pdh->get('member', 'other_members', array($intMainCharID));
				$arrMemberIDs		= array_merge($arrMemberIDs, array($intMainCharID), $arrTwinkCharIDs);
			}
		}
		
		return $arrMemberIDs;
	}
	
	public function display_settings($jsonSettings){
		$this->parse_settings($jsonSettings);
		
		$hash_articles	= substr(md5(__CLASS__.'articles'), 0, 5);
		$hash_category	= substr(md5(__CLASS__.'category'), 0, 5);
		
		$all_categories			= $this->pdh->aget('article_categories', 'name', 0, array($this->pdh->get('article_categories', 'id_list')));
		$hspinner_articles		= (new hspinner('articles', ['id'=>$hash_articles, 'value' => $this->settings['articles'], 'size' => 6, 'min' => 0, 'max' => 10000, 'step' => 5, 'returnJS' => true]))->output();
		$hmultiselect_category	= (new hmultiselect('category', ['id'=>$hash_category, 'options' => $all_categories, 'value' => $this->settings['category'], 'returnJS'=>true]))->output();
		
		$htmlout = '<fieldset class="settings">
			<legend>'.$this->lang('title').'</legend>
			<dl>
				<dt><label>'.$this->lang('articles').'</label></dt>
				<dd>'.$hspinner_articles.'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->lang('category').'</label></dt>
				<dd>'.$hmultiselect_category.'</dd>
			</dl>
		</fieldset>';
		
		return $htmlout;
	}
}
?>