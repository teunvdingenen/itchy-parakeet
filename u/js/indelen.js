var table_vol_shft = "<table class='table table-striped table-bordered table-hover table-condensed droppable_shft'>";
var table_vol = "<table class='table table-striped table-bordered table-hover table-condensed droppable_vol'>";
var table_shft = "<table class='table table-striped table-bordered table-hover table-condensed'>";

var tr_id = 1;

function setVolunteerShift(task, email) {
	$.post("saveVolunteerShift.php", {"name":task, "email":email}, function(response) {
		console.log(task);
		console.log(email);
		if( response == 0 ) {
			//console.log("OK");
		} else {
			alert("Taak: "+task+" niet opgeslagen voor: "+email+". Bel Teun!");
		}
	});
}

function setVolunteerEvents() {
	$('table.droppable_vol tr').on("dragstart", function (event) {
	    var dt = event.originalEvent.dataTransfer;
	    dt.setData('Text', $(this).attr('id'));
	});

	$('table.droppable_vol').on("dragenter dragover drop", function (event) {
	   event.preventDefault();
	   if (event.type === 'drop') {
			var data = event.originalEvent.dataTransfer.getData('Text',$(this).attr('id'));
		    de=$('#'+data).detach();
		    de.appendTo($(this));
		    setVolunteerShift("", de.children('.email').html());
	   };
   });
}

function setShiftEvents() {
    $('table.droppable_shft').on("dragenter dragover drop", function (event) {
	   event.preventDefault();
	   if (event.type === 'drop') {
			var data = event.originalEvent.dataTransfer.getData('Text',$(this).attr('id'));
		    de=$('#'+data).detach();
		    $(this).find('.open:first').replaceWith(de);
		    //de.appendTo($(this));
		    setVolunteerShift($(this).parent().attr('id'), de.children('.email').html());
	   };
    });

	$('table.droppable_shft tr').on("dragstart", function (event) {
	    var dt = event.originalEvent.dataTransfer;
	    dt.setData('Text', $(this).attr('id'));
	});
}

function translateDay(day) {
	if( day == 'Su') {
		return "Zondag";
	} else if( day == 'Mo' ) {
		return "Maandag";
	}else if( day == 'Tu' ) {
		return "Dinsdag";
	}else if( day == 'We' ) {
		return "Woensdag";
	}else if( day == 'Th' ) {
		return "Donderdag";
	}else if( day == 'Fr' ) {
		return "Vrijdag";
	}else if( day == 'Sa' ) {
		return "Zaterdag";
	}
	return "Onbekend";
}

function decodeshift(shift) {
	len = shift.length;
	decoded = new Object();
	decoded.end = shift.substring(len-4,len);
	decoded.start = shift.substring(len-8,len-4);
	decoded.day = translateDay(shift.substring(len-10,len-8));
	decoded.task = shift.substring(0,len-10);
	return decoded;
}

function createShiftsTable(shifts) {
	$('.shiftcontent').html('');
	var html = "";
	html += table_shft;
	var all_email = "";
	$.each(shifts, function(shift, val) {
		dShift = decodeshift(shift);
		html += "<td><table class='shift'><tr>";
		html += "<th class='hidden shift_name'>"+shift+"</th></tr>";
		html += "<tr><th>"+dShift.day+"</th></tr>";
		html += "<tr><th>"+dShift.start+" tot "+dShift.end+"</th></tr>";
		html += "<tr><th>"+val.num+" personen</th></tr></table></td>";
		var count = 0;
		html += "<td id="+shift+">"+table_vol_shft;
		$.each(val.volunteers, function(i,volunteer) {
			count += 1;
			html += "<tr id='"+tr_id+"' draggable='true'><td class='email hidden'>"+volunteer.email+"</td>";
			html += "<td class='name'>"+volunteer.firstname+ " " + volunteer.lastname+"</td></tr>";
			tr_id+=1;
			all_email += volunteer.email + ", ";
		});
		while( count < val.num ) {
			html+="<tr class='open'><td></td></tr>";
			count++;
		}
		html += "</table></tr>";
	});
	html += "</table>";
	$('.shiftcontent').html(html);
	$('#emailadressen').html(all_email);
	setShiftEvents();
}

function createVolunteerTable(volunteers) {
	$('.volunteercontent').html('');
	var html = "";
	html += table_vol;
	$.each(volunteers, function(i,volunteer) {
		html += "<tr id='"+tr_id+"' draggable='true'><td class='email hidden'>"+volunteer.email+"</td>";
		html += "<td class='name'>"+volunteer.firstname+" "+volunteer.lastname+"</td></tr>";
		tr_id+=1;
	});
	html += "<tr><td></td></tr>";
	html += "</table>";
	$('.volunteercontent').html(html);
	setVolunteerEvents();
}

$(document).ready(function() {
	$('.taskselect').change(function() {
		$.post("getShifts.php", {"shift":$(this).val()}, function(response) {
			createShiftsTable(JSON.parse(response));
			$('.volunteerselect').change();
		});
		$('#emailheader').html("Email adressen ingedeeld bij "+$('.taskselect').val()+":");
	});

	$('.volunteerselect').change(function() {
		var data = new Object();
		data.task = $('.taskselect').val();
		data.type = $('.volunteerselect').val();
		$.post("getVolunteers.php", data, function(response) {
			createVolunteerTable(JSON.parse(response));
		});
	});
	$('.taskselect').change();
	$('.volunteerselect').change();
});