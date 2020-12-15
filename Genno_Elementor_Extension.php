<?php
/**
 * Plugin Name: Genoo Elementor Extension
 * Description:  This plugin requires the WPMKtgEngine or Genoo plugin installed before order to activate.
 * Version:     1.3.9
 * Author:      Genoo
 * Text Domain: genoo-elementor-extension
 */

if (!defined('ABSPATH'))
{
    exit; // Exit if accessed directly.
    
}

/**
 * Main Genoo Elementor Extension
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
use \Elementor\Plugin;

require_once(plugin_dir_path( __FILE__ ).'deploy/updater.php' );
wpme_genno_elementor_updater_init(__FILE__);

register_activation_hook(__FILE__, function () {
    // Basic extension data
    global $wpdb;
    $fileFolder = basename(dirname(__FILE__));
    $file = basename(__FILE__);
    $filePlugin = $fileFolder . DIRECTORY_SEPARATOR . $file;
    // Activate?
    $activate = FALSE;
    $isGenoo = FALSE;
    // Get api / repo
    if (class_exists('\WPME\ApiFactory') && class_exists('\WPME\RepositorySettingsFactory')) {
        $activate = TRUE;
        $repo = new \WPME\RepositorySettingsFactory();
        $api = new \WPME\ApiFactory($repo);
        if (class_exists('\Genoo\Api')) {
            $isGenoo = TRUE;
        }
    } elseif (class_exists('\Genoo\Api') && class_exists('\Genoo\RepositorySettings')) {
        $activate = TRUE;
        $repo = new \Genoo\RepositorySettings();
        $api = new \Genoo\Api($repo);
        $isGenoo = TRUE;
    } elseif (class_exists('\WPMKTENGINE\Api') && class_exists('\WPMKTENGINE\RepositorySettings')) {
        $activate = TRUE;
        $repo = new \WPMKTENGINE\RepositorySettings();
        $api = new \WPMKTENGINE\Api($repo);
    }
    // 1. First protectoin, no WPME or Genoo plugin
        if ($activate == FALSE && $isGenoo == FALSE) { ?>
  
<div class="alert">
<p style="font-family:Segoe UI;font-size:14px;">This plugin requires the WPMKtgEngine or Genoo plugin installed before order to activate</p>
</div>
    <?php die;
 
 genoo_wpme_deactivate_plugin($filePlugin, 'This extension requires WPMktgEngine or Genoo plugin to work with.');
    } else {
        // Make ACTIVATE calls if any?
        
    }

});


add_action('admin_menu', 'Genoo_elementor');
function Genoo_elementor()
{
    add_menu_page('Genoo Addons', 'Genoo Addons', 'manage_options', 'api_manager', 'viewDashboard');
}

function viewDashboard()
{
    echo "genoo addons plugin occurs";
}

//add form,cta,survey widgets in elementor categories.
function add_elementor_widget_categories()
{

    $elementsManager = Plugin::instance()->elements_manager;
    $elementsManager->add_category('Genoo-elementor', array(
        'title' => 'Genoo Addons',
        'icon' => 'fonts',
    ));

}

add_action('elementor/elements/categories_registered', 'add_elementor_widget_categories');
final class Genoo_Elementor_Extension
{

    /**
     * Plugin Version
     *
     * @since 1.0.0
     *
     * @var string The plugin version.
     */
    const VERSION = '1.0.0';

    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     *
     * @var string Minimum Elementor version required to run the plugin.
     */
    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     *
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '7.0';

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var Genoo_Elementor_Extension The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     *
     * @access public
     * @static
     *
     * @return Genoo_Elementor_Extension An instance of the class.
     */
    public static function instance()
    {

        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    /**
     * Constructor
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function __construct()
    {

        add_action('init', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'init']);

    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function i18n()
    {

        load_plugin_textdomain('genoo-elementor-extension');

    }

    /**
     * Initialize the plugin
     *
     * Load the plugin only after Elementor (and other plugins) are loaded.
     * Checks for basic plugin requirements, if one check fail don't continue,
     * if all check have passed load the files required to run the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */

    public function init()
    {

        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded'))
        {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>='))
        {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<'))
        {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }

        //Include plugin files
        //$this->includes();
        // Register widgets
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
        add_action('elementor/init', function ()
        {
            $elementsManager = Plugin::instance()->elements_manager;
            $elementsManager->add_category('custom-widgets', array(
                'title' => 'Custom Widgets',
                'icon' => 'fonts',
            ));
        });

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_missing_main_plugin()
    {

        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor */
        esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'genoo-elementor-extension') , '<strong>' . esc_html__('Genoo Elementor Extension', 'genoo-elementor-extension') . '</strong>', '<strong>' . esc_html__('Elementor', 'genoo-elementor-extension') . '</strong>');

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_elementor_version()
    {

        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
        esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'genoo-elementor-extension') , '<strong>' . esc_html__('Genoo Elementor Extensionn', 'genoo-elementor-extension') . '</strong>', '<strong>' . esc_html__('Elementor', 'genoo-elementor-extension') . '</strong>', self::MINIMUM_ELEMENTOR_VERSION);

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_php_version()
    {

        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
        /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
        esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'genoo-elementor-extension') , '<strong>' . esc_html__('Genoo Elementor Extension', 'genoo-elementor-extension') . '</strong>', '<strong>' . esc_html__('PHP', 'genoo-elementor-extension') . '</strong>', self::MINIMUM_PHP_VERSION);

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);

    }

    /**
     * Include Files
     *
     * Load required plugin core files.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function includes()
    {

        require_once (__DIR__ . '/form-widget.php');
        require_once (__DIR__ . '/survey-widget.php');
        require_once (__DIR__ . '/cta-widget.php');

    }

    public function register_widgets()
    {

        $this->includes(); // <- register the widget class name here
        \Elementor\Plugin::instance()
            ->widgets_manager
            ->register_widget_type(new \Form_Widget());
        \Elementor\Plugin::instance()
            ->widgets_manager
            ->register_widget_type(new \Survey_Widget());
        \Elementor\Plugin::instance()
            ->widgets_manager
            ->register_widget_type(new \Cta_Widget());

    }

}

Genoo_Elementor_Extension::instance();

