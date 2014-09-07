<?php

require '../../../../../wp-blog-header.php';

$table = new Table('worker_event_organizers');
$all_rows = $table->get_all();

$arr = array();

for ($i = 0; $i < count($all_rows); $i++)
{
    $arr[] = $all_rows[$i]->name;
}

echo json_encode($arr);

?>