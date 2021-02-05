
<?php require_once('header.php') ?>

<ul class="toolbar">
  <li><a href="duplicates.php?system=<?= $system ?>">Remove duplicates</a></li>
</ul>

<?php render_view('_gamelist', array('games' => $games)); ?>

<?php require_once('footer.php') ?>
