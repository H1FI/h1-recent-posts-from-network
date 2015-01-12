<?php
    /*
    Plugin Name: Herc Recent Posts from Network
    Description: Displays recent posts from a network without slowing down the page load.
    Author: Todd D. Nestor - todd.nestor@gmail.com
    Version: 1.0
    License: GNU General Public License v3 or later
    License URI: http://www.gnu.org/licenses/gpl-3.0.html
    */
    
    /**
     * Class HerculesRecentPostsFromNetwork creates a widget that displays the 20 mos recent posts from the network.
     *
     * This widget does not search for the most recent posts, but instead any time someone on the network
     * publishes a post, that post is added to the beginning of a list (a list that gets cutoff after 20 entries)
     * that is used to populate this widget.  Posts are only added when they are first published.
     */
    class HerculesRecentPostsFromNetwork
    {

        /**
         * The Constructor function adds the function that pushes published posts to the list, as well as registers the widget.
         */
        function __construct()
        {

            add_action( 'publish_post', array( $this, 'AddPosttoHerculesRecentPostsFromNetwork' ) );

            add_action( 'widgets_init', array( $this, 'RegisterHerculesRecentPostsFromNetworkWidget' ) );
        }

        /**
         * This function actually adds the post to the list of network recent posts when it is published for the first time.
         *
         * If the content on the post is empty then the title will be 'Auto Draft' and so it is not added to the list
         * until it is published with content.  Once a post has made it to the list it is never added back to the beginning.
         * That is, if you update the post it will not come back on to the list, nor would it move to the front if it was
         * already on the list.
         */
        function AddPosttoHerculesRecentPostsFromNetwork()
        {
            global $post;

            if( $post->post_title == 'Auto Draft' )
                return;

            if( !get_post_meta($post->ID, 'published_once', true ) )
            {
                global $wpdb;

                $domains = array();
                $blog_id = get_current_blog_id();
                $rows = $wpdb->get_results( "SELECT * FROM {$wpdb->dmtable} WHERE `blog_id`=$blog_id ORDER BY id DESC LIMIT 0,1" );
                foreach( $rows as $key=>$val )
                {
                    $domains[] = 'http://' . $val->domain;
                }

                $orig_blogurl = ((get_option('home'))?get_option('home'):get_option('siteurl'));
                $mapped_blogurl = count( $domains ) > 0 ? $domains[0] : $orig_blogurl;


                $essential_post_data = array(
                    'title'         =>  $post->post_title,
                    'permalink'     =>  str_replace( $orig_blogurl, $mapped_blogurl, get_the_permalink( $post->ID ) ),
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
        function RegisterHerculesRecentPostsFromNetworkWidget()
        {
            if( is_super_admin() || !is_admin() )
                register_widget( 'HerculesRecentPostsFromNetworkWidget' );
        }

    }
    
    $hercules_recent_network_posts = new HerculesRecentPostsFromNetwork;

    /**
     * Class HerculesRecentPostsFromNetworkWidget builds the widget and all the necessary functions for it.
     *
     * This widget has no options, so it is really just registering the widget and constructing how it displays.
     */
    class HerculesRecentPostsFromNetworkWidget extends WP_Widget 
    {
    
        /**
         * This is how we start a new widget, it builds the parent object.
         */
        function HerculesRecentPostsFromNetworkWidget() 
        {
            // Instantiate the parent object
            parent::__construct( false, 'Recent Posts from the Network', array( 'description' => 'A widget that shows the most recent 30 posts from the entire network' ) );
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

            $latest_posts = get_site_option( 'network_latest_posts' );
            
            if( !is_array( $latest_posts ) )
            {
                $latest_posts = array();
            }
            
            echo $before_widget;
            echo $before_title;
            echo 'Recent Posts from the Network';
            echo $after_title;
            echo '<div class="hrpn-block">';
            echo '<ul class="hrpn-ul">';
            $counter = 1;
            if( count( $latest_posts ) > 0 )
            {
                foreach( $latest_posts as $key=>$val )
                {
                    if( !empty( $val['permalink']) && !empty( $val['title'] ) && $counter <= 30 )
                    {
                        $counter++;
                        echo '<li class="hrpn-li hrpn-clearfix">';
                        echo '<h3 class="hrpn-title">';
                        echo '<a target="_blank" href="' . $val['permalink'] . '" title="' . $val['title'] . '">' . $val['title'] . '</a>';
                        echo '</h3>';
                        echo '</li>';
                    }
                }
            }
            else
            {
                echo '<li class="hrpn-li hrpn-clearfix">';
                echo '<h3 class="hrpn-title">';
                    echo 'No posts to display.';
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
            // Save widget options
        }
    
        /**
         * If we give this widget options, then the form those options get set in would be created here.
         *
         * @param array $instance Wordpress passes this instance variable to the function.
         */
        function form( $instance ) 
        {
            // Output admin widget options form
        }
    }
    
?>