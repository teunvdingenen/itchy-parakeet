var table_vol_shft = "<table class='table table-striped table-bordered table-hover table-condensed droppable_shft'>";
var table_vol = "<table class='table table-striped table-bordered table-hover table-condensed droppable_vol'>";
var table_shft = "<table class='table table-striped table-bordered table-hover table-condensed'>";

var tr_id = 1;

function setVolunteerShift(task, email) {
	$.post("saveVolunteerShift.php", {"name":task, "email":email}, function(response) {
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
	if( day == 0) {
		return "Zondag";
	} else if( day == 1 ) {
		return "Maandag";
	}else if( day == 2 ) {
		return "Dinsdag";
	}else if( day == 3 ) {
		return "Woensdag";
	}else if( day == 4 ) {
		return "Donderdag";
	}else if( day == 5 ) {
		return "Vrijdag";
	}else if( day == 6 ) {
		return "Zaterdag";
	}
	return "Onbekend";
}

function addZero(minutes) {
	if( minutes < 10 ) {
		return "0" + minutes;
	}
	return minutes;
}

function createShiftsTable(shifts) {
	$('.shiftcontent').html('');
	var html = "";
	html += table_shft;
	var all_email = "";
	$.each(shifts, function(shift, val) {
		var start = new Date(val.startdate);
		var end = new Date(val.enddate);
		html += "<td><table class='shift'><tr>";
		html += "<th class='hidden shift_name'>"+shift+"</th></tr>";
		html += "<tr><th>"+translateDay(start.getDay())+"</th></tr>";
		html += "<tr><th>"+start.getHours()+":"+addZero(start.getMinutes())+" tot "+end.getHours()+":"+addZero(end.getMinutes())+"</th></tr>";
		html += "<tr><th>"+val.num+" personen</th></tr></table></td>";
		var count = 0;
		html += "<td id="+shift+">"+table_vol_shft;
		$.each(val.volunteers, function(i,volunteer) {
			count += 1;
			html += "<tr class='data' data-content = '"+volunteer.contrib0_desc+"' rel='popover' data-placement='left' data-original-title='Omschrijving' data-trigger='hover' id='"+tr_id+"' draggable='true'><td class='email hidden'>"+volunteer.email+"</td>";
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
	$(".data").popover();
	setShiftEvents();
}

function createVolunteerTable(volunteers) {
	$('.volunteercontent').html('');
	var html = "";
	html += table_vol;
	$.each(volunteers, function(i,volunteer) {
		html += "<tr class='data' data-content = '"+volunteer.contrib0_desc+" rel='popover' data-placement='left' data-original-title='Omschrijving' data-trigger='hover' id='"+tr_id+"' draggable='true'><td class='email hidden'>"+volunteer.email+"</td>";
		html += "<td class='name'>"+volunteer.firstname+" "+volunteer.lastname+(volunteer.nightshift === 'J' ? "*" : "") + "</td></tr>";
		tr_id+=1;
	});
	html += "<tr><td></td></tr>";
	html += "</table>";
	$('.volunteercontent').html(html);
	$(".data").popover();
	setVolunteerEvents();
}

$(document).ready(function() {
	$('.taskselect').change(function() {
		$('#roosterlink').attr('href','rooster?t='+$(this).val());
		$.post("getShifts.php", {"shift":$(this).val()}, function(response) {
			createShiftsTable(JSON.parse(response));
			$('.volunteerselect').change();
		});
		$('#emailheader').html("Email adressen ingedeeld bij "+$('.taskselect').val()+":");
	});

	$('.volunteerselect').change(function() {
		var data = new Object();
		data.contrib = $('.taskselect').val();
		data.type = $('.volunteerselect').val();
		$.post("getVolunteers.php", data, function(response) {
			createVolunteerTable(JSON.parse(response));
		});
	});

	$('.team_droppable').on("dragenter dragover drop", function (event) {
		event.preventDefault();
	   if (event.type === 'drop') {
	   		var team = "vrijwilligers";
	   		if( $('.oth_team').html() == "acts" ) {
	   			team = "acts";
	   		}
	   		var data = event.originalEvent.dataTransfer.getData('Text',$(this).attr('id'));
	   		var name = $('#'+data).find('.name').html();
		    $('#'+data).popover("hide")
	   		$('#modal-content').text("Weet je zeker dat je "+name+" wilt overzetten naar "+team+"?");
	   		$('#trid').html(data);
    		$('#modal').modal('show');
	   };
	});

	$("#signoff").click(function() {
		var id = $('#trid').text();
		de=$('#'+id).detach();
		setVolunteerShift($('.oth_team').html(), de.children('.email').html());
		$('#modal').modal('hide');
	});

	$('.taskselect').change();
	$('.volunteerselect').change();
	$('#modal').modal('hide');
});