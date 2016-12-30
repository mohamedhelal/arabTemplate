<?php
/**
 * -----------------------------------
 * File  : BaseTemplate.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArTemplate;


abstract class BaseTemplate extends DataTemplate
{
    /**
     * @var string
     */
    protected $template_dir;
    /**
     * @var string
     */
    protected $compiler_dir;
    /**
     * @var string
     */
    protected $cache_dir;
    /**
     * @var BaseTemplate
     */
    protected $parent;
    /**
     * @var ArTemplate
     */
    static public $arTemplate;
    /**
     * @var bool
     */
    protected $caching = false;
    /**
     * @var bool
     */
    protected $leftTime = false;
    /**
     * all models shortcut
     * @var array
     */
    protected static $modules = [];
    /**
     * set function names
     * @var array
     */
    protected static $functions = [];
    /**
     * default file ext
     * @var string
     */
    protected $ext ='.tpl';
    /**
     * all templates objects
     * @var array
     */
    protected static $templates = [];
    /**
     * @param string $ext
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
    }

    /**
     * set alias name to function
     * @param $name
     * @param callable $callable
     * @return $this
     */
    public function setFunction($name,callable $callable){
        static::$functions[$name] = $callable;
        return $this;
    }

    /**
     * check if function alias exists or not
     * @param $name
     * @return bool
     */
    public function functionExists($name){
        return (array_key_exists($name,static::$functions));
    }
    /**
     * call function by alias name
     * @param $name
     * @param array $args
     * @return mixed
     */
    public function getFunctionTpl($name,$args = []){
        return call_user_func_array(static::$functions[$name],$args);
    }
    /**
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * set module path
     * @param $module
     * @param $path
     * @return $this
     */
    public  function setModules($module,$path)
    {
        self::$modules[$module] = $path;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getCacheDir()
    {
        return $this->cache_dir;
    }

    /**
     * @return mixed
     */
    public function getCompilerDir()
    {
        return $this->compiler_dir;
    }

    /**
     * @return mixed
     */
    public function getTemplateDir()
    {
        return $this->template_dir;
    }

    /**
     * @return BaseTemplate
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return boolean
     */
    public function isCaching()
    {
        return $this->caching;
    }

    /**
     * @return boolean
     */
    public function isLeftTime()
    {
        return $this->leftTime;
    }
    /**
     * @param mixed $cache_dir
     */
    public function setCacheDir($cache_dir)
    {
        $this->cache_dir = $cache_dir;
    }

    /**
     * @param mixed $template_dir
     */
    public function setTemplateDir($template_dir)
    {
        $this->template_dir = $template_dir;
    }

    /**
     * @param mixed $compiler_dir
     */
    public function setCompilerDir($compiler_dir)
    {
        $this->compiler_dir = $compiler_dir;
    }

    /**
     * @param BaseTemplate $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param ArTemplate $arTemplate
     */
    public function setArTemplate($arTemplate)
    {
        static::$arTemplate = $arTemplate;
    }

    /**
     * @param boolean $caching
     */
    public function setCaching($caching)
    {
        $this->caching = $caching;
    }

    /**
     * @param boolean $leftTime
     */
    public function setLeftTime($leftTime)
    {
        $this->leftTime = $leftTime;
    }

    /**
     * get template object
     * @param $template
     * @param string $type
     * @param array $data
     * @param bool $leftTime
     * @param null $parent
     * @return FileTemplate|mixed
     */
    protected function getTemplateObject($template,$data = [] ,$leftTime = false,$type = ArTemplate::file,$parent = null){
        if($parent == null){
            $parent = $this;
        }
        $parent_vars = $parent->varTpl;
        if(array_key_exists($template,static::$templates)){
            $tpl = static::$templates[$template];
            $parent = $tpl->getParent();
        }else{
            $tpl = new FileTemplate($template,$type,$leftTime,$parent);
        }
        $tpl->with($parent_vars);
        $tpl->with($parent->shared);
        $tpl->with($data);
        return $tpl;
    }

    /**
     * check if template exists
     * @param $template
     * @param string $type
     * @return bool
     */
    public function isExists($template,$type = ArTemplate::file){
        return (new FileTemplate($template,$type,false,static::$arTemplate))->exists();
    }
    /**
     * @param $template
     * @param string $type
     * @param array $data
     * @param bool $leftTime
     * @return FileTemplate|mixed
     */
    public function render($template,$data = [] ,$leftTime = false,$type = ArTemplate::file){
        return static::$arTemplate->createTemplate($template,$data,$leftTime,$type,$this);
    }

    /**
     * @param $template
     * @param string $type
     * @param array $data
     * @param bool $leftTime
     * @param bool $display
     * @return string
     */
    protected function executes($template,$data = [] ,$leftTime = false,$type = ArTemplate::file,$display = true){
        $template = $this->render($template,$data,$leftTime,$type);
        return $template->getContent($display);
    }

    /**
     * @param $template
     * @param string $type
     * @param array $data
     * @param bool $leftTime
     */
    public function display($template,$data = [] ,$leftTime = false,$type = ArTemplate::file){
        $this->executes($template,$data,$leftTime,$type,true);
    }

    /**
     * @param $template
     * @param string $type
     * @param array $data
     * @param bool $leftTime
     * @return string
     */
    public function fetch($template,$data = [] ,$leftTime = false,$type = ArTemplate::file){
        return $this->executes($template,$data,$leftTime,$type,false);
    }

    /**
     * @param $template
     * @param string $type
     * @param array $data
     * @param bool $leftTime
     */
    public function extendTemplate($template,$data = [] ,$leftTime = false,$type = ArTemplate::file){
        $template = static::$arTemplate->createTemplate($template,$type,$data,$leftTime);
        $template->getContent(true);
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}