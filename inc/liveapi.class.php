<?php

class MobileAPI extends reqAPI{

	/**
	 * @desc: rest URL
	 */
	public $restURL;

	/**
	 * @desc: rest URL suffix
	 */
	public $suffix;

	/**
	 * @desc: the file token
	 */
	private $file_token;

	/**
	 * @desc: the file on json format
	 */
	public $file_sports;




	function __construct($settings) 
	{
		$this->restURL 		= $settings['rest_url'];
		$this->suffix		= $settings['rest_url_ext'];
		$this->file_token 	= $settings['file_token'];
		$this->file_sports 	= $settings['file_sports'];

		parent::__construct($settings);
	}

	
	/**
	 * @desc: json decode string
	 */
	function allSports()
	{
		$_sportsAll = file_get_contents($this->file_sports);

		$json = json_decode($_sportsAll);
		$_sports = array();
		foreach($json as $key => $val) 
		{
			$_sports[$val->code] = array(
				'id' 	=> $val->defaultEventPathId,
				'desc' 	=> $val->description,
				'code' 	=> $val->code
				);
		}

		return $_sports;
	}


	function getSports() 
	{

	 	$url = $this->restURL . '/ept?lineId=2&originId=3';
		$_sports = $this->allSports();

		$this->get_curl_result($url);
		$this->m_debug();

		if($this->status)
		{

			$list = array();
			$json = json_decode($this->result);

			foreach($json->ept as $cont => $val){

				$list[] = array(
					'id' =>	$_sports[$val->code]['id'],
					'name' =>	$val->desc,
					'sportCode' =>	$val->code
				);

			}

			//echo json_encode($list,JSON_PRETTY_PRINT);
			return $list;
		}
	}

