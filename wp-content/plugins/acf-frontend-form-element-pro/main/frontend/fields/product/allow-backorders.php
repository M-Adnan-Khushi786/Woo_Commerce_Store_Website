<?php

if( ! class_exists('acf_field_allow_backorders') ) :

class acf_field_allow_backorders extends acf_field {
	
	
	/*
	*  initialize
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'allow_backorders';
		$this->label = __( "Allow Backorders",FEA_NS );
		$this->category = __( 'Product Inventory', FEA_NS );
		$this->defaults = array(
			'multiple' 		=> 0,
			'allow_null' 	=> 0,
			'choices'		=> array(),
			'default_value'	=> '',
			'ui'			=> 0,
			'ajax'			=> 0,
			'placeholder'	=> '',
			'return_format'	=> 'value',
            'field_type'    => 'radio',
            'layout'        => 'vertical',
            'other_choice'  => 0,
		);
        add_filter( 'acf/load_field/type=select',  [ $this, 'load_allow_backorders_field'], 2 );
        add_filter( 'acf/update_value/type=' . $this->name,  [ $this, 'pre_update_value'], 9, 3 );      
		
	}

    function load_allow_backorders_field( $field ){
        if( ! empty( $field['custom_backorders'] ) ){
            $field['type'] = 'allow_backorders';
        }
        return $field;
    }


    
    function prepare_field( $field ){
        if( ! $field['choices'] ){
            $field['choices'] = array(
                'no'     => __( 'Do not allow', 'woocommerce' ),
                'notify' => __( 'Allow, but notify customer', 'woocommerce' ),
                'yes'    => __( 'Allow', 'woocommerce' ),
            );
        }

        if( isset( $GLOBALS['form_fields'] ) ){
            $fields = $GLOBALS['form_fields'];    
            $field['conditional_logic'] = array(
                array(
                    array(
                        'field' => $fields['product_types'],
                        'operator' => '==',
                        'value' => 'simple',
                    ),
                    array(
                        'field' => $fields['manage_stock'],
                        'operator' => '==',
                        'value' => '1',
                    )
                ),
                array(
                    array(
                        'field' => $fields['product_types'],
                        'operator' => '==',
                        'value' => 'variable',
                    ),
                    array(
                        'field' => $fields['manage_stock'],
                        'operator' => '==',
                        'value' => '1',
                    )
                ),
            );
        }
        return $field;
    }

    public function load_value( $value, $post_id = false, $field = false ){
        $value = get_post_meta( $post_id, '_backorders', true );
        return $value;
    }

    function load_field( $field ){
      $field['name'] = $field['type'];
      return $field;
    }

function pre_update_value( $value, $post_id = false, $field = false ){
        update_metadata( 'post', $post_id, '_backorders', $value );
        return null;
    }

    public function update_value( $value, $post_id = false, $field = false ){
        return null;
    }

    function render_field( $field ){
        $field['type'] = $field['field_type'];
        acf_render_field( $field );

    }

    	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {	
		
		// field_type
		acf_render_field_setting( $field, array(
			'label'			=> __('Appearance',FEA_NS),
			'instructions'	=> __('Select the appearance of this field',FEA_NS),
			'type'			=> 'select',
			'name'			=> 'field_type',
			'optgroup'		=> true,
			'choices'		=> array(
                'radio' => __('Radio Buttons', FEA_NS),
                'select' => _x('Select', 'noun', FEA_NS)
			)
		));
    }

}

// initialize
acf_register_field_type( 'acf_field_allow_backorders' );

endif;
	
?>