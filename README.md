بسم الله الرحمن الرحيم
السلام عليكم ورحمة الله وبركاتة
نظام قوالب القالب العربى النسخة 8 تم اعادة برمجتها من الصفر و تطويرها فى ارجو من يقوم بتجربتها  بقوم بكتابة الاخطاء التى ظهرة ليه

بسم الله 

اولا استدعاء الكلاس و انشاء نسخة من الكلاس
[php]
require 'arabTemplate.php';
// انشاء نسخة من الكلاس
$artpl = new ArabTemplate();
// تعطبل او تفعيل خاصة الكاش
$artpl->caching = false;
// اضافة مجلد القوالب
$artpl->setTemplateDir('templates');
// اضافة مجلد الملفات التى تم تحويلها
$artpl->setCompileDir('compilers');
// اضافة مجلد الكاش
$artpl->setCacheDir('caches');
[/php]
استدعاء القوالب
[php]
$artpl->display('index.tpl');
// or 
echo $artpl->fetch('index.tpl');
[/php]
تمرير المتغيرات للقالب
[php]
$artpl->assign('obj', 'MyTest' );
[/php]

استخدام المتغيرات داخل القالب
[php]
{$var}
استخدام المصفوفات داخل القالب
[php]

{$row.key}
{$row[key]}
{$row[$key.name]}
[/php]

استخدام الكلاسات داخل القالب

[php]
{$obj->property}
{MyClass::$property}
{MyClass::$property.key.name}
{$obj::$property}
{$obj::$property.key.name}
[/php]
استخدام الدوال فى القالب
[php]
{myName($row,'mohamed')}
{$obj->method('name')}
{MyClass::method('name')}
{$obj::method('name')}}
[/php]

استدعاء قوالب داخل القالب
[php]
{include file="index.tpl" caching}
// تمرير
{include file="index.tpl" title="MyPageTitle" caching}
[/php]
انشاء المتغيرات فى القالب

[php]
{$name = 'mohamed helal'}
{$name = getTemplateVars('name')}
{$i = 2}
{++$i}
{--$i}
{$i *= 2}
{assign var="my" value=" MyTest::$array.names.first"}
[/php]

استخدام داله باسم اخر  فى القالب

[php]

$artpl->setFunction('ReturnArray', 'MyTest::getMyName');
{ReturnArray($rows)}
{$myfunc = ReturnArray($rows)}
[/php]
كتابة كود phpداخل القالب
[php]
{php}
	$var ='myCodeTest';
	echo $var ;
{/php}

[/php]

		
استخدام الوب foreach
[php]

{foreach $rows as $row}
	{$row@key}
   {foreachelse}
{/foreach}

{foreach $rows as $key => $val}
   {foreachelse}
{/foreach}
[/php]
استخدام for
[php]
	{for $i = 0;$i < 10;$i++}
		{$i}
	{/for}
[/php]

استخدام for متعدده

[php]
	{for $i = 0,$j = 0;$i < 10,$j < 10;$i++,$j+=2}
		{$i}
		{$j}
	{/for}
[/php]

استخدام الشروط داخل القالب

[php]
{if $name =="mohamed"}
// do same thing
{elseif $name =="helal"}
// do same thing
{else}
// do same thing
{/if}

[/php]
التحقق من وجود ملف الكاش
[php]
	if($artpl->isCached('index.tpl'))
	{
		// do same thing
	}
	$artpl->display('index.tpl');
[/php]


استخدام  القوالب من قاعدة البيانات


[php]
// تمرير داله جلب القالب من قاعدة البيانات و ارجاع القيم المطلوبة
$artpl->setResource(function($name){
	$query = mysql_query("select from thems where style ='main' and name ='$name'");
	$row = mysql_fetch_assoc($query);
	return array('code' => $row['htmlcontent'],'lastupdate' => $row['lastupdate']);
});
[/php]

خاصية الكاش عند تفعيل الكاش هيعمل كاش للناتج المعروض فى المتصفح
للكل ملف ولكن فى خاصية جديدة وهى انك ممكن تعمل كاش للصفحة  كلها  فى صفحة واحدة




تفعيل هذة الخاصية

[php]
// تفعيل خاصة كاش لجميع الملفات فى ملف واحد
$artpl->allow_output_file();

[/php]
وعند تفعيل هذة الخاصية عليك استخدام الداله التالية
هذه الداله مهمتها استدعاء ملف الكاش و عدم تنفيذ اى كود 
من بعد هذة الداله يعنى هيستدعى ملف الكاش  الى هو ملف واحد  و اى كود استعلام او طباعة او الخ لن يتم استدعاءة
[php]
// استدعاء هذا الملف  
$artpl->get_output_file();

[/php]










