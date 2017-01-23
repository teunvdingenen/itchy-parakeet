function setErrorBorder(f) {
	f.closest("div").addClass("has-error");
}

function unsetErrorBorder(f) {
	f.closest("div").removeClass("has-error");
}

$(document).ready(function() {
	$('#create-form').validate({
		ignore: ".ignore",
		rules: {
			email: {
				maxlength: 255,
				email: true,
				required: true
			},
            password: {
                minlength: 6,
				maxlength: 255,
				required: true
			},
            repeat: {
                minlength: 6,
				maxlength: 255,
				required: true
            },
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
			city: {
				minlength: 2,
				maxlength: 255,
				required: true
			},
			birthdate: {
				required: true
			},
			gender: {
				required: true
			},
			phone: {
				minlength: 2,
				maxlength: 32,
				required: true
			}
        }
    });

	jQuery.extend(jQuery.validator.messages, {
        required: "Dit is een verplicht veld.",
        remote: "Controleer dit veld.",
        email: "Vul hier een geldig e-mailadres in.",
        url: "Vul hier een geldige URL in.",
        date: "Vul hier een geldige datum in.",
        dateISO: "Vul hier een geldige datum in.",
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

    $(".datepicker").datepicker({
        format: 'dd/mm/yyyy',
        language: "nl"
    });
});
