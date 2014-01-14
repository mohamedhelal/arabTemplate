��� ���� ������ ������
������ ����� ����� ���� �������
���� ����� ������ ������ ������ 8 �� ����� ������� �� ����� � ������� �� ���� �� ���� ��������  ���� ������ ������� ���� ���� ���

��� ���� 

���� ������� ������ � ����� ���� �� ������
[php]
require 'arabTemplate.php';
// ����� ���� �� ������
$artpl = new ArabTemplate();
// ����� �� ����� ���� �����
$artpl->caching = false;
// ����� ���� �������
$artpl->setTemplateDir('templates');
// ����� ���� ������� ���� �� �������
$artpl->setCompileDir('compilers');
// ����� ���� �����
$artpl->setCacheDir('caches');
[/php]
������� �������
[php]
$artpl->display('index.tpl');
// or 
echo $artpl->fetch('index.tpl');
[/php]
����� ��������� ������
[php]
$artpl->assign('obj', 'MyTest' );
[/php]

������� ��������� ���� ������
[php]
{$var}
������� ��������� ���� ������
[php]

{$row.key}
{$row[key]}
{$row[$key.name]}
[/php]

������� �������� ���� ������

[php]
{$obj->property}
{MyClass::$property}
{MyClass::$property.key.name}
{$obj::$property}
{$obj::$property.key.name}
[/php]
������� ������ �� ������
[php]
{myName($row,'mohamed')}
{$obj->method('name')}
{MyClass::method('name')}
{$obj::method('name')}}
[/php]

������� ����� ���� ������
[php]
{include file="index.tpl" caching}
// �����
{include file="index.tpl" title="MyPageTitle" caching}
[/php]
����� ��������� �� ������

[php]
{$name = 'mohamed helal'}
{$name = getTemplateVars('name')}
{$i = 2}
{++$i}
{--$i}
{$i *= 2}
{assign var="my" value=" MyTest::$array.names.first"}
[/php]

������� ���� ���� ���  �� ������

[php]

$artpl->setFunction('ReturnArray', 'MyTest::getMyName');
{ReturnArray($rows)}
{$myfunc = ReturnArray($rows)}
[/php]
����� ��� php���� ������
[php]
{php}
	$var ='myCodeTest';
	echo $var ;
{/php}

[/php]

		
������� ���� foreach
[php]

{foreach $rows as $row}
	{$row@key}
   {foreachelse}
{/foreach}

{foreach $rows as $key => $val}
   {foreachelse}
{/foreach}
[/php]
������� for
[php]
	{for $i = 0;$i < 10;$i++}
		{$i}
	{/for}
[/php]

������� for ������

[php]
	{for $i = 0,$j = 0;$i < 10,$j < 10;$i++,$j+=2}
		{$i}
		{$j}
	{/for}
[/php]

������� ������ ���� ������

[php]
{if $name =="mohamed"}
// do same thing
{elseif $name =="helal"}
// do same thing
{else}
// do same thing
{/if}

[/php]
������ �� ���� ��� �����
[php]
	if($artpl->isCached('index.tpl'))
	{
		// do same thing
	}
	$artpl->display('index.tpl');
[/php]


�������  ������� �� ����� ��������


[php]
// ����� ���� ��� ������ �� ����� �������� � ����� ����� ��������
$artpl->setResource(function($name){
	$query = mysql_query("select from thems where style ='main' and name ='$name'");
	$row = mysql_fetch_assoc($query);
	return array('code' => $row['htmlcontent'],'lastupdate' => $row['lastupdate']);
});
[/php]

����� ����� ��� ����� ����� ����� ��� ������ ������� �� �������
���� ��� ���� �� ����� ����� ��� ��� ���� ���� ��� ������  ����  �� ���� �����




����� ��� �������

[php]
// ����� ���� ��� ����� ������� �� ��� ����
$artpl->allow_output_file();

[/php]
���� ����� ��� ������� ���� ������� ������ �������
��� ������ ������ ������� ��� ����� � ��� ����� �� ��� 
�� ��� ��� ������ ���� ������� ��� �����  ��� �� ��� ����  � �� ��� ������� �� ����� �� ��� �� ��� ��������
[php]
// ������� ��� �����  
$artpl->get_output_file();

[/php]










