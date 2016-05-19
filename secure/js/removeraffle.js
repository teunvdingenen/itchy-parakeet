
$('.unraffle').click(function() {
	var remove = $(this).closest('tr').children().children('#code').text();
	$.post("removeRaffle.php", {"remove":remove}, function(response){
	});
	location.reload();
});