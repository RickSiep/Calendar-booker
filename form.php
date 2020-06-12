<?php

class FormDate
{
    private $options;

    public function createForm()
    {
        ?>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" id="booking_form">
                <input type="hidden" name="action" value="save_date_submit">
                <div class="booking-form-wrapper">

                    <div class="booking-form-divider booking-form-left">

                        <label for="date_first_form">Van</label>
                        <label for="date_till_form">Tot</label>

                        <div class="booking-form-dates">
                            <input type="text" id="date_first_form" placeholder="Startdatum" name="from" required readonly>
                            <i class="fas fa-minus fa-2x booking-form-icon"></i>
                            <input type="text" id="date_till_form" name="till" placeholder="Einddatum" required readonly>
                        </div>

                        <div class="booking-form-input">
                            <label for="name_form" class="booking-form-text">Naam</label>
                            <input type="text" id="name_form" placeholder="Uw naam" name="name" class="booking-form-text" required>
                        </div>

                        <div class="booking-form-input">
                            <label for="email_form">Email</label>
                            <input type="email" id="email_form" placeholder="Uw email" name="email" required>
                        </div>

                        <div class="booking-form-input">
                            <label for="phone_form">Telefoonnummer</label>
                            <input type="text" id="phone_form" placeholder="Uw telefoon" name="phone" required>
                        </div>

                    </div>

                    <div class="booking-form-divider booking-form-right">

                        <label for="person_form">Personen 13+ en aantal honden</label>
                        <br>
                        <div class="booking-form-dates">
                            <input type="number" id="person_form" placeholder="Personen" name="people" required>
                            <i class="fas fa-minus fa-2x booking-form-icon"></i>
                            <input type="number" id="dog_form" name="dogs" placeholder="Honden" required>
                        </div>

                        <div class="booking-form-price-section">
                            <div class="booking-form-price-text">
                                <p>Betaling een week van te voren</p>
                                <p id="price_first" class="booking-form-blue-color"></p>
                            </div>

                            <div class="booking-form-price-text">
                                <p>Betalen zo snel mogelijk</p>
                                <p id="price_second" class="booking-form-blue-color"></p>
                            </div>

                            <div class="booking-form-price-text">
                                <p>Totaal</p>
                                <p id="total_price" class="booking-form-blue-color"></p>
                            </div>

                            <div class="booking-form-input">
                                <input type="submit" value="Boek nu!" id="booking_form_submit">
                            </div>

                        </div>

                    </div>
                </div>
        </form>

        <?php
    }

    public function createAdminForm()
    {
        ?>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
            <input type="hidden" name="action" value="save_date_submit">

            <div class="booking-form-wrapper">
                <div class="booking-form-divider booking-form-left">

                    <label for="date_first_form">Van</label>
                    <label for="date_till_form">Tot</label>

                    <div class="booking-form-dates">
                        <input type="text" id="date_first_form_admin" placeholder="Startdatum" name="from" required>
                        <i class="fas fa-minus fa-2x booking-form-icon"></i>
                        <input type="text" id="date_till_form_admin" name="till" placeholder="Einddatum" required>
                    </div>

                    <div class="booking-form-input">
                        <label for="name_form" class="booking-form-text">Naam</label>
                        <input type="text" id="name_form" placeholder="Uw naam" name="name" class="booking-form-text" required>
                    </div>

                    <div class="booking-form-input">
                        <label for="email_form">Email</label>
                        <input type="email" id="email_form" placeholder="Uw email" name="email" required>
                    </div>

                    <div class="booking-form-input">
                        <label for="phone_form">Telefoonnummer</label>
                        <input type="text" id="phone_form" placeholder="Uw telefoon" name="phone" required>
                    </div>

                </div>

                <div class="booking-form-divider booking-form-right">

                    <label for="person_form">Personen 13+ en aantal honden</label>
                    <br>
                    <div class="booking-form-dates">
                        <input type="number" id="person_form" placeholder="Personen" name="people" required>
                        <i class="fas fa-minus fa-2x booking-form-icon"></i>
                        <input type="number" id="dog_form" name="dogs" placeholder="Honden" required>
                    </div>

                    <div class="booking-form-price-section">
                        <div class="booking-form-price-text">
                            <p>Betaling een week van te voren</p>
                            <p id="price_first" class="booking-form-blue-color"></p>
                        </div>

                        <div class="booking-form-price-text">
                            <p>Betalen zo snel mogelijk</p>
                            <p id="price_second" class="booking-form-blue-color"></p>
                        </div>

                        <div class="booking-form-price-text">
                            <p>Totaal</p>
                            <p id="total_price" class="booking-form-blue-color"></p>
                        </div>

                        <div class="booking-form-input">
                            <input type="submit" value="Boek nu!" id="booking_form_submit">
                        </div>

                    </div>

                </div>
            </div>
        </form>

        <?php
    }

