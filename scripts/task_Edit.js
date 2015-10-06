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

var thisproject=$( "div" ).find( ".thisproject" ).prop("id");

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
						if (JisReadonly==0)
						{
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
						if (JisReadonly==0)
						{
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
						if (JisReadonly==0)
						{
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
						if (JisReadonly==0)
						{
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

});