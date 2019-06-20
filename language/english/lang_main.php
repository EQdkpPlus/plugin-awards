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

if (!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  'awards'						=> 'Awards',

  // Description
  'awards_short_desc'			=> 'Get achievements, fame and honor.',
  'awards_long_desc'			=> 'This Plugin allows to obtain achievments.',
  
  // General
  'aw_achievement'				=> 'Achievement',
  'aw_achievements'				=> 'Achievements',
  'aw_ap'						=> 'Award Points',
  'aw_points'					=> 'Award Points',
  'aw_dkp'						=> 'DKP',
  'aw_progress'					=> 'Progress',
  'aw_all_guild_achievements'	=> 'All guildachievements',
  'aw_customtab_title'			=> 'My Achievements',
  'aw_tab_user'					=> 'My Achievements',
  'aw_user_unreached'			=> 'Has not earned this achievement',
  'aw_member_unreached'			=> 'not earned',
  'aw_is_inactive'				=> 'Achievement is inactive',
  'aw_is_special'				=> 'Achievement is invisible',
  
  'user_sett_tab_awards'		=> 'Awards Plugin',
  'user_sett_fs_awards'			=> 'Awards mainsettings',
  'user_sett_f_aw_show_hook'	=> 'Show Awards Quick Info',
  'user_sett_f_aw_layout'		=> 'Layout',
  'user_sett_f_aw_pagination'	=> 'Achievements per site',
  'user_sett_f_aw_admin_pagination'   => '[ACP] Achieve/Assignments per site',
  'user_sett_f_aw_layout_default'	  => 'Default',
  'user_sett_f_aw_layout_minimalist'  => 'Minimalist',
  'user_sett_f_ntfy_awards_new_award' => 'New Achievement',
  'user_sett_f_ntfy_awards' 	=> 'Awards',
  
  // Admin
  'aw_manage_achievements'		=> 'Manage achievements',
  'aw_manage_assignments'		=> 'Manage assignments',
  'aw_tab_assign'				=> '<i class="adminicon"></i>All assignments',
  'aw_tab_achieve'				=> '<i class="adminicon"></i>All achievements',
  
  'aw_add_achievement'			=> 'Add achievement',
  'aw_edit_achievement'			=> 'Edit achievement',
  'aw_add_assignment'			=> 'Assign achievement',
  'aw_edit_assignment'			=> 'Edit assignment',
  
  'aw_special'					=> 'Special achievement',
  'aw_value'					=> 'AP worth',
  'aw_dkp_value'				=> 'DKP worth',
  'aw_auto_assign'				=> 'Auto-assign',
  'aw_icon_header'				=> 'Select icon',
  'aw_upload_icon'				=> 'Add icon',
  'aw_edit_icon'				=> 'Edit icon',
  
  'aw_name_help'				=> 'Name / Title of achievement',
  'aw_desc_help'				=> 'Description of achievement',
  'aw_active_help'				=> 'Determines whether this achievement may be assigned.',
  'aw_special_help'				=> 'Invisible Achievements will be only shown, when they are reached.',
  'aw_ap_help'					=> 'Award Points (achievementpoints) serve as Awards own currency',
  'aw_dkp_help'					=> 'These DKP get the respective player which reached this achievement.',
  'aw_dkp_warn'					=> 'A later change of DKP is not recommended, already assigned DKP will not be updated!',
  'aw_event_help'				=> 'Select an Event for this achievement.',
  'aw_auto_assign_help'			=> '"Plugins: Awards" Cronjob must be activated!',
  'aw_icon_help'				=> 'Select or upload a matched icon.<br />SVG images have additional a colorswitch option, but for this function you need to optimize your SVG like the <a href="https://eqdkp-plus.eu/wiki/Plugin:_Awards">Wiki</a>.',
  
  'aw_sortation'				=> 'Sorting',
  'aw_icon_colors'				=> 'Icon colors',
  'aw_module'					=> 'Cronjob Module',
  'aw_module_settings'			=> 'Cronjob Module settings',
  
  'aw_listachiev_footcount'		=> '... %s Achievement(s) founded / %s per site',
  'aw_listassign_footcount'		=> '... %s Assignment(s) founded / %s per site',
  
  // Awards Quick Info
  'aw_tt_reached_ap'			=> 'Reached Awardpoints',
  'aw_tt_reached_dkp'			=> 'Reached DKP',
  'aw_tt_my_awards'				=> 'All my achievements',
  'aw_tt_all_awards'			=> 'All Guildachievments',
  
  
  // Cronjob Modules
  'aw_module_row_delete'		=> 'Delete this condition',
  'aw_module_condition'			=> 'these condtions must be hitted.',
  'aw_module_all'				=> 'All',
  'aw_module_any'				=> 'One of',
  'aw_module_choose_option'		=> '-- Choose a condition --',
  
  
  // System Messages
  'action_achievement_added'	=> 'Achievement created',
  'action_achievement_deleted'	=> 'Achievement deleted',
  'action_achievement_updated'	=> 'Achievement updated',
  'action_assignment_added'		=> 'Achievement assigned',
  'action_assignment_deleted'	=> 'Assignment edited',
  'action_assignment_deleted'	=> 'Assignment deleted',
  
  'aw_plugin_not_installed'		=> 'Awards-Plugin is not installed.',
  'aw_no_permission'			=> 'You don\'t have the permissions.',
  'aw_add_success'				=> '%s was added',
  'aw_add_nosuccess'			=> '%s could not be added',
  'aw_upd_success'				=> '%s has been changed',
  'aw_upd_nosuccess'			=> '%s couldn\'t be changed',
  'aw_assign_success'			=> '%s was<br />%s assigned',
  'aw_assign_nosuccess'			=> '%s could not be assigned',
  'aw_del_assign'				=> 'Assignment(s) deleted',
  'aw_module_load_error'		=> 'Couldn\'t load Modul.<br />Try again: <a href="javascript:get_module_settings(\'%s\');" onclick="$(this).parent().remove();">Load module</a>',
  
  'aw_confirm_delete_achievement'	=> 'Are you sure, that you will delete the selected %s achievements? All associated DKP of the characters will be deleted, too!',
  'aw_confirm_delete_assignment'	=> 'Are you sure, that you will delet all %s assignments? All reached DKP will be deleted too!',
  
  'aw_upd_assignment_warning'	=> '<h3>A later editing of an assignment is on your own risk!</h3>
  									No ones get notifications and while the editing proccess can you get irreversibles errors.<br />
									So use this function with cautious.',
);

?>
