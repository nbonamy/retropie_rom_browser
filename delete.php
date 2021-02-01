
<?php

// base
require_once('romsite.php');
require_once('gamelist.php');

function delete_game($system, $filename, $image, $update_gamelist = TRUE) {

  // rom filename
  $romdir = BASE_ROM."$system/";
  if (!is_abs_path($filename)) {
    $filename = $romdir.$filename;
  }

  // delete
  if (unlink($filename)) {

    // delete image
    if ($image !== NULL) {
      if (!is_abs_path($image)) {
        $image = BASE_IMAGE."$system/".$image;
      }
      unlink($image);
    }

    // remove from gamelist
    if ($update_gamelist === TRUE) {
      remove_game_from_gamelist($system, basename($filename));
    }

    // done
    return TRUE;

  }

  // too bad
  return FALSE;

}

// process here too
if (isset($_GET['filename'])) {

  if (delete_game($system, $_GET['filename'], $_GET['image'], TRUE)) {

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

}
