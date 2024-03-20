
  // array difference
  function diff(A, B) {
    return A.filter(function (a) {
      return B.indexOf(a) == -1;
    });
  };


  function initEasyTicker() {
   if ($(".no-market").length == 0) 
   {
      var vticker = $('.content-wrapper').easyTicker({
            direction: 'up',
            easing: 'easeInOutBack',
            speed: 'slow',
            interval: 8000,
            height: 'auto',
            visible: 3,
            mousePause: 0
        });

      var tickObj = vticker.data('easyTicker');
      tickObj.stop();

      if ($(".match-container").length > 6) 
      {
        console.log('matchA container LENGTH is: ' + $(".match-container").length + ' should run easyticker');
        tickObj.start(); 
      }
    }
  }


  function displayNotFound(msg) {
    if (msg == "NOTFOUND")
    {
      $('#no_result').show(); 
    }
    if (msg == "NOTREADY")
    {
      $('#not_ready').show(); 
    }
  }


  function updateSet(match, data) {

    for (i=0; i<5; i++)
    {
      if(typeof(data.set_details[i]) != "undefined" && data.set_details[i] !== null) 
      {
        $(".scores-container").find("#Aset_" + i + "_" + match).html(data.set_details[i].a);
        $(".scores-container").find("#Bset_" + i + "_" + match).html(data.set_details[i].b);

        if(typeof(data.current_set) != "undefined" && data.current_set !== null) 
        {
          set_indicator = i + 1;
          set_color = '';
          if (set_indicator == data.current_set) 
          {
            set_color = 'scores_set';
          }

          $("#Aset_"+ i +"_"+ match).attr('class', set_color);
          $("#Bset_"+ i +"_"+ match).attr('class', set_color);
        }
      }
    } //.. for
  } //.. function


  function updateMatch(match, data) {

    if(typeof(data.match_detail) != "undefined" && data.match_detail !== null) 
    {  
      for (i=0; i<5; i++)
      {
        set_a_win = '';
        set_b_win = '';

        if(typeof(data.match_detail[i]) != "undefined" && data.match_detail[i] !== null) 
        {   
          $(".scores-container").find("#Aset_" + i + "_" + match).html(data.match_detail[i].a);
          $(".scores-container").find("#Bset_" + i + "_" + match).html(data.match_detail[i].b);

          set_win = data.match_detail[i].win;
          if (set_win == 'a')
          {
            set_a_win = 'scores_set';
          }

          if (set_win == 'b')
          {
            set_b_win = 'scores_set';
          }

          // update color of set
          $("#Aset_"+ i +"_"+ match).attr('class', set_a_win);
          $("#Bset_"+ i +"_"+ match).attr('class', set_b_win);
        }
      } //.. for
    }
  } //.. function


  function updateOdds(match, data) {
    var odds_set = '';
    var odds_draw_marker = 0;
    if(typeof(data.odds_details.players_odds) != "undefined" && data.odds_details.players_odds !== null) 
    {
      odds_hidden = 0;
      $.each(data.odds_details.players_odds, function(odds_name, odds_value) 
      {
                          
        // DEFINE CLASS NAME
        if (odds_name == "Draw" || odds_name == "No Goal")
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

        if (odds_draw_marker == 1)
        {
           $('#odds_div_' + match).addClass('odds-selection draw');  
        }
        else
        {
           $('#odds_div_' + match).removeClass('draw');
        }

      
        if(typeof(odds_value.flags) != "string") 
        {
          odds_hidden = 1;
        }
        else
        {
          odds_set += '<div class="odds-container">'+
                          '<div class="'+ odds_span_class_container +'">'+
                            '<span class="'+ odds_span_class_name +'">'+ odds_name +'</span>'+
                          '</div>'+
                          '<div class="price-container">'+
                            '<span class="'+ odds_span_class_price +'">'+ odds_value.price +'</span>'+
                          '</div>'+
                       '</div>';
        }
      }); // end each odds    


      // WRITE IT ON THE TEMPLATE
      var odds_desc = data.odds_details.desc;
      var odds_period = data.odds_details.period;
      var current_quarter = '<span class="current-quarter" id="quarter_'+ match +'">'+ data.match_quarter +'</span>';

      if (odds_hidden == 0)
      {
        var odds_div = '<h1 class="market-title" id="odds_data_'+ match +'">'+ odds_desc +' &mdash; '+ odds_period + current_quarter + '</h1>' + odds_set;
      }
      else
      {
        var odds_div = '<h1 class="market-title"></h1>';
      }
      
      $("#odds_div_"+ match).html(odds_div);

    } //.. if odds defined

  } //.. function



  function removeSports() {
    var sportsIDs = [];
    $(".content-wrapper").find(".sports-ul").each(function(){ sportsIDs.push(this.id); });
    
    var inactive_sport = diff(sportsIDs, myids.sports); 
  
    $.each(inactive_sport, function(index, unsport) {
      console.log('XXX removing sport: ' + unsport);
    });

  } //.. function



  function removeLeagues() {
    var leaguesIDs = [];
    $(".content-wrapper").find(".ul-league").each(function(){ leaguesIDs.push(this.id); });

    tmp_league = [];
    clean = '';

    // remove spaces in league names
    $.each(myids.leagues, function(index, league_name) {
      clean = JSON.stringify(league_name).replace(/\W/g, '');
      tmp_league.push(clean);
    });

    var inactive_leagues = diff(leaguesIDs, tmp_league); 
       
    $.each(inactive_leagues, function(index, unleague) {
     
      lgid = $("#"+unleague).parent().attr("id");
      $("#"+lgid).remove();
    });

  } //.. function


  function removeMatches() {

    var matchesIDs = [];
    $(".content-wrapper").find(".match-container").each(function(){ matchesIDs.push(this.id); });
    
    var inactive_matches = diff(matchesIDs, myids.match); 
    $.each(inactive_matches, function(index, unmatch) {
      $("#"+unmatch).remove();
    });

  } //.. function


  function appendLeague(league_ctr, league_id, sport_ctr, match_id, sport_id) {

    var league_div = '<li class="league-container testDest-'+ league_ctr +'" id="testDest-'+ league_id +'">'+
                          '<ul class="ul-league" id="'+ league_id +'">'+
                            '<li class="league-'+ sport_ctr +'-'+ league_ctr +' league-title">'+
                                 '<h1>'+ match_id.slice(0,-1) +'</h1>'+
                            '</li>'+
                          '</ul>'+ 
                       '</li>';
    
    if ($('#'+league_id).length == 0) 
    { 
      $("#" + sport_id).append(league_div);   
    }  
}


