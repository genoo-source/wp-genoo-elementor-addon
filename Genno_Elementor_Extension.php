<?php
/**
 * Plugin Name: Genoo Elementor Extension
 * Description:  This plugin requires the WPMKtgEngine or Genoo plugin installed before order to activate.
 * Version:     1.4.4
 * Author:      Genoo
 * Text Domain: genoo-elementor-extension
 */

if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly.
}

/**
 * Main Genoo Elementor Extension
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
use Elementor\Plugin;

require_once plugin_dir_path(__FILE__) . 'deploy/updater.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
wpme_genno_elementor_updater_init(__FILE__);

register_activation_hook(__FILE__, function () {
    // Basic extension data
   global $wpdb;
    $fileFolder = basename(dirname(__FILE__));
    $file = basename(__FILE__);
    $filePlugin = $fileFolder . DIRECTORY_SEPARATOR . $file;
    // Activate?
    $activate = false;
    $isGenoo = false;
    // Get api / repo
    if (
        class_exists('\WPME\ApiFactory') &&
        class_exists('\WPME\RepositorySettingsFactory')
    ) {
        $activate = true;
        $repo = new \WPME\RepositorySettingsFactory();
        $api = new \WPME\ApiFactory($repo);
        if (class_exists('\Genoo\Api')) {
            $isGenoo = true;
        }
    } elseif (
        class_exists('\Genoo\Api') &&
        class_exists('\Genoo\RepositorySettings')
    ) {
        $activate = true;
        $repo = new \Genoo\RepositorySettings();
        $api = new \Genoo\Api($repo);
        $isGenoo = true;
    } elseif (
        class_exists('\WPMKTENGINE\Api') &&
        class_exists('\WPMKTENGINE\RepositorySettings')
    ) {
        $activate = true;
        $repo = new \WPMKTENGINE\RepositorySettings();
        $api = new \WPMKTENGINE\Api($repo);
    }
    // 1. First protectoin, no WPME or Genoo plugin
    if ($activate == false && $isGenoo == false) { ?>
  
<div class="alert">
<p style="font-family:Segoe UI;font-size:14px;">This plugin requires the WPMKtgEngine or Genoo plugin installed before order to activate</p>
</div>
    <?php
    die();

    genoo_wpme_deactivate_plugin(
        $filePlugin,
        'This extension requires WPMktgEngine or Genoo plugin to work with.'
    );
    } else {

        $sql = "CREATE TABLE {$wpdb->prefix}leadtype_form_save_elementor (
            id mediumint(8) unsigned not null auto_increment,
            post_id mediumint(8) unsigned not null,
            item_id varchar(255),
            is_active tinyint(1),
            lead_values varchar(255),
            lead_label  varchar(255),
            folder_id int(11), PRIMARY KEY(id));";
            
       dbDelta($sql);

       $lead_save_table = "ALTER TABLE {$wpdb->prefix}leadtype_form_save_elementor
       ADD COLUMN `folder_id` int(11) UNSIGNED AFTER `lead_label`";
       
       $item_id_store = "ALTER TABLE {$wpdb->prefix}leadtype_form_save_elementor
       ADD COLUMN `item_id` varchar(255) UNSIGNED AFTER `post_id`";
       
       $wpdb->query($lead_save_table);
       
       $wpdb->query($item_id_store);
    }

   
});

//add form,cta,survey widgets in elementor categories.
function add_elementor_widget_categories()
{
    $elementsManager = Plugin::instance()->elements_manager;
    $elementsManager->add_category('Genoo-elementor', [
        'title' => 'Genoo Addons',
        'icon' => 'fonts',
    ]);
}
//elementor categories registered for form,survey
add_action(
    'elementor/elements/categories_registered',
    'add_elementor_widget_categories'
);
add_action(
    'wpmktengine_init',
    function ($repositarySettings, $api, $cache) {
        /**
    
     * Add extensions to the Extensions list
    
     */
        add_filter(
            'wpmktengine_tools_extensions_widget',
            function ($array) {
                $array['Genoo Elementor Extension'] =
                    '<span style="color:green">Active</span>';

                return $array;
            },
            10,
            1
        );
    },
    10,
    3
);

