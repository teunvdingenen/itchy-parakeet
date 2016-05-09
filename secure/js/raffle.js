
$('#selectall').change(function() {
	var val = $(this).is(':checked');
	console.log(val);
	$('.table > tbody > tr').each(function() {
		$(this).find('[type=checkbox]').prop('checked',val);
	});
}) 

function storeWinners() {
	$('.table > tbody > tr').each(function() {
		if( $(this).closest('tr').find('[type=checkbox]').is(':checked')) {
			winners.push($(this).children().children('#email').text())
		}
	});

	$.post("storeRaffle.php", {"winners":winners}, function(response){
		//var json = JSON.parse(response);
		console.log(response);
	});
}



$(document).ready(function() {

});