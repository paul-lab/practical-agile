$(function() {
       	$('#summary').click(function() {
		if ($('#extra').css('display') == 'none') 
		{
			$("#container").css("width","100%" );
			$("#container").css("max-height","999px" );
			$("#container").css("overflow","auto" );
			$("#container").css("height","auto" );
			$("#detail").css("height","auto" );
			$("#detail").css("overflow","auto" );
			$("#extra").css("display","inline" );
		}else{ 
			$("#container").css("width","495px" );
			$("#extra").css("display","none" );
			$("#container").css("max-height","325px" );
			$("#container").css("overflow","hidden" );
			$("#detail").css("overflow","hidden" );
			$("#detail").css("height","228px" );
		}
        });
});
