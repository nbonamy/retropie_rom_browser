<?php

// base
require_once('romsite.php');
require_once('gamelist.php');

$gamelist = read_gamelist('mame-libretro');
print_r($gamelist);

