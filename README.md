بسم الله الرحمن الرحيم
==============
السلام عليكم ورحمة الله وبركاتة
نظام قوالب القالب العربى النسخة 8 تم اعادة برمجتها من الصفر و تطويرها فى ارجو من يقوم بتجربتها  بقوم بكتابة الاخطاء التى ظهرة ليه

* * *

بسم الله 

* * *

اولا استدعاء الكلاس و انشاء نسخة من الكلاس

* * *

```php
require 'arabTemplate.php';
```
// انشاء نسخة من الكلاس

```php
$artpl = new ArabTemplate();
```
// تعطبل او تفعيل خاصة الكاش

```php
$artpl->caching = false;
```
// اضافة مجلد القوالب

```php
$artpl->setTemplateDir('templates');
```
// اضافة مجلد الملفات التى تم تحويلها

```php
$artpl->setCompileDir('compilers');
```
// اضافة مجلد الكاش

```php
$artpl->setCacheDir('caches');
```
* * *
استدعاء القوالب

```php
$artpl->display('index');
```

// or 

```php
echo $artpl->fetch('index');
```
* * * 
تمرير المتغيرات للقالب

```php
$artpl->assign('obj', 'MyTest' );
```

استخدام المتغيرات داخل القالب

```php
{{$var}}
```

استخدام المصفوفات داخل القالب

```php
{{$row.key}}
{{$row[key]}}
{{$row[$key.name]}}
```

استخدام الكلاسات داخل القالب

```php
{{$obj->property}}
{{MyClass::$property}}
{{MyClass::$property.key.name}}
{{$obj::$property}}
{{$obj::$property.key.name}}
```

استخدام الدوال فى القالب

```php
{{myName($row,'mohamed')}}
{{$obj->method('name')}}
{{MyClass::method('name')}}
{{$obj::method('name')}}
```
* * *
استخدام الكلاس داخل القالب



مثال على الكلاس

```php
class MyTest
{
    public static $Myname = "Mohamedhelal";
    public static $array  = array('names' => array('first' => 'Mohamed'));
    public static function setMyName($val)
    {
        self::$Myname = $val;
        return new self();
    }
    public function getThis()
    {
        return $this;
    }
    public function getName()
    {
        return self::$Myname;
    }
}
```

وداخل القالب
```php
{{$obj::setMyName('Mohamed')->getThis()->getThis()->getThis()->getThis()->getName()}}

```

او


```php
{{MyTest::setMyName('Mohamed')->getThis()->getThis()->getThis()->getThis()->getName()}}

```



* * *
استدعاء قوالب داخل القالب

```php
{{include file="index" caching}}
// تمرير
{{include file="index" title="MyPageTitle" caching}}

{{include file=$filename title="MyPageTitle" caching}}
```


استدعاء القوالب من مجلدات الموديلات

```php
$artpl->setModuleDir('test', dirname(__FILE__).'/modules/test/views/');
$artpl->setModuleDir('users', dirname(__FILE__).'/modules/users/views/');

```

عرض قالب من مجلد الموديل

```php
$artpl->display('test::index');
$artpl->display('users::index');

```

او استدعاء قالب داخل  القالب من الموديل

```php

{{include file="test::index" title="MyPageTitle" caching}}
{{include file="users::index" title="MyPageTitle" caching}}

```

استخدام البلوكات 

وتحويل محتوى النص الى قالب للعرض بالمحتوى الى فيه

مثال
كود صفحة ال php
```php 
$rows = array();
for ($i = 1 ;$i < 10;$i++)
{
	$rows[] = (object)array(
                     'first' => 'Mohamed-'.$i,
                    'last' => 'Helal - '.$i,
                    'id' => $i,
                    'image' => 'MyImage',
                    'code' =>'
                        <h1>Code Compiled {{$row_file->first}}</h1>
                        {{foreach $rows as $row}}
{{$row->first}}<br/>
{{myName(($row->first == \'mohamed\'?$row->first:\'mohamed\'),($row->last == \'helal\'?$row->first:\'helal\'))}}
{{/foreach}}',
                    'lastupdate' =>(time()-(60*60))
            );
}
$artpl->assign('rows',$rows);
```
كود قالب html

```code
{{foreach $rows as $row_file}}
{{$_artpl->evalCode($row_file->first,$row_file->code,$row_file->lastupdate)}}
{{/foreach}}
```

انشاء المتغيرات فى القالب

```php
{{$name = 'mohamed helal'}}
{{$name = getTemplateVars('name')}}
{{$i = 2}}
{{++$i}}
{{--$i}}
{{$i *= 2}}
{{assign var="my" value=" MyTest::$array.names.first"}}
```

