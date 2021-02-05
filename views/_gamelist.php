
<?php require_once('header.php') ?>

<?php
  foreach ($games as $game) {
    render_view('_game', array(
      'system' => $game['system'],
      'title' => $game['title'],
      'filename' => $game['filename'],
      'favorite' => $game['favorite'],
      'image' => $game['image'],
      'cover' => $game['cover']
    ));
  }
?>

<?php require_once('footer.php') ?>
