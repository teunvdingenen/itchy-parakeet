

function saveActsChanges() {
	var emails = [];
	var numbers = [];
	var tasks = [];
	$('.table > tbody > tr').each(function() {
		emails.push($(this).children().children('.email').text());
		numbers.push($(this).closest('tr').find('[type=text]').val());
		if( $(this).closest('tr').find('[type=checkbox]').is(':checked')) {
			tasks.push("");
		} else {
			tasks.push("act");
		}
	});
	console.log(emails);
	console.log(numbers);
	console.log(tasks);
	$.post("storeVolunteerValues.php", {"emails":emails,"numbers":numbers,"tasks":tasks}, function(response){
		console.log(response);
		location.reload();
	});
}

function saveVolunteerChanges() {
	var emails = [];
	var numbers = [];
	var tasks = [];
	$('.table > tbody > tr').each(function() {
		emails.push($(this).children().children('.email').text());
		numbers.push($(this).closest('tr').find('[type=text]').val());
		tasks.push($(this).closest('tr').find('select').val());
	});
	console.log(emails);
	console.log(numbers);
	console.log(tasks);
	$.post("storeVolunteerValues.php", {"emails":emails,"numbers":numbers,"tasks":tasks}, function(response){
		console.log(response);
		location.reload();
	});
}