<?php
class DygraphsWidgetTest extends PHPUnit_Framework_TestCase {
	
	private $controller;
	
	protected function setUp() {
		$this->controller = new Controller('test');
	}
	
	public function testInit() {
		$widget = $this->controller->beginWidget('DygraphsWidget');
		$this->assertInstanceOf('DygraphsWidget', $widget);
	}
}