    public static function dataFormSubmit()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bookings';

//        Get all post fields
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_text_field($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $people = sanitize_text_field($_POST['people']);
        $dogs = sanitize_text_field($_POST['dogs']);
        $date_from = sanitize_text_field($_POST['from']);
        $date_till = sanitize_text_field($_POST['till']);

//        Calculate the price
        $price = calculatePriceForm($date_from, $date_till, $people, $dogs);

//        Set time so that we can save it with extra time.
        $date_till_dateTime = new DateTime($date_till);

//        Get date from and add the time so that we can compare it with the database
        $date_from_time = $date_from . ' 00:00:00';

//        Check if start date already exists so that we can't get any duplicates
        $results = $wpdb->get_row(
            "    SELECT *
                 FROM  $table_name
                 WHERE start_date = '{$date_from_time}'"
        );

//        If we get a duplicate result, show error page, else we'll save the values
        if (isset($results)) {
            wp_safe_redirect(home_url() . '/error-reserveren');
        } else {

//          Set time so that the calendar.js counts it as an extra day
            $date_till_dateTime->setTime(12, 55);

//          Insert the values into the database.
            $wpdb->insert(
                $table_name,
                [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'status' => 'bezet',
                    'start_date' => $date_from,
                    'end_date' => $date_till_dateTime->format('Y-m-d H:i:s'),
                    'price' => $price,
                    'created_at' => current_time('mysql')
                ]
            );

//          Check the mail text for variables, and if they contain them, replace the string with the value of the var.
            $search = ['$name', '$price'];
            $replace = [$name, $price];
            $mailText = nl2br(file_get_contents(get_option('calendar_mail')));

            $mailTest = str_replace($search, $replace, $mailText);

//          Send a mail to the customer who made a reservation, and one to the owner of the site
            wp_mail($email, 'Cadzand boeking', $mailTest, 'Content-Type: text/html; charset=UTF-8', get_option('attachment'));

            wp_mail('e.willems@ma-web.nl', 'Cadzand boeking',
                '<h2>Een nieuwe boeking op Vakantiehuis Cadzand!</h2>
                    <br>
                    ' . $email . '<p>Heeft een boeking gemaakt, van ' . $date_from . ' tot ' . $date_till . '</p>
                    <br>
                    <p>Voor  €' . $price . ' euro</p>'
                , 'Content-Type: text/html; charset=UTF-8');

            wp_safe_redirect(home_url() . '/succes');
        }
    }
}

//  A function to calculate the price, since we need to check if no one tampered with the javascript price
function calculatePriceForm($start_date, $stop_date, $people, $dogs)
{
    $price = 0;
    $dates = [];
    $date_from = new DateTime($start_date);
    $date_till = new DateTime($stop_date);

//    Calculate people prices
    $people_price = ($people * 1.50);

//    Add dogs to the price
    $dogs_price = ($dogs * 15);
    $price += $dogs_price;

//  While the first date is lower than the last date, get the price from the options, and then set the price and eventually return it.
    while($date_from <= $date_till) {
        array_push($dates, $date_from);
        $date_from->modify('+1 day');
        switch ($date_from->format('m')) {
            case 1: case 2: case 3: case 10: case 11: case 12:
            $price += (int)get_option('low_price');
            $price += $people_price;
            break;

            case 7: case 8:
            $price += (int)get_option('summer_price');
            $price += $people_price;
            break;

            case 4: case 5: case 6: case 9:
            $price += (int)get_option('late_price');
            $price += $people_price;
            break;
        }
    }

    return $price;
}

add_action('admin_post_nopriv_save_date_submit', ['FormDate', 'dataFormSubmit']);
add_action('admin_post_save_date_submit', ['FormDate', 'dataFormSubmit']);
