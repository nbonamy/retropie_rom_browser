
<?php

// base
require_once('romsite.php');
require_once('gamelist.php');

// open page
open_page($system);

if (!file_exists('covers')) {

  echo <<<END

    <div>In order to use the ROMS gallery some commands need to be run:</div>
    <ul>
      <li>ln -s \$HOME/.emulationstation/downloaded_images ${!${''} = __DIR__}/covers</li>
      <li>find \$HOME/.emulationstation/gamelists -name gamelist.xml -exec chmod o+w {} \;
    </ul>
    <div>Reload the page once done.</div>
    
END;

  exit();

}


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
      $cover = $path_parts['filename'];

      // check ext
      $extension = $path_parts['extension'];
      if (starts_with($extension, 'state') || in_array($extension, IGNORED_EXTS)) {
        continue;
      }

      // find cover
      if (file_exists(BASE_IMAGE."$system/$cover-image.jpg")) {
        $cover = "$cover-image.jpg";
        $image = "covers/$system/$cover";
      } else if (file_exists(BASE_IMAGE."$system/$cover-image.png")) {
        $cover = "$cover-image.png";
        $image = "covers/$system/$cover";
      } else {
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

    // get data
    $filename = $game['filename'];
    $favorite = $game['favorite'];
    $title = $game['title'];
    $image = $game['image'];
    $cover = $game['cover'];

    // output
    echo <<<END
    <div class="game" data-name="$filename" data-image="$cover">
      <div class="action right delete">×</div>
      <div class="action left favorite ${!${''} = ($favorite ? 'active' : '') }">${!${''} = ($favorite ? '♥️' : '♡') }</div>
      <img src="$image" title="$filename">
      <div class="content">
        <span>$title</span>
      </div>
    </div>
END;

  }
  
}

// done
close_page($system);
