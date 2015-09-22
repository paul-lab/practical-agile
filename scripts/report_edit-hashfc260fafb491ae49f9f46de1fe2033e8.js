$(function() {

	$('#external').on('change', function () {
		var testit=  $(this).val();
		if(testit==1)
		{
			$('#extrasql').text('SELECT * FROM story where story.Project_ID="{Project}" and (' );
			$('#extrasqlend').text( ')' );
		}else{
			$('#extrasql').text( '' );
			$('#extrasqlend').text( '' );
		}
	});
});