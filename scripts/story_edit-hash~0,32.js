
$(function(){var thisproject=$("div").find(".thisproject").prop("id");if($('.dupestory').prop("id").substring(3)>0){$('.dupestory').show();}
$('.dupestory').mouseover(function(){$('html, body').css("cursor","pointer");});$('.dupestory').mouseout(function(){$('html, body').css("cursor","default");});$('.dupestory').click(function(){var thisstory='SAID='+$('.dupestory').prop("id").substring(3);if($(this).prop("id").substring(0,3)=='dut'){thisstory=thisstory+'&TASKS=True';}
$.ajax({type:"GET",url:"story_Duplicate.php",data:thisstory,success:function(data){$('#msg_div').html(data);$('#msg_div').show();}});});$('#singleFieldTags').tagit({autocomplete:{delay:0,minLength:0},tagSource:function(request,response){$.ajax({type:"GET",url:"project_GetTags.php",data:'PID='+thisproject,success:function(data){var bgr=data.substring(2).split(",");arrx=$.grep(bgr,function(a){if(a.substr(0,request.term.length).toLowerCase()==request.term.toLowerCase()){return a;}});response(arrx.sort());}});},tagLimit:20,onTagClicked:function(event,ui){var thisurl='story_List.php?searchstring=tag:'+ui.tagLabel+'&PID='+thisproject+'&Type=search';window.location=thisurl;}});$("textarea").htmlarea({toolbar:[["html"],["bold","italic","underline","strikethrough","superscript","subscript"],["cut","copy","paste"],["increasefontsize","decreasefontsize"],["forecolor"],["orderedList","unorderedList"],["indent","outdent","justifyleft","justifycenter","justifyright"],["link","unlink"]]});});