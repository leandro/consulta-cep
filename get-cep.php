#!/usr/bin/php
<?php
  // USAGE: ./get-cep.php 02012000
  require 'cep-data.php';
  echo CEPData::fetch($argv[1]);
?>
