<?php
/**
 * -----------------------------------
 * File  : index.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */
/**
 * autoload classes file
 */
spl_autoload_register(function ($class){
    $class = str_replace('\\',DIRECTORY_SEPARATOR,$class);
    require_once (__DIR__.DIRECTORY_SEPARATOR.$class.'.php');
});

/***
 * create new object
 */

$arbTpl = new \ArTemplate\ArTemplate();
$arbTpl->setCompilerDir('compilers');
$arbTpl->setTemplateDir('templates');
$arbTpl->setCacheDir('caches');
//$arbTpl->setCaching(true);
//$arbTpl->setLeftTime(60*60);
$arbTpl->setFunction('name',function ($var){
    return htmlentities($var);
});
$arbTpl->with('layout','layout3');
$arbTpl->with('var','<h1>Mohamed Hellal</h1>');
$arbTpl->display('index');
