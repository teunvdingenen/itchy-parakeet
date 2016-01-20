
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

function setupCharts() {
	genders = {'Male':0,'Female':0};
	$.post("signupstats.php", {"type":"raffle"}, function(response){
		console.log(response);
		var json = JSON.parse(response);
		genders = json['gender'];
		cities = json['city'];
		ages = json['ages'];
		visits = json['visits'];
		genderchart = createGenderChart(json['gender'], "#genderchart");
		citieschart = createCityChart(json['city'], "#citieschart");
		ageschart = createAgeChart(json['ages'], "#ageschart");
		visitschart = createVisitsChart(json['visits'], "#visitschart");
	});
}

function updateCharts() {
	/**
	if(genderchart.segments[0].value == 0 && genderchart.segments[1].value == 0) {
		genderchart = createGenderChart(genders, "#genderchart");
	} else {
		genderchart.segments[0].value = genders['male'];
		genderchart.segments[1].value = genders['female'];
		genderchart.update();
	}
	**/
	genderchart.destroy();
	citieschart.destroy();
	ageschart.destroy();
	visitschart.destroy();
	genderchart = createGenderChart(genders, "#genderchart");
	citieschart = createCityChart(cities, "#citieschart");
	ageschart = createAgeChart(ages, "#ageschart");
	visitschart = createVisitsChart(visits, "#visitschart");
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
    $('.raffle-table > tbody  > tr').each(function() {
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

$(".raffle-table tr").on('click', function() {
	var email = $(this).children().children('#email').text();
	var gender = $(this).children().children('#gender').text();
	var city = $(this).children().children('#city').text();
	var age = calculateAge(new Date($(this).children().children('#birthdate').text()));
	var visit = parseInt($(this).children().children('#visits').text());
	if( $(this).hasClass('selected')) {
		$(this).removeClass('selected');
		var index = winners.indexOf(email);
		if (index >= 0) {
		  winners.splice( index, 1 );
		}
		removeStat(gender, city, age, visit);
	} else {
		$(this).addClass('selected');
		winners.push(email);
		addStat(gender, city, age, visit);
	}
});

function storeWinners() {
	$.post("storeRaffle.php", {"winners":winners}, function(response){
		//var json = JSON.parse(response);
		console.log(response);
	});
}

//$('.confirm').on('click', function(e){
	
//});

$(document).ready(function() {
	setupCharts();
	setupFilters();
});