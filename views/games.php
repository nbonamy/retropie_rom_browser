
<ul class="toolbar">
  <li><a href="duplicates.php?system=<?= $system ?>">Remove duplicates</a></li>
</ul>

<?php
  foreach ($games as $game) {
    render_view('game', array(
      'system' => $game['system'],
      'title' => $game['title'],
      'filename' => $game['filename'],
      'favorite' => $game['favorite'],
      'image' => $game['image'],
      'cover' => $game['cover']
    ));
  }
?>
