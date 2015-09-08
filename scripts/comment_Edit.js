//$(function() {
$(document).ready(function(){
var array;
var toggle;

var thisproject=$( "div" ).find( ".thisproject" ).prop("id");

//  display/hide  of the comments block
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
				 		$("#Story_AID").attr("value", id);
						var parid=$("#Parent_ID_"+idx).val();
						$('div#replyto_'+id).text('');
						var data='PID='+thisproject+'&Parent_ID='+parid+'&Iteration_ID='+citer+'&User_Name='+$("#User_Name_"+id).val()+'&Story_AID='+$("#Story_AID_"+id).val()+'&replyid='+idx.substring(2)+'&Type='+idx.substring(0,1)+'&comment_text='+$("#comment_text_"+id).text();
						if (JisReadonly==0)
						{
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
						if (JisReadonly==0)
						{
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
});