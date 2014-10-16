<?php

/**
 * Class for adding or removing the menu items.
 *
 * Add or remove the navigation links.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerHostMenuItems
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_filter('wp_nav_menu_objects', array($this, 'menu_links'));

        add_filter('wp_list_pages_excludes',
                   array($this, 'exclude_from_wp_list_pages'));
    }

    /** 
     * Exclude event adding page if user is not logged in.
     *
     * @param array $objects The list of nav links.
     *
     * @return array
     */
    function menu_links($objects)
    {
        if (!is_user_logged_in())
        {
            for ($i = 1; $i <= count($objects); $i++)
            {
                if ($objects[$i]->title == 'Add Event')
                {
                    unset($objects[$i]);
                }
            }
            return $objects;
        }
        else
        {
            return $objects;
        }
    }

    /** 
     * Excludes the chosen pages.
     *
     * @param array $exclude_array The list of pages.
     *
     * @return array
     */
    function exclude_from_wp_list_pages($exclude_array)
    { 
        if (!is_user_logged_in())
        {
            $id = get_option("add-event-page-ID", 0);
            $exclude_array = $exclude_array + array($id);
        }
        return $exclude_array;
    }
}
new WorkerHostMenuItems();

?>