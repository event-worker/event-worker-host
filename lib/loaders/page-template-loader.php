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
class WorkerHostTemplateLoader
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_filter('page_template', array($this, 'add_events_page_template'));
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
new WorkerHostTemplateLoader();

?>