<?php
require_once('../../../../wp-config.php');

global $wpdb;

$table = $wpdb->prefix . 'bookings';

$data = [];

$results = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id");

foreach($results as $result)
{
    $data[] = [
        'id' => $result->id,
        'title' => $result->status,
        'start' => $result->start_date,
        'end' => $result->end_date
    ];
}

echo json_encode($data);
