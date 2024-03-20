<?php
/**
 * get data from DB *only*
 * @return: load html template
 * 
 */

require('config.php');

$settings['data_source'] = 'DB'; 
$site = new Site($settings);
// load config settings
$data['settings'] = $site->get_settings();


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="refresh" content="1800">

    <meta name="description" content="">
    <meta name="author" content="">
	
	<title>MSW LIVE Odds ALL sports</title>
	<link href="tpl/css/style.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="tpl/js/jquery.smarticker.min.js"></script>
    <script type="text/javascript" src="tpl/js/jquery.easy-ticker.js"></script>
    <script type="text/javascript" src="tpl/js/newsticker.functions.js"></script>
    <script type="text/javascript" src="tpl/js/functions.js"></script>
    <script type="text/javascript" src="tpl/js/jquery.jclock.js"></script>
	<script type="text/javascript" src="tpl/js/front-end.js"></script>
  </head>

  <body style="margin: 0;">

  	<!-- Header -->
    <div class="header">
	  <div class="header-container">
	    <div class="header-main">
	        <div class="logo">
	            <img src="tpl/img/msw-logo.png">
	        </div>
	        
	        <div class="date">
                <span><?=date('m/d/Y')?></span>
                <div id="clock-ticker">	<span class="clock-digit"> 00:00:00 PM</span>
				</div>	
	        </div>
	    </div>
	    
        <div class="header-inner">
            <div class="title">
            	<marquee direction="left">
					<span>LIVE</span> ODDS&nbsp;&nbsp;-&nbsp;&nbsp;MATCHES HAPPENING NOW!
				</marquee>
                
            </div>
        </div>
	  </div>
	</div>

    <div style="width: 100%; height: 99vh; border: none;">

    	<?php
	    	if ($site->check_request() === FALSE)
				$site->load('odds-invalid-request.tpl.php', $data);
			else
			{
				// declarations
				$data = array();
				$data['environment'] = $site->environment;
				$data['valid_request'] = TRUE;
				$data['notice']['invalid_request'] = "";
				$data['sport'] = (!empty($site->request_value)) ? "?sport=" . $site->request_value : ""; 
				$data['result'] = "";
				$data['date_time'] = date('M d, Y h:i:s A');
				// load our template
				$site->load('odds-live.tpl.php', $data);
			}

    	?>

    </div>

  </body>

</html>
