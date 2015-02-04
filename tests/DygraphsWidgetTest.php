<?php
class DygraphsWidgetTest extends PHPUnit_Framework_TestCase {
	
	private $controller;
	
	protected function setUp() {
		$this->controller = new Controller('test');
	}
	
	public function testEmptyInit() {
		$widget = $this->controller->beginWidget('DygraphsWidget');
		$this->assertInstanceOf('DygraphsWidget', $widget);
		$this->assertEquals('yw0', $widget->htmlOptions['id']);
		$this->assertEquals('dygraphs_yw0', $widget->jsVarName);
	}
}