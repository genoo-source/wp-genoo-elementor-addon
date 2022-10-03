<?php
/**
 * Class Genoo_Action_After_Submit
 * @see https://developers.elementor.com/custom-form-action/
 * Custom elementor form action after submit to add a settings
 * Genoo_Action_After_Submit list via API
 */
use WPMKTENGINE\Wordpress\Utils;
use Genoo\Utils\Strings;
use WPMKTENGINE\RepositoryForms;
use WPME\RepositoryFactory;
use WPME\RepositorySettingsFactory;
use WPMKTENGINE\Cache;
use WPME\ApiFactory;

class Genoo_Action_After_Submit extends
    \ElementorPro\Modules\Forms\Classes\Action_Base
{
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */

    public function __construct()
    {
        //get email data while select email folders
        add_action('wp_ajax_emaildata', [$this, 'emaildata']);
        
        //save leadtype when user selects create leadtype
        add_action('wp_ajax_btnleadsave', [$this, 'btnleadsave']);
        //get leadtypes id form leadfolder by using particular leadfolderid
        add_action('wp_ajax_sendleadfolder', [$this, 'sendleadfolder']);
        //get form name update

        //emaildata
    }
    // default name of function

    public function get_name()
    {
        return 'Genoo / WPMktgEngine';
    }

    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    //display default label

    public function get_label()
    {
        return __('Genoo / WPMktgEngine', 'Genoo Elementor Extension');
    }

    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    //to show all data form

    public function run($record, $ajax_handler)
    {
        $settings = $record->get('form_settings');

        // Make sure that there is a SelectEmailfolder field ID
        if (empty($settings['SelectEmailfolder'])) {
            return;
        }
        // Make sure that there is a SelectEmail field ID

        if (empty($settings['SelectEmail'])) {
            return;
        }
        if (empty($settings['labelleadtypeitem'])) {
            return;
        }

        if (empty($settings['labelleadtypeitem'])) {
            return;
        }

        // Make sure that there is a SelectLeadFolder field ID
        if (empty($settings['SelectLeadFolder'])) {
            return;
        }

        // Make sure that there is a SelectLeadType field ID
        if (empty($settings['SelectLeadType'])) {
            return;
        }
        if (empty($settings['Source'])) {
            return;
        }
        if (empty($settings['labelemailfolderitem'])) {
            return;
        }
        // Make sure that there is a Createleadtype field ID
        if (empty($settings['Createleadtype'])) {
            return;
        }

        // Make sure that there is a SelectWebinar field ID
        if (empty($settings['SelectWebinar'])) {
            return;
        }

        // Get sumitetd Form data
        $raw_fields = $record->get('fields');

        // Normalize the Form Data
        $fields = [];
        foreach ($raw_fields as $id => $field) {
            $fields[$id] = $field['value'];
        }

        // Make sure that the user entered an email

        // Send the request
    }

    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     */

    public function register_settings_section($widget)
    {
        //getting array key to set default values for controls
        $getemailfolders = array_keys($this->getemailfolders());
        $getleadtypes = array_keys($this->getleadtypes());
        $datasuccess = array_keys($this->datasuccess());
        $webinars = array_keys($this->webinars());
        $folderapi = array_keys($this->folderapi());

        //start genoo/wpmktgengine section
        $widget->start_controls_section('section_genoo', [
            'label' => __('Genoo / WPMktgEngine', 'Genoo Elementor Extension'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            'classes' => 'mycustomclass',
            'condition' => [
                'submit_actions' => $this->get_name(),
            ],
        ]);

        $widget->add_control('labelleadtypeitem', [
            'label' => __(
                'Select LeadType (where leads will be put who submit this form):',
                'Genoo Elementor Extension'
            ),
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => __('<div></div>'),
            'content_classes' => 'html_lead_type_develop',
        ]);

        $widget->add_control(
            'SelectLeadFolder',
            [
                'label' => __(
                    'Select lead folder:',
                    'Genoo Elementor Extension'
                ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => $folderapi[0],
                'options' => $this->folderapi(),
                'label_block' => true,
            ],
            ['orderby' => 'ASC']
        );

        $widget->add_control('SelectLeadType', [
            'label' => __('Select LeadType', 'Genoo Elementor Extension'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $this->getleadtypes(),
            'default' => $getleadtypes[0],
            'label_block' => true,
            'separator' => 'before',
        ]);

        $widget->add_control('Createleadtype', [
            'label' => __('Create lead type', 'Genoo Elementor Extension'),
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => __(
                '<div class="leadtypecreate" name="leadtypecreate"><div><input name="createlead" id="createlead" class="createlead"  /></div><div><button type="button" class="btn btn-primary createleadsave">save</button></div>',
                'Genoo Elementor Extension'
            ),
            'content_classes' => 'createleadtypesave',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'SelectLeadType',
                        'operator' => '==',
                        'value' => '2',
                    ],
                ],

            ],
        ]);

        $widget->add_control('Source', [
            'label' => __(
                'Source (set on lead record if this is a new lead):
                ',
                'Genoo Elementor Extension'
            ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'enter source text here....',
            'separator' => 'before',
        ]);

        $widget->add_control('labelemailfolderitem', [
            'label' => __(
                'Select Confirmation Email to Send Upon Form Completion (optional):',
                'Genoo Elementor Extension'
            ),
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => __('<div></div>'),
            'content_classes' => 'html_email_folder_develop',
        ]);

        $widget->add_control('SelectEmailfolder', [
            'label' => __('Select Email Folder:', 'Genoo Elementor Extension'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $this->getemailfolders(),
            'default' => $getemailfolders[0],
            'placeholder' => 'Select email folders',
            'label_block' => true,
        ]);

        $widget->add_control('SelectEmail', [
            'label' => __('Select Email to Send:', 'Genoo Elementor Extension'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $this->datasuccess(),
            'default' => $datasuccess[0],
            'label_block' => true,
            'required' => true,
            'separator' => 'before',

            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'SelectEmailfolder',
                        'operator' => '!==',
                        'value' => 0,
                    ],
                ],
            ],
        ]);

        $widget->add_control('show_title', [
            'label_on' => __('Show', 'Genoo Elementor Extension'),
            'label_off' => __('Hide', 'Genoo Elementor Extension'),
            'label' => __(
                'Register User into Webinar?',
                'Genoo Elementor Extension'
            ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'classes' => 'selectwebinarval',
            'return_value' => 'yes',
            'default' => 'No',
        ]);

        $widget->add_control('SelectWebinar', [
            'label' => __('Select Webinar:', 'Genoo Elementor Extension'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $this->webinars(),
            'default' => $webinars[0],
            'placeholder' => 'Select Webinar',
            'label_block' => true,
            'separator' => 'before',
            'condition' => [
                'show_title' => 'yes',
            ],
        ]);

        $widget->end_controls_section();
    }

 //function for geting leadtypes based on lead folderid

    public function sendleadfolder()
    {
        global $WPME_API;

        $lead_id = $_REQUEST['lead_folder_id'];
        //getting api response for leadtypes.
        if (method_exists($WPME_API, 'callCustom')):
            try {
                // Make a GET request, to Genoo / WPME api, for that rest endpoint
                $leadtypes[0] = 'Select Lead Types';
                $leadtypes[1] = '---------------------------------';
                $leadtypes[2] = 'Create new lead type';
                $leadTypes = $WPME_API->callCustom('/leadtypes', 'GET', null);
                foreach ($leadTypes as $leadType):
                    if ($leadType->folder_id == $lead_id):
                        $leadtypes[$leadType->id] = $leadType->name;
                    endif;
                endforeach;
                wp_send_json($leadtypes);
            } catch (Exception $e) {
                if ($WPME_API->http->getResponseCode() == 404):
                       // Looks like folders not found
                       endif;
            }
        endif;
    }
    //function for getting email folders
    public static function getemailfolders()
    {
        global $WPME_API;
        //getting api response for leadtypes, zoomwebinars, emailfolders

        if (method_exists($WPME_API, 'callCustom')):
            try {
                $emailfolderslead[0] = 'Select Email Folders';
                // Make a GET request, to Genoo / WPME api, for that rest endpoint
                $emailfolders = $WPME_API->callCustom(
                    '/emailfolders',
                    'GET',
                    null
                );
                foreach ($emailfolders as $emailfolder):
                    $emailfolderslead[$emailfolder->id] = $emailfolder->name;
                endforeach;
            } catch (Exception $e) {
                if ($WPME_API->http->getResponseCode() == 404):
                    // Looks like folders not found
                    $emailfolderslead[0] = 'Select Email Folders';
                endif;
            }
        endif;
        return $emailfolderslead;
    }
    //function for getting webinars.
    public static function webinars()
    {
        global $WPME_API;
        //getting api response for leadtypes, zoomwebinars, emailfolders

        if (method_exists($WPME_API, 'callCustom')):
            try {
                $zoomwebinarslead[0] = 'Select webinars';
                // Make a GET request, to Genoo / WPME api, for that rest endpoint
                $zoomwebinars = $WPME_API->callCustom(
                    '/zoomwebinars/all',
                    'GET',
                    null
                );

                if (is_array($zoomwebinars) || is_object($zoomwebinars)) {
                    foreach ($zoomwebinars as $zoomwebinar):
                        $zoomwebinarslead[$zoomwebinar->id] =
                            $zoomwebinar->startdate .
                            ' ::' .
                            $zoomwebinar->name;
                    endforeach;
                }
            } catch (Exception $e) {
                if ($WPME_API->http->getResponseCode() == 404):
                    // Looks like folders not found
                    $zoomwebinarslead[0] = 'Select webinars';
                endif;
            }
        endif;

        return $zoomwebinarslead;
    }
//function for getting leadtypes
public static function getleadtypes()
{
        global $WPME_API, $post;
        //getting api response for leadtypes,
        $elementor_data = get_post_meta( $post->ID, '_elementor_data', true );
            if ( !empty( $elementor_data ) ):
            $decode_datas = json_decode( $elementor_data );
            foreach ( $decode_datas as $decode_data ) {
                $data = $decode_data->elements;
                foreach ( $data as $dataelement ) {
                $data_elements_value = $dataelement->elements;

                    foreach ( $data_elements_value as $elements_value ) {

                        $dataleadtypefolder = isset($elements_value->settings->SelectLeadFolder) ?  $elements_value->settings->SelectLeadFolder  : '';
                        $dataleadtype = isset($elements_value->settings->SelectLeadType) ? $elements_value->settings->SelectLeadType : '';

                        if (method_exists($WPME_API, 'callCustom')):

                    $data_elements_value = $dataelement->elements;
                  foreach ( $data_elements_value as $elements_value ) {
                    $dataleadtypefolder_id = isset($elements_value->settings->SelectLeadFolder) ?  $elements_value->settings->SelectLeadFolder  : '';
                        $result = explode("#", $dataleadtypefolder_id);
                        if($result[1]!='')
                        {
                        $dataleadtypefolder = $result[1];
                        }
                        else
                        {
                        $dataleadtypefolder = $result[0];
                        }
                    $dataleadtype = isset($elements_value->settings->SelectLeadType) ? $elements_value->settings->SelectLeadType : '';

                    if (method_exists($WPME_API, 'callCustom')):
     
                            try {

                                // Make a GET request, to Genoo / WPME api, for that rest endpoint
                                $leadtypes[0] = 'Select Lead Types';
                                $leadtypes[1] = '------------------';
                                $leadTypes = $WPME_API->callCustom(
                                    '/leadtypes',
                                    'GET',
                                    null
                                );
                                foreach ($leadTypes as $leadType):
                                    $leadtypes[2] = 'Create new lead type';
                                    if (
                                        $dataleadtypefolder ==
                                        $leadType->folder_id
                                    ):
                                        $leadtypes[$leadType->id] =
                                            $leadType->name;
                                    endif;
                                endforeach;
                            } catch (Exception $e) {
                                if ($WPME_API->http->getResponseCode() == 404):
                                    // Looks like folders not found
                                    $leadtypes[0] = 'Select Lead Types';
                                    $leadtypes[1] = '------------------';
                                endif;
                            }
                        endif;
                    }
                }
            }

            // Make a GET request, to Genoo / WPME api, for that rest endpoint
            // Looks like folders not found

        else:
            if (method_exists($WPME_API, 'callCustom')):
                try {
                    $leadtypes[0] = 'Select Lead Types';
                    $leadtypes[1] = '------------------';
                    $leadTypes = $WPME_API->callCustom(
                        '/leadtypes',
                        'GET',
                        null
                    );
                    foreach ($leadTypes as $leadType):
                        $leadtypes[2] = 'Create new lead type';
                        $leadtypes[$leadType->id] = $leadType->name;
                    endforeach;
                } catch (Exception $e) {
                    if ($WPME_API->http->getResponseCode() == 404):
                        $leadtypes[0] = 'Select Lead Types';
                        $leadtypes[1] = '------------------';
                    endif;
                }
            endif;
            endif;
            return $leadtypes;
          }
        //function for getting emails based on emailfolderid

    public function emaildata()
    {
        global $WPME_API;

       //getting api response for email.

        $id = $_REQUEST['selectid'];
        
            if ( method_exists( $WPME_API, 'callCustom' ) ):
            try {
                // Make a GET request, to Genoo / WPME api, for that rest endpoint

                if ($id != 0):
                    $email_datas = $WPME_API->callCustom(
                        '/emails/' . $id,
                        'GET',
                        null
                    );
                else:
                    $email_datas = $WPME_API->getEmails();
                endif;
                foreach ($email_datas as $email_data):
                    $emailtypes[$email_data->id] = $email_data->name;
                endforeach;
                wp_send_json($emailtypes);
            } catch (Exception $e) {
                if ($WPME_API->http->getResponseCode() == 404):
                    // Looks like folders not found
                    endif;
            }
        endif;
    }

    //getting emails if email id already present in database

    public function datasuccess()
    {
        global $post, $WPME_API;

        $elementor_data = get_post_meta($post->ID, '_elementor_data', true);
        if (!empty($elementor_data)):
            $decode_datas = json_decode($elementor_data);
            foreach ($decode_datas as $decode_data) {
                $data = $decode_data->elements;

                foreach ($data as $dataelement) {
                    $data_element = $dataelement->elements;
                    // $customfields = '';
                    foreach ($data_element as $elements_value) {
                        $datavalue =
                            isset($elements_value->settings->SelectEmailfolder) ? $elements_value->settings->SelectEmailfolder : '';
                        $dataemail = isset($elements_value->settings->SelectEmail) ? $elements_value->settings->SelectEmail : '';
                        //calling leadfields api for showing dropdown
                        if (method_exists($WPME_API, 'callCustom')):
                            $email_values[0] = 'Select email';
                            try {
                                if ($datavalue != 0):
                                    $customfields = $WPME_API->callCustom(
                                        '/emails/' . $datavalue,
                                        'GET',
                                        null
                                    );
                                    foreach ($customfields as $customfield):
                                        $email_values[$customfield->id] =
                                            $customfield->name;
                                    endforeach;
                                endif;
                                //$email_values = array();
                            } catch (Exception $e) {
                                if ($WPME_API->http->getResponseCode() == 404):
                                    // Looks like leadfields not found
                                    $email_values[0] = 'Select email';
                                endif;
                            }
                        endif;
                    }
                }
            }

            //$email_values = array();
            // Looks like leadfields not found
        else:
            if (method_exists($WPME_API, 'callCustom')):
                try {
                    $email_values[0] = 'Select email';
                    $customfields = $WPME_API->getEmails();

                    foreach ($customfields as $customfield):
                        $email_values[$customfield->id] = $customfield->name;
                    endforeach;
                } catch (Exception $e) {
                    if ($WPME_API->http->getResponseCode() == 404):
                        $email_values[0] = 'Select email';
                    endif;
                }
            endif;
        endif;

        return $email_values;
    }

    /**
     * On Export
     *
     * Clears form settings on export
     * @access Public
     * @param array $element
     */
    //function for create new lead type

        public function btnleadsave() {

            global $WPME_API;
            $createlead = array();
            $createlead['name'] = $_REQUEST['leadtypevalue'];
            $createlead['description'] = $_REQUEST['description'];
            $createlead['mngdlistind'] = $_REQUEST['mngdlistind'];
            $createlead['costforall'] =  $_REQUEST['costforall'];
            $createlead['costperlead'] = $_REQUEST['costperlead'];
            $createlead['sales_ind'] =   $_REQUEST['sales_ind'];
            $createlead['system_ind'] = $_REQUEST['system_ind'];
            $createlead['blog_commenters'] = $_REQUEST['blog_commenters'];
            $createlead['blog_subscribers'] = $_REQUEST['blog_subscribers'];
            $createlead['folder_id'] = $_REQUEST['folder_id'];

            if ( method_exists( $WPME_API, 'callCustom' ) ):
            try {
                $leadresponse = $WPME_API->callCustom(
                    '/createLeadType',
                    'POST',
                    $createlead
                );

                wp_send_json($leadresponse->ltid);
            } catch (Exception $e) {
            }
        endif;
    }

    //function for list all leadtype folders.

    public function folderapi()
    {
        global $WPME_API;
        if (method_exists($WPME_API, 'callCustom')):
            try {
                $foldernames[-1] = 'Select lead folder';
                $getfolders = $WPME_API->callCustom(
                    '/listLeadTypeFolders/Uncategorized',
                    'GET',
                    'NULL'
                );

                foreach ($getfolders as $getfolder):
                    $foldernames[0] = 'Uncategorized';
                    $foldernames[$getfolder->type_id . '#'] = $getfolder->name;
                    foreach ($getfolder->child_folders as $child_folders):
                        if ($child_folders->parent_id == $getfolder->type_id) {
                            $foldernames[
                                $getfolder->type_id .
                                    '#' .
                                    $child_folders->type_id
                            ] = '--' . $child_folders->name;
                        }
                    endforeach;
                endforeach;
            } catch (Exception $e) {
                $foldernames[-1] = 'Select lead folder';
            }
        endif;
        return $foldernames;
    }

    //default function in action after submit

    public function on_export($element)
    {
        unset(
            $element['SelectEmailfolder'],
            $element['SelectLeadFolder'],
            $element['SelectLeadType'],
            $element['Createleadtype'],
            $element['SelectEmail'],
            $element['SelectWebinar']
        );
    }
}
