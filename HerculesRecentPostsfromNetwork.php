<?php
    /*
    Plugin Name: H1 Recent Posts from Network
    Description: Displays recent posts from a network without slowing down the page load. Forked from Hercules Recent Posts from Network
    Author: Todd D. Nestor - todd.nestor@gmail.com, Daniel Koskinen / H1
    Version: 1.0
    License: GNU General Public License v3 or later
    License URI: http://www.gnu.org/licenses/gpl-3.0.html
    */
    
    /**
     * Class H1_Recent_Posts_From_Network creates a widget that displays the 20 mos recent posts from the network.
     *
     * This widget does not search for the most recent posts, but instead any time someone on the network
     * publishes a post, that post is added to the beginning of a list (a list that gets cutoff after 20 entries)
     * that is used to populate this widget.  Posts are only added when they are first published.
     */
    class H1_Recent_Posts_From_Network
    {

        /**
         * The Constructor function adds the function that pushes published posts to the list, as well as registers the widget.
         */
        function __construct()
        {

            load_plugin_textdomain( 'hrpn', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

            add_action( 'publish_post', array( $this, 'Add_Post_To_H1_Recent_Posts_From_Network' ) );
            add_action( 'widgets_init', array( $this, 'Register_H1_Recent_Posts_From_Network_Widget' ) );
            
            $first_post = get_site_option( 'first_post' );
            $first_post = str_replace( "SITE_URL", esc_url( network_home_url() ), $first_post );
            $first_post = str_replace( "SITE_NAME", get_current_site()->site_name, $first_post );
            $this->first_post = $first_post;
        }

        /**
         * This function actually adds the post to the list of network recent posts when it is published for the first time.
         *
         * If the content on the post is empty then the title will be 'Auto Draft' and so it is not added to the list
         * until it is published with content.  Once a post has made it to the list it is never added back to the beginning.
         * That is, if you update the post it will not come back on to the list, nor would it move to the front if it was
         * already on the list.
         */
        function Add_Post_To_H1_Recent_Posts_From_Network()
        {
            global $post;

            if( $post->post_title == __( 'Auto Draft' ) )
                return;

            if( !get_post_meta($post->ID, 'published_once', true ) && ( $post->ID != 1 || strpos( $post->content, $this->first_post ) === false ) )
            {
                global $wpdb;

                $domains = array();
                $blog_id = get_current_blog_id();

                // Ignore main site posts
                if ( $blog_id = BLOG_ID_CURRENT_SITE )
                    return;

                $rows = $wpdb->get_results( "SELECT * FROM {$wpdb->dmtable} WHERE `blog_id`=$blog_id ORDER BY id DESC LIMIT 0,1" );
                foreach( $rows as $key=>$val )
                {
                    $domains[] = 'http://' . $val->domain;
                }

                $orig_blogurl = ((get_option('home'))?get_option('home'):get_option('siteurl'));
                $mapped_blogurl = count( $domains ) > 0 ? $domains[0] : $orig_blogurl;

                $thumbnail = get_the_post_thumbnail( $post->ID, 'medium', array( 'class'=>'hrpn-alignleft hrpn-thumb' ) );
                
                $essential_post_data = array(
                    'title'         =>  $post->post_title,
                    'permalink'     =>  str_replace( $orig_blogurl, $mapped_blogurl, get_the_permalink( $post->ID ) ),
                    'thumbnail'     =>  $thumbnail,
                    'date'          =>  $post->post_date
                );

                $current_recent_posts = get_site_option( 'network_latest_posts' );

                if( empty( $current_recent_posts ) )
                    $current_recent_posts = array();

                array_unshift( $current_recent_posts, $essential_post_data );
                $new_recent_posts = array_slice( $current_recent_posts, 0, 50 );

                update_site_option( 'network_latest_posts', $new_recent_posts );
                update_post_meta($post->ID, 'published_once', 'true');
            }
        }

        /**
         * This function registers the Network Recent Posts widget that is in a different class.
         */
        function Register_H1_Recent_Posts_From_Network_Widget()
        {
            if( is_super_admin() || !is_admin() )
                register_widget( 'H1_Recent_Posts_From_Network_Widget' );
        }

    }
    
    $hercules_recent_network_posts = new H1_Recent_Posts_From_Network;

    /**
     * Class H1_Recent_Posts_From_Network_Widget builds the widget and all the necessary functions for it.
     *
     * This widget has no options, so it is really just registering the widget and constructing how it displays.
     */
    class H1_Recent_Posts_From_Network_Widget extends WP_Widget
    {
    
        /**
         * This is how we start a new widget, it builds the parent object.
         */
        function H1_Recent_Posts_From_Network_Widget()
        {
            // Instantiate the parent object
            parent::__construct( false, 'H1 Recent Posts from the Network', array( 'description' => __( 'A widget that shows the most recent posts from the entire network', 'hrpn' ) ) );
        }
    
        /**
         * This function actually builds the widget that is displayed.
         *
         * @param array $args is the arguments that Wordpress sends it for this widget.
         * @param array $instance Wordpress gives us the instance, this just has to be here.
         */
        function widget( $args, $instance ) 
        {
            extract( $args, EXTR_SKIP );
    
            if ( is_numeric($widget_args) )
                $widget_args = array( ‘number’ => $widget_args );
            $widget_args = wp_parse_args( $widget_args, array( ‘number’ => -1 ) );
            extract( $widget_args, EXTR_SKIP );
            
            $instance = wp_parse_args( (array) $instance, self::get_defaults() );

            $latest_posts = get_site_option( 'network_latest_posts' );
            
            if( !is_array( $latest_posts ) )
            {
                $latest_posts = array();
            }
            
            $new_tab = $instance['new-tab'] != false ? ' target="_blank" ' : '';
            
            echo $before_widget;
            echo $before_title;
            echo $instance['title'];
            echo $after_title;
            echo '<div class="hrpn-block">';
            echo '<ul class="hrpn-ul">';
            $counter = 1;
            
            $max_items = !empty( $instance['limit'] ) ? $instance['limit'] : 5;
            
            if( count( $latest_posts ) > 0 )
            {
                foreach( $latest_posts as $key=>$val )
                {
                    if( !empty( $val['permalink']) && !empty( $val['title'] ) && $counter <= $max_items )
                    {
                        $counter++;
                        echo '<li class="hrpn-li hrpn-clearfix">';
                        
                        echo '<h3 class="hrpn-title">';
                        echo '<a ' . $new_tab . ' href="' . $val['permalink'] . '" title="' . $val['title'] . '">' . $val['title'] . '</a>';

                        if ( !empty( $val['date'] ) ) {
                            echo ' <span class="hrpn-date">('.  date( __( 'j.n.Y', 'hrpn' ), strtotime( $val['date'] ) ) . ')</span>';
                        }
                        echo '</h3>';
                        
                        if( !empty( $val['thumbnail'] ) && $instance['thumb'] != false )
                        {
                            echo '<a ' . $new_tab . ' href="' . $val['permalink'] . '" title="' . $val['title'] . '">' . $val['thumbnail'] . '</a>';
                        }
                        
                        echo '</li>';
                    }
                }
            }
            else
            {
                echo '<li class="hrpn-li hrpn-clearfix">';
                echo '<h3 class="hrpn-title">';
                    echo __( 'No posts to display.' , 'hrpn' );
                echo '</h3>';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
            echo $after_widget;
        }
    
        /**
         * If we ever add options, this is where we'd put the function to set those options.
         *
         * @param array $new_instance Wordpress gives us this variable.
         * @param array $old_instance Wordpress also gives us this one, we never manually call this function.
         */
        function update( $new_instance, $old_instance ) 
        {
            $instance = $old_instance;

            $instance['title']            = strip_tags( $new_instance['title'] );
            $instance['limit']            = (int)( $new_instance['limit'] );
            $instance['thumb']            = isset( $new_instance['thumb'] )     ? (bool) $new_instance['thumb'] : false;
            $instance['new-tab']          = isset( $new_instance['new-tab'] )   ? (bool) $new_instance['new-tab'] : false;

            return $instance;
        }
    
        /**
         * If we give this widget options, then the form those options get set in would be created here.
         *
         * @param array $instance Wordpress passes this instance variable to the function.
         */
        function form( $instance ) 
        {
            $instance = wp_parse_args( (array) $instance, self::get_defaults() );

            $id_prefix = $this->get_field_id('');
            ?>
            <div class="">

                <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                        Title
                    </label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
                </p>

                <p>
                    <label for="<?php echo $this->get_field_id( 'limit' ); ?>">
                        Number of posts to show
                    </label>
                    <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" step="1" min="0" max="50" value="<?php echo (int)( $instance['limit'] ); ?>" />
                </p>

                <?php if ( current_theme_supports( 'post-thumbnails' ) ) { ?>

                    <p>
                        <input id="<?php echo $this->get_field_id( 'thumb' ); ?>" name="<?php echo $this->get_field_name( 'thumb' ); ?>" type="checkbox" <?php checked( $instance['thumb'] ); ?> />
                        <label class="input-checkbox" for="<?php echo $this->get_field_id( 'thumb' ); ?>">
                            Display Thumbnail
                        </label>
                    </p>

                <?php } ?>
                
                <p>
                        <input id="<?php echo $this->get_field_id( 'new-tab' ); ?>" name="<?php echo $this->get_field_name( 'new-tab' ); ?>" type="checkbox" <?php checked( $instance['new-tab'] ); ?> />
                        <label class="input-checkbox" for="<?php echo $this->get_field_id( 'new-tab' ); ?>">
                            Open in new tab
                        </label>
                    </p>

            </div>
            <?php
        }

        /**
         * Render an array of default values.
         *
         * @return array default values
         */
        private static function get_defaults() {

            $defaults = array(
                'title'     => 'Recent Posts from the Network',
                'limit'     => 5,
                'thumb'     => true,
                'new-tab'   => true,
            );

            return $defaults;
        }
    }
    
?>