$(document).ready(function() {
	$('#reset-form').validate({
		ignore: ".ignore",
		rules: {
            password: {
                minlength: 6,
				maxlength: 255,
				required: true
			},
            repeat: {
                minlength: 6,
				maxlength: 255,
				required: true
            }
        }
    });
});