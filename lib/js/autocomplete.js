jQuery(document).ready(function()
{
    jQuery.ajax(
    {
        type: 'POST',
        dataType : 'json',
        url: '../wp-content/plugins/event-worker/lib/loaders/organizer-loader.php',
        success: function(result)
        {
            //console.log(result);
            jQuery('.auto').autocomplete(
            {
                source: result,
                autoFocus: true,
                delay: 50,
                select: function(event, ui)
                {
                    var selectedObj = ui.item;

                    jQuery('#organizer_address').val("loading...");
                    jQuery('#organizer_phone').val("loading...");
                    jQuery('#organizer_email').val("loading...");
                    jQuery('#organizer_website').val("loading...");

                    jQuery.ajax(
                    {
                        type: 'POST',
                        dataType : 'json',
                        url: '../wp-content/plugins/event-worker/lib/loaders/organizer-data-loader.php',
                        data: {id: selectedObj.value},
                        success: function(result)
                        {
                            //console.log(result[0][0]);
                            jQuery('#organizer_address').val(result[0][0].address);
                            jQuery('#organizer_phone').val(result[0][0].phone);
                            jQuery('#organizer_email').val(result[0][0].email);
                            jQuery('#organizer_website').val(result[0][0].website);
                        
                        },
                    });

                }
            });

        },
    });
});