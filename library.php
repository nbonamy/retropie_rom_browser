<?php

// base
require_once('romsite.php');
require_once('gamelist.php');

function get_rom_fullpath($system, $filename) {
  if (is_abs_path($filename)) {
    return $filename;
  } else {
    return BASE_ROM."$system/$filename";
  }
}

function delete_game($system, $filename, $image, $update_gamelist = TRUE) {

  // rom filename
  $filename = get_rom_fullpath($system, $filename);

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
