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

	$id		 = '6';
	$title 	 = 'Test';

	$content = '<p>Test Inhalt</p>';




	$this->tpl->assign_block_vars('custom_tabs', array(
		'ID'		=> $id,
		'NAME'		=> $title,
		'CONTENT'	=> $content,
	));

  }
}
?>