var task = "";
var startd = "";
var starth = "";
var endh = "";
var enddate_set = false;

function setName() {
	if( $('#autoname').is(':checked') ) {
		var name = task + startd + starth + endh;
		$('#name').val(name);
	}
}

function get_name(element) {
	return element.closest("tr").find(".name").text();
}

$(document).ready(function() {
	$('#startdate').datetimepicker({
		format: "dddd, DD/MM/YYYY HH:mm",
	});
    $('#enddate').datetimepicker({
        useCurrent: false, //Important! See issue #1075
		format: "dddd, DD/MM/YYYY HH:mm",
    });
    $("#startdate").on("dp.change", function (e) {
        $('#enddate').data("DateTimePicker").minDate(e.date);
        if( !enddate_set ) {
        	end = moment(e.date).add(2, 'hours');
        	$('#enddate_input').val(end.format("dddd, DD/MM/YYYY HH:mm"));
        	endh = end.format("HHmm");
        }
        starth = e.date.format("HHmm");
        startd = e.date.format("dd");
        setName();
    });
    $("#enddate").on("dp.change", function (e) {
        $('#startdate').data("DateTimePicker").maxDate(e.date);
        endh = e.date.format("HHmm");
        setName();
        enddate_set = true;
    });

    $('#name').change(function() {
    	var name = $(this).val();
    	var p = $(this).closest('.input-group').find('.working');
    	p.html("<span class='glyphicon glyphicon-refresh spinning'></span>");
    	$.post("checkshiftname.php", {"name":name}, function(response) {
    		if( response == 0 ) {
				p.html("<i class='glyphicon glyphicon-ok text-success'></i>");
			} else {
				p.html("<i class='glyphicon glyphicon-remove text-danger'></i>");
			}
    	});
    });

    $('.changenr').change(function() {
    	name = $(this).closest('tr').find('.name').html();
    	p = $(this).closest('tr').find('.status');
    	p.html("<span class='glyphicon glyphicon-refresh spinning'></span>");
    	$.post("saveShiftChange.php", {"name":name, 'nrrequired':$(this).val()}, function(response) {
    		if( response == 0 ) {
				p.html("<i class='glyphicon glyphicon-ok text-success'></i>");
			} else {
				p.html("<i class='glyphicon glyphicon-remove text-danger'></i>");
			}
		});
    });

    $('#taskselect').change(function() {
    	task = $('#taskselect').val();
    	setName();
    });

    $('#autoname').change(function() {
    	$('#name').prop( "readonly", this.checked );
    	if( this.checked)  {
    		setName();
    	}
    });

    $(".removeshift").click(function() {
    	element = $(this);
		$.post("removeShift.php", {"name":get_name($(this))}, function(response) {
			if( response == 0 ) {
				console.log(response);
				element.closest("tr").remove();
			}
		});
	});

    $('#shift-form').validate({
		ignore: ".ignore",
		rules: {
			name: {
				maxlength: 16,
				required: true
			},
			task: {
				maxlength: 16,
				required: true
			},
			startdate: {
				required: true
			},
			enddate: {
				required: true
			},
			nrrequired: {
				maxlength: 8,
				required: true
			}
		}
	});

	$('#autoname').change();
});