function buildScores(match_key, match_value, league_id, sport_id) {

  var country_league = match_value.country_league;
  var playerA_name = match_value.players.A;
  var playerA_score = match_value.score_detail.a;
  var playerB_name = match_value.players.B;
  var playerB_score = match_value.score_detail.b;
  var li_matches = '<li class="match-container" id="'+ match_key +'">'+
                      '<div class="scores-container">'+
                        '<p class="scores-t-1">'+ 
                            '<span class="team-name">'+
                              playerA_name +
                            '</span>'+
                            '<span class="scoreset-cont" id="aplayer_set_'+ match_key +'">'+
                              '<span class="final-score" id="aplayer_'+ match_key +'">'+ playerA_score +'</span>'+
                            '</span>'+
                        '</p>'+
                        '<p class="scores-t-2">'+ 
                          '<span class="team-name">'+
                            playerB_name +
                          '</span>'+
                          '<span class="scoreset-cont" id="bplayer_set_'+ match_key +'">'+
                            '<span class="final-score" id="bplayer_'+ match_key +'">'+ playerB_score +'</span>'+
                          '</span>'+
                        '</p>'+
                      '</div>'+
                      '<div class="odds-selection" id="odds_div_'+ match_key +'"></div>'+ 
                    '</li>'; 
                                    
  if ($('#'+match_key).length == 0) 
  {
    //$("#end-page").remove();   
    $("#" + league_id).append(li_matches);
    console.log(sport_id + ' ........match appended: ' + match_key);
  }            
}


function buildSet(match_key, match_value, sport_id) {

    if(typeof(match_value.set_details) != "undefined" && match_value.set_details !== null) 
    {

      var sports_set = sport_id + '_set';
      var set = '  ';
      if (sports_set == 'tennis_set') set = 3; 
      if (sports_set == 'volleyball_set') set = 4; 
      if (sports_set == 'beachvolleyball_set') set = 4;

      var aplayer_set_str = '';
      var bplayer_set_str = '';

      if (jQuery.isEmptyObject(match_value.set_details)) {}
      else
      {
        console.log('top set ' + sports_set + 'set ' + set);
        for (i = 0; i < set; i++)
        {   
          var set_a_score = 0;
          var set_b_score = 0;

          if(typeof(match_value.set_details[i]) != "undefined" && match_value.set_details[i] !== null) 
          {
            set_a_score = match_value.set_details[i].a;
            set_b_score = match_value.set_details[i].b;
          }

          if(typeof(match_value.current_set) != "undefined" && match_value.current_set !== null) 
          {
            set_indicator = i + 1;
            set_color = 'class=""';
            if (set_indicator == match_value.current_set) {
              set_color = ' class="score_set"';
            }
          }

          aplayer_set_str += '<span id="Aset_'+ i +'_'+ match_key + '" '+set_color+'>'+ set_a_score +'</span>';
          bplayer_set_str += '<span id="Bset_'+ i +'_'+ match_key + '" '+set_color+'>'+ set_b_score +'</span>';                                                   

        } //.. for

      } //.. else 


      if ($("#Aset_0_"+ match_key).length == 0) 
      {
          $("#aplayer_set_"+ match_key).prepend(aplayer_set_str);
          console.log(sport_id + ' ---------> A player score appended: ' + match_key);
      }
      
      if ($("#Bset_0_"+ match_key).length == 0) 
      {
          $("#bplayer_set_"+ match_key).prepend(bplayer_set_str);
          console.log(sport_id + ' ---------> B score appended: ' + match_key);
      }

    } 
}


