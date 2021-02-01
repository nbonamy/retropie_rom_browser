
<div class="game" data-name="<?= $filename ?>" data-image="<?= $cover ?>">
  <div class="action right delete">×</div>
  <div class="action left favorite <?= $favorite ? 'active' : '' ?>"><?= $favorite ? '♥️' : '♡' ?></div>
  <img src="<?= $image ?>" title="<?= $filename ?>">
  <div class="content">
    <span><?= $title ?></span>
  </div>
</div>
