<?php
/**
 * @link https://github.com/Sibilino/yii-dygraphswidget
 * @copyright Copyright (c) 2015 Luis Hernández Hernández
 * @license http://opensource.org/licenses/MIT MIT
 */
class DygraphsWidget extends CWidget {
	
	/**
	 * URL to the dygraphs library to be used. If not specified, the widget will publish its own distribution of the Dygraphs library.
	 * @var string
	 */
	public $scriptUrl;
	/**
	 * The position at which the graph initialization script will be registered. See {@link CClientScript::registerScriptFile} for possible values.
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
	/**
	 * CSS selector that matches checkboxes in your DOM. If this property is set, the matched checkboxes will control the visibility
	 * of series in the Dygraphs chart. To associate a series with a checkbox, specify the id of the series in the {@link checkBoxReferenceAttr}
	 * attribute of the checkbox.
	 * @var string Optional. If this property is not set, the visibility checkbox feature will be disabled.
	 */
	public $checkBoxSelector;
	/**
	 * The attribute of each checkbox matched by {@link $checkBoxSelector} that indicats which of the series is controlled by that checkbox.
	 * @var string Optional. By default, the attribute is "id".
	 */
	public $checkBoxReferenceAttr = 'id';
	
	public function init() {
		if (!isset($this->scriptUrl)) {
			$this->scriptUrl = Yii::app()->assetManager->publish(dirname(__FILE__).'/js/dygraph-combined.js');
		}
		Yii::app()->clientScript->registerScriptFile($this->scriptUrl, CClientScript::POS_HEAD);
		if (!isset($this->scriptPosition)) {
			$this->scriptPosition = CClientScript::POS_READY;
		}
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
		$script = "
			var $this->jsVarName = new Dygraph(
				document.getElementById('$id'),
				$data,
				$options
			);
		";
		Yii::app()->clientScript->registerScript(__CLASS__."#$id-run-dygraphs", $script, $this->scriptPosition);
		
		if (isset($this->checkBoxSelector)) {
			$this->registerCheckboxScript();
		}
		
	}
	
	/**
	 * Registers the necessary jQuery scripts to enable the visibility checkbox feature.
	 * @see checkBoxSelector
	 * @see checkBoxReferenceAttr
	 */
	protected function registerCheckboxScript() {
		Yii::app()->clientScript->registerCoreScript('jquery');
		$script = "
			// Check the checkboxes that correspond to visible series
			$.each($this->jsVarName.getOption('visibility'), function (i, val) {
				$('$this->checkBoxSelector[$this->checkBoxReferenceAttr=' + i + ']').prop('checked', val);
			});
			// On checkbox click, modify the visibility of the corresponding series
			$('$this->checkBoxSelector').click(function () {
				$this->jsVarName.setVisibility($(this).attr('$this->checkBoxReferenceAttr'), $(this).prop('checked'));
			});
		";
		$id = $this->htmlOptions['id'];
		Yii::app()->clientScript->registerScript(__CLASS__."#$id-checkbox-function", $script, $this->scriptPosition);
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