Event Worker Host
=================

A WordPress plugin to add and manage Events.

<hr>
##1 Requirements
####1.1 Permalinks
The WordPress installation requires permalinks (Post name).

<hr>
##2 Install
####2.1 Install the plugin
Install the plugin to the `wp-content/plugins` folder.
####2.2 Set permalinks on
Set the permalinks to `Post name`.
####2.3 Activate the plugin
Activate the plugin from the `Plugins` menu.

<hr>
##3 Optional settings
####3.1 Events as a front page
Create an empty page with a slug `events` and set it as a `static front page`.
####3.2 Add events page
Create an empty page and add a shortcode `[worker_form]` to it. This will
allow the logged-in users to post new events without using the admin back-end
and the required user roles.
####3.3 Change API endpoint
Set the new API endpoint if needed in the `Event Worker Options`.
