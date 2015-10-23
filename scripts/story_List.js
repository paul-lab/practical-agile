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

jQuery(document).ready(function () {

	var thisproject=$( "div" ).find( ".thisproject" ).prop("id");
	var thisiteration=$( "div" ).find( ".thisiteration" ).prop("id");

// keep these here to stop repeated firing

	// get the list of cards for the left hand planning page iteration if a selection has been made and it is not the same as the other panel
	$('#LIID').change(function(){
		if($(this).val()!=$('#RIID').val() && JisReadonly==0)
		{
			$.ajax({
				type: "GET",
				url: "iteration_Planning_get.php",
				data: 'PID='+thisproject+'&IID='+$(this).val()+'&LorR='+'left',
				success: function (data) {
					var outs=data.split('{6B89778E-1B36-4E75-A7F2-301656217750}');
					$(".LIID").html(outs[0]);
					$("#leftsize").text(' Total: '+outs[1]+' pts.');
					// re-init cos we just added a whole load of stuff
					bloop();
				}
			});
		}else{
			$(this).val('');
			$(".LIID").html('');
		}
	});





	// get the list of cards for the right hand planning itration if a selection has been made and it is not the same as the other panel
	$('#RIID').change(function(){
		if($(this).val()!=$('#LIID').val() && JisReadonly==0)
		{
			$.ajax({
				type: "GET",
				url: "iteration_Planning_get.php",
				data: 'PID='+thisproject+'&IID='+$(this).val()+'&LorR='+'right',
				success: function (data) {
					var outs=data.split('{6B89778E-1B36-4E75-A7F2-301656217750}');
					$(".RIID").html(outs[0]);
					$("#rightsize").text(' Total: '+outs[1]+' pts.');
					// re-init cos we just added a whole load of stuff
	 				bloop();
				}
			});
		}else{
			$(this).val('');
			$(".RIID").html('');
		}
	});


    // Initialise the plugin when the DOM is ready to be acted upon
    	bloop();

	// if we are passed backfrom a story edit page fetch the l&r iteratins again
	if (getParameterByName('LeftIID'))
	{
		$('#LIID').val( getParameterByName('LeftIID')).change();
	}

	if (getParameterByName('RightIID'))
	{
		$('#RIID').val( getParameterByName('RightIID')).change();
	}


});


