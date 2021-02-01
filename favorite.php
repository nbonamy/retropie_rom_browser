
<?php

// base
require_once('romsite.php');
require_once('gamelist.php');

// open gamelist
$gamelist = get_gamelist_filename($system);
if (file_exists($gamelist) === FALSE) {
  json_response(404, array(
    'error' => 'Gamelist not found',
  ));
}

// read file and add favorite line
$ingame = FALSE;
$unfaved = FALSE;
$lines = array();
foreach (file($gamelist) as $line) {

  if ($ingame === FALSE) {

    if (contains($line, '<path>') && contains($line, htmlentities($_GET['filename']))) {
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
