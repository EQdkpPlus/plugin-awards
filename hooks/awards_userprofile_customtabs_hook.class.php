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
			
			$content = '
				<div id="awards">
					<div id="progress-header">
						<div class="progress-left floatLeft">Fortschritt:&nbsp;</div>
						<div class="progress-right floatRight">
							<span id="achievement-points">Loading...</span>&nbsp;<i class="fa fa-bookmark-o"></i>
						</div>
						<div id="my_aw_progress">
							<div class="progress-label">Loading...</div>
						</div>
					</div>
					<div class="aw-list">
						<div class="reached">
			';
			
			$arrAllMemberIDs  = $this->pdh->get('member', 'connection_id', array($user_id['user_id']));
			$arrAllAwards	  = $this->pdh->get('awards_achievements', 'id_list');
			$arrLibAssIDs	  = array();
			$arrReachedAwards = array();
			$intReachedAP	  = 0;
			
			//fetch all assignments of each member and filter double entrys
			$arrLibAssIDs = array();
			foreach($arrAllMemberIDs as $intMemberID){
				$ass_by_member = $this->pdh->get('awards_library', 'ids_where_member', array($intMemberID));
				foreach($ass_by_member as $ass_id){
					$arrLibAssIDs[] = $ass_id;
				}
			}
			$arrLibAssIDs = array_unique($arrLibAssIDs);
			
			//fetch the achievements by assignments
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
				
				if( pathinfo($strAchIcon, PATHINFO_EXTENSION) == "svg"){
					$strAchIcon = file_get_contents($strAchIcon);
					
					// build the CSS Code for each SVG
					$arrAchIconColors = unserialize( $this->pdh->get('awards_achievements', 'icon_colors', array($intAchID)) );
					$icon_color_step = 1;
					$strAchIconCSS = '';
					foreach($arrAchIconColors as $strAchIconColor){
						$strAchIconCSS .= '.award.ac-'.$intAchID.' .ac-icon svg g:nth-child('.$icon_color_step.'){fill: '.$strAchIconColor.';}';
						$icon_color_step++;
					}
					$this->tpl->add_css($strAchIconCSS);
				} else {
					$strAchIcon = '<img src="'.$strAchIcon.'" />';
				}
				
				$blnAchActive  = $this->pdh->get('awards_achievements', 'active', array($intAchID));
				$blnAchSpecial = $this->pdh->get('awards_achievements', 'special', array($intAchID));
				$intAchPoints  = $this->pdh->get('awards_achievements', 'points', array($intAchID));
				$intAchDKP     = $this->pdh->get('awards_achievements', 'dkp', array($intAchID));
				$intReachedAP += $intAchPoints;
				$arrReachedAwards[] = $intAchID;
				if($intAchDKP < 0){
					$blnAchDKP = 1;
				} elseif($intAchDKP > 0){
					$blnAchDKP = 2;
				} else {
					$blnAchDKP = 0;
				}
				
				//build the HTML structure for achievment display of fetched data
				$content .= '
					<div class="award ac-'.$intAchID.' awToggleTrigger">
						<div class="ac-icon floatLeft">
							'.$strAchIcon.'
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
			
			//---- BUILD UNREACHED Container -----------------------------------------
			$content .= '</div><div class="unreached">';
			####################################
			
			$arrUnreachedAwards = array_diff($arrAllAwards, $arrReachedAwards);
			foreach($arrUnreachedAwards as $intUnreachedAwardID){
				$blnAchSpecial = $this->pdh->get('awards_achievements', 'special', array($intUnreachedAwardID));
				if(!$blnAchSpecial)continue;
				
				$strAchName  = $this->user->multilangValue( $this->pdh->get('awards_achievements', 'name', array($intUnreachedAwardID)) );
				$strAchDesc  = $this->user->multilangValue( $this->pdh->get('awards_achievements', 'description', array($intUnreachedAwardID)) );
				$strAchIcon  = $this->pdh->get('awards_achievements', 'icon', array($intUnreachedAwardID));
				$icon_folder = $this->pfh->FolderPath('images', 'awards');
				if( file_exists($icon_folder.$strAchIcon) ){
					$strAchIcon = $icon_folder.$strAchIcon;
				} else {
					$strAchIcon = 'plugins/awards/images/'.$strAchIcon;
				}
				
				if( pathinfo($strAchIcon, PATHINFO_EXTENSION) == "svg"){
					$strAchIcon = file_get_contents($strAchIcon);
					
					// build the CSS Code for each SVG
					$arrAchIconColors = unserialize( $this->pdh->get('awards_achievements', 'icon_colors', array($intAchID)) );
					$icon_color_step = 1;
					$strAchIconCSS = '';
					foreach($arrAchIconColors as $strAchIconColor){
						$strAchIconCSS .= '.award.ac-'.$intAchID.' .ac-icon svg g:nth-child('.$icon_color_step.'){fill: '.$strAchIconColor.';}';
						$icon_color_step++;
					}
					$this->tpl->add_css($strAchIconCSS);
				} else {
					$strAchIcon = '<img src="'.$strAchIcon.'" />';
				}
				
				$blnAchActive  = $this->pdh->get('awards_achievements', 'active', array($intUnreachedAwardID));
				$intAchPoints  = $this->pdh->get('awards_achievements', 'points', array($intUnreachedAwardID));
				$intAchDKP     = $this->pdh->get('awards_achievements', 'dkp', array($intUnreachedAwardID));
				if($intAchDKP < 0){
					$blnAchDKP = 1;
				} elseif($intAchDKP > 0){
					$blnAchDKP = 2;
				} else {
					$blnAchDKP = 0;
				}
				
				//build the HTML structure
				$content .= '
					<div class="award ac-'.$intAchID.' awToggleTrigger">
						<div class="ac-icon floatLeft">
							'.$strAchIcon.'
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
						<p class="ac-date"> </p>
					</div>
					<div class="ac-user-list" style="display:none;">
						Dieser Erfolg wird noch erarbeitet...
					</div>
				</div>
				';
			}
			
			####################################
			$content .= '</div></div></div>';
			
			
			
			$this->tpl->add_js('
				$("#my_aw_progress").progressbar({
					value: '.count($arrLibAchIDs).',
					max: '.count($arrAllAwards).',
				});
				$(".progress-label").text("'.count($arrLibAchIDs).' / '.count($arrAllAwards).'");
				$("#achievement-points").text("'.$intReachedAP.'");
				
				$(".awToggleTrigger").on("click", function(event){
					if ($(this).hasClass("member-view")){
						$(this).children(".ac-user-list").css("display","none");
						$(this).removeClass("member-view");
					} else {
						$(this).addClass("member-view");
						$(this).children(".ac-user-list").css("display","inline-block");
					}
				});
			', 'docready');
			
			//------------------------------------------------------
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