function buildMatches(sport_id, match_key, match_value) {

  if (sport_id != 'volleyball' &&  sport_id != 'tennis' && sport_id != 'beachvolleyball')
  {
    if(typeof(match_value.match_detail) != "undefined" && match_value.match_detail !== null) 
    {
      var sports_set = sport_id + '_set';
      var match = '';
      if (sports_set == 'basketball_set') match = 3; 
      if (sports_set == 'icehockey_set') match = 2; 
      if (sports_set == 'americanfootball_set') match = 3; 
      if (sports_set == 'soccer_set') match = 1; 
      if (sports_set == 'handball_set') match = 3;
      var aplayer_set_str = '';
      var bplayer_set_str = '';

      if (jQuery.isEmptyObject(match_value.match_detail)) { }
      else
      {
        for (i=0; i<=match; i++)
        {   
         
          // actual garnered score
          var set_a_score = parseFloat(0);
          var set_b_score = parseFloat(0);
          set_a_win = '';
          set_b_win = '';

          if(typeof(match_value.match_detail[i]) != "undefined" && match_value.match_detail[i] !== null) 
          {
            set_a_score = parseFloat(match_value.match_detail[i].a);
            set_b_score = parseFloat(match_value.match_detail[i].b);
            set_win = match_value.match_detail[i].win;

            if (set_win == 'a')
            {
              set_a_win = 'class="scores_set"';
            }
            if (set_win == 'b')
            {
              set_b_win = 'class="scores_set"';
            }
          }

          aplayer_set_str += '<span id="Aset_'+ i +'_'+ match_key + '" '+set_a_win+'>'+ set_a_score +'</span>';
          bplayer_set_str += '<span id="Bset_'+ i +'_'+ match_key + '" '+set_b_win+'>'+ set_b_score +'</span>';                                                   

        } //.. for
      } // else - if have sets


      if ($("#Aset_0_"+ match_key).length == 0) 
      {
          $("#aplayer_set_"+ match_key).prepend(aplayer_set_str);
          console.log(sport_id + ' ---------> A player score appended: ' + match_key);
      }
      if ($("#Bset_0_"+ match_key).length == 0) 
      {
          $("#bplayer_set_"+ match_key).prepend(bplayer_set_str);
          console.log(sport_id + ' ---------> B score appended: ' + match_key);
      }

    } //.. typeof

  } //.. sports for set tracking

}


function buildOdds(match_key, match_value) {

  var odds_div = '';
  if(typeof(match_value.odds_details) != "undefined" && match_value.odds_details !== null) 
  {
    if(typeof(match_value.odds_details.players_odds) != "undefined" && match_value.odds_details.players_odds !== null) 
    {
      var odds_set = '';
      $.each(match_value.odds_details.players_odds, function(odds_name, odds_value) 
      {
         
          console.log('oddz name below ' + odds_name);
          if (odds_name == "Draw" || odds_name == "No Goal")
          {
             odds_span_class_name = 'odds-draw';
             odds_span_class_price = 'draw-price';
             odds_span_class_container = 'draw-container';
             $('#odds_div_' + match_key).addClass('draw');
          }
          else
          {
             odds_span_class_name = 'odds-team1';
             odds_span_class_price = 'odds-price1';
             odds_span_class_container = 'team-container';
          }

          odds_set += '<div class="odds-container">'+
                          '<div class="'+ odds_span_class_container +'">'+
                            '<span class="'+ odds_span_class_name +'">'+ odds_name +'</span>'+
                          '</div>'+
                          '<div class="price-container">'+
                            '<span class="'+ odds_span_class_price +'">'+ odds_value.price +'</span>'+
                          '</div>'+
                       '</div>';

      }); // end each odds

      var odds_desc = match_value.odds_details.desc;
      var odds_period = match_value.odds_details.period;
      var current_quarter = '<span class="current-quarter" id="quarter_'+ match_key +'">'+ match_value.match_quarter +'</span>';
      var odds_div = '<h1 class="market-title" id="odds_data_'+ match_key +'">'+ odds_desc +' &mdash; '+ odds_period + current_quarter + '</h1>'+ odds_set;

      if ($('#odds_data_'+ match_key).length == 0)
      {
        $("#odds_div_"+ match_key).append(odds_div);
      }
    }
  }
  else
  {
      var odds_div = '<h1 class="market-title"> There\'s no available market at the moment </h1>';
      $("#odds_div_"+ match_key).html(odds_div);  
      console.log('.....odds updated - no available market');
  }
}


