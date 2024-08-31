<?php

//require_once( 'line.php' );
require_once( '../plugins/line/line.php');

$mngr = rLine::load();
if($mngr->setHandlers())
{
	$theSettings->registerPlugin($plugin["name"],$pInfo["perms"]);
	$jResult .= $mngr->get();
}
else
	$jResult .= "plugin.disable(); noty('line: '+theUILang.pluginCantStart,'error');";
