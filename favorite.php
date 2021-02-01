
<?php

// base
require_once('romsite.php');
require_once('gamelist.php');

// open gamelist
$gamelist = get_gamelist_filename($system);
if (!file_exists($gamelist)) {
  json_response(404, array(
    'error' => 'Gamelist not found',
  ));
}
if (!is_writable($gamelist)) {
  json_response(403, array(
    'error' => 'Gamelist not writeable',
  ));
}

// read file and add favorite line
$found = FALSE;
$ingame = FALSE;
$unfaved = FALSE;
$lines = array();
foreach (file($gamelist) as $line) {

  if ($ingame === FALSE) {

    if (contains($line, '<path>') && contains($line, htmlentities($_GET['filename']))) {
      $found = TRUE;
      $ingame = TRUE;
    }

  } else {

    if (contains($line, '<favorite>')) {
      $unfaved = TRUE;
      $line = NULL;
    } else if (contains($line, '</game>')) {
      if ($unfaved === FALSE) {
        $lines[] = '    <favorite>true</favorite>'.PHP_EOL;
      }
      $ingame = FALSE;
    }

  }

  // add line
  if ($line !== NULL) {
    $lines[] = $line;
  }

}

// found?
if ($found !== TRUE) {
  json_response(404, array(
    'error' => 'ROM not found in gamelist.xml'
  ));
}

// write
if (file_put_contents($gamelist, $lines) == FALSE) {

  // response
  json_response(500, array(
    'error' => error_get_last(),
  ));

} else {

  // done
  json_response(200, array(
    'rom' => $_GET['filename'],
    'favorite' => !$unfaved
  ));
  
}
