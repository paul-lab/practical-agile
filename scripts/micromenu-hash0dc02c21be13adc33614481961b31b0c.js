$(document).ready(function(){var g;var b;var f=$("div").find(".thisproject").prop("id");$(".auditpopup").click(function(){var h=$(this).prop("id").substring(6);var i="";if($(this).prop("id").substring(5,6)==="s"){i="AID"}else{i="PID"}if($("#allaudits_"+h).is(":hidden")){$.ajax({type:"GET",url:"audit_List.php",data:"id="+h+"&type="+i,success:function(j){$("#allaudits_"+h).append(j)}});$("#allaudits_"+h).show()}else{$("#allaudits_"+h).empty();$("#allaudits_"+h).hide()}});$(".commentpopup").click(function(){if($.isArray(g)){if($("#commentspop"+g[0]+"_"+g[1]).is(":visible")){b=g[1];$("#commentspop"+g[0]+"_"+g[1]).empty();$("#commentspop"+g[0]+"_"+g[1]).hide()}}g=$(this).prop("id").substring(7).trim().split("_");g[0];g[1]*=1;if(b!=g[1]){$.ajax({type:"GET",url:"comment_List.php",data:"id="+g[1]+"&key="+g[0],success:function(h){$("#commentspop"+g[0]+"_"+g[1]).append(h);$(".submit_button").click(function(){var n=$(this).attr("id").substring(15);var i=$(this).attr("id").substring(13);var k=$("#CIteration_ID").val();var m=escape($("#comment_text_"+n).htmlarea("toHtmlString"));if(m.length>7000){m=m.substring(0,7000);alert("Comment Truncated")}$("#Story_AID").attr("value",n);var j=$("#Parent_ID_"+i).val();$("div#replyto_"+n).text("");var l="PID="+f+"&Parent_ID="+j+"&Iteration_ID="+k+"&User_Name="+$("#User_Name_"+n).val()+"&Story_AID="+$("#Story_AID_"+n).val()+"&replyid="+i.substring(2)+"&Type="+i.substring(0,1)+"&comment_text="+m;if(JisReadonly===0){$.ajax({type:"GET",url:"comment_Add.php",data:l,success:function(o){e($("#comment_count_"+i),1);if(j!=0){$("#commentreply_"+j).append(o);$("#deletecomment"+j).hide();$("#Parent_ID_"+i).attr("value",0);$("div#replyto_"+i).text("")}else{$("#commentlist"+i).append(o)}}})}});$("#commentspop"+g[0]+"_"+g[1]).on("click",".deletecomment",function(){var j=$(this).attr("id").substring(15);var i="#comment_count_"+g[0]+"_"+g[1];if(JisReadonly===0){$.ajax({type:"GET",url:"comment_Delete.php",data:"id="+j+"&PID="+f+"&AID="+g[1]+"&type="+g[0],success:function(k){if(k.trim()=="1"){$("#comment_"+j).remove();e($(i),-1)}}})}});$("#commentspop"+g[0]+"_"+g[1]).on("click",".reply",function(){var j=$(this).attr("id");$("#deletecomment"+g[0]+"_"+j).hide();var i=$(this).attr("href").substring(12);$("div#replyto_"+i).text("Reply to:"+$("div#comment_body_"+j).text().substring(0,50)+"...");$("#Parent_ID_"+i).attr("value",j)});$("textarea").htmlarea({toolbar:[["html"],["bold","italic","underline","strikethrough","superscript","subscript"],["cut","copy","paste"],["increasefontsize","decreasefontsize"],["forecolor"],["orderedList","unorderedList"],["indent","outdent","justifyleft","justifycenter","justifyright"],["link","unlink"]]})}});$("#commentspop"+g[0]+"_"+g[1]).show()}else{$("#commentspop"+g[0]+"_"+g[1]).empty();$("#commentspop"+g[0]+"_"+g[1]).hide();b=0}});function e(j,i){if(typeof j!="undefined"){i*=1;var h=j.html().trim().slice(1,-1)*1;h+=i;j.html(" ("+h+")")}}$(".taskpopup").click(function(){var h=$(this).prop("id");if($("#alltasks_"+h).is(":hidden")){$.ajax({type:"GET",url:"task_List.php",data:"pid="+f+"&aid="+h,success:function(j){$("#alltasks_"+h).append(j);$(".savetask").hide();$(".indet1").prop("indeterminate",true);var i=$("#sortabletask"+h).sortable({update:function(k,l){$.ajax({type:"GET",url:"update_taskorder.php",data:$(i).sortable("serialize")})}});$(".deletetask").mouseover(function(){$("html, body").css("cursor","pointer")});$(".deletetask").mouseout(function(){$("html, body").css("cursor","default")});$(".deletetask").click(function(){if(JisReadonly===0){var k=($(this).parent().attr("id").substring(5));var m=$("#done_"+k).prop("value");if(m==0){m=1}var l=$("#desc_"+k).val();$(this).parent().remove();$.ajax({type:"GET",url:"task_Delete.php",data:"id="+k+"&PID="+f+"&AID="+h+"&desc="+l,success:function(){a($("#task_count_"+h),m)}})}});$(".divRow .done").click(function(){if(JisReadonly===0){var m=$(this).attr("id").substring(5);c($(this));var l=$(this).prop("value");var k=l*10;a($("#task_count_"+h),k);$.ajax({type:"GET",url:"update_task_Status.php",data:"TID="+m+"&DONE="+l+"&PID="+f+"&AID="+h+"&desc="+$("#desc_"+m).val()})}});$(".edittask").mouseover(function(){$("html, body").css("cursor","pointer")});$(".edittask").mouseout(function(){$("html, body").css("cursor","default")});$(".divRow .edittask").click(function(){if(JisReadonly===0){$(this).hide();$(this).parent().find(".savetask").show();var k=$(this).attr("id").substring(9);$("#desc_"+k).prop("disabled",false);$("#user_"+k).prop("disabled",false);$("#expected_"+k).prop("disabled",false);$("#actual_"+k).prop("disabled",false)}});$(".savetask").mouseover(function(){$("html, body").css("cursor","pointer")});$(".savetask").mouseout(function(){$("html, body").css("cursor","default")});$(".divRow .savetask").click(function(){$(this).hide();$(this).parent().find(".edittask").show();var l=$(this).attr("id").substring(9);$("#desc_"+l).prop("disabled",true);$("#user_"+l).prop("disabled",true);$("#expected_"+l).prop("disabled",true);$("#actual_"+l).prop("disabled",true);var k="TID="+l;k+="&PID="+f+"&AID="+h;k+="&desc="+$("#desc_"+l).val();k+="&user="+$("#user_"+l).val();k+="&exph="+$("#expected_"+l).val();k+="&acth="+$("#actual_"+l).val();$.ajax({type:"GET",url:"task_Update.php",data:k})});$(".savenew").mouseover(function(){$("html, body").css("cursor","pointer")});$(".savenew").mouseout(function(){$("html, body").css("cursor","default")});$(".savenew").click(function(){if(JisReadonly===0){var l=$(this).parent().attr("id").substring(7);var k="AID="+l;k+="&PID="+f;k+="&desc="+$("#ndesc_"+l).val();k+="&user="+$("#taskuser_"+l).val();k+="&exph="+$("#nexph_"+l).val();k+="&acth="+$("#nacth_"+l).val();$.ajax({type:"GET",url:"task_Add.php",data:k,success:function(m){$("#alltasks_"+l).empty();$("#alltasks_"+l).hide();$("#"+l).click();a($("#task_count_"+l),30)}})}})}});$("#alltasks_"+h).show()}else{$("#alltasks_"+h).empty();$("#alltasks_"+h).hide()}});function a(j,h){h*=1;var i=j.html().trim().slice(1,-1).split("/");i[0]*=1;if(isNaN(i[1])){i[1]=0}i[1]*=1;switch(h){case 2:i[0]-=1;i[1]-=1;break;case 20:i[0]+=1;break;case 0:i[0]-=1;break;case 1:i[1]-=1;break;case 30:i[1]+=1}j.html("("+i[0]+"/"+i[1]+")")}function c(h){switch(h.prop("value")){case"0":h.prop("checked",false);h.prop("indeterminate",true);h.prop("value",1);break;case"1":h.prop("checked",true);h.prop("indeterminate",false);h.prop("value",2);break;default:h.prop("checked",false);h.prop("indeterminate",false);h.prop("value",0)}}$(".uploadpopup").click(function(){var h=$(this).prop("id").substring(2);if($("#allupload_"+h).is(":hidden")){$.ajax({type:"GET",url:"upload_List.php",data:"PID="+f+"&AID="+h,success:function(i){$("#allupload_"+h).append(i);$(".deleteupload").mouseover(function(){$("html, body").css("cursor","pointer")});$(".deleteupload").mouseout(function(){$("html, body").css("cursor","default")});$(".deleteupload").click(function(){if(JisReadonly===0){var j=($(this).prop("id"));var k=$(this).parent().text();$(this).parent().remove();$.ajax({type:"GET",url:"upload_Delete.php",data:"PID="+f+"&AID="+h+"&Name="+j+"&Type="+$(this).parent().prop("id").substring(7)+"&fdet="+k,success:function(){d($("#upload_count_"+h),-1)}})}});$(".uploadnew").mouseover(function(){$("html, body").css("cursor","pointer")});$(".uploadnew").mouseout(function(){$("html, body").css("cursor","default")});$(".uploadnew").click(function(){if(JisReadonly===0){var k=$(this).parent().attr("id").substring(7);var j=$("#ndesc_"+k).prop("files")[0];var l=new FormData();l.append("AID",k);l.append("PID",f);l.append("file",j);$.ajax({type:"POST",url:"upload_Add.php",dataType:"text",cache:false,contentType:false,processData:false,data:l,success:function(m){if(m.length>5){$("#allupload_"+k).append(m)}else{$("#allupload_"+k).empty();$("#allupload_"+k).hide();$("#up"+k).click();d($("#upload_count_"+k),1)}}})}})}});$("#allupload_"+h).show()}else{$("#allupload_"+h).empty();$("#allupload_"+h).hide()}});function d(h,j){if(typeof h!="undefined"){j*=1;var i=h.html().trim().slice(1,-1)*1;i+=j;h.html(" ("+i+")")}}});