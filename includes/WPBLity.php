<?php

if ( !class_exists('WPBLity') ) {

  class WPBLity extends WPBakeryShortCode {

    function __construct() {
        add_action( 'init', array($this, 'create_shortcode'), 999 );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_shortcode( 'wpb_lity', array($this, 'render_shortcode') );
    }

    public function create_shortcode() {
      // stop all if WPB not enable
        if ( !defined('WPB_VC_VERSION') ) {
            return;
        }

        vc_map(array(
            'name' => esc_html__('Video Lightbox', 'nt-agricom'),
            'base' => 'wpb_lity',
            'description' => esc_html__('Open video as lightbox', 'nt-agricom'),
            'category' => esc_html__( 'Content', 'nt-agricom'),
            'params' => array(
                array(
                    'type' => 'attach_image',
                    'heading' => esc_html__('Video Poster', 'nt-agricom'),
                    'param_name' => 'poster'
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('URL Link', 'nt-agricom'),
                    'param_name' => 'url_link'
                )
            )
        ));
    }

    public function render_shortcode( $atts, $content, $tag ) {
        $atts = (shortcode_atts(array(
            'poster' => '',
            'url_link' => ''
        ), $atts));

        $content = wpb_js_remove_wpautop($content, true);
        $video_poster = wp_get_attachment_url( esc_html($atts['poster']),'full' );
        $url_link = esc_html($atts['url_link']);

        $output = '';
        $output .= '<div class="wpb-lity">';
        $output .= '<figure class="wpb-lity__image"><img src="' . esc_url( $video_poster ) . '"></figure>';
        $output .= '<a class="wpb-lity__link" data-lity href="' . esc_url( $url_link ) . '">Video Link</a>';
        $output .= '</div>';

        return $output;
    }

    public function enqueue_scripts() {
        global $post;

        if ( has_shortcode( $post->post_content, 'wpb_lity' ) ) {
            wp_enqueue_script( 'wpblity-front-script', plugins_url('assets/js/lity.min.js', dirname(__FILE__)), array( 'jquery' ), WPB_LITY_VERSION );
            wp_enqueue_style( 'wpblity-front-style', plugins_url('assets/css/lity.min.css', dirname(__FILE__)), array(), WPB_LITY_VERSION );
        }
	    
    }

  }

  new WPBLity();

}