	/**
	 * @param: none
	 * @return: ARRAY if success otherwise FALSE
	 */
	function sortLiveData()
	{
		
		$tmp = json_decode($this->result, true);
		if (empty($tmp)) return FALSE;
		$tmp = null;

		// get all ids
		$json_result = json_decode($this->result);
		$ids 		 = (isset($json_result->ids)) ? $json_result->ids : "";
		$num_ids 	 = (count($ids))-1;
		if (!isset($json_result->items)) return false;
		$items 		 = (array) $json_result->items;
		$live_data 	 = array();

		if ($num_ids < 1) 
			return FALSE;
		else
		{

			for ($i=0; $i<=$num_ids; $i++)
			{
				
				// get live detail
				if (array_key_exists('l'.$ids[$i], $items))
				{
					$valid_key 			= $items['l'.$ids[$i]];
					$country_region 	= "Country";

					$sport 				= (isset($valid_key->path->Sport)) ? $valid_key->path->Sport : "";
					$sport_code 		= (isset($valid_key->code)) ? $valid_key->code : "";
					$category			= (isset($valid_key->path->Category)) ? $valid_key->path->Category : "";
					$country 			= (isset($valid_key->path->$country_region)) ? $valid_key->path->$country_region : "";
					$league 			= (isset($valid_key->pdesc)) ? $valid_key->pdesc : "";
					$game_description 	= (isset($valid_key->desc)) ? $valid_key->desc : "";
					$flags				= (isset($valid_key->flags)) ? $valid_key->flags : "";
					$match_details 		= (isset($valid_key->match)) ? $valid_key->match : "";
					$score 				= (isset($valid_key->score)) ? $valid_key->score : "";
					$current_set 		= (isset($valid_key->currSet)) ? $valid_key->currSet : "";
					$playerA			= (isset($valid_key->a)) ? $valid_key->a : "";
					$playerB			= (isset($valid_key->b)) ? $valid_key->b : "";
					$set_details 		= (isset($valid_key->set)) ? $valid_key->set : "";
					$match_time_minutes = (isset($valid_key->time->m)) ? $valid_key->time->m : "";
					$match_tme_seconds 	= (isset($valid_key->time->s)) ? $valid_key->time->s: "";
					$match_time_status	= (isset($valid_key->time->status)) ? $valid_key->time->status : "";
					$match_quarter		= (isset($valid_key->time->p)) ? $valid_key->time->p : "";

					// for score value for tennis
					if ($sport_code == 'TENN')
					{
						$score->a = set_tennis_score($score->a);
						$score->b = set_tennis_score($score->b);						
					}
						
					$cleague = (!empty($country)) ? $country : $category;

                    $leaguename = $cleague . " : " . $league;
                    
                    if(strpos($leaguename, 'electronic') !== false) {
                        
                    } else {

					    $live_data[$sport_code . '-l' . $ids[$i]] 
													= array(
														"sport"			 => $sport,
														"sport_code"	 => $sport_code,
														"description" 	 => $game_description,
														"category"		 => $category,
														"country"		 => $country,
														"country_league" => $cleague . " : " . $league,
														"players"		 => array(
																			'A' => $playerA,
																			'B'	=> $playerB,
																			),
														"match_detail"	 => $match_details,
														"score_detail"	 => $score,
														"flags"			 => $flags,
														"current_set"	 => $current_set,
														"set_details"	 => $set_details,
														"match_time"	 => $match_time_minutes . " : " .$match_tme_seconds, 
														"match_status"   => $match_time_status,
														"match_quarter"  => $match_quarter
														);
                            
                    } // end of checking for electronic leagues
					
				} // ..end get live 
				

				// get match detail
				if (array_key_exists('m'.$ids[$i], $items))
				{
					$valid_key 		= $items['m'.$ids[$i]];
					$live_parent 	= (isset($valid_key->parent)) ? $valid_key->parent : "";

					foreach($live_data as $k => $v)
					{
						$arrk 		= explode("-", $k);
						$live_key 	= $arrk[1];

						if ($live_key == $live_parent)
						{
							$match_desc 		= (isset($valid_key->desc)) ? $valid_key->desc : "";
							$match_pos 			= (isset($valid_key->pos)) ? $valid_key->pos : "";
							$match_market_type 	= (isset($valid_key->marketTypeGroup)) ? $valid_key->marketTypeGroup : "";
							$match_style 		= (isset($valid_key->style)) ? $valid_key->style : "";
							$match_period 		= (isset($valid_key->period)) ? $valid_key->period : "";
							
							
							$live_data[$arrk[0]."-".$live_key]['odds_id'] = 'm'.$ids[$i];
							$live_data[$arrk[0]."-".$live_key]['m'.$ids[$i]] 
									= array(
										"desc" 			=> $match_desc,
										"pos"	 		=> $match_pos,
										"market_type"	=> $match_market_type,
										"style"			=> $match_style,
										"period"		=> $match_period,
										);
						}			

					}
				
				} // ..end get match


				// get odds detail
				if (array_key_exists('o'.$ids[$i], $items))
				{

					$valid_key = $items['o'.$ids[$i]];		
					$parent_id = (isset($valid_key->parent)) ? $valid_key->parent : "";

					foreach ($live_data as $key => $value) 
					{
						$live_data_keys = array_keys($value);

						$arrk 		= explode("-", $key);
						$live_key 	= $arrk[1];

					
						if (in_array($parent_id, $live_data_keys))
						{
							
							$odds_price = (isset($valid_key->price)) ? $valid_key->price : "";
							$odds_desc 	= (isset($valid_key->desc)) ? $valid_key->desc : "";
							$odds_pos 	= (isset($valid_key->pos)) ? $valid_key->pos : "";
							$flags		= (isset($valid_key->flags)) ? $valid_key->flags : "";

							$live_data[$arrk[0]."-".$live_key][$parent_id]["players_odds"][$odds_desc]
												= array(
														"parent_id" => $parent_id,
														"price" => $odds_price,
														"pos" 	=> $odds_pos,
														"flags" => $flags,
														);
							
						}
						
					}

				} // ..end get odds

			} // ..end for


			// combine all sports 
			foreach ($live_data as $key => $value) 
			{
				$key_arr = explode("-", $key);	
				$live_data[$key_arr[0]][$key_arr[1]] = $value;
				unset($live_data[$key]);
			}


		} // ..end else

		return $live_data;	
	} // ..end function



} // end class

?>