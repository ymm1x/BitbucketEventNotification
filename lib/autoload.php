<?php
require_once __DIR__ . '/utils.php';
require_once 'SplClassLoader.php';

$splClassLoader = new SplClassLoader('BitbucketEventNotification', __DIR__);
$splClassLoader->register();
