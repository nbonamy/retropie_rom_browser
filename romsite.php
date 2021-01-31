<?php

// paths
define('BASE_ROM', '/home/pi/RetroPie/roms/');
define('BASE_IMAGE', '/home/pi/.emulationstation/downloaded_images/');
define('BASE_GAMELIST', '/home/pi/.emulationstation/gamelists/');
define('IGNORED_FILENAMES', array('tos.img'));
define('IGNORED_EXTS', array('srm', 'bin', 'jpg', 'conf'));

// global
$system = @$_GET['system'];

// load config
$config_filename = __DIR__.'/config.ini';
if (file_exists($config_filename)) {
  $config = parse_ini_file($config_filename);
} else {
  $config = array();
}

// some helper functions

function contains($haystack, $needle) {
  return stripos($haystack, $needle) !== FALSE;
}

function starts_with($haystack, $needle) {
  return stripos($haystack, $needle) === 0;
}

function trim_xml($text, $tag) {
  $text = str_replace("<$tag>", '', $text);
  $text = str_replace("</$tag>", '', $text);
  $text = trim($text);
  return $text;
}

function json_response($status, $content) {
  http_response_code($status);
  header('Content-Type: application/json');
  echo json_encode($content);
  exit();
}