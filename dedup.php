<?php

// base
require_once('romsite.php');
require_once('gamelist.php');
require_once('delete.php');

// this can take some time
set_time_limit(0);

// init
$games = array();
$gamelist = read_gamelist($system);

// extract title
foreach ($gamelist as $rom => $game) {
  
  // 1st get title
  $title = $game['name'];
  $title = preg_replace('/\([^\)]*\)/', '', $title);
  $title = trim($title);

  // init score
  $name = $game['name'];
  $game['score'] = 0;

  // beta
  if (preg_match('/\([^\)]*beta[^\)]*\)/i', $game['path'])) {
    $game['score'] -= 20;
  }

  // bootleg
  if (preg_match('/\([^\)]*bootleg[^\)]*\)/i', $name)) {
    $game['score'] -= 10;
  }

  // cocktail
  if (preg_match('/\([^\)]*cocktail[^\)]*\)/i', $name)) {
    $game['score'] -= 5;
  }

  // protected
  if (contains($name, 'protected') && !contains($name, 'unprotected')) {
    $game['score'] -= 500;
  }

  // geo
  $geo_found = FALSE;
  foreach (GEOGRAPHIES as $country => $score) {
    if (preg_match('/\([^\)]*' . $country . '[^\)]*\)/i', $name)) {
      $game['score'] += $score;
      $geo_found = TRUE;
      break;
    }
  }

  // simpler geos (n64)
  if ($geo_found === FALSE) {
    if (contains($game['path'], '(W)')) {
      $game['score'] += GEOGRAPHIES['World'];
    } else if (contains($game['path'], '(E)')) {
      $game['score'] += GEOGRAPHIES['Europe'];
    } else if (contains($game['path'], '(U)')) {
      $game['score'] += GEOGRAPHIES['USA'];
    } else if (contains($game['path'], '(J)')) {
      $game['score'] += GEOGRAPHIES['Japan'];
    }
  }

  // revision/set/...
  $matches = NULL;
  if (preg_match('/\([^\)]*(rev|rev\.|revision|set|program code)( |\.)(\w+)[^\)]*\)/i', $name, $matches)) {
    $match = end($matches);
    if (is_numeric($match)) {
      $game['score'] += intval($match);
    } else {
      $game['score'] += ord($match)-ord('A')+1;
    }
  }

  // version number
  $matches = NULL;
  if (preg_match('/\([^\)]*v(\d)\.(\d)[^\)]*\)/i', $name, $matches)) {
    $game['score'] += intval($matches[1])*10 + intval($matches[2]);
  }

  // new?
  if (preg_match('/\([^\)]*new version[^\)]*\)/i', $name)) {
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

// count
$count = 0;
foreach ($games as $title => &$roms) {
  $count += count($roms)-1;
}

// output
open_page($system);
if ($count == 0) {

  echo 'No duplicates found';

} else if (isset($_GET['confirmed']) && $_GET['confirmed'] == TRUE) {

  echo '<ul>';
  $deleted = array();
  foreach ($games as $title => &$roms) {
    for ($i=1; $i<count($roms); $i++) {

      // delete without updating gamelist
      if (!file_exists($roms[$i]['path'])) {
        echo "<li>File does not exist for \"{$roms[$i]['path']}\" ({$roms[$i]['path']})</li>";
        $deleted[] = basename($roms[$i]['path']);
      } else if (delete_game($system, $roms[$i]['path'], $roms[$i]['image'], FALSE)) {
        echo "<li>Deleted \"{$roms[$i]['path']}\" ({$roms[$i]['path']})</li>";
        $deleted[] = basename($roms[$i]['path']);
      } else {
        echo "<li>Error while deleting \"{$roms[$i]['path']}\" ({$roms[$i]['path']})</li>";
      }
    }
  }
  echo '</ul>';

  // update gamelist
  if (count($deleted) > 0) {
    remove_game_from_gamelist($system, $deleted);
  }

} else {

  render_view('dedup', array('games' => $games));

}

// back
echo "<div><a href=\"index.php?system=$system\">Back</a></div>";

// done
close_page($system);
