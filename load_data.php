<?php
/**
 * Fetch live data from DB
 * @return: array (data) or string (error message)
 */

require('config.php');

$settings['data_source'] = 'DB'; 
$site = new Site($settings);

// set default
$data['valid_request'] = TRUE;
$data['notice']['invalid_request'] = "";
$data['sport'] = "";
$data['result'] = "";

// load config settings
$data['settings'] = $site->get_settings();

// check URL request if valid, see config.php
if ($site->check_request() === FALSE)
	$data['notice']['invalid_request'] = "invalid request";
else
{
	//debug_show($site->data_source);
	switch ($site->data_source)
	{
		case "API":

			$data['notice']['invalid_request'] = "API source is not allowed";
			break;

		case "DB":

			$result = $site->fetch_live_data();
			//debug_show($result);
			if ($result === FALSE)
				$data['notice']['invalid_request'] = "DB Error";
			break;
	}
}



/*
if ($site->request_value == 'all')
{
	echo "NOTREADY";
	exit;
}
*/

// check for errors
if ($data['notice']['invalid_request'] == "")
{
	$data['result'] = $site->get_active_sport($result, $site->request_value);	
}
else 
{
	echo $data['notice']['invalid_request'];
	exit();
}


//debug_show($data['result']);
//exit;

// test-case
//unset($data['result']['volleyball']);

if ($data['result'] == "NOTFOUND")
	echo "NOTFOUND";
else
	//debug_show($data['result']);
	print_r(json_encode($data['result'], true));
?>