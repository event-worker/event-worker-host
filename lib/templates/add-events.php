<?php

/**
 * Class for add events page template.
 *
 * Load the page template.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerHostAddEventsPage
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        get_header();
        echo '<div align="center">';

        the_post();
        the_content();

        echo '</div>';
        //get_sidebar();

        get_footer();
    }
}
new WorkerHostAddEventsPage();

?>