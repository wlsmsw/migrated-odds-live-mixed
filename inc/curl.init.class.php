<?php

/**
 * @desc: communicate with the LVS API
 */
class reqAPI
{

	//var $ch,$url,$result,$status,$token,$error = array();

	/**
	 * @desc: cURL connection handler
	 */
	private $ch;

	/**
	 * @desc: the API url
	 */
	public $url;

	/**
	 * @desc: result of cURL exec
	 */
	public $result;

	/**
	 * @desc: status of cURL exec
	 */
	public $status;

	/**
	 * @desc: the access token
	 */
	private $token;

	/**
	 * @desc: error 
	 * @return: int (curl error code)
	 */
	public $error;

	/**
	 * @desc: curl error detail 
	 * @return: str (curl error detail)
	 */
	public $error_detail;

	/**
	 * @desc: token text file
	 */
	private $file_token;

	/**
	 * @desc: the log file
	 */
	private $log_file;


	function __construct($settings)
	{
		//echo "im called!";
		//debug_show($settings);
		$this->file_token = $settings['file_token'];
		$this->log_file   = $settings['log_file'];
	}


	function reqToken() 
	{
		try 
		{
			//throw $err = new customException("damn error");

			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $this->restURL .'/acc/token');
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt ($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
			curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt ($ch, CURLOPT_HEADER, true);
			curl_setopt ($ch, CURLOPT_NOBODY,  false);
			$this->result = curl_exec($ch);

			if (curl_exec($ch) === false)
			{
				throw $err = new customException("cURL error: " . curl_error($ch));
			}


			$_res = explode("\n", str_replace("\r","\n",$this->result));
			foreach($_res as $line) 
			{
				if (stristr($line,'X-LVS-HSToken')) 
				{
					$_tokenAccess = str_replace('X-LVS-HSToken: ','',$line);
					break;
				}
			}

		
			$filename = $this->file_token;
			if (file_put_contents($filename, $_tokenAccess) === FALSE)
			{
				throw $err = new customException("access token file put content error.");
			}

			$this->token = $_tokenAccess;

		}	
		catch (customException $e) 
		{
			$err->debug_notifier($e->errorMessage());
		}

		curl_close($ch);
		return $_tokenAccess;
	}


	function getToken()
	{
		try 
		{
			$file = file_get_contents($this->file_token);
			if ($file === FALSE)
				throw $err = new customException("error: token cannot get file content. ");
			else
				$this->token = $file;
		}	
		catch (customException $e) 
		{
			$err->debug_notifier($e->errorMessage());
		}

		return $file;
	}


	function get_curl_result($url='', $method = 'GET', $_login=FALSE)
	{
		timer_start();

		$this->url = !empty($url) ? $url : $this->url;
		//$this->url = 'https://bo.lvsmsw.prd/abp/rest/marketTypes?code=BASK';

		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);

		
		if($_login == true || isset($_GET['token'])) 
		{
			$this->reqToken();
			exit;
		}
		

		$this->getToken();

		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
			'Content-type: application/json',
			'X-LVS-HSToken: '. $this->token
		));

		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT ,0); 
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($this->ch, CURLOPT_HEADER, 0);
		curl_setopt($this->ch, CURLOPT_NOBODY,  false);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST ,$method);

		try 
		{
			$this->result = curl_exec($this->ch);
			if ($this->result === FALSE)
			{
				throw $err = new customException("error: curl exec failed. ");
				return FALSE;
			}
		}	
		catch (customException $e) 
		{
			$err->debug_notifier($e->errorMessage());
		}
		
		
		$this->status = $this->status($this->url);
		curl_close($this->ch);
		timer_stop();
	}


	protected function status($url)
	{
		$stat = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		if($stat == 200 || $stat == 1) 
		{
			return true;
		}
		else
		{
			$this->error = array_merge(array('status'=> $stat));
			$http_codes = parse_ini_file("curl_status_code.ini");
			//debug_show("url: " . $url ."<br/>");
			//$this->error_detail = $http_codes[$this->error['status']];
			$this->error_detail = "cURL error: " . $this->error['status'] . " - " . $http_codes[$this->error['status']];
			log_message($this->log_file, $this->error_detail);
			//debug_show($error_message);
			return false;
		}
	}

} // end class





function timer_start() 
{
	global $timestart;
	
	$timestart = microtime( true );

	return true;
}


function timer_stop( $display = 0, $precision = 3 ) 
{
	global $timestart, $timeend;
	
	$timeend = microtime( true );
	$timetotal = $timeend - $timestart;

	$r = number_format( $timetotal, $precision );
	
	if ($display)
		echo "time: ".$r." <br />";

	return $r;
}

?>