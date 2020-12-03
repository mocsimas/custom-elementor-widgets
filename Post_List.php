<?php

namespace Elementor;



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



class Post_List extends Widget_Base {



   public function get_id() {

      return 'posts_list';

   }



   public function get_name(){

       return 'posts_list';

   }



   public function get_title() {

      return __( 'Įrašai', 'elementor-custom-element' );

   }



   public function get_icon() {

      return 'fas fa-list';

   }



   protected function _register_controls() {



      $this->add_control(

         'section_blog_posts',

         [

            'label' => __( 'Valdiklio nustatymai', 'elementor-custom-element' ),

            'type' => Controls_Manager::SECTION,

         ]

      );



      $this->add_control(

         'post_types',

         [

            'label'     => __( 'Įrašų tipai', 'elementor-custom-element' ),

            'type'      => Controls_Manager::SELECT,

            'default'   => 0,

            'section'   => 'section_blog_posts',

            'options'   => [

                0    => __('Visi', 'elementor-custom-element'),

                1    => __('Įrašai', 'elementor-custom-element'),

                2    => __('Puslapiai', 'elementor-custom-element'),

            ],

         ]

      );



      $this->add_control(

         'posts_per_page',

         [

            'label'     => __( 'Įrašų skaičius puslapyje', 'elementor-custom-element' ),

            'type'      => Controls_Manager::NUMBER,

            'section'   => 'section_blog_posts',

            'default'   => 1

         ]

      );

      $this->add_control(

         'posts_count',

         [

            'label'     => __( 'Įrašų skaičius', 'elementor-custom-element' ),

            'type'      => Controls_Manager::NUMBER,

            'section'   => 'section_blog_posts',

            'default'   => 1

         ]

      );

      $this->add_control(

         'columns_amount',

         [

            'label'     => __( 'Įrašų skaičius vienoje eilutėje', 'elementor-custom-element' ),

            'type'      => Controls_Manager::SELECT,

            'default'   => 2,

            'section'   => 'section_blog_posts',

            'options'   => [

                0 => 1,

                1 => 2,

                2 => 3,

                3 => 4,

            ],

         ]

      );



   }



   protected function render() {

      global $wp_query;

      $settings = $this->get_settings_for_display();

      $post_type = ! empty( $settings['post_types'] ) ? esc_html($settings['post_types']) : 0;

      $posts_count = ! empty( $settings['posts_count'] ) ? (int)esc_html($settings['posts_count']) : 3;

      $columns_amount = $settings['columns_amount'] != '' ? (int)esc_html($settings['columns_amount'])+1 : 3;

      $posts_per_page = ! empty( $settings['posts_per_page'] ) ? (int)esc_html($settings['posts_per_page']) : intval(get_option( 'posts_per_page' ));

      if($post_type == 1){

         $post_type = 'post';

      }else if($post_type == 2){

         $post_type = 'page';

      }else{

         $post_type = 'any';

      }

      $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

      $args = array(

         'numberposts'     => intval($posts_count),

         'post_types'      => $post_type,

         'orderby'         => 'date',

         'order'           => 'DESC',

         'paged'           => intval($paged),

         'posts_per_page'  => intval($posts_per_page),

      );



      $recent_posts = new \WP_Query($args);

      if($recent_posts->have_posts()){

      ?>



         <div class="sm-recent-posts-wrapper">



         <?php

            $rowCount = 0;

            $bootstrapColWidth = 12 / $columns_amount;

            while($recent_posts->have_posts()){

               $recent_posts->the_post();

               if($rowCount % $columns_amount == 0) { ?> <div class="row"> <?php } 

                  $rowCount++; ?>

                     <div class="sm-recent-post-card col-md-<?php echo $bootstrapColWidth; ?>">
                        <?php if(has_post_thumbnail(get_the_ID())){ ?>
                           <div class="sm-recent-post-image-container">
                              <figure>
                                 <img class="sm-recent-post-image" src="<?= get_the_post_thumbnail_url(get_the_ID()) ?>" alt="<?= get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', TRUE) ?>" title="<?= get_the_title(get_post_thumbnail_id()) ?>">
                              </figure>
                           </div>
                        <?php }?>

                        <h4 class="sm-recent-post-title"><a href="<?= get_the_permalink(get_the_ID()) ?>"><?= the_title(); ?></a></h4>

                        <div class="sm-recent-post-categories-container">
                           <span class="sm-recent-post-categories">
                              <?php 
                              $categories = wp_get_post_categories(get_the_ID(), array('fields' => 'all'));
                                 foreach($categories as $category_key => $category){
                                    if($category->term_id != 1){
                                       echo "<a href='".get_category_link($category->term_id)."'>{$category->name}</a>";
                                       if($categories[$category_key+1] && $categories[$category_key+1]->term_id != 1){
                                          echo ", ";
                                       }
                                    }
                                 }

                              ?>
                           </span>
                        </div>

                        <div class="sm-recent-post-excerpt"><?= the_excerpt(); ?></div>

                        <a href="<?php the_permalink(); ?>" class="sm-recent-post-button">Skaityti</a>

                     </div>

                  <?php if($rowCount % $columns_amount == 0) { ?> </div> <?php } 
            }?>

            </div>
            
            <?php if(!is_front_page()){ ?>

               <div class="posts-pagination">
                  <?php echo 
                  
                  paginate_links(array(
                     'current'    => max( 1, $paged ),
                     'total'      => $recent_posts->max_num_pages,
                     'show_all'   => true,
                     'prev_text'  => null,
                     'next_text'  => null,
                  ));

                  ?>
               </div>

            <?php }?>
            
         </div>

      <?php

         wp_reset_postdata();

      }

   }



   protected function content_template() {}



   public function render_plain_content( $instance = [] ) {}



}



Plugin::instance()->widgets_manager->register_widget_type(new Post_List());

