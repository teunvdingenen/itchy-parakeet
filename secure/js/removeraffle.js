
$('.unraffle').click(function() {
	var remove = $(this).closest('tr').children().children('#code').text();
	$.post("removeRaffle.php", {"remove":remove}, function(response){
	});
	location.reload();
});

$(document).ready(function() {
	$.post("signupstats.php", {"type":"raffle"}, function(response){
		console.log(response);
		$("#statcontent").html($(response).find('table'));
	});
});