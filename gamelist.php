<?php

// base
require_once('romsite.php');

function read_gamelist($system) {

  // favorites
  $games = array();
  $gamelist = BASE_GAMELIST."$system/gamelist.xml";
  if (file_exists($gamelist) === TRUE) {

    // read
    foreach (file($gamelist) as $line) {

      if (contains($line, '</game>')) {
        if ($path != NULL) {
          $games[$path] = array(
            'path' => $path,
            'name' => $name ?? $path,
            'favorite' => $favorite,
          );
        }
        $path = NULL;
        $name = NULL;
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

      // favorite
      if (contains($line, '<favorite>')) {
        $favorite = TRUE;
      }
    }
  
  }

  // done
  return $games;

}

function remove_game_from_gamelist($system, $title) {

  // open gamelist
  $gamelist = BASE_GAMELIST."$system/gamelist.xml";
  if (file_exists($gamelist) === FALSE) {
    return FALSE;
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
        $ignore = contains($line, $title);
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
