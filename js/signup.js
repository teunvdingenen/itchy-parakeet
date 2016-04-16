//Depricated
function setBirthdateOptions() {
	var html;
	for(var i=1; i<=31;i++){
		html += "<option value="+i+">"+i+"</option>";
	}
	$("#birthday").append(html);
	html = "";
	for(var i=1; i<=12;i++){
		html += "<option value="+i+">"+i+"</option>";
	}
	$("#birthmonth").append(html);
	html="";
	for(var i=1900; i<=1998;i++){
		html += "<option value="+i+">"+i+"</option>";
	}
	$("#birthyear").append(html);
}

function setErrorBorder(f) {
	f.closest("div").addClass("has-error");
}

function unsetErrorBorder(f) {
	f.closest("div").removeClass("has-error");
}

function showAct0Input() {
	$("#act0type").closest(".form-group").show();
	$("#act0desc").closest(".form-group").show();
	$("#act0need").closest(".form-group").show();
	$("#contrib0desc").closest(".form-group").hide();
}

function showAct1Input() {
	$("#act1type").closest(".form-group").show();
	$("#act1desc").closest(".form-group").show();
	$("#act1need").closest(".form-group").show();
	$("#contrib1desc").closest(".form-group").hide();
}

function hideAct0Input() {
	$("#act0type").closest(".form-group").hide();
	$("#act0desc").closest(".form-group").hide();
	$("#act0need").closest(".form-group").hide();
	$("#contrib0desc").closest(".form-group").show();
}

function hideAct1Input() {
	$("#act1type").closest(".form-group").hide();
	$("#act1desc").closest(".form-group").hide();
	$("#act1need").closest(".form-group").hide();
	$("#contrib1desc").closest(".form-group").show();
}

function showPreparations() {
	$("#preparations").show();
	$("#prepcounter").show();
	$("#prepinfo").hide();
	$("#prepintro").show();
}

function hidePreparations() {
	$("#preparations").hide();
	$("#prepcounter").hide();
	$("#prepinfo").show();
	$("#prepintro").hide();
}

$(document).ready(function() {
	$("#contrib0").change(function() {
		if( $(this).val() == "ivbk") {
			hideAct0Input();
			$("#ivbk0info").show();
			$("#act0info").hide();
			$("#afb0info").hide();
			$("#ontw0info").hide();
		} else if( $(this).val() == "act") {
			showAct0Input();
			$("#ivbk0info").hide();
			$("#act0info").show();
			$("#afb0info").hide();
			$("#ontw0info").hide();
		} else if ($(this).val() == "afb" ) {
			hideAct0Input();
			$("#ivbk0info").hide();
			$("#act0info").hide();
			$("#afb0info").show();
			$("#ontw0info").hide();
		} else if ($(this).val() == "ontw") {
			hideAct0Input();
			$("#ivbk0info").hide();
			$("#act0info").hide();
			$("#afb0info").hide();
			$("#ontw0info").show();
		}
	});

	$("#contrib1").change(function() {
		if( $(this).val() == "ivbk") {
			hideAct1Input();
			$("#ivbk1info").show();
			$("#act1info").hide();
			$("#afb1info").hide();
			$("#ontw1info").hide();
		} else if( $(this).val() == "act") {
			hideAct1Input();
			$("#ivbk1info").hide();
			$("#act1info").show();
			$("#afb1info").hide();
			$("#ontw1info").hide();
		} else if ($(this).val() == "afb" ) {
			hideAct1Input();
			$("#ivbk1info").hide();
			$("#act1info").hide();
			$("#afb1info").show();
			$("#ontw1info").hide();
		} else if ($(this).val() == "ontw") {
			hideAct1Input();
			$("#ivbk1info").hide();
			$("#act1info").hide();
			$("#afb1info").hide();
			$("#ontw1info").show();
		}
	});
	$("textarea").keyup(function() {
		var label = $(this).next();
		var chars = $(this).val().length;
		if( chars == 0 ) {
			label.text("Max 256 karakters.");
			unsetErrorBorder($(this));
		} else if( chars >= 256 ) {
			label.text("");
			setErrorBorder($(this));
		} else {
			var remain = 256-chars;
			label.text( remain + " resterend.");
			unsetErrorBorder($(this));
		}
	});

	$("#preparationsbox").change(function() {
		if( $(this).is(":checked") ) {
			showPreparations();
		} else {
			hidePreparations();
		}
	});

	$('#signup-form').validate({
		rules: {
			firstname: {
				minlength: 2,
				maxlength: 255,
				required: true
			},
			lastname: {
				minlength: 2,
				maxlength: 255,
				required: true
			},
			email: {
				maxlength: 255,
				required: true,
				email: true
			},
			city: {
				minlength: 2,
				maxlength: 255,
				required: true
			},
			birthdate: {
				required: true,
				date: true
			},
			gender: {
				required: true
			},
			phone: {
				minlength: 2,
				maxlength: 255,
				required: true
			},
			partner: {
				maxlength: 255,
				email: true
			},
			contrib0desc: {
				maxlength: 255
			},
			contrib1desc: {
				maxlength: 255
			},
			act0desc: {
				maxlength:255
			},
			act1desc: {
				maxlength:255
			},
			act0need: {
				maxlength:255
			},
			act1need: {
				maxlength:255
			},
			motivation: {
				maxlength:255
			}, 
			familiar: {
				maxlength:255
			},
			preparations: {
				maxlength:255
			},
			terms0: {
				required: true
			},
			terms1: {
				required: true
			},
			terms2: {
				required: true
			},
			terms3: {
				required: true
			}
		},
		messages: {
			terms0: "Je bent verplicht de voorwaarden te accepteren",
			terms1: "Je bent verplicht de voorwaarden te accepteren",
			terms2: "Je bent verplicht de voorwaarden te accepteren",
			terms3: "Je bent verplicht de voorwaarden te accepteren"
		}
	});

	jQuery.extend(jQuery.validator.messages, {
        required: "Dit is een verplicht veld.",
        remote: "Controleer dit veld.",
        email: "Vul hier een geldig e-mailadres in.",
        url: "Vul hier een geldige URL in.",
        date: "Vul hier een geldige datum in.",
        dateISO: "Vul hier een geldige datum in (ISO-formaat).",
        number: "Vul hier een geldig getal in.",
        digits: "Vul hier alleen getallen in.",
        creditcard: "Vul hier een geldig creditcardnummer in.",
        equalTo: "Vul hier dezelfde waarde in.",
        accept: "Vul hier een waarde in met een geldige extensie.",
        maxlength: jQuery.validator.format("Vul hier maximaal {0} tekens in."),
        minlength: jQuery.validator.format("Vul hier minimaal {0} tekens in."),
        rangelength: jQuery.validator.format("Vul hier een waarde in van minimaal {0} en maximaal {1} tekens."),
        range: jQuery.validator.format("Vul hier een waarde in van minimaal {0} en maximaal {1}."),
        max: jQuery.validator.format("Vul hier een waarde in kleiner dan of gelijk aan {0}."),
        min: jQuery.validator.format("Vul hier een waarde in groter dan of gelijk aan {0}.")
	});

	$("#contrib0").change();
	$("#contrib1").change();
	$("#preparationsbox").change();
	setBirthdateOptions();
});