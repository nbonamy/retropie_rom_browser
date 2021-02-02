<?php

// base
require_once('romsite.php');
require_once('library.php');
require_once('gamelist.php');

// this can take some time
set_time_limit(0);

// init
$games = find_duplicates($system);

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
      if (!file_exists(get_rom_fullpath($system, $roms[$i]['path']))) {
        echo "<li>File does not exist for \"{$roms[$i]['path']}\" ({$roms[$i]['name']})</li>";
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
    remove_games_from_gamelist($system, $deleted);
  }

} else {

  render_view('dedup', array(
    'system' => $system,
    'games' => $games,
    'count' => $count,
  ));

}

// back
echo "<div><a href=\"index.php?system=$system\">Back</a></div>";

// done
close_page();
