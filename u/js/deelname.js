
$(document).ready(function() {
	$('#transactionmethod').change(function() {
		total = parseFloat($('#hidden-total').text());
		if( $(this).val() == "ideal") {
			$('.transaction').text('0,29');
			total += 0.29;
		} else if( $(this).val() == "mistercash") {
			$('.transaction').text('0,39');
			total += 0.39;
		} else if( $(this).val() == "creditcard") {
			amount = 0.25 + total * 0.028;
			total += amount;
			$('.transaction').text(amount);
		} else {
			$('.transaction').text('0,00');
		}
		$('.total').text(total);
	});
	$('#buyer-form').validate({
		ignore: ".ignore",
		rules: {
			email: {
				email: true,
				maxlength: 255,
				required: true
			},
			code: {
				minlength: 8,
				maxlength: 16,
				required: true
			},
			city: {
				minlength: 2,
				maxlength: 255,
				required: true
			},
			street: {
				minlength: 2,
				maxlength: 255,
				required: true
			},
			postal: {
				minlength: 2,
				maxlength: 255,
				required: true
			},
			terms4: {
				required: true
			}
		},
		messages: {
			terms4: "Je bent verplicht deze voorwaarde te accepteren"
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
	$('#transactionmethod').change();
	$(".data").popover();
});
