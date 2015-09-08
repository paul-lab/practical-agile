$(function() {

// iaudit
       	$('.auditpopup').click(function() {
		var thisstory = $(this).prop("id").substring(5);
		if ($('#allaudits_'+thisstory).is(":hidden")){
			$.ajax({
				type: "GET",
				url: "audit_List.php",
				data: 'aid='+thisstory, 
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