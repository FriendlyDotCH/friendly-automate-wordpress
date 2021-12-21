<?php

    class Elementic_Form_Action extends \ElementorPro\Modules\Forms\Classes\Action_Base {
        public function get_name() {return 'Friendly Automate';}

        public function get_label() {return __( 'Friendly Automate', 'text-domain' );}

        /**
         * register elementor forms
         * @param type $widget 
         * @return type
         */
        public function register_settings_section( $widget ) {

            $widget->start_controls_section(
                'section_friendly',
                [
                    'label' => __( 'Friendly Automate', 'text-domain' ),
                    'condition' => [
                        'submit_actions' => $this->get_name(),
                    ],
                ]
            );

            $widget->add_control(
                'friendly_url',
                [
                    'label' => __( 'Friendly Automate Form URL *', 'text-domain' ),
                    'type' => \Elementor\Controls_Manager::URL,
                    'placeholder' => 'http://yourfriendlyurl.com/',
                    'label_block' => true,
                    'separator' => 'before',
                    'description' => __( 'Enter the URL where you have Friendl yAutomate installed', 'text-domain' ),
                ]
            );


            $widget->add_control(
                'friendly_form_id',
                [
                    'label' => __('Friendly Automate Form ID *', 'text-domain'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'placeholder' => '99',
                    'label_block' => true,
                    'separator' => 'before',
                    'description' => __( 'Fill with your form id', 'text-domain' ),
                ]
            );


            $widget->end_controls_section();
        }


        public function run( $record, $ajax_handler ) {
            $settings = $record->get( 'form_settings' );

            //  Make sure that there is a Friendly url
            if ( empty( $settings['friendly_url'] ) ) {
                return;
            }

            //  Make sure that there have a Form ID
            if ( empty( $settings['friendly_form_id'] ) ) {
                return;
            }

            // Get submitted Form Data
            $raw_fields = $record->get( 'fields' );

            // Normalize the Form Data
            $fields = [
                'formId' => $settings['friendly_form_id']
            ];
            foreach ( $raw_fields as $id => $field ) {
                $fields[ $id ] = $field['value'];
            }

			
            $response = wp_remote_post(rtrim($settings['friendly_url']['url'],"/")."/form/submit?formId=".$settings['friendly_form_id'], [
                'body' => ["mauticform" => $fields],
				'headers' => [ 'client_ip' => $_SERVER[ "REMOTE_ADDR" ]]
            ] );

            // $message = preg_match('/<div class=\"well text-center\">(.*?)<\/div>/s', $response['body'], $match);




            // echo trim(strip_tags($match[1]));

        }





        public function on_export( $element ) {
            return $element;
        }
    }