# Dygraphs Widget for Yii
-------------------------
A simple graph widget for Yii 1, based on [Dygraphs] (http://dygraphs.com/).

## Changelog
------------
1.1.0 - Added visibility checkboxes feature.

## Installation
---------------
Download the latest release and unpack the **contents** of the `widget` folder inside the `protected\extensions\dygraphswidget` folder within your Yii application.

## Usage
--------
In your view, create the widget with your data matrix as its *data* option.
```php
$this->widget('ext.dygraphswidget.DygraphsWidget', array(
		'data'=> $your_data,
	));
```

## Dygraphs options
-------------------
You can set the *options* property to pass additional options to the Dygraphs object:
```php
$this->widget('DygraphsWidget', array(
		'data'=> $your_data,
		'options'=>array(
			'labels' => array('X', 'Sin', 'Rand', 'Pow'),
			'title'=> 'Main Graph',
			//...
		),
	));
```

## Data formats
---------------
The data property can be specified in three different formats. Consider the following examples, and make sure to read [the official documentation] (http://dygraphs.com/data.html) for more details:
- **Matrix**
```php
$data = array(
	array(1, 25, 100),
	array(2, 50, 90),
	array(3, 100, 80),
	//...
);
```
- **URL**
An absolute URL to a text file with the data.
```php
$data = 'http://dygraphs.com/dow.txt';
```
- **Function**
A string with JS code that returns a data object usable by Dygraphs.
```php
$data = 'function () {
	var data = [];
      for (var i = 0; i < 1000; i++) {
        var base = 10 * Math.sin(i / 90.0);
        data.push([i, base, base + Math.sin(i / 2.0)]);
      }
      var highlight_start = 450;
      var highlight_end = 500;
      for (var i = highlight_start; i <= highlight_end; i++) {
        data[i][2] += 5.0;
      }
	return data;
}';
```

## Additional options
---------------------

The following widget properties can also be specified:
- **xIsDate**: Set this property to true if the x-values (first value in each row of the data matrix) are date strings, in order to properly convert them to JS date objects for Dygraphs.
- **scriptUrl**: The URL where the Dygraphs.js library is taken from. If not set, the widget will locally publish its own distribution of the Dygraphs library.
- **model** and **attribute**: Specify a CModel instance and one of its attributes in order to take the data from it.
- **jsVarName**: Specifies a custom name for the JS variable that will receive the Dygraphs object upon creation.
- **htmlOptions**: Additional HTML attributes for the graph-containing div.

## Passing JavaScript functions
-------------------------------
Both the data and options for the widget support literal JavaScript code. In order to pass JavaScript code, just prepend *js:* to the string containing the code.
For example, if your data is contained in a JavaScript var with the name *javascriptData*:
```php
$data = 'js:javascriptData';
```
Or let's say you need to pass a function for a Callback option:
```php
$options = array(
	'underlayCallback' => 'js:function(canvas, area, g)
			{
				var bottom_left = g.toDomCoords(highlight_start, -20);
				var top_right = g.toDomCoords(highlight_end, +20);

				var left = bottom_left[0];
				var right = top_right[0];

				canvas.fillStyle = "rgba(255, 255, 102, 1.0)";
				canvas.fillRect(left, area.y, right - left, area.h);
            }'
);
```

Alternatively, you can pass a new instance of CJavaScriptExpression() constructed with your JavaScript string.

## Visibility checkboxes
------------------------
It is often useful to hide and show some of the dataseries in a chart. The widget features helper scripts to easily control series visibily with checkboxes.

To use this feature, make sure your page has one checkbox per series in the chart, and give each checkbox an `id` attribute with the index of the series controlled by it.
Then, configure the widget with a `checkBoxSelector` that matches the group of checkboxes. For example, for a chart with 3 data series:
```html
<input class="visibility" id="0" type="checkbox">
<input class="visibility" id="1" type="checkbox">
<input class="visibility" id="2" type="checkbox">
```
```php
$this->widget('DygraphsWidget', array(
		'data'=> array(
			// (x, series0, series1, series2)
			array(0, 3, 25, 247),
			array(1, 6, 26, 127),
			array(2, 9, 27, -7),
			//...
		),
		'checkBoxSelector' => 'input.visibility',
		//...
	),
));
```

The attribute that associates a checkbox with a data series (`id` in the example) can be changed by configuring `checkBoxReferenceAttr`.