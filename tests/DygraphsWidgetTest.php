<?php
class TestModel extends CFormModel {
	public $chart = array(
		array(1, 25, 100),
		array(2, 50, 90),
		array(3, 100, 80),
	);
}

class DygraphsWidgetTest extends PHPUnit_Framework_TestCase {
	
	private $controller;
	
	protected function setUp() {
		$this->controller = new Controller('test');
	}
	
	public function testInit() {
		
		$model = new TestModel();
		$widget = $this->controller->beginWidget('DygraphsWidget', array(
			'model'=>$model,
			'attribute'=>'chart',
		));
		
		$this->assertInstanceOf('DygraphsWidget', $widget);
		$this->assertTrue(isset($widget->htmlOptions['id']));
		$this->assertTrue(isset($widget->jsVarName));
		$this->assertEquals($model->chart, $widget->data);
		$this->assertTrue(Yii::app()->clientScript->isScriptFileRegistered($widget->scriptUrl));
	}
	
	public function testRun() {
		$this->expectOutputString('<div id="test"></div>');
		$widget = $this->controller->widget('DygraphsWidget', array(
				'htmlOptions' => array('id'=>'test'),
		));
	}
	
	private function dataTester($data, $expected) {
		$widget = $this->controller->widget('DygraphsWidget', array(
				'data'=>$data,
		));
		$scripts = array_values(end(Yii::app()->clientScript->scripts));
		$this->assertContains($expected, end($scripts));
	}
	
	public function testDataUrl() {
		$this->dataTester('http://localhost/testdata.csv', "'http://localhost/testdata.csv',");
	}
	
	public function testDataFunction() {
		$this->dataTester('function () {return [[1, 3, 4],[2, 7, 20]];}', "function () {return [[1, 3, 4],[2, 7, 20]];},");
	}
	
	public function testDataArray() {
		$this->dataTester(array(
				array(1, 25, 100),
				array(2, 50, 90),
				array(3, 100, 80),
			), "[[1,25,100],[2,50,90],[3,100,80]]");
	}
	
	public function testDataWithDates() {
		$data = array(
				array("2014/01/10 00:06:50", 25, 100),
				array("2014/12/23 10:16:40", 50, 90),
				array("2015/07/01 03:09:19", 100, 80),
			);
		
		$widget = $this->controller->widget('DygraphsWidget', array(
				'data'=>$data,
				'xIsDate'=>true,
		));
		$scripts = array_values(end(Yii::app()->clientScript->scripts));
		$this->assertContains("[[new Date('2014/01/10 00:06:50'),25,100],[new Date('2014/12/23 10:16:40'),50,90],[new Date('2015/07/01 03:09:19'),100,80]]", end($scripts));
	}
	
}