function bloop(){

	$(function() {

	var gstoryid=0;
	
	//$('#LIID').val( getParameterByName('LeftIID'));
	//$('#RIID').val( getParameterByName('RightIID'));

	var thisproject=$( "div" ).find( ".thisproject" ).prop("id");
	var thisiteration=$( "div" ).find( ".thisiteration" ).prop("id");


//default to showing 3 lines when in the list view (options are 1,2,3)
	var nlines=3;
	$("#"+nlines+"line").hide();
	showLines(nlines);
	

//default to only showing a single summary line when in the tree view
	var treelines=1

	$('#1line').click(function() {
		showLines(1);
	});

	$('#2line').click(function() {
		showLines(2);
	});

	$('#3line').click(function() {
		showLines(3);
	});

function showLines(n){
	switch(n){
		case 1:
			$("#1line").hide();
			$("#2line").show();
			$("#3line").show();
			$(".line-2-div").hide();
			$(".line-3-div").hide();
			nlines=1;
			return;
		case 2:
			$("#2line").hide();
			$("#1line").show();
			$("#3line").show();
			$(".line-2-div").hide();
			$(".line-3-div").show();
			nlines=2;
			return;

		default:
			$("#3line").hide();
			$("#1line").show();
			$("#2line").show();
			$(".line-2-div").show();
			$(".line-3-div").show();	
			nlines=3;
	}
}


// sortable iteration table

	$( "#sortable-left, #sortable-right" ).sortable({
		dropOnEmpty: true,
      		connectWith: ".connectedSortable"
	}).disableSelection();


	// this only applies to the sprint planning page.
	$( "#sortable-left, #sortable-right" ).sortable({
		update: function(event, ui) {

			// what has triggered this update.
			var wherearewe = $(this).prop("id");

			LeftIID  = $('#LIID').val();
			RightIID = $('#RIID').val();
			// see what has happened with the rank & which way things have moved.
			if (ui.position.top>ui.originalPosition.top)
			{
				rank='d';
			}else{
				if (ui.position.top<ui.originalPosition.top)
				{
					rank='i';
				}else{
					rank='s'
				}
			}
	// has this moved rtl or ltr
			if (ui.position.left>ui.originalPosition.left)
			{
				newiid=RightIID;
				oldiid=LeftIID;
				mov='ltr';
			}else{
				if(ui.position.left<ui.originalPosition.left){
					newiid=LeftIID;
					oldiid=RightIID;
					mov='rtl';
				}else{
					mov='same';
				}
			}
			// if iteration change
			if (mov!='same')	
			{
				// only move & audit it once
				if (wherearewe=='sortable-left')
				{
					$.ajax({
						type: "GET",
						url: "update_storyiteration.php",
						data: 'PID='+thisproject+'&AID='+ui.item[0].id.substring(6)+'&IID='+newiid+'&OIID='+oldiid+'&mov='+mov,
						success: function (data) {
							$("#rightsize").text(' Total: '+data+' pts.');
						}
					});
				}
			}

			// only  update the rank and audit if we need to
			if (wherearewe=='sortable-left' && mov=='rtl')
			{
				$.ajax({
					type: "GET",
					url: "update_storyorder.php",
					data: $("#sortable-left").sortable("serialize")+'&PID='+thisproject+'&AID='+ui.item[0].id.substring(6)+'&rank='+rank
				});
			}

			// only  update the rank and audit if we need to
			if (wherearewe=='sortable-right' && mov=='ltr')
			{
				$.ajax({
					type: "GET",
					url: "update_storyorder.php",
					data: $("#sortable-right").sortable("serialize")+'&PID='+thisproject+'&AID='+ui.item[0].id.substring(6)+'&rank='+rank
				});
			}
		}
	
	});


	$( "#sortable" ).sortable({
		update: function(event, ui) {
			if (JisReadonly==0)
			{
				// see what has happened with the rank
				if (ui.position.top>ui.originalPosition.top)
				{
					rank='d';
				}else{
					rank='i';
				}
				$.ajax({
					type: "GET",
					url: "update_storyorder.php",
					data: $("#sortable").sortable("serialize")+'&PID='+thisproject+'&AID='+ui.item[0].id.substring(6)+'&rank='+rank
					});
				}
			}
	});

	$( "#sortable, #sortable-left, #sortable-right" ).sortable( "option", "handle", ".storystatus" );

	// this is to support the scrum board status change.
	$( "#status1, #status2, #status3, #status4,#status5, #status6,#status7, #status8,#status9, #status10" ).sortable({
		receive: function(event, ui) {
		if (JisReadonly==0)
		{
				$.ajax({
					type: "GET",
					url: "update_boardstorystatus.php",
					data: 'PID='+thisproject+'&AID='+ui.item.attr("id")+'&STAID='+this.id.substring(6)+'&IID='+$(this).attr("name")
				});
		}
	    	},
		items: "li:not(.scrumtitle)",
		start: function( event, ui ) {},
		connectWith: ".connectedSortable"
	}).disableSelection();

// double click edit on scum board
	$(".scrumdetail").dblclick(function() {
		window.location.href="story_Edit.php"+'?PID='+thisproject+'&AID='+$(this).attr("id")+'&IID='+thisiteration;
	});

// double click edit on sortable list
	$(".storybox-div").dblclick(function() {

// are we in iteration planning
		var LeftIID  = $('select[name="LIID"]').val();
		var RightIID = $('select[name="RIID"]').val();
		var gbt='';
		if(LeftIID>0 || RightIID>0)
		{
			gbt='&gobackto='+escape('iteration_Planning.php?PID='+thisproject+'&IID='+thisiteration+'&LeftIID='+LeftIID+'&RightIID='+RightIID);
		}

		window.location.href="story_Edit.php"+'?PID='+thisproject+'&AID='+$(this).attr("id").substring(8)+'&IID='+thisiteration+gbt;
	});


// show hide 	.menu_div
	$('.storybox-div').mouseover(function() {
		$('#menu_div_'+$(this).attr("id").substring(8)).css('visibility', 'visible');
	});
	$('.storybox-div').mouseout(function() {
		$('#menu_div_'+$(this).attr("id").substring(8)).css('visibility', 'hidden')
	});

// D & D cursor
	$('.storystatus').mouseover(function() {
		$('html, body').css("cursor", "move");
	});
	$('.storystatus').mouseout(function() {
		$('html, body').css("cursor", "default");
	});

// Status
	$('.statuspopup').mouseover(function() {
		$('html, body').css("cursor", "pointer");
	});
	$('.statuspopup').mouseout(function() {
		$('html, body').css("cursor", "default");
	});


// find the id of the story we are busy working with in the story listview
	$('.storybox-div').click(function() {
		gstoryid = $(this).attr("id").substring(8);
	}); 

// iteration select minimenu setup
	$( ".iterationdialog" ).dialog({
		autoOpen: false,
		resizable: false,
		modal: true
	});  

// iteration select minimenu open
       	$('.iterationpopup').click(function() {
        	$('.iterationdialog').dialog('open');
    		$(".iterationdialog").dialog().dialog('widget').position({
			my: 'left', 
			at: 'right', 
			of: $(this) 
		});
        });

	$('.iterationdialog .ui-button').click(function () {
		$('.iterationdialog').dialog('close');
		if (JisReadonly==0)
		{
			$.ajax({
				type: "GET",
				url: "update_storyiteration.php",
				data: 'PID='+thisproject+'&AID='+gstoryid+'&IID='+$(this).attr('id')+'&OIID='+$(this).parent().attr("id").substring(5),
				success: function () {
			                $('#story_'+gstoryid).hide();
				}
			});
		}
	});

// Status change

	$( ".statusdialog").dialog({
		autoOpen: false,
		resizable: false,
		modal: true
	});  

       	$('.statuspopup').click(function() {

        	$('.statusdialog').dialog('open');
    		$(".statusdialog").dialog().dialog('widget').position({
			my: 'left', 
			at: 'right', 
			of: $(this) 
		});
        });

	$('.statusdialog .ui-button').click(function () {
		var color = $(this).css("background-color");
		$('.statusdialog').dialog('close');
		if (JisReadonly==0)
		{
// SAID is Status text not an id
			$.ajax({
				type: "GET",
				url: "update_storystatus.php",
				data: 'PID='+thisproject+'&AID='+gstoryid+'&SAID='+$(this).attr('id')+'&IID='+$(this).parent().attr("id").substring(6),
				success: function (data) {
					$("#status_div"+gstoryid).text(data);
					$("#span_div"+gstoryid).css("background",color );
					$("#status_div"+gstoryid).css("background",color );
				}
			});
		}
	});

// Quickview
       	$('.quickview').click(function() {
		if ($("#line-2-div"+$(this).prop("id").substring(9)).css("max-height") == "28px")
		{
			$("#line-2-div"+$(this).prop("id").substring(9)).show();
			$("#line-3-div"+$(this).prop("id").substring(9)).show();
			$("#line-2-div"+$(this).prop("id").substring(9)).css("max-height","999em" );
		}else{ 
			$("#line-2-div"+$(this).prop("id").substring(9)).css("max-height","28px" );
		}
        });


	$(".tree").fancytree({
		extensions: ["dnd"],
		activeVisible: true, // Make sure, active nodes are visible (expanded).
  		aria: false, // Enable WAI-ARIA support.
    		autoActivate: true, // Automatically activate a node when it is focused (using keys).
    		autoScroll: false, // Automatically scroll nodes into visible area.
    		clickFolderMode: 2, // 1:activate, 2:expand, 3:activate and expand, 4:activate (dblclick expands)
    		checkbox: false, // Show checkboxes.
    		debugLevel: 0, // 0:quiet, 1:normal, 2:debug
		fx: null,
    		icons: false, // Display node icons.
    		keyboard: true, // Support keyboard navigation.
    		keyPathSeparator: "/", // Used by node.getKeyPath() and tree.loadKeyPath().
    		minExpandLevel: 1, // 1: root node is not collapsible
   		selectMode: 1, // 1:single, 2:multi, 3:multi-hier
    		tabbable: true, // Whole tree behaves as one single control
    		titlesTabbable: true,  // Node titles can receive keyboard focus
   
 	     	init: function(event, data, flag) {
			$(".tree").fancytree("getRootNode").visit(function(node){
 				node.setExpanded(true);
      			});
			showLines(treelines);
		},

     		activate: function(event, data) {
        		var node = data.node;
		        // acces node attributes
		        $("#echoActive").text(node.title);
		},



 	     	keydown: function(event, data) {
        		switch( event.which ) {
        			case 32: // [space]
          			data.node.toggleSelected();
          			return false;
        		}
	      	},
 	     	 dblclick : function(event, data) {
			if(data.node.parent.data.key != '_1') window.location.href="story_Edit.php"+'?PID='+data.node.data.pid+'&AID='+data.node.key+'&IID='+data.node.data.iid;

	      	},
		dnd: {
		        preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
		        preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
		        autoExpandMS: 600,

		        dragStart: function(node, data) {
		          /** This function MUST be defined to enable dragging for the tree.
		           *  Return false to cancel dragging of node.
		           */
			  goldparent=node.parent.data.key;
		//no dnd for release or iterationo trees.
			if (node.data.nodndflag=='nodnd')
			{
				return false;
			}else{
		        	return true;
			}
	        },

		        dragEnter: function(node, data) {
		          /** data.otherNode may be null for non-fancytree droppables.
		           *  Return false to disallow dropping on node. In this case
		           *  dragOver and dragLeave are not called.
		           *  Return 'over', 'before, or 'after' to force a hitMode.
		           *  Return ['before', 'after'] to restrict available hitModes.
		           *  Any other return value will calc the hitMode from the cursor position.
		           */
// Prevent dropping onto a story that is not in the backlog 
				if (node.data.iteration != 'Backlog') return 'after';
		         	if(node.parent.data.key == '_1'){  return false; }
			           return true;
		        },
		        dragDrop: function(node, data) {

// Just to prevent accidently creating parent stories
		 		updateit=true;	
				if (data.hitMode+ node.hasChildren() === "overfalse")
				{
					updateit = confirm ("You are creating a new Parent Story\n Is this really what you want to do?");
				}
		
				if (updateit==true  && JisReadonly==0)
				{ 
		 			goldparent=data.otherNode.parent.key;
					data.otherNode.moveTo(node, data.hitMode);
					if( data.hitMode=='over'){
						gnewparent=node.key;
					}else{
						gnewparent=node.parent.key;
					}
		 				if (goldparent!=gnewparent){
		 					$.ajax({
		 						type: "GET",
		 						url: "update_storyparent.php",
		 						data: 'PID='+thisproject+'&SID='+data.otherNode.key+'&NPAR='+gnewparent+'&OPAR='+goldparent,
		 						success: function (data) {
// 	TODO update parent points on page 
									node.setExpanded(true);
									showLines(nlines);
		 						}
		 					});
		 				}
		 				var dict = node.parent.toDict(true);
		 				var stories='';
		 				for(var pName in dict.children) {
		 					stories=stories+'story[]='+dict.children [pName].key+'&';
		 				}
		 				$.ajax({
		 					type: "GET",
		 					url: "update_epicstoryorder.php",
		 					data: stories
		 				});
		 		}
		        }
		}
	});

	$(".btnCollapseAll").click(function(){
		$("#tree"+$(this).prop("id")).fancytree("getRootNode").visit(function(node){
       			node.setExpanded(false);
      		});
	});
	$(".btnExpandAll").click(function(){
		$("#tree"+$(this).prop("id")).fancytree("getRootNode").visit(function(node){
			node.setExpanded(true);
		});
		
	});



	});
}


function  getParameterByName(key) {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars[key];
}