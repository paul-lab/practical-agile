/*
* Practical Agile Scrum tool
*
* Copyright 2013-2015, P.P. Labuschagne

* Released under the MIT license.
* https://github.com/paul-lab/practical-agile/blob/master/_Licence.txt
*
* Homepage:
*   	http://practicalagile.co.uk
*	http://practicalagile.uk
*
*/

	$(function() {
		document.title = 'Practical Agile: '+$("#phpbread").text().substring(13);
		$("#breadcrumbs").html($("#phpbread").html());
		if ($("#phpnavicons")){
			$("#navicons").html($("#phpnavicons").html());
		}
	});


	var thisproject=$( "div" ).find( ".thisproject" ).prop("id");
	var thisuser=$( "div" ).find( ".thisuser" ).prop("id");

$(function(){

   $('input:radio').change(function(){
		$.ajax({
			type: "GET",
			url: "estimate_Add.php",
			data: 'PID='+thisproject+'&EST='+$(this).val()+'&WHO='+thisuser,
			success: function (data) {
			}
		});
    });

	$('#Clear').click(function(){
		$.ajax({
			type: "GET",
			url: "estimate_Clear.php",
			data: 'PID='+thisproject,
			success: function (data) {
				$(".hideit").hide()
				$(".clearit").text('');
				$('input[name="Size"]').prop('checked', false);
			}
		});
    });

	 $('#Show').click(function(){
		$.ajax({
			type: "GET",
			url: "estimate_Show.php",
			data: 'PID='+thisproject,
			success: function (data) {
			}
		});
    });
});


function refresh() {
	$.ajax({
		type: "GET",
		url: "estimate_Refresh.php",
		data: 'PID='+thisproject,
		success: function (data) {
			var obj = JSON.parse(data);
			// Clear votes & ticks average & size
			if (obj.c3284d0f94606de1fd2af172aba15bf3==-1){
				$(".hideit").hide();
				$(".clearit").text('');
				$('input[name="Size"]').prop('checked', false);
			}

			// tick anybody that has voted since last clear
			if (obj.c3284d0f94606de1fd2af172aba15bf3==0){
				$.each( obj, function( key, value ) {
					if (value != false){
						$("#t"+key).show();
					}else{
						$("#t"+key).hide();
					}
				});

			}
			if (obj.c3284d0f94606de1fd2af172aba15bf3==1){
			// show the votes and an average
				var estave=0;
				var estcnt=0;
				$.each( obj, function( key, value ) {
					if (value != false){
						$("#s"+key).html('<b>'+value+'</b>');
					//	$("#t"+key).hide();
						if (key !="c3284d0f94606de1fd2af172aba15bf3"){
							estave = estave + Number(value);
							estcnt = estcnt + 1;
						}
					}
				});
				// round to 1 decimal place
				estave= Math.round(estave / estcnt * 10)/10;
				if (estave > 0){
					$("#estave").html('<b>'+estave+'</b>');
				}
			}
		}

	});
setTimeout(refresh, 3000);
}

setTimeout(refresh, 3000);