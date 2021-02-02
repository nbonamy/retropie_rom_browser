
<ul class="toolbar">
  <li><a href="dedup.php?system=<?= $system ?>">Remove duplicates</a></li>
</ul>

<?php
  foreach ($games as $game) {
    render_view('game', array(
      'title' => $game['title'],
      'filename' => $game['filename'],
      'favorite' => $game['favorite'],
      'image' => $game['image'],
      'cover' => $game['cover']
    ));
  }
?>
