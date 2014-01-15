<?php
/**
 #--------------------------------------------------------------------------------------
 # نظام قوالب القالب العربى نظام مجانى   لفصل الكود عن البرمجة لتسهيل عمل  المصميمبن و التسهيل على المطورين تطوير برمجياتهم
 # وممكن ان تقوم بتعديل على الكود كما حب و ستخدمة فى برامجك المجانية و المدفوعة و ممكن ان تقوم بتطويرة و التعديل علة كما تحب
 #--------------------------------------------------------------------------------------
 #  @package    :  ArabTemplate the PHP compiling template engine
 #	@version	: 8
 #  @author		: Mohamed Helal<mohamedhelal123456@gmail.com>
 #  @copyright  : Mohamed Helal 2010 - 2014
 #--------------------------------------------------------------------------------------
 */
require 'arabTemplate.php';
$artpl = new ArabTemplate();
$artpl->caching = false;
$artpl->setTemplateDir('templates');
$artpl->setCompileDir('compilers');
$artpl->setCacheDir('caches');
//$artpl->allow_output_file();
//$artpl->get_output_file();
class MyTest
{
	public static $Myname = "Mohamedhelal";
	public static $array  = ['names' => ['first' => 'Mohamed']];
	public static function getMyName($val)
	{
		return $val;
	}
}
$artpl->setFunction('ReturnArray', 'MyTest::getMyName');
$rows = array();
for ($i = 1 ;$i < 10;$i++)
{
	$rows[] = ['first' => 'Mohamed-'.$i,'last' => 'Helal - '.$i,'id' => $i];
}
$artpl->assign('rows',$rows);
$artpl->assign('obj', 'MyTest' );
$artpl->display('index');

