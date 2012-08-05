/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *       used when the cookie was set.
 *
 * @param String name The name of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given name.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String name The name of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
if( typeof(jQuery.cookie) == "undefined" ) {
    jQuery.cookie = function(name, value, options) {
	if (typeof value != 'undefined') { // name and value given, set cookie
	    options = options || {};
	    if (value === null) {
		value = '';
		options.expires = -1;
	    }
	    var expires = '';
	    if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
		var date;
		if (typeof options.expires == 'number') {
		    date = new Date();
		    date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
		} else {
		    date = options.expires;
		}
		expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
	    }
	    // CAUTION: Needed to parenthesize options.path and options.domain
	    // in the following expressions, otherwise they evaluate to undefined
	    // in the packed version for some reason...
	    var path = options.path ? '; path=' + (options.path) : '';
	    var domain = options.domain ? '; domain=' + (options.domain) : '';
	    var secure = options.secure ? '; secure' : '';
	    document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
	} else { // only name given, get cookie
	    var cookieValue = null;
	    if (document.cookie && document.cookie != '') {
		var cookies = document.cookie.split(';');
		for (var i = 0; i < cookies.length; i++) {
		    var cookie = jQuery.trim(cookies[i]);
		    // Does this cookie string begin with the name we want?
		    if (cookie.substring(0, name.length + 1) == (name + '=')) {
			cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
			break;
		    }
		}
	    }
	    return cookieValue;
	}
    };
}

/*
 * This is the weather plugin
 *
 */

jQuery(function($) {

    var g = window;
    
    g.get_weather = function( cb ) {
	if ($.cookie('loc_longitude') && $.cookie('loc_latitude')) {
	    getWeather(cb);
	} else {
	    g.weather_cb = cb;
	    $("head").append(
		$("<script>").attr("src","http://www.geoplugin.net/json.gp?callback=?")
	    );
	}
    };

    g.geoPlugin = function(data) {
	$.cookie('loc_latitude', data.geoplugin_latitude, {expires: 1});	
	$.cookie('loc_longitude', data.geoplugin_longitude, {expires: 1});
	$.cookie('loc_country', data.geoplugin_countryName, {expires: 1});
	$.cookie('loc_region', data.geoplugin_region, {expires: 1});
	$.cookie('loc_city', data.geoplugin_city, {expires: 1});
	$.cookie('loc_country_code', data.geoplugin_countryCode, {expires: 1});

	var cb = g.weather_cb;
	delete g.weather_cb;
	getWeather( cb );
    }

    g.setWeather = function(data) {
	var clouds = data.weatherObservation.clouds;
	var weather = data.weatherObservation.weatherCondition;
	var temp = data.weatherObservation.temperature;
	var humidity = data.weatherObservation.humidity;
	
	var conditions_img = getConditionsImage(clouds, weather);
	
	var conditions = '';
	if (weather == 'n/a') {
	    if (clouds == 'n/a') {
		conditions = 'fine';
	    } else {
		conditions = clouds;
	    }
	} else {
	    conditions = weather;
	}
	
	$.cookie('loc_conditions', conditions);	
	$.cookie('loc_conditions_img', conditions_img);	
	$.cookie('loc_temp', temp);	
	$.cookie('loc_humidity', humidity);
	$.cookie('loc_desc', weather);

	var cb = g.weather_cb;
	delete g.weather_cb;	
	cb( getConditionsObject(conditions, conditions_img, temp, humidity, weather) );
    }    

    function getWeather( cb ) {
	var latitude = $.cookie('loc_latitude');
	var longitude = $.cookie('loc_longitude');
	
	var loc_conditions = $.cookie('loc_conditions');
	var loc_conditions_img = $.cookie('loc_conditions_img');
	var loc_temp = $.cookie('loc_temp');
	var loc_humidity = $.cookie('loc_humidity');
	var loc_desc = $.cookie('loc_desc');
	
	if (loc_conditions && loc_conditions_img) {
	    cb( getConditionsObject(loc_conditions, loc_conditions_img, loc_temp, loc_humidity, loc_desc) );
	} else {
	    var url = "http://ws.geonames.org/findNearByWeatherJSON?lat=" + latitude + "&lng=" + longitude + "&callback=setWeather";
	    g.weather_cb = cb;
	    $("head").append(
		$("<script>").attr("src",url)
	    );
	}
    }

    function getConditionsImage(clouds, weather) {
	if (weather == 'n/a') {
	    switch (clouds) {
	    case 'n/a':
		return 'sunny.gif';
	    case 'clear sky':
		return 'sunny.gif';
	    case 'few clouds':
		return 'partly_cloudy.gif';
	    case 'scattered clouds':
		return 'partly_cloudy.gif';
	    case 'broken clouds':
		return 'partly_cloudy.gif';
	    default:
		return 'cloudy.gif';
	    }
	} else {
	    weather = weather.replace('light ', '').replace('heavy ', '').replace(' in vicinity', '');
	    switch(weather) {
	    case 'drizzle':
		return 'rain.gif';
	    case 'rain':
		return 'rain.gif';
	    case 'snow':
		return 'snow.gif';
	    case 'snow grains':
		return 'sleet.gif';
	    case 'ice crystals':
		return 'icy.gif';
	    case 'ice pellets':
		return 'icy.gif';
	    case 'hail':
		return 'sleet.gif';
	    case 'small hail':
		return 'sleet.gif';
	    case 'snow pellets':
		return 'sleet.gif';
	    case 'unknown precipitation':
		return 'rain.gif';
	    case 'mist':
		return 'mist.gif';
	    case 'fog':
		return 'fog.gif';
	    case 'smoke':
		return 'smoke.gif';
	    case 'volcanic ash':
		return 'smoke.gif';
	    case 'sand':
		return 'dust.gif';
	    case 'haze':
		return 'haze.gif';
	    case 'spray':
		return 'rain.gif';
	    case 'widespread dust':
		return 'dust.gif';
	    case 'squall':
		return 'flurries.gif';
	    case 'sandstorm':
		return 'dust.gif';
	    case 'duststorm':
		return 'dust.gif';
	    case 'well developed dust':
		return 'dust.gif';
	    case 'sand whirls':
		return 'dust.gif';
	    case 'funnel cloud':
		return 'flurries.gif';
	    case 'tornado':
		return 'storm.gif';
	    case 'waterspout':
		return 'storm.gif';
	    case 'showers':
		return 'storm.gif';
	    case 'thunderstorm':
		return 'thunderstorm.gif';
	    default:
		if (weather.indexOf("rain")) {
		    return 'rain.gif';
		} else if (weather.indexOf("snow")) {
		    return 'snow.gif';
		} else if (weather.indexOf("thunder")) {
		    return 'thunderstorm.gif';
		} else if (weather.indexOf("dust")) {
		    return 'dust.gif';
		} else {
		    return 'sunny.gif';
		}
	    }
	}
    }

    function getConditionsObject(conditions, conditions_img, temp, humidity, desc) {
	var country = $.cookie('loc_country');
	var region = $.cookie('loc_region');
	var city = $.cookie('loc_city');
	var loc_country_code = $.cookie('loc_country_code');
	if (loc_country_code == 'US') {
	    temp = parseInt(temp) + 32;
	    temp_type = "F";
	} else {
	    temp_type = "C";
	}

	var r = {};
	r.cond = conditions;
	r.img = conditions_img;
	r.temp = temp;
	r.humidity = humidity;
	r.country = country;
	r.region = region;
	r.city = city;
	r.desc = desc;
	return r;
    }
});
