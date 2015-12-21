$(document).ready(function(){

// Audit Stuff
	var array;
	var toggle;
	var thisproject=$( "div" ).find( ".thisproject" ).prop("id");

       	$('.auditpopup').click(function() {
		var thisstory = $(this).prop("id").substring(6);
		var typ='';
		if($(this).prop("id").substring(5,6)==='s'){
			typ='AID';
		}else{
			typ='PID';
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


//  display/hide the comments block
       	$('.commentpopup').click(function() {

		// if we already have visible comments on this page then get rid of them
		if($.isArray(array)){
			if ($('#commentspop'+array[0]+'_'+array[1]).is(":visible")){
				toggle = array[1];
				$('#commentspop'+array[0]+'_'+array[1]).empty();
				$('#commentspop'+array[0]+'_'+array[1]).hide();
			}
		}
		array=$(this).prop("id").substring(7).trim().split('_');
		array[0];
		array[1]*=1;

		// Show/Hide comments for a story
		if (toggle!=array[1]){
			$.ajax({
				type: "GET",
				url: "comment_List.php",
				data: 'id='+array[1]+'&key='+array[0],
				success: function (data) {
					$('#commentspop'+array[0]+'_'+array[1]).append(data);
					// add a comment
					$(".submit_button").click(function() {
						var id = $(this).attr("id").substring(15);
						var idx = $(this).attr("id").substring(13);
						var citer=$("#CIteration_ID").val();
						var ctext = escape($('#comment_text_'+id).htmlarea('toHtmlString'));
						if (ctext.length > 7000){
							ctext=ctext.substring(0,7000);
							alert ('Comment Truncated');
						}
				 		$("#Story_AID").attr("value", id);
						var parid=$("#Parent_ID_"+idx).val();
						$('div#replyto_'+id).text('');
						var data='PID='+thisproject+'&Parent_ID='+parid+'&Iteration_ID='+citer+'&User_Name='+$("#User_Name_"+id).val()+'&Story_AID='+$("#Story_AID_"+id).val()+'&replyid='+idx.substring(2)+'&Type='+idx.substring(0,1)+'&comment_text='+ctext;
						if (JisReadonly===0){
							$.ajax({
								type: "GET",
								url: "comment_Add.php",
								data: data,
								success: function (data) {
									updatecommentcount($( "#comment_count_"+idx ),1);
									if(parid!=0){
										$('#commentreply_'+parid).append(data);
										// reset parent ID and hide parent delete
										$('#deletecomment'+parid).hide();
										$("#Parent_ID_"+idx).attr("value", 0); 
										$('div#replyto_'+idx).text('');
									}else{
										$('#commentlist'+idx).append(data);
									} 
					      			}
							});
						}
					});
				// Delete a single comment
					$('#commentspop'+array[0]+'_'+array[1]).on('click', '.deletecomment', function() {						
						var cid=$(this).attr("id").substring(15);
						var idx3="#comment_count_"+array[0]+'_'+array[1];
						if (JisReadonly===0){
							$.ajax({
								type: "GET",
								url: "comment_Delete.php",
								data: 'id='+cid+'&PID='+thisproject+'&AID='+array[1]+'&type='+array[0],
								success: function (data) {
									if (data.trim()=='1'){
										$('#comment_'+cid).remove();
										updatecommentcount($(idx3 ),-1);
									}
								}
							});
						}
				      	});

				//set the id for parent if this is a reply to a comment  and hide the delete
					$('#commentspop'+array[0]+'_'+array[1]).on('click', '.reply', function() {
						var id = $(this).attr("id"); 
						$('#deletecomment'+array[0]+'_'+id).hide();
						var aid=$(this).attr("href").substring(12);
						$('div#replyto_'+aid).text('Reply to:'+$('div#comment_body_'+id).text().substring(0,50)+'...');   
						$("#Parent_ID_"+aid).attr("value", id); 
					});


        			/*
       				The below table contains a list of the missing "built-in" toolbar buttons that can be used.
  
				p  Creates a new paragraph "<p>"  
				h1  Surrounds the selected text with a <h1> tag.  
				h2  Surrounds the selected text with a <h2> tag.  
				h3  Surrounds the selected text with a <h3> tag.  
				h4  Surrounds the selected text with a <h4> tag.  
				h5  Surrounds the selected text with a <h5> tag.  
				h6  Surrounds the selected text with a <h6> tag.  
				image  Allows for an image to be inserted into the current caret location.  
			        */
					$("textarea").htmlarea({
						toolbar: [
							["html"],
						        ["bold", "italic", "underline","strikethrough","superscript", "subscript"],
							["cut","copy","paste"],
						        ["increasefontsize", "decreasefontsize"],["forecolor"],
							["orderedList","unorderedList"],
							["indent", "outdent","justifyleft", "justifycenter","justifyright"],
						        ["link", "unlink"]
						]
					});
				}
			});
			$('#commentspop'+array[0]+'_'+array[1]).show();	
		}else{
			$('#commentspop'+array[0]+'_'+array[1]).empty();
			$('#commentspop'+array[0]+'_'+array[1]).hide();
			toggle=0;
		}
      	});


	function updatecommentcount(commnt,updown){

		if (typeof commnt!='undefined'){
			updown*=1;
			var ccnt=commnt.html().trim().slice(1,-1)*1;
			ccnt+=updown;
			commnt.html(' ('+ccnt+')')
		}
	}



// iTask
       	$('.taskpopup').click(function() {
		var thisstory = $(this).prop("id");
		if ($('#alltasks_'+thisstory).is(":hidden")){
			$.ajax({
				type: "GET",
				url: "task_List.php",
				data: 'pid='+thisproject+'&aid='+thisstory, 
				success: function (data) {
					$('#alltasks_'+thisstory).append(data);
					// hide the task save button
					$(".savetask").hide();		
					$('.taskdialog').find( ".indet1" ).prop('indeterminate',true);	
	 				var qwe=$('#sortabletask'+thisstory).sortable({
						update: function(event, ui) {
						$.ajax({
							type: "GET",
							url: "update_taskorder.php",
							data: $(qwe).sortable("serialize")
							});
						}
					});

					$('.deletetask').mouseover(function() {
						$('html, body').css("cursor", "pointer");
					});
					$('.deletetask').mouseout(function() {
						$('html, body').css("cursor", "default");
					});
					$(".deletetask").click(function() {
						if (JisReadonly===0){
							var thistask =($(this).parent().attr('id').substring(5));
							var thisstate=$("#done_"+thistask).prop('value');
							if (thisstate==0){thisstate=1;}
							var des=$('#desc_'+thistask).val();
							$(this).parent().remove();
							$.ajax({
								type: "GET",
								url: "task_Delete.php",
								data: 'id='+thistask+'&PID='+thisproject+'&AID='+thisstory+'&desc='+des,
								success: function () {
										updatetaskcount($( "#task_count_"+thisstory ),thisstate);
								} 
							});
						}
					});
				// update existing task done state
					$(".divRow .done").click(function() {
						if (JisReadonly===0){
							var thistask=$(this).attr("id").substring(5);
							toggletaskstate($(this));
							var thisstatus=$(this).prop('value');
							var taskstatus=thisstatus*10;
							updatetaskcount($( "#task_count_"+thisstory ),taskstatus);
							$.ajax({
								type: "GET",
								url: "update_task_Status.php",
								data: 'TID='+thistask+'&DONE='+thisstatus+'&PID='+thisproject+'&AID='+thisstory+'&desc='+$('#desc_'+thistask).val()
							});
						}
					})
				// edit existing task
					$('.edittask').mouseover(function() {
						$('html, body').css("cursor", "pointer");
					});
					$('.edittask').mouseout(function() {
						$('html, body').css("cursor", "default");
					});
					$(".divRow .edittask").click(function() {
						if (JisReadonly===0){
							$(this).hide();
					 		$(this).parent().find('.savetask').show();
							var id=$(this).attr("id").substring(9);
							$("#desc_"+id).prop("disabled", false);
							$("#user_"+id).prop("disabled", false);
							$("#expected_"+id).prop("disabled", false);
							$("#actual_"+id).prop("disabled", false);
						}
					});

				// update existing task in story
					$('.savetask').mouseover(function() {
						$('html, body').css("cursor", "pointer");
					});
					$('.savetask').mouseout(function() {
						$('html, body').css("cursor", "default");
					});
	
					$(".divRow .savetask").click(function() {
						$(this).hide();
				 		$(this).parent().find('.edittask').show();
						var id=$(this).attr("id").substring(9);
						$("#desc_"+id).prop("disabled", true);
						$("#user_"+id).prop("disabled", true);
						$("#expected_"+id).prop("disabled", true);
						$("#actual_"+id).prop("disabled", true);
						var updatetask='TID='+id;
						updatetask +='&PID='+thisproject+'&AID='+thisstory;
						updatetask += '&desc='+ $('#desc_'+id).val();
						updatetask += '&user='+ $('#user_'+id).val();
						updatetask += '&exph='+ $('#expected_'+id).val();
						updatetask += '&acth='+ $('#actual_'+id).val();
						$.ajax({
							type: "GET",
							url: "task_Update.php",
							data: updatetask
						});

					});


				// add a new task
					$('.savenew').mouseover(function() {
						$('html, body').css("cursor", "pointer");
					});
					$('.savenew').mouseout(function() {
						$('html, body').css("cursor", "default");
					});

					$(".savenew").click(function() {
						if (JisReadonly===0){
							var thisstory=$(this).parent().attr("id").substring(7);
							var newtask = 'AID='+ thisstory;
							newtask += '&PID='+thisproject;
							newtask += '&desc='+ $('#ndesc_'+thisstory).val();
							newtask += '&user='+ $('#taskuser_'+thisstory).val();
							newtask += '&exph='+ $('#nexph_'+thisstory).val();
							newtask += '&acth='+ $('#nacth_'+thisstory).val();
							$.ajax({
								type: "GET",
								url: "task_Add.php",
								data: newtask,
								success: function (data) {
									$('#alltasks_'+thisstory).empty();
									$('#alltasks_'+thisstory).hide();
									$('#'+thisstory).click();
									updatetaskcount($( "#task_count_"+thisstory ),30)
					        		}
							});
						}
					});
				}
			});	

			$('#alltasks_'+thisstory ).show();	
		}else{
			$('#alltasks_'+thisstory).empty();
			$('#alltasks_'+thisstory).hide();
		}
        });


	function updatetaskcount(tsk,act){
		act*=1;
		var array=tsk.html().trim().slice(1,-1).split('/');		
		array[0]*=1;	
		if(isNaN(array[1])){array[1]=0;};
		array[1]*=1;
		switch(act){
			case 2:
				array[0]-=1;
				array[1]-=1;
				break; 
			case 20:
				array[0]+=1;
				break; 
			case 0:
				array[0]-=1;
				break;
			case 1:
				array[1]-=1;	
				break;
			case 30:
				array[1]+=1;
		}

		tsk.html('('+array[0]+'/'+array[1]+')')
	}

	function toggletaskstate(cb) {
		switch(cb.prop("value")) {
		// unchecked, going indeterminate
	            case '0': 
			cb.prop('checked',false);
	                cb.prop('indeterminate',true);
	 		cb.prop('value',1);
	                break;
	
		// indeterminate, going checked
	            case '1': 
			cb.prop('checked',true);
	                cb.prop('indeterminate',false);
	 		cb.prop('value',2);
	                break;
	
		// checked, going unchecked
		    default: 
			cb.prop('checked',false);
	                cb.prop('indeterminate',false);
			cb.prop('value',0);
			};
	}




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
						if (JisReadonly===0){
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
						if (JisReadonly===0){
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