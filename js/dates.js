// Vars that are used later
let dates = [];
let index;
let day = [];
let endDay = [];
let isDisabled;
let notDisabled;
let startdatesNoTime;
let enddatesNoTime;
let first_selected;
let second_selected;
let first_selected_admin;
let second_selected_admin;
let maxSecondDate;
let setDate = 0;
let price = 0;
let halfPrice = 0;
let minDate = 0;

function getDates(startDate, stopDate) {
    price = 0;
    startDate = moment(startDate);
    stopDate = moment(stopDate);

    while (startDate <= stopDate) {
        dates.push(moment(startDate).format('YYYY-MM-dd'));
        startDate = moment(startDate).add(1, 'days');

        // Check months for pricing
        switch (startDate.month()) {
            case 1: case 2: case 3: case 10: case 11: case 12:
                price += parseInt(prices['lowSeason']);
                halfPrice = (price / 2);
                document.getElementById('price_first').innerHTML = halfPrice;
                document.getElementById('price_second').innerHTML = halfPrice;
                document.getElementById('total_price').innerHTML = price;
                break;
            case 7: case 8:
                price += parseInt(prices['highSeason']);
                halfPrice = (price / 2);
                document.getElementById('price_first').innerHTML = halfPrice;
                document.getElementById('price_second').innerHTML = halfPrice;
                document.getElementById('total_price').innerHTML = price;
                break;
            case 4: case 5: case 6: case 9:
                price += parseInt(prices['lateSeason']);
                halfPrice = (price / 2);
                document.getElementById('price_first').innerHTML = halfPrice;
                document.getElementById('price_second').innerHTML = halfPrice;
                document.getElementById('total_price').innerHTML = price;
                break;
        }
    }
    return dates;
}

jQuery(document).ready(function($) {
    if($('#date_till_form').length >0 || $('#date_till_form_admin').length > 0 ){
        let date_first = $('#date_first_form');
        let date_till = $('#date_till_form');
        let data;
        const end_date = [];
        const start_date = [];

        //      Ajax call
        $.ajax({
            method : 'GET',
            url : datesScript.pluginsUrl + '/js/load-events.php',
            data: {format: 'json'},
            success : function (d) {
                data = JSON.parse(d);
                data.forEach(calendarData => {
                    start_date.push(Object(calendarData)['start']);
                    end_date.push(Object(calendarData)['end']);
                });

                // Make moment objects of them all and push them into day
                start_date.map(x => {
                    day.push(moment(x).format('YYYY-MM-DD'));
                });

                // Same here
                end_date.map(x => {
                    endDay.push(moment(x).format('YYYY-MM-DD'));
                });

                // Have an array of startdates without
                startdatesNoTime = start_date.map(x => {
                   let replacedDate = x.replace(" 00:00:00", "");
                   return replacedDate;
                });

                enddatesNoTime = end_date.map(x => {
                   let replacedDate = x.replace(" 00:00:00", "");
                   return replacedDate;
                });
            },
        });

        // First datepicker
        date_first.datepicker({
            dateFormat: "yy-mm-dd",
            minDate: 0,
            showWeek: 1,
            beforeShowDay: function(date){
                let dateString = jQuery.datepicker.formatDate('yy-mm-dd', date);
                isDisabled = day.indexOf(dateString) == -1;
                return [(date.getDay() == 1 || date.getDay() == 5) && isDisabled]
            },
            onSelect: function(){
                first_selected = moment($(this).val()).format('YYYY-MM-DD');
                // If there is still a value cached, just calculate the price
                if (second_selected) {
                    getDates(first_selected, second_selected);
                }

                // This is the date for the second datepicker that you can't go lower on
                setDate = new Date(first_selected);

                // Un disable the second datepicker
                date_till.prop("disabled", false);

                // Destroy the second datepicker, and refresh it with new data.
                date_till.datepicker("destroy");
                setSecondDatePicker();

                // Sort array in lowest dates.
                const sorted = day.sort(function (a, b) {
                    // '2020-5-20'.split('-')
                    // gives ["2020", "05", "20"]
                    a = a.split('-');
                    b = b.split('-');
                    return a[0] - b[0] || a[1] - b[1] || a[2] - b[2];
                });

                // Calculate what the first day is that is above the set date, and make it the max date.
                for (let i = 0; i < sorted.length; i++) {
                    if (first_selected < day[i]) {
                        // If it's not in the end date array.
                        if (enddatesNoTime.indexOf(day[i]) === -1) {
                            index = startdatesNoTime.indexOf(day[i]);
                            notDisabled = day[index];
                            maxSecondDate = new Date(startdatesNoTime[index]);
                            maxSecondDate.setDate(maxSecondDate.getDate() + 1);
                            console.log(maxSecondDate);
                            date_till.datepicker("option", "maxDate", maxSecondDate);
                            date_till.val("");
                            break;
                        } else {
                            notDisabled = null;
                            maxSecondDate = new Date(day[i]);
                            date_till.datepicker("option", "maxDate", maxSecondDate);
                            date_till.val("");
                            break;
                        }
                        break;
                    }
                }
            }
        });

        // Calculate prices if they are both checked.
        if(second_selected) {
            getDates(first_selected, second_selected);
        }

        // The second datepicker has to be a function, so you can call it again.
        function setSecondDatePicker() {
            date_till.datepicker({
                    dateFormat: "yy-mm-dd",
                    minDate: setDate,
                    beforeShowDay: function(date){
                        let dateString = jQuery.datepicker.formatDate('yy-mm-dd', date);
                        isDisabled = day.indexOf(dateString) == -1;
                        if (dateString === notDisabled) {
                            isDisabled = 1;
                        }
                        return [(date.getDay() == 1 || date.getDay() == 5) && isDisabled]
                    },
                    onSelect: function () {
                        second_selected = moment($(this).val()).format('YYYY-MM-DD');
                        getDates(first_selected, second_selected);
                    }
                });
            }
        }

    // Admin forms
    if($('#date_till_form_admin').length > 0 ) {
        $('#date_first_form_admin').datepicker({
            dateFormat: "yy-mm-dd",
            onSelect: function () {
                first_selected_admin = moment($(this).val()).format('YYYY-MM-DD');
            }
        });

        $('#date_till_form_admin').datepicker({
            dateFormat: "yy-mm-dd",
            onSelect: function () {
                second_selected_admin = moment($(this).val()).format('YYYY-MM-DD');
                getDates(first_selected_admin, second_selected_admin);
            }
        });
    }



});