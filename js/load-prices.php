<?php
require_once('../../../../wp-config.php');

$prices = [];

$prices['summer_price'] = get_option('summer_price');
$prices['late_price'] = get_option('late_price');
$prices['low_price'] = get_option('low_price');

echo json_encode($prices);
