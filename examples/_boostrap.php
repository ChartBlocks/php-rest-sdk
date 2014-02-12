<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include(__DIR__ . '/../vendor/autoload.php');
} elseif (file_exists(__DIR__ . '/../../autoload.php')) {
    include(__DIR__ . '/../../autoload.php');
} else {
    throw new \Exception('Could not find autoload.php');
}
