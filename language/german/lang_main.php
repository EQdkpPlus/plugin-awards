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

$lang = array(
  'awards'						=> 'Awards',
  
  // Description
  'awards_short_desc'			=> 'Erhalte Erfolge, Ruhm und Ehre.',
  'awards_long_desc'			=> 'Dieses Plugin ermöglicht es Erfolge zu erhalten.',
  
  // General
  'aw_achievement'				=> 'Erfolg',
  'aw_achievements'				=> 'Erfolge',
  'aw_ap'						=> 'Award Punkte',
  'aw_points'					=> 'Award Punkte',
  'aw_dkp'						=> 'DKP',
  'aw_progress'					=> 'Fortschritt',
  'aw_all_guild_achievements'	=> 'Alle Gildenerfolge',
  'aw_customtab_title'			=> 'Meine Erfolge',
  'aw_tab_user'					=> 'Meine Erfolge',
  'aw_user_unreached'			=> 'Hat diesen Erfolg noch nicht errungen',
  'aw_member_unreached'			=> 'nicht errungen',
  'aw_is_inactive'				=> 'Erfolg ist deaktiviert',
  'aw_is_special'				=> 'Erfolg ist versteckt',
  
  'user_sett_tab_awards'		=> 'Awards Plugin',
  'user_sett_fs_awards'			=> 'Awards Haupteinstellungen',
  'user_sett_f_aw_show_hook'	=> 'Awards Quick Info anzeigen',
  'user_sett_f_aw_layout'		=> 'Layout',
  'user_sett_f_aw_pagination'	=> 'Erfolge pro Seite',
  'user_sett_f_aw_admin_pagination'   => '[ACP] Erfolge/Zuweisungen pro Seite',
  'user_sett_f_aw_layout_default'	  => 'Standart',
  'user_sett_f_aw_layout_minimalist'  => 'Minimalistisch',
  
  // Admin
  'aw_manage_achievements'		=> 'Erfolge verwalten',
  'aw_manage_assignments'		=> 'Zuweisungen verwalten',
  'aw_tab_assign'				=> '<i class="adminicon"></i>Alle Erfolge',
  'aw_tab_achieve'				=> '<i class="adminicon"></i>Alle Zuweisungen',
  
  'aw_add_achievement'			=> 'Erfolg hinzufügen',
  'aw_edit_achievement'			=> 'Erfolg editieren',
  'aw_add_assignment'			=> 'Erfolg zuweisen',
  'aw_edit_assignment'			=> 'Zuweisung editieren',
  
  'aw_special'					=> 'Spezial',
  'aw_value'					=> 'AP Wert',
  'aw_dkp_value'				=> 'DKP Wert',
  'aw_auto_assign'				=> 'Automatisch zuweisen',
  'aw_icon_header'				=> 'Icon auswählen',
  'aw_upload_icon'				=> 'Icon hinzufügen',
  
  'aw_name_help'				=> 'Name / Titel des Erfolgs',
  'aw_desc_help'				=> 'Beschreibung des Erfolgs',
  'aw_active_help'				=> 'Bestimmt, ob dieser Erfolg zugewiesen werden darf.',
  'aw_special_help'				=> 'Besondere Erfolge sind erst sichtbar wenn diese errungen wurden.',
  'aw_ap_help'					=> 'Award Punkte(Erfolgspunkte) dienen als seperate Währung zu DKP.',
  'aw_dkp_help'					=> 'DKP Punkte werden bei erhalt dem jeweiligem Spieler zugerechnet.',
  'aw_dkp_warn'					=> 'Ein nachträgliches ändern der DKP wird nicht empfohlen, bereits vergebene DKP werden nicht aktualisiert!',
  'aw_event_help'				=> 'Wähle ein Ereignis für diesen Erfolg aus.',
  'aw_auto_assign_help'			=> '"Plugins: Awards" Cronjob muss aktiviert sein!<br />
									Beispiel: [Ab X Raids] 100',
  'aw_icon_help'				=> 'Wähle ein passendes Icon aus oder lade ein eigenes hoch.<br />SVG Bilder bieten zusätzliche Farbmöglichkeiten, dazu sollten sie aber entsprechend angepasst sein siehe dazu das Wiki.',
  
  'aw_sortation'				=> 'Sortierung',
  'aw_icon_colors'				=> 'Icon Farben',
  'aw_module'					=> 'Cronjob Modul',
  'aw_module_settings'			=> 'Cronjob Modul Einstellungen',
  
  'aw_listachiev_footcount'		=> '... %s Erfolg(e) gefunden / %s pro Seite',
  'aw_listassign_footcount'		=> '... %s Zuweisung(en) gefunden / %s pro Seite',
  
  // Awards Quick Info
  'aw_tt_reached_ap'			=> 'Erhaltene Awardpunkte',
  'aw_tt_reached_dkp'			=> 'Erhaltene DKP',
  'aw_tt_my_awards'				=> 'Alle meine Erfolge',
  'aw_tt_all_awards'			=> 'Alle Gildenerfolge',
  
  
  // Cronjob Modules
  'aw_module_row_delete'		=> 'Lösche diese Bedingung',
  'aw_module_condition'			=> 'der folgenden Bedingungen müssen zu treffen.',
  'aw_module_all'				=> 'Alle',
  'aw_module_any'				=> 'Eines',
  
  'aw_cronmodule_raids'			=> 'Charakter hat an [x] Raids teilgenommen',
  'aw_cronmodule_inf_raids'		=> 'Raids',
  'aw_cronmodule_birthday'		=> 'Benutzer hat Geburtstag',
  'aw_cronmodule_cap'			=> 'Charakter erhielt über [x] DKP',
  'aw_cronmodule_inf_cap'		=> 'DKP',
  'aw_cronmodule_items'			=> 'Charakter erhielt über [x] Items',
  'aw_cronmodule_inf_items'		=> 'Items',
  /* PUT HERE THE LANGUAGE STRIPES OF YOUR OWN MODULE */
  
  
  // System Messages
  'action_achievement_added'	=> 'Erfolg erstellt',
  'action_achievement_deleted'	=> 'Erfolg gelöscht',
  'action_achievement_updated'	=> 'Erfolg aktualisiert',
  'action_assignment_added'		=> 'Erfolg zugewiesen',
  'action_assignment_deleted'	=> 'Erfolgszuweisung geändert',
  'action_assignment_deleted'	=> 'Erfolgszuweisung gelöscht',
  
  'aw_plugin_not_installed'		=> 'Das Awards-Plugin ist nicht installiert.',
  'aw_no_permission'			=> 'Du hast keine Berechtigung die Erfolge einzusehen.',
  'aw_add_success'				=> '%s wurde hinzugefügt',
  'aw_add_nosuccess'			=> '%s konnte nicht hinzugefügt werden',
  'aw_assign_success'			=> '%s wurde<br />%s zugewiesen',
  'aw_assign_nosuccess'			=> '%s konnte nicht zugewiesen werden',
  'aw_del_assign'				=> 'Zuweisung(en) entfernt',
  
  'aw_confirm_delete_achievement'	=> 'Bist du sicher, dass Du die Erfolge %s wirklich löschen willst? Alle erhaltenen DKP werden dabei auch gelöscht!',
  'aw_confirm_delete_assignment'	=> 'Bist du sicher, dass Du die Zuweisungen %s wirklich löschen willst? Alle erhaltenen DKP werden dabei auch gelöscht!',
  
  'aw_upd_assignment_warning'	=> '<h3>Das nachträgliche editieren einer Zuweisung geschieht auf eigenem Risiko!</h3>
  									Es werden keine Benachrichtigungen an die Spieler verschickt und beim ändern der Zuweisung können unwiederrufliche Fehler auftreten.<br />
									Nutze daher diese Funktion bitte mit bedacht.',
);

?>