function display_match(sport_id, league, msg, sport_ctr) {
  console.log('message: ' + msg);
  console.log('match sport ctr: ' + sport_ctr);

  // LEAGUES --------------------------
  var li_leagues = '';
  league_ctr = 1;
  $.each(league, function(match_id, match_data) {

    var league_id = JSON.stringify(match_id).replace(/\W/g, '');

    // WRITE LEAGUE
    if ($('#testDest'+league_id).length == 0)
    {
      appendLeague(league_ctr, league_id, sport_ctr, match_id, sport_id);
    } 


    // WRITE MATCHES
    var li_matches = '';
    $.each(match_data, function(key, match_value) {

      var match_key = key;

      buildScores(match_key, match_value, league_id, sport_id);
      buildSet(match_key, match_value, sport_id);
      buildMatches(sport_id, match_key, match_value);
      buildOdds(match_key, match_value);
        
    }); // end each matches

    league_ctr++;
  }); // end each league

} // end function display match
   

function display_sport(data, sport_ctr)  {

  console.log('sports ctr: ' + sport_ctr);
  $.each(data, function(sport, league) {

    var sport_id = JSON.stringify(sport).replace(/\W/g, '');
    console.log('sport id: ' + sport_id);
    var sport_div = '<ul>'+ 
                      '<li class="sports-1 next-el current sports-header bg-'+ sport_id +'" id="my_sport">'+
                        '<i class="icon '+ sport_id +'"></i>'+
                          '<h1>'+ sport +'</h1>'+
                      '</li>'+
                    '</ul>';

    var sport_div_body = '<ul class="sports-ul ul-'+ sport_id +'" id="'+ sport_id +'"></ul>';


    if(typeof($("#"+ sport_id)) != "undefined" && $("#"+ sport_id) !== null) 
    {
      if ($("#"+ sport_id).length == 0) {
        $(".content-wrapper-sport").append(sport_div);
        $(".content-wrapper").append(sport_div_body);
      }
    }

    appendmsg = 'im from append';
    console.log('league' + league);

    if (league == 'NOTACTIVE')
    {
      $('.sports-ul').html('<li class="no-market" style="height:85%"><h1>NO LIVE GAME AT THE MOMENT</h1></li>');
    }
    else
    {
      display_match(sport_id, league, appendmsg, sport_ctr); 
    }

  }); // end each sport

} // end display()


function addZero(i) {
  
  if (i < 10) {
    i = "0" + i;
  }
  return i;
}


function updateSportData(sport, league) {

  console.log('sport: ' + sport_id + ' FOUND');
  if (league == 'NOTACTIVE')
  {
    $('.sports-ul').html('<li class="no-market" style="height:85%"><h1>NO LIVE GAME AT THE MOMENT</h1></li>');
    console.log('appending no market');
    return false; 
  }
  else
  {
    $('.no-market').remove();
  }


  // UPDATE
  $.each(league, function(league_name, lval) 
  {
    $.each(lval, function(match, data) 
    {
      // check if match is already displayed
      if ($('#'+match).length > 0) 
      { 
        
        console.log('coming in.. updating scores in ' + match);
        $(".scores-container").find("#aplayer_" + match).html(data.score_detail.a);
        $(".scores-container").find("#bplayer_" + match).html(data.score_detail.b);

        //use SET if not blank 
        if (typeof(data.set_details) != 'string')
        {
          updateSet(match, data);
        } 
        else // use MATCH
        {         
          updateMatch(match, data); 
        }

        // ODDS UPDATE
        if(typeof(data.odds_details) != "undefined" && data.odds_details !== null) 
        {
          updateOdds(match, data);
        }
        else
        {
            var odds_div = '<h1 class="market-title"> NO AVAILABLE MARKET </h1>';
            $("#odds_div_"+ match).html(odds_div);  
        }

      } // close check existing match
      else
      {
          console.log('building our display for the match... ' + match);
          updatemsg = 'im from update';
          display_match(sport_id, league, updatemsg, sport_ctr); // append the new data  
      }

    }); // each match
  }); // end league
}


window.setInterval(reloadIFrame, 900000); // 15 minutes
function reloadIFrame() {
  document.getElementById('frmtimer').contentWindow.location.reload();
  //alert('frame reload');
}






