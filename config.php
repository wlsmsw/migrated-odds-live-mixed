<?php
/*
Description: all site configuration goes in here
Author: MSW WebDev
Version: 1.0
Date Created: September 15, 2015 
*/

// !IMPORTANT ~ put all site settings here
$settings = array(
            'alpha_url'          => "localhost",
            'staging_url'        => "mswsites.com/mswodds/mixed-live/",
            'live_url'           => "mswlive.com/mswodds/mixed-live/",
            'version'            => "1.0",
            'data_source'        => "API", // DB or API
            'db'                 => array(
                                       // host, username, password, dbname
                                       'alpha'     => array('localhost', 'root', '', 'msw_live'), //setup1 @home
                                       'staging'   => array('localhost','api_mswdev','nff3Y+No2]^}','api_live_sports'),
                                       'live'      => array('localhost','mswliv5_api_mswdev','JHTh4?b(W(EF','mswliv5_api_live_sports'),
                                       'lvs'       => array('localhost','lvsodds_liveuser','rMsv22Z7fN','lvsodds_liveodds'),
                                    ),
            'developer_email'    => "itprojectsdevelopment@megasportsworld.com",
            'timezone'           => "Asia/Manila",
            'valid_request'      => array('sport'), // set valid request here
            'file_token'         => "token.txt", // the token text file
            'file_sports'        => "sports.json.txt",
            'rest_url'           => "https://slipstream.msw.ph/xapi/m",
            'rest_url_ext'       => "/live?lineId=2&originId=3",
            'log_file'           => "logs/events.log.txt"
            );

$var = $GLOBALS["_SERVER"];

switch($var['SERVER_NAME']){
    case 'mswlive.com':
        $settings['environment'] = 'live';
        break;
    case 'mswsites.com':
        $settings['environment'] = 'staging';
        break;
    default:
        $settings['environment'] = 'lvs';
}


// require all needed files
require('inc/helper.class.php');
require('inc/curl.init.class.php');
require('inc/liveapi.class.php');
require('controller/main.class.php');

class Config 
{

    public  $environment;
    public  $version; 
    public  $developer_email;
    private $valid_request;
    public  $data_source;
 
    private $db_host;
    private $db_username;
    private $db_password;
    private $db_name;

    public  $db_conn;
    private $log_file;


 	 function __construct() 
 	 {
   }


   function set_settings($settings)
   {
      $this->environment      = $settings['environment'];
      $this->version          = $settings['version'];
      $this->developer_email  = $settings['developer_email'];
      $this->alpha_url        = $settings['alpha_url'];
      $this->staging_url      = $settings['staging_url'];
      $this->live_url         = $settings['live_url'];

      $this->valid_request    = $settings['valid_request'];
      $this->data_source      = $settings['data_source'];

      $this->db_config        = $settings['db'];
      $this->log_file         = $settings['log_file'];

   	  $this->set_error_reporting();
      date_default_timezone_set($settings['timezone']);      
   }


   // get settings
   function get_settings()
   {
      return array(
                  'environment'  => $this->environment,
                  'version'      => $this->version,
               );
   }


   // get valid URL request
   function get_valid_request()
   {
      return $this->valid_request;
   }


   // set error reporting
   function set_error_reporting()
   {
      switch ($this->environment) 
   	  {
   		   case "alpha":
   			  error_reporting(-1); // display all errors
   			  break;
   		
         case "staging":
				  error_reporting(-1);
   			  break;
   		
         case "live":
          if ($_SERVER['SERVER_NAME'] == $this->live_url)
   			    error_reporting(0); // don't display any errors
          else
            error_reporting(-1);
          
   			break;
   	  }
   }


 

   function connect_db()
   {
        
      $this->db_host       = $this->db_config[$this->environment][0];
      $this->db_username   = $this->db_config[$this->environment][1];
      $this->db_password   = $this->db_config[$this->environment][2];
      $this->db_name       = $this->db_config[$this->environment][3];

      try 
      {
         $this->db_conn = new mysqli('p:'.$this->db_host, $this->db_username, $this->db_password, $this->db_name);
         if (mysqli_connect_errno())
         {
            throw $err = new customException("error: mysql connect failed. ");
            return FALSE;
         }

      }  
      catch (customException $e) 
      {
         $err->debug_notifier($e->errorMessage(), $this->environment);
      }

   }


