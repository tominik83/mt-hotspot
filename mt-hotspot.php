<?php

/**
 * Plugin Name: MT-HotSpot
 * Plugin URI:        https://github.com/tominik83/WordPress-Plugins/tree/d81f3622208d63befa9e14642954c8763e30b3fb/log-reg
 * Description:       Mikrotik Hotspot Login Routine
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2 and ...
 * Author:            Mihajlo Tomic
 * Author URI:        https://dev.bibliotehnika.tk/about/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mt-hotspot
 * Domain Path:       /languages
 */

// if( !defined('ABSPATH')) {
//     echo 'Sta pokusavas Brate?';
//     exit;
// }


error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!defined('ABSPATH')) {
    wp_die(__('You can\'t access this page', 'MT-HotSpot'));
}

// check wp version
if (!version_compare(get_bloginfo('version'), '6.4.1', '>=')) {
    $notice = sprintf('%1$s requires WordPress version %2$s or greater. Please update your WordPress to the latest version.', '<strong>MT-HotSpot</strong>', '<strong>6.4.1</strong>');
    add_action('admin_notices', function () use ($notice) {
        ?>
        <div class="notice is-dismissible notice-error">
            <p>
                <?php
                echo $notice;
                ?>
            </p>
        </div>
        <?php
    });
}

// function localize_user_status()
// {
//     wp_localize_script(
//         'welcome',
//         'userStatus',
//         array(
//             'isUserLoggedIn' => is_user_logged_in(),
//         )
//     );
// }

// add_action('wp_enqueue_scripts', 'localize_user_status');


class Hotspot
{

    public function __construct()
    {

        // Add icons
        add_action('wp_enqueue_scripts', array($this, 'add_boxicons_stil'));

        // Add assets (js, css, etc)
        add_action('wp_enqueue_scripts', array($this, 'load_assets'));
        add_action('wp_enqueue_scripts', array($this, 'dodaj_dinamicke_stilove'));


        // Add shortcode
        // add_shortcode('mt-hotspot-form', array($this, 'load_shortcode'));
        add_shortcode('mikrotik_hotspot', array($this, 'mikrotik_login_form'));
        // add_shortcode('mt-hotspot-update', array($this, 'update_shortcode'));
        // Settings Page
        add_action('admin_init', array($this, 'registracija_podesavanja'));
        add_action('admin_menu', array($this, 'settings_page'));

        register_activation_hook(__FILE__, array($this, 'activate_plugin'));

    }

    public function load_assets()
    {
        wp_enqueue_style('mt-hotspot-style', plugin_dir_url(__FILE__) . 'dist/hotspot.css', array(), 1, 'all');

        wp_enqueue_script('mt-hotspot-script', plugin_dir_url(__FILE__) . 'dist/hotspot.js', array('jquery'), 1, true);
    }

    public function settings_page()
    {
        add_menu_page(
            'MT-HotSpot Settings',
            'Hotspot Settings',
            'manage_options',
            'mt_hotspot_settings',
            array($this, 'prikazi_stranicu_podesavanja'),
            'dashicons-admin-generic',
            20
        );
    }

