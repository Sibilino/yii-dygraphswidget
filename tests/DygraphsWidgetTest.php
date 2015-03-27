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
		$this->assertRegExp("@.*\/assets\/[^\/]+\/dygraph-combined\.js$@", $widget->scriptUrl);
		$this->assertEquals($model->chart, $widget->data);
		$this->assertEquals(CClientScript::POS_READY, $widget->scriptPosition);
		$this->assertTrue(Yii::app()->clientScript->isScriptFileRegistered($widget->scriptUrl, CClientScript::POS_HEAD));
	}
	
	public function testRun() {
		$this->expectOutputString('<div id="test"></div>');
		$widget = $this->controller->widget('DygraphsWidget', array(
				'htmlOptions' => array('id'=>'test'),
				'scriptPosition' => CClientScript::POS_END,
		));
		$this->assertTrue(Yii::app()->clientScript->isScriptRegistered('DygraphsWidget#test-run-dygraphs', CClientScript::POS_END));
		$this->assertFalse(Yii::app()->clientScript->isScriptRegistered('DygraphsWidget#test-checkbox-function', CClientScript::POS_END));
	}
	
	public function testCheckBoxes() {
		$widget = $this->controller->widget('DygraphsWidget', array(
				'htmlOptions' => array('id'=>'test2'),
				'checkBoxSelector' => ".visible-series",
				'checkBoxReferenceAttr' => "series-id",
		));
		$this->assertTrue(Yii::app()->clientScript->isScriptRegistered('DygraphsWidget#test2-checkbox-function'));
		$script = Yii::app()->clientScript->scripts[$widget->scriptPosition]['DygraphsWidget#test2-checkbox-function'];
		$this->assertContains('.visible-series[series-id=', $script);
	}
	
	private function getScript($id) {
		$scripts = Yii::app()->clientScript->scripts;
		if (isset($scripts[CClientScript::POS_READY]) && isset($scripts[CClientScript::POS_READY]["DygraphsWidget#$id-run-dygraphs"])) {
			return $scripts[CClientScript::POS_READY]["DygraphsWidget#$id-run-dygraphs"];
		}
		return false;
	}
	
	public function dataFormatProvider() {
		return array(
			array('http://localhost/testdata.csv', "'http://localhost/testdata.csv',"),
			array('function () {return [[1, 3, 4],[2, 7, 20]];}', "function () {return [[1, 3, 4],[2, 7, 20]];},"),
			array('js:function () {return [[1, 3, 4],[2, 7, 20]];}', "function () {return [[1, 3, 4],[2, 7, 20]];},"),
			array(array(
				array(1, 25, 100),
				array(2, 50, 90),
				array(3, 100, 80),
			), "[[1,25,100],[2,50,90],[3,100,80]]"),
		);
	}
	
	/**
	 * @dataProvider dataFormatProvider
	 */
	public function testDataFormats($data, $expected) {
		$widget = $this->controller->widget('DygraphsWidget', array(
				'data'=>$data,
		));
		$id = $widget->htmlOptions['id'];
		$script = $this->getScript($id);
		$this->assertNotFalse($script);
		$this->assertContains($expected, $script);
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
		$this->assertContains(
				"[[new Date('2014/01/10 00:06:50'),25,100],[new Date('2014/12/23 10:16:40'),50,90],[new Date('2015/07/01 03:09:19'),100,80]]",
				$this->getScript($widget->htmlOptions['id'])
		);
	}
	
	public function testVarName() {
		$widget = $this->controller->widget('DygraphsWidget', array(
				'jsVarName'=>'testvar',
		));
		$this->assertContains(
				"var testvar = new Dygraph(",
				$this->getScript($widget->htmlOptions['id'])
		);
	}
	
	public function testOptions() {
		$widget = $this->controller->widget('DygraphsWidget', array(
				'options'=>array(
		                'strokeWidth' => 2,
		                'parabola' => array(
		                  'strokeWidth' => 0.0,
		                  'drawPoints' => true,
		                  'pointSize' => 4,
		                  'highlightCircleSize' => 6
		                ),
		                'line' => array(
		                  'strokeWidth' => 1.0,
		                  'drawPoints' => true,
		                  'pointSize' => 1.5
		                ),
		                'sine wave' => array(
		                  'strokeWidth' => 3,
		                  'highlightCircleSize' => 10
		                ),
				),
		));
		$this->assertContains(
				"{'strokeWidth':2,'parabola':{'strokeWidth':0,'drawPoints':true,'pointSize':4,'highlightCircleSize':6},'line':{'strokeWidth':1,'drawPoints':true,'pointSize':1.5},'sine wave':{'strokeWidth':3,'highlightCircleSize':10}}",
				$this->getScript($widget->htmlOptions['id'])
		);
	}
	
	public function testHtmlOptions() {
		$this->expectOutputString('<div id="test-id" class="test-class centered" data-toggle="dropdown" onChange="alert(&#039;hello&#039;)"></div>');
		$widget = $this->controller->widget('DygraphsWidget', array(
				'htmlOptions'=>array(
					'id' =>  'test-id',
					'class' => 'test-class centered',
					'data-toggle' => 'dropdown',
					'onChange' => "alert('hello')"
				),
		));
	}
}