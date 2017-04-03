var table_vol_shft = "<table class='table table-striped table-bordered table-hover table-condensed droppable_shft'>";
var table_vol = "<table class='table table-striped table-bordered table-hover table-condensed droppable_vol'>";
var table_shft = "<table class='table table-striped table-bordered table-hover table-condensed shift'>";

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
		    $(this).find('.open:first').remove();
		    de.appendTo($(this));
		    //setVolunteerShift($(this).closest('.shift').find('.shift_name').html(), de.children('.email').html());
	   };
    });

	$('table.droppable_shft tr').on("dragstart", function (event) {
	    var dt = event.originalEvent.dataTransfer;
	    dt.setData('Text', $(this).attr('id'));
	});
	
}

function createShiftsTable(shifts) {
	$('.shiftcontent').html('');
	var html = "";
	html += table_shft;
	$.each(shifts, function(shift, val) {
		html += "<tr>";
		html += "<th class='shift_name'>"+shift+"</th>";
		var num = val.num;
		var count = 0;
		html += "<td>"+table_vol_shft;
		$.each(val.volunteers, function(i,volunteer) {
			count += 1;
			html += "<tr id='"+tr_id+"' draggable='true'><td class='email hidden'>"+volunteer.email+"</td>";
			html+="<td class='name'>"+volunteer.firstname+ " " + volunteer.lastname+"</td></tr>";
			tr_id+=1;
		});
		while( count < num ) {
			html+="<tr class='open'><td></td></tr>";
			count++;
		}
		html += "</table></tr>";
	});
	html += "</table>";
	$('.shiftcontent').html(html);
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
	});

	$('.volunteerselect').change(function() {
		var data = new Object();
		data.task = $('.taskselect').val();
		data.type = $('.volunteerselect').val();
		$.post("getVolunteers.php", data, function(response) {
			createVolunteerTable(JSON.parse(response));
		});
	});
});