   /**
    * @desc: check all codes saved in the db
    */
   function all_sports_config()
   {
      $this->connect_db();
      $scodes = array();

      $sql = "select sports_code from live_sports";
      try 
      {
         if ($result = $this->db_conn->query($sql)) 
         {
            while ($obj = $result->fetch_object()) 
            {
               $scodes[$obj->sports_code] = array();
            }

            $result->close();
            return $scodes;
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
      return;

   } // ..end get sports



   function json_sports_config()
   {
    // sports ib json format
      $_sportsAll = '[ {

           "code" : "FOOT",

           "description" : "Football",

           "defaultEventPathId" : 240

         }, {

           "code" : "FOOT",

           "description" : "Soccer",

           "defaultEventPathId" : 240

         }, {

           "code" : "HORS",

           "description" : "Horse Racing",

           "defaultEventPathId" : 364

         }, {

           "code" : "MOSP",

           "description" : "Motor Sports",

           "defaultEventPathId" : 22881

         }, {

           "code" : "USHO",

           "description" : "US Horse Racing",

           "defaultEventPathId" : 365

         }, {

           "code" : "USGR",

           "description" : "US Greyhound Racing",

           "defaultEventPathId" : 366

         }, {

           "code" : "GOLF",

           "description" : "Golf",

           "defaultEventPathId" : 237

         }, {

           "code" : "TENN",

           "description" : "Tennis",

           "defaultEventPathId" : 239

         }, {

           "code" : "AMFB",

           "description" : "American Football",

           "defaultEventPathId" : 1

         }, {

           "code" : "CRIC",

           "description" : "Cricket",

           "defaultEventPathId" : 215

         }, {

           "code" : "MISC",

           "description" : "Misc",

           "defaultEventPathId" : 22883

         }, {

           "code" : "RUGL",

           "description" : "Rugby League",

           "defaultEventPathId" : 22878

         }, {

           "code" : "SNOO",

           "description" : "Snooker",

           "defaultEventPathId" : 22884

         }, {

           "code" : "BOXI",

           "description" : "Boxing",

           "defaultEventPathId" : 238

         }, {

           "code" : "BOXI",

           "description" : "MMA/Boxing",

           "defaultEventPathId" : 238

         }, {

           "code" : "BASE",

           "description" : "Baseball",

           "defaultEventPathId" : 226

         }, {

           "code" : "GREY",

           "description" : "Greyhound Racing",

           "defaultEventPathId" : 1000

         }, {

           "code" : "BASK",

           "description" : "Basketball",

           "defaultEventPathId" : 227

         }, {

           "code" : "RUGU",

           "description" : "Rugby Union",

           "defaultEventPathId" : 22877

         }, {

           "code" : "ATHL",

           "description" : "Athletics",

           "defaultEventPathId" : 22885

         }, {

           "code" : "IHUS",

           "description" : "Ice Hockey - US",

           "defaultEventPathId" : 228

         }, {

           "code" : "DART",

           "description" : "Darts",

           "defaultEventPathId" : 22886

         }, {

           "code" : "POKE",

           "description" : "Poker",

           "defaultEventPathId" : 22887

         }, {

           "code" : "POLI",

           "description" : "Politics",

           "defaultEventPathId" : 22888

         }, {

           "code" : "ENTE",

           "description" : "Entertainment",

           "defaultEventPathId" : 22889

         }, {

           "code" : "SPEE",

           "description" : "Speedway",

           "defaultEventPathId" : 22890

         }, {

           "code" : "CAFB",

           "description" : "Canadian Football",

           "defaultEventPathId" : 231

         }, {

           "code" : "YACH",

           "description" : "Yachting",

           "defaultEventPathId" : 900

         }, {

           "code" : "FORM",

           "description" : "Formula 1",

           "defaultEventPathId" : 1300

         }, {

           "code" : "HAND",

           "description" : "Handball",

           "defaultEventPathId" : 1100

         }, {

           "code" : "BEVO",

           "description" : "Beach Volleyball",

           "defaultEventPathId" : 1250

         }, {

           "code" : "VOLL",

           "description" : "Volleyball",

           "defaultEventPathId" : 1200

         }, {

           "code" : "WATE",

           "description" : "Water Polo",

           "defaultEventPathId" : 1400

         }, {

           "code" : "BAND",

           "description" : "Bandy",

           "defaultEventPathId" : 1700

         }, {

           "code" : "CHES",

           "description" : "Chess",

           "defaultEventPathId" : 1750

         }, {

           "code" : "BIAT",

           "description" : "Biathlon",

           "defaultEventPathId" : 1800

         }, {

           "code" : "FUTS",

           "description" : "Futsal",

           "defaultEventPathId" : 1600

         }, {

           "code" : "BEAC",

           "description" : "Beach Soccer",

           "defaultEventPathId" : 1500

         }, {

           "code" : "TABL",

           "description" : "Table Tennis",

           "defaultEventPathId" : 1900

         }, {

           "code" : "LOTT",

           "description" : "Lottery",

           "defaultEventPathId" : 2000

         }, {

           "code" : "ICEH",

           "description" : "Ice Hockey",

           "defaultEventPathId" : 2100

         }, {

           "code" : "MOTO",

           "description" : "MotoGP",

           "defaultEventPathId" : 2200

         }, {

           "code" : "RALL",

           "description" : "Rally WRC",

           "defaultEventPathId" : 2300

         }, {

           "code" : "SWIM",

           "description" : "Swimming",

           "defaultEventPathId" : 2400

         }, {

           "code" : "RUG7",

           "description" : "Rugby 7s",

           "defaultEventPathId" : 2500

         }, {

           "code" : "CURL",

           "description" : "Curling",

           "defaultEventPathId" : 3300

         }, {

           "code" : "BOWL",

           "description" : "Bowls",

           "defaultEventPathId" : 3400

         }, {

           "code" : "FIEL",

           "description" : "Field Hockey",

           "defaultEventPathId" : 3500

         }, {

           "code" : "BADM",

           "description" : "Badminton",

           "defaultEventPathId" : 5000

         }, {

           "code" : "MOCY",

           "description" : "Motorcycling",

           "defaultEventPathId" : 2600

         }, {

           "code" : "CYCL",

           "description" : "Cycling",

           "defaultEventPathId" : 2700

         }, {

           "code" : "NINE",

           "description" : "Nine-Ball Pool",

           "defaultEventPathId" : 2800

         }, {

           "code" : "WINT",

           "description" : "Winter Sports",

           "defaultEventPathId" : 2900

         }, {

           "code" : "GAEL",

           "description" : "Gaelic Games",

           "defaultEventPathId" : 3000

         }, {

           "code" : "OLYM",

           "description" : "Olympic Games",

           "defaultEventPathId" : 3100

         }, {

           "code" : "FBSP",

           "description" : "Football Specials",

           "defaultEventPathId" : 3200

         }, {

           "code" : "AURL",

           "description" : "Aussie Rules",

           "defaultEventPathId" : 3700

         }, {

           "code" : "FLOO",

           "description" : "Floorball",

           "defaultEventPathId" : 3600

         }, {

           "code" : "SKII",

           "description" : "Skiing",

           "defaultEventPathId" : 3800

         } ]';

         

         $json = json_decode($_sportsAll);

         $_sports = array();

         foreach($json as $key => $val){

            /*
            $_sports[$val->code] = array(

               'id' => $val->defaultEventPathId,

               'desc' => $val->description,

               'code' => $val->code

            );
            */

            $sdesc = strtolower(str_replace(' ', '', $val->description));

            $_sports[$sdesc] = array(

               'id' => $val->defaultEventPathId,

               //'desc' => $val->description,

               'code' => $val->code 

            );

         }

      return $_sports; 

   }

  
} // ..end class
?>