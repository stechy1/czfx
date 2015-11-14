<?php

$config = file_get_contents("app/config/config.json");
$config = (array) json_decode($config);

var_dump($config);