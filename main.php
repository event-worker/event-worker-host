<?php
/*
Plugin Name: Event Worker Host
Plugin URI: https://github.com/sugardrunk/event-worker-host
Description: Manage events
Version: 1.0
License: GPLv2
Author: Janne Kähkönen
Author URI: http://koti.tamk.fi/~c1jkahko/
*/

/**
 * The init point of the app.
 *
 * Load the needed classes and start the app.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerMainClass
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {   
        /* The activation hook is executed when the plugin is activated. */
        register_activation_hook(__FILE__,array($this,'event_worker_activation'));

        /* The deactivation hook is executed when the plugin is deactivated */
        register_deactivation_hook(__FILE__,array($this,'event_worker_deactivation'));

        /* We add a function of our own to the my_hook action. */
        add_action('my_hook', array($this,'generate_pdf'));

        require_once('lib/redirect.php');
        require_once('lib/core.php');
        require_once('lib/api/wp-slim-framework.php');
        require_once('lib/api-routes.php');
        require_once('lib/loaders/scripts-and-styles-loader.php');
        require_once('lib/loaders/page-template-loader.php');
        require_once('lib/loaders/feed-loader.php');

        add_action('plugins_loaded', array($this, 'event_worker_init'));
        add_filter('query_vars', array($this, 'addnew_query_vars' ));
    }

    /**
     * Add filter variable to filter posts
     *
     * @param TODO
     *
     */
    function addnew_query_vars($vars)
    {   
        $vars[] = 'filter';
        return $vars;
    }

    /**
     * TODO.
     *
     */
    function event_worker_init()
    {
        load_plugin_textdomain('event-worker-translations', FALSE, dirname(plugin_basename(__FILE__)).'/lib/languages/');
    }

    /**
     * This function is executed when the user activates the plugin
     *
     */
    function event_worker_activation()
    {
        wp_schedule_event(time(), 'hourly', 'my_hook');
    }

    /**
     * This function is executed when the user deactivates the plugin.
     *
     */
    function event_worker_deactivation()
    {
        wp_clear_scheduled_hook('my_hook');
    }

    /**
     * This is the function that is executed by the recurring action.
     *
     */
    function generate_pdf()
    {
        require('lib/pdf-generator.php');
    }
}
new WorkerMainClass();

?>