//form integration
add_action('elementor_pro/init', function () {
    // Here its safe to include our action class file
    include_once 'Genoo_Action_After_Submit.php';
    include_once 'Elementor_Forms_Patterns_Validation.php';
    // Instantiate the action class
    $sendy_action = new Genoo_Action_After_Submit();

    // Register the action with form widget
    \ElementorPro\Plugin::instance()
        ->modules_manager->get_modules('forms')
        ->add_form_action($sendy_action->get_name(), $sendy_action);
});

add_filter(
    'elementor_pro/forms/render/item/select',
    function ($item, $index, $form) {

        global $wpdb,$post;

       $formvalue = $item['_id'];

        

      if ($item['third_party_input'] == 'leadtypes') {

            $table_Setup = $wpdb->prefix."leadtype_form_save_elementor";

     
            $leadtypes = $wpdb->get_results("select * from $table_Setup where `post_id`=$post->ID and `item_id`='".$formvalue."'");

            $values = array();


            foreach ($leadtypes as $leadtype) {
            
                $values[] = "$leadtype->lead_label|$leadtype->lead_values"; 
            }

        $valuetests = implode("\n", $values);
        
            $empty = '';
         
            $item['field_options'] =  "$empty|\n$valuetests";
         
        }
     
        return $item;

      
    },
    10,
    3
); 

add_filter(
    'elementor_pro/forms/render/item/checkbox',
    function ($item, $index, $form) {
        global $wpdb,$post;

     

        if ($item['third_party_input'] == 'leadtypes') {
           
            $values = array();

         $table_Setup = $wpdb->prefix."leadtype_form_save_elementor";

         $formvalue = $item['_id'];

        
            $leadtypes = $wpdb->get_results("select * from $table_Setup where `post_id`=$post->ID and `item_id`='".$formvalue."'");

            foreach ($leadtypes as $leadtype) {
            
                $values[] = "$leadtype->lead_label|$leadtype->lead_values"; 
            }
        
            $valuetests = implode("\n", $values);

         

            $item['field_options'] = "$valuetests";
        
        }
        return $item;
    },
    10,
    3
);
add_filter(
    'elementor_pro/forms/render/item/radio',
    function ($item, $index, $form) {
        global $wpdb,$post;

      if ($item['third_party_input'] == 'leadtypes') {
          
            $values = array();
           
            $table_Setup = $wpdb->prefix."leadtype_form_save_elementor";

            $formvalue = $item['_id'];

        
            $leadtypes = $wpdb->get_results("select * from $table_Setup where `post_id`=$post->ID and `item_id`='".$formvalue."'");

            foreach ($leadtypes as $leadtype) {
            
                $values[] = "$leadtype->lead_label|$leadtype->lead_values"; 
            }

            $valuetests = implode("\n", $values);

            $item['field_options'] = "$valuetests";
          
        }

        return $item;
    },
    10,
    3
);



