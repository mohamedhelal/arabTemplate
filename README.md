بسم الله الرحمن الرحيم
==============
السلام عليكم ورحمة الله وبركاتة
نظام قوالب القالب العربى النسخة 10 تم اعادة برمجتها من الصفر و تطويرها فى ارجو من يقوم بتجربتها  بقوم بكتابة الاخطاء التى ظهرة ليه

* * *

بسم الله 

* * *

اولا التثبيت بواسطة مدير الحزم `composer`

* * *

```bash
composer require mohamedhelal/arabtemplate
```
// انشاء نسخة من الكلاس

```php
$artpl = new \ArTemplate\ArTemplate([
    // اضافة مجلد القوالب
    'template-folder' => realpath('path'),
    // مجلد الملفات المحولة
    'compiled-folder' => realpath('path'),
    // تفعيل وإلغاء الكاش
    'caching'         => false,
    // مجلد ملفات الكاش
    'cache-folder'    => realpath('path')
]);
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
$artpl->with('obj', 'MyTest' );
```

استخدام المتغيرات داخل القالب

```php
{% $var %}
```

استخدام المصفوفات داخل القالب

```php
{% $row.key %}
{% $row[key] %}
{% $row[$key.name] %}
```

استخدام الكلاسات داخل القالب

```php
{% $obj->property %}
{% MyClass::$property %}
{% MyClass::$property.key.name %}
{% $obj::$property %}
{% $obj::$property.key.name %}
```

استخدام الدوال فى القالب

```php
{% myName($row,'mohamed') %}
{% $obj->method('name') %}
{% MyClass::method('name') %}
{% $obj::method('name') %}
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
{% $obj::setMyName('Mohamed')->getThis()->getThis()->getThis()->getThis()->getName() %}

```

او


```php
{% MyTest::setMyName('Mohamed')->getThis()->getThis()->getThis()->getThis()->getName() %}

```



* * *
استدعاء قوالب داخل القالب

```php
{% include file="index" %}


{% include 'index'  %}
{% include $var  %}
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

{% include file="test::index" %}
{% include $var %}

```


انشاء المتغيرات فى القالب

```php
{% $name = 'mohamed helal' %}
{% $i = 2 %}
{% ++$i %}
{% --$i %}
{% $i *= 2 %}
{% assign('my','value') %}
{% with('my','value') %}
```

استخدام داله باسم اخر  فى القالب

```php

$artpl->setFunction('ReturnArray', 'MyTest::getMyName');
{% ReturnArray($rows) %}
{% $myfunc = ReturnArray($rows) %}
```
استخدام الداله داخل القالب و مع عدم طباعتها

```php

{% |function_name($var,...)| %}
```

انشاء داله داخل القالب

```php

 
        {% function createMenuMapList($row,$mylinks) %}
        	{% $row->name %} || {% $mylinks %}
        {% /function %}
        
```

استدعاء الداله التى تم انشائها داخل القالب

```php
{% createMenuMapList($row,$mylinks) %}
```

		
استخدام الوب foreach

```php
{% foreach $rows as $row %}
	{% $row@key %}
   {% foreachelse}{
{% /foreach %}

{% foreach $rows as $key => $val %}
   {% foreachelse %}
{% /foreach %}
```

عمل تكرار بال key => val

```php

{% foreach $rows as $key => $val %}
   {% foreachelse %}
{% /foreach %}
```

استخدام متغير الكائن
```php

{% foreach $rows as $row %}
   {% $row@index %}
   {% $row@first %}
   {% $row@last %}
   {% $row@first %}
   
   {% $rows@count() %}
   
   {% $row@is_div_by(2) %}
   
   {% $row@is_even_by(2) %}
   
{% /foreach %}
```



استخدام for

```php
	{% for $i = 0;$i < 10;$i++ %}
		{% $i %}
	{% /for %}
```

استخدام for متعدده

```php
	{% for $i = 0,$j = 0;$i < 10,$j < 10;$i++,$j+=2 %}
		{% $i %}
		{% $j %}
	{% /for %}
```
استخدام break|continue
```php 
{% break|continue %}
```


استخدام الشروط داخل القالب

```php
{% if $name =="mohamed" %}
// do same thing
{% elseif $name =="helal" %}
// do same thing
{% else %}
// do same thing
{% /if %}

```


استخدام الشروط القصيرة

```php
{% $var == 'mohamed'?true:false %}
```
 دمج المتغيرات
```php
{% $var ."MohamedHelal" %}
```

التعليقات
```php
{%*
	// تعليقات  لن يتم معلجنها
	{% $var %}
*%}
```


عمل وراثة للقالب

parent.tpl

```php
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>{% block 'header' %}My Default Page  Title {% /block %}</title>
</head>
<body>
	{% block 'body' %}
		My Default Page  Content
	{% /block %}
</body>
</html>

```
son.tpl

```php

{% extends file="parent" %}
{% extends "parent" %}
{% extends $layout %}

{% block "header" %}
	My Extend Page Header
{% /block %}


{% block "body" %}
	My Extend Page Content
{% /block %}
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
