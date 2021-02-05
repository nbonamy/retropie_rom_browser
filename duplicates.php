<?php

// base
require_once('includes/romsite.php');
require_once('includes/library.php');
require_once('includes/gamelist.php');

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
if ($count == 0) {

  render_page('duplicates', array(
    'title' => 'Duplicates',
    'content' => '<div>No duplicates found</div>'
  ));

} else if (isset($_GET['confirmed']) && $_GET['confirmed'] == TRUE) {

  $content = '<ul>';
  $deleted = array();
  foreach ($games as $title => &$roms) {
    for ($i=1; $i<count($roms); $i++) {

      // delete without updating gamelist
      if (!file_exists(get_rom_fullpath($system, $roms[$i]['path']))) {
        $content .= "<li>File does not exist for \"{$roms[$i]['path']}\" ({$roms[$i]['name']})</li>";
        $deleted[] = basename($roms[$i]['path']);
      } else if (delete_game($system, $roms[$i]['path'], $roms[$i]['image'], FALSE)) {
        $content .= "<li>Deleted \"{$roms[$i]['path']}\" ({$roms[$i]['path']})</li>";
        $deleted[] = basename($roms[$i]['path']);
      } else {
        $content .= "<li>Error while deleting \"{$roms[$i]['path']}\" ({$roms[$i]['path']})</li>";
      }
    }
  }
  $content .= '</ul>';

  // update gamelist
  if (count($deleted) > 0) {
    remove_games_from_gamelist($system, $deleted);
  }

  // output
  render_page('duplicates', array(
    'title' => 'Duplicates',
    'content' => $content,
  ));

} else {

  render_view('duplicates', array(
    'title' => 'Duplicates',
    'games' => $games,
    'count' => $count,
  ));

}
