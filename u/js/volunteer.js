function setErrorBorder(f) {
	f.closest("div").addClass("has-error");
}

function unsetErrorBorder(f) {
	f.closest("div").removeClass("has-error");
}

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
	$.post("storeVolunteerValues.php", {"emails":emails,"numbers":numbers,"tasks":tasks}, function(response){
		console.log(response);
		location.reload();
	});
}

function saveVolunteerChanges() {
	var emails = [];
	var numbers = [];
	var tasks = [];
	var notes = [];
	$('.table > tbody > tr').each(function() {
		var changed = $(this).closest('tr').find('.changed').html();
		if( changed == 1 ) {
			emails.push($(this).children().children('.email').text());
			numbers.push($(this).closest('tr').find('[type=text]').val());
			tasks.push($(this).closest('tr').find('select').val());
			notes.push($(this).closest('tr').find('textarea').val());
		}
	});
	$.post("storeVolunteerValues.php", {"emails":emails,"numbers":numbers,"tasks":tasks,"notes":notes}, function(response){
		console.log(response);
		location.reload();
	});
}

$(document).ready(function() {
	$("textarea").keyup(function() {
		var chars = $(this).val().length;
		if( chars == 0 ) {
			unsetErrorBorder($(this));
		} else if( chars >= 1024 ) {
			setErrorBorder($(this));
		} else {
			unsetErrorBorder($(this));
		}
		$(this).closest('tr').find('.changed').html('1');
	});
	$("select").change(function() {
		$(this).closest('tr').find('.changed').html('1');
	});
	$("[type=text]").keyup(function() {
		$(this).closest('tr').find('.changed').html('1');
	});
});