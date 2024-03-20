<?php
/*
Description: The main class of the site
Author: MSW WebDev
Version: 1.0
*/

class Site extends Config 
{

	public $request;
	public $valid_request;
	public $query;
	public $request_value;

	/*
	 * @desc: set all site configurations
	 */
	function __construct($settings) 
 	{
      	$this->set_settings($settings);
   	}

   	/*
   	 * @desc: loads the the template
   	 */
	function load($tpl, $data) 
	{
		include_once('tpl/' . $tpl);
	}

	/*
   	 * @desc: render the template
   	 */
	function render($tpl) 
	{
		return file_get_contents('tpl/' . $tpl);
	}


	/*
	 * @dec: get query string from URL
	 */
	function get_url()
	{
		if (isset($_SERVER['QUERY_STRING']))
			return $this->request = $_SERVER['QUERY_STRING'];
		else return $this->request = "";
	}


	/*
	 * @desc: checks the query string if valid on the config file
	 */
	function check_request()
	{
		parse_str($this->get_url(), $queries);
		$this->query = $queries;
		$action_keys = array_keys($queries);

		foreach ($action_keys as $key)
		{
			$valid = in_array($key, $this->get_valid_request());
			if ($valid === FALSE) return false;
		}

		$this->get_request_value();
		return true;
	}

	/**
	 * @desc: get parameter
	 */
	function get_request_value()
	{		
		//debug_show($this->query);
		//$this->request_value = (empty($this->query)) ? "all" : array_values($this->query)[0];
		$this->request_value = "all";
	}


	/**
	 * @desc: fetch records from database
	 * @return: FALSE if failed; array if success
	 */
	function fetch_live_data()
	{
		$this->connect_db();

		//$sql = "SELECT sports_code, json_data, datetime_added FROM live_sports";
		//exclude e-gaming
		$sql = "SELECT sports_code, json_data, datetime_added FROM live_sports WHERE sports_code NOT IN ('COUN', 'LEAG', 'DOTA')";
		$live_row_data = array();

		try 
		{
			if ($result = $this->db_conn->query($sql)) 
			{
			    while ($obj = $result->fetch_object()) 
			    {
					$live_row_data[$obj->sports_code] = json_decode($obj->json_data, true);
			    }

			    $result->close();
			    //debug_show($live_row_data);
			    return $live_row_data;
			}
			else
			{
				throw $err = new customException("error: mysql error <br />sql: " . $sql);
				return FALSE;	
			}
		}	
		catch (customException $e) 
		{
			$err->debug_notifier($e->errorMessage(), $this->environment);
		}
	}


	/**
	 * @desc: update records in the database
	 * @return: FALSE if failed; TRUE if success
	 */
    function update_live_data($array)
    {
    	$this->connect_db();

    	if ($this->check_sports_code($array) === FALSE)
 		{
 			return FALSE;
 		}

    	/* // this is for manual insertion of sports code
    	foreach ($array as $k => $v)
    	{
    		$sports_code = $k;
    		$time_now 	= date("Y-m-d h:i:s");
    		$sql = "insert into live_sports (sports_code, json_data, datetime_added) values ('{$sports_code}', '', '{$time_now}')";
    		$this->db_conn->query($sql);
    	}
    	return;
		*/

		//debug_show($array);

		foreach ($array as $key => $val)
		{
			$sport_code = $key;
			$json_data 	= addslashes(json_encode($val));
			$time_now 	= date("Y-m-d h:i:s");

			$sql = "UPDATE live_sports SET json_data = '{$json_data}', datetime_added = '{$time_now}' where sports_code = '{$sport_code}' ";
			
			//debug_show("-->".$sql);					
			try 
			{
				if ($this->db_conn->query($sql) === FALSE)
				{
					throw $err = new customException("error: mysql error <br />sql: " . $sql);
					return FALSE;
				}
			}	
			catch (customException $e) 
			{
				$err->debug_notifier($e->errorMessage(), $this->environment);
			}

		}  

		return TRUE;
    }

