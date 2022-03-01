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
    }

    /**
     * add_class_field_control
     * @param $element
     * @param $args
     * //add new field inside the advanced tab
     */

    public function add_class_field_control($element, $args)
    {
        global $post;
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
