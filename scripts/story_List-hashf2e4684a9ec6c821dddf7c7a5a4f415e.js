jQuery(document).ready(function(){var a=$("div").find(".thisproject").prop("id");var b=$("div").find(".thisiteration").prop("id");$("#LIID").change(function(){if($(this).val()!=$("#RIID").val()&&JisReadonly==0){$.ajax({type:"GET",url:"iteration_Planning_get.php",data:"PID="+a+"&IID="+$(this).val()+"&LorR=left",success:function(c){var d=c.split("{6B89778E-1B36-4E75-A7F2-301656217750}");$(".LIID").html(d[0]);$("#leftsize").text(" Total: "+d[1]+" pts.");bloop()}})}else{$(this).val("");$(".LIID").html("")}});$("#RIID").change(function(){if($(this).val()!=$("#LIID").val()&&JisReadonly==0){$.ajax({type:"GET",url:"iteration_Planning_get.php",data:"PID="+a+"&IID="+$(this).val()+"&LorR=right",success:function(c){var d=c.split("{6B89778E-1B36-4E75-A7F2-301656217750}");$(".RIID").html(d[0]);$("#rightsize").text(" Total: "+d[1]+" pts.");bloop()}})}else{$(this).val("");$(".RIID").html("")}});bloop();if(getParameterByName("LeftIID")){$("#LIID").val(getParameterByName("LeftIID")).change()}if(getParameterByName("RightIID")){$("#RIID").val(getParameterByName("RightIID")).change()}});function bloop(){$(function(){var b=0;var d=$("div").find(".thisproject").prop("id");var e=$("div").find(".thisiteration").prop("id");var c=3;$("#"+c+"line").hide();f(c);var a=1;$("#1line").click(function(){f(1)});$("#2line").click(function(){f(2)});$("#3line").click(function(){f(3)});function f(g){switch(g){case 1:$("#1line").hide();$("#2line").show();$("#3line").show();$(".line-2-div").hide();$(".line-3-div").hide();c=1;return;case 2:$("#2line").hide();$("#1line").show();$("#3line").show();$(".line-2-div").hide();$(".line-3-div").show();c=2;return;default:$("#3line").hide();$("#1line").show();$("#2line").show();$(".line-2-div").show();$(".line-3-div").show();c=3}}$("#sortable-left, #sortable-right").sortable({dropOnEmpty:true,connectWith:".connectedSortable"}).disableSelection();$("#sortable-left, #sortable-right").sortable({update:function(g,h){var i=$(this).prop("id");LeftIID=$("#LIID").val();RightIID=$("#RIID").val();if(h.position.top>h.originalPosition.top){rank="d"}else{if(h.position.top<h.originalPosition.top){rank="i"}else{rank="s"}}if(h.position.left>h.originalPosition.left){newiid=RightIID;oldiid=LeftIID;mov="ltr"}else{if(h.position.left<h.originalPosition.left){newiid=LeftIID;oldiid=RightIID;mov="rtl"}else{mov="same"}}if(mov!="same"){if(i=="sortable-left"){$.ajax({type:"GET",url:"update_storyiteration.php",data:"PID="+d+"&AID="+h.item[0].id.substring(6)+"&IID="+newiid+"&OIID="+oldiid+"&mov="+mov,success:function(j){$("#rightsize").text(" Total: "+j+" pts.")}})}}if(i=="sortable-left"&&mov=="rtl"){$.ajax({type:"GET",url:"update_storyorder.php",data:$("#sortable-left").sortable("serialize")+"&PID="+d+"&AID="+h.item[0].id.substring(6)+"&rank="+rank})}if(i=="sortable-right"&&mov=="ltr"){$.ajax({type:"GET",url:"update_storyorder.php",data:$("#sortable-right").sortable("serialize")+"&PID="+d+"&AID="+h.item[0].id.substring(6)+"&rank="+rank})}}});$("#sortable").sortable({update:function(g,h){if(JisReadonly==0){if(h.position.top>h.originalPosition.top){rank="d"}else{rank="i"}$.ajax({type:"GET",url:"update_storyorder.php",data:$("#sortable").sortable("serialize")+"&PID="+d+"&AID="+h.item[0].id.substring(6)+"&rank="+rank})}}});$("#sortable, #sortable-left, #sortable-right").sortable("option","handle",".storystatus");$("#status1, #status2, #status3, #status4,#status5, #status6,#status7, #status8,#status9, #status10").sortable({receive:function(g,h){if(JisReadonly==0){$.ajax({type:"GET",url:"update_boardstorystatus.php",data:"PID="+d+"&AID="+h.item.attr("id")+"&STAID="+this.id.substring(6)+"&IID="+$(this).attr("name")})}},items:"li:not(.scrumtitle)",start:function(g,h){},connectWith:".connectedSortable"}).disableSelection();$(".scrumdetail").dblclick(function(){window.location.href="story_Edit.php?PID="+d+"&AID="+$(this).attr("id")+"&IID="+e});$(".storybox-div").dblclick(function(){var i=$('select[name="LIID"]').val();var g=$('select[name="RIID"]').val();var h="";if(i>0||g>0){h="&gobackto="+escape("iteration_Planning.php?PID="+d+"&IID="+e+"&LeftIID="+i+"&RightIID="+g)}window.location.href="story_Edit.php?PID="+d+"&AID="+$(this).attr("id").substring(8)+"&IID="+e+h});$(".storybox-div").mouseover(function(){$("#menu_div_"+$(this).attr("id").substring(8)).css("visibility","visible")});$(".storybox-div").mouseout(function(){$("#menu_div_"+$(this).attr("id").substring(8)).css("visibility","hidden")});$(".storystatus").mouseover(function(){$("html, body").css("cursor","move")});$(".storystatus").mouseout(function(){$("html, body").css("cursor","default")});$(".statuspopup").mouseover(function(){$("html, body").css("cursor","pointer")});$(".statuspopup").mouseout(function(){$("html, body").css("cursor","default")});$(".storybox-div").click(function(){b=$(this).attr("id").substring(8)});$(".iterationdialog").dialog({autoOpen:false,resizable:false,modal:true});$(".iterationpopup").click(function(){$(".iterationdialog").dialog("open");$(".iterationdialog").dialog().dialog("widget").position({my:"left",at:"right",of:$(this)})});$(".iterationdialog .ui-button").click(function(){$(".iterationdialog").dialog("close");if(JisReadonly==0){$.ajax({type:"GET",url:"update_storyiteration.php",data:"PID="+d+"&AID="+b+"&IID="+$(this).attr("id")+"&OIID="+$(this).parent().attr("id").substring(5),success:function(){$("#story_"+b).hide()}})}});$(".statusdialog").dialog({autoOpen:false,resizable:false,modal:true});$(".statuspopup").click(function(){$(".statusdialog").dialog("open");$(".statusdialog").dialog().dialog("widget").position({my:"left",at:"right",of:$(this)})});$(".statusdialog .ui-button").click(function(){var g=$(this).css("background-color");$(".statusdialog").dialog("close");if(JisReadonly==0){$.ajax({type:"GET",url:"update_storystatus.php",data:"PID="+d+"&AID="+b+"&SAID="+$(this).attr("id")+"&IID="+$(this).parent().attr("id").substring(6),success:function(h){$("#status_div"+b).text(h);$("#span_div"+b).css("background",g);$("#status_div"+b).css("background",g)}})}});$(".quickview").click(function(){if($("#line-2-div"+$(this).prop("id").substring(9)).css("max-height")=="28px"){$("#line-2-div"+$(this).prop("id").substring(9)).show();$("#line-3-div"+$(this).prop("id").substring(9)).show();$("#line-2-div"+$(this).prop("id").substring(9)).css("max-height","999em")}else{$("#line-2-div"+$(this).prop("id").substring(9)).css("max-height","28px")}});$(".tree").fancytree({extensions:["dnd"],activeVisible:true,aria:false,autoActivate:true,autoScroll:false,clickFolderMode:2,checkbox:false,debugLevel:0,fx:null,icons:false,keyboard:true,keyPathSeparator:"/",minExpandLevel:1,selectMode:1,tabbable:true,titlesTabbable:true,init:function(h,i,g){$(".tree").fancytree("getRootNode").visit(function(j){j.setExpanded(true)});f(a)},activate:function(h,i){var g=i.node;$("#echoActive").text(g.title)},keydown:function(g,h){switch(g.which){case 32:h.node.toggleSelected();return false}},dblclick:function(g,h){if(h.node.parent.data.key!="_1"){window.location.href="story_Edit.php?PID="+h.node.data.pid+"&AID="+h.node.key+"&IID="+h.node.data.iid}},dnd:{preventVoidMoves:true,preventRecursiveMoves:true,autoExpandMS:600,dragStart:function(g,h){goldparent=g.parent.data.key;if(g.data.nodndflag=="nodnd"){return false}else{return true}},dragEnter:function(g,h){if(g.data.iteration!="Backlog"){return"after"}if(g.parent.data.key=="_1"){return false}return true},dragDrop:function(i,j){updateit=true;if(j.hitMode+i.hasChildren()==="overfalse"){updateit=confirm("You are creating a new Parent Story\n Is this really what you want to do?")}if(updateit==true&&JisReadonly==0){goldparent=j.otherNode.parent.key;j.otherNode.moveTo(i,j.hitMode);if(j.hitMode=="over"){gnewparent=i.key}else{gnewparent=i.parent.key}if(goldparent!=gnewparent){$.ajax({type:"GET",url:"update_storyparent.php",data:"PID="+d+"&SID="+j.otherNode.key+"&NPAR="+gnewparent+"&OPAR="+goldparent,success:function(l){i.setExpanded(true);f(c)}})}var k=i.parent.toDict(true);var h="";for(var g in k.children){h=h+"story[]="+k.children[g].key+"&"}$.ajax({type:"GET",url:"update_epicstoryorder.php",data:h})}}}});$(".btnCollapseAll").click(function(){$("#tree"+$(this).prop("id")).fancytree("getRootNode").visit(function(g){g.setExpanded(false)})});$(".btnExpandAll").click(function(){$("#tree"+$(this).prop("id")).fancytree("getRootNode").visit(function(g){g.setExpanded(true)})})})}function getParameterByName(c){var e=[],d;var a=window.location.href.slice(window.location.href.indexOf("?")+1).split("&");for(var b=0;b<a.length;b++){d=a[b].split("=");e.push(d[0]);e[d[0]]=d[1]}return e[c]};