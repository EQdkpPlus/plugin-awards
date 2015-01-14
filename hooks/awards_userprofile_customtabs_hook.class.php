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
	public function userprofile_customtabs(){
		
		#if($this->user->check_auths(array('u_awards_view', 'a_awards_manage'), 'OR', false)){}
		
		
			$out = array(
				'title'   => "Erfolge",
				'content' => "Mein Content",		#$this->routing->build('list_user_achievements');
			);
		
		
			return $out;
		
		
	}
	
	
  } //end class
} //end if class not exists
?>