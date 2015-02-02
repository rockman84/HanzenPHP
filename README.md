HanzenPHP
=========
HanzenPHP is extended third_party for CodeIgniter

##Features:
-Improvement writing code
-easy error handling
-plugin class
-before and after controller execute
-many more...

##Requirements

-CodeIgniter V2.1.4 or more


##How To Install

-copy 'hanzen_php' folder into 'application/third_pary/'
-copy all files inside core folder into 'application/core/'
-optional*: open 'application/core/HP_Controller.php' make sure 'HP_PATH' correct path location of hanzen_php folder. if already correct path, you can skip to next step.
-open config.php file in 'application/config/' and change line 109 to $config['subclass_prefix'] = 'HP_';
-create every controller with <b>extend HP_Controller not CI_Controller</b> anymore


##Example

###Create Controller:
homepage.php
```
<?php
// use HP_Controller not CI_Controller
class Homepage class HP_Controller{
  function index(){
    // your code here
  }
}
```
###Create Model:
my_data.php
```
// class need surfix name _Model
class My_data_Model extends HP_Model{
  function my_method(){
  
  }
}
```

### Create Plugin:
foo_plugin.php
```
// class need surfix name _Plugin
class Foo_plugin extends HP_Plugin{
  function bar(){
    // your code here
    // your cant use all features same as controller;
  }
}
```
###Load Plugin:
```
get_plugin('your_plugin_name')->method();
```
###Load Library:
```
get_library('your_library_name')->your_method();
```

###Load Model:
```
get_model('your_model_name')->your_method();
```

###Load Helper:
```
get_helper('your_helper_name');
```

##Documentation
-<a href="http://www.rockbeat.web.id/api_reference/hanzenphp" >API Referance </a>

&copy; 2014 | Wong Hansen
