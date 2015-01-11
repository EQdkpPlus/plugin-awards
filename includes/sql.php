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

$awardsSQL = array(

	'uninstall' => array(
		1     => 'DROP TABLE IF EXISTS `__awards_achievements`',
		2     => 'DROP TABLE IF EXISTS `__awards_assignments`',
	),

	'install'   => array(
		1 => "CREATE TABLE `__awards_achievements` (
			`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` TEXT COLLATE utf8_bin NOT NULL,
			`description` TEXT COLLATE utf8_bin NULL,
			`sort_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
			`active` TINYINT(3) UNSIGNED NULL DEFAULT '0',
			`special` TINYINT(3) UNSIGNED NULL DEFAULT '0',
			`points` INT(10) NULL DEFAULT '0',
			`icon` TEXT NULL COLLATE 'utf8_bin',
			`icon_colors` TEXT NULL COLLATE 'utf8_bin',
			`module` TEXT NULL COLLATE 'utf8_bin',
			`dkp` INT(10) NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		)
		DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
		",
		2 => "CREATE TABLE `__awards_assignments` (
			`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`date` INT(10) NULL DEFAULT '0',
			`user_id` INT(10) UNSIGNED NULL DEFAULT '0',
			`achievement_id` INT(10) UNSIGNED NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		)
		DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
		",
		)
	);

?>