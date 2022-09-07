<?php

class Elementor_Forms_Patterns_Validation
{
    public function __construct()
    {
        add_action(
            'elementor/element/form/section_form_fields/before_section_end',
            [$this, 'add_class_field_control'],
            100,
            2
        );
  
        add_action(
            'elementor/element/form/section_form_fields/before_section_end',
            [$this, 'add_class_field_control_leadtype'],
            100,
            2
        );
          
        add_action(
            'elementor/element/form/section_form_fields/before_section_end',
            [$this, 'add_class_field_control_leadfolder'],
            100,
            2
        );
      
           add_action(
            'elementor/element/form/section_form_fields/before_section_end',
            [$this, 'add_class_field_control_leadtype_save'],
            100,
            2
        );
    }

    /**
     * add_class_field_control
     * @param $element
     * @param $args
     * //add new field inside the advanced tab
     */

    public function add_class_field_control($element, $args)
    {
 
        global $post, $WPME_API;
        $elementor = \Elementor\Plugin::instance();
        $control_data = $elementor->controls_manager->get_control_from_stack(
            $element->get_name(),
            'form_fields'
        );
    

        if (is_wp_error($control_data)) {
            return;
        }
        
        if(is_array($this->map_fields())){
           $map_fields = array_keys($this->map_fields());
	}
        // create a new class control as a repeater field
        $tmp = new Elementor\Repeater();

        $tmp->add_control('third_party_input', [
            'name' => 'third_party_input',
            'label' => 'Genoo/WPMktgEngine Field:',
            'inner_tab' => 'form_fields_advanced_tab',
            'tab' => 'content',
            'tabs_wrapper' => 'form_fields_tabs',
            'type' => 'select',
            'options' => $this->map_fields(),
            'default' => $map_fields[0],
            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'custom_id',
                        'operator' => '!==',
                        'value' => 'name',
                    ],
                    [
                        'name' => 'custom_id',
                        'operator' => '!==',
                        'value' => 'email',
                    ],
                ],
            ],
        ]);
        $tmp->add_control('pre_mapped_name', [
            'name' => 'pre_mapped_name',
            'label' => 'Genoo pre Mapped With',
            'inner_tab' => 'form_fields_advanced_tab',
            'tab' => 'content',
            'tabs_wrapper' => 'form_fields_tabs',
            'type' => 'text',
            'disabled' => true,

            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'custom_id',
                        'operator' => '==',
                        'value' => 'name',
                    ],
                ],
            ],
            'default' => 'First Name',
        ]);
        $tmp->add_control('pre_mapped_email', [
            'name' => 'pre_mapped_email',
            'label' => 'Genoo pre Mapped With',
            'inner_tab' => 'form_fields_advanced_tab',
            'tab' => 'content',
            'tabs_wrapper' => 'form_fields_tabs',
            'type' => 'text',
            'disabled' => true,

            'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'custom_id',
                        'operator' => '==',
                        'value' => 'email',
                    ],
                ],
            ],
            'default' => 'Email',
        ]);

        $pattern_field = $tmp->get_controls();

        $third_party_input = $pattern_field['third_party_input'];
        $pre_mapped_email = $pattern_field['pre_mapped_email'];

        $pre_mapped_name = $pattern_field['pre_mapped_name'];
        // insert new class field in advanced tab before field ID control
        $new_order = [];
        foreach ($control_data['fields'] as $field_key => $field) {
            if ('custom_id' === $field['name']) {
                $new_order['third_party_input'] = $third_party_input;
                $new_order['pre_mapped_name'] = $pre_mapped_name;
                $new_order['pre_mapped_email'] = $pre_mapped_email;
            }

            $new_order[$field_key] = $field;
        }

        $control_data['fields'] = $new_order;

        $element->update_control('form_fields', $control_data);
    }     

    public function add_class_field_control_leadtype($element, $args)
    {
        global $post, $WPME_API;
        $elementor = \Elementor\Plugin::instance();
        $control_data = $elementor->controls_manager->get_control_from_stack(
            $element->get_name(),
            'form_fields'
        );
        
        if (is_wp_error($control_data)) {
            return;
        }
        $tmp = new Elementor\Repeater();

        if (method_exists($WPME_API, 'callCustom')):
            try {
                // Make a GET request, to Genoo / WPME api, for that rest endpoint

                $leadTypes = $WPME_API->callCustom('/listLeadTypeFolders/Uncategorized', 'GET', null);
               
            } catch (Exception $e) {
                if ($WPME_API->http->getResponseCode() == 404):


                    // Looks like folders not found
                endif;
            }
        endif;
        
        $leadTypesvalueset = array();
         $leadTypesvalueset[0] = 'Uncategorized';
       
        foreach($leadTypes as $leadTypesvalue)
        { 
            $leadTypesvalueset[$leadTypesvalue->type_id . '#'] = $leadTypesvalue->name;

        foreach ($leadTypesvalue->child_folders as $leadTypesfolders):
                        
                   if ($leadTypesfolders->parent_id == $leadTypesvalue->type_id) {
                                $leadTypesvalueset[
                                    $leadTypesvalue->type_id .
                                        '#' .
                                        $leadTypesfolders->type_id
                                ] = '--' . $leadTypesfolders->name;
                            }
            
            endforeach;
        }
    
        
         $tmp->add_control(
			'show_leadfolders',
			[
			    'name' => 'show_leadfolders',
				'label' => esc_html__( 'Select lead folders', 'Genoo Elementor Extension' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
		         'options' => $leadTypesvalueset,
		          'condition' => [
                    'third_party_input' => 'leadtypes',
                ],
                ]
		);
		
		$leadfolder_value_loader ='<div class="elementorleadfolderloader">';
		
        $leadfolder_value_loader .='<p style="display:none;"><img src='.plugins_url(
                    "/images/loading.gif",
                    __FILE__
                ).' /></p>';
        $leadfolder_value_loader.='</div>';
        
        
            $tmp->add_control('elementor_lead_folder_loader',[
                'inner_tab' => 'form_fields_advanced_tab',
                'tab' => 'content',
                'tabs_wrapper' => 'form_fields_tabs',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __(
                   $leadfolder_value_loader,
                    'Genoo Elementor Extension'
                ),
                'content_classes' => 'elementor_lead_folder_loader',
                
                'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'show_leadfolders',
                        'operator' => '!=',
                        'value' => '',
                    ],
                    
                    [
                        'name' => 'third_party_input',
                        'operator' => '==',
                        'value' => 'leadtypes',
                    ],
                ],
            ],
            ]);

        
          $pattern_field = $tmp->get_controls();
       
          $new_order = [];
            foreach ($control_data['fields'] as $field_key => $field) {

                if ('custom_id' === $field['name']) {

                    
            $new_order['show_leadfolders'] = $pattern_field['show_leadfolders'];
             $new_order['elementor_lead_folder_loader'] = $pattern_field['elementor_lead_folder_loader'];
            
                }
            
        $new_order[$field_key] = $field;
            }

            $control_data['fields'] = $new_order;


            $element->update_control('form_fields', $control_data);
      
    }
    
  public function updated_lead_types()
  {
      global $post;
      
     $elementor_data = get_post_meta($post->ID, '_elementor_data', true);
        
    if (!empty($elementor_data)):
        
           $get_show_leadfolders = [];
        
            $decode_datas = json_decode($elementor_data);
           
            foreach ($decode_datas as $decode_data) {
                
                $data = $decode_data->elements;

                foreach ($data as $dataelement) {
                    
                    $data_element = $dataelement->elements;
                 
                    foreach ($data_element as $elements_value) {
                        
                 foreach($elements_value->settings->form_fields as $form_fields)
                 {
                 
                 $get_show_leadfolders[] = $form_fields->show_leadfolders;
                 
                 }
                        
   }
    }
        }
        return $get_show_leadfolders;
                 
        endif;
  }

 public function add_class_field_control_leadfolder($element, $args)
    {
          global $post, $WPME_API;
        $elementor = \Elementor\Plugin::instance();
        $control_data = $elementor->controls_manager->get_control_from_stack(
            $element->get_name(),
            'form_fields'
        );
        
           $updated_lead_types = $this->updated_lead_types();
           
            $leadtypes_array = [];
                
            foreach($updated_lead_types as $updated_lead_type)
            {
                foreach($updated_lead_type as $updated_lead)
                {
                     $update_lead_type = explode("#", $updated_lead);
                     
                      $update_lead_type_value = $update_lead_type[1];
                      
                      if($update_lead_type_value!='')
                      {
                        $leadtypes_array[] = $update_lead_type_value;  
                      }
                      else
                      {
                      $leadtypes_array[] = $update_lead_type[0];      
                      }
                       
                    
                }
                
            }
            
         if (is_wp_error($control_data)) {
            return;
        }
        $tmp = new Elementor\Repeater();
        
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
        
        $leadtypesbasedonfolder = array();
               
        foreach($leadTypesvalues as $leadTypesvalue)
        {
            foreach($folder_names as $key => $foldervalue){  
         if (in_array($leadTypesvalue->folder_id, $leadtypes_array)) :
        
              if($key==$leadTypesvalue->folder_id):
             $leadtypesbasedonfolder[$leadTypesvalue->folder_id.'-'.$leadTypesvalue->id] = $leadTypesvalue->name. "#(" . $foldervalue . ")";
             endif;
      
               endif;
        }
        }
        
          
            $tmp->add_control(
			'show_leadtypes',
			[
			    'name' => 'show_leadtypes',
				'label' => esc_html__( 'Select lead types', 'Genoo Elementor Extension' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
		         'options' => $leadtypesbasedonfolder,
                 'default' => $leadtypesbasedonfolder[0],
                
                  'conditions' => [
                'relation' => 'and',
                'terms' => [
                    [
                        'name' => 'show_leadfolders',
                        'operator' => '!=',
                        'value' => '',
                    ],
                    
                    [
                        'name' => 'third_party_input',
                        'operator' => '==',
                        'value' => 'leadtypes',
                    ],
                ],
            ],
                 ]
		);

         $pattern_field = $tmp->get_controls();
         
           $new_order = [];
           
         foreach ($control_data['fields'] as $field_key => $field) {

                if ('custom_id' === $field['name']) {
               $new_order['show_leadtypes'] = $pattern_field['show_leadtypes'];
                }
            $new_order[$field_key] = $field;
            }

            $control_data['fields'] = $new_order;

            $element->update_control('form_fields', $control_data);
        
    }
    
   public function add_class_field_control_leadtype_save($element, $args)
    {
        
           global $post, $WPME_API,$wpdb;
        
        $elementor = \Elementor\Plugin::instance();
        $control_data = $elementor->controls_manager->get_control_from_stack(
            $element->get_name(),
            'form_fields'
        );

        if (is_wp_error($control_data)) {
            return;
        }
        $tmp = new Elementor\Repeater();
        
        $tmp->add_control('leadsavetypbutton', [
                'name' => 'leadsavetypbutton',
            'inner_tab' => 'form_fields_advanced_tab',
            'tab' => 'content',
            'tabs_wrapper' => 'form_fields_tabs',
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => __(
                '<button type="button"  class="btn btn-primary saveleadtypeidset">Edit label</button>',
                'Genoo Elementor Extension'
            ),
            'content_classes' => 'saveleadtypeidsetup',
            'conditions' => [
                'relation' => 'and',
                'terms' => [
                
                    [
                        'name' => 'show_leadfolders',
                        'operator' => '!=',
                        'value' => '',
                    ],
                    [
                        'name' => 'third_party_input',
                        'operator' => '==',
                        'value' => 'leadtypes',
                    ],  
                      [
                        'name' => 'show_leadtypes',
                        'operator' => '!=',
                        'value' => '',
                    ],
                  
                ],
            ],  
        ]); 
        $lead_value_loader ='<div class="elementorleadloader">';
        $lead_value_loader .='<p style="display:none;"><img src='.plugins_url(
                    "/images/loading.gif",
                    __FILE__
                ).' /></p>';
        $lead_value_loader.='</div>';
        
        $lead_value_after_save ='<div class="lead_value_after_save">';
        
        $lead_value_after_save.='</div>';
        
        $updatelabel = $wpdb->prefix.'leadtype_form_save_elementor';
        
                $lead_options = [];
                
           $updated_label_items = array();

        $updated_labels = $wpdb->get_results("select lead_values,lead_label,folder_id from $updatelabel where `post_id`=$post->ID");
         
   
        foreach($updated_labels as $updated_label){
         
          $updated_label_items[$updated_label->folder_id.'-'.$updated_label->lead_values] = $updated_label->lead_label;
            }
            
            
    $tmp->add_control('elementor_lead_loader',[
                'inner_tab' => 'form_fields_advanced_tab',
                'tab' => 'content',
                'tabs_wrapper' => 'form_fields_tabs',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __(
                   $lead_value_loader,
                    'Genoo Elementor Extension'
                ),
                'content_classes' => 'elementor_lead_loader',
                'condition' => [
                    'third_party_input' => 'leadtypes',
                ],
            ]);
            
       
    $tmp->add_control('checkbox_after_save_values',[
                'inner_tab' => 'form_fields_advanced_tab',
                'tab' => 'content',
                'tabs_wrapper' => 'form_fields_tabs',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __(
                   $lead_value_after_save,
                    'Genoo Elementor Extension'
                ),
                'content_classes' => 'checkbox_after_save_values',
                'condition' => [
                    'third_party_input' => 'leadtypes',
                ],
            ]);
            
            
          $tmp->add_control(
			'updated_labels',
			[
			    'name' => 'updated_labels',
				'label' => esc_html__( 'Edited Labels', 'Genoo Elementor Extension' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
					'multiple' => true,
		         'options' => $updated_label_items,
		         'conditions' => [
                'relation' => 'and',
                'terms' => [
                     [
                        'name' => 'show_leadfolders',
                        'operator' => '!=',
                        'value' => '',
                    ],
                    [
                        'name' => 'show_leadtypes',
                        'operator' => '!=',
                        'value' => '',
                    ],
                    [
                        'name' => 'third_party_input',
                        'operator' => '==',
                        'value' => 'leadtypes',
                    ],
                 
                ],
            ],
                 ]
		);
            
           $pattern_field = $tmp->get_controls();


        // insert new class field in advanced tab before field ID control
            $new_order = [];
            foreach ($control_data['fields'] as $field_key => $field) {

                if ('custom_id' === $field['name']) {

                    $new_order['leadsavetypbutton'] = $pattern_field['leadsavetypbutton'];
                    
                      $new_order['elementor_lead_loader'] = $pattern_field['elementor_lead_loader'];
                    
                    
                    $new_order['checkbox_after_save_values'] = $pattern_field['checkbox_after_save_values'];
                     
                    
                    $new_order['updated_labels'] = $pattern_field['updated_labels'];
               
           
                }
            

                $new_order[$field_key] = $field;
            }

            $control_data['fields'] = $new_order;


            $element->update_control('form_fields', $control_data);
  
        
    }

 //show leadfields for mapping

    public function map_fields()
    {
        global $WPME_API;

        //calling leadfields api for showing dropdown
        if (method_exists($WPME_API, 'callCustom')):
            try {
                $customfields = $WPME_API->callCustom(
                    '/leadfields',
                    'GET',
                    null
                );
                if ($WPME_API->http->getResponseCode() == 204):
                    // No leadfields based on folderdid onchange! Ooops


                elseif ($WPME_API->http->getResponseCode() == 200):
                    $customfieldsjson = $customfields;
                endif;
            } catch (Exception $e) {
                if ($WPME_API->http->getResponseCode() == 404):


                    // Looks like leadfields not found
                endif;
            }
        endif;
        //$pre_mapped_fields = array( 'First Name', 'Email');
        foreach ($customfieldsjson as $customfields):
            //   if ( !in_array( trim( $customfields->label ), $pre_mapped_fields ) ):
            $map_fields[0] = 'Do not map fields';
            $map_fields[$customfields->key] = $customfields->label;
            //endif;
        endforeach;

        return $map_fields;
    }

 //get selected item options
    
       public function get_item_options()
       {
        global $post, $WPME_API;
        
        
        $elementor_data = get_post_meta($post->ID, '_elementor_data', true);
        
          if(!empty($elementor_data)):
            $decode_datas = json_decode($elementor_data);
           
            foreach($decode_datas as $decode_data) {
                $data = $decode_data->elements;

                foreach($data as $dataelement) {
                    $data_element = $dataelement->elements;
                 
                    foreach($data_element as $elements_value) {
                       
                      foreach($elements_value->settings->form_fields as $item_form_fields)
                         {
               
                $item_ids[$item_form_fields->_id] = $item_form_fields->updated_labels;
                      
                         }
                  
                    }

}
}


return array_filter($item_ids);
 
endif;

}

}
new Elementor_Forms_Patterns_Validation();
?>
