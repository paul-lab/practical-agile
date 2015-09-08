$(function() {

// iaudit
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