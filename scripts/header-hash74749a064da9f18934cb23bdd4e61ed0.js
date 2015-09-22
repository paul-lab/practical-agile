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



