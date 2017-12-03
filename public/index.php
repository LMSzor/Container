<?php

require_once __DIR__ . '/../vendor/autoload.php';

$c = new \LMSzor\Container\Container();

$r = $c->get(\LMSzor\Mockup\Bar::class);

var_dump($r);