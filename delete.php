<?php

// base
require_once('includes/romsite.php');
require_once('includes/library.php');

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
