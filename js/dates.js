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
let dogPrice = 0;
let people = 0;
let counter = 0;

// Get today plus one week, and set it as max date.
let todayDate = new Date();
todayDate.setDate(todayDate.getDate() + 7);

// This function calculates the prices of the first date and the second value
function getPrices(startDate, stopDate) {
    // Check how many people there, and add 1.50 for every person
    let peoplePrice = (people * 1.50);
    price = 0;
    price += dogPrice;

    // Make a moment object of the dates to make it easier to calculate.
    startDate = moment(startDate);
    stopDate = moment(stopDate);

    // While the start date is lower than the stop date.
    while (startDate <= stopDate) {
        dates.push(moment(startDate).format('YYYY-MM-dd'));
        startDate = moment(startDate).add(1, 'days');

        // Check months for pricing, add the price of the months.
        switch (startDate.month()) {
            case 0: case 1: case 2: case 9: case 10: case 11:
                price += parseInt(prices['lowSeason']);
                price += peoplePrice;
                halfPrice = (price / 2);
                document.getElementById('price_first').innerHTML = '€' + halfPrice;
                document.getElementById('price_second').innerHTML = '€' + halfPrice;
                document.getElementById('total_price').innerHTML = '€' + price;
                break;
            case 6: case 7:
                price += parseInt(prices['highSeason']);
                price += peoplePrice;
                halfPrice = (price / 2);
                document.getElementById('price_first').innerHTML = '€' + halfPrice;
                document.getElementById('price_second').innerHTML = '€' + halfPrice;
                document.getElementById('total_price').innerHTML = '€' + price;
                break;
            case 3: case 4: case 5: case 8:
                price += parseInt(prices['lateSeason']);
                price += peoplePrice;
                halfPrice = (price / 2);
                document.getElementById('price_first').innerHTML = '€' + halfPrice;
                document.getElementById('price_second').innerHTML = '€' + halfPrice;
                document.getElementById('total_price').innerHTML = '€' + price;
                break;
        }
    }
    return dates;
}

// Calculate all the days in between the start dates and end dates, so that they can't be selected no matter what.
function getDates(firstDate, secondDate) {
    let dateArray = [];
    let currentDate = moment(firstDate);
    currentDate.add(1, 'days');
    let secondDateStop = moment(secondDate);
    secondDateStop.subtract(1, 'days');

    while (currentDate <= secondDateStop) {
        day.push( moment(currentDate).format('YYYY-MM-DD') )
        currentDate = moment(currentDate).add(1, 'days');
    }

    return dateArray;
}

// Load Jquery, and make an ajax call
jQuery(document).ready(function($) {
    if($('#date_till_form').length > 0 || $('#date_till_form_admin').length > 0 ){
        let date_first = $('#date_first_form');
        let date_till = $('#date_till_form');
        let data;
        const end_date = [];
        const start_date = [];

        //  Ajax call
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

                // Have an array of start dates without time
                startdatesNoTime = start_date.map(x => {
                   let replacedDate = x.replace(" 00:00:00", "");
                   return replacedDate;
                });

                // Have an array of end times with time
                enddatesNoTime = end_date.map(x => {
                   let replacedDate = x.replace(" 12:55:00", "");
                   return replacedDate;
                });

                // Disable the remaining dates
                startdatesNoTime.forEach(element => {
                    getDates(element, enddatesNoTime[counter]);
                    counter++;
                })
            },
        });



        // First datepicker
        date_first.datepicker({
            dateFormat: "yy-mm-dd",
            minDate: todayDate,
            showWeek: 1,
            beforeShowDay: function(date){
                // This function is a true or false, it checks for each date.
                // If a date is true, then it's not disabled, if it's false, it's disabled.
                // We disable every day that isn't monday or friday, and every date that already has a start date or is booked over.
                let dateString = jQuery.datepicker.formatDate('yy-mm-dd', date);
                isDisabled = day.indexOf(dateString) == -1;
                return [(date.getDay() == 1 || date.getDay() == 5) && isDisabled]
            },
            onSelect: function(){
                // Set the price to 0 when something is selected
                price = 0;
                first_selected = moment($(this).val()).format('YYYY-MM-DD');
                // If there is still a value cached, just calculate the price
                document.getElementById('price_first').innerHTML = '€0';
                document.getElementById('price_second').innerHTML = '€0';
                document.getElementById('total_price').innerHTML = '€0';

                // This is the date for the second datepicker that you can't go lower on
                setDate = new Date(first_selected);
                setDate.setDate(setDate.getDate() + 1);

                // Un disable the second datepicker
                date_till.prop("disabled", false);
                date_till.val("");

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
                            // Check if you select a friday, and if it's possible to then book on the next monday
                            index = startdatesNoTime.indexOf(day[i]);
                            notDisabled = day[i];
                            maxSecondDate = new Date(startdatesNoTime[index]);
                            maxSecondDate.setDate(maxSecondDate.getDate() + 1);
                            date_till.datepicker("destroy");
                            setSecondDatePicker();
                            date_till.datepicker("option", "maxDate", maxSecondDate);
                            break;
                        }
                        break;
                    }
                }
            }
        });

        // Calculate prices if they are both checked.
        if(second_selected) {
            getPrices(first_selected, second_selected);
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
                        getPrices(first_selected, second_selected);
                    }
                });
            }
        }

    // Dog prices and people prices.
    $('#dog_form').change(function() {
        let dogs = $('#dog_form').val();
        dogPrice = (dogs * 15);
        if (first_selected && second_selected) {
            getPrices(first_selected, second_selected);
        }
    });

    $('#person_form').change(function() {
        people = $('#person_form').val();
        if (first_selected && second_selected) {
            getPrices(first_selected, second_selected);
        }
    });

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
                getPrices(first_selected_admin, second_selected_admin);
            }
        });
    }
});