$(function() {
var thisproject=$( "div" ).find( ".thisproject" ).prop("id");


	if ($('.dupestory').prop("id").substring(3)>0)
	{
		$('.dupestory').show();
	}


	$('.dupestory').mouseover(function() {
		$('html, body').css("cursor", "pointer");
	});
	$('.dupestory').mouseout(function() {
		$('html, body').css("cursor", "default");
	});


	// Duplicate the story
	$('.dupestory').click(function() {
		var thisstory='SAID='+$('.dupestory').prop("id").substring(3);
		// are we duplicating the tasks as well
		if ($(this).prop("id").substring(0,3)=='dut')
		{
			thisstory=thisstory+'&TASKS=True';
		}
		$.ajax({
			type: "GET",
			url: "story_Duplicate.php",
			data: thisstory,
			success: function (data) {
				$('#msg_div').html(data);
        		}
		});
	});
	



	$('#singleFieldTags').tagit({	
		autocomplete: {delay: 0, minLength: 0},
		tagSource:function( request, response ) {
			$.ajax({
				// get existing tags for this project
				type: "GET",
				url: "project_GetTags.php",
				data: 'PID='+thisproject,
				success: function (data) {
					var bgr=data.substring(2).split(",");
					arrx = $.grep(bgr, function( a ) {
						if(a.substr(0,request.term.length).toLowerCase() == request.term.toLowerCase())
						{
							return a;
						}		
					});
					response (arrx);
	        		}
			});
		},
		tagLimit: 20,
		onTagClicked: function(event,ui){
			var thisurl = 'story_List.php?searchstring=tag:'+ui.tagLabel+'&PID='+thisproject+'&Type=search';
			window.location = thisurl;
		}
	});




        /*
            
The below table contains a list of the missing "built-in" toolbar buttons that can be used for the jhtml control.
  
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
});
