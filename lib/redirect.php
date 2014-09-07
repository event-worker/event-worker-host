<?php

/**
 * Class for adding the shortcode for adding events.
 *
 * Add a shortcode for add events page.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
function my_page_template_redirect()
{
    if(is_front_page() && is_page('events') )
    {
        wp_redirect( home_url( '/events/' ) );
        exit();
    }
}
add_action( 'template_redirect', 'my_page_template_redirect' );

?>