// formsubmit for lead
add_action(
    'elementor_pro/forms/new_record',
    function ($record, $ajax_handler) {
        global $post, $WPME_API;
        $form_name = $record->get_form_settings('form_name');
        $settings = $record->get('form_settings');
        $email_folder_id = $settings['SelectEmailfolder'];
        $select_lead_id = $settings['SelectLeadType'];
        $select_email_id = $settings['SelectEmail'];
        $select_webinar = $settings['SelectWebinar'];
        $source_input = $settings['Source'];
        $page_url = get_permalink(get_the_ID());

        if (!empty($select_lead_id)):
            $selectvalues = [];
            $selectvalues['form_name'] = $form_name;
            $selectvalues['lead_type_id'] = $select_lead_id;
            $selectvalues['client_ip_address'] = $_SERVER['REMOTE_ADDR'];
            $selectvalues['page_url'] = $page_url;
            $selectvalues['form_type'] = 'EF';
            $selectvalues['source'] = $source_input;

            if (!empty($select_email_id)):
                $selectvalues['confirmation_email_id'] = $select_email_id;
            endif;
            if (!empty($select_webinar)):
                $selectvalues['webinar_id'] = $select_webinar;
            endif;

            foreach ($settings as $setting) {
                foreach ($setting as $value) {
                    if (!empty($value['custom_id'])):
                        $custom_ids[] = $value;
                    endif;
                }
            }
            $raw_fields = $record->get('fields');
            $fields = [];
            foreach ($raw_fields as $id => $field) {
                $fields[$id] = $field['value'];
            }
           $selected_submission_values = [];
            foreach ($custom_ids as $custom_values) {
                
                foreach ($fields as $key => $val) {
                    if ($custom_values['custom_id'] == $key):
                        if ($custom_values['third_party_input'] != ''):
                            $firstindex = strstr(
                                $custom_values['third_party_input'],
                                'c00'
                            );
                            $lastindex = strstr(
                                $custom_values['third_party_input'],
                                'date'
                            );
                            if ($firstindex == true && $lastindex == true):
                                $date = date_create($val);
                                $date = date_format($date, 'Y-m-d');
                                $selectvalues[
                                    $custom_values['third_party_input']
                                ] = $date . 'T' . '00:00:00+00:00';
                                update_post_meta(
                                    $post->ID,
                                    $custom_values['third_party_input'],
                                    $date . 'T' . '00:00:00+00:00'
                                );
                            elseif ($firstindex == false && $lastindex == true):
                                $date = date_create($val);
                                $date = date_format($date, 'm/d/Y');
                                update_post_meta(
                                    $post->ID,
                                    $custom_values['third_party_input'],
                                    $date
                                );
                                $selectvalues[
                                    $custom_values['third_party_input']
                                ] = $date;
                            elseif ($custom_values['field_type'] == 'radio'):
                                $selectvalues[
                                    $custom_values['third_party_input']
                                ] = '1';
                                $selected_submission_values[] = $val;
                                update_post_meta(
                                    $post->ID,
                                    $custom_values['third_party_input'],
                                    '1'
                                );
                            elseif ($custom_values['field_type'] == 'checkbox'):
                                $spliting_option_values = explode(',', $val);
                                $selectvalues[
                                    $custom_values['third_party_input']
                                ] = '1';
                                foreach($spliting_option_values as $spliting_option_value){
                                $selected_submission_values[] = $spliting_option_value;
                                   }
                                update_post_meta(
                                    $post->ID,
                                    $custom_values['third_party_input'],
                                    '1'
                                );
                                
                            elseif($custom_values['field_type'] == 'select'):
                                $selected_submission_values[] = $val;
                            else:
                                update_post_meta(
                                    $post->ID,
                                    $custom_values['third_party_input'],
                                    $val
                                );
                                $selectvalues[
                                    $custom_values['third_party_input']
                                ] = $val;
                            endif;
                        else:
                            if ($custom_values['custom_id'] == 'name'):
                                $namesplit = explode(' ', $val);
                                update_post_meta(
                                    $post->ID,
                                    'first_name',
                                    $namesplit[0]
                                );
                                update_post_meta(
                                    $post->ID,
                                    'last_name',
                                    $namesplit[1]
                                );
                                $selectvalues['first_name'] = $namesplit[0];
                                $selectvalues['last_name'] = $namesplit[1];
                            elseif ($custom_values['custom_id'] == 'email'):
                                $custom_values['custom_id'] = 'email';
                                update_post_meta($post->ID, 'email', $val);
                                $selectvalues['email'] = $val;
                            else:
                                $selectvalues[
                                    $custom_values['custom_id']
                                ] = $val;
                                update_post_meta(
                                    $post->ID,
                                    $custom_values['custom_id'],
                                    $val
                                );
                            endif;
                        endif;
                    endif;
                }
            }
           //  
            if (method_exists($WPME_API, 'callCustom')):
                try {
                    $response = $WPME_API->callCustom(
                        '/leadformsubmit',
                        'POST',
                        $selectvalues
                    );
                    
                
                    if ($WPME_API->http->getResponseCode() == 204):
                        // No values based on folderdid onchange! Ooops


                    elseif ($WPME_API->http->getResponseCode() == 200):
                    endif;
                } catch (Exception $e) {

                    if ($WPME_API->http->getResponseCode() == 404):


                        // Looks like leadfields not found
                    endif;
                }
            endif;

            $genoo_ids = $response->genoo_id;
             
            foreach ($selected_submission_values as $lead_value):
                      
                        $WPME_API->setLeadUpdate(
                            $genoo_ids,
                            $lead_value,
                            $selectvalues['email'],
                            $selectvalues['first_name'],
                            $selectvalues['last_name']
                        );
                    endforeach;

            setcookie(
                '_gtld',
                $genoo_ids,
                time() + 10 * 365 * 24 * 60 * 60,
                '/'
            );
            else : 
                 $ajax_handler->add_error_message( 'it was NEVER sent to the Genoo account' );

            
        endif;
    },
    10,
    2
);
function custom_logs($message) { 
    if(is_array($message)) { 
        $message = json_encode($message); 
    } 
    $file = fopen("../custom_logs678.log","a"); 
    echo fwrite($file, "\n" . date('Y-m-d h:i:s') . " :: " . $message); 
    fclose($file); 
}

