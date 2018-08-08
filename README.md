# Pure Template Component
Simple and fast template engine
# How To render a view
1. Define the default path in which the engine will search for view files:
    ```php
    Pure\Template\View::namespace( $default_path );
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
        $filename,              // the file palced inside the base path
        $direct_output = true,   // if true, the output is displayed
        $dont_compute = false    // if true, no engine extensions are applied
    );
    ```

6. Instead of instantiate the view object, it is possibile to directly output a view by a static function:
    ```php
    Pure\Template\View::make(
        $filename,
        $params = array(),
        $direct_output = true,
        $dont_compute = false
    );
    ```

7. How to locate views in different paths and render them using namespaces

    It is possible to define several namespaces, each namespace refers to a certain path

    ```php
    Pure\Template\View::namespace('path/views');                // define the base namespace
    Pure\Template\View::namespace('path/views/auth', 'auth');    // define the auth namespace
    ```

    Once the namespaces are defined, it is possible to load views using the syntax 

    ```php
    "namespace::view_filename"
    ```

    For example:

    ```php
    Pure\Template\View::make('welcome.php');        // file: path/views/welcome.php
    Pure\Template\View::make('auth::login.php');    // file: path/views/auth/login.php
    ```


##### Simple example

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
    View::namespace('views');
    
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


##### Practical example

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