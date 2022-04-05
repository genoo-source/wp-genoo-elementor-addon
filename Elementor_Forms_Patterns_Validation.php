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
        $map_fields = array_keys($this->map_fields());
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
        global $post, $wpdb,$WPME_API;
        $elementor = \Elementor\Plugin::instance();
        $control_data = $elementor->controls_manager->get_control_from_stack(
            $element->get_name(),
            'form_fields'
        );

       $tablenames = $wpdb->prefix."leadtype_form_save_elementor";
      
     
         
        if (is_wp_error($control_data)) {
            return;
        }
        $tmp = new Elementor\Repeater();

        if (method_exists($WPME_API, 'callCustom')):
            try {
                // Make a GET request, to Genoo / WPME api, for that rest endpoint

                $leadTypes = $WPME_API->callCustom('/leadtypes', 'GET', null);
            } catch (Exception $e) {
                if ($WPME_API->http->getResponseCode() == 404):


                    // Looks like folders not found
                endif;
            }
        endif;

        $lead_details =  $wpdb->get_results("select `lead_values` from $tablenames where `post_id`=$post->ID");


        foreach($lead_details as $lead_detail)
        {

             $lead_value_explode = $lead_detail->lead_values;

        }

        $lead_value_options ='<div class="checkbox_values">';
       
        foreach ($leadTypes as $leadType) {

        $lead_value_options.='<div><input type="checkbox" id="checkbox_values" value="'.$leadType->id.'" name="checkbox_values[]"
            value=""checked; /><label>'.$leadType->name.'</label></div>';
            
    }

        $lead_value_options.= '<button type="button" class="leadtypesave" name="leadtypesave">Save</button></div>';

        $lead_value_after_save ='<div class="lead_value_after_save">';
        
     
        $lead_value_after_save.='</div>';
    

        $tmp->add_control('checkbox_values',[
            'name' => 'checkbox_values',
            'inner_tab' => 'form_fields_advanced_tab',
            'tab' => 'content',
            'tabs_wrapper' => 'form_fields_tabs',
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => __(
               $lead_value_options,
                'Genoo Elementor Extension'
            ),
            'content_classes' => 'leadtype_options',
            'condition' => [
                'third_party_input' => 'leadtypes',
            ],
        ]);
        $tmp->add_control('checkbox_count_items',[
            'name' => 'checkbox_count_items',
            'inner_tab' => 'form_fields_advanced_tab',
            'tab' => 'content',
            'tabs_wrapper' => 'form_fields_tabs',
            'type' => 'text',
            'content_classes' => 'leadtype_options',
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
			'input_lead_values',
			[
                'name' => 'input_lead_values',
				'label' => esc_html__( 'Update Value', 'Genoo elementor extention' ),
				'type' => 'text',
				'separator' => 'before',
			   'classes' => 'lead_values',
			]
		);
        $tmp->add_control(
			'input_lead_labels',
			[
                'name' => 'input_lead_labels',
				'label' => esc_html__( 'Update Label', 'Genoo elementor extention' ),
				'type' => 'text',
				'separator' => 'before',
			   'classes' => 'lead_labels',
			]
		);
           
      
        $pattern_field = $tmp->get_controls();

       

      // insert new class field in advanced tab before field ID control
            $new_order = [];
            foreach ($control_data['fields'] as $field_key => $field) {

              
                if ('custom_id' === $field['name']) {
                    $new_order['checkbox_values'] =  $pattern_field['checkbox_values'];
                    $new_order['checkbox_after_save_values'] =  $pattern_field['checkbox_after_save_values'];
                    $new_order['input_lead_values'] = $pattern_field['input_lead_values'];
                    $new_order['input_lead_labels'] = $pattern_field['input_lead_labels'];
                  
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




}
new Elementor_Forms_Patterns_Validation();
?>
