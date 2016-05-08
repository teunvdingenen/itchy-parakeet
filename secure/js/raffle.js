
var winners = [];

var genders = [];
var cities = {};
var ages = {};
var visits = {};

var genderchart;
var citieschart;
var ageschart;
var visitschart;

var allowed_ages = [];
var allowed_cities = [];
var allowed_visits = [];
var allowed_editions = [];
var allowed_contribs = [];
var allowed_gender = [];

function lookup( arr, name ) {
    $.each(arr, function(key, value) {
        if( name === key )
            return true;
    });
    return false;
}

function splitlabelsandvalues(data) {
	var labels = [];
	var values = [];
	$.each(data, function(key, val) {
		if( val === null ) {
		} else {
			values.push(val);
			labels.push(key);
		}
	});
	return [{'labels': labels}, {'values':values}];
}

function addStat(gender, city, age, visit) {
	genders[gender] += 1;
	if( city in cities ) {
		cities[city] += 1;
	} else {
		cities[city] = 1;
	}
	if( age in ages ) {
		ages[age] += 1;
	} else {
		ages[age] = 1;
	}
	if( visit in visits ) {
		visits[visit] += 1;
	} else {
		visits[visit] = 1;
	}
	updateCharts();
}

function removeStat(gender, city, age, visit) {
	genders[gender] -= 1;
	if( city in cities ) {
		cities[city] -= 1;
	} else {
		//error
	}
	if( age in ages ) {
		ages[age] -= 1;
	} else {
		//error
	}
	if( visit in visits ) {
		visits[visit] -= 1;
	} else {
		//error
	}
	updateCharts();
}

function contains(element, array) {
	if(array.length == 0) {
		return true;
	}
	return ($.inArray(element, array) >= 0);
}

function filter() {
    $('.table > tbody  > tr').each(function() {
        var hasAge = contains($(this).children().children('#age').text(), allowed_ages);
        var hasCity = contains($(this).children().children('#city').text(), allowed_cities);
        var hasVisits = contains($(this).children().children('#visits').text(), allowed_visits); 
        var hasContrib0 = contains($(this).children().children('#contrib0').text(), allowed_contribs);
        var hasContrib1 = contains($(this).children().children('#contrib1').text(), allowed_contribs);
        var hasGender = contains($(this).children().children('#gender').text(), allowed_gender);

        var editions = $(this).children().children('#editions').text().split(",").filter(function(el) {return el.length != 0});
        var hasEdition = true;
        if( allowed_editions.length != 0 ) {
        	var hasEdition = allowed_editions.some(function (v) {
                return editions.indexOf(v) >= 0;
            });
        }
        if( hasGender && hasAge && hasCity && hasVisits && (hasContrib1 || hasContrib0) && hasEdition) {
            $(this).css("display","");
        } else {
            $(this).css("display","none");
        }
    });
}

function setupFilters() {
    $("#agefilter").focusout(function() {
        allowed_ages = [];
        allowed_ages = $(this).val().replace(/^\s+|\s+$/g,"").split(/\s*,\s*/).filter(function(el) {return el.length != 0});
        filter();
    });
    $("#cityfilter").focusout(function() {
        allowed_cities = [];
        allowed_cities = $(this).val().replace(/^\s+|\s+$/g,"").split(/\s*,\s*/).filter(function(el) {return el.length != 0});
        filter();
    });
    $("#visitsfilter").focusout(function() {
        allowed_visits = [];
        allowed_visits = $(this).val().replace(/^\s+|\s+$/g,"").split(/\s*,\s*/).filter(function(el) {return el.length != 0});
        filter();
    });
    $("#editionsfilter").focusout(function() {
        allowed_editions = [];
        allowed_editions = $(this).val().replace(/^\s+|\s+$/g,"").split(/\s*,\s*/).filter(function(el) {return el.length != 0});
        filter();
    });
    $("#contribfilter").focusout(function() {
        allowed_contribs = [];
        allowed_contribs = $(this).val().replace(/^\s+|\s+$/g,"").split(/\s*,\s*/).filter(function(el) {return el.length != 0});
        filter();
    });
    $("#genderfilter").change(function() {
    	allowed_gender = [];
    	if( $(this).val() == "both" ) {
    		allowed_gender = ["Male", "Female"];
    	} else {
    		allowed_gender.push($(this).val());
    	}
    	filter();
    });
}

function storeWinners() {
	$('.table > tbody  > tr').each(function() {
		if( $(this).closest('tr').find('[type=checkbox]').is(':checked')) {
			winners.push($(this).children().children('#email').text())
		}
	});

	$.post("storeRaffle.php", {"winners":winners}, function(response){
		//var json = JSON.parse(response);
		console.log(response);
	});
}

//$('.confirm').on('click', function(e){
	
//});

$(document).ready(function() {
	setupFilters();
});