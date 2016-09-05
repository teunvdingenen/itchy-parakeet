

function getInfo() {
	$.get("getarrivedinfo.php", "", function(response){
		var obj = jQuery.parseJSON(response);
		var nr_attendies = obj.length;
		var rows = Math.sqrt(nr_attendies);
		var i = 0;
		var html = "<table class='table table-condensed'>";
		for( var j = 0; j < rows; j++ ) {
			html += "<tr>";
			for( var k = 0; k < rows; k++ ) {
				if( i >= nr_attendies) { 
					break;
				}
				var attendee = obj[i];
				var color = "";
				if( attendee.attending == 1 ) {
					color = "btn-success";
				} else {
					color = "btn-default";
				}
				var name = attendee.firstname + " " + attendee.lastname;
				html += "<td><button class='btn btn-lg " + color + " btn-block btn-sm' role='button' data-toggle='modal' data-target='#"+attendee.code+"'>"+name+"</div></td>";
				html += "<div id='"+attendee.code+"' class='modal fade' tabindex='-1' role='dialog' aria-labelledby='"+attendee.id+"'>";
				html += "<div class='modal-dialog modal-sm' role='document'>";
				html += "<div class='modal-content'>";
				html += "<div class='modal-header'>";
				html += "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
				html += "<h4 class='modal-title' id='"+attendee.id+"'>"+name+"</h4>";
				html += "</div>";
				html += "<div class='modal-body'>";
				html += "Ticket code: " + attendee.code + "<br> Transactie: " + attendee.id;
				html += "</div><div class='modal-footer'></div>";
				html += "</div></div></div>";
				i++;
			}
			html += "</tr>";
		}
		html+="</table>";
		$("#content").html(html);
	});
}

$(document).ready(function() {
	getInfo();
	setInterval(function(){ 
    getInfo();
}, 5000);
});
    