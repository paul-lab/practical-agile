$(function() {

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
       	$('.auditpopup').click(function() {
		var thisstory = $(this).prop("id").substring(6);

		if($(this).prop("id").substring(5,6)=='s')
		{
			var typ='AID';
		}else{
			var typ='PID';
		}
		if ($('#allaudits_'+thisstory).is(":hidden")){
			$.ajax({
				type: "GET",
				url: "audit_List.php",
				data: 'id='+thisstory+'&type='+typ, 
				success: function (data) {
					$('#allaudits_'+thisstory).append(data);				
				}
			});	

			$('#allaudits_'+thisstory ).show();	
		}else{
			$('#allaudits_'+thisstory).empty();
			$('#allaudits_'+thisstory).hide();
		}
        });
});