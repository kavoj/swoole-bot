<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */


use Kcloze\Bot\Process;

define('BOT_ROOT', realpath(__DIR__ . '/../'));

require BOT_ROOT . '/vendor/autoload.php';

$config = require BOT_ROOT . '/config.php';

//启动
(new Process())->start($config);
