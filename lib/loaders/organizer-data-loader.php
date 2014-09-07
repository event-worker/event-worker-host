<?php

require '../../../../../wp-blog-header.php';

$name = $_POST['id'];

$table = new Table('worker_event_organizers');
$record = $table->get_by( array('name' => $name) );

$arr = array($record);
echo json_encode($arr);

?>