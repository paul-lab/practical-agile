$(function() {

var thisproject=$( "div" ).find( ".thisproject" ).prop("id");

// iupload
       	$('.uploadpopup').click(function() {
		var thisstory = $(this).prop("id").substring(2);
		if ($('#allupload_'+thisstory).is(":hidden")){
			$.ajax({
				type: "GET",
				url: "upload_List.php",
				data: 'PID='+thisproject+'&AID='+thisstory, 
				success: function (data) {
					$('#allupload_'+thisstory).append(data);
					// hide the upload save button
					//$(".saveupload").hide();		


					$('.deleteupload').mouseover(function() {
						$('html, body').css("cursor", "pointer");
					});
					$('.deleteupload').mouseout(function() {
						$('html, body').css("cursor", "default");
					});
					$(".deleteupload").click(function() {
						if (JisReadonly==0)
						{
							var thisupload =($(this).prop('id'));
							var fdet=$(this).parent().text();
							$(this).parent().remove();
							$.ajax({
								type: "GET",
								url: "upload_Delete.php",
								data: 'PID='+thisproject+'&AID='+thisstory+'&Name='+thisupload+'&Type='+$(this).parent().prop('id').substring(7)+'&fdet='+fdet,
								success: function () {
										updateuploadcount($( "#upload_count_"+thisstory),-1);
								} 
							});
						}
					});



				// add a new upload
					$('.uploadnew').mouseover(function() {
						$('html, body').css("cursor", "pointer");
					});
					$('.uploadnew').mouseout(function() {
						$('html, body').css("cursor", "default");
					});

					$(".uploadnew").click(function() {
						if (JisReadonly==0)
						{
							var thisstory=$(this).parent().attr("id").substring(7);
							var file_data = $('#ndesc_'+ thisstory).prop('files')[0];     
							var form_data =new FormData();
							form_data.append('AID', thisstory);
							form_data.append('PID', thisproject);
							form_data.append('file', file_data);
							$.ajax({
								type: "POST",
								url: "upload_Add.php",
	                					dataType: 'text',  // what to expect back from the PHP script, if anything
						                cache: false,
						                contentType: false,
						                processData: false,
						                data: form_data,                         
								success: function (data) 
								{
									if(data.length>5)
									{
										$('#allupload_'+thisstory).append(data);
									}else{
										$('#allupload_'+thisstory).empty();
										$('#allupload_'+thisstory).hide();
										$('#up'+thisstory).click();
										updateuploadcount($( "#upload_count_"+thisstory ),1)
									}
					        		}
							});
						}
					});
				}
			});	

			$('#allupload_'+thisstory ).show();	
		}else{
			$('#allupload_'+thisstory).empty();
			$('#allupload_'+thisstory).hide();
		}
        });


	function updateuploadcount(upload,updown){
		if (typeof upload!='undefined'){
			updown*=1;
			var ccnt=upload.html().trim().slice(1,-1)*1;
			ccnt+=updown;
			upload.html(' ('+ccnt+')')
		}
	}


});