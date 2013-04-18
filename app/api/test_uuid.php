<?php
require_once('lib_uuid.php');

$uuid = UUID::mint();
//$uuid->mint();
$str = $uuid->__get("string");
//$str = hello;
print $str;
?>