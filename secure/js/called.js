var selected = 0;

function setPersonInfo(email) {
	$.post("getperson.php", {"email":email, "level":"raffle"}, function(response){
		$(".person_info").html($('<div>').html(response).find('.secure_content').html());
		
		$("#called-button").on('click', function() {
			var found_email = $(".person_info #email").text();
			$.post("called.php", {'email':found_email}, function(response){
				setPersonInfo(found_email);
				//reload or something
			});
		});
	});
}

function setCalled(email) {
	$.post("called.php", {"email":email}, function(response){
		console.log(response);
		setPersonInfo(email);
	});
}

$(".called-table tr").on('click', function() {
	if( $(this).hasClass('selected')) {
		$(this).removeClass('selected');
		$(".person_info").hide();
		selected = 0;
	} else {
		$(".person_info").show();
		$(this).addClass('selected');
		setPersonInfo($(this).children().children('#email').text());
		if( selected != 0 ) {
			selected.removeClass('selected');
		}
		selected = $(this);
	}
});

$(document).ready(function() { 
	$(".person_info").hide();
});