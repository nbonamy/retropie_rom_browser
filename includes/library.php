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

function is_valid_rom($filename) {

  // skip other dots
  $filename = basename($filename);
  if (starts_with($filename, '.') || in_array($filename, IGNORED_FILENAMES)) {
    return FALSE;
  }

  // split
  $path_parts = pathinfo($filename);

  // check ext
  $extension = $path_parts['extension'];
  if (starts_with($extension, 'state') || in_array($extension, IGNORED_EXTS)) {
    return FALSE;
  }

  // seems ok
  return TRUE;
  
}

function list_systems() {

  // get systems
  $systems = array();
  $dir = new DirectoryIterator(BASE_ROM);
  foreach ($dir as $dirinfo) {
    if (!$dirinfo->isDot() && $dirinfo->isDir()) {
      $dir2 = new DirectoryIterator(BASE_ROM.$dirinfo->getFilename());
      foreach ($dir2 as $fileinfo) {
        if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
          if (is_valid_rom($fileinfo->getFilename())) {
            $systems[] = $dirinfo->getFilename();
            break;
          }
        }
      }
    }
  }

  // sort
  sort($systems);

  // done
  return $systems;

}

function list_games($system) {

  // favorites
  $metadata = read_gamelist($system);

  // get games
  $games = array();
  $dir = new DirectoryIterator(BASE_ROM.$system);
  foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot() && !$fileinfo->isDir()) {

      // skip non valid roms
      $filename = $fileinfo->getFilename();
      if (is_valid_rom($filename) == FALSE) {
        continue;
      }

      // split
      $path_parts = pathinfo($fileinfo->getPathname());

      // find cover
      if (isset($metadata[$filename]['image'])) {
        $cover = basename($metadata[$filename]['image']);
      } else {
        // default to jpg
        $cover = $path_parts['filename'].'.jpg';
      }

      // fallback
      $image = "covers/$system/$cover";
      if ($cover === NULL || !file_exists($image)) {
        //$image = "https://upload.wikimedia.org/wikipedia/commons/thumb/0/02/CD_icon_test.svg/1920px-CD_icon_test.svg.png";
        $image = "https://cdn4.iconfinder.com/data/icons/disk-drives-1/512/Switch_Cartridge-01-512.png";
      }

      // done
      $gamename = $filename;
      $favorite = FALSE;
      if (isset($metadata[$filename])) {
        $gamename = $metadata[$filename]['name'];
        $favorite = $metadata[$filename]['favorite'];
      }

      $games[] = array(
        'system' => $system,
        'filename' => $filename,
        'title' => $gamename,
        'favorite' => $favorite,
        'cover' => $cover,
        'image' => $image
      );
    }
  }

  // sort
  usort($games, function($a, $b) {
    return strcasecmp($a['title'], $b['title']);
  });

  // done
  return $games;

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

function find_duplicates($system) {

  // init
  $games = array();
  $gamelist = read_gamelist($system);

  // extract title
  foreach ($gamelist as $rom => $game) {
    
    // 1st get title
    $title = $game['name'];

    // remove parenthesized stuff
    $title = preg_replace('/\([^\)]*\)/', '', $title);
    
    // avoid word separators to interfere
    $title = preg_replace('/[-:]/', ' ', $title);
    
    // final trimming
    $title = preg_replace('/ [ ]*/', ' ', $title);
    $title = trim($title);

    // init score
    $fullname = $game['name'].' '.$game['path'];
    $game['score'] = 0;

    // beta
    if (preg_match('/\([^\)]*beta[^\)]*\)/i', $fullname)) {
      $game['score'] -= 20;
    }

    // bootleg
    if (preg_match('/\([^\)]*bootleg[^\)]*\)/i', $fullname)) {
      $game['score'] -= 10;
    }

    // cocktail
    if (preg_match('/\([^\)]*cocktail[^\)]*\)/i', $fullname)) {
      $game['score'] -= 5;
    }

    // protected
    if (contains($fullname, 'protected') && !contains($fullname, 'unprotected')) {
      $game['score'] -= 500;
    }

    // geo
    $geo_found = FALSE;
    foreach (GEOGRAPHIES as $country => $score) {
      if (preg_match('/\([^\)]*'.$country.'[^\)]*\)/i', $fullname)) {
        $game['score'] += $score;
        $geo_found = TRUE;
        break;
      }
    }

    // simpler geos (n64)
    if ($geo_found === FALSE) {
      if (contains($fullname, '(W)')) {
        $game['score'] += GEOGRAPHIES['World'];
      } else if (contains($fullname, '(E)')) {
        $game['score'] += GEOGRAPHIES['Europe'];
      } else if (contains($fullname, '(U)')) {
        $game['score'] += GEOGRAPHIES['USA'];
      } else if (contains($fullname, '(J)')) {
        $game['score'] += GEOGRAPHIES['Japan'];
      }
    }

    // revision/set/...
    $matches = NULL;
    if (preg_match('/\([^\)]*(rev|rev\.|revision|set|program code)( |\.)(\w+)[^\)]*\)/i', $fullname, $matches)) {
      $match = end($matches);
      if (is_numeric($match)) {
        $game['score'] += intval($match);
      } else {
        $game['score'] += ord($match)-ord('A')+1;
      }
    }

    // version number
    $matches = NULL;
    if (preg_match('/\([^\)]*v(\d)\.(\d)[^\)]*\)/i', $fullname, $matches)) {
      $game['score'] += intval($matches[1])*10 + intval($matches[2]);
    }

    // new?
    if (preg_match('/\([^\)]*new version[^\)]*\)/i', $fullname)) {
      $game['score'] += 1;
    }

    // done
    $games[$title][] = $game;

  }

  // remove games which have only one rom
  foreach ($games as $title => &$roms) {
    if (count($roms) == 1) {
      unset($games[$title]);
    }
  }

  // now sort by score
  foreach ($games as $title => &$roms) {
    usort($roms, function($a, $b) {
      if ($a['score'] == $b['score']) {
      return strcmp($a['path'], $b['path']); 
      } else {
        return $a['score'] < $b['score'];
      }
    });
  }

  // final sort
  ksort($games);  

  // done
  return $games;

}
