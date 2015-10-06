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


	$('#search').click(function() {
	var data='SSTR='+$('#searchstring').val()+'?type="search"'
		$.ajax({
			type: "GET",
			url: "search.php",
			data: data,
			success: function (data) {
				
			}
		});
	}); 

	$('.hideSummary').click(function() {
		$('.chart_div').hide();
		$('.SummaryTable').hide();
		$('.hideSummary').hide();
		$('.showSummary').show();
	});

	$('.showSummary').click(function() {
		$('.chart_div').show();
		$('.SummaryTable').show();
		$('.hideSummary').show();
		$('.showSummary').hide();
	});

}); 



