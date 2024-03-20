<?php

/**
 * @desc: Helper Classes
 */

// custom Exception class
class customException extends Exception 
{
	public function errorMessage() 
	{
	    $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
	    .': '.$this->getMessage();
	    return $errorMsg;
	}


	function debug_notifier($message, $environment = "ALPHA")
	{
		if ($environment == "LIVE")
		{
			$server_name = (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : " live odds";

			$subject 	= "Subject: Bug Notice - " . $_SERVER['SERVER_NAME'];	
			$headers   	= array();
			$headers[] 	= "MIME-Version: 1.0";
			$headers[] 	= "Content-type: text/plain; charset=iso-8859-1";
			$headers[] 	= "From: MSW Debug Mailer";
			$headers[] 	= $subject;

			mail($this->developer_email, $subject, $message, implode("\r\n", $headers));

			// add log
			log_message($settings['log_file'], $message);
		}
		else
		{
			echo "<pre>".$message."</pre>";
		}
	}
}


function sample_usage() 
{
	$str = "
			try 
			{
				if ($this->db_conn->query($sql) === FALSE)
					throw $err = new customException(\"error: mysql error <br />sql: \" . $sql);
					return FALSE;
			}	
			catch (customException $e) 
			{
				$err->debug_notifier($e->errorMessage());
			}

			";

	return $str;
}


/**
 * log events
 * @param: $message
 * @return: none
 */
function log_message($log_file, $message)
{
	$log  = file_get_contents($log_file);
	$log .= $message . "\n";
	try 
	{
		if (file_put_contents($log_file, $log) === FALSE)
			throw $err = new customException("logging error");
		return FALSE;
	}	
	catch (customException $e) 
	{
		$err->debug_notifier($e->errorMessage());
	}
}

/**
 * return current date and time
 * @param: none
 * @return: str
 */
function datetime_now()
{
	return date("M-d-y h:i:s");
}

/**
 * return matching score for Tennis
 */
function set_tennis_score($score)
{
	$tennis_score = array('1' => '15',
						  '2' => '30',
						  '3' => '40',
						  '4' => 'A');
	if (array_key_exists($score, $tennis_score))
		return $tennis_score[$score];
	else
		return $score;
}


/**
 * remove non alpha-numeric char
 */
function clean_str($str)
{
	return preg_replace("/[^A-Za-z0-9]/", '', $str);
}


function debug_show($data, $mode="p")
{
	echo "<pre>";
	if ($mode == "v")
		var_dump($data);
	else if ($mode == "p")
		print_r($data);
	else echo $data;
	echo "</pre>";
	echo "<br />---------------------------------------<br />";
}


function debugr_show($data)
{
	echo "<pre>";
	print_r($data);
	echo "</pre>";	
}







?>