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
 
if (!defined('EQDKP_INC')){
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | awards_userprofile_customtabs_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('awards_userprofile_customtabs_hook')){
  class awards_userprofile_customtabs_hook extends gen_class
  {
	
	/**
	  * userprofile_customtabs
	  * display the achievement tab
	  */
	public function userprofile_customtabs($user_id){
		if($this->user->check_auths(array('u_awards_view', 'a_awards_manage'), 'OR', false)){
			$this->tpl->add_js("
				$('.awToggleTrigger').on('click', function(event){
					if ($(this).hasClass('member-view')){
						$(this).children('.ac-user-list').css('display','none');
						$(this).removeClass('member-view');
					} else {
						$(this).addClass('member-view');
						$(this).children('.ac-user-list').css('display','inline-block');
					}
				});
			", "docready");
			
			
			$content = '<div id="awards"><div class="aw-list"><div class="reached">';
			
			$arrAllMemberIDs = $this->pdh->get('member', 'connection_id', array($user_id['user_id']));
			$arrLibAssIDs	 = $this->pdh->get('awards_library', 'id_list');
			
			foreach($arrLibAssIDs as $intLibAssID){
				$arrLibAchIDs[$intLibAssID] = $this->pdh->get('awards_library', 'achievement_id', array($intLibAssID));
			}
			
			//merge & rewrite Awards
			$arrLibAchIDs = array_unique($arrLibAchIDs);
			$arrLibAssIDs = array_keys($arrLibAchIDs);
			
			foreach($arrLibAssIDs as $intAssID){
				$strAchDate	 = $this->time->user_date( $this->pdh->get('awards_library', 'date', array($intAssID)) );
				$intAchID	 = $this->pdh->get('awards_library', 'achievement_id', array($intAssID));
				$strAchName  = $this->user->multilangValue( $this->pdh->get('awards_achievements', 'name', array($intAchID)) );
				$strAchDesc  = $this->user->multilangValue( $this->pdh->get('awards_achievements', 'description', array($intAchID)) );
				$strAchIcon  = $this->pdh->get('awards_achievements', 'icon', array($intAchID));
				$icon_folder = $this->pfh->FolderPath('images', 'awards');
				if( file_exists($icon_folder.$strAchIcon) ){
					$strAchIcon = $icon_folder.$strAchIcon;
				} else {
					$strAchIcon = 'plugins/awards/images/'.$strAchIcon;
				}
				
				$blnAchActive  = $this->pdh->get('awards_achievements', 'active', array($intAchID));
				$blnAchSpecial = $this->pdh->get('awards_achievements', 'special', array($intAchID));
				$intAchPoints  = $this->pdh->get('awards_achievements', 'points', array($intAchID));
				$intAchDKP     = $this->pdh->get('awards_achievements', 'dkp', array($intAchID));
				if($intAchDKP < 0){
					$blnAchDKP = 1;
				} elseif($intAchDKP > 0){
					$blnAchDKP = 2;
				} else {
					$blnAchDKP = 0;
				}
				
				//------------------------------------------------------------------------------
				$content .= '
					<div class="award ac-'.$intAchID.' awToggleTrigger">
						<div class="ac-icon floatLeft">
							<img src="/'.$strAchIcon.'" />
						</div>
						<div class="ac-points floatRight">
				';
				
				if($blnAchDKP != 0){
					if($blnAchDKP == 1){
						$content .= '<span class="ac-points-big" style="color: #C03D00;">';
					} else {
						$content .= '<span class="ac-points-big" style="color: #20C000;">';
					}
					$content .= $intAchDKP.'<span class="ac-points-small">'.$intAchPoints.'</span></span>';
					
				} else {
					$content .= '<span class="ac-points-big">'.$intAchPoints.'</span>';
				}
				
				$content .= '
					</div>
					<div class="ac-main">
						<h2 class="ac-title">'.$strAchName.'</h2>
						<p class="ac-desc">'.$strAchDesc.'</p>
						<p class="ac-date">'.$strAchDate.'</p>
					</div>
					<div class="ac-user-list" style="display:none;">
						Bisher haben 73 deiner Charactere diesen Erfolg erreicht.
					</div>
				</div>
				';
			}
			
			$content .= '</div><div class="unreached">';
			###
			$content .= 'Hier sieht man die nicht erreichten non-special awards.';
			###
			$content .= '</div></div></div>';
			
			$output = array(
				'title'   => $this->user->lang('aw_customtab_title'),
				'content' => $content,
			);
			return $output;
		}
	}
	
	
  } //end class
} //end if class not exists
?>