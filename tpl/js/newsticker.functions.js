$(document).ready(function() {

  $.easing.easeInOutBack = function(x, t, b, c, d, s) {
     if (s == undefined) s = 1.70158; 
     if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
     return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
  };
 
  /*
  // START News Ticker
    $('.smarticker5').smarticker({
			theme:'2',
			imagesPath:'tpl/img/',
			rssFeed:'http://sports.espn.go.com/espn/rss/news,'+
			'http://sports.espn.go.com/espn/rss/nfl/news,'+
			'http://sports.espn.go.com/espn/rss/nba/news,'+
			'http://sports.espn.go.com/espn/rss/mlb/news,'+
			'http://sports.espn.go.com/espn/rss/nhl/news,'+
			'http://www.foxsportsasia.com/football-rss,'+
			'http://www.foxsportsasia.com/f1-rss,'+
			'http://sports.espn.go.com/espn/rss/rpm/news,'+
			'http://api.foxsports.com/v1/rss?partnerKey=zBaFxRyGKCfxBagJG9b8pqLyndmvo7UU&tag=soccer,'+
			'http://sports.espn.go.com/espn/rss/ncb/news,'+
			'http://sports.espn.go.com/espn/rss/ncf/news,'+
			'http://sports.espn.go.com/espn/rss/poker/master,'+
			'http://api.foxsports.com/v1/rss?partnerKey=zBaFxRyGKCfxBagJG9b8pqLyndmvo7UU&tag=ufc,'+
			'http://www.foxsportsasia.com/tennis-rss,'+				
			'http://www.FOXSPORTSASIA.com/golf-rss ',				
			rssCats: 'HEADLINES,NFL,NBA,MLB,NHL,Football,Formula 1,Motorsports,Soccer,College Basketball,College Football,Poker,UFC,Tennis,Golf',
			rssSources:'msw-ticker-logo-site.png,msw-ticker-logo-new.png',
			rssColors:'121212,121212'
		});
  // END News Ticker
  */
});


// Scroll to top before on load!
$(window).on('beforeunload', function() {
    $(window).scrollTop(0); 
});