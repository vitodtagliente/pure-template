# Pure Template Component
Simple and fast template engine
# How To render a view
1. Define the default path in which the engine will search for view files:
    ```php
    Pure\Template\View::path( $default_path );
    ```
2. Instantiate a view object:
    ```php
    $view = new Pure\Template\View();
    // or
    $view = new Pure\Template\View(
        array('param1' => 'value1', ... , 'paramN' => 'valueN')
    );
    ```
3. Set params:
    ```php
    $view->paramJ = 'valueJ';
    ```
    In this way it is possibile to define other parameters outside the default constructor.
4. Clear params:
    ```php
    $view->clear();
    ```
5. Render the output:
    ```php
    $view->render(
        $filename, // the file palced inside of Pure\Template\View::path()
        $direct_output = true, // if true, the output is displayed
        $dont_compute = false // if true, no engine extensions are applied
    );
    ```
7. Instead of instantiate the view object, it is possibile to directly output a view by a static function:
    ```php
    Pure\Template\View::make(
        $filename,
        $params = array(),
        $direct_output = true,
        $dont_compute = false
    );
    ```
#### Let me show an example:
1. Define a view in path: views/example.php
    ```html
    <html>
    <head>
        <title>Example</title>
    </head>
    <body>
        <?php echo $foo; ?>
    </body>
    </html>
    ```
2. Render the view:
    ```php
    use Pure\Template\View;

    // Set the default path
    View::path('views');

    $view = new View();
    $view->foo = "Hello View!"; // set the param foo
    $result = $view->render('example.php');
    ```
3. The output will be this:
    ```html
    <html>
    <head>
        <title>Example</title>
    </head>
    <body>
        Hello View!
    </body>
    </html>
    ```
# How To avoid <?php ?> inline calls
In the last example we used
```html
<body>
    <?php echo $foo; ?>
</body>
```
to render the foo variable, which is defined as:
```php
$view = new View();
$view->foo = "Hello View!"; // set the param foo
```
If during the render phase, the argument $dont_compute is set to false, the view engine extensions are called. Which means that more features are available.
1. Render paramas in fast way:
    ```html
    <body>
        {{ $foo }}
    </body>
    ```
# How To extend views
Another Pure Template extension let to extend views and override contents.
1. To extend a view:
    ```php
    @extends('view_filename')
    ```
    the @extends must be the first statement
2. Sections have to be defined in parent view:
    ```php
    @section('section_name')
    ```
3. Sections can be override as follow:
    ```php
    @begin('section_name')
    <h1> HTML content </h1>
    @end
    ```
#### Let me show an example:
1. Define a parent template placed in views/template.php
    ```html
    <html>
    <head>
        <title>Example</title>
    </head>
    <body>
        @section('content')
    </body>
    </html>
    ```
2. Define a new view derived by this template:
    ```php
    @extends('template.php')

    @begin('content')
    <p>Hello View!</p>
    @end
    ```
3. The output will be this:
    ```html
    <html>
    <head>
        <title>Example</title>
    </head>
    <body>
        <p>Hello View</p>
    </body>
    </html>
    ```
