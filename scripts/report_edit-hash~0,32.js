
$(function(){Flipflop($('#external').val());$('#external').on('change',function(){Flipflop($(this).val());});function Flipflop(testit)
{if(testit==1){$('#extrasql').text('SELECT * FROM story where story.Project_ID="{Project}" and (');$('#extrasqlend').text(')');$('#Qorder').show();}else{$('#extrasql').text('');$('#extrasqlend').text('');$('#Qorder').text('');$('#Qorder').hide();}}});