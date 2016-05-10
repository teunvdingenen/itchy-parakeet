
$('#selectall').change(function() {
	var val = $(this).is(':checked');
	console.log(val);
	$('.table > tbody > tr').each(function() {
		$(this).find('[type=checkbox]').prop('checked',val);
	});
}) 

function storeWinners() {
	var winners = [];
	$('.table > tbody > tr').each(function() {
		if( $(this).closest('tr').find('[type=checkbox]').is(':checked')) {
			winners.push($(this).children().children('#email').text())
		}
	});

	$.post("storeRaffle.php", {"winners":winners}, function(response){
		//var json = JSON.parse(response);
		location.reload();
	});
}



$(document).ready(function() {
	$.post("signupstats.php", {"type":"raffle"}, function(response){
		console.log(response);
		$("#statcontent").html($(response).find('table'));
	});
});