<?php

// paths
define('BASE_ROM', '/home/pi/RetroPie/roms/');
define('BASE_IMAGE', '/home/pi/.emulationstation/downloaded_images/');
define('BASE_GAMELIST', '/home/pi/.emulationstation/gamelists/');

// ignored stuff
define('IGNORED_FILENAMES', array('tos.img'));
define('IGNORED_EXTS', array('txt', 'srm', 'bin', 'jpg', 'png', 'conf', 'xml', 'sqlite'));

// geographies
define('GEOGRAPHIES', array(
  'World' => 80,
  'Euro' => 60,
  'Europe' => 60,
  'France' => 60,
  'US' => 40,
  'USA' => 40,
  'UK' => 20,
  'Germany' => -10,
  'Spanish' => -10,
  'Hispanic' => -10,
  'Brazil' => -10,
  'Japan' => -20,
  'China' => -40,
  'Korea' => -40,
  'Asia' => -40,
));

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

function is_abs_path($path) {
  return starts_with($path, '/') || starts_with($path, '~');
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

function render_view($view, $data) {
  extract($data);
  require('views/' . $view . '.php');
}

function open_page($system) {
  render_view('header', array(
    'title' => 'ROMS' . ($system != NULL ? ' - '.strtoupper($system) : '')
  ));
}

function close_page($system) {
  render_view('footer', array(
    'system' => $system
  ));
}
