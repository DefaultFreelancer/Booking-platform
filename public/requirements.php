<?php
$required_php_version = '7.1.3';

$requirements = array(
    "php_version" => ($required_php_version <= phpversion()) ? $required_php_version : false
);


$required_extensions = array("openssl", "pdo", "mbstring", "tokenizer", "xml", "ctype", "json", "curl");

foreach ($required_extensions as $extension){
    $requirements[$extension] = extension_loaded($extension);
}

return $requirements;
