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
class WorkerSubmitShortcode
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_shortcode('worker_form', array($this, 'worker_form_shortcode'));
        add_action('save_post', array($this,'custom_save_page'));
        add_action( 'wp_footer', array($this, 'add_js_to_wp_footer' ));
        add_action( 'wp_ajax_view_site_description', array($this, 'view_site_description' ));
        add_action( 'wp_ajax_nopriv_view_site_description', array($this, 'view_site_description' ));
        add_action( 'wp_head', array($this, 'pluginname_ajaxurl'));
    }

function pluginname_ajaxurl() {
?>
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
}


    function view_site_description(){
    echo get_bloginfo( 'description', 'display' );
    die();
}

    function add_js_to_wp_footer(){ ?>
    <script type="text/javascript">
    jQuery('#view_site_description').click(function(){
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {"action": "view_site_description"},
            success: function(data){alert(data);}
        });
        return false;
    });
    </script>
<?php }

    /** 
     * The shorcode.
     *     
     */
    function worker_form_shortcode()
    {
        $worker_event_name = '';
        $worker_event_text = '';
        $worker_event_category = '';

        // The user needs to be logged in.
        if (!is_user_logged_in())
        {
            return '<p>' . __('You need to be logged in to post!') .'</p>';
        }

        if (isset($_POST['worker_form_create_event_submitted']) &&
            wp_verify_nonce($_POST['worker_form_create_event_submitted'],
            'worker_form_create_event') )
        {
            $worker_event_name = trim($_POST['worker_event_name']);
            $worker_event_text = trim($_POST['worker_event_text']);
            
            $worker_event_start_date = $_POST['worker_event_start_date'];
            $worker_event_end_date = $_POST['worker_event_end_date'];

            $worker_event_location = trim($_POST['worker_event_location']);
            $worker_event_location_name = trim($_POST['worker_event_location_name']);
            $worker_event_geolocation = trim($_POST['worker_event_geolocation']);

            // OPTIONAL 
            $worker_event_price = trim($_POST['worker_event_price']);
            $worker_event_website = trim($_POST['worker_event_website']);

            $worker_event_organizer = trim($_POST['worker_event_organizer']);
            $worker_event_organizer_address = trim($_POST['organizer_address']);

            // NEEDED
            if ($worker_event_name != '' &&
                $worker_event_text != '' &&
                $worker_event_start_date != '' &&
                $worker_event_end_date != '' &&
                $worker_event_location != '')
            {
                $event_data = array(
                    'post_title' => $worker_event_name,
                    'post_content' => $worker_event_text,
                    'post_status' => 'pending',
                    'post_type' => 'events'
                );

                // ADD TO DATABASE
                if ($event_id = wp_insert_post($event_data))
                {
                    if (!empty($_POST["worker_event_category"]))
                    {
                        $cat_ids = $_POST['worker_event_category'];

                        $cats = null;
                        $names = null;
                        $temp = 0;

                        if ($cat_ids)
                        {
                            foreach ($cat_ids as $c)
                            {
                                $cats[] = get_term($term_id = $c, $taxonomy_name ='event_category');
                                $names[] = $cats[$temp]->name;
                                $temp++;
                            }
                        }

                        wp_set_object_terms($event_id,  $names, 'event_category');
                    }

                    update_post_meta($event_id,
                                     'event_start_date',
                                      $worker_event_start_date);
                   
                    update_post_meta($event_id,
                                     'event_end_date',
                                      $worker_event_end_date);

                    $order = new WorkerFormatDate($worker_event_start_date);
                    $only_digits = $order->$worker_event_start_date;

                    $end_order = new WorkerFormatDate($worker_event_end_date);
                    $only_digits2 = $end_order->$worker_event_end_date;

                    update_post_meta($event_id,
                                     'event_start_order',
                                     $only_digits);

                    update_post_meta($event_id,
                                     'event_end_order',
                                     $only_digits2);

                    update_post_meta($event_id,
                                     'event_location',
                                     $worker_event_location);

                    update_post_meta($event_id,
                                     'event_location_name',
                                     $worker_event_location_name);

                    update_post_meta($event_id,
                                     'event_geolocation',
                                     $worker_event_geolocation);

                    update_post_meta($event_id,
                                     'event_price',
                                     $worker_event_price);

                    update_post_meta($event_id,
                                     'event_website',
                                     $worker_event_website);

                    $organizer_data = Array(
                        'address' => trim($_POST['organizer_address']),
                        'phone' => trim($_POST['organizer_phone']),
                        'email' => trim($_POST['organizer_email']),
                        'website' => trim($_POST['organizer_website'])
                    );

                    update_post_meta($event_id,
                                     'event_organizer',
                                     $worker_event_organizer);

                    update_post_meta($event_id,
                                     'event_organizer_data',
                                     $organizer_data);

                    $temp = array('name' => $worker_event_organizer) + $organizer_data;
                    $table = new Table('worker_event_organizers');
                    $names = $table->get_all();

                    $check = true;

                    foreach ($names as $name)
                    {
                        if ($name->name === $worker_event_organizer)
                        {
                            $check = false;
                        }                
                    }
                    if ($check === true)
                    {
                        $table->insert($temp);
                    }

                    echo '<p>' . __('Event created and awaiting moderation!') .
                         '</p>';
                }
            }
            else
            {
                echo __('Event NOT saved! Fields are empty!') . '<br><br>';
            }
        }

        // Show the submit form.
        echo $this->worker_get_create_event_form($worker_event_category);
    }

    /** 
     * Get the submit form.
     *
     * @param string $worker_event_category The default category.
     *
     * @return string
     */
    function worker_get_create_event_form($worker_event_category = 0)
    {
        $out = '<div class="eventdivcontainer" align="center"><a id="view_site_description" href="javascript:void(0);">View Our Site Description</a>';

        $out .= '<form id="create_event_form" method="post" action="">';

        $out .= '<table width=100%;>
                    <tr>
                      <td class="eventtablecontainer">' . __('NAME') . '</td>
                      <td class="eventtablecontainersecond"><input type="text" style="width:100%; id="worker_event_name" name="worker_event_name" value=""/></td>
                    </tr>
                    <tr>                    
                      <td class="eventtablecontainer">' . __('START DATE') . '</td>
                      <td class="eventtablecontainersecond"><input type="text" class="eventdate" style="width:100%;" id="worker_event_start_date" name="worker_event_start_date" value=""/></td>
                    </tr>
                    <tr>
                      <td class="eventtablecontainer">' . __('END DATE') . '</td>
                      <td class="eventtablecontainersecond"><input type="text" class="eventdate" style="width:100%;" id="worker_event_end_date" name="worker_event_end_date" value=""/></td>
                    </tr>
                    <tr>
                      <td class="eventtablecontainer">' . __('PRICE') . '</td>
                      <td class="eventtablecontainersecond"><input type="number" min="0" style="width:100%;" id="worker_event_price" name="worker_event_price" value="" onkeypress="return isNumberKey(event)"/></td> 
                    </tr>
                    <tr>
                      <td class="eventtablecontainersecond" colspan="2"><textarea id="worker_event_text" style="width:100%;height:100px;" name="worker_event_text" placeholder="EVENT DESCRIPTION"/></textarea></td>
                    </tr>
                    <tr>
                      <td class="eventtablecontainer">' . __('WEBSITE') . '</td>
                      <td class="eventtablecontainersecond"><input type="url" style="width:100%;" id="worker_event_website" name="worker_event_website" value="' . '' .'"/></td> 
                    </tr>
                    <tr>
                      <td class="eventtablecontainer">' . __('CATEGORY') . '</td>
                      <td class="eventtablecontainersecond">' . $this->worker_get_event_categories_dropdown('event_category', $worker_event_category) . '</td> 
                    </tr>
                    <tr>

                      <td class="eventtablecontainer">' . __('ORGANIZER') . '</td>' .
                      '<td class="eventtablecontainersecond">' .
                      '<input type="text" id="worker_event_organizer" name="worker_event_organizer" style="width:100%;" class="auto" value="' . wp_get_current_user()->display_name .'"/>' .
                      '<input type="text" id="organizer_address" name="organizer_address" placeholder="Address" style="width:50%;" value=""/>' .
                      '<input type="text" id="organizer_phone" name="organizer_phone" placeholder="Phone" style="width:50%;" value=""/>' .
                      '<input type="text" id="organizer_email" name="organizer_email" placeholder="E-mail" style="width:50%;" value=""/>' .
                      '<input type="text" id="organizer_website" name="organizer_website" placeholder="Website" style="width:50%;" value=""/></td>' .

                    '</tr>
                    <tr>
                      <td class="eventtablecontainer">' . __('LOCATION') . '</td>
                      <td class="eventtablecontainersecond"><input id="worker_event_location_name" name="worker_event_location_name" placeholder="Name" type="text" style="width:50%;"/><input placeholder="Address" type="text" style="width:50%;" id="worker_event_location" name="worker_event_location" value=""/></td>
                      <input type="hidden" id="worker_event_geolocation" name="worker_event_geolocation" value=""/>
                    </tr>
                 </table>
                 <div id="googleMap" style="align:center;width:100%;height:300px;"></div><br>';

        $out .= wp_nonce_field('worker_form_create_event', 'worker_form_create_event_submitted');
        $out .= '<input type="submit" id="worker_submit" name="worker_submit" value="Submit event"><br><br><br>';
       
        $wslh = new WorkerScriptLoaderHelper();
        $wslh->getLocation();

        $out .= '</form></div>';

        return $out;
    }

    /** 
     * Get the categories dropdown.
     *
     * @param string $taxonomy TODO
     * @param string $selected TODO
     *
     * @return string
     */
    function worker_get_event_categories_dropdown($taxonomy, $selected)
    {   
        $select_cats = wp_dropdown_categories(array(
            'taxonomy' => $taxonomy,
            'name' => 'worker_event_category',
            'selected' =>  $selected,
            'hide_empty' => 0,
            'echo' => 0));

        // Hack the dropdown to multiple select.
        $selected = str_replace("name='worker_event_category' id=",
                                "name='worker_event_category[]' multiple='multiple' id=",
                                $select_cats );

        return $selected;
    }

    /** 
     * Save the data.
     *
     * @param int $post_id the page id
     *
     */
    function custom_save_page($post_id)
    {
        $content_post = get_post($post_id);
        $content = $content_post->post_content;

        if( has_shortcode( $content, 'worker_form' ) )
        {
            /** 
             * Add the options to the database.
             *
             * If the content has a [worker_form] short code
             * this check returs true.
             *
             * @param string $option_name the option name
             * @param string $new_value the option value
             *
             */
            function add_page_options($option_name, $new_value)
            {
                // The option already exists, just update it.
                if (get_option($option_name) !== false)
                {
                    update_option($option_name, $new_value);
                }
                // The option hasn't been added yet. Add with $autoload set to 'no'.
                else
                {
                    $deprecated = null;
                    $autoload = 'no';
                    add_option($option_name, $new_value, $deprecated, $autoload);
                }
            }

            $option_name = 'add-event-page-ID';
            $new_value = get_the_ID();
            add_page_options($option_name,  $new_value);
            
            $option_name2 = 'add-event-page';
            $post = get_post($new_value);
            $slug = $post->post_name;
            add_page_options($option_name2, $slug);

            $option_name3 = 'add-event-page-title';
            add_page_options($option_name3, get_the_title($new_value));

            remove_action('save_post', array($this,'custom_save_page'));
        }
    }
}
new WorkerSubmitShortcode();

?>