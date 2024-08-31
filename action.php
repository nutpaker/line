<?php
//require_once( 'line.php' );
require_once( dirname(__FILE__).'/../../plugins/history/history.php');
require_once( dirname(__FILE__).'/../../plugins/line/line.php');

if(isset($_REQUEST['cmd']))
{
	$cmd = $_REQUEST['cmd'];
	switch($cmd)
	{
		case "set":
		{
			$up = rLine::load();
			$up->set();
			CachedEcho::send($up->get(),"application/javascript");
			break;
		}
		case "get":
		{
			$up = rHistoryData::load();
			CachedEcho::send(JSON::safeEncode($up->get($_REQUEST['mark'])),"application/json");
  	                break;
		}
		case "delete":
		{
			$up = rHistoryData::load();
			$hashes = array();
			if(!isset($HTTP_RAW_POST_DATA))
				$HTTP_RAW_POST_DATA = file_get_contents("php://input");
			if(isset($HTTP_RAW_POST_DATA))
			{
				$vars = explode('&', $HTTP_RAW_POST_DATA);
				foreach($vars as $var)
				{
					$parts = explode("=",$var);
					$hashes[] = $parts[1];
  	                	}
				$up->delete( $hashes );
			}
			CachedEcho::send(JSON::safeEncode($up->get(0)),"application/json");
  	                break;
		}
	}
}