add_action('wp_head', 'myplugin_ajaxurls');
function myplugin_ajaxurls()
{
    echo '<script type="text/javascript">
            var ajaxurl = "' .
        admin_url('admin-ajax.php') .
        '";
            </script>';
}

// define the elementor/db/before_save callback
function custom_elementor_db_after_save($post_id, array $editor_data)
{
    //custom code here
    global $WPME_API, $wpdb;


    $datatable = $wpdb->prefix."leadtype_form_save_elementor";
    //   if(!empty($editor_data)):

    foreach ($editor_data as $elementorcode) {
        foreach ($elementorcode['elements'] as $code) {
            $values = [];

            $code_id = [];
            $code_ids = [];

            foreach ($code['elements'] as $codeset) {
             
                $values['form_name'] = $codeset['settings']['form_name'];

                $values['form_type'] = 'EF';

                $code_id['id'] = $codeset['id'];

                

                $get_values = get_post_meta(
                    $post_id,
                    'code_' . $codeset['id'],
                    true
                );

                if ($get_values == '') {
                    $values['form_id'] = '0';
                } else {
                    $values['form_id'] = $get_values;
                }

                update_function($values, $post_id, $codeset['id']);

                $code_ids[] = $codeset['id'];

            

            }
        }
    }

    $deleting_data = $wpdb->get_results(
        "SELECT `meta_key` FROM  $wpdb->postmeta where `post_id` = $post_id AND meta_key LIKE 'code_%'"
    );

    $meta_types = array_column($deleting_data, 'meta_key');

    $unique_values = array_unique($meta_types);

    $deletevalues = [];

    foreach ($unique_values as $delete_id) {
        $coding_id = substr($delete_id, strpos($delete_id, '_') + 1);

        if (!in_array($coding_id, $code_ids)) {
            $form_id = get_post_meta($post_id, 'code_' . $coding_id, true);

            $form_title = get_post_meta($post_id, $form_id, true);

            $deletevalues['form_name'] = $form_title;
            $deletevalues['form_id'] = $form_id;

            if (method_exists($WPME_API, 'callCustom')):
                try {
                    $deleteresponse = $WPME_API->callCustom(
                        '/deleteGravityForm',
                        'DELETE',
                        $deletevalues
                    );

                    if ($WPME_API->http->getResponseCode() == 204):
                        // No values based on form name,form id onchange! Ooops


                    elseif ($WPME_API->http->getResponseCode() == 200):
                        delete_post_meta($post_id, 'code_' . $coding_id);
                        delete_post_meta($post_id, $form_id);
                    endif;
                } catch (Exception $e) {
                    if ($WPME_API->http->getResponseCode() == 404):


                        // Looks like formname or form id not found
                    endif;
                }
            endif;
        }
    }
}