    public function prikazi_stranicu_podesavanja()
    {
        ?>
        <div class="wrap">
            <h1>MT-HotSpot Settings</h1>
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php settings_fields('mt_hotspot_podesavanja'); ?>
                <?php do_settings_sections('mt_hotspot_podesavanja'); ?>

                <!-- Dodajte polje za odabir loga -->
                <label for="hotspot_logo">Hotspot Logo:</label>
                <?php
                $logo_id = get_option('hotspot_logo');
                echo wp_get_attachment_image($logo_id, 'thumbnail');
                ?>
                <input type="hidden" name="hotspot_logo" id="hotspot_logo" value="<?php echo esc_attr($logo_id); ?>">
                <button class="button" id="upload_logo_button">Odaberi logo</button>

                <?php submit_button(); ?>
            </form>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
                    var frame = wp.media({
                        title: 'Odaberi logo',
                        button: { text: 'Odaberi logo' },
                        multiple: false
                    });

                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $('#hotspot_logo').val(attachment.id);
                        $('img').attr('src', attachment.url);
                    });

                    $('#upload_logo_button').on('click', function (e) {
                        e.preventDefault();
                        frame.open();
                    });
                } else {
                    console.error('wp.media is not available');
                }
            });
        </script>
        <?php
    }



    // Funkcija za registraciju podešavanja
    public function registracija_podesavanja()
    {
        register_setting('mt_hotspot_podesavanja', 'hotspot_ime');
        register_setting('mt_hotspot_podesavanja', 'hotspot_logo'); // Dodajte ovu liniju za registraciju loga
        add_settings_section('mt_hotspot_sekcija', 'Hotspot Settings', array($this, 'prikazi_sekciju'), 'mt_hotspot_podesavanja');
        add_settings_field('hotspot_ime', 'Hotspot Name', array($this, 'prikazi_polje_za_unos'), 'mt_hotspot_podesavanja', 'mt_hotspot_sekcija');
        add_settings_field('hotspot_logo', 'Hotspot Logo', array($this, 'prikazi_polje_za_unos_loga'), 'mt_hotspot_podesavanja', 'mt_hotspot_sekcija'); // Dodajte ovu liniju za polje loga
    }

    public function prikazi_polje_za_unos_loga()
    {
        $logo_id = get_option('hotspot_logo');
        echo '<input type="hidden" id="hotspot_logo" name="hotspot_logo" value="' . esc_attr($logo_id) . '" />';
    }

    // Pomoćne funkcije za prikazivanje sekcije i polja za unos
    public function prikazi_sekciju()
    {
        echo '<p>Customize your Hotspot settings here.</p>';
    }

    public function prikazi_polje_za_unos()
    {
        $hotspot_ime = get_option('hotspot_ime', 'Hotspot');
        echo '<input type="text" id="hotspot_ime" name="hotspot_ime" value="' . esc_attr($hotspot_ime) . '" />';
    }

    public function add_boxicons_stil()
    {
        // Proveri da li je stil već uključen
        if (!wp_style_is('boxicons', 'enqueued')) {
            // Ako nije, dodaj stil
            wp_enqueue_style('boxicons', 'https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css');
        }
    }



    public function mikrotik_login_form()
    {
        include(plugin_dir_path(__FILE__) . 'includes/login.php');
    }

    public function mikrotik_logout_form()
    {
        include(plugin_dir_path(__FILE__) . 'includes/logout.html');
    }

    function dodaj_dinamicke_stilove()
    {
        $bg_image = plugin_dir_url(__FILE__) . 'includes/img/bg.jpg';

        $custom_css = "
        .hs-wrapper .form-box {
                background-image: url('$bg_image');
                background-size: cover; /* prilagodi veličinu pozadine */
                /* dodajte dodatne stilove prema potrebi */
            }
        ";

        wp_add_inline_style('mt-hotspot-style', $custom_css);
    }

    public function load_shortcode()
    { ?>



        <?php
    }

    public function create_hotspot_page()
    {
        $hotspot_page_args = array(
            'post_title' => 'Hotspot',
            'post_content' => '[mikrotik_hotspot]', // Dodajte shortcode ako želite
            'post_status' => 'publish',
            'post_type' => 'page',
        );

        $hotspot_page_id = wp_insert_post($hotspot_page_args);

        // Proveri da li je stranica uspešno kreirana
        if ($hotspot_page_id) {
            // Postavi template za kreiranu stranicu
            $this->set_hotspot_template($hotspot_page_id);
        }
    }






    public function set_hotspot_template($page_id)
    {
        $template_path = get_stylesheet_directory() . 'templates/template-hotspot.php';

        // Proveri da li template fajl postoji
        if (locate_template($template_path)) {
            update_post_meta($page_id, '_wp_page_template', $template_path);
        } else {
            // Template fajl nije pronađen, možete prijaviti grešku ili preduzeti odgovarajuće akcije
            error_log('Template file not found: ' . $template_path);
        }
    }




    public function activate_plugin()
    {
        // Pozovi funkciju za kreiranje stranice prilikom aktivacije plugina
        $this->create_hotspot_page();
    }


}

new Hotspot;
