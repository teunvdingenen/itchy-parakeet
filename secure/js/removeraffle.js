
$('.unraffle').click(function() {
	var remove = $(this).closest('tr').children().children('#code').text();
	$.post("removeRaffle.php", {"remove":remove}, function(response){
	});
	location.reload();
});

$('.fullticket').click(function() {
	var ticketcode = $(this).closest('tr').children().children('#code').text();
	$.post("giftticket.php", {"gift":ticketcode},function(response) {
	});
	location.reload();
});