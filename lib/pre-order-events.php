<?php

/**
 * Class for ordering the posts.
 *
 * Order the posts.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerPreOrderPosts
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_filter('pre_get_posts', array($this,'custom_pre_get_posts'));
        add_filter('pre_get_posts', array($this, 'search_filter'));
    }

    /** 
     * Return the parsed time.
     *
     * @return string
     *
     */
    function parse_the_time()
    {   
        date_default_timezone_set('Europe/Helsinki');
        $today = new DateTime('NOW');
        $today = $today->format('YmdHi');

        return $today;
    }

    /** 
     * Order the posts by the event start date.
     *
     * @param object $query query object
     *
     * @return object
     */
    function search_filter($query)
    {
        if ($query->is_search)
        {
            $query->set('post_type', array('post', 'pages', 'events'));
            return $query;
        }
    }

    /** 
     * Order the posts by the event start date.
     *
     * @param object $query query object.
     *
     * @return object
     */
    function custom_pre_get_posts($query)
    {
        if ($query->is_main_query() && $query->is_post_type_archive("events"))
        {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query->set('post_type', 'events');
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', 'event_start_order');
            $query->set('paged', $paged);
            $query->set('order', 'ASC');
            if (isset($_GET['filter']) && $_GET['filter'] === 'today')
            {
                $meta_query = array(
                    array(
                        'key' => 'event_start_order',
                        'type' => 'numeric',
                        'value' => $this->parse_the_time(),
                        'compare' => '<='
                    )
                );
                $query->set( 'meta_query', $meta_query );
            }

            $args = array(
                'post_type'   => 'events',
                'post_status' => 'publish',
                'numberposts' => -1
            );

            $posts = get_posts($args);

            foreach ($posts as $post)
            {
                $compare = get_post_meta($post->ID, 'event_end_order');

                if ($compare[0] < $this->parse_the_time())
                {
                    $post = array('ID' => $post->ID, 'post_status' => 'draft');
                    wp_update_post($post);
                }
            }
        }

        if ($query->is_tax() && !$query->is_page())
        {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query->set('post_type', 'events');
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', 'event_start_order');
            $query->set('paged', $paged);
            $query->set('order', 'ASC');

            if (isset($_GET['filter']) && $_GET['filter'] === 'today')
            {
                $meta_query = array(
                    array(
                        'key' => 'event_start_order',
                        'type' => 'numeric',
                        'value' => $this->parse_the_time(),
                        'compare' => '<='
                    )
                );
                $query->set( 'meta_query', $meta_query );
                
            }
        }
        if ($query->is_author())
        {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query->set('post_type', 'events');
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', 'event_start_order');
            $query->set('paged', $paged);
            $query->set('order', 'ASC');

            if (isset($_GET['filter']) && $_GET['filter'] === 'today')
            {
                $meta_query = array(
                    array(
                        'key' => 'event_start_order',
                        'type' => 'numeric',
                        'value' => $this->parse_the_time(),
                        'compare' => '<='
                    )
                );
                $query->set( 'meta_query', $meta_query );
            }
        }

        remove_action('pre_get_posts', 'custom_pre_get_posts'); // run once
    }
}
new WorkerPreOrderPosts();

?>