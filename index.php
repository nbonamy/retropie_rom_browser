
<?php

// base
require_once('romsite.php');
require_once('library.php');
require_once('gamelist.php');

// open page
open_page($system);

// setup
if (!file_exists('covers')) {
  render_view('setup', array('path' => __DIR__));
  exit();
}

// system or not
if ($system === NULL) {

  // get systems
  $systems = list_systems();

  // echo
  render_view('systems', array(
    'systems' => $systems
  ));

} else {

  // get
  $games = list_games($system);

  // render
  render_view('games', array(
    'games' => $games
  ));
  
}

// done
close_page($system);
