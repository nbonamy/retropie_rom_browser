<?php

// base
require_once('includes/romsite.php');
require_once('includes/library.php');
require_once('includes/gamelist.php');

// for all systems
$favorites = array();
$systems = list_systems();

// open gamelists
foreach ($systems as $system) {

  // read it
  $games = list_games($system);
  foreach ($games as $game) {
    if ($game['favorite'] == 1) {
      $favorites[] = $game;
    }

  }

}

// sort
usort($favorites, function($a, $b) {
  return strcasecmp($a['title'], $b['title']);
});


// output
render_view('favorites', array(
  'title' => 'Favorites',
  'games' => $favorites,
));
