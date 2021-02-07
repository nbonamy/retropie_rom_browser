<?php

// paths
define('BASE_ROM', '/home/pi/RetroPie/roms/');
define('BASE_IMAGE', '/home/pi/.emulationstation/downloaded_images/');
define('BASE_GAMELIST', '/home/pi/.emulationstation/gamelists/');

// valid/ignored stuff: from https://retropie.org.uk/about/systems/
define('IGNORED_FILENAMES', array('tos.img'));
define('ROM_EXTS', array(
  '32x', 'a26', 'a52', 'a78', 'adf', 'asc', 'atr', 'bas', 'bat', 'bin', 'bz2',
  'cas', 'cbn', 'ccc', 'cdi', 'col', 'com', 'cpc', 'crt', 'cso', 'ctg', 'ctr', 'cue',
  'd64', 'dcm', 'dmk', 'dsk', 'dump', 'exe', 'fig', 'g64', 'gam', 'gb', 'gba', 'gbc', 'gdi', 'gg', 'gz',
  'ima', 'img', 'int', 'ipf', 'iso', 'j64', 'jag', 'jvc', 'lnx', 'm3u', 'md', 'mdf', 'mgd', 'mgt', 'mx1', 'mx2',
  'n64', 'nds', 'nes', 'ngc', 'ngp', 'os9', 'pbp', 'pce', 'raw', 'rom',
  'sad', 'sbt', 'scl', 'sfc', 'sg', /*'sh', */'smc', 'smd', 'sms', 'sna', 'st', 'stx', 'swc', 'szx', 't64',
  'tap', 'toc', 'trd', 'tzx', 'udi', 'v64', 'vb', 'vdk', 'vec', 'wav', 'ws', 'wsc',
  'x64', 'xex', 'xfd', 'z', 'z2', 'z64', 'z80', 'zip', 'znx', 
));

// geographies
define('GEOGRAPHIES', array(
  'World' => 80,
  'Europe' => 60,
  'Euro' => 60,
  'France' => 60,
  'USA' => 40,
  'US' => 40,
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
  global $system;
  require('views/' . $view . '.php');
}
