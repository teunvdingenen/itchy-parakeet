function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
    return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

function HSVtoRGB(h, s, v) {
    var r, g, b, i, f, p, q, t;
    if (arguments.length === 1) {
        s = h.s, v = h.v, h = h.h;
    }
    i = Math.floor(h * 6);
    f = h * 6 - i;
    p = v * (1 - s);
    q = v * (1 - f * s);
    t = v * (1 - (1 - f) * s);
    switch (i % 6) {
        case 0: r = v, g = t, b = p; break;
        case 1: r = q, g = v, b = p; break;
        case 2: r = p, g = v, b = t; break;
        case 3: r = p, g = q, b = v; break;
        case 4: r = t, g = p, b = v; break;
        case 5: r = v, g = p, b = q; break;
    }
    return {
        r: Math.round(r * 255),
        g: Math.round(g * 255),
        b: Math.round(b * 255)
    };
}

function calculateAge(birthday) { // birthday is a date
    var ageDifMs = Date.now() - birthday.getTime();
    var ageDate = new Date(ageDifMs); // miliseconds from epoch
    return Math.abs(ageDate.getUTCFullYear() - 1970);
}

function createVisitsChart(visits, canvas) {
	var ctx = $(canvas).get(0).getContext("2d");
	var labeldata = [];
	var values = [];
	$.each(visits, function(key, val) {
		values.push(val);
		labeldata.push(key);
	});
	var bardata = {
		labels : labeldata,
		datasets : [{
			label: "Leeftijden",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: values
		}]
	};
	return new Chart(ctx).Bar(bardata);
}

function createSignDateChart(signupdates, canvas) {
	var ctx = $(canvas).get(0).getContext("2d");
	var labeldata = [];
	var dates = [];
	$.each(signupdates, function(key, val) {
		dates.push(val);
		labeldata.push(key);
	});
	var linedata = {
		labels : labeldata,
		datasets : [{
			label: "Inschrijvingen",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: dates
		}]
	};
	return new Chart(ctx).Line(linedata);
}

function createContribChart(contrib, canvas) {
	var ctx = $(canvas).get(0).getContext("2d");
	var elements = 0; 
	$.each(contrib, function(key) {
		elements++;
	});
	var colorParts = 1 / (elements + 4);
	var data = [];
	var i = 2;
	$.each(contrib, function(key, val) {
		var rgbcolor = HSVtoRGB(0.5, 0.2, i*colorParts);
		var colorhex = rgbToHex(rgbcolor.r, rgbcolor.g, rgbcolor.b);
		data.push({value: val, color: colorhex, label:key});
		i++;
	});
	return new Chart(ctx).Pie(data);
}

function createGenderChart(genderInfo, canvas) {
	var ctx = $(canvas).get(0).getContext("2d");
	var colorMale = HSVtoRGB(0.5, 0.2, 0.3);
	colorMale = rgbToHex(colorMale.r, colorMale.g, colorMale.b);
	var colorFemale = HSVtoRGB(0.5, 0.2, 0.6);
	colorFemale = rgbToHex(colorFemale.r, colorFemale.g, colorFemale.b);
	var men = genderInfo["male"];
	var women = genderInfo["female"];
	if( men === null ) { men = 0;}
	if( women === null) { women = 0;}
	var data = [
		{
			value: men,
			color: colorMale,
			label:"male"
		},{
			value: women,
			color:colorFemale,
			label:"female"
		}];
	return new Chart(ctx).Pie(data);
}

function createCityChart(cities, canvas) {
	var ctx = $(canvas).get(0).getContext("2d");

	var labeldata = [];
	var values = [];
	$.each(cities, function(key, val) {
		values.push(val);
		labeldata.push(key);
	});
	var bardata = {
		labels : labeldata,
		datasets : [{
			label: "Steden",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: values
		}]
	};
	return new Chart(ctx).Bar(bardata);
}

function createAgeChart(age, canvas) {
	var ctx = $(canvas).get(0).getContext("2d");
	var labeldata = [];
	var dates = [];
	$.each(age, function(key, val) {
		dates.push(val);
		labeldata.push(key);
	});
	var linedata = {
		labels : labeldata,
		datasets : [{
			label: "Leeftijden",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: dates
		}]
	};
	return new Chart(ctx).Bar(linedata);
}