استخدام داله باسم اخر  فى القالب

```php

$artpl->setFunction('ReturnArray', 'MyTest::getMyName');
{{ReturnArray($rows)}}
{{$myfunc = ReturnArray($rows)}}
```
استخدام الداله داخل القالب و مع عدم طباعتها

```php

{{|function_name($var,...)|}}
```

انشاء داله داخل القالب

```php

 
        {{function createMenuMapList($row,$mylinks)}}
        	{{$row->name}} || {{$mylinks}}
        {{/function}}
        
```

استدعاء الداله التى تم انشائها داخل القالب

```php
{{createMenuMapList($row,$mylinks)}}
```
كتابة كود phpداخل القالب

```php
{{php}}
	$var ='myCodeTest';
	echo $var ;
{{/php}}
```

		
استخدام الوب foreach

```php
{{foreach $rows as $row}}
	{{$row@key}}
   {{foreachelse}{
{{/foreach}}

{{foreach $rows as $key => $val}}
   {{foreachelse}}
{{/foreach}}
```

عمل تكرار بال key => val

```php

{{foreach $rows as $key => $val}}
   {{foreachelse}}
{{/foreach}}
```

استخدام متغير الكائن
```php

{{foreach $rows as $row}}
   {{$row@index}}
   {{$row@first}}
   {{$row@last}}
   {{$row@first}}
   
   {{$rows@count()}}
   
   {{$row@is_div_by(2)}}
   
   {{$row@is_even_by(2)}}
   
{{/foreach}}
```



استخدام for

```php
	{{for $i = 0;$i < 10;$i++}}
		{{$i}}
	{{/for}}
```

استخدام for متعدده

```php
	{{for $i = 0,$j = 0;$i < 10,$j < 10;$i++,$j+=2}}
		{{$i}}
		{{$j}}
	{{/for}}
```
استخدام break|continue
```php 
{{break|continue}}
```


استخدام الشروط داخل القالب

```php
{{if $name =="mohamed"}}
// do same thing
{{elseif $name =="helal"}}
// do same thing
{{else}}
// do same thing
{{/if}}

```


استخدام الشروط القصيرة

```php
{{$var == 'mohamed'?true:false}}
```
 دمج المتغيرات
```php
{{$var."MohamedHelal"}}
```

التعليقات
```php
{{*
	// تعليقات  لن يتم معلجنها
	{{$var}}
*}}
```


عمل وراثة للقالب

parent.tpl

```php
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>{{extend_header}}My Default Page  Title {{/extend}}</title>
</head>
<body>
	{{extend_body}}
		My Default Page  Content
	{{/extend}}
</body>
</html>

```
son.tpl

لازم يكون ال content
هو نفسة الى فى  ملف parent.tpl
extend_body = body
او سوفا يظهر خطاء
```php

{{extends file="parent"}}
{{content name = "header"}}
	My Extend Page Header
{{/content}}


{{content name = "body"}}
	My Extend Page Content
{{/content}}
```

الناتج

```php
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>
	My Extend Page Header
</title>
</head>
<body>
	
	My Extend Page Content

</body>
</html>
```

التحقق من وجود ملف الكاش

```php
	if($artpl->isCached('index'))
	{
		// do same thing
	}
	$artpl->display('index');
```




استخدام  القوالب من قاعدة البيانات


```php
// تمرير داله جلب القالب من قاعدة البيانات و ارجاع القيم المطلوبة
$artpl->setResource(function($name){
	$query = mysql_query("select from thems where style ='main' and name ='$name'");
	$row = mysql_fetch_assoc($query);
	return array('code' => $row['htmlcontent'],'lastupdate' => $row['lastupdate']);
});
```

خاصية الكاش عند تفعيل الكاش هيعمل كاش للناتج المعروض فى المتصفح
للكل ملف ولكن فى خاصية جديدة وهى انك ممكن تعمل كاش للصفحة  كلها  فى صفحة واحدة




تفعيل هذة الخاصية

```php
// تفعيل خاصة كاش لجميع الملفات فى ملف واحد
$artpl->allow_output_file();

```
وعند تفعيل هذة الخاصية عليك استخدام الداله التالية
هذه الداله مهمتها استدعاء ملف الكاش و عدم تنفيذ اى كود 
من بعد هذة الداله يعنى هيستدعى ملف الكاش  الى هو ملف واحد  و اى كود استعلام او طباعة او الخ لن يتم استدعاءة

```php
// استدعاء هذا الملف  
$artpl->get_output_file();

```
