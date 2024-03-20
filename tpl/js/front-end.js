 
$(document).ready(function(){

	var options = {
	
		format:'<span class=\"dt\">%I:%M:%S %P</span>',
		
		timeNotation: '12h',
		
		am_pm: true,
		
		utc:true,
		
		utc_offset: 8
		
	}
	
	$('#clock-ticker .clock-digit').jclock(options);
	
});