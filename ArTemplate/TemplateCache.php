<?php
/**
 * -----------------------------------
 * File  : TemplateCache.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArTemplate;


class TemplateCache
{
    /**
     * @var FileTemplate
     */
    protected $file;
    /**
     * TemplateCache constructor.
     * @param FileTemplate $file
     */
    public function __construct(FileTemplate &$file)
    {
        $this->file = $file;

    }


    /**
     * @return string
     */
    public function getName()
    {
        $name = str_replace(array('\\', '/', ':', '.'), '-', $this->file->getFileName());
        $compiler_name = (rtrim($this->file->getCacheDir(), DS) . DS . sha1($name) . '.cache.' . $name . '.php');
        return $compiler_name;
    }

    /**
     * @return bool
     */
    public function exists(){
        return is_file($this->getName());
    }

    /**
     * re create cache file
     */
    public function reCreate(){
        unlink($this->getName());
    }

    /**
     * create content
     * @param $content
     */
    public function create($content){
        $old = umask(0);
        file_put_contents($this->getName(), $content);
        umask($old);
    }
}