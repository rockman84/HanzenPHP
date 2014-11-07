HanzenPHP
=========
HanzenPHP is extended third_party for CodeIgniter

<h2>Features:</h2>
<ul>
<li>easy load class with simple function</li>
<li>easy error handling</li>
<li>changed writing code for Model class with surfix '_Model'</li>
<li>added plugin library</li>
<li>added before and after controller execute</li>
<li>many more...</li>
</ul>

<h2>how to install</h2>
<ul>
<li>copy 'hanzen_php' folder into 'application/third_pary/'</li>
<li>copy all files inside core folder into 'application/core/'</li>
<li>optional*: open 'application/core/HP_Controller.php' make sure 'HP_PATH' correct path location of hanzen_php folder. if already correct path, you can skip to next step.</li>
<li>open config.php file in 'application/config/' and change line 109 to $config['subclass_prefix'] = 'HP_';</li>
<li>create every controller with <b>extend HP_Controller not CI_Controller</b> anymore</li>
<li>read user guide</li>
</ul>

&copy; 2014 | Wong Hansen
