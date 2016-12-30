<?php
/**
 * -----------------------------------
 * File  : TemplateCompiler.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArTemplate;


class TemplateCompiler
{
    /**
     * @var FileTemplate
     */
    protected $file;
    /**
     * @var array
     */
    protected $includes = [];
    /**
     *  all code before function
     * @var array
     */
    protected $before = [];
    /**
     * @var FileTemplate
     */
    protected $parent = null;
    protected $parent_name;
    protected $extends;
    /**
     * this
     * @var array
     */
    protected $thisBlock = [];
    /**
     * @var array
     */
    protected $parentBlocks = [];
    /**
     * @var array
     */
    protected $tags = [];

    /**
     * TemplateCompiler constructor.
     * @param FileTemplate $fileTemplate
     */
    public function __construct(FileTemplate &$fileTemplate)
    {
        $this->file = &$fileTemplate;
        $path = $fileTemplate->getCompilerDir();
        if (!is_dir($path)) {
            $old = umask(0);
            mkdir($path, DIR_WRITE_MODE);
            umask($old);
        }

        if(is_dir($path) && !is_writable($path)){
             $old = umask(0);
            chmod($path,DIR_WRITE_MODE);
            umask($old);
        }

    }

    /**
     * @return bool
     */
    public function exists()
    {
        return is_file($this->getName());
    }

    /**
     * start process template
     */
    public function process()
    {
        if ($this->exists()) {
            $_arTpl = $this->file;
            ob_start();
            include_once($this->getName());
            ob_end_clean();
            $_arTpl = null;
            $meta = $this->file->getMeta();

            if (isset($meta['parent'])) {
                $this->parent = $this->file->render($meta['parent']);

            }
            if (isset($meta['thisBlocks'])) {
                $this->thisBlock = $meta['thisBlocks'];
            }
            $this->parentBlocks = $this->getAllParentBlocks();
        }
    }

    /**
     * @return array
     */
    public function getAllParentBlocks()
    {
        $blocks = $this->parentBlocks;
        if ($this->parent instanceof FileTemplate) {
            $meta = $this->parent->getMeta();
            if (isset($meta['thisBlocks'])) {
                $blocks = array_diff($meta['thisBlocks'], $blocks);
            }
        }
        return $blocks;
    }

    /**
     * @return string
     */
    public function callBackName()
    {
        return '_template_content_' . sha1($this->file->getFileName());
    }

    /**
     * @return string
     */
    public function getName()
    {
        $name = str_replace(array('\\', '/', ':', '.'), '-', $this->file->getFileName());
        $compiler_name = (rtrim($this->file->getCompilerDir(), DS) . DS . sha1($name) . '.file.' . $name . '.php');
        return $compiler_name;
    }

    /**
     * replace code and re compiler file
     */
    public function reCompiler()
    {

        $source = $this->replaceSourceCode();
        $content = "<?php\n/**\n* Create By ArabTemplate Version : " . ArTemplate::version . "\n* Created Date : " . date('d/m/Y');
        $content .= "\n* File Path :'" . $this->file->getFullPath() . "'\n*/\n";
        $content .= "/**\n* includes files\n*/" . "\n";
        foreach ($this->includes as $include) {
            $content .= "include_once('$include');\n";
        }
        if (count($this->before)) {
            foreach ($this->before as $item) {
                $content .= $item . "\n<?php\n";
            }

        }
        if ($this->parent instanceof FileTemplate) {
            $this->file->setMeta(array('parent' => $this->parent->getFileName()));
            $content .= "\n\$_arTpl->setMeta(array('parent' => '" . $this->parent->getFileName() . "'));\n";
        }
        if (count($this->thisBlock)) {
            $this->file->setMeta(array('thisBlocks' => $this->thisBlock));
            $content .= "\n\$_arTpl->setMeta( array('thisBlocks' =>  array(\n'" . implode("',\n'", $this->thisBlock) . "'))\n);\n";
        }
        $content .= "if(!function_exists('" . $this->callBackName() . "')){\n";
        $content .= "function " . $this->callBackName() . '(&$_arTpl){?>' . "\n";
        $content .= $source;
        if ($this->parent instanceof FileTemplate) {
            $content .= "<?php \$_arTpl->getParentExtends()->getContent(true);?>\n";
        }
        $content .= "\n\n<?php } } ?>";
        if(is_file($this->getName()) && !is_writable($this->getName())){
            unlink($this->getName());
        }

        $old = umask(0);
        file_put_contents($this->getName(), $content);
        umask($old);
    }

    /**
     * @param bool $source
     * @return mixed
     */
    public function replaceSourceCode($source = false)
    {
        $source = ($source == false ? $this->file->source() : $source);
        $defaults = [
            '#' . ArTemplate::left . '\s*(?P<cfun>FUNCTION)\s+(?P<name>[\w]+)\((?P<val>.+?)\)\s*' . ArTemplate::right . '(?P<content>((?R)|.*))' . ArTemplate::left . '\s*\/FUNCTION\s*' . ArTemplate::right . '#is',
        ];
        $replaceSourceCode = preg_replace_callback(
            $defaults,
            [$this, 'createDefaultSystem'],
            $source);
        $replaceSourceCode = preg_replace_callback(
            '#' . ArTemplate::left . '(.+?)' . ArTemplate::right . '#is',
            [$this, 'geCallFromSystem'],
            $replaceSourceCode);
        return $replaceSourceCode;
    }

    /**
     * @param $match
     * @return null
     */
    public function createDefaultSystem($match)
    {
        $cfun = strtolower($match['cfun']);
        if ($cfun == 'function') {
            $content = '';
            $content .= "if(!function_exists('{$match['name']}')){\n";
            $content .= "function {$match['name']}(" . (empty($match['val']) ? "" : $match['val']) . "){\n";
            $content .= "\$_arTpl=" . get_class($this->file) . "::getInstance();\n";
            preg_match_all('/\$(?:([\w]+)\s*(?:=\s*(.+))?)/i', $match['val'], $matches);
            if (count($matches[1])) {
                foreach ($matches[1] as $index => $var) {
                    $content .= "\$_arTpl->with('$var',\$$var);\n";
                }
            }
            $content .= "?>\n" . $this->replaceSourceCode($match['content']);
            $content .= "\n<?php } } ?>\n";
            $this->before[] = $content;
        }
        return null;
    }

    /**
     * @param $match
     * @return mixed
     */
    public function geCallFromSystem($match)
    {
        $default = [
            '\s*(?P<var>EXTENDS)\s+(?:FILE=(?P<file>.+)|(?P<file>.+))\s*',
            '\s*(?P<var>BLOCK)\s+(?:NAME=(?:(?:\'|")(?P<name>.+?)(?:\'|"))|(?:(?:\'|")(?P<name>.+?)(?:\'|")))\s*',
            '\s*(?P<var>FOREACH)\s+(?P<from>.+?)\s+AS\s+(?:(?:\$(?P<key>[\w]+))\s*=>\s*)?(?:\$(?P<value>[\w]+))',
            '\s*(?P<var>IF|ELSEIF)\s+(?P<condition>.+)',
            '\s*(?P<con>.+)\?(?P<f1>.*)\:(?P<f2>.+)\s*',
            '\s*(?P<var>ELSE|FOREACHELSE)\s*',
            '\s*(?P<var>\/(IF|FOREACH|BLOCK|FOR))\s*',
            '\s*(?P<var>FOR)\s+(?P<val>.+)\s*',

            '\s*(?P<display>include)\s+(?:FILE\s*=(?P<val>.+)|(?P<val>.+))\s*',
            '\s*(?P<display>(with|fetch|display|assign))\((?P<val>.+)\)\s*',
            '\s*(?P<break>(BREAK|CONTINUE))\s*',
            '\s*(?P<print>.+)\s*',
        ];
        $code = preg_replace_callback('#(?J)' . implode('|', $default) . '#i', [$this, 'getMatch'], $match[1]);
        return $code;
    }

    /**
     * @param $match
     * @return mixed
     */
    public function getMatch($match)
    {

        if (isset($match['var']) && !empty($match['var'])) {
            $var = strtolower($match['var']);
            if (in_array($var, ['if', 'elseif'])) {
                $var = 'if';
            }
            $method = '_replace_' . str_replace('/', 'end_', $var);
            if (method_exists($this, $method)) {
                return $this->{$method}($match);
            }
        } elseif (isset($match['print']) && !empty($match['print'])) {
            return $this->_replace_print($match);
        } elseif (isset($match['display']) && !empty($match['display'])) {
            return $this->_replace_system_function($match);
        } elseif (isset($match['break']) && !empty($match['break'])) {
            return '<?php ' . $match['break'] . ';?>';
        } elseif (isset($match['con']) && !empty($match['con'])) {
            return $this->_replace_short_if($match);
        }
        return false;
    }

    /**
     * @param $var
     * @param array $prefix
     * @return mixed
     */
    public function replaceVarCode($var, &$prefix = [])
    {
        $patterns = [
            '#(?P<var>\$(?P<val>[\w\.\->:@]+))#',
            '#(?P<var>\[(?P<val>[\w]+)\])#',
            '#(?P<fun>[\w]+)\((?P<val>.*)\)#',
        ];

        return preg_replace_callback($patterns, function ($match) use (&$prefix) {
            return $this->replaceVarType($match, $prefix);
        }, $var);
    }

    /**
     * @param $match
     * @param array $prefix
     * @return string
     */
    public function replaceVarType($match, &$prefix = [])
    {

        if (isset($match['var'])) {
            $var = $match['var'];

            if ($var[0] == '$' && preg_match_all('#(?P<all>(->|\.|@|::)?([\w]+))#', $match['val'], $matches)) {

                return $this->replaceChangeVar($matches['all'], $prefix);
            } elseif ($var[0] == '[' && substr($var, -1) == ']') {
                if (is_numeric($match['val'])) {
                    return '[' . $match['val'] . ']';
                } else {
                    if (empty($match['val'])) {
                        return null;
                    }
                    return '[\'' . $match['val'] . '\']';
                }
            }
        } elseif (isset($match['fun'])) {
            $function_name = $match['fun'];
            if (function_exists($function_name)) {
                return $match[0];
            } else {

                if($this->file->functionExists($function_name)){
                    return '$_arTpl->getFunctionTpl(\''.$function_name.'\',['.$match['val'].'])';
                }
                $file = __DIR__ . DS . 'plugins' . DS . 'function.' . $function_name . '.php';
                if (is_file($file)) {
                    $this->includes[] = $file;
                    return $match[0];
                }
            }
        }

        return $match[0];
    }

    /**
     * @param array $vars
     * @param array $prefix
     * @return string
     */
    public function replaceChangeVar($vars = [], &$prefix = [])
    {

        $result = [];
        $vars = array_reverse($vars, true);
        $isStatic = false;
        $notUseValue = false;
        foreach ($vars as $index => $var) {
            if (empty($var)) {
                continue;
            }
            if (strpos($var, '.') === 0) {
                $val = substr($var, 1);
                if (is_numeric($val)) {
                    $result[$index] = '[' . $val . ']';
                } else {
                    $result[$index] = '[\'' . $val . '\']';
                }
            } elseif (strpos($var, '->') === 0) {
                $result[$index] = $var;
            } elseif (strpos($var, '@') === 0) {
                $result[$index] = str_replace('@', '->', $var);
                $notUseValue = true;
            } elseif (strpos($var, '::') === 0) {
                $result[$index] = $var;
                $isStatic = true;
            } else {
                if ($var == '_arTpl') {
                    $result[] = '$_arTpl';
                } else {
                    if ($isStatic) {
                        /*  $name = trim('$_static_var_'.$var);
                          if(!isset($prefix[$name])){
                              $prefix[$var] = $name.' = $_arTpl->varTpl[\'' . $var . '\']->value';
                          }*/
                        if (isset($this->file->varTpl[$var])) {
                            $name =
                                (is_object($this->file->varTpl[$var]->value) ?
                                    get_class($this->file->varTpl[$var]->value) :
                                    $this->file->varTpl[$var]->value);
                        } else {
                            $name = $var;
                        }
                        $result[$index] = $name;
                    } else {
                        $var_name = '$_arTpl->varTpl[\'' . $var . '\']';
                        $var_name = ($notUseValue == true ? $var_name : $var_name.'->value');
                        $result[$index] = $var_name;
                    }
                }
            }
        }
        $result = array_reverse($result);
        return implode('', $result);
    }

    /**
     * extends layout file
     * @param $match
     * @return null
     */
    public function _replace_extends($match)
    {

        $var = $this->replaceVarCode($match['file']);
        $_arTpl = $this->file;
        $this->parent_name = $var;
        eval("\$var = $var;");
        $this->parent = ArTemplate::$arTemplate->render($var);
        $this->parentBlocks = $this->getAllParentBlocks();
        return '<?php $_arTpl->setParentExtends('.$this->parent_name.');?>';
    }
    /**
     * @param $extends
     */
    public function setParentExtends($extends){
        $this->extends = ArTemplate::$arTemplate->render($extends);
    }

    /**
     * FileTemplate
     * @return mixed
     */
    public function getParentExtends(){
        return $this->extends;
    }
    /**
     * @param $match
     * @return null
     */
    public function _replace_block($match)
    {
        $name = 'template_block_' . sha1($match['name']);
        $this->tags['blocks'][] = array('id' => $name, 'name' => $match['name']);
        if (!in_array($name, $this->parentBlocks)) {
            $this->thisBlock[] = $name;
        }
        $content = "<?php /* {block '{$match['name']}'} {$this->file->getFullPath()}*/\n";
        $content .= "\nif(!function_exists('$name')){\n";
        $content .= "function $name(&\$_arTpl){?>\n";
        return $content;
    }

    /**
     * close if
     * @return string
     */
    public function _replace_end_block()
    {
        $meta = array_pop($this->tags['blocks']);
        $name = $meta['id'];
        $block_name = $meta['name'];
        $content = "<?php } }\n";
        if (empty($this->parent) ) {
            $content .= "echo $name(\$_arTpl);\n";
        }
        $content .= "/*{/block '$block_name'}*/\n";
        $content .= "?>";
        return $content;
    }

    /**
     * print all
     * @param $match
     * @return string
     */
    public function _replace_print($match)
    {
        if (substr($match['print'], 0, 1) == '*' && substr($match['print'], -1) == '*') {
            return '<?php /*' . $match['print'] . '*/;?>';
        } else if (substr($match['print'], 0, 1) == '|' && substr($match['print'], -1) == '|') {
            $prefix = [];
            $print = $this->replaceVarCode(substr($match['print'], 1, -1), $prefix);
            if (count($prefix)) {
                $print = implode(';', $prefix) . ';' . $print;
            }
            return '<?php ' . $print . ';?>';
        } else if (substr($match['print'], 0, 2) == '!!' && substr($match['print'], -2) == '!!') {
            $prefix = [];
            $print = $this->replaceVarCode(substr($match['print'], 2, -2), $prefix);
            if (count($prefix)) {
                $prefix = implode(';', $prefix) . ';';
            } else {
                $prefix = null;
            }
            return '<?php ' . $prefix . ' echo htmlentities(' . $print . ');?>';
        } else if (preg_match('/^\$([\w]+)\s*(?:[=]{1})\s*(.+)$/', $match['print'], $matches)) {
            $prefix = [];
            $print = $this->replaceVarCode($matches[2], $prefix);
            if (count($prefix)) {
                $prefix = implode(';', $prefix) . ';';
            } else {
                $prefix = null;
            }
            return ("<?php  $prefix  \$_arTpl->with('" . $matches[1] . "',$print);?>\n");
        } else if (preg_match('#\$([^\+|\-|\*|\/|=]+)\s*(\+=|\*=|\-=|\/=){1}\s*(.+)#', $match['print'], $matches)) {

            $prefix = [];
            $print = $this->replaceVarCode($match['print'], $prefix);
            if (count($prefix)) {
                $prefix = implode(';', $prefix) . ';';
            } else {
                $prefix = null;
            }
            return ("<?php  $prefix  $print;?>\n");
        }
        $prefix = [];
        $print = $this->replaceVarCode($match['print'], $prefix);
        if (count($prefix)) {
            $prefix = implode(';', $prefix) . ';';
        } else {
            $prefix = null;
        }
        return ('<?php ' . $prefix . ' echo ' . $print . ';?>');
    }

    /**
     * return system function all
     * @param $match
     * @return string
     */
    public function _replace_system_function($match)
    {

        if ($match['display'] == 'with' || $match['display'] == 'assign') {
            // echo '<pre>';print_r($match);die();
            return ('<?php $_arTpl->with(' . $this->replaceVarCode($match['val']) . ');?>');
        } elseif ($match['display'] == 'display' || $match['display'] == 'include') {
            return ('<?php $_arTpl->display(' . $this->replaceVarCode($match['val']) . ');?>');
        } elseif ($match['display'] == 'fetch') {
            return ('<?php echo $_arTpl->fetch(' . $this->replaceVarCode($match['val']) . ');?>');
        }
    }

    /**
     * open if or else if
     * @param $match
     * @return string
     */
    public function _replace_if($match)
    {
        $prefix = [];
        $condition = $this->replaceVarCode($match['condition'], $prefix);
        return '<?php ' . (count($prefix) ? implode(';)', $prefix) . ';' : null) . (strtolower($match['var']) == 'if' ? 'if' : '} elseif') . '(' . $condition . '){?>';
    }

    /**
     * short if
     * @param $match
     * @return string
     */
    public function _replace_short_if($match)
    {
        $prefix = [];
        $condition = $this->replaceVarCode($match[0], $prefix);
        return '<?php echo ('.$condition.');?>';
    }

    /**
     * close if and open else
     * @return string
     */
    public function _replace_else()
    {
        return '<?php } else{ ?>';
    }

    /**
     * close if
     * @return string
     */
    public function _replace_end_if()
    {
        return '<?php } ?>';
    }

    /**
     * open foreach
     * @param $match
     * @return string
     */
    public function _replace_foreach($match)
    {
        $from = $this->replaceVarCode($match['from']);
        $value = '$_arTpl->varTpl[\'' . $match['value'] . '\']';
        $this->tags['foreach'][] = $value;
        $content = "<?php \n";
        $content .= "\$_loop_from_array = " . $from . ";\n";
        $content .= "\$_arTpl->with('{$match['value']}',[]);\n";
        $key = null;
        if (!empty($match['key']) && is_string($match['key'])) {
            $content .= "\$_arTpl->with('{$match['key']}',false);\n";
            $key = '$_arTpl->varTpl[\'' . $match['key'] . '\']';
        }
        $content .= "if(!is_array(\$_loop_from_array) && !is_object(\$_loop_from_array))  settype(\$_loop_from_array,'array'); \n";
        $content .= "\$_loop_from_array_count = (count(\$_loop_from_array) -1);\n";
        $content .= "foreach(\$_loop_from_array as " . $value . "->key => " . $value . "->value){\n";
        $content .= $value . "->isLoop = true;\n";
        $content .= "\n " . $value . "->index++;";
        $content .= "\n " . $value . "->first = (" . $value . "->index === 0);";
        $content .= "\n " . $value . "->last = (" . $value . "->index === \$_loop_from_array_count );";
        if (!empty($key)) {
            $content .= $key . "->value = " . $value . "->key;\n";
        }
        $content .= '?>';
        return $content;

    }

    /**
     * close foreach
     * @return string
     */
    public function _replace_foreachelse()
    {
        $value = array_pop($this->tags['foreach']);
        return "<?php } if(" . $value . "->isLoop == false){?>\n";
    }

    /**
     * close foreach
     * @return string
     */
    public function _replace_end_foreach()
    {
        return '<?php } ?>';
    }


    /**
     * open for loop
     * @param $match
     * @return string
     */
    public function _replace_for($match)
    {
        $val = substr($match['val'], 0, strpos($match['val'], ';'));
        preg_match_all('/,?(?:\$(?P<var>[\w]+))\s*=(?P<val>[^,]+)/', $val, $matches);
        $content = "<?php\n";
        foreach ($matches['var'] as $index => $var) {
            $content .= "\$_arTpl->with('{$var}'," . $this->replaceVarCode($matches['val'][$index]) . ");\n";

        }
        $content .= "for(" . $this->replaceVarCode($match['val']) . "){?>\n";
        return $content;
    }

    /**
     * close for
     * @return string
     */
    public function _replace_end_for()
    {
        return '<?php } ?>';
    }
}