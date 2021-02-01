<?php

// base
require_once('romsite.php');

function get_gamelist_filename($system) {
  return BASE_GAMELIST."$system/gamelist.xml";
}

function read_gamelist($system) {

  // open gamelist
  $gamelist = get_gamelist_filename($system);
  if (file_exists($gamelist) === FALSE) {
    return FALSE;
  }

  // favorites
  $games = array();
  $favorite = FALSE;
  $name = NULL;
  $image = NULL;

  // read
  foreach (file($gamelist) as $line) {

    if (contains($line, '</game>')) {
      if ($path != NULL) {
        $games[$path] = array(
          'path' => $path,
          'image' => $image,
          'name' => $name ?? $path,
          'favorite' => $favorite,
        );
      }
      $path = NULL;
      $name = NULL;
      $image = NULL;
      $favorite = FALSE;
    }

    // path
    if (contains($line, '<path>')) {
      $line = trim_xml($line, 'path');
      $path = basename($line);
    }

    // name
    if (contains($line, '<name>')) {
      $name = trim_xml($line, 'name');
    }

    // image
    if (contains($line, '<image>')) {
      $image = trim_xml($line, 'image');
    }

    // favorite
    if (contains($line, '<favorite>')) {
      $favorite = TRUE;
    }
  }

  // done
  return $games;

}

function remove_game_from_gamelist($system, $title) {
  return remove_games_from_gamelist($system, $title);
}

function remove_games_from_gamelist($system, $titles) {

  // open gamelist
  $gamelist = get_gamelist_filename($system);
  if (file_exists($gamelist) === FALSE) {
    return FALSE;
  }

  // if single title then make it a list
  if (!is_array($titles)) {
    $titles = array($titles);
  }

  // read file
  $ingame = FALSE;
  $lines = array();
  $game = array();
  foreach (file($gamelist) as $line) {

    if ($ingame === FALSE) {

      if (contains($line, '<game ')) {
        $ingame = TRUE;
        $ignore = FALSE;
      }

    } else {

      // end of game
      if (contains($line, '</game>')) {
        if ($ignore === FALSE) {
          array_push($lines, ...$game);
        } else {
          $line = NULL;
        }
        $ingame = FALSE;
        $game = array();
      }

      // check path for ignore
      if (contains($line, '<path>')) {
        foreach ($titles as $t) {
          if (contains($line, $t)) {
            $ignore = TRUE;
            break;
          }
        }
      }

    }

    // add line
    if ($line !== NULL) {
      if ($ingame) {
        $game[] = $line;
      } else {
        $lines[] = $line;
      }
    }

  }

  // write
  if (file_put_contents($gamelist, $lines) === FALSE) {
    return FALSE;
  }

  // done
  return TRUE;

}
