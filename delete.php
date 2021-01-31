
<?php

// base
require_once('romsite.php');
require_once('gamelist.php');

// rom filename
$romdir = BASE_ROM."$system/";
$filename = $romdir.$_GET['filename'];

// archive or delete
$rc = FALSE;
if (FALSE){//$config['archive'] == TRUE) {

  // archive dir
  $archive_dir = "$romdir/archives/";
  if (file_exists($archive_dir) === FALSE) {
    $rc = mkdir($archive_dir);
  } else {
    $rc = TRUE;
  }

  // move file
  if ($rc === TRUE) {
    $rc = rename($filename, $archive_dir.$_GET['filename']);
  }

} else {

  // delete
  $rc = unlink($filename);

}

if ($rc === TRUE) {

  // delete image
  $image = BASE_IMAGE."$system/".$_GET['image'];
  unlink($image);

  // remove from gamelist
  remove_game_from_gamelist($system, $_GET['filename']);

  // done
  json_response(200, array(
    'rom' => $filename,
    'image' => $image
  ));

} else {

  // response
  json_response(500, array(
    'error' => error_get_last(),
  ));

}