//add the action after details store
add_action(
    'elementor/editor/after_save',
    'custom_elementor_db_after_save',
    10,
    2
);

//save form data into wpmktgengine
function update_function($values, $post_id, $code_id)
{
    global $WPME_API, $wpdb;

    $get_value = get_post_meta($post_id, 'code_' . $code_id, true);

    if (method_exists($WPME_API, 'callCustom')):
        try {
            $response = $WPME_API->callCustom(
                '/saveExternalForm',
                'POST',
                $values
            );

            if ($WPME_API->http->getResponseCode() == 204):
                // No values based on form name, form id onchange! Ooops


            elseif ($WPME_API->http->getResponseCode() == 200):
                if ($get_value == $response->genoo_form_id):
                    update_post_meta(
                        $post_id,
                        $response->genoo_form_id,
                        $values['form_name']
                    );

                    update_post_meta(
                        $post_id,
                        'code_' . $code_id,
                        $response->genoo_form_id
                    );
                else:
                    add_post_meta(
                        $post_id,
                        $response->genoo_form_id,
                        $values['form_name']
                    );

                    add_post_meta(
                        $post_id,
                        'code_' . $code_id,
                        $response->genoo_form_id
                    );
                endif;
            endif;
        } catch (Exception $e) {
            if ($WPME_API->http->getResponseCode() == 404):


                // Looks like formname or form id not found
            endif;
        }
    endif;
}

//if user deletes the post form should be deleted
add_action('before_delete_post', 'custom_post_delete_function');
function custom_post_delete_function($postid)
{
    global $wpdb, $WPME_API;

    $elementor_data = get_post_meta($postid, '_elementor_data', true);

    $decode_datas = json_decode($elementor_data);

    foreach ($decode_datas as $decode_data) {
        $data = $decode_data->elements;

        foreach ($data as $dataelement) {
            $data_element = $dataelement->elements;

            $deletevalues = [];

            foreach ($data_element as $elements_value) {
                $form_id = get_post_meta(
                    $postid,
                    'code_' . $elements_value->id,
                    true
                );

                $form_title = get_post_meta($postid, $form_id, true);

                $deletevalues['form_name'] = $form_title;

                $deletevalues['form_id'] = $form_id;

                if (method_exists($WPME_API, 'callCustom')):
                    try {
                        $deleteresponse = $WPME_API->callCustom(
                            '/deleteGravityForm',
                            'DELETE',
                            $deletevalues
                        );

                        if ($WPME_API->http->getResponseCode() == 204):
                            // No values based on form name,form id onchange! Ooops


                        elseif ($WPME_API->http->getResponseCode() == 200):
                            delete_post_meta($postid, 'code_' . $coding_id);

                            delete_post_meta($postid, $form_id);
                        endif;
                    } catch (Exception $e) {
                        if ($WPME_API->http->getResponseCode() == 404):


                            // Looks like formname or form id not found
                        endif;
                    }
                endif;
            }
        }
    }
}

