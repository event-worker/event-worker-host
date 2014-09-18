<?php

/**
 * Class for loading the templates.
 *
 * load the template for events archive, single event and add events page.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerTemplateLoader
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_filter('single_template', array($this, 'single_event_template'));
        add_filter('page_template', array($this, 'add_events_page_template'));
        add_filter('template_redirect', array($this, 'archive_event_template'));
        //add_filter('edit_post_link', array($this, 'myfunction2'));
        //add_filter('the_author', array($this, 'myfunction2'));
        //add_filter('the_title', array($this, 'myfunction'));
        //add_filter('the_content', array($this, 'myfunction'));
    }

    /** 
     * Get the archive template.
     *
     */
    function archive_event_template()
    {
        $dir = dirname( __FILE__ ) . '/../templates/archive-events.php';

        if (
             (!is_page() && is_post_type_archive("events")) ||
             (is_tax() && !is_page()) ||
             //(is_search()) ||
             (is_author())
           )
        {
            include_once($dir);
            exit();
        }
    }

    function myfunction()
    {   
        echo get_post_meta(get_the_ID(), 'event_location')[0];
    }

    function myfunction2()
    {   
        
    }

    /** 
     * Get the single event template.
     *
     * @param string $single_template the template
     *
     * @return string
     *
     */
    function single_event_template($single_template)
    {
        if (is_singular('events'))
        {
            $single_template = dirname( __FILE__ ) .
                               '/../templates/single-events.php';
        }
        return $single_template;
    }
    
    /** 
     * Get the add events page template.
     *
     * @param string $page_template the template
     *
     * @return string
     *
     */
    function add_events_page_template($page_template)
    {
        $current = get_option('add-event-page');

        if (is_page($current))
        {
            $page_template = dirname( __FILE__ ) .
                             '/../templates/add-events.php';
        }
        return $page_template;
    }
}
new WorkerTemplateLoader();

?>