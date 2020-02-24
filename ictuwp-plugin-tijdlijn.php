<?php
/*
// * ICTU / WP tijdlijn. 
// * 
// * Plugin Name:         ICTU / WP tijdlijn
// * Plugin URI:          https://github.com/ICTU/digitale-overheid-wordpress-plugin-timelineplugin/
// * Description:         Insert usable and accessible timelines in your post or page 
// * Version:             1.1.3
// * Version description: Stijlaanpassing tbv digitaleoverheid.nl anno 2020.
// * Author:              Paul van Buuren
// * Author URI:          https://wbvb.nl
// * License:             GPL-2.0+
// * 
// * Text Domain:         rhswp-timeline
// * Domain Path:         /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	define( 'RHSWP_TIMELINE_DEBUG', true );
}


if ( ! class_exists( 'RHSWP_timelineplugin' ) ) :

/**
 * Register the plugin.
 *
 * Display the administration panel, insert JavaScript etc.
 */
  class RHSWP_timelineplugin {
  
      /**
       * @var string
       */
      public $version = '1.1.3';
  
  
      /**
       * @var timeline
       */
      public $rhswptimeline = null;
  
      /**
       * Init
       */
      public static function init() {
      
        $rhswptimeline_this = new self();
      
      }
  
  
      /**
       * Constructor
       */
      public function __construct() {
  
          $this->define_constants();
          $this->includes();
          $this->setup_actions();
          $this->setup_filters();
          $this->setup_shortcode();
          $this->append_comboboxes();
  
  
      }
  
  
      /**
       * Define timeline constants
       */
      private function define_constants() {
  
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
  
        define( 'RHSWP_TIMELINE_VERSION',    $this->version );
        define( 'RHSWP_TIMELINE_FOLDER',     'rhswp-timeline' );
        define( 'RHSWP_TIMELINE_BASE_URL',   trailingslashit( plugins_url( RHSWP_TIMELINE_FOLDER ) ) );
        define( 'RHSWP_TIMELINE_ASSETS_URL', trailingslashit( RHSWP_TIMELINE_BASE_URL . 'assets' ) );
        define( 'RHSWP_TIMELINE_PATH',       plugin_dir_path( __FILE__ ) );
        define( 'RHSWP_CPT_TIMELINE',        "tijdlijncpt" );
        define( 'RHSWP_TIMELINE_JS_HANDLE',  'rhswp-timeline-frontend-js');
        define( 'RHSWP_TIMELINE_DO_DEBUG',   false );
  
      }
  
  
      /**
       * All timeline classes
       */
      private function plugin_classes() {
  
          return array(
              'RHSWP_timelineSystemCheck'  => RHSWP_TIMELINE_PATH . 'inc/rhswp-timeline.systemcheck.class.php',
          );
  
      }
  
  
      /**
       * Load required classes
       */
      private function includes() {
  
        $autoload_is_disabled = defined( 'RHSWP_TIMELINE_AUTOLOAD_CLASSES' ) && RHSWP_TIMELINE_AUTOLOAD_CLASSES === false;
        
        if ( function_exists( "spl_autoload_register" ) && ! ( $autoload_is_disabled ) ) {
          
          // >= PHP 5.2 - Use auto loading
          if ( function_exists( "__autoload" ) ) {
            spl_autoload_register( "__autoload" );
          }
          spl_autoload_register( array( $this, 'autoload' ) );
          
        } 
        else {
          // < PHP5.2 - Require all classes
          foreach ( $this->plugin_classes() as $id => $path ) {
            if ( is_readable( $path ) && ! class_exists( $id ) ) {
              require_once( $path );
            }
          }
        }
      }
  
      /**
       * filter for when the CPT is previewed
       */
      public function content_filter_for_preview( $content = '' ) {
        global $post;
      
        if ( ( RHSWP_CPT_TIMELINE == get_post_type() ) && ( is_single() ) ) {
  
          // lets go
          $this->register_frontend_style_script();
          return $content . '<p>' . __( 'Dit is een niet-functionele preview van deze tijdlijn. Je kunt deze het best bekijken door dit in een bericht te embedden', "rhswp-timeline" ) . '</p>' . $this->rhswp_timeline_do_output( $post->ID, true ) ;
        }
        else {
          return $content;
        }
        
      }
  
  
      /**
       * Autoload timeline classes to reduce memory consumption
       */
      public function autoload( $class ) {
  
          $classes = $this->plugin_classes();
  
          $class_name = strtolower( $class );
  
          if ( isset( $classes[$class_name] ) && is_readable( $classes[$class_name] ) ) {
              require_once( $classes[$class_name] );
          }
  
      }
  
  
      /**
       * Register the [timeline] shortcode.
       */
      private function setup_shortcode() {
  
          add_shortcode( 'timeline', array( $this, 'register_shortcode' ) );
          add_shortcode( 'timeline', array( $this, 'register_shortcode' ) ); // backwards compatibility
  
      }
  
  
      /**
       * Hook timeline into WordPress
       */
      private function setup_actions() {
  
          add_action( 'init', array( $this, 'register_post_type' ) );
          add_action( 'admin_footer', array( $this, 'admin_footer' ), 11 );
  
      }
  
  
      /**
       * Hook timeline into WordPress
       */
      private function setup_filters() {
  
          add_filter( 'media_buttons_context', array( $this, 'admin_insert_rhwwptimeline_button' ) );
  
        	// content filter
          add_filter( 'the_content', array( $this, 'content_filter_for_preview' ) );
  
      }
  
  
  
      /**
       * Register post type
       */
      public function register_post_type() {
  
      	$labels = array(
      		"name"                  => "tijdlijnen",
      		"singular_name"         => _x( "Timeline", "labels", "rhswp-timeline" ),
      		"menu_name"             => _x( "Timelines", "labels", "rhswp-timeline" ),
      		"all_items"             => _x( "All timelines", "labels", "rhswp-timeline" ),
      		"add_new"               => _x( "Add new", "labels", "rhswp-timeline" ),
      		"add_new_item"          => _x( "Add new timeline", "labels", "rhswp-timeline" ),
      		"edit"                  => _x( "Edit?", "labels", "rhswp-timeline" ),
      		"edit_item"             => _x( "Edit timeline", "labels", "rhswp-timeline" ),
      		"new_item"              => _x( "New timelines", "labels", "rhswp-timeline" ),
      		"view"                  => _x( "Show", "labels", "rhswp-timeline" ),
      		"view_item"             => _x( "View timeline", "labels", "rhswp-timeline" ),
      		"search_items"          => _x( "Search timeline", "labels", "rhswp-timeline" ),
      		"not_found"             => _x( "Not found", "labels", "rhswp-timeline" ),
      		"not_found_in_trash"    => _x( "No timelines found in trash", "labels", "rhswp-timeline" ),
      		"parent"                => _x( "Parent", "labels", "rhswp-timeline" ),
      		);
      
      	$args = array(
      		"labels"                => $labels,
          "label"                 => __( 'Timeline', '' ),
          "labels"                => $labels,
          "description"           => "",
          "public"                => true,
          "publicly_queryable"    => true,
          "show_ui"               => true,
          "show_in_rest"          => false,
          "rest_base"             => "",
          "has_archive"           => false,
          "show_in_menu"          => true,
          "exclude_from_search"   => false,
          "capability_type"       => "post",
          "map_meta_cap"          => true,
          "hierarchical"          => false,
          "rewrite"               => array( "slug" => RHSWP_CPT_TIMELINE, "with_front" => true ),
          "query_var"             => true,
      		"supports"              => array( "title" ),					
      		);
      	register_post_type( RHSWP_CPT_TIMELINE, $args );
      	
      	flush_rewrite_rules();
  
      }
  
  
      /**
       * Shortcode used to display timeline
       *
       * @return string HTML output of the shortcode
       */
      public function register_shortcode( $atts ) {
  
          extract( shortcode_atts( array(
              'id' => false,
              'restrict_to' => false
          ), $atts, 'timeline' ) );
  
  
          if ( ! $id ) {
              return false;
          }
  
          // we have an ID to work with
          $rhswptimeline = get_post( $id );
  
          // check the timeline is published and the ID is correct
          if ( ! $rhswptimeline || $rhswptimeline->post_status != 'publish' || $rhswptimeline->post_type != RHSWP_CPT_TIMELINE ) {
              return "<!-- timeline {$atts['id']} not found -->";
          }
  
          // lets go
          $this->register_frontend_style_script();
          
          return $this->rhswp_timeline_do_output($id);
  
      }
  
  
      /**
       * Add the help tab to the screen.
       */
      public function help_tab() {
  
        $screen = get_current_screen();
  
        // documentation tab
        $screen->add_help_tab( array(
          'id'      => 'documentation',
          'title'   => __( 'Documentation', "rhswp-timeline" ),
          'content' => "<p><a href='https://github.com/ICTU/digitale-overheid-wordpress-plugin-timelineplugin/documentation/' target='blank'>" . __( 'Timeline documentation', "rhswp-timeline" ) . "</a></p>",
          )
        );
      }
      
  
      /**
       * Register frontend styles
       */
      public function register_frontend_style_script() {
        if ( !is_admin() ) {
  
          $infooter = false;

          wp_enqueue_style( 'rhswp-timeline-frontend', RHSWP_TIMELINE_BASE_URL . 'css/rhswp-tijdlijn-frontend.css', array(), RHSWP_TIMELINE_VERSION, 'screen' );
  
          // don't add to any admin pages
          if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) && ( defined( 'RHSWP_TIMELINE_DEBUG' ) && RHSWP_TIMELINE_DEBUG ) ) {
  	        wp_enqueue_script( RHSWP_TIMELINE_JS_HANDLE, RHSWP_TIMELINE_ASSETS_URL . 'js/timeliner-2018.12.19.rijksoverheid.js', array( 'jquery' ), RHSWP_TIMELINE_VERSION, $infooter );
          } else {
  	        wp_enqueue_script( RHSWP_TIMELINE_JS_HANDLE, RHSWP_TIMELINE_ASSETS_URL . 'js/min/timeliner-2018.12.19.rijksoverheid-min.js', array( 'jquery' ), RHSWP_TIMELINE_VERSION, $infooter );
          }
  
          
          $this->localize_frontend_scripts();
  
        }
  
      }
  
      /**
       * Localise admin script
       */
      public function localize_frontend_scripts() {
  
          wp_localize_script( RHSWP_TIMELINE_JS_HANDLE, 'timeline', array(
                  'toggle_close'  => __( 'Section collapse',  "rhswp-timeline" ),
                  'toggle_open'   => __( 'Section open',      "rhswp-timeline" ),
              )
          );
  
      }
  
      
  
  
      /**
       * Register admin-side styles
       */
      public function register_admin_styles() {
  
          wp_enqueue_style( 'timeline-admin-styles', RHSWP_TIMELINE_BASE_URL . 'css/rhswp-tijdlijn-admin.css', false, RHSWP_TIMELINE_VERSION );
  
          do_action( 'RHSWP_TIMELINE_register_admin_styles' );
  
      }
  
  
      /**
       * Register admin JavaScript
       */
      public function register_admin_scripts() {
  
          // media library dependencies
          wp_enqueue_media();
  
          // plugin dependencies
          wp_enqueue_script( 'jquery-ui-core', array( 'jquery' ) );
  //        wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery', 'jquery-ui-core' ) );
  
          wp_dequeue_script( 'link' ); // WP Posts Filter Fix (Advanced Settings not toggling)
          wp_dequeue_script( 'ai1ec_requirejs' ); // All In One Events Calendar Fix (Advanced Settings not toggling)
  
          $this->localize_admin_scripts();
  
          do_action( 'RHSWP_TIMELINE_register_admin_scripts' );
  
      }
  
  
      /**
       * Localise admin script
       */
      public function localize_admin_scripts() {
  
          wp_localize_script( 'timeline-admin-script', 'timeline', array(
  						'url'               => __( "URL", "rhswp-timeline" ),
  						'new_window'        => __( "New Window", "rhswp-timeline" ),
  						'confirm'           => __( "Are you sure?", "rhswp-timeline" ),
  						'ajaxurl'           => admin_url( 'admin-ajax.php' ),
  						'resize_nonce'      => wp_create_nonce( 'rhswp_timeline_resize' ),
  						'add_timeline_nonce'   => wp_create_nonce( 'rhswp_timeline_add_timeline' ),
  						'change_timeline_nonce'=> wp_create_nonce( 'rhswp_timeline_change_timeline' ),
  						'iframeurl'         => admin_url( 'admin-post.php?action=rhswp_timeline_preview' ),
  					)
  				);
  
      }
  
      //========================================================================================================
      /**
       * Output the HTML
       */
  		public function rhswp_timeline_do_output( $timelineid, $dopreview = false ) {
  			
  		  $timeline_id                       = 'timeline-' . $timelineid;
  		  $titlestring                       = get_the_title( $timelineid );
  		  $uniqueid                          = $this->getuniqueid( $timelineid );    
  		  $timeline_introduction             = '&nbsp;';
  		  $mainitemcounter                   = 0;
  			$theid                             = RHSWP_CPT_TIMELINE . '_' . $timelineid;
//  			$ariahidden 											 = ' tabindex="-1" aria-expanded="false" aria-hidden="true"';
  			$ariahidden 											 = '';
  			$timelineMajorIntro								 = 'timelineMajorIntro';
  			$timelineMinor								 		 = 'timelineMinor';
  			$cssstatus												 = '';
  		  
  		  if ( $dopreview ) {
  				$ariahidden 										 = '';
  				$timelineMajorIntro 						 = 'timelineMajorIntro_test';
  				$timelineMinor 						 			 = 'timelineMinor_test';
  				$cssstatus											 = ' preview';
  		  }
  		
  		  if ( function_exists( 'get_field' ) ) {
  		    $timeline_introduction           = get_field( 'timeline_introduction', $timelineid );
  		  }
  		  
  		  $returnstring = '<div class="timeline-main' . $cssstatus . '">';
  		  
  		  $returnstring .= '<div class="timeline-introduction"><h2>' . esc_html( $titlestring ) . '</h2>';
  		  
  		  if ( $timeline_introduction ) {
  		    $returnstring .= '<p>' . $timeline_introduction . '</p>';
  		  }
  		  $returnstring .= '</div>';
  		    
  		  if( have_rows( 'timeline_items', $timelineid ) ) {
  			  
  				if ( ! $dopreview ) {
  				  $returnstring .= '<div class="timelineToggle"> <button>tekst</button> </div>';
  				}		
  				
  		    while( have_rows('timeline_items', $timelineid ) ) : the_row();
  
  		      $mainitemcounter++;
  
  		      // vars
  		      $majortimeline_title = get_sub_field('timeline_item_title');
  		      $majortimeline_intro = get_sub_field('timeline_item_intro');
  		
  		      $returnstring .= '<div class="timelineMajor">';
  					$returnstring .= '  <h3 class="timelineMajorMarker">';
  					if ( ! $dopreview ) {
  			      $returnstring .= '<a href="#" tabindex="0">';
  		      }
  		      $returnstring .= '<span>' . esc_html( $majortimeline_title ) . '</span>';
  					if ( ! $dopreview ) {
  			      $returnstring .= '</a>';
  		      }
  		      $returnstring .= '</h3>';
  		      
  		      $returnstring .= '  <div class="majorEvent"' . $ariahidden . '>';
  		
  		      if ( $majortimeline_intro ) {
  		        $returnstring .= '    <div class="' . $timelineMajorIntro . '">' . $majortimeline_intro . '</div>';
  		      }
  		
  		      if( have_rows('timeline_item_subitems', $timelineid ) ) {
  
  					  $subitemcounter                   = 0;
  		
  		        while( have_rows('timeline_item_subitems', $timelineid ) ) : the_row();
  		
  		          $subitemcounter++;
  		
  		          $sub_item_title     = get_sub_field('timeline_item_subitem_title');
  		          $sub_item_text      = get_sub_field('timeline_item_subitem_text');
  		          $sub_item_title_id  = sanitize_title( $uniqueid . '-' . $sub_item_title ) . '-' . $mainitemcounter . '-' . $subitemcounter;
  		          $sub_item_text_id   = $sub_item_title_id . '-' . $mainitemcounter . '-' . $subitemcounter;
  		
  		          $returnstring .= '    <div class="'. $timelineMinor . '">';
  		          $returnstring .= '      <div class="timelineEventHead">';
  
  							if ( ( ! $dopreview ) && $sub_item_text ) {
  								$returnstring .= '<a href="#" tabindex="0" aria-controls="' . $sub_item_text_id . '">';
  							}
  							else {
  								$returnstring .= '<div class="item-zonder-tekst">&nbsp;</div>';
  							}
  
  							$returnstring .= '<h4 id="' . $sub_item_title_id . '"> <span>' . esc_html( $sub_item_title ) . '</span></h4>'; 
  
  							if ( ( ! $dopreview ) && $sub_item_text ) {
  								$returnstring .= '</a>'; 
  							}
  							
  							$returnstring .= '</div>'; 

                if ( $sub_item_text ) {
    		          $returnstring .= '      <div id="' . $sub_item_text_id . '" role="region" ' . $ariahidden . ' class="timelineEvent" aria-labelledby="' . esc_html( $sub_item_title_id ) . '">' . $sub_item_text . '</div>'; 
                }  
                
  		          $returnstring .= '    </div>'; //  class="timelineMinor"
  		
  		        endwhile;
  		
  		      }          
  
  		      $returnstring .= '  </div>'; //  class="majorEvent"
  		      $returnstring .= '</div>'; //  class="timelineMajor"
  		      
  		    endwhile;
  		
  				if ( ! $dopreview ) {
//  			    $returnstring .= '<div class="timelineToggle"> <a href="#" class="expandAll" tabindex="0">' . __( 'Section open',      "rhswp-timeline" ) . '</a> </div>';

  				  $returnstring .= '<div class="timelineToggle"> <button>tekst</button> </div>';

  			  }         
  			   
  		  }          
  		
  		  $returnstring .= '</div>';
  		
  		  return $returnstring;
  		}
  
      //========================================================================================================
  
      private function get_stored_values( $timelineid, $postkey, $defaultvalue = '' ) {
  
        if ( RHSWP_TIMELINE_DO_DEBUG ) {
          $returnstring = $defaultvalue;
        }
        else {
          $returnstring = '';
        }
  
        $temp = get_post_meta( $timelineid, $postkey, true );
        if ( $temp ) {
          $returnstring = $temp;
        }
        
        return $returnstring;
      }
  
      //========================================================================================================
      
      public function getuniqueid( $timeline_id ) {
        
        global $post;
        
        return 'timeline-' . $timeline_id . '-post-' . $post->ID;    
      
      }
  
      //========================================================================================================
      
      public function append_comboboxes() {
      
        if( function_exists('acf_add_local_field_group') ):
        
        acf_add_local_field_group(array(
        	'key' => 'group_5a95987117a26',
        	'title' => _x( "Fields for timeline", "ACF-labels", "rhswp-timeline" ),
        	'fields' => array(
        		array(
        			'key' => 'field_5a9598d291372',
        			'label' => _x( "Introduction", "ACF-labels", "rhswp-timeline" ),
        			'name' => 'timeline_introduction',
        			'type' => 'textarea',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'default_value' => '',
        			'placeholder' => '',
        			'maxlength' => '',
        			'rows' => '',
        			'new_lines' => '',
        		),
        		array(
        			'key' => 'field_5a95a69b2cd46',
        			'label' => _x( "Timeline", "ACF-labels", "rhswp-timeline" ),
        			'name' => 'timeline_items',
        			'type' => 'repeater',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'collapsed' => 'field_5a95a6b42cd47',
        			'min' => 0,
        			'max' => 0,
        			'layout' => 'row',
        			'button_label' => _x( "Add item", "ACF-labels", "rhswp-timeline" ),
        			'sub_fields' => array(
        				array(
        					'key' => 'field_5a95a6b42cd47',
        					'label' => _x( "Jaar", "ACF-labels", "rhswp-timeline" ),
        					'name' => 'timeline_item_title',
        					'type' => 'text',
        					'instructions' => '',
        					'required' => 0,
        					'conditional_logic' => 0,
        					'wrapper' => array(
        						'width' => '',
        						'class' => '',
        						'id' => '',
        					),
        					'default_value' => '',
        					'placeholder' => '',
        					'prepend' => '',
        					'append' => '',
        					'maxlength' => '',
        				),
        				array(
        					'key' => 'field_5a95c124067da',
        					'label' => _x( "Toelichting", "ACF-labels", "rhswp-timeline" ),
        					'name' => 'timeline_item_intro',
        					'type' => 'wysiwyg',
        					'instructions' => '',
        					'required' => 0,
        					'conditional_logic' => 0,
        					'wrapper' => array(
        						'width' => '',
        						'class' => '',
        						'id' => '',
        					),
        					'default_value' => '',
        					'tabs' => 'all',
        					'toolbar' => 'full',
        					'media_upload' => 1,
        					'delay' => 0,
        				),
        				array(
        					'key' => 'field_5a95c3cd2159d',
        					'label' => _x( "Datums", "ACF-labels", "rhswp-timeline" ),
        					'name' => 'timeline_item_subitems',
        					'type' => 'repeater',
        					'instructions' => '',
        					'required' => 0,
        					'conditional_logic' => 0,
        					'wrapper' => array(
        						'width' => '',
        						'class' => '',
        						'id' => '',
        					),
        					'collapsed' => 'field_5a95c3f12159e',
        					'min' => 0,
        					'max' => 0,
        					'layout' => 'row',
        					'button_label' => _x( "Item toevoegen", "ACF-labels", "rhswp-timeline" ),
        					'sub_fields' => array(
        						array(
        							'key' => 'field_5a95c3f12159e',
        							'label' => _x( "Item (datum)", "ACF-labels", "rhswp-timeline" ),
        							'name' => 'timeline_item_subitem_title',
        							'type' => 'text',
        							'instructions' => '',
        							'required' => 0,
        							'conditional_logic' => 0,
        							'wrapper' => array(
        								'width' => '',
        								'class' => '',
        								'id' => '',
        							),
        							'default_value' => '',
        							'placeholder' => '',
        							'prepend' => '',
        							'append' => '',
        							'maxlength' => '',
        						),
        						array(
        							'key' => 'field_5a95c40c2159f',
        							'label' => _x( "Uitleg bij item", "ACF-labels", "rhswp-timeline" ),
        							'name' => 'timeline_item_subitem_text',
        							'type' => 'wysiwyg',
        							'instructions' => '',
        							'required' => 0,
        							'conditional_logic' => 0,
        							'wrapper' => array(
        								'width' => '',
        								'class' => '',
        								'id' => '',
        							),
        							'default_value' => '',
        							'tabs' => 'all',
        							'toolbar' => 'full',
        							'media_upload' => 1,
        							'delay' => 0,
        						),
        					),
        				),
        			),
        		),
        	),
        	'location' => array(
        		array(
        			array(
        				'param' => 'post_type',
        				'operator' => '==',
        				'value' => RHSWP_CPT_TIMELINE,
        			),
        		),
        	),
        	'menu_order' => 0,
        	'position' => 'normal',
        	'style' => 'default',
        	'label_placement' => 'top',
        	'instruction_placement' => 'label',
        	'hide_on_screen' => array(
        		0 => 'permalink',
        		1 => 'the_content',
        		2 => 'excerpt',
        		3 => 'custom_fields',
        		4 => 'discussion',
        		5 => 'comments',
        		6 => 'format',
        		7 => 'page_attributes',
        		8 => 'featured_image',
        	),
        	'active' => 1,
        	'description' => '',
        ));
        
        endif;
        
      }    
  
      //========================================================================================================
      
  
  
  
      /**
       * Check our WordPress installation is compatible with timeline
       */
      public function do_system_check() {
  
          $systemCheck = new RHSWP_timelineSystemCheck();
          $systemCheck->check();
  
      }
  
  
  
      /**
       *
       */
      public function update_timeline() {
  
          check_admin_referer( "rhswp_timeline_update_timeline" );
  
          $capability = apply_filters( 'rhswp_timeline_capability', 'edit_others_posts' );
  
          if ( ! current_user_can( $capability ) ) {
              return;
          }
  
          $rhswp_timeline_id = absint( $_POST['RHSWP_TIMELINE_id'] );
  
          if ( ! $rhswp_timeline_id ) {
              return;
          }
  
          // update settings
          if ( isset( $_POST['settings'] ) ) {
  
              $new_settings = $_POST['settings'];
  
              $old_settings = get_post_meta( $rhswp_timeline_id, 'RHSWP_TIMELINE_settings', true );
  
              // convert submitted checkbox values from 'on' or 'off' to boolean values
              $checkboxes = apply_filters( "RHSWP_TIMELINE_checkbox_settings", array( 'noConflict', 'fullWidth', 'hoverPause', 'links', 'reverse', 'random', 'printCss', 'printJs', 'smoothHeight', 'center', 'carouselMode', 'autoPlay' ) );
  
              foreach ( $checkboxes as $checkbox ) {
                  if ( isset( $new_settings[$checkbox] ) && $new_settings[$checkbox] == 'on' ) {
                      $new_settings[$checkbox] = "true";
                  } else {
                      $new_settings[$checkbox] = "false";
                  }
              }
  
              $settings = array_merge( (array)$old_settings, $new_settings );
  
              // update the timeline settings
              update_post_meta( $rhswp_timeline_id, 'RHSWP_TIMELINE_settings', $settings );
  
          }
  
          // update timeline title
          if ( isset( $_POST['title'] ) ) {
  
              $timeline = array(
                  'ID' => $rhswp_timeline_id,
                  'post_title' => esc_html( $_POST['title'] )
              );
  
              wp_update_post( $timeline );
  
          }
  
          // update individual timeline
          if ( isset( $_POST['attachment'] ) ) {
  
              foreach ( $_POST['attachment'] as $timeline_id => $fields ) {
                  do_action( "RHSWP_TIMELINE_save_{$fields['type']}_timeline", $timeline_id, $rhswp_timeline_id, $fields );
              }
  
          }
  
      }
  
  
  
  
      /**
       * Get all timelines. Returns an array of 
       * published timelines.
       *
       * @param string $sort_key
       * @return an array of published timelines
       */
      public function get_all_timelines( $sort_key = 'date' ) {
  
          $rhswptimelines = array();
  
          // list the tabs
          $args = array(
              'post_type'         => RHSWP_CPT_TIMELINE,
              'post_status'       => 'publish',
              'orderby'           => $sort_key,
              'suppress_filters'  => 1, // wpml, ignore language filter
              'order'             => 'ASC',
              'posts_per_page'    => -1
          );
  
          $args = apply_filters( 'RHSWP_TIMELINE_get_all_timelines_args', $args );
  
          // WP_Query causes issues with other plugins using admin_footer to insert scripts
          // use get_posts instead
          $timelines = get_posts( $args );
  
          foreach( $timelines as $timeline ) {
  
              $rhswptimelines[] = array(
                  'title'   => $timeline->post_title,
                  'id'      => $timeline->ID
              );
  
          }
  
          return $rhswptimelines;
  
      }
  
  
  
  
      
  
  
      /**
       * Append the 'Add timeline' button to selected admin pages
       */
      public function admin_insert_rhwwptimeline_button( $context ) {
  
          $capability = apply_filters( 'rhswp_timeline_capability', 'edit_others_posts' );
  
          if ( ! current_user_can( $capability ) ) {
              return $context;
          }
  
          global $pagenow;
  
          $posttype = 'post';
  
          if ( isset( $_GET['post'] ) ) {
            
            $posttype = get_post_type( $_GET['post'] );
  
          }
          if ( isset( $_GET['post_type'] ) ) {
            
            $posttype = $_GET['post_type'];
  
          }
  
          $available_post_types = get_post_types();
          
          $allowed_post_types = array();
          
          // to do: possibility to exclude some post types to allow for the insert timeline button
          foreach ( $available_post_types as $available_post_type ) {
  					if ( defined( 'RHSWP_CPT_RIJKSVIDEO' ) && $available_post_type === RHSWP_CPT_RIJKSVIDEO ) {
  						continue;
  					}
            if ( $available_post_type !== RHSWP_CPT_TIMELINE ) {
              array_push( $allowed_post_types, $available_post_type );
            }
          }
  
          if ( ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) && ( in_array( $posttype, $allowed_post_types ) ) ) {
              $context .= '<a href="#TB_inline?&inlineId=choose-timeline-selector-screen" class="thickbox button" title="' .
                  __( "Select and insert timeline", "rhswp-timeline" ) .
                  '"><span class="wp-media-buttons-icon" style="background: url(' . RHSWP_TIMELINE_ASSETS_URL . 'images/icon-timeline.png); background-repeat: no-repeat; background-size: 16px 16px; background-position: center center;"></span> ' .
                  __( "Insert timeline", "rhswp-timeline" ) . '</a>';
          }
  
          return $context;
  
      }
  
  
      /**
       * Append the 'Choose timeline' thickbox content to the bottom of selected admin pages
       */
      public function admin_footer() {
  
          global $pagenow;
  
          // Only run in post/page creation and edit screens
          if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) {
              $rhswptimelines = $this->get_all_timelines( 'title' );
              ?>
  
              <script type="text/javascript">
                  jQuery(document).ready(function() {
                    jQuery('#insert_timeline').on('click', function() {
                      var id = jQuery('#timeline-select option:selected').val();
                      window.send_to_editor('[<?php echo RHSWP_CPT_TIMELINE ?> id=' + id + ']');
                      tb_remove();
                    })
                  });
              </script>
  
              <div id="choose-timeline-selector-screen" style="display: none;">
                  <div class="wrap">
                      <?php
  
                          if ( count( $rhswptimelines ) ) {
                              echo "<h3>" . __( "Select a timeline for your post", "rhswp-timeline" ) . "</h3>";
                              echo "<select id='timeline-select'>";
                              echo "<option disabled=disabled>" . __( "Select timeline", "rhswp-timeline" ) . "</option>";
                              foreach ( $rhswptimelines as $rhswptimeline ) {
                                  echo "<option value='{$rhswptimeline['id']}'>{$rhswptimeline['title']}</option>";
                              }
                              echo "</select>";
                              echo "<button class='button primary' id='insert_timeline'>" . __( "Select and insert timeline", "rhswp-timeline" ) . "</button>";
                          } else {
                              _e( "No timelines available", "rhswp-timeline" );
                          }
                      ?>
                  </div>
              </div>
  
              <?php
          }
      }
  
  
  
  	public function do_debug( $logline = '' ) {
  
  		$subject = '(' . getmypid() . ') ';
  
  		if ( true === WP_DEBUG ) {
  			if ( is_array( $logline ) || is_object( $logline ) ) {
  				error_log( $subject . ' - ' .  print_r( $logline, true ) );
  			}
  			else {
  				error_log( $subject . ' - ' .  $logline );
  			}
  		}
  	}
  
  
  
  }

endif;

//========================================================================================================

/**
 * Initialise translations
 */
 
function rhswp_timeline_load_plugin_textdomain() {

	load_plugin_textdomain( "rhswp-timeline", false, basename( dirname( __FILE__ ) ) . '/languages/' );

}

//========================================================================================================

add_action( 'plugins_loaded', array( 'RHSWP_timelineplugin', 'init' ), 10 );

//add_action( 'plugins_loaded', array( 'RHSWP_timelineplugin', 'rhswp_timeline_load_plugin_textdomain' ), 88 );


add_action( 'plugins_loaded', 'rhswp_timeline_load_plugin_textdomain' );

