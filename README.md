# Dygraphs Widget for Yii
=========================
A simple widget for Yii 1, based on [Dygraphs] (http://dygraphs.com/).

## Installation
---------------
Just unpack the widget in the `extensions` folder of your Yii application.

## Usage
--------
In your view, create the widget with your data matrix as its *data* option.
```
$this->widget('ext.yii-dygraphswidget.DygraphsWidget', array(
		'data'=> $your_data,
	));
```

## Dygraphs options
----------
You can set the *options* property to pass additional options to the Dygraphs object:
```
$this->widget('DygraphsWidget', array(
		'data'=> $your_data,
		'options'=>array(
			'labels' => array('X', 'Sin', 'Rand', 'Pow'),
			'title'=> 'Main Graph',
			...
		),
	));
```

## Data formats
---------------
The data property can be specified in three different formats. Consider the following examples, and make sure to read [http://dygraphs.com/data.html] (the official documentation) for more details:
1. Matrix
```
$data = array(
	array(1, 25, 100),
	array(2, 50, 90),
	array(3, 100, 80),
	...
);
```
2. URL
```
$data = 'http://dygraphs.com/dow.txt';
```
3. Function
A string with JS code that returns a data object usable by Dygraphs. You can omit the function declaration:
```
$data = '
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
';
```

## Additional options
---------------------
The following widget properties can also be specified:
- **xIsDate**: Set this property to true if the x-values (first value in each row of the data matrix) are a date strings, in order to properly convert them to JS date objects for Dygraphs.
- **scriptUrl**: The URL where the Dygraphs.js library is taken from. You can modify this property to use a locally-hosted Dygraphs library, or to use another version.
- **model** and **attribute**: Specify a CModel instance and one of its attributes in order to take the data from it.
- **jsVarName**: Specifies a custom name for the JS variable that will receive the Dygraphs object upon creation.

 