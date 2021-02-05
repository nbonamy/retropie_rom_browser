
<?php require_once('header.php') ?>

<div>In order to use the ROMS gallery some commands need to be run:</div>
<ul>
  <li>sudo ln -s $HOME/.emulationstation/downloaded_images <?= $path ?>/covers</li>
  <li>sudo find $HOME/.emulationstation/gamelists -name gamelist.xml -exec chmod o+w {} \;
</ul>
<div>Reload the page once done.</div>

<?php require_once('footer.php') ?>
