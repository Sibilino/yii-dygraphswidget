<?php
/**
 * @link https://github.com/Sibilino/yii-dygraphswidget
 * @copyright Copyright (c) 2015 Luis Hern�ndez Hern�ndez
 * @license http://opensource.org/licenses/MIT MIT
 */
class DygraphsWidget extends CWidget {
	
	/**
	 * URL to the dygraphs library to be used. If not specified, the widget will publish its own distribution of the Dygraphs library.
	 * @var string
	 */
	public $scriptUrl;
	/**
	 * The position where the Dygraphs.js library will be registered. See {@link CClientScript::registerScriptFile} for possible values.
	 * @var integer
	 */
	public $scriptPosition;
	/**
	 * Can be used together with $attribute instead of setting the $data property.
	 * @var CModel
	 */
	public $model;
	/**
	 * Attribute of $model from which $data will be taken.
	 * @var string
	 */
	public $attribute;
	/**
	 * The data array to be passed to the graph.
	 * The standard format is to use a matrix of array rows, for which $row[0] is the X axis, and $row[N] is the Y axis for the data series N.
	 * Alternatively, a string representing a JavaScript function may be passed instead. This code does not need to include the "function () {}".
	 * @link http://dygraphs.com/data.html#array
	 * @var mixed Array or string
	 */
	public $data = array();
	/**
	 * HTML options for the div containing the graph.
	 * @var array
	 */
	public $htmlOptions = array();
	/**
	 * Additional Dygraphs options that will be passed to the Dygraphs object upon initialization.
	 * @link http://dygraphs.com/options.html
	 * @var array
	 */
	public $options = array();
	/**
	 * The name of the JS variable that will receive the Dygraphs object. Optional.
	 * @var string
	 */
	public $jsVarName;
	/**
	 * If set to true and this graph's data is an array, the first column of each data row will be converted to JS Date object.
	 * @var boolean
	 */
	public $xIsDate;
	
	public function init() {
		if (!isset($this->scriptUrl)) {
			$this->scriptUrl = Yii::app()->assetManager->publish(dirname(__FILE__).'/js/dygraph-combined.js');
		}
		if (!isset($this->scriptPosition)) {
			$this->scriptPosition = CClientScript::POS_READY;
		}
		Yii::app()->clientScript->registerScriptFile($this->scriptUrl, $this->scriptPosition);
		if ($this->hasModel()) {
			$attr = $this->attribute;
			$this->data = $this->model->$attr;
		}
		if (!isset($this->htmlOptions['id'])) {
			$this->htmlOptions['id'] = $this->getId();
		}
		if (!isset($this->jsVarName)) {
			$this->jsVarName = 'dygraphs_'.$this->getId();
		}
	}
	
	public function run() {
		
		echo CHtml::tag('div', $this->htmlOptions, '');
		
		$id = $this->htmlOptions['id'];
		$options = CJavaScript::encode($this->options);
		$data = $this->preprocessData();
		$script = "var $this->jsVarName = new Dygraph(
			 document.getElementById('$id'),
			 $data,
			 $options
		);";
		Yii::app()->clientScript->registerScript(__CLASS__."#$id-run-dygraphs", $script);
	}
	
	/**
	 * @return boolean whether this widget is associated with a data model.
	 */
	protected function hasModel() {
		return $this->model instanceof CModel && $this->attribute!==null;
	}
	
	/**
	 * Encodes the current data into the proper JS variable, URL or function.
	 * @return Ambigous <string, mixed>
	 */
	protected function preprocessData() {
		if (is_string($this->data)) {
			if (strpos($this->data, 'function') === 0) {
				$this->data = new CJavaScriptExpression($this->data);
			} else {
				$url_validator = new CUrlValidator();
				$url = $url_validator->validateValue($this->data);
				if ($url !== false) {
					$this->data = $url;
				}
			}
		} elseif (is_array($this->data)&& $this->xIsDate) {
			foreach ($this->data as &$row) {
				$row[0] = new CJavaScriptExpression("new Date('$row[0]')");
			}
		}
		return CJavaScript::encode($this->data);
	}
}