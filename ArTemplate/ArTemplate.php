<?php
/**
#--------------------------------------------------------------------------------------
# نظام قوالب القالب العربى نظام مجانى   لفصل الكود عن البرمجة لتسهيل عمل  المصميمبن و التسهيل على المطورين تطوير برمجياتهم
# وممكن ان تقوم بتعديل على الكود كما حب و ستخدمة فى برامجك المجانية و المدفوعة و ممكن ان تقوم بتطويرة و التعديل علة كما تحب
#--------------------------------------------------------------------------------------
#  @package    :  ArabTemplate the PHP compiling template engine
#	@version	: 10
#  @author		: Mohamed Helal<mohamedhelal123456@gmail.com>
#  @copyright  : Mohamed Helal 2010 - 2016
#--------------------------------------------------------------------------------------
 */
/**
 * -----------------------------------
 * File  : ArTemplate.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */




namespace ArTemplate;


class ArTemplate extends BaseTemplate
{
    /**
     * ar template version number
     */
    const version = 10;
    const file = 'file';
    const string = 'string';
    const left = '{{';
    const right = '}}';
    /**
     * ArTemplate constructor.
     */
    public function __construct()
    {
        define('ArTemplate',true);
        if(!defined('DS')){
            define('DS',DIRECTORY_SEPARATOR);
        }
        if (!defined('DIR_WRITE_MODE')) {
            define('DIR_WRITE_MODE', 0777);
        }

        if (!defined('FILE_WRITE_MODE')) {
            define('FILE_WRITE_MODE', 0666);
        }
        static::$arTemplate = $this;
        $this->with('_ar_tpl',$this);
        include_once('plugins'.DS.'functions.php');
    }

    /**
     * create template object
     * @param $template
     * @param string $type
     * @param array $data
     * @param bool $leftTime
     * @param null $parent
     * @return FileTemplate|mixed
     */
    public function createTemplate($template,$data = [] ,$leftTime = false,$type = ArTemplate::file,$parent = null){
        $tpl = $this->getTemplateObject($template,$data,$leftTime,$type ,$parent);
        $tpl->process();
        return $tpl;
    }

   
}