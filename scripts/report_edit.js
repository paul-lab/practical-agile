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

	Flipflop($('#external').val());

	$('#external').on('change', function () {
		Flipflop($(this).val());
	});


	function Flipflop(testit)
	{
		if(testit==1)
		{
			$('#extrasql').text('SELECT * FROM story where story.Project_ID="{Project}" and (' );
			$('#extrasqlend').text( ')' );
			$('#Qorder').show();
		}else{
			$('#extrasql').text( '' );
			$('#extrasqlend').text( '' );
			$('#Qorder').text( '' );
			$('#Qorder').hide();
		}
	}

});