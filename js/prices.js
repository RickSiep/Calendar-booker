let prices = {
    highSeason: 50,
    lateSeason: 40,
    lowSeason: 30,
};

jQuery(document).ready(function($) {
    if($('#date_till_form').length >0 || $('#date_till_form_admin').length > 0 ) {
        $.ajax({
            method : 'GET',
            url : priceScript.pluginsUrl + '/js/load-prices.php',
            data: {format: 'json'},
            success : function (d) {
                data = JSON.parse(d);
                prices['highSeason'] = data['summer_price'];
                prices['lateSeason'] = data['late_price'];
                prices['lowSeason'] = data['low_price'];
            },
        });
    }
});