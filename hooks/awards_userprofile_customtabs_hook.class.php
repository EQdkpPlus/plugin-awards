<?php

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | awards_userprofile_customtabs_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('awards_userprofile_customtabs_hook')){
  class awards_userprofile_customtabs_hook extends gen_class
  {
	/* List of dependencies */
    public static $shortcuts = array('user', 'tpl', 'game', 'config');



  /**
    * portal
    * Do the hook 'portal'
    *
    * @return array
    */
	$out = array(
		'title' => "Title of the Tab",
		'content' => "Content of the Tab",
	);


	return $out;
  }
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	registry::add_const('short_awards_userprofile_customtabs_hook', awards_userprofile_customtabs_hook::$shortcuts);
}
?>