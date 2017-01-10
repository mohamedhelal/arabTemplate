<?php
/**
 * -----------------------------------
 * File  : FileTemplate.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArTemplate;


class FileTemplate extends BaseTemplate
{
    /**
     * template name
     * @var string
     */
    protected $template;
    /**
     * full template path
     * @var string
     */
    protected $full_path;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var TemplateCompiler
     */
    protected $compiler;
    /**
     * @var TemplateCache
     */
    protected $cache;
    /**
     * this template blocks
     * @var array
     */
    protected $meta = [];
    /**
     * @var FileTemplate
     */
    protected static $instance;

    /**
     * FileTemplate constructor.
     * @param $template
     * @param $type
     * @param $leftTime
     * @param BaseTemplate $parent
     */
    public function __construct($template, $type, $leftTime, BaseTemplate $parent)
    {
        $this->template = $template;
        $this->type = $type;
        $this->setCacheDir($parent->getCacheDir());
        $this->setCompilerDir($parent->getCompilerDir());
        $this->setTemplateDir($parent->getTemplateDir());
        $this->setParent($parent);
        $this->setCaching($parent->isCaching());
        $this->setLeftTime(($leftTime == false ? $parent->isLeftTime() : $leftTime));
        $this->setExt($parent->getExt());
        if ($this->type == 'file' && strrpos($this->template, '.') === false) {
            $this->template = $this->template . $this->ext;
        }
        $this->compiler = new TemplateCompiler($this);
        $this->cache = new TemplateCache($this);
        static::$instance = $this;
    }

    /**
     * @param $extends
     */
    public function setParentExtends($extends){
        $this->compiler->setParentExtends($extends);
    }

    /**
     * @return mixed
     */
    public function getParentExtends(){
        return $this->compiler->getParentExtends();
    }
    /**
     * @return FileTemplate
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     */
    public function setMeta($meta)
    {
        if (is_array($meta)) {
            $this->meta = array_merge($this->meta, $meta);
        } else {
            $this->meta[] = $meta;
        }
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return $this->full_path;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        if ($this->type == ArTemplate::file) {
            if (is_file($file = rtrim($this->template_dir, DS) . DS . $this->template) || is_file($file = $this->template)) {
                $this->full_path = $file;
                return true;
            } else {
                if (strpos($this->template, '::')) {
                    $explode = explode('::', $this->template, 2);
                    $module = $explode[0];
                    $file = $explode[1];
                    if (array_key_exists($module, static::$modules)) {
                        $path = rtrim(static::$modules[$module], DS) . DS . $file;
                        if (is_file($path)) {
                            $this->full_path = $path;
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function source()
    {
        if ($this->type == 'file') {
            return file_get_contents($this->full_path);
        }
        return $this->template;
    }

    /**
     * create and call compiler file
     */
    public function process()
    {
        if ($this->exists()) {

            $compiler_name = $this->compiler->getName();
            $name = $this->full_path;
            if (!is_file($compiler_name) || (filemtime($name) > filemtime($compiler_name))) {
                $this->compiler->reCompiler();
            }
            $this->compiler->process();
            return true;
        }
        throw new TemplateException('Template File :"' . $this->template . '" Not Exists');
    }

    /**
     * @param bool $display
     * @return string
     */
    public function getContent($display = false)
    {
        if($this->caching === true && $this->cache->exists()){
            $cache = $this->cache->getName();
            $cacheTime = (filemtime($cache) + ($this->leftTime == false ? (7 * 24 * 60 * 60) : $this->leftTime));
            if($cacheTime >= time()){
                ob_start();
                require($cache);
                if($display == false){
                    return ob_get_clean();
                }
                return '';
            }else{
                $this->cache->reCreate();
            }

        }
        $_arTpl = $this;
        $callback = $this->compiler->callBackName();
        if (!function_exists($callback)) {
            ob_start();
            include_once($this->compiler->getName());
            ob_end_clean();
        }
        ob_start();
        $callback($this);
        $content = ob_get_clean();
        if($this->caching === true){
            $this->cache->create($content);
        }
        if ($display === true) {
            echo $content;
        } else {
            return $content;
        }
    }

    /**
     * get content
     * @return string
     */
    public function __toString()
    {
        return $this->getContent(false);
    }

}