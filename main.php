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

        add_action('plugins_loaded', array($this, 'myplugin_init'));
        add_filter('query_vars', array($this, 'addnew_query_vars' ));
    }

    /**
     * TODO.
     * filter is the name of variable you want to add.
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
    function myplugin_init()
    {
        load_plugin_textdomain('my-pluginname', FALSE, dirname(plugin_basename(__FILE__)).'/lib/languages/');
    }

    /**
     * This function is executed when the user activates the plugin
     *
     */
    function event_worker_activation()
    {
        wp_schedule_event(time(), 'testaus', 'my_hook');

        global $wpdb;

        $table_name = $wpdb->prefix . "worker_event_organizers";

        /*
         * We'll set the default character set and collation for this table.
         * If we don't do this, some characters could end up being converted 
         * to just ?'s when saved in our table.
         */
        $charset_collate = '';

        if (!empty($wpdb->charset))
        {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if (!empty( $wpdb->collate ))
        {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        $sql = "CREATE TABLE $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          name varchar(55) DEFAULT '' NOT NULL,
          address varchar(55) DEFAULT '' NOT NULL,
          phone varchar(55) DEFAULT '' NOT NULL,
          email varchar(55) DEFAULT '' NOT NULL,
          website varchar(55) DEFAULT '' NOT NULL,
          UNIQUE KEY id (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
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
