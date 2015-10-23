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