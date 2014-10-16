<?php
/*
Plugin Name: Event Worker Host
Plugin URI: https://github.com/event-worker/event-worker-host
Description: Manage events
Version: 1.0
License: GPLv2
Author: Janne Kähkönen
Author URI: http://koti.tamk.fi/~c1jkahko/
*/

/**
 * The init point of the app.
 *
 * Load the needed classes and translations. Also set the query vars.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerHostMain
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        require_once('lib/core.php');

        //include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        //if (is_plugin_active('event-worker-client/main.php'))
        //{
            //echo "HELLO";
            //die();
        //}

        //var_dump(class_exists('Slim'), class_exists('\\Slim\\Slim')); //false, true

        if (!class_exists('\\Slim\\Slim'))
        {
            require_once('lib/api/slim-helper.php');
            require_once('lib/api/api-routes.php');
        }

        require_once('lib/loaders/scripts-and-styles-loader.php');
        require_once('lib/loaders/page-template-loader.php');

        add_action('plugins_loaded', array($this, 'event_worker_init'));
        add_filter('query_vars', array($this, 'addnew_query_vars' ));
    }

    /**
     * Add query variable to filter the posts.
     *
     * @param string $vars the query variable.
     *
     */
    function addnew_query_vars($vars)
    {   
        $vars[] = 'filter';
        return $vars;
    }

    /**
     * Load the translations on plugin load.
     *
     */
    function event_worker_init()
    {
        load_plugin_textdomain('event-worker-translations', FALSE, dirname(plugin_basename(__FILE__)).'/lib/languages/');
    }
}
new WorkerHostMain();

?>