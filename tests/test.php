<?php
Yii::setPathOfAlias('dev', dirname(__FILE__).'/../widget');
return CMap::mergeArray(
	require(dirname(__FILE__).'/../tester/protected/config/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
		),
		'import'=>array(
			'dev.*',
		)
	)
);
