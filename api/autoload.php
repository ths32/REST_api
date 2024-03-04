<?php
require_once __DIR__ . '/vendor/autoload.php';

// spl_autoload_register(function ($class) {
//     $class = str_replace('\\', '/', $class);
//     $class = str_replace('Api/', '', $class); // Remove 'Api/' from the class path
//     require_once __DIR__ . "/$class.php";
// });