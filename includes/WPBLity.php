<?php

if ( !class_exists('WPBLity') ) {

  class WPBLity extends WPBakeryShortCode {

    function __construct() {
        add_action( 'vc_before_init', array( $this, 'create_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_shortcode( 'wpb_lity', array($this, 'render_shortcode') );
    }

    public function create_shortcode() {
      // stop all if WPB not enable
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }

        vc_map(array(
            'name' => esc_html__( 'Video Lightbox', 'wpb-lity' ),
            'base' => 'wpb_lity',
            'description' => esc_html__( 'Open video as lightbox', 'wpb-lity' ),
            'category' => esc_html__( 'Content', 'wpb-lity' ),
            'params' => array(
                array(
                    'type'          => 'attach_image',
                    'heading'       => esc_html__( 'Video Poster', 'wpb-lity' ),
                    'param_name'    => 'poster'
                ),

                array(
                    'type'          => 'textfield',
                    'heading'       => esc_html__( 'URL Link', 'wpb-lity' ),
                    'param_name'    => 'url_link',
                    'admin_label'   => true
                ),

                array(
                    'type'          => 'textfield',
                    'heading'       => esc_html__( 'Label Link', 'wpb-lity' ),
                    'description'   => esc_html__( 'Leave empty to use default text: Video Link', 'wpb-lity' ),
                    'param_name'    => 'label_link'
                ),

                array(
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Link Shape', 'wpb-lity' ),
                    'description'   => esc_html__( 'Shape of button link', 'wpb-lity' ),
                    'param_name'    => 'link_shape',
                    'group'         => esc_html__( 'Styles', 'wpb-lity' ),
                    'value'         => array(
                        'Square'        => 'square',
                        'Rounded'       => 'rounded',
                        'Pills'         => 'pills',
                        'Nude'          => 'nude',
                    ),
                ),

                array(
                    'type'              => 'colorpicker',
                    'heading'           => esc_html__( 'Link Color', 'wpb-lity' ),
                    'param_name'        => 'link_color',
                    'value'             => '',
                    // 'edit_field_class'  => 'vc_col-xs-6',
                    'group'             => esc_html__( 'Styles', 'wpb-lity' ),
                ),

                // array(
                //     'type'              => 'colorpicker',
                //     'heading'           => esc_html__( 'Link Color Hover', 'wpb-lity' ),
                //     'param_name'        => 'link_color_hover',
                //     'value'             => '',
                //     'edit_field_class'  => 'vc_col-xs-6',
                //     'group'             => esc_html__( 'Styles', 'wpb-lity' ),
                // ),

                array(
                    'type'              => 'colorpicker',
                    'heading'           => esc_html__( 'Background Color', 'wpb-lity' ),
                    'param_name'        => 'link_bg_color',
                    'value'             => '',
                    // 'edit_field_class'  => 'vc_col-xs-6',
                    'group'             => esc_html__( 'Styles', 'wpb-lity' ),
                ),

                // array(
                //     'type'              => 'colorpicker',
                //     'heading'           => esc_html__( 'Background Color Hover', 'wpb-lity' ),
                //     'param_name'        => 'link_bg_color_hover',
                //     'value'             => '',
                //     'edit_field_class'  => 'vc_col-xs-6',
                //     'group'             => esc_html__( 'Styles', 'wpb-lity' ),
                // ),

                array(
                    'type'          => 'textfield',
                    'heading'       => esc_html__( 'Extra class name', 'wpb-lity' ),
                    'description'   => esc_html__( 'add a class name and refer to it in custom CSS', 'wpb-lity' ),
                    'param_name'    => 'custom_class',
                ),
            )
        ));
    }

    public function render_shortcode( $atts, $content, $tag ) {
        $args = array(
            'poster'                => '',
            'url_link'              => '',
            'label_link'            => 'Video Link',
            'link_color'            => '',
            // 'link_color_hover'      => '',
            'link_bg_color'         => '',
            // 'link_bg_color_hover'   => '',
            'link_shape'            => 'square',
            'custom_class'          => ''
        );

        $atts = ( shortcode_atts( $args, $atts ) );

        $content        = wpb_js_remove_wpautop( $content, true );
        $video_poster   = wp_get_attachment_url( esc_html( $atts[ 'poster' ] ), 'full' );
        $url_link       = esc_url( $atts[ 'url_link' ] );
        $label_link     = esc_html( $atts[ 'label_link' ] );
        $link_shape     = $atts[ 'link_shape' ];
        $link_color     = $atts[ 'link_color' ];
        $link_bg_color  = $atts[ 'link_bg_color' ];
        $custom_class   = $atts[ 'custom_class' ];

        // component class state
        $component_class = array();
        $component_class[] = 'wpb-lity';
        if ( $video_poster  ) {
            $component_class[] = 'wpb-lity--with-image';
        }

        // link class state
        $link_class = array();
        $link_class[] = 'wpb-lity__link';
        if ( $video_poster  ) {
            $link_class[] = 'wpb-lity__link--with-image';
        }
        if ( $link_shape == 'rounded' ) {
            $link_class[] = 'wpb-lity__link--rounded';   
        }
        if ( $link_shape == 'pills' ) {
            $link_class[] = 'wpb-lity__link--pills';   
        }
        if ( $link_shape == 'nude' ) {
            $link_class[] = 'wpb-lity__link--nude';   
        }

        // link css variables
        $link_styles = array();
        if ( $link_color ) {
            $link_styles[] = '--color-link:'. esc_attr( $link_color ) .';';
        }
        if ( $link_bg_color ) {
            $link_styles[] = '--color-link-bg:'. esc_attr( $link_bg_color ) .';';
        }

        $link_attrs = array();
        $link_attrs[] = 'class="'. implode( ' ', $link_class) .'"';
        $link_attrs[] = 'href="' . $url_link . '"';
        $link_attrs[] = $link_styles ? 'style="'. implode( ' ', $link_styles) .'"' : NULL;

        $output = '';
        $output .= '<div class="'. implode( ' ', $component_class) .'">';
        
        if ( $video_poster ) {
            $output .= '<figure class="wpb-lity__image">';
                $output .= '<img class="wpb-lity__image-item" src="' . esc_url( $video_poster ) . '">';
            $output .= '</figure>';
        }

            // $output .= '<a class="'. implode( ' ', $link_class) .'" data-lity href="' . $url_link . '">' . $label_link . '</a>';
            $output .= '<a '. implode( ' ', $link_attrs) .' data-lity>';
                $output .= $label_link;
            $output .= '</a>';
        $output .= '</div>';

        return $output;
    }

    public function enqueue_scripts() {
        global $post;

        if ( has_shortcode( $post->post_content, 'wpb_lity' ) ) {
            wp_enqueue_script( 'wpblity-front-script', plugins_url('assets/js/lity.min.js', dirname(__FILE__)), array( 'jquery' ), WPB_LITY_VERSION );
            wp_enqueue_style( 'wpblity-front-style', plugins_url('assets/css/lity.min.css', dirname(__FILE__)), array(), WPB_LITY_VERSION );
            wp_enqueue_style( 'wpblity-theme-style', plugins_url('assets/css/wpb-lity.min.css', dirname(__FILE__)), array(), WPB_LITY_VERSION );
        }	    
    }

  }

  new WPBLity();

}
