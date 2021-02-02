
<ul>
  <li><a href="favorites.php">favorites</a><br/>&nbsp;</li>
  <?php foreach ($systems as $system): ?>
    <li><a href="index.php?system=<?= $system ?>"><?= $system ?></a></li>
  <?php endforeach; ?>
</ul>
