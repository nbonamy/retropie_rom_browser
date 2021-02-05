
<?php require_once('header.php') ?>

<ul>
<li><a href="favorites.php">favorites</a></li>
<li><a id="search" href="#">search</a><br/>&nbsp;</li>
  <?php foreach ($systems as $system): ?>
    <li><a href="index.php?system=<?= $system ?>"><?= $system ?></a></li>
  <?php endforeach; ?>
</ul>

<?php require_once('footer.php') ?>