add_action('elementor_pro/forms/default_submit_actions', function ($actions) {
    return array_merge($actions, ['Genoo / WPMktgEngine']);
});

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
        if (is_null(self::$_instance)) {
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
        add_action(
            'elementor/editor/after_enqueue_scripts',
            [$this, 'adminEnqueueScripts'],
            10
        );
        add_action('admin_init', [$this, 'adminEnqueueScripts']);
        add_action('wp_ajax_leadtype_elementor_savelist', [
            $this,
            'leadtype_elementor_savelist',
        ]);
          add_action('wp_ajax_select_lead_types_options', [
            $this,
            'select_lead_types_options',
        ]);
             
        add_action('wp_ajax_leadtype_elementor_change_savelist',[$this,'leadtype_elementor_change_savelist']);
        
        add_action('wp_ajax_leadtype_delete_option',[$this,'leadtype_delete_option']);
        
        add_action('wp_ajax_leadfolder_delete_option',[$this,'leadfolder_delete_option']);

        add_action('wp_ajax_get_all_lead_types_options',[$this,'get_all_lead_types_options']);

     //   add_action('wp_ajax_leadtype_elementor_updatelist',[$this,'leadtype_elementor_updatelist']);
        add_action('wp_ajax_get_leadtype_values',[$this,'get_leadtype_values']);
        add_action('wp_ajax_select_option_items',[$this,'select_option_items']);
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
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [
                $this,
                'admin_notice_missing_main_plugin',
            ]);
            return;
        }

        // Check for required Elementor version
        if (
            !version_compare(
                ELEMENTOR_VERSION,
                self::MINIMUM_ELEMENTOR_VERSION,
                '>='
            )
        ) {
            add_action('admin_notices', [
                $this,
                'admin_notice_minimum_elementor_version',
            ]);
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [
                $this,
                'admin_notice_minimum_php_version',
            ]);
            return;
        }

        //Include plugin files
        //$this->includes();
        // Register widgets
        add_action('elementor/widgets/widgets_registered', [
            $this,
            'register_widgets',
        ]);
        add_action('elementor/init', function () {
            $elementsManager = Plugin::instance()->elements_manager;
            $elementsManager->add_category('custom-widgets', [
                'title' => 'Custom Widgets',
                'icon' => 'fonts',
            ]);
        });
    }
    public function adminEnqueueScripts($hook)
    {
        // scripts
        wp_enqueue_script(
            'my_custom_script',
            plugin_dir_url(__FILE__) . 'file.js',
            [],
            '1.0'
        );
        wp_enqueue_style(
            'my_custom_style',
            plugin_dir_url(__FILE__) . 'elementorcustom.css',
            [],
            '1.0'
        );
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
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__(
                '"%1$s" requires "%2$s" to be installed and activated.',
                'genoo-elementor-extension'
            ),
            '<strong>' .
                esc_html__(
                    'Genoo Elementor Extension',
                    'genoo-elementor-extension'
                ) .
                '</strong>',
            '<strong>' .
                esc_html__('Elementor', 'genoo-elementor-extension') .
                '</strong>'
        );

        printf(
            '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>',
            $message
        );
    }
    
    public function select_option_items()
    {
        global $wpdb;
        $get_item_id = $_REQUEST['item_id'];
        $get_post_id = $_REQUEST['post_id'];
        
        $get_select_option_items =  $wpdb->get_results("select `lead_label`,`lead_values` from `item_id`=$get_item_id and `post_id`=$get_post_id");
        
           $get_select_option_item_array = [];
        foreach($get_select_option_items as $get_select_option_item)
        {
            $get_select_option_item_array[$get_select_option_item->lead_values] = $get_select_option_item->lead_label;
        }
        
        wp_send_json($get_select_option_item_array);
        
        
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
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__(
                '"%1$s" requires "%2$s" version %3$s or greater.',
                'genoo-elementor-extension'
            ),
            '<strong>' .
                esc_html__(
                    'Genoo Elementor Extension',
                    'genoo-elementor-extension'
                ) .
                '</strong>',
            '<strong>' .
                esc_html__('Elementor', 'genoo-elementor-extension') .
                '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf(
            '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>',
            $message
        );
    }
    public function leadtype_elementor_savelist()
    {
        global $wpdb;
    
        $checkboxdetails = $_REQUEST['check_after_values'];

        $post_id = $_REQUEST['post_id'];
        $form_id = $_REQUEST['item_id'];
       
        
        $table = $wpdb->prefix . 'leadtype_form_save_elementor';
        
        $wpdb->delete($table,['post_id' => $post_id,'item_id' => $form_id]);
       

    foreach ($checkboxdetails as $checkboxdetail) {
          $string = $checkboxdetail["labelvalue"];
          
          $lead_type_store =  explode("#", $string);
          
            if($lead_type_store[1]!='')
             {
               $get_lead_type_id =  $lead_type_store[1];
             }
             else
             {
               $get_lead_type_id =  $lead_type_store[0];   
             }
          
        $result = explode("-", $get_lead_type_id);
        
         $labelvalue = $result[1];
         
         $wpdb->insert($table, [
                'post_id' => $post_id,
                'lead_values' => $labelvalue,
                'folder_id' => $result[0],
                'lead_label' => stripslashes($checkboxdetail['label']),
                'item_id' => $form_id
            ]); 
   
           
        }
       
    }
    public function get_all_lead_types_options()
    {
        global $wpdb;
          $leadtype_form_save = $wpdb->prefix . 'leadtype_form_save_elementor';
         $post_id = $_REQUEST['post_id'];
        $form_id = $_REQUEST['item_id'];
        
    
        
        $leadTypes = $wpdb->get_results(
        "select `lead_label`,`lead_values`,`folder_id` from $leadtype_form_save where item_id='$form_id' and post_id=$post_id order by id"
    );

    foreach ($leadTypes as $leadType) {
        $lead_results[$leadType->folder_id . "-" . $leadType->lead_values] =
            $leadType->lead_label;
    }

    wp_send_json($lead_results);
    }
    
    public function leadfolder_delete_option()
    {
       global $WPME_API,$wpdb;
       
         $leadtype_form_save = $wpdb->prefix . 'leadtype_form_save_elementor';
          
         $post_id = $_REQUEST['post_id'];
         
         $item_id = $_REQUEST['item_id'];  
         
         $lead_folder_id = $_REQUEST['delete_lead_folder_id'];
         
             $result = explode("#", $lead_folder_id);
             
             if($result[1]!='')
             {
               $lead_folder_id_value =  $result[1];
             }
             else
             {
               $lead_folder_id_value =  $result[0];   
             }
             
           
       
         $wpdb->delete($leadtype_form_save,['post_id' => $post_id,'item_id' => $item_id,'folder_id' => $lead_folder_id_value]);
        
            $leadtypes_based_folder_id = [];
            
                 if (method_exists($WPME_API, 'callCustom')):
                try {
                    // Make a GET request, to Genoo / WPME api, for that rest endpoint
    
                       $leadTypesvalues = $WPME_API->callCustom('/leadtypes', 'GET', null);
                       
                       foreach($leadTypesvalues as $leadTypesvalue)
                       {
                           if($lead_folder_id_value == $leadTypesvalue->folder_id)
                           {
                            $leadtypes_based_folder_id[$leadTypesvalue->folder_id.'-'.$leadTypesvalue->id] =   $leadTypesvalue->name; 
                           }
                       }
                } catch (Exception $e) {
                    if ($WPME_API->http->getResponseCode() == 404):
                       // Looks like folders not found
                    endif;
                }
            endif;
        wp_send_json($leadtypes_based_folder_id);
    }
