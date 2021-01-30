
<?php

// base
require_once('romsite.php');
require_once('gamelist.php');

// delete rom
$filename = BASE_ROM."$system/".$_GET['filename'];
if (unlink($filename) === TRUE) {

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
