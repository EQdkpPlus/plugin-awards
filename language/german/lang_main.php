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
  'awards_short_desc'			=> 'Dieses Plugin ermöglicht es Erfolge zu erhalten',
  'awards_long_desc'			=> 'Dieses Plugin ermöglicht es Erfolge zu erhalten.',
  
  // General
  'aw_manage_achievements'		=> 'Erfolge verwalten',
  'aw_manage_assignments'		=> 'Zuweisungen verwalten',
  
  'aw_add_achievement'			=> 'Erfolg hinzufügen',
  'aw_edit_achievement'			=> 'Erfolg editieren',
  'aw_add_assignment'			=> 'Erfolg zuweisen',
  'aw_edit_assignment'			=> 'Zuweisung editieren',
  'aw_icon_header'				=> 'Icon auswählen',
  'aw_upload_icon'				=> 'Icon hinzufügen',
  'aw_special'					=> 'Spezial',
  'aw_auto_assign'				=> 'Automatisch zuweisen',
  'aw_dkp_value'				=> 'DKP Wert',
  'aw_multidkp'					=> 'Multidkp-Konten',
  'aw_sortation'				=> 'Sortierung',
  'aw_points'					=> 'Punkte',
  'aw_icon_colors'				=> 'Icon Farben',
  'aw_module'					=> 'Modul',
  'aw_dkp'						=> 'DKP',
  'aw_event_id'					=> 'Ereignis ID',
  'aw_achievement'				=> 'Erfolg',
  
  'aw_listachiev_footcount'		=> '... %1\$d Erfolg(e) gefunden / %2\$d pro Seite',
  'aw_listassign_footcount'		=> '... %1\$d Zuweisung(en) gefunden / %2\$d pro Seite',
  
  
  'aw_adj_id'					=> 'Korrektur ID',
  'aw_adj_gk'					=> 'Korrektur Group Key',
  
  
  // Adjustment Modules
  'aw_cron_module_0'			=> 'Kein Modul ausgewählt',
  'aw_cron_module_1'			=> 'Modul 1 nicht verfügbar',
  'aw_cron_module_2'			=> 'Modul 2 nicht verfügbar',
  
  // System Messages
  'action_achievement_deleted'	=> 'Erfolg gelöscht',
  'action_achievement_added'	=> 'Erfolg erstellt',
  'action_achievement_updated'	=> 'Erfolg aktualisiert',
  
  'aw_plugin_not_installed'		=> 'Das Awards-Plugin ist nicht installiert.',
  'aw_add_success'				=> '%s wurde hinzugefügt',
  'aw_add_nosuccess'			=> '%s konnte nicht hinzugefügt werden',
  'aw_assign_success'			=> '%s wurde %s zugewiesen',
  'aw_assign_nosuccess'			=> '%s konnte nicht zugewiesen werden',
  'aw_del_assign'				=> 'Zuweisung(en) entfernt',
  
  'aw_confirm_delete_achievement'	=> 'Bist du sicher, dass Du die Erfolge %s wirklich löschen willst? Alle erhaltenen DKP werden dabei auch gelöscht!',
  

);

?>