public function select_lead_types_options()
{
    global $WPME_API;
    
    $select_lead_folder_id = $_REQUEST['select_lead_folder_id'];
    
       if (method_exists($WPME_API, 'callCustom')):
            try {
                // Make a GET request, to Genoo / WPME api, for that rest endpoint

                   $leadTypesvalues = $WPME_API->callCustom('/leadtypes', 'GET', null);
                    $get_folders = $WPME_API->callCustom(
                                "/listLeadTypeFolders/Uncategorized",
                                "GET",
                                "NULL"
                            );

                            foreach ($get_folders as $get_folder):
                                $folder_names[0] = "Uncategorized";
                                $folder_names[$get_folder->type_id] =
                                    $get_folder->name;
                                foreach (
                                    $get_folder->child_folders
                                    as $child_folders
                                ):
                                    if (
                                        $child_folders->parent_id ==
                                        $get_folder->type_id
                                    ) {
                                        $folder_names[$child_folders->type_id] =
                                            "--" . $child_folders->name;
                                    }
                                endforeach;
                            endforeach;
            } catch (Exception $e) {
                if ($WPME_API->http->getResponseCode() == 404):


                    // Looks like folders not found
                endif;
            }
        endif; 
        
        $select_lead_folder_id_value = [];
        
        foreach($select_lead_folder_id as $select_lead_folder_id_value_option)
        {
              $result = explode("#", $select_lead_folder_id_value_option);
             
             if($result[1]!='')
             {
               $select_lead_folder_id_value[] =  $result[1];
             }
             else
             {
               $select_lead_folder_id_value[] =  $result[0];   
             }
        }
               
        foreach($leadTypesvalues as $leadTypesvalue)
        {
            foreach($folder_names as $key => $foldervalue){   
           if(in_array($leadTypesvalue->folder_id,$select_lead_folder_id_value)):
               
                 if($key==$leadTypesvalue->folder_id):
           $leadTypesvalueset[$leadTypesvalue->folder_id.'-'.$leadTypesvalue->id] =  $leadTypesvalue->name. "#(" . $foldervalue . ")";
           endif;
           endif;
            }
        }
        
      
        
    wp_send_json($leadTypesvalueset);
    
}
public function leadtype_delete_option()
{
    global $wpdb;
$leadtype_form_save = $wpdb->prefix . 'leadtype_form_save_elementor';
$get_delete_lead_type_id = $_REQUEST['delete_lead_type_id'];
$post_id = $_REQUEST['post_id'];
$item_id = $_REQUEST['item_id'];
$get_delete_lead_type_ids = explode("-", $get_delete_lead_type_id);
$folder_id = $get_delete_lead_type_ids[0];
$lead_value = $get_delete_lead_type_ids[1];

 $wpdb->delete($leadtype_form_save,['post_id' => $post_id,'item_id' => $item_id,'lead_values' => $lead_value,'folder_id' => $folder_id]);

}
 public function leadtype_elementor_change_savelist()
 {
         global $wpdb;
    
        $post_id = $_REQUEST['post_id'];
        $item_id = $_REQUEST['item_id'];
        $checkboxdetails = $_REQUEST['check_after_values'];
        $leadtype_form_save = $wpdb->prefix . 'leadtype_form_save_elementor';

        foreach ($checkboxdetails as $checkboxdetail) {
            
        $lead_label_value = $checkboxdetail['label'];
        
         $lead_label_option = explode("#(", $lead_label_value);
             
         $string = $checkboxdetail["labelvalue"];
        
        $result = explode("-", $string);
        $labelvalue = $result[1];
        $get_value = $wpdb->get_results(
            "select * from $leadtype_form_save where `post_id`=$post_id and `item_id`='$item_id' and `lead_values`=$labelvalue");
            
       if(empty($get_value)){
            
            $wpdb->insert($leadtype_form_save, [
                    'post_id' => $post_id,
                    'lead_values' =>  $result[1],
                    'lead_label' => stripslashes($lead_label_option[0]),
                    'folder_id' => $result[0],
                    'item_id' => $item_id
                ]); 
             
            
        }
        }
        
         
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
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__(
                '"%1$s" requires "%2$s" version %3$s or greater.',
                'genoo-elementor-extension'
            ),
            '<strong>' .
                esc_html__(
                    'Genoo Elementor Extension',
                    'genoo-elementor-extension'
                ) .
                '</strong>',
            '<strong>' .
                esc_html__('PHP', 'genoo-elementor-extension') .
                '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf(
            '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>',
            $message
        );
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
        require_once __DIR__ . '/form-widget.php';
        require_once __DIR__ . '/survey-widget.php';
        require_once __DIR__ . '/cta-widget.php';
    }

    public function register_widgets()
    {
        $this->includes(); // <- register the widget class name here
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(
            new \Form_Widget()
        );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(
            new \Survey_Widget()
        );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(
            new \Cta_Widget()
        );
    }
}

Genoo_Elementor_Extension::instance();
