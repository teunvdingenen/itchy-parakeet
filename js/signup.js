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

$(document).ready(function() {
	$("#contrib0").change(function() {
		if( $(this).val() == "act") {
			$("#act0row").show();
			$("#contrib0row").hide();
		} else {
			$("#act0row").hide();
			$("#contrib0row").show();
		}
	});

	$("#contrib1").change(function() {
		if( $(this).val() == "act") {
			$("#act1row").show();
			$("#contrib1row").hide();
		} else {
			$("#act1row").hide();
			$("#contrib1row").show();
		}
	});
	$("textarea").keyup(function() {
		var label = $(this).next();
		var chars = $(this).val().length;
		if( chars == 0 ) {
			label.text("Max 256 karakters.");
		} else if( chars >= 256 ) {
			label.text("Maximum karakters overschreden!");
		} else {
			var remain = 256-chars;
			label.text( remain + " resterend.")
		}
	});

	$("#contrib0").change();
	$("#contrib1").change();
	setBirthdateOptions();
});