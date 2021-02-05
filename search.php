<?php

// base
require_once('includes/romsite.php');
require_once('includes/library.php');
require_once('includes/gamelist.php');

// for all systems
$results = array();
$systems = list_systems();

// open gamelists
foreach ($systems as $system) {

  // read it
  $games = list_games($system);
  foreach ($games as $game) {
    if (contains($game['title'], $_GET['q'])) {
      $results[] = $game;
    }

  }

}

// sort
usort($results, function($a, $b) {
  return strcasecmp($a['title'], $b['title']);
});


// output
render_view('gamelist', array(
  'title' => 'Search results',
  'games' => $results,
));
