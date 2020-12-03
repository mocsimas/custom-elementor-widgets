<?php



/**

 * Plugin Name: Elementor papildinys

 * Description: Papildomas funkcionalumas, kuris praturtins Jūsų svetainę įvairias priedais.

 * Version: 1.0

 * Author: Simas Mockus

 */



if ( ! defined( 'ABSPATH' ) ) exit;



class Elementor_Init_Custom_Widgets {



   private static $instance = null;



   public static function get_instance() {

      if ( ! self::$instance )

         self::$instance = new self();

      return self::$instance;

   }



   public function init(){

      add_action( 'wp_enqueue_scripts', [ $this, 'widget_scripts' ] );

      add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) );

   }



   public function widgets_registered() {



      // We check if the Elementor plugin has been installed / activated.

      if(defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')){



         // We look for any theme overrides for this custom Elementor element.

         // If no theme overrides are found we use the default one in this plugin.



         $widget_file = 'plugins/elementor/my-widget.php';

         $template_file = locate_template($widget_file);

         if ( !$template_file || !is_readable( $template_file ) ) {

            $template_file = plugin_dir_path(__FILE__).'Post_List.php';

         }

         if ( $template_file && is_readable( $template_file ) ) {

            require_once $template_file;

         }

      }

   }

   public function widget_scripts(){
      wp_register_style('sm-custom-elementor-widgets', plugins_url('assets/css/cew-style.css', __FILE__));
      wp_enqueue_style('sm-custom-elementor-widgets');
   }

}



Elementor_Init_Custom_Widgets::get_instance()->init();