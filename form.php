<?php
//session_start();

class FormDate
{
    private $options;

    public function createForm()
    {

////      Token to prevent CSRF
//        $token = md5(uniqid(rand(), TRUE));
//        $_SESSION['token'] = $token;
//
//        if (!isset($_SESSION['token']))
//        {
//            $_SESSION['token'] = md5(uniqid(rand(), TRUE));
//        }

        ?>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                <input type="hidden" name="action" value="save_date_submit">
<!--                <input type="hidden" name="token" value="-->
                <?php //echo $token; ?>
                <!--" />-->

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

                        <div class="booking-form-input">
                            <label for="phone_form">Personen boven de 13</label>
                            <input type="number" id="person_form" placeholder="Personen" name="people" required>
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
                                <input type="submit" value="Boek nu!">
                            </div>

                        </div>

                    </div>
                </div>
        </form>

        <?php
    }

    public function createAdminForm()
    {
//      Token to prevent CSRF
//        $token = md5(uniqid(rand(), TRUE));
//        $_SESSION['token'] = $token;
//
//        if (!isset($_SESSION['token']))
//        {
//            $_SESSION['token'] = md5(uniqid(rand(), TRUE));
//        }
        ?>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
            <input type="hidden" name="action" value="save_date_submit">
<!--            <input type="hidden" name="token" value="--><?php //echo $token; ?><!--" />-->

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

                    <div class="booking-form-input">
                        <label for="phone_form">Personen boven de 13</label>
                        <input type="number" id="person_form" placeholder="Personen" name="people" required>
                    </div>

                    <div class="booking-form-price-section">

                        <div class="booking-form-price-text">
                            <p>Betaling een week van tevoren</p>
                            <p id="price_first" class="booking-form-blue-color"></p>
                        </div>

                        <div class="booking-form-price-text">
                            <p>Op aankomst</p>
                            <p id="price_second" class="booking-form-blue-color"></p>
                        </div>

                        <div class="booking-form-price-text">
                            <p>Totaal</p>
                            <p id="total_price" class="booking-form-blue-color"></p>
                        </div>

                    </div>

                    <input type="submit" value="Boek nu!">

                </div>
            </div>
        </form>

        <?php
    }

    public static function dataFormSubmit()
    {
//        if ($_POST['token'] == $_SESSION['token'])
//        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'bookings';

            $name = sanitize_text_field($_POST['name']);
            $email = sanitize_text_field($_POST['email']);
            $phone = sanitize_text_field($_POST['phone']);
            $people = sanitize_text_field($_POST['people']);
            $date_from = sanitize_text_field($_POST['from']);
            $date_till = sanitize_text_field($_POST['till']);
            $price = calculatePriceForm($date_from, $date_till, $people);

            $date_till_dateTime = new DateTime($date_till);

            $date_till_dateTime->setTime(12, 55);

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
//        }

////      remove all session variables
//        session_unset();
//
////      destroy the session
//        session_destroy();

        $search = ['$name', '$price'];
        $replace = [$name, $price];
        $mailText = nl2br(file_get_contents(get_option('calendar_mail')));

        $mailTest = str_replace($search, $replace, $mailText);

        wp_mail($email, 'Cadzand boeking', $mailTest, 'Content-Type: text/html; charset=UTF-8', get_option('attachment'));

        wp_mail('rick-siepelinga@hotmail.com', 'Cadzand boeking',
            '<h2>Een nieuwe boeking</h2>
             <br>
             ' . $email . '<p>Heeft een boeking gemaakt, van ' . $date_from . ' tot ' . $date_till . '</p>
             <br>
              <p>Voor  €' . $price . ' euro</p>'
            , 'Content-Type: text/html; charset=UTF-8');

        wp_safe_redirect(wp_get_referer());
    }
}

function calculatePriceForm($start_date, $stop_date, $people)
{
    $price = 0;
    $dates = [];
    $date_from = new DateTime($start_date);
    $date_till = new DateTime($stop_date);

//    Calculate people prices
    $people_price = ($people * 1.50);

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
