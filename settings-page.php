<?php

require_once('form.php');

class SettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $wpdb;


    public function __construct()
    {
        add_action('admin_menu', [$this, 'addPluginPage']);
        add_action('admin_init', [$this, 'pageInit']);
    }

    /**
     * Add options page
     */
    public function addPluginPage()
    {
//        Add the menu page
        add_menu_page(
            __('calendar-settings', 'textdomain'),
            'Calendar menu',
            'manage_options',
            'calendar-menu',
            array($this, 'createAdminPage'),
            'dashicons-welcome-widgets-menus',
            6
        );
//        Check all the orders page
        add_submenu_page(
            'calendar-menu',
            'Check orders',
            'Check orders',
            'manage_options',
            'calender-menu',
            array($this, 'createOrderPage')
        );
//        Nieuwe date page
        add_submenu_page(
            'calendar-menu',
            'Nieuwe datum',
            'Nieuwe datum',
            'manage_options',
            'date-menu',
            array($this, 'createNewDatePage')
        );
//        For writing the mail
        add_submenu_page(
            'calendar-menu',
            'Mail',
            'Mail',
            'manage_options',
            'mail-menu',
            array($this, 'createMailPage')
        );

//        Edit page
        add_submenu_page(
            '',
            'Bewerken',
            'Bewerken',
            'manage_options',
            'edit-menu',
            array($this, 'createNewEditPage')
        );
    }

    /**
     * Options page callback
     */

    /**
     * Register and add settings
     */
    public function pageInit()
    {
        register_setting(
            'calendar-settings-group', // Option group
            'calendar-settings', // Option name
            [ $this, 'sanitize' ] // Sanitize
        );

        add_settings_section(
            'price_setting', // ID
            'Vakantiehuis settings', // Title
            [ $this, 'printSectionInfo' ], // Callback
            'calender-settings' // Page
        );

        add_settings_field(
            'high_season',
            'Hoogseizoen prijs',
            [ $this, 'setSummerPrice' ],
            'calender-settings',
            'price_setting'
        );

        add_settings_field(
            'late_season',
            'Naseizoen prijs',
            [ $this, 'setLateSeason' ],
            'calender-settings',
            'price_setting'
        );

        add_settings_field(
            'low_season',
            'Laag seizoen prijs',
            [ $this, 'setLowSeason' ],
            'calender-settings',
            'price_setting'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function printSectionInfo()
    {
        $translated = __('Voeg hier je prijzen toe!');
        print $translated;
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function setSummerPrice()
    {
        printf(
            '<input type="text" id="high_season" name="calender-settings[high_season]" 
            value="' . get_option('summer_price') . '" />'
        );
    }

    public function setLateSeason()
    {
        printf(
            '<input type="text" id="late_season" name="calender-settings[late_season]" 
            value="' . get_option('late_price') . '" />'
        );
    }

    public function setLowSeason()
    {
        printf(
            '<input type="text" id="low_season" name="calender-settings[low_season]" 
            value="' . get_option('low_price') . '" />'
        );
    }

//    Sub pages

    public function createAdminPage()
    {
        // Set class property

        ?>
        <div class="wrap">
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                <?php
                // This prints out all hidden setting fields
                settings_fields('calender-settings-group');
                do_settings_sections('calender-settings');
                submit_button();
                ?>
                <input type="hidden" name="action" value="prices_send">
            </form>
        </div>
        <?php
    }

    public function createOrderPage()
    {
        global $wpdb;

        $table = $wpdb->prefix . 'bookings';

        $results = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id");

        ?>
        <table class="admin-table-booking">
            <thead>
            <tr>
                <th scope="col" class="admin-th-booking">Naam</th>
                <th scope="col" class="admin-th-booking">Email</th>
                <th scope="col" class="admin-th-booking">Telefoon</th>
                <th scope="col" class="admin-th-booking">Status</th>
                <th scope="col" class="admin-th-booking">Begin</th>
                <th scope="col" class="admin-th-booking">Eind</th>
                <th scope="col" class="admin-th-booking">Prijs</th>
                <th scope="col" class="admin-th-booking">Verwijder</th>
                <th scope="col" class="admin-th-booking">Verzend</th>
            </tr>
            </thead>
            <tbody>
                <?php
                foreach($results as $result) {
                    ?>
                    <tr>
                        <th scope="row" class="admin-th-booking"><?php echo $result->name ?></th>
                        <td class="admin-td-booking"><a href='mailto:<?php echo $result->email ?>'><?php echo $result->email ?></a></td>
                        <td class="admin-td-booking"><?php echo $result->phone ?></td>
                        <td class="admin-td-booking"><?php echo $result->status ?></td>
                        <td class="admin-td-booking"><?php echo $result->start_date ?></td>
                        <td class="admin-td-booking"><?php echo $result->end_date ?></td>
                        <td class="admin-td-booking"><?php echo $result->price ?></td>
                        <td class="admin-td-booking">
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                                <input type="hidden" name="action" value="delete_booking">
                                <input type="hidden" value="<?php echo $result->id?>" name="id">
                                <input type="submit" value="Delete" class="button button-primary" onclick="return(confirm('Wil je deze boeking verwijderen?'))">
                            </form>
                        </td>
                        <td>
                            <a href="?page=edit-menu&id=<?php echo $result->id ?>" class="button button-primary">Edit</a>
                        </td>
                    </tr>

                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    }

    public function createNewDatePage()
    {
        $form = new FormDate();
        $form->createAdminForm();
    }

//    Create mail page
    public function createMailPage()
    {
        ?>
        <h3>Mail is: <?php echo get_option('calendar_mail')?></h3>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_mail">
            <h2>Tekst voor de mail</h2>
            <input type="file" name="mail" accept=".txt" value="mail">
            <br>
            <br>

            <h3>Attachment is: <?php echo get_option('attachment')?></h3>
            <h2>File die bij de mail zit</h2>
            <input type="file" value="attachment" accept=".doc,.docx,.pdf" name="attachment">
            <br>
            <input type="submit" class="button btn-primary" value="Verstuur" name="submit">
        </form>
      <?php
    }

    /**
     * Create edit form
     *
     */
    public function createNewEditPage()
    {
        global $wpdb;

        $id = $_GET['id'];

        $table = $wpdb->prefix . 'bookings';

        $result = $wpdb->get_results( "SELECT * FROM {$table} WHERE id = $id");

        $result = json_decode(json_encode($result), true);

        ?>
        <h3>Bewerk <?php echo $result[0]['name'] ?></h3>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
            <div class="booking-form-wrapper">
                <input type="hidden" name="action" value="save_booking">
                <input type="hidden" name="id" value="<?php echo $id?>">

                <div class="booking-form-divider booking-form-left">

                    <label for="date_first_form">Van</label>
                    <label for="date_till_form">Tot</label>

                    <div class="booking-form-dates">
                        <input type="text" id="date_first_form_admin" placeholder="Startdatum" name="from" value="<?php echo $result[0]['start_date']?>" required>
                        <i class="fas fa-minus fa-2x booking-form-icon"></i>
                        <input type="text" id="date_till_form_admin" name="till" placeholder="Einddatum" value="<?php echo $result[0]['end_date']?>" required>
                    </div>

                    <div class="booking-form-input">
                        <label for="name_form" class="booking-form-text">Naam</label>
                        <input type="text" id="name_form" placeholder="Uw naam" name="name" class="booking-form-text" value="<?php echo $result[0]['name'] ?>" required>
                    </div>

                    <div class="booking-form-input">
                        <label for="email_form">Email</label>
                        <input type="email" id="email_form" placeholder="Uw email" name="email" value="<?php echo $result[0]['email'] ?>" required>
                    </div>

                    <div class="booking-form-input">
                        <label for="phone_form">Telefoonnummer</label>
                        <input type="text" id="phone_form" placeholder="Uw telefoon" value="<?php echo $result[0]['phone'] ?>" name="phone" required>
                    </div>

                </div>

                <div class="booking-form-divider booking-form-right">

                    <div class="booking-form-input">
                        <label for="status">Status</label>
                        <select name="status">
                            <?php
                            if($result[0]['status'] == 'bezet') {
                                ?>
                                <option value="bezet">bezet</option>
                                <option value="verhuurd">verhuurd</option>
                                <?php
                            } else {
                                ?>
                                <option value="verhuurd">verhuurd</option>
                                <option value="bezet">bezet</option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>

                    <div class="booking-form-price-section">
                        <div class="booking-form-price-text">
                            <p>Aanbetaling</p>
                            <p id="price_first" class="booking-form-blue-color"></p>
                        </div>
                        <div class="booking-form-price-text">
                            <p>Op aankomst</p>
                            <p id="total_price" class="booking-form-blue-color"></p>
                        </div>
                    </div>

                    <input type="submit" value="Verstuur edit" class="button button-primary">

                </div>
            </div>
        </form>
        <?php
    }

}

// Sets the updated prices
function setPrices()
{

    $prices = [];

    $summer_price = sanitize_text_field($_POST['calender-settings']['high_season']);
    $late_price = sanitize_text_field($_POST['calender-settings']['late_season']);
    $low_price = sanitize_text_field($_POST['calender-settings']['low_season']);

    $prices['summer_price'] = $summer_price;
    $prices['late_price'] = $late_price;
    $prices['low_price'] = $low_price;

    foreach($prices as $name => $value)
    {
        if(numeric($value)) {
            update_option($name, $value, 0);
        }
    }

    $location = wp_get_referer();
    header("Location: $location");

}

// Deletes a booking
function deleteBooking()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'bookings';
    $id = $_POST['id'];

    $wpdb->delete($table_name, array('id' => $_POST['id']));

    wp_safe_redirect(wp_get_referer());

}

// Saves an edit of the booking
function saveBooking()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'bookings';
    $id = $_POST['id'];

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_text_field($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $status = sanitize_text_field($_POST['status']);
    $date_from = sanitize_text_field($_POST['from']);
    $date_till = sanitize_text_field($_POST['till']);
    $price = calculatePrice($date_from, $date_till);

    $date_till = new DateTime($date_till);

    $date_till->setTime(12, 55);

//    The data you get from the post
    $data_post = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'status' => $status,
        'start_date' => $date_from,
        'end_date' => $date_till->format('Y-m-d H:i:s'),
        'price' => $price,
        'created_at' => current_time('mysql')
    ];

//    Get the id of the user and put it into an array, because it works better with wpdb update.
    $data_id = [
            'id' => $id
    ];

//    Update the post
    $wpdb->update(
        $table_name,
        $data_post,
        $data_id
    );

    wp_safe_redirect(wp_get_referer());

}

function saveMail()
{
//    Check if the mail.txt isn't empty, then run function
    if(!empty($_FILES["mail"]["name"])) {
        if (get_option('calendar_mail')) {
            unlink(get_option('calendar_mail'));
        }
        update_option('calendar_mail', uploadFile($_FILES['mail']), false);
    }

//    Check if the attachment itself isn't empty
    if(!empty($_FILES["attachment"]["name"])) {
        if (get_option('attachment')) {
            unlink(get_option('attachment'));
        }
        update_option('attachment', uploadFile($_FILES['attachment']), false);
    }

   wp_safe_redirect(wp_get_referer());
}

function numeric($num){
    // will check if is numeric
    $int = $num;
    if(empty($int) or strcmp(preg_replace("/[^0-9,.]/", "", $int), $num) != 0 )
    {
        return false;
    }
    else return true;
}

function uploadFile($file) {
//    The directory where the file is going to go.
    $targetDir = __DIR__ . '\attachments\\';
    $fileNameDoc = basename($file["name"]);

//    Check the filepath, and what filetype it is.
    $targetFilePath = $targetDir . $fileNameDoc;
    $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

//  If the file isn't empty
    if(!empty($file["name"])) {
        //allow certain file formats
        $allowTypes = array('txt', 'docx', 'doc', 'pdf');
        if (in_array($fileType, $allowTypes)) {
            $targetFilePath = str_replace('\\', '/', $targetFilePath);
            copy($file["tmp_name"], $targetFilePath);
            $attachment = $targetFilePath;
            return $attachment;
        }
    }
}

function calculatePrice($start_date, $stop_date)
{
    $price = 0;
    $dates = [];
    $date_from = new DateTime($start_date);
    $date_till = new DateTime($stop_date);

    while($date_from <= $date_till) {
        array_push($dates, $date_from);
        $date_from->modify('+1 day');
        switch ($date_from->format('m')) {
            case 1: case 2: case 3: case 10: case 11: case 12:
            $price += (int)get_option('low_price');
            break;
            case 7: case 8:
            $price += (int)get_option('summer_price');
            break;
            case 4: case 5: case 6: case 9:
            $price += (int)get_option('late_price');
            break;
        }
    }
    return $price;
}

add_action('admin_post_prices_send', 'setPrices');
add_action('admin_post_delete_booking', 'deleteBooking');
add_action('admin_post_save_booking', 'saveBooking');
add_action('admin_post_save_mail', 'saveMail');

if (is_admin()) {
    $my_settings_page = new SettingsPage();
}