    /**
     * @desc: add new sports code
     */
    function check_sports_code($array)
    {
    	$live_sport_code = array_keys($array);
    	$config_sport_code = array_keys($this->all_sports_config());
    	$result = array_diff($live_sport_code, $config_sport_code);

    	// insert new sports code in the db
    	if (count($result) > 0)
    	{
    		$time_now 	= date("Y-m-d h:i:s");
	    	foreach ($result as $key => $value) 
	    	{
	    		try 
				{
					$sql = "insert into live_sports (sports_code, json_data, datetime_added) values ('{$value}', '', '{$time_now}')";
					if ($this->db_conn->query($sql) === FALSE)
					{
						throw $err = new customException("error: mysql error <br />sql: " . $sql);
						return FALSE;
					}
				}	
				catch (customException $e) 
				{
					$err->debug_notifier($e->errorMessage(), $this->environment);
				}

	    	}
    	}
    	return TRUE;
    }


    // for manual override
    function save_all_sports()
    {
    	$this->connect_db();
    	$array = $this->json_sports_config();

    	foreach ($array as $k => $v)
    	{
    		$sports_code = $k;
    		$time_now 	= date("Y-m-d h:i:s");
    		$sql = "insert into live_sports (sports_code, json_data, datetime_added) values ('{$sports_code}', '', '{$time_now}')";
    		$this->db_conn->query($sql);
    	}
    	return;	
    }


    /**
     * determine which sport is active and collect the ids
     * @return: array
     */
    function get_active_sport($result, $sport)
    {
    	//debug_show($sport);
    	//exit();

    	$arr = array();
    	$ids['sports'] 	= array();
    	$ids['leagues'] = array();
    	$ids['match'] 	= array();

    	$requested_sport = strtolower($sport);
    	$temp_array = array();

    	foreach ($result as $sport => $parent)
    	{
    		if (empty($parent) === FALSE) 
    		{
	    		$key_id = key($parent);
	    		
	    		if (isset($parent[$key_id]['sport']))
	    			$sport_name = strtolower($parent[$key_id]['sport']);
	    		else
	    			{
	    				echo "sport name can't be found.";
	    				exit();
	    			}	

	    		if ($sport_name != "soccer")
	    		{	
		    		// set 6 matches per page
		    		$batch = array(6, 12, 18, 24, 30);
		    		$ctr = 1;
		    		$n = 1;
		    		foreach ($parent as $key => $row)
		    		{
		    			if (isset($row['odds_id']))
		    			{
			    			// remove odds id key
			    			$odds_info = $row[$row['odds_id']];
			    			unset($row[$row['odds_id']]);
			    			$row['odds_details'] = $odds_info;
		    			}


		    			// combine same league
		    			$my_country_league = $row['country_league'].$n;
		    			//$arr[$sport_name][$my_country_league][$key] = $row; 
		    			$arr[$sport_name . ' - ' . $my_country_league][$key] = $row; 


		    			if ($requested_sport == 'all' || $sport_name == $requested_sport)
		    			{
		    				// collect all id's
		    				array_push($ids['leagues'], $my_country_league);
		    				array_push($ids['match'], $key);			
		    			}

		    			if (in_array($ctr, $batch))
		    			{
		    				$n++;
		    			}
		    			$ctr++;
		    		}
		    	}


	    		if ($requested_sport == 'all' || $sport_name == $requested_sport)
	    		{
	    			// collect all the id's
	    			array_push($ids['sports'], clean_str($sport_name));
	    		}
    		}
    	}


    	$requestedsport = str_replace(' ', '', $requested_sport);

    	if ($requested_sport != 'all')
	    {
	    	if (array_key_exists($requested_sport, $arr))
	    	{
	    		$temp_array = $arr[$requested_sport];
	    		unset($arr);
	    		$arr[$requested_sport] = $temp_array;
	    	}
	    	else if (array_key_exists($requestedsport, $this->json_sports_config()))
	    	{
	    		unset($arr);
	    		$arr[$requested_sport] = "NOTACTIVE";
	    	}
	    	else
	    	{
	    		return "NOTFOUND";	
	    	}
	    }

		$ids['date'] = date("h:i:s");
    	$arr['myids'] = $ids;
    	//debug_show($arr);
    	return $arr;
    }


	/**
	 * @desc: test catch reference
	 */	
	function test_catch()
	{
		try 
		{
			//throw error
			if (1 != 2) 
			throw $err = new customException("the error details");
		}	
		catch (customException $e) 
		{
			$err->debug_notifier($e->errorMessage());
		}
	}


} // end site class..

?>