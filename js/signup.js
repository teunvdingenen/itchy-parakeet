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
	f.css("border-top", "2px solid #8C3737");
  	f.css("border-left", "2px solid #FF3B3B");
  	f.css("border-right", "2px solid #FF3B3B");
  	f.css("border-bottom", "2px solid #8C3737");
}

function unsetErrorBorder(f) {
	f.css("border-top", "1px solid #7c7c7c");
  	f.css("border-left", "1px solid #c3c3c3");
  	f.css("border-right", "1px solid #c3c3c3");
  	f.css("border-bottom", "1px solid #ddd");
}

$(document).ready(function() {
	$("#contrib0").change(function() {
		if( $(this).val() == "ivbk") {
			$("#ivbk0desc").show();
			$("#act0desc").hide();
			$("#afb0desc").hide();
			$("#ontw0desc").hide();

			$("#act0row").hide();
			$("#contrib0row").show();
		} else if( $(this).val() == "act") {
			$("#act0row").show();
			$("#contrib0row").hide();

			$("#ivbk0desc").hide();
			$("#act0desc").show();
			$("#afb0desc").hide();
			$("#ontw0desc").hide();
		} else if ($(this).val() == "afb" ) {
			$("#ivbk0desc").hide();
			$("#act0desc").hide();
			$("#afb0desc").show();
			$("#ontw0desc").hide();

			$("#act0row").hide();
			$("#contrib0row").show();
		} else if ($(this).val() == "ontw") {
			$("#ivbk0desc").hide();
			$("#act0desc").hide();
			$("#afb0desc").hide();
			$("#ontw0desc").show();

			$("#act0row").hide();
			$("#contrib0row").show();
		}
	});

	$("#contrib1").change(function() {
		if( $(this).val() == "ivbk") {
			$("#ivbk1desc").show();
			$("#act1desc").hide();
			$("#afb1desc").hide();
			$("#ontw1desc").hide();

			$("#act1row").hide();
			$("#contrib1row").show();
		} else if( $(this).val() == "act") {
			$("#act1row").show();
			$("#contrib1row").hide();

			$("#ivbk1desc").hide();
			$("#act1desc").show();
			$("#afb1desc").hide();
			$("#ontw1desc").hide();
		} else if ($(this).val() == "afb" ) {
			$("#ivbk1desc").hide();
			$("#act1desc").hide();
			$("#afb1desc").show();
			$("#ontw1desc").hide();

			$("#act1row").hide();
			$("#contrib1row").show();
		} else if ($(this).val() == "ontw") {
			$("#ivbk1desc").hide();
			$("#act1desc").hide();
			$("#afb1desc").hide();
			$("#ontw1desc").show();

			$("#act1row").hide();
			$("#contrib1row").show();
		}
	});
	$("textarea").keyup(function() {
		var label = $(this).next();
		var chars = $(this).val().length;
		if( chars == 0 ) {
			label.text("Max 256 karakters.");
			unsetErrorBorder($(this));
		} else if( chars >= 256 ) {
			label.text("Maximum karakters overschreden!");
			setErrorBorder($(this));
		} else {
			var remain = 256-chars;
			label.text( remain + " resterend.");
			unsetErrorBorder($(this));
		}
	});

	$(".verify").focusout( function () {
		var len = $(this).val().length;
		if( len == 0 ) {
			setErrorBorder($(this));
		} else {
			unsetErrorBorder($(this));
		}
	});

	$(".phone").focusout( function() {
		var len = $(this).val().length;
		var isNumber = /^[0-9|+]+$/.test($(this).val());
		if( len == 0 | !isNumber ) {
			setErrorBorder($(this));
		} else {
			unsetErrorBorder($(this));
		}
	});

	$(".number").focusout( function() {
		var len = $(this).val().length;
		var isNumber = /^[0-9]+$/.test($(this).val());
		if( len == 0 | !isNumber ) {
			setErrorBorder($(this));
		} else {
			unsetErrorBorder($(this));
		}
	});

	$(".email").focusout( function () {
		var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    	var len = $(this).val().length;
    	if( re.test($(this).val()) ) {
    		unsetErrorBorder($(this));
    	} else if( ( len == 0 ) && ($(this).attr('id') == "partner") ) {
    		unsetErrorBorder($(this));
    	} else {
    		setErrorBorder($(this));
    	}
	});

	$("#contrib0").change();
	$("#contrib1").change();
	setBirthdateOptions();
});