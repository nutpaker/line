<?php

require_once( dirname(__FILE__).'/../../php/cache.php');
require_once( dirname(__FILE__).'/../../php/util.php');
require_once( dirname(__FILE__).'/../../php/settings.php');
require_once( dirname(__FILE__).'/../../php/Snoopy.class.inc');
eval(FileUtil::getPluginConf('line'));

class rLine
{
	public $hash = "line.dat";
	public $modified = false;
	public $log = array
	( 
		"line_enabled"=>0, 
		"line_addition"=>0, 
		"line_finish"=>0, 
		"line_deletion"=>0,
		"line_token"=>'',
	);

	static public function load()
	{
		$cache = new rCache();
		$ar = new rLine();
		if($cache->get($ar))
		{
			if(!array_key_exists("line_enabled",$ar->log))
			{
				$ar->log["line_enabled"] = 0;
				$ar->log["line_addition"] = 0;
				$ar->log["line_finish"] = 0;
				$ar->log["line_deletion"] = 0;
				$ar->log["line_token"] = '';
			}
		}
		return($ar);
	}
	public function store()
	{
		$cache = new rCache();
		return($cache->set($this));
	}
    
	public function set()
	{
		if(!isset($HTTP_RAW_POST_DATA))
			$HTTP_RAW_POST_DATA = file_get_contents("php://input");
		if(isset($HTTP_RAW_POST_DATA))
		{
			$vars = explode('&', $HTTP_RAW_POST_DATA);
			foreach($vars as $var)
			{
				$parts = explode("=",$var);
				$this->log[$parts[0]] = ($parts[0]=='line_token') ? $parts[1] : intval($parts[1]);
  	                }
			$this->store();
			$this->setHandlers();
		}
	}
	public function get()
	{
		return("theWebUI.line = ".JSON::safeEncode($this->log).";");
	}
	public function setHandlers()
	{
		global $rootPath;
		if($this->log["addition"] || ($this->log["line_enabled"] && $this->log["line_addition"]))
		{
			$addCmd = getCmd('execute.nothrow.bg').'={'.Utility::getPHP().','.$rootPath.'/plugins/line/update.php'.',1,$'.
				getCmd('d.get_name').'=,$'.getCmd('d.get_size_bytes').'=,$'.getCmd('d.get_bytes_done').'=,$'.
				getCmd('d.get_up_total').'=,$'.getCmd('d.get_ratio').'=,$'.getCmd('d.get_creation_date').'=,$'.
				getCmd('d.get_custom').'=addtime,$'.getCmd('d.get_custom').'=seedingtime'.
				',"$'.getCmd('t.multicall').'=$'.getCmd('d.get_hash').'=,'.getCmd('t.get_url').'=,'.getCmd('cat').'=#",$'.
				getCmd('d.get_custom1')."=,$".getCmd('d.get_custom')."=x-line,".
				User::getUser().'}';
		}				
		else
			$addCmd = getCmd('cat=');
		if($this->log["finish"] || ($this->log["line_enabled"] && $this->log["line_finish"]))
			$finCmd = getCmd('execute.nothrow.bg').'={'.Utility::getPHP().','.$rootPath.'/plugins/line/update.php'.',2,$'.
				getCmd('d.get_name').'=,$'.getCmd('d.get_size_bytes').'=,$'.getCmd('d.get_bytes_done').'=,$'.
				getCmd('d.get_up_total').'=,$'.getCmd('d.get_ratio').'=,$'.getCmd('d.get_creation_date').'=,$'.
				getCmd('d.get_custom').'=addtime,$'.getCmd('d.get_custom').'=seedingtime'.
				',"$'.getCmd('t.multicall').'=$'.getCmd('d.get_hash').'=,'.getCmd('t.get_url').'=,'.getCmd('cat').'=#",$'.
				getCmd('d.get_custom1')."=,$".getCmd('d.get_custom')."=x-line,".
				User::getUser().'}';
		else
			$finCmd = getCmd('cat=');
		if($this->log["deletion"] || ($this->log["line_enabled"] && $this->log["line_deletion"]))
			$delCmd = getCmd('execute.nothrow.bg').'={'.Utility::getPHP().','.$rootPath.'/plugins/line/update.php'.',3,$'.
				getCmd('d.get_name').'=,$'.getCmd('d.get_size_bytes').'=,$'.getCmd('d.get_bytes_done').'=,$'.
				getCmd('d.get_up_total').'=,$'.getCmd('d.get_ratio').'=,$'.getCmd('d.get_creation_date').'=,$'.
				getCmd('d.get_custom').'=addtime,$'.getCmd('d.get_custom').'=seedingtime'.
				',"$'.getCmd('t.multicall').'=$'.getCmd('d.get_hash').'=,'.getCmd('t.get_url').'=,'.getCmd('cat').'=#",$'.
				getCmd('d.get_custom1')."=,$".getCmd('d.get_custom')."=x-line,".
				User::getUser().'}';
		else
			$delCmd = getCmd('cat=');
		$req = new rXMLRPCRequest( array(
			rTorrentSettings::get()->getOnInsertCommand( array('tline'.User::getUser(), $addCmd ) ),
			rTorrentSettings::get()->getOnFinishedCommand( array('tline'.User::getUser(), $finCmd ) ),
			rTorrentSettings::get()->getOnEraseCommand( array('tline'.User::getUser(), $delCmd ) ),
			));
		return($req->success());
	}

	static protected function bytes( $bt )
	{
		$a = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
		$ndx = 0;
		if($bt == 0)
			$ndx = 1;
		else
		{
			if($bt < 1024)
			{
				$bt = $bt / 1024;
				$ndx = 1;
			}
			else
			{
				while($bt >= 1024)
				{
       		    			$bt = $bt / 1024;
      					$ndx++;
	         		}
			}
		}
		return((floor($bt*10)/10)." ".$a[$ndx]);
	}

	public function lineNotify( $data )
	{
		global $lineNotifications, $lineEndpoint;

		$actions = array
		(
			1 => 'addition', 
			2 => 'finish', 
			3 => 'deletion',
		);
		$section = $lineNotifications[$actions[$data['action']]];

		$fields = array
		(
			'{name}', '{label}', '{size}', '{downloaded}', '{uploaded}', '{ratio}',
			'{creation}', '{added}', '{finished}', '{tracker}',
		);


		$values = array
		(
			$data['name'], 
			$data['label'], 
			self::bytes($data['size']), 
			self::bytes($data['downloaded']), 
			self::bytes($data['uploaded']), 
			$data['ratio'],
			strftime('%c',$data['creation']), 
			strftime('%c',$data['added']),
			strftime('%c',$data['finished']),
			$data['tracker'],
		);
		
		$message = str_replace( $fields, $values, $section['message'] );
		
//		https://developers.line.biz/flex-simulator/?status=success


	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	date_default_timezone_set("Asia/Bangkok");
	$sMessage = "มีรายการสั่งซื้อเข้าจ้า....";
	$chOne = curl_init(); 
	curl_setopt( $chOne, CURLOPT_URL, $lineEndpoint); 
	curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0); 
	curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0); 
	curl_setopt( $chOne, CURLOPT_POST, 1); 
	curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=".$message); 
	$headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$this->log["line_token"].'', );
	curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 
	curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1); 
	$result = curl_exec( $chOne ); 
	curl_close( $chOne );   

	}
}
