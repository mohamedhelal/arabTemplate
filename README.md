بسم الله الرحمن الرحيم
==============
السلام عليكم ورحمة الله وبركاتة
نظام قوالب القالب العربى النسخة 8 تم اعادة برمجتها من الصفر و تطويرها فى ارجو من يقوم بتجربتها  بقوم بكتابة الاخطاء التى ظهرة ليه

* * *

بسم الله 

* * *

اولا استدعاء الكلاس و انشاء نسخة من الكلاس

* * *

```code
require 'arabTemplate.php';
```
// انشاء نسخة من الكلاس

```code
$artpl = new ArabTemplate();
```
// تعطبل او تفعيل خاصة الكاش

```code
$artpl->caching = false;
```
// اضافة مجلد القوالب

```code
$artpl->setTemplateDir('templates');
```
// اضافة مجلد الملفات التى تم تحويلها

```code
$artpl->setCompileDir('compilers');
```
// اضافة مجلد الكاش

```code
$artpl->setCacheDir('caches');
```

استدعاء القوالب

```code
$artpl->display('index');
```

// or 

```code
echo $artpl->fetch('index');
```

تمرير المتغيرات للقالب

```code
$artpl->assign('obj', 'MyTest' );
```

استخدام المتغيرات داخل القالب

```code
{$var}
```

استخدام المصفوفات داخل القالب

```code
{{$row.key}}
{{$row[key]}}
{{$row[$key.name]}}
```

استخدام الكلاسات داخل القالب

```code
{{$obj->property}}
{{MyClass::$property}}
{{MyClass::$property.key.name}}
{{$obj::$property}}
{{$obj::$property.key.name}}
```

استخدام الدوال فى القالب

```code
{{myName($row,'mohamed')}}
{{$obj->method('name')}}
{{MyClass::method('name')}}
{{$obj::method('name')}}
```

استدعاء قوالب داخل القالب

```code
{{include file="index" caching}}
// تمرير
{{include file="index" title="MyPageTitle" caching}}
```

انشاء المتغيرات فى القالب

```code
{{$name = 'mohamed helal'}}
{{$name = getTemplateVars('name')}}
{{$i = 2}}
{{++$i}}
{{--$i}}
{{$i *= 2}}
{{assign var="my" value=" MyTest::$array.names.first"}}
```

استخدام داله باسم اخر  فى القالب

```code

$artpl->setFunction('ReturnArray', 'MyTest::getMyName');
{{ReturnArray($rows)}}
{{$myfunc = ReturnArray($rows)}}
```

كتابة كود phpداخل القالب

```code
{{php}}
	$var ='myCodeTest';
	echo $var ;
{{/php}}
```

		
استخدام الوب foreach

```code
{{foreach $rows as $row}}
	{{$row@key}}
   {{foreachelse}{
{{/foreach}}

{{foreach $rows as $key => $val}}
   {{foreachelse}}
{{/foreach}}
```

استخدام for

```code
	{{for $i = 0;$i < 10;$i++}}
		{{$i}}
	{{/for}}
```

استخدام for متعدده

```code
	{{for $i = 0,$j = 0;$i < 10,$j < 10;$i++,$j+=2}}
		{{$i}}
		{{$j}}
	{{/for}}
```
استخدام break|continue
```code 
{{break|continue}}
```


استخدام الشروط داخل القالب

```code
{{if $name =="mohamed"}}
// do same thing
{{elseif $name =="helal"}}
// do same thing
{{else}}
// do same thing
{{/if}}

```


استخدام الشروط القصيرة

```code
{{$var == 'mohamed'?true:false}}
```
 دمج المتغيرات
```code
{{$var."MohamedHelal"}}
```



عمل وراثة للقالب

parent.tpl

```code
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
```code

{{extends file="parent"}}
{{content name = "header"}}
	My Extend Page Header
{{/content}}


{{content name = "body"}}
	My Extend Page Content
{{/content}}
```

الناتج

```code
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

```code
	if($artpl->isCached('index'))
	{
		// do same thing
	}
	$artpl->display('index');
```




استخدام  القوالب من قاعدة البيانات


```code
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

```code
// تفعيل خاصة كاش لجميع الملفات فى ملف واحد
$artpl->allow_output_file();

```
وعند تفعيل هذة الخاصية عليك استخدام الداله التالية
هذه الداله مهمتها استدعاء ملف الكاش و عدم تنفيذ اى كود 
من بعد هذة الداله يعنى هيستدعى ملف الكاش  الى هو ملف واحد  و اى كود استعلام او طباعة او الخ لن يتم استدعاءة

```code
// استدعاء هذا الملف  
$artpl->get_output_file();

```
