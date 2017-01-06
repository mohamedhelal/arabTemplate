<?php
namespace ArTemplate;
/**
* ArTemplate Class 
* 
* Arab Template software Free software to separate the code for programming,
* to facilitate the work of designers and make it easier for developers to develop their software
* And possible to modify the code as love and serve him in your software free and paid,
* and possible that is being developed and edited the bug as you like
* ---------------------------------------------------------------------------------
* نظام قوالب القالب العربى نظام مجانى لفصل الكود عن البرمجة لتسهيل عمل  المصممين
* و التسهيل على المطورين تطوير برمجياتهم
* وممكن ان تقوم بتعديل على الكود كما حب و ستخدمة فى برامجك المجانية و المدفوعة 
* و ممكن ان تقوم بتطويرة و التعديل علة كما تحب
* ---------------------------------------------------------------------------------
* @package    ArTemplate the PHP compiling template engine
* @subpackage BaseTemplate
* @author     Mohamed Helal <mohamedhelal123456@gmail.com>
* @copyright  Mohamed Helal 2010 - 2016
* @license    MIT
* @see        https://github.com/mohamedhelal/arabTemplate/
*/
class ArTemplate extends BaseTemplate
{
    /**
     * ar template version number
     */
    const version = 10;
    const file = 'file';
    const string = 'string';
    const left = '{%';
    const right = '%}';

    /**
     * ArTemplate constructor.
     */
    public function __construct(array $config = null)
    {
        if($config)
        {
            // add simple config to use in engine. it well be need php >=v7.0.0
            $this->template_dir = $config['template-folder'] ?? null;
            $this->compiler_dir = $config['compiled-folder'] ?? null;
            $this->cache_dir    = $config['cache-folder'] ?? null;
            $this->caching      = $config['caching'] ?? false;
        }

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
