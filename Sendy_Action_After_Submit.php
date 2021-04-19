<?php
/**
 * Class Sendy_Action_After_Submit
 * @see https://developers.elementor.com/custom-form-action/
 * Custom elementor form action after submit to add a subsciber to
 * Sendy list via API 
 */
use WPMKTENGINE\Wordpress\Utils;
use Genoo\Utils\Strings;
use WPMKTENGINE\RepositoryForms;
use WPME\RepositoryFactory;
use WPME\RepositorySettingsFactory;
use WPMKTENGINE\Cache;
use WPME\ApiFactory;

class Sendy_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
   
 
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
 add_action('wp_ajax_datainsert', array($this, 'datainsert'));
add_action('wp_ajax_nopriv_datainsert', array($this, 'datainsert'));

    //datainsert
    } 
	 
	 
	 
	 
	public function get_name() {
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
	public function get_label() {
		return __( 'Genoo / WPMktgEngine', 'Genoo Elementor Extension' );
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
	public function run( $record, $ajax_handler ) {
		$settings = $record->get( 'form_settings' );
 
		//  Make sure that there is a Sendy installation url
		if ( empty( $settings['Select'] ) ) {
			return;
		}

		//  Make sure that there is a Sendy list ID
		if ( empty( $settings['SelectLeadType'] ) ) {
			return;
		}

		// Make sure that there is a Sendy Email field ID
		// which is required by Sendy's API to subsribe a user
		if ( empty( $settings['SelectEmail'] ) ) {
			return;
		}

		// Get sumitetd Form data
		$raw_fields = $record->get( 'fields' );
		


		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}

		// Make sure that the user emtered an email
		// which is required by Sendy's API to subsribe a user
		if ( empty( $fields[ $settings['SelectEmail'] ] ) ) {
			return;
		}

		// If we got this far we can start building our request data
		// Based on the param list at https://sendy.co/api
		$sendy_data = [
			'email' => $fields[ $settings['SelectEmail'] ],
			'list' => $settings['SelectLeadType'],
			'ipaddress' => \ElementorPro\Classes\Utils::get_client_ip(),
			'referrer' => isset( $_POST['referrer'] ) ? $_POST['referrer'] : '',
		];

		// add name if field is mapped
		if ( empty( $fields[ $settings['SelectEmail'] ] ) ) {
			$sendy_data['name'] = $fields[ $settings['SelectEmail'] ];
		}

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
	public function register_settings_section( $widget ) {
	    
	    $getemailfolders = array_keys($this->getemailfolders());
	    $getleadtypes = array_keys($this->getleadtypes());
	    $datasuccess = array_keys($this->datasuccess());
	    $webinars = array_keys($this->webinars());
		$widget->start_controls_section(
			'section_genoo',
			[
				'label' => __( 'Genoo / WPMktgEngine', 'Genoo Elementor Extension' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);
			$widget->add_control(
			'Select',
			[
				'label' => __('Select Email Folder:', 'Genoo Elementor Extension'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' =>  $this->getemailfolders(),
                'default' => $getemailfolders[0],
				'placeholder' => 'Select email folders',
				'label_block' => true,
				'separator' => 'before',
				'class' => 'testclass',
			
			
			
			]
		);
		$widget->add_control(
			'adminurl',
			[
				'label' => __( '', 'Genoo Elementor Extension' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<div class="adminurl" name="adminurl" style="display:none";><input name="getadminurl" class="getadminurl" value="'.admin_url('admin-ajax.php').'" /></div>', 'Genoo Elementor Extension' ),
				'content_classes' => 'adminurls',
			]
	
		);

  
		$widget->add_control(
			'SelectLeadType',
			[
				'label' => __('Select LeadType (where leads will be put who submit this form):', 'Genoo Elementor Extension' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' =>  $this->getleadtypes(),
                'default' =>  $getleadtypes[0],
				'label_block' => true,
				'separator' => 'before',
				
			
			
                 
			]
		);

  
	$widget->add_control(
			'SelectEmail',
			[
				'label' => __('Select Email to Send:', 'Genoo Elementor Extension'),
				'type' => \Elementor\Controls_Manager::SELECT,
			    'options' =>  $this->datasuccess(),
                 'default' => $datasuccess[0],
				'label_block' => true,
				'required' => true,
				'separator' => 'before',
			    'conditions' => [
			    'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'Select',
                        'operator' => '!==',
                        'value' => 'Select email folders'
                    ],
                    [
                        'name' => 'Select',
                        'operator' => '!==',
                        'value' => '0'
                    ],
                     [
                        'name' => 'SelectEmail',
                        'operator' => '!=',
                        'value' => ' '
                    ],
                       
                  
                ]
               
             ],
            			
			
			]
		);
	    $widget->add_control(
		'show_title',
		[
			'label_on' => __('Show', 'Genoo Elementor Extension' ),
			'label_off' => __('Hide', 'Genoo Elementor Extension' ),
			'label' => __('Register User into Webinar?', 'Genoo Elementor Extension' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default' => 'No',
		]);
		
			$widget->add_control(
			'SelectWebinar',
			[
				'label' => __('Select Webinar:', 'Genoo Elementor Extension'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' =>  $this->webinars(),
                 'default' => $webinars[0],
				'placeholder' => 'Select Webinar',
				'label_block' => true,
				'separator' => 'before',
				 'condition' => array(
                'show_title' => 'yes',
                 ),
			
			
			
			]
		);

		
		$widget->end_controls_section();

	}

public static function getleadtypes()
{
          global $WPME_API;
        //getting api response for leadtypes,zoomwebinars,emailfolders
        if (method_exists($WPME_API, 'callCustom')):
            try { // Make a GET request, to Genoo / WPME api, for that rest endpoint
                $leadTypes = $WPME_API->callCustom('/leadtypes', 'GET', NULL);
                 foreach($leadTypes as $leadType):
                        $leadtypes[0] = 'Select Lead Types';
                     $leadtypes[$leadType->id] = $leadType->name;
                     endforeach;
            }
               catch(Exception $e) {
                    if ($WPME_API->http->getResponseCode() == 404):
                        // Looks like folders not found
                        
                    endif;
                }
            endif;
            return $leadtypes;
       
}
public static function getemailfolders()
{
          global $WPME_API;
        //getting api response for leadtypes,zoomwebinars,emailfolders
        if (method_exists($WPME_API, 'callCustom')):
            try { // Make a GET request, to Genoo / WPME api, for that rest endpoint
                $emailfolders = $WPME_API->callCustom('/emailfolders', 'GET', NULL);
                 foreach($emailfolders as $emailfolder):
                     $emailfolderslead[0] = 'Select Email Folders';
                     $emailfolderslead[$emailfolder->id] = $emailfolder->name;
                     endforeach;
            }
               catch(Exception $e) {
                    if ($WPME_API->http->getResponseCode() == 404):
                        // Looks like folders not found
                        
                    endif;
                }
            endif;
            return $emailfolderslead;
       
}
public static function webinars()
{
          global $WPME_API;
        //getting api response for leadtypes,zoomwebinars,emailfolders
        if (method_exists($WPME_API, 'callCustom')):
            try { // Make a GET request, to Genoo / WPME api, for that rest endpoint
                $zoomwebinars = $WPME_API->callCustom('/zoomwebinars/all', 'GET', NULL);
                 foreach($zoomwebinars as $zoomwebinar):
                     $zoomwebinarslead[0] = 'Select webinars';
                     $zoomwebinarslead[$zoomwebinar->id] = $zoomwebinar->name;
                     endforeach;
            }
               catch(Exception $e) {
                    if ($WPME_API->http->getResponseCode() == 404):
                        // Looks like folders not found
                        
                    endif;
                }
            endif;
           
            return $zoomwebinarslead;
       
}

//select


   public function datainsert()
    {
         global $WPME_API;
       
         $id = $_REQUEST['selectid'];
       
  
    if (method_exists($WPME_API, 'callCustom')):
        try { // Make a GET request, to Genoo / WPME api, for that rest endpoint
        
          if($id!=0):
            $email_data = $WPME_API->callCustom('/emails/' . $id, 'GET', NULL);
           
            else:
                 $email_data = 'Select emails';
             endif;
           wp_send_json($email_data);
          
            }
            catch(Exception $e) {
                if ($WPME_API->http->getResponseCode() == 404):
                    // Looks like emails not found
                    
                endif;
            }
        endif;
        
       
    }
    public function datasuccess()
    {
     global $post;
      
     $elementor_data = get_post_meta($post->ID,'_elementor_data',true);
         
      $decode_datas = json_decode($elementor_data);
      
      foreach($decode_datas as $decode_data)
      {
       $data = $decode_data->elements;
       
       
       foreach($data as $dataelement)
       {
          
         $data_element = $dataelement->elements;
         
         
         foreach($data_element as $elements_value)
         {
            $datavalue = $elements_value->settings->Select;
            $dataemail = $elements_value->settings->SelectEmail;
             global $WPME_API;
            //calling leadfields api for showing dropdown
              if (method_exists($WPME_API, 'callCustom')):
            try {
                if($datavalue!=0):
                 $customfields = $WPME_API->callCustom('/emails/' .$datavalue, 'GET', NULL);
                 foreach ($customfields as $customfield):
                          $email_values[0] = 'Select emails';
                          $email_values[$customfield->id] = $customfield->name;
                          endforeach;
                         else:
                        $email_values[0] = 'Select emails';    
                         endif;
                          
            }
              catch(Exception $e) {
                    if ($WPME_API->http->getResponseCode() == 404):
                        // Looks like leadfields not found
                        
                    endif;
                }
                    
                    endif;
                    if($dataemail==0)
                    {
                      $email_values[0] = 'Select emails'; 
                    }
                        
              return $email_values;
     
     }
     
     
     
   }
  }
 

    }

  	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @access Public
	 * @param array $element
	 */
/*	public function maybe_add_pattern( $field, $field_index, $form_widget ) {
		if ( ! empty( $field['Select'] ) ) {

			$form_widget->add_render_attribute( 'input' . $field_index,
				[
					// Add pattern attribute
					'SelectEmail' => $field['SelectEmail'],
					// Add pattern to validation message
					'oninvalid' => 'this.setCustomValidity( "Please match the requested format \"' . $field['field_patten'] . '\" ")',
					// Clear validation message when needed
					'oninput' => 'this.setCustomValidity("")',
				]
			);
		}
		return $field;
	}
} 
	*/ 
	 
	 
	 
	 
	 
	public function on_export( $element ) {
		unset(
			$element['Select'],
			$element['SelectLeadType'],
			$element['SelectEmail'],
			$element['SelectWebinar']
		);
	}
	
	
	
}
     