
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
  $systems = array();
  $dir = new DirectoryIterator(BASE_IMAGE);
  foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
      $systems[] = $fileinfo->getFilename();
    }
  }

  // sort
  sort($systems);

  // echo
  echo "<ul>";
  foreach ($systems as $system) {
    echo "<li><a href=\"index.php?system=$system\">$system</a></li>";
  }
  echo "</ul>";

} else {

  // toolbar
  echo <<<END
    <ul class="toolbar">
      <li><a href="dedup.php?system=$system">Remove duplicates</a></li>
    </ul>
END;

  // favorites
  $metadata = read_gamelist($system);

  // get games
  $games = array();
  $dir = new DirectoryIterator(BASE_ROM."$system");
  foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot() && !$fileinfo->isDir()) {

      // skip other dots
      $filename = $fileinfo->getFilename();
      if (starts_with($filename, '.') || in_array($filename, IGNORED_FILENAMES)) {
        continue;
      }

      // split
      $path_parts = pathinfo($fileinfo->getPathname());

      // check ext
      $extension = $path_parts['extension'];
      if (starts_with($extension, 'state') || in_array($extension, IGNORED_EXTS)) {
        continue;
      }

      // find cover
      if (isset($metadata[$filename]['image'])) {
        $cover = basename($metadata[$filename]['image']);
      } else {
        $cover = $path_parts['filename'];
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

  // echo
  foreach ($games as $game) {
    render_view('game', array(
      'title' => $game['title'],
      'filename' => $game['filename'],
      'favorite' => $game['favorite'],
      'image' => $game['image'],
      'cover' => $game['cover']
    ));
  }
  
}

// done
close_page($system);
