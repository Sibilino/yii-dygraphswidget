<?php

// change the following paths if necessary
$yiit='C:/wamp/www/framework/yiit.php';
$config=dirname(__FILE__).'/test.php';

require_once($yiit);

Yii::createWebApplication($config);
