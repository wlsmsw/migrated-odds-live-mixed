<!DOCTYPE html>
<html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <META HTTP-EQUIV="Refresh" CONTENT="600">
  <title>MSW LIVE Odds ALL sports</title>
  
  <script type="text/javascript">
   $(document).ready(function(){ 
       
    (function worker() {

      $.ajax({

        type: "POST",        
        url: 'load_data.php', 
		    data: 'sport=<?php echo $data['sport']; ?>',
        
        success: function(data) {
          var start = new Date().getTime(); 
          if(typeof(data) != "undefined" && data !== null) 
          {
            if (data == "NOTFOUND" || data == "NOTREADY")
            {
              displayNotFound(data);
            }
            else
            { 
              obj = JSON.parse(data);
              var keys = Object.keys(obj);
              myids = obj.myids; // get all active match ids
              delete obj.myids;  // unset ids in main array
              
              if(Object.keys(myids.sports).length <= 0){
                  displayNotFound("NOCONTENT");
                  return false;
              }else{
                  $('#no_content').hide(); 
              }
                
              // this is the function to work with
              var league_list = [];
              var match_list = [];
                
              //get all keys under basketball
              /*var text = "basketball";
              var live_leagues = []; //current basketball leagues in live db
              for (let i = 0; i < keys.length; i++) {
                 if(keys[i].indexOf(text) !== -1){
                    live_leagues.push(keys[i]);    
                 }
                }*/
            
              //priority leagues to be displayed
              var allowed_leagues = ["NBA", "NCAA", "PBA", "KBL", "CBA", "B LEAGUE", "NBL"];
              var in_leagues = []; //hold leagues within allowed leagues
              var out_leagues = []; //hold leagues not in allowed leagues
              
              
              //loop through fetched leagues and check if there are allowed leagues
              for (let j=0; j<keys.length; j++){ 
                  
                var partofarray = 0;
            
                for (let k=0; k<allowed_leagues.length; k++){
                    if((keys[j].indexOf(allowed_leagues[k]) !== -1)&&(keys[j].indexOf("basketball") !== -1)){
                        partofarray++;
                    }
                }
                
                if(partofarray > 0){
                    in_leagues.push(keys[j]);
                }else{
                    out_leagues.push(keys[j]);
                }
                    
              }
              
              //if there are items in in_leagues, remove items in out_leagues from main object
              if(in_leagues.length > 0){
                  for (let l=0; l<out_leagues.length;l++){
                      delete obj[out_leagues[l]];
                  }
              }
                
              //TEST DATA
              // console.log("obj = "+JSON.stringify(obj));
              /*obj = {"squash - International : Black Ball Squash Open1": {
                "l16973588": {
                  "sport": "Squash",
                  "sport_code": "SQUA",
                  "description": "Ibrahim, Youssef vs Elshorbagy, Marwan",
                  "betradar_mid": "",
                  "category": "International",
                  "country": "",
                  "country_league": "International : Black Ball Squash Open",
                  "players": {
                    "A": "Ibrahim, Youssef",
                    "B": "Elshorbagy, Marwan"
                  },
                  "match_detail": [
                    {
                      "win": "b",
                      "a": "0",
                      "b": "1"
                    }
                  ],
                  "score_detail": {
                    "period": "OR",
                    "a": "0",
                    "b": "1"
                  },
                  "flags": "",
                  "current_set": "",
                  "set_details": [
                    {
                      "win": "b",
                      "a": "10",
                      "b": "12"
                    },
                    {
                      "a": "6",
                      "b": "7"
                    }
                  ],
                  "match_time": "9 : 54",
                  "match_status": 2,
                  "match_quarter": "G2",
                  "odds_id": "m2277608283",
                  "odds_details": {
                    "desc": "Head to Head",
                    "pos": 1,
                    "market_type": "MONEY_LINE",
                    "style": "TWO_OUTCOME",
                    "period": "Live Match",
                    "players_odds": {
                      "Ibrahim, Youssef": {
                        "parent_id": "m2277608283",
                        "price": "8.25",
                        "pos": 1,
                        "flags": ""
                      },
                      "Elshorbagy, Marwan": {
                        "parent_id": "m2277608283",
                        "price": "1.06",
                        "pos": 2,
                        "flags": ""
                      }
                    }
                  }
                }
              }}*/


              $.each(obj, function(league, data) 
              {
                // LEAGUES --------------------------
                var li_leagues = '';
                league_ctr = 1;
                league_id = league;
                var odds_draw_marker = '';
                var league_name = '';

                var league_array = league.split("-");
                var sportr_id = league_array[0];
                var sports_class_header = sportr_id.replace(/\s/g, '');
                var league_id = JSON.stringify(league).replace(/\W/g, '');
                
                //for testing specific sports
                // if(sportr_id != "squash ") return true;

                league_title = league.slice(0, -1);
                league_name = 'lc-' + league_id;
                league_list.push(league_name);
                
                lname = league_title.split(' - ');
                lname2 = lname[1].split(' : ');
                
                fin_lname = lname[0]; //sport
                league_name = lname2[1];
                if(lname2[0] == ''){
                    fin_lname = fin_lname+' - '+lname2[1]; //sport + league
                }else{
                    fin_lname = fin_lname+' - '+lname2[0]+' : '+lname2[1]; //sport + country + league
                }

                var sport_league = '<li class="league-container" id="lc-'+ league_id +'" style="margin: 0px;">' +
                                      '<ul class="ul-league ul-'+ sports_class_header +'" id="'+ league_id +'">'+
                                        '<li class="league-1-2 league-title bg-'+ sports_class_header +'">' +
                                          '<h1><i class="icon '+sports_class_header+'"></i>'+ fin_lname +'</h1>' +
                                        '</li>'
                                      '</ul>' +
                                    '</li>';


                  

                  odds_selection_draw = 'odds-selection';

                  // start looping match
                  $.each(data, function(match_id, match_data) 
                  {
                    match_list.push(match_id);
                    var odds_details_desc = '';
                    var odds_details_period = '';
                    var player_odds = '';
                    var no_of_odds = 0;
                    var multi_odd_class = "";
                    

                    if(typeof(match_data.odds_details) != "undefined" && match_data.odds_details !== null) 
                    {
                      odds_details_desc = match_data.odds_details.desc;
                      odds_details_period = match_data.odds_details.period;
                      odds_details_players_odds = match_data.odds_details.players_odds;


                      if(sportr_id != 'basketball ' && match_data.odds_details.market_type != "MONEY_LINE")
                        return true;
                      
                      //filter market per sports
                      /*switch(sportr_id) {
                            case "basketball ":
                                  if(!(match_data.odds_details.desc == "Win/Draw/Win" || match_data.odds_details.desc == "Head To Head"))
                                    return true;
                            break;      
                            case "tennis ":
                            case "volleyball ":
                            case "american football ":
                            case "table tennis ":
                            case "boxing ":
                            case "rugby ":
                            case "cricket ":
                                  if(!(match_data.odds_details.desc == "Head To Head"))
                                    return true;
                            break;
                            case "soccer ":
                            case "football ":
                            case "ice hockey ":
                                  if(!(match_data.odds_details.desc == "Win/Draw/Win"))
                                    return true;
                            break;
                            case "aussie rules ":  
                            case "beach volleyball ":
                            case "badminton ":
                            case "baseball ":
                            case "handball ":
                            case "snooker ":
                            case "baseball ":
                            break;
                            default:
                            break;
                          }  
                      */


                      if(typeof(odds_details_players_odds) != "undefined" && odds_details_players_odds !== null) 
                      {
                        odds_draw_marker = 0;
                        no_of_odds = Object.keys(odds_details_players_odds).length;
                        // console.log("("+no_of_odds+") "+match_id);

                        $.each(odds_details_players_odds, function(name, player_data) 
                        {
                           // DEFINE CLASS NAME for CSS
                            if (name == "Draw" || name == "No Goal")
                            {
                              odds_span_class_name = 'odds-draw';
                              odds_span_class_price = 'draw-price';
                              odds_span_class_container = 'draw-container';
                              odds_draw_marker = 1;
                            }
                            else
                            {
                              odds_span_class_name = 'odds-team1';
                              odds_span_class_price = 'odds-price1';
                              odds_span_class_container = 'team-container';                               
                            }

                            if(no_of_odds == 4)//adjust display if 4 odds by adding class
                              multi_odd_class = "four-odds";
                            
                            if(typeof(player_data.flags) == "object") 
                            {
                              player_odds += '<div class="odds-container '+multi_odd_class+'"></div>';
                            }
                            else
                            { 
                              player_odds += '<div class="odds-container '+multi_odd_class+'">' +
                                                '<div class="'+ odds_span_class_container +'">' +
                                                    '<span class="'+ odds_span_class_name +'">' + name +'</span>'+
                                                '</div>'+
                                                '<div class="price-container">'+
                                                    '<span class="'+ odds_span_class_price +'">'+ player_data.price +'</span>'+
                                                '</div>'+
                                             '</div>';
                            }
      
                        }); // end odds building

                      }
                        // console.log("."+sportr_id+"."+ match_data.match_quarter);

                        //remove "/ Set 1"...
                        split_match_quarter = match_data.match_quarter.split(" / ");
                        match_data.match_quarter = split_match_quarter[0];
                        
                        //if tennis, sets will be their quarter match
                        if(sportr_id == "tennis " && split_match_quarter.length > 1)
                        {
                            match_data.match_quarter = "S" + split_match_quarter[1].substring(0, 1);
                        }
                          


                        //remove quarter label 
                        if(sportr_id == "cricket ")
                          match_data.match_quarter = "";

                        //reverse quarter label
                        if(sportr_id == "football " || sportr_id == "baseball ")
                          match_data.match_quarter = match_data.match_quarter.split("").reverse().join("");

                          // ODDS MAIN
                          qtr_info = '<h1 class="market-title" id="odds_data_'+ match_id +'"> <span class="current-quarter" id="quarter_'+ match_id +'">'+ match_data.match_quarter +'</span>'+
                                          '<div id="market-title-header">' + odds_details_desc + ' &ndash; ' + odds_details_period + '</div>' +
                                      '</h1>';
                        
                        
                        //League header, available market only
                        if ($('#'+league_id).length == 0)
                        {
                          $(".sports-ul").append(sport_league);
                        }

                    }
                    else
                    {
                      qtr_info = '';
                      player_odds = '<h1 class="market-title"> NO AVAILABLE MARKET </h1>';
                      return true; //skip no available market rows

                    }

                    var header_score_detail = '';
                    var playerA_score_detail = '';
                    var playerB_score_detail = '';
                    var header_score_blank = '';
                    var playerA_score_blank = '';
                    var playerB_score_blank = '';
                    var baseball_header_score_detail = '';
                    var baseball_playerA_score_detail = '';
                    var baseball_playerB_score_detail = '';
                    var playerA_score = '';
                    var playerB_score = '';
                    var header_score = 1;
                    var rawA_score = 0;
                    var rawB_score = 0;
                    var counter_limit = 4;
                    
                    if (sportr_id == "ice hockey ") counter_limit = 2;
                    if (sportr_id == "cricket ") counter_limit = 0;
                    if (sportr_id == "soccer " || sportr_id == "football ") counter_limit = 2;
                    if (sportr_id == "tennis " || sportr_id == "table tennis " || sportr_id == "volleyball " || sportr_id == "squash ") counter_limit = 5;
                    if (sportr_id == "baseball ") counter_limit = 9;

                    var scores_limit = 5; //default number of scores to align all sport's scores
                    if(sportr_id == "baseball ") //except baseball with 9 sets and 3 additional
                      scores_limit = 9;

                    /*console.log("set = "+ JSON.stringify(match_data.set_details) +
                                " match = "+ JSON.stringify(match_data.match_detail) + 
                                " details = "+ JSON.stringify(match_data));*/

                    // quarter scores
                    for (i=0; i<scores_limit; i++)
                    {
                      qtr_win = 'c';
                      if (sports_class_header == "tennis" ||sports_class_header == "tabletennis" || sports_class_header == "volleyball" || sports_class_header == "beachvolleyball" || sports_class_header == "squash")
                      {
                        if(typeof(match_data.set_details[i]) != "undefined" && match_data.set_details[i] !== null) 
                        {
                          rawA_score = match_data.set_details[i].a;   
                          rawB_score = match_data.set_details[i].b;  
                        //   qtr_win = match_data.set_details[i].win;
                          
                          
                        }
                      }
                      else
                      {
                        if(typeof(match_data.match_detail[i]) != "undefined" && match_data.match_detail[i] !== null) 
                        {
                          rawA_score = match_data.match_detail[i].a;   
                          rawB_score = match_data.match_detail[i].b;  
                        //   qtr_win = match_data.match_detail[i].win;
                        }
                      }

                      current_set = match_data.current_set; // tennis, volleyball, beachvolleyball
                      current_quarter = match_data.match_quarter;
                      
                      j = i + 1;
                      if (sportr_id == "volleyball " || sportr_id == "beachvolleyball ")
                      {
                        current_set = match_data.current_set;
                        if (current_set == j) 
                        {
                          qtrA_win = "scores_set";
                          qtrB_win = "scores_set";
                        }
                        else
                        {
                          qtrA_win = "xx";
                          qtrB_win = "xx";
                        }
                      }
                      else
                      {
                        current_quarter = match_data.match_quarter;
                        qtr = current_quarter.slice(-1);
                        if (qtr == j) 
                        {
                          qtrA_win = "scores_set";
                          qtrB_win = "scores_set";
                        }
                        else
                        {
                          qtrA_win = "xx";
                          qtrB_win = "xx";
                        }
                      }

                      
                        
                      /*var quarter_first_string = match_data.match_quarter.substring(0, 1);
                      
                      header_quarter_score = quarter_first_string + header_score;*/

                      switch(sportr_id) {
                        case "basketball ":
                            if(league_name == "NCAA Men"){
                                header_quarter_score = "H" + header_score;
                                counter_limit = 2;
                            }else{
                                header_quarter_score = "Q" + header_score;
                            }
                            break;
                        case "american football ":
                        case "aussie rules ":
                              header_quarter_score = "Q" + header_score;
                          break;
                        case "football ":
                        case "soccer ":
                        case "rugby ":
                              header_quarter_score = "H" + header_score;
                          break;
                        case "tennis ":
                        case "volleyball ":
                        case "beach volleyball ":
                        case "badminton ":
                              header_quarter_score = "S" + header_score;
                          break;
                        case "baseball ":
                              header_quarter_score = "I" + header_score;
                          break;
                        case "table tennis ":
                        case "squash ":
                              header_quarter_score = "G" + header_score;
                          break;
                        case "ice hockey ":
                        case "handball ":
                              header_quarter_score = "P" + header_score;
                          break;
                        case "snooker ":
                              header_quarter_score = "M" + header_score;
                          break;
                        case "boxing ":
                              header_quarter_score = "R" + header_score;
                          break;
                        default:
                              header_quarter_score = header_score;
                        break;
                      }  

                      /*
                                
                      golf
                      motorsports
                      formula1
                      darts
                      wintersports
                      cycling
                      */
                        
                      //add span for border in score header  
                      header_quarter_score = '<span class="score-header">'+ header_quarter_score +'</span>';
                      
                      /*if(sportr_id == "cricket ")//final score only for cricket
                      {
                          header_quarter_score = "&nbsp;";
                          rawA_score = "&nbsp;";
                          rawB_score = "&nbsp;";
                      }*/

                      var baseball_class = "";
                      if(sportr_id == "baseball ")
                      {
                        baseball_class = "baseball-score";

                        if(match_data.match_detail.length <= (i+1))
                        {
                          rawA_score = 0;
                          rawB_score = 0;
                        }
                        
                      }
                        
                      if (i < counter_limit) 
                      {

                        header_score_detail  += '<span id="Hset_'+ i +'_'+ match_id +'" class="xx '+ baseball_class +'">'+ header_quarter_score +'</span>';
                        playerA_score_detail += '<span id="Aset_'+ i +'_'+ match_id +'" class="'+ qtrA_win +' '+ baseball_class +'">'+ rawA_score +'</span>';
                        playerB_score_detail += '<span id="Bset_'+ i +'_'+ match_id +'" class="'+ qtrB_win +' '+ baseball_class +'">'+ rawB_score +'</span>';
                      }
                      else
                      {
                        header_score_blank  += '<span id="Hset_'+ i +'_'+ match_id +'" class="xx '+ baseball_class +'">&nbsp;</span>';
                        playerA_score_blank += '<span id="Aset_'+ i +'_'+ match_id +'" class="'+ qtrA_win +' '+ baseball_class +'">&nbsp;</span>';
                        playerB_score_blank += '<span id="Bset_'+ i +'_'+ match_id +'" class="'+ qtrB_win +' '+ baseball_class +'">&nbsp;</span>';
                      }

                      

                      header_score++;
                      var rawA_score = 0;
                      var rawB_score = 0;

                    } // end loop for scores
                    
                    //add blank scores in the beginning to align scores in other sports
                    header_score_detail = header_score_blank + header_score_detail;
                    playerA_score_detail = playerA_score_blank + playerA_score_detail;
                    playerB_score_detail = playerB_score_blank + playerB_score_detail;

                    if(match_data.score_detail != "") 
                    {
                      finalA_score = match_data.score_detail.a;   
                      finalB_score = match_data.score_detail.b;   
                    }
                    else
                    {
                      finalA_score = 0;   
                      finalB_score = 0;
                    }



                    header_final_score = '<span class="final-score" id="aplayer_'+ match_id +'">FINAL</span>';
                    playerA_score = '<span class="final-score" id="aplayer_'+ match_id +'">'+ finalA_score +'</span>';
                    playerB_score = '<span class="final-score" id="bplayer_'+ match_id +'">'+ finalB_score +'</span>';

                    
                    var sport_match_score = '<li class="match-container" id="'+ match_id +'">'+
                                                '<div class="scores-container">'+
                                                    '<p class="scores-t-1">'+
                                                        '<span class="team-name"></span>'+
                                                        '<span class="scoreheaderset-cont" id="player_set_'+ match_id +'">'+
                                                            header_score_detail +
                                                            header_final_score +
                                                        '</span>'+
                                                    '</p>'+
                                                    '<p class="scores-t-1">'+
                                                        '<span class="team-name">'+ match_data.players.A +'</span>'+
                                                        '<span class="scoreset-cont" id="aplayer_set_'+ match_id +'">'+
                                                            playerA_score_detail +
                                                            playerA_score +
                                                        '</span>'+
                                                    '</p>'+
                                                    '<p class="scores-t-2">'+
                                                        '<span class="team-name">'+ match_data.players.B +'</span>'+
                                                        '<span class="scoreset-cont" id="bplayer_set_'+ match_id +'">'+
                                                            playerB_score_detail +
                                                            playerB_score +
                                                        '</span>'+
                                                    '</p>'+
                                                '</div>';
                    
                    
                    if(match_data.odds_details.market_type == "MONEY_LINE"){
                        
                        sport_match_score = sport_match_score + '<div class="odds-selection draw" id="odds_div_'+ match_id +'">'+
                                                    qtr_info +
                                                    player_odds +
                                                '</div>';
                        
                    }

                    sport_match_score = sport_match_score +'</li>';

                    if ($('#' + match_id).length == 0)
                    {
                      $("#" + league_id).append(sport_match_score);
                    }
                    else
                    {
                      if ($('#aplayer_set_'+ match_id).length > 0)
                      {
                        $("#aplayer_set_"+ match_id).html(playerA_score_detail + playerA_score);
                        //console.log('aplayer_set_' + match_id + ' updated');
                      }

                      if ($('#bplayer_set_'+ match_id).length > 0)
                      {
                        $("#bplayer_set_"+ match_id).html(playerB_score_detail + playerB_score);
                        //console.log('bplayer_' + match_id + ' updated');
                      }

                      if ($('#odds_div_' + match_id).length > 0)
                      {
                        $("#odds_div_" + match_id).html(qtr_info + player_odds);
                        //console.log('odds updated in ' + match_id);
                      }
                    }

                    if (odds_draw_marker == 0)
                    {
                       $('#odds_div_' + match_id).removeClass('draw');
                       //console.log('removing class draw on match ' + match_id);
                    }
                    else
                    {
                       $('#odds_div_' + match_id).addClass('draw');
                       //console.log('adding class draw on match ' + match_id);
                    }
                  
                  }); // match

              }); // end each league

              
              // remove league
              var leagueIDs = [];
              $(".sports-ul").find(".league-container").each(function(){ leagueIDs.push(this.id); });
              
              var inactive_leagues = diff(leagueIDs, league_list); 

              //console.log('posted league: ' + leagueIDs); // processed sports id
              //console.log('new league ids: ' + league_list); // new sports id data
              //console.log('diff league: ' + inactive_leagues); // id's left
              
              $.each(inactive_leagues, function(index, unleague) {
              // console.log('XXX removing league: ' + unleague);
                $("#"+unleague).remove();
              });

              // ------------------------------------------------------------
              // remove match
              var matchesIDs = [];
              $(".league-container").find(".match-container").each(function(){ matchesIDs.push(this.id); });
              
              var inactive_matches = diff(matchesIDs, match_list); 

              //console.log('posted match: ' + matchesIDs); // processed sports id
              //console.log('new match ids: ' + match_list); // new sports id data
              //console.log('diff matches: ' + inactive_matches); // id's left
              
              $.each(inactive_matches, function(index, unmatch) {
              // console.log('XXX removing match: ' + unmatch);
                $("#"+unmatch).remove();
              });
              
            //   console.log(match_list.sort());
               
            } // end else 

          } // end validate data from ajax
          
          var end = new Date().getTime();
          var time = end - start;
          // console.log('Execution time: ' + time);          
        
        },
        complete: function() {
        
         if ($(".no-market").length == 0) 
         {
            var vticker = $('.content-wrapper').easyTicker({
                  direction: 'up',
                  easing: 'easeInOutBack',
                  speed: 'slow',
                  interval: 8000,
                  height: 'auto',
                  visible: 1,
                  mousePause: 0
              });

            var tickObj = vticker.data('easyTicker');
            tickObj.stop();

            if ($(".match-container").length > 5) 
            {
              // console.log('matchA container LENGTH is: ' + $(".match-container").length + ' should run easyticker');
              tickObj.start(); 
            }
          }
        
          setTimeout(worker, 10000); // ten seconds
            
        } // end complete
        }); // end ajax
      })(); // end worker function


      $(window).on("resize", function() {  

        var screenHeight = $(window).height();
        var screenWidth = $(window).width();
        var oddsWrapperHeight = $('.content-wrapper').height();
        var clockWidth = $('#clock-ticker').width();
        var smartickerWidth = screenWidth - clockWidth;

        $('.content-wrapper').css('height', oddsWrapperHeight * 0.1);
        $('.smarticker5').css('width', smartickerWidth);
          
      })
      .resize();

  }); // jquery  
  </script>
  <link rel="stylesheet" type="text/css" href="tpl/css/jquery.smarticker.min.css">
  <link rel="stylesheet" type="text/css" href="tpl/css/style.css">
</head>
<body>
  <div class="row content-row" id="no_result" style="display:none;color:black" align="center">
    <h2><br /> UNRECOGNIZED REQUESTED SPORT </h2>
  </div>
  <div class="row content-row" id="not_ready" style="display:none;color:black" align="center">
    <h2><br /> ALL SPORT VIEWING NOT READY YET </h2>
  </div>
  <div class="row content-row" id="no_content" style="display:none;color:black" align="center">
    <h2><br /> CURRENTLY NO AVAILABLE LIVE ODDS </h2>
  </div>
  <div class="content-wrapper" style="position: relative; height: 1061px; overflow: hidden;">
  <ul class="sports-ul" style="margin: 0px; position: absolute; top: 0px;">
  </ul>
  </div>


</body>
</html>