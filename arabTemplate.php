<?php
/**
 #--------------------------------------------------------------------------------------
 # نظام قوالب القالب العربى نظام مجانى   لفصل الكود عن البرمجة لتسهيل عمل  المصميمبن و التسهيل على المطورين تطوير برمجياتهم 
 # وممكن ان تقوم بتعديل على الكود كما حب و ستخدمة فى برامجك المجانية و المدفوعة و ممكن ان تقوم بتطويرة و التعديل علة كما تحب 
 #--------------------------------------------------------------------------------------
 #  @package    :  ArabTemplate the PHP compiling template engine
 #	@version	: 8
 #  @author		: Mohamed Helal<mohamedhelal123456@gmail.com>
 #  @copyright  : Mohamed Helal 2010 - 2014 
 #-------------------------------------------------------------------------------------- 
 */
/**
 #--------------------------------------------------------------------------------------
 # الثوابت العامة
 #--------------------------------------------------------------------------------------
 */
define('ARAB_TEMPLATE', true);
if(!defined('DS'))define('DS', DIRECTORY_SEPARATOR);
/**
 * #-------------------------------------------------------------------
 * 	الكلاس الرئيسى للنظام
 * #-------------------------------------------------------------------
 */

class ArabTemplate
{
	/**
	 * #-------------------------------------------------------------------
	 * متغيرات البيانات
	 * #-------------------------------------------------------------------
	 */
	public    		$varTple   				= array();
	public  static  $globals	    		= array();
	private static  $templates				= array();
	private static  $functions				= array();
	private 		$Syntax					= array();
	private static  $codes					= array();
	/**
	 * #-------------------------------------------------------------------
	 * باقى المتغيرات الاساسية
	 * #-------------------------------------------------------------------
	 */
	private  	    $template_dir			= false;
	private 	    $compile_dir			= 'Compiles';
	private 	    $cache_dir				= 'Caches';
	public 			$caching				= false;
	public 			$cache_lefttime     	= false;
	public 			$parent					= null;
	public 			$cache_name				= null;
	public 			$filename				= null;
	private 		$fileout				= false;
	public 			$source					= null;
	private 		$use_database			= false;
	private 		$use_database_function  = false;
	private 		$lastupdate				= false;
	private 		$outputFile				= 'output_arabtemplate';
	private 		$allowOutPutFile		= false;
	/**
	 * #-------------------------------------------------------------------
	 *  بداية و نهاية البحث عن  البيانات التى يجب تغيرها فى القالب
	 * عند تغير بداية البحث و نهاية يجب عليك اختيار  عناص لن تستخدمها فى القالب ابدا
	 * #-------------------------------------------------------------------
	 */
	// متغير بداية البحث من 
	private 		$ldelim					= '{{';
	// متغير نهاية البحث
	private 		$rdelim					= '}}';
	/**
	 * #-------------------------------------------------------------------
	 * ثوابت 
	 * #-------------------------------------------------------------------
	 */
	public function __construct(){}
	/**
	 * #-------------------------------------------------------------------
	 *  السماح لعمل كاش لجميع الملفات فى ملف واحد
	 * #------------------------------------------------------------------- 
	 */
	public function allow_output_file()
	{
		ob_start();
		$this->allowOutPutFile = true;
	}
	/**
	 * #-------------------------------------------------------------------
	 *  استدعاء القوالب من قاعدة البيانات
	 * #------------------------------------------------------------------- 
	 * @param unknown $callback
	 * @return ArabTemplate
	 */
	public function setResource($callback)
	{
		$this->use_database = true;
		$this->use_database_function = $callback;
		return $this;
	}
	/**
	 * #-------------------------------------------------------------------
	 *  اضافة داله باسم اخر
	 * #-------------------------------------------------------------------  
	 * @param unknown $name
	 * @param unknown $callback
	 * @return ArabTemplate
	 */
	public function setFunction($name,$callback)
	{
		self::$functions[(string)$name] = $callback;
		return $this;
	}
	/**
	 * #-------------------------------------------------------------------
	 *  عرض الاخطاء
	 * #------------------------------------------------------------------- 
	 * @param unknown $error
	 */
	private function error($error)
	{
		trigger_error($error);die();
	}
	/**
	 * #-------------------------------------------------------------------
	 *  تمرير المتغيرات للقالب
	 * #------------------------------------------------------------------- 
	 * @param unknown $var
	 * @param string $value
	 * @return ArabTemplate
	 */
	public function assign($var ,$value = null)
	{
		if(is_array($var))
		{
			foreach ($var as $key => $val)
			{
				$this->assign($key,$val);
			}
		}
		else
		{
			if($value instanceof ArabTemplateVar)
			{
				$this->varTple[$var] = $value;
			}
			else
			{
				$this->varTple[$var] = new ArabTemplateVar($value);
			}
		}
		return $this;
	}
	/**
	 * #-------------------------------------------------------------------
	 *  تمرير المتغيرات للقالب
	 * #-------------------------------------------------------------------
	 * @param unknown $var
	 * @param string $value
	 * @return ArabTemplate
	 */
	public function assignByRef($var ,&$value = null)
	{
		if(is_array($var))
		{
			foreach ($var as $key => &$val)
			{
				$this->assignByRef($key,$val);
			}
		}
		else
		{
			if($value instanceof ArabTemplateVar)
			{
				$this->varTple[$key] = &$value;
			}
			else
			{
				$this->varTple[$var] = new ArabTemplateVar($value);
			}
		}
		return $this;
	}
	/**
	 * #-------------------------------------------------------------------
	 * حذف جميع المتغير او متغيرات معينة
	 * #-------------------------------------------------------------------  
	 * @param unknown $Assign
	 */
	public function clearAssign($vars = 'all')
	{
		if($vars == 'all')
	       $this->varTple = array();
		else if(!empty($vars))
		{
			foreach ((array)$vars as $var)
			{
				unset($this->varTple[$var]);
			}
		}	
	}
	/**
	 * #-------------------------------------------------------------------
	 * جلب قيمة من متغيرات القالب
	 * #------------------------------------------------------------------- 
	 * @param string $varname
	 * @return multitype:NULL
	 */
	public  function getTemplateVars($varname = false)
	{
		$values = array();
		if(empty($varname) || $varname === false || is_array($varname))
		{
			foreach ($this->varTple as $key => $val)
			{
				if($varname === false)
				{
					$values[$key] = $val->val;
				}
				else if(is_array($varname) && in_array($key, $varname))
				{
					$values[$key] = $val->val;
				}
			}
			return $values;
		}
		else if(!empty($varname) && is_string($varname) && isset($this->varTple[$varname]))
		{
			$values = $this->varTple[$varname]->val;
		}
		return $values;
	}
	/**
	 * #-------------------------------------------------------------------
	 * اضافة مجلد التحويل 
	 * #-------------------------------------------------------------------  
	 * @param unknown $compile_dir
	 */
	public function setCompileDir($compile_dir)
	{
		$this->compile_dir = realpath($compile_dir).DS;
	}
	public function getCompileDir()
	{
		return $this->compile_dir ;
	}
	/**
	 * #-------------------------------------------------------------------
	 * اضافة مجلد للكاش
	 * #-------------------------------------------------------------------  
	 * @param unknown $cache_dir
	 */
	public function setCacheDir($cache_dir)
	{
		$this->cache_dir = realpath($cache_dir).DS;
	}
	public function getCacheDir()
	{
		return $this->cache_dir;
	}
	/**
	 * #-------------------------------------------------------------------
	 * اضافة مجلد للقوالب 
	 * #-------------------------------------------------------------------  
	 * @param unknown $template_dir
	 */
	public function setTemplateDir($template_dir)
	{
		$this->template_dir = realpath($template_dir).DS;
	}
	public function getTemplateDir()
	{
		return $this->template_dir;
	}
	/**
	 * #-------------------------------------------------------------------
	 * انشاء اسم للملف المتحويل
	 * #-------------------------------------------------------------------  
	 * @return string
	 */
	public function createCompileName($type = false,$filename = false,$cache_name = null)
	{
	    $filename = ($filename != false?$filename:$this->filename);	
	    $cache_name = ($cache_name != false?$cache_name:$this->cache_name);
		$compile  = sha1($filename).str_replace(['\\','/',':','?'], '-', $cache_name.'#'.$filename);
		if($type == 'cache')
		{
			$compile = $this->cache_dir.str_replace('#', '.cache.', $compile);
		}
		else
		{
			$compile = $this->compile_dir.str_replace('#', '.file.', $compile);
		}
		return ($compile.'.php');
	}
	
	
	/**
	 * #-------------------------------------------------------------------
	 *	التحقق من وجود الملف
	 * #-------------------------------------------------------------------  
	 */
	private function getTemplate()
	{
		if(!is_dir($this->template_dir) && $this->use_database == false)
		{
			$this->error('Templates Folder \''.$this->template_dir.'\' Not Found');
		}
		else if(!is_readable($this->template_dir) && $this->use_database == false)
		{
			$this->error('Templates Folder \''.$this->template_dir.'\' Not Readable');
		}
		else
		{
		   if(!is_dir($this->compile_dir))
			{
				if(!mkdir($this->compile_dir,777))
				{
					$this->error('Unable To Create Compile Folder\''.$this->compile_dir.'\'');
				}
			}
			else if($this->caching === true && !is_dir($this->cache_dir))
			{
				if(!mkdir($this->cache_dir,777))
				{
					$this->error('Unable To Create Caches Folder\''.$this->cache_dir.'\'');
				}
			}
			if($this->use_database == true)
			{
				
				$template_data = call_user_func($this->use_database_function,$this->filename);
				if(is_array($template_data) && isset($template_data['code'],$template_data['lastupdate']))
				{
					$this->lastupdate = $template_data['lastupdate'];
					$this->source     = $template_data['code'];
					return true;
				}
			}
			if(is_file($this->template_dir.$this->filename));
			else if(is_file($this->filename))
			{
				$this->fileout = true;
			}
			else
			{
				$this->error('Template File  \''.$this->filename.' \' Not Found');
			}
			
		}
	}
	
	/**
	 * #-------------------------------------------------------------------
	 * كتابة الملف المحول
	 * #-------------------------------------------------------------------  
	 */
	private function  writeCompiler()
	{
		$filename = $this->filename;
		if($this->use_database === true &&  is_string($this->source));
		else if($this->fileout === false)
		{
			$filename     = $this->template_dir.$this->filename;
			$this->source = file_get_contents($filename);
		}
		else if($this->fileout === true)
		{
			$this->source = file_get_contents($this->filename);
		}
		$source = $this->compileCode($this->source);
		$callname = 'template_content_'.md5($this->createCompileName());
		$data   = "<?php \n/**\n * Create By ArabTemplate version 8";
		$data  .= "\n * File Name : $filename\n * Create Date : ".date('d/m/Y')."\n */ \n";
		$data  .= "\nif(!defined('ARAB_TEMPLATE')) exit('no direct access allowed');";
		$data  .= "\n// File Content Function ";
		$data  .= "\nif(!function_exists('$callname')){\n function $callname(\$_artpl){?>\n";
		$data  .= $source."\n<?php \$_artpl->clearAssign();} } ?>";
		file_put_contents($this->createCompileName(), $data);
		return true;
	}
	/**
	 * #-------------------------------------------------------------------
	 *	انشاء ملف المحول
	 * #-------------------------------------------------------------------  
	 * @return boolean
	 */
	public function getCompilerFile()
	{
		if($this->caching === true && $this->cache_lefttime != false && is_int($this->cache_lefttime) && $this->cache_lefttime > 0)
		{
			if((filemtime($this->createCompileName('cache'))+$this->cache_lefttime)  >= time())
			{
				require_once($this->createCompileName('cache'));
				return true;
			}
		}
		$this->getTemplate();
		if(is_file($this->createCompileName()))
		{
			$filemtime = filemtime($this->createCompileName());
			if($this->use_database === true && is_numeric($this->lastupdate) && is_string($this->source) && $this->lastupdate > $filemtime)
			{
				 unlink($this->createCompileName());
			}
			else if($this->fileout === false &&  $this->use_database === false && filemtime($this->template_dir.$this->filename) > $filemtime)
			{
				unlink($this->createCompileName());
			}
			else if($this->fileout === true && $this->use_database === false && filemtime($this->filename) > $filemtime)
			{
				unlink($this->createCompileName());
			}
			else
			{
				$callname = 'template_content_'.md5($this->createCompileName());
				if(!function_exists($callname))
				{
					require_once($this->createCompileName());
				}
				echo $callname($this);
				return true;
			}
		}
		$this->writeCompiler();
		require_once($this->createCompileName());
		$callname = 'template_content_'.md5($this->createCompileName());
		echo $callname($this);
		return true;
	}
	/**
	 * #-------------------------------------------------------------------
	 * التحقق من وجود الكاش
	 * #-------------------------------------------------------------------
	 * @param unknown $template
	 * @param string $cache_name
	 * @return boolean
	 */
	public  function isCached($template,$cache_name = null)
	{
		return (is_file($this->createCompileName('cache',$template,$cache_name)));
	}
	public function clearCache($template,$cache_name = null)
	{
		if ($this->isCached($template,$cache_name))
		{
			unlink($this->createCompileName('cache',$template,$cache_name));
		}
	}
	public function clearAllCache($time = false)
	{
		if($this->caching == false)
		{
			return false;
		}
		$files = array_diff(scandir($this->cache_dir), array('.','..'));
		foreach ($files as $file)
		{
			if(!empty($file))
			{
				if($time != false && is_numeric($time) && filemtime($this->cache_dir.$file) == (time()-$time))
				{
					unlink($this->cache_dir.$file);
				}
				else if ($time == false)
				{
					unlink($this->cache_dir.$file);
				}
			}
		}
	}
	/**
	 * #-------------------------------------------------------------------
	 * انشاء القالب للعرض
	 * #-------------------------------------------------------------------
	 * @param string $template
	 * @param string $code
	 * @param array $data
	 * @param int $lefttime
	 * @param string $cache_name
	 * @param string $parent
	 * @param string $merge
	 * @return  
	 */
	private function createTemplate($template  ,$data = array(),$cache_name = null,$cache_lefttime = false,$parent = null,$merge = true)
	{
		
		if($parent === null && ($this instanceof ArabTemplate || $this instanceof ArabTemplateFile || is_subclass_of($this, 'ArabTemplate') ))
		{
			$parent = $this;
		}
		if(isset(self::$templates[$template]))
		{
			$tpl =  self::$templates[$template];
			$parent = $tpl->parent;
		}
		else
		{
			$tpl = new ArabTemplateFile();
			$tpl->setTemplateDir($parent->getTemplateDir());
			$tpl->setCompileDir($parent->getCompileDir());
			$tpl->setCacheDir($parent->getCacheDir());
			if($this->allowOutPutFile === true)
			{
				$tpl->allow_output_file();
			}
			if($this->use_database === true && is_callable($this->use_database_function))
			{
				$tpl->setResource($this->use_database_function);
			}
			$tpl->caching           	= ($parent->caching == false && $cache_name === true?true:($this->caching || $parent->caching));
			$tpl->filename 				= $template;
			$tpl->cache_lefttime 		= $cache_lefttime;
			$tpl->cache_name            = ($cache_name != false && is_string($cache_name)?$cache_name:null);
			$tpl->parent   				= $parent;
			self::$templates[$template] = $tpl;
			
		}
		if($merge === true)
		{
			$tpl->assign(array_merge($parent->varTple,(array)$data));
		}
		else
		{
			$tpl->assign($data);
		}
		
		return $tpl;
	}
	/**
	 * #-------------------------------------------------------------------
	 * عرض القالب
	 * #------------------------------------------------------------------- 
	 * @param string $template
	 * @param string $code
	 * @param string $lefttime
	 * @param string $cache_name
	 */
	public function fetch($template  ,$data = array(),$cache_name = null,$cache_lefttime = false,$merge = true)
	{
		$tpl = $this->createTemplate($template  ,$data  ,$cache_name ,$cache_lefttime ,$this,$merge);
		ob_start();
		$tpl->getCompilerFile();
		return ob_get_clean();
	}
	/**
	 * #-------------------------------------------------------------------
	 * عرض القالب
	 * #-------------------------------------------------------------------
	 * @param string $template
	 * @param string $code
	 * @param string $lefttime
	 * @param string $cache_name
	 */
	public function display($template  ,$data = array(),$cache_name = null,$cache_lefttime = false,$merge = true)
	{
		echo $this->fetch($template  ,$data ,$cache_name ,$cache_lefttime ,$merge);
	}
	
	/**
	 * #-------------------------------------------------------------------
	 * 	داله حذف الكلاس
	 * #-------------------------------------------------------------------
	 */
	public function __destruct()
	{
		$content = ob_get_clean();
		if(
		  $this->caching === true && 
		  ((!is_subclass_of($this, 'ArabTemplate') && $this->allowOutPutFile === true) ||
		 (is_object($this->parent)  && $this->allowOutPutFile === false)) || 
		 (is_subclass_of($this, 'ArabTemplate')  && !($this instanceof ArabTemplateFile) && $this->allowOutPutFile === true)
		)
		{
			$cache = ($this->filename == false && $this->allowOutPutFile == true?$this->outputFile:false);
			file_put_contents($this->createCompileName('cache',$cache), $content);
		}
		echo $content;
	}
	/**
	 * #-------------------------------------------------------------------
	 * عرض القالب النهائى 
	 * #-------------------------------------------------------------------
	 * @return boolean
	 */
	public function get_output_file()
	{
		if($this->caching === true && $this->allowOutPutFile === true && is_file($this->createCompileName('cache',$this->outputFile)))
		{
			include_once $this->createCompileName('cache',$this->outputFile);exit();
		}
		return false;
	}
	/**
	 * #-------------------------------------------------------------------
	 * تغير محتوى القالب
	 * #-------------------------------------------------------------------
	 * @param unknown $code
	 * @return unknown
	 */
	private function compileCode($code)
	{
		$this->rdelim  = preg_quote($this->rdelim);
		$this->ldelim  = preg_quote($this->ldelim);
		$setvar_val = 
		[
		'(\$[\w\.]+)+(?:\s*([\+|\-|\*|\/]*=)(.*|(?R)))',
		'(\+{2})+(\$[\w\.]+)',
		 '(\-{2})+(\$[\w\.]+)'
		];
		$code = preg_replace_callback('#{PHP}(?:(?R)|(.*?)){/php}#is',array($this,'_reset_php_code'), $code);
		$code = preg_replace('/'.$this->ldelim.'\*.*\*'.$this->rdelim.'/s','', $code);
		$code = preg_replace('/'.$this->ldelim.'\s*(break|continue)\s*'.$this->rdelim.'/i', '<?php $1;?>', $code);
		$code = $this->_chang_Syntax($code);
		$code = preg_replace_callback('/'.$this->ldelim.'\s*(?:'.implode('|', $setvar_val).')\s*'.$this->rdelim.'/', array(&$this,'_reset_var_val'), $code);
		$code = preg_replace_callback('/'.$this->ldelim.'\s*(\$?[\w:]+)\(([^'.$this->ldelim.$this->rdelim.']+)\)\s*'.$this->rdelim.'/', array(&$this,'_print_function_var'), $code);
		$code = preg_replace_callback('/'.$this->ldelim.'\s*(\$?[\w]+[^'.$this->ldelim.$this->rdelim.']*)\s*'.$this->rdelim.'/', array(&$this,'_print_var'), $code);
		$code = str_replace(array_keys(self::$codes), array_values(self::$codes), $code);
		return $code;
	}
	/**
	 * #-------------------------------------------------------------------
	 *  تغير الاساسية فى القالب
	 * #------------------------------------------------------------------- 
	 * @param unknown $code
	 * @return mixed
	 */
	private function _chang_Syntax($code)
	{
		$Syntax   =
		[
		'(FOREACH)\s+(.*|(?R)*)\s+AS\s+(\$[\w]+)(?:\s*=>\s*(\$[\w]+))?',
		'(IF)\s+([^\?{}:]+)\?([^\?{}\:]+)\:([^\?{}\:]+)',
		'(IF|ELSEIF)([^{}]+)',
		'(\/IF)',
		'(\/FOREACH)',
		'(ELSE)',
		'(FOREACHELSE)',
		'(FOR)\s+(.*|(?R)*)',
		'(\/FOR)'
		];
		$system_function =
		[
		'assign',
		'include',
		'fetch'
		];
		$code = preg_replace_callback('/'.$this->ldelim.'\s*(?:'.implode('|', $Syntax).')\s*'.$this->rdelim.'/i', array(&$this,'_chack_item_type'), $code);
		 $code = preg_replace_callback('/'.$this->ldelim.'\s*('.implode('|', $system_function).')\s+([^'.$this->ldelim.$this->rdelim.']+)\s*'.$this->rdelim.'/i', array(&$this,'_system_function'), $code);
		return $code;
	}
	/**
	 * #-------------------------------------------------------------------
	 * 	دوال التعامل مع المتغيرات
	 * #-------------------------------------------------------------------
	 */
	private function  get_var_tpl($var)
	{
		return "\$_artpl->varTple['$var']";
	}
	/**
	 * #-------------------------------------------------------------------
	 * 	التحقق من الدوال
	 * #-------------------------------------------------------------------
	 * @param unknown $function_name
	 * @param unknown $function_args
	 * @return string|mixed
	 */
	public function reset_function_tpl($matchs)
	{
		
		$function_name = $matchs[1];$function_args = $matchs[2];
		if(function_exists($function_name))
		{
			return $function_name.'('.$this->_replace_var($function_args).')';
		}
		else if(isset(self::$functions[$function_name]))
		{
			$function_args = $this->_replace_var($function_args);
			return  '$_artpl->get_function_tpl(\''.$function_name.'\',array('.$function_args.'))';
		}
		else if(preg_match('/^\$[\w]+::.+/', $function_name))
		{
			return $this->_replace_var($function_name).'('.$this->_replace_var($function_args).')';
		}
		else if(method_exists($this, $function_name))
		{
			return  '$_artpl->'.$function_name.'('.$this->_replace_var($function_args).')';
		}
		return  $function_name.'('.$this->_replace_var($function_args).')';
	}
	/**
	 * #-------------------------------------------------------------------
	 * جلب الدوال الى القالب
	 * #------------------------------------------------------------------- 
	 * @param unknown $callback
	 * @param unknown $args
	 * @return mixed
	 */
	public function get_function_tpl($callback,$args)
	{
		return call_user_func_array(self::$functions[$callback], $args);
	}
	/**
	 * #-------------------------------------------------------------------
	 * 	جلب قيمة المتغير من المصفوفة
	 * #-------------------------------------------------------------------
	 * @param unknown $var
	 * @return mixed
	 */
	private function _replace_var($var)
	{
		return  preg_replace_callback('/(\$[\w\-\>\.\[\]\:\$@]+)|([\w:]+\(([^\(\)]*|(?R))*\))|([\w\-\>\.\[\]\:\$@]+)/', array($this,'_replace_val'), $var);
	}
	private function _replace_val($matchs)
	{
		if(stripos($matchs[0], '$') === 0)
		{
			return  preg_replace_callback('/\$([\w\-\>\.\[\]\:\$@]+)/', array($this,'_chack_var_type'), $matchs[0]);
		}
		elseif(preg_match('/^([\w]+)::(.+)/', $matchs[0],$sub_matchs))
		{
			$sub_var = $sub_matchs[2];
			if(strpos($sub_matchs[2], '.'))
			{
				$array   = explode('.',$sub_matchs[2]);
				$sub_var  = array_shift($array).$this->_change_var_data(implode('.',$array),false,true);unset($array);
			}
			$print = $sub_matchs[1].'::'.$sub_var;unset($sub_var);
			return $print;
		}
		else 
		{
			return  preg_replace_callback('/([\w:]+)\((.*)\)/', array($this,'reset_function_tpl'), $matchs[0]);
		}
	}
	/**
	 * #-------------------------------------------------------------------
	 * 	تغير قيمة المتغيرات الفرعية
	 * #-------------------------------------------------------------------
	 * @param unknown $matchs
	 * @return string
	 */
	private function _replace_sub_var($matchs)
	{
		$val = $this->_replace_var($matchs[1]);
		return '['.(is_numeric($val)?$val:($val == $matchs[1]?"'".$val.'\'':$val)).']';
		
	}
	/**
	 * #-------------------------------------------------------------------
	 * 	داله التحقق من نوع المتغير
	 * #------------------------------------------------------------------- 
	 * @param unknown $matchs
	 */
	private function _chack_var_type($matchs)
	{
		if(preg_match('/^[\w]+\-\>.+/', $matchs[1]))
		{
			return $this->_change_var_data($matchs[1],true);
		}
		else
		{
			return $this->_change_var_data($matchs[1]);
		}
	}
	/**
	 * #-------------------------------------------------------------------
	 * 	داله تغير المتغيرات
	 * #------------------------------------------------------------------- 
	 * @param unknown $var
	 */
	private function _change_var_data($var,$isObject = false ,$return = false)
	{
		$spilter = ($isObject === true?'->':'.');
		if(preg_match_all('/\[(([^\[\]]*|(?R)*)*)\]/', $var,$matchs))
		{
			$var = str_replace($matchs[0], str_replace($spilter, '&', $matchs[0]), $var);
		}
		$explode = explode($spilter, $var);
		$tags    = array();
		foreach ($explode as $index => $val)
		{
			if(!empty($val))
			{
				if($return == false && $index == 0 &&  preg_match('/^[\w]+$/i', $val))
				{
					if($val != '_artpl')
					{
						$tags[] = $this->get_var_tpl($val).'->val';
					}
					else
					{
						$tags[] = '$_artpl';
					}
					
				}
				else if($return == false && $index == 0 &&  preg_match('/(^[\w]+)@(.+)/i', $val,$matchs))
				{
					$tags[] = $this->get_var_tpl($matchs[1]).'->'.$this->_replace_var(str_replace('&', $spilter, $matchs[2]));
				}
				else if($return == false && $index == 0 && preg_match('/^([\w]+)(\[.+\])/', $val,$matchs))
				{
					
					$sub_var = preg_replace_callback('/\[(([^\[\]]*|(?R)*)*)\]/', array(&$this,'_replace_sub_var'), str_replace('&', $spilter, $matchs[2]));
					$tags[]  = $this->get_var_tpl($matchs[1]).'->val'.$sub_var;
				}
				else if($return == false && $index == 0 && preg_match('/^([\w]+)::(.+)/', $val,$sub_matchs))
				{
					$sub_var = str_replace('&', $spilter, $sub_matchs[2]);
					if(strpos($sub_var, '.'))
					{
						$array   = explode('.',$sub_var);
						$sub_var  = array_shift($array).$this->_change_var_data(implode('.',$array),false,true);
					}
					$class = $this->get_var_tpl($sub_matchs[1])->val;
					$tags[] = (is_object($class)?get_class($class):$class).'::'.$sub_var;
				}
				else if(is_numeric($val))
				{
					$tags[] = '['.$val.']';
				}
				else
				{
					
					if($isObject == true)
					{
						if(strpos($val, '.'))
						{
							$array   = explode('.',$val);
							$tags[]  = array_shift($array).$this->_change_var_data(implode('.',$array),false,true);
						}
						else
						{
							$tags[] = $val;
						}
					}
					else
					{
						$tags[] = '[\''.$val.'\']';
					}
				}
			}
		}
		
		if($isObject == true)
		{
			return implode('->', $tags);
		}
		else
		{
			return implode('', $tags).((substr($var,-1) == '.')?'.':null);
		}
	}
	/**
	 * #-------------------------------------------------------------------
	 * 	تغير الكواد ال php
	 * #-------------------------------------------------------------------
	 */
	private function _reset_php_code($matchs)
	{
		$count  = count(self::$codes)?:0;
		$count++;
		$replay = '{#PHPCODE-'.$count.'#}';
		self::$codes[$replay] = "<?php \n".rtrim(ltrim($matchs[1],'<?php'),'?>')."\n?>";
		return $replay;
	}
	/**
	 * #-------------------------------------------------------------------
	 * طباعة المتغيرات
	 * #-------------------------------------------------------------------
	 * @param unknown $matchs
	 * @return string
	 */
	private function _print_var($matchs)
	{
		if(preg_match('/^([\w]+)::(.+)/', $matchs[1],$sub_matchs))
		{
			$sub_var = $sub_matchs[2];
			if(strpos($sub_matchs[2], '.'))
			{
				$array   = explode('.',$sub_matchs[2]);
				$sub_var  = array_shift($array).$this->_change_var_data(implode('.',$array),false,true);unset($array);
			}
			$print = $sub_matchs[1].'::'.$sub_var;unset($sub_var);
		}
		else
		{
			$print = $this->_replace_var($matchs[1]);
		}
		return '<?php echo '.$print.';?>';
	}
	/**
	 * #-------------------------------------------------------------------
	 * انشاء المتغيرات
	 * #-------------------------------------------------------------------
	 * @param unknown $matchs
	 * @return string|unknown
	 */
	private  function _reset_var_val($matchs)
	{
		
		$tags  = [];
		foreach ($matchs as $mat)
		{
			if(!empty($mat))
			{
				$tags[] = $mat;
			}
		}
		
		$matchs = $tags;unset($tags,$mat);
		if(strpos($matchs[1], '$') === 0)
		{
			$set = '<?php ';
			if(trim($matchs[2]) == '=' && preg_match('/[a-z0-9_\$]+/i', $matchs[1]) && !isset($this->varTple[ltrim($matchs[1],'$')]))
			{
				$this->assign(ltrim($matchs[1],'$'),null);
				$set .= "\$_artpl->assign('".ltrim($matchs[1],'$')."', null); \n";
			}
			return $set.$this->_replace_var($matchs[1]).' '.$matchs[2].' '.$this->_replace_var($matchs[3]).';?>';
		}
		else if(count($matchs) == 3 && in_array($matchs[1], array('++','--')) )
		{
			
			return '<?php '.$matchs[1].$this->_replace_var($matchs[2]).';?>';
		}
		
		return $matchs[0];
	}
	/**
	 * #-------------------------------------------------------------------
	 *  طباعة الدوال
	 * #-------------------------------------------------------------------
	 */
	private function _print_function_var($matchs)
	{
		return '<?php echo '.$this->reset_function_tpl(array(1 => $matchs[1],2 => $matchs[2])).';?>';
	}
	/**
	 * #-------------------------------------------------------------------
	 *  تغير 
	 * #-------------------------------------------------------------------
	 */
	private function _chack_item_type($matchs)
	{
		$matchs = array_filter($matchs);
		$krsort = array();
		$index  = 0;
		foreach ($matchs as $match)
		{
			$krsort[$index] = $match;
			$index++;
		}
		$matchs = $krsort;unset($krsort);
		if(count($matchs) > 2 && method_exists($this, $method_name = '_set_call_'.ucfirst(strtolower($matchs[1]))))
		{
			return $this->$method_name(array_slice($matchs, 1));
		}
		else if(count($matchs) == 2 &&  method_exists($this, $method_name = '_set_call_End_'.ucfirst(ltrim(strtolower($matchs[1]),'/'))))
		{
			return $this->$method_name(array_slice($matchs, 1));
		}
	}
	/**
	 * #-------------------------------------------------------------------
	 *  تغير دوال النظام
	 * #-------------------------------------------------------------------
	 */
	private function _system_function($matchs)
	{
		$function = strtolower(trim($matchs[1]));
		$attr     = $this->_get_attr($matchs[2]);
		if($function == 'assign' && isset($attr['var'],$attr['value']))
		{
			return "<?php \$_artpl->assign(".$attr['var'].", ".$attr['value']."); ?>\n";
		}
		else if($function == 'include' || $function == 'fetch')
		{
			
			$file = '';
			if(isset($attr[0]))
			{
				$file = $attr[0];
				unset($attr[0]);
			}
			else if(isset($attr['file']))
			{
				$file = $attr['file'];
				unset($attr['file']);
			}
			$data = false;
			if(count($attr['implodes']))
			{
				foreach ($attr['implodes'] as $index => $val)
				{
					if(stripos($val, "'file'") !== false)
					{
						unset($attr['implodes'][$index]);
					}
				}
				$data = (count($attr['implodes'])?",array(".implode(',', $attr['implodes']).")":false);
				if(isset($attr['prams']) && count($attr['prams']))
				{
					if(in_array('caching', $attr['prams']))
					{
						$data.=',true';
						$sindex = array_search('caching', $attr['prams']);
						unset($attr['prams'][$sindex]);
					}
					$data.= (count($attr['prams'])?','.implode(",", $attr['prams']):null);
				}
			}
			$include  = '<?php ';
			$include .= ($function == 'include'?'$_artpl->display':'echo $_artpl->fetch')."(".$file.$data.");?>"; 
			return $include;
		}
	}
	/**
	 * #-------------------------------------------------------------------
	 * جلب عناصر  و القيمة الخاصة بيها
	 * #-------------------------------------------------------------------
	 * @param unknown $var
	 * @return multitype:
	 */
	private function _get_attr($var)
	{
		$tags 		= [];
		$pattrens   = 
		[
			'(?:([\w\$]+)\s*=\s*(?:[\'|"]?)(?:([^\'"\s]+)|(?R)*)(?:[\'|"]?))',
			'(?:(?:[\'"])(?:([^\'"]+)|(?R)*)(?:[\'"]))',
			'([^\s\'\"]+)'
		];
		if(preg_match_all('/'.implode('|', $pattrens).'/', $var,$matchs))
		{
			foreach ($matchs[1] as $index => $val)
			{
				if(!empty($val) && !empty($matchs[2][$index]) && strpos($val, '$') === false)
				{
					$val_item = $this->_replace_var($matchs[2][$index]);
					$tags[$val] = ($val_item == $matchs[2][$index]?'"'.$matchs[2][$index].'"':$val_item);
					$tags['implodes'][] = "'$val' => $tags[$val]";
				}
				else if(!empty($val) && !empty($matchs[2][$index]) && strpos($val, '$') === 0)
				{
					$tags[$val] = $matchs[2][$index];
				}
			}
			foreach ($matchs[3] as $index => $val)
			{
				if(!empty($val) )
				{
					$val_item = $this->_replace_var($val);
					$tags[$index] = ($val_item == $val?'"'.$val.'"':$val_item);
				}
			}
			foreach ($matchs[4] as $index => $val)
			{
				if(!empty($val) )
				{
					$tags['prams'][] = $val;
				}
			}
		}
		return $tags;
	}
	/**
	 * #-------------------------------------------------------------------
	 *  تغير الفور اتش
	 * #-------------------------------------------------------------------
	 */
	private function _set_call_Foreach($matchs)
	{
		$foreach = "<?php \n ";
		$foreach.="\$from_fetch = ".$this->_replace_var($matchs[1]);
		if(count($matchs) < 4)
		{
			$val = ltrim($matchs[2],'$');
			$key = false;
		}
		else
		{
			$val = ltrim($matchs[3],'$');
			$key = ltrim($matchs[2],'$');
		}
		$varval = $this->get_var_tpl($val);
		$foreach.= ";\n \$_artpl->assign('".$val."', null);";
		$foreach.= "\n ".$varval."->loop = false;";
		if($key != false)
			$foreach.= "\n \$_artpl->assign('".$key."', null);";
		$foreach.= "\n if(!is_array(\$from_fetch) && !is_object(\$from_fetch))settype(\$from_fetch,'array');";
		$foreach.= "\n foreach(\$from_fetch as ".$varval."->key => ".$varval."->val){";
		$foreach.= "\n ".$varval."->loop = true;";
		$foreach.= "\n ".$varval."->index++;";
		$foreach.= "\n ".$varval."->first = ".$varval."->index === 0;";
		$foreach.= "\n ".$varval."->last =".$varval."->index === ".$varval."->count();";
		if($key != false)
			$foreach.= "\n ".$varval."->val =". $varval."->key;";
		$foreach.="\n?>";
		$this->open_tag('foreach', array('tag' => 'foreach','from' => $val));
		return $foreach;
	}
	/**
	 * #-------------------------------------------------------------------
	 *  تغير الفور اتش الس
	 * #-------------------------------------------------------------------
	 */
	private function _set_call_End_Foreachelse($matchs)
	{
		$data = $this->close_tag('foreach');
		$this->open_tag('foreachelse', $data);
		return "<?php } if(\$_artpl->get_var_tpl('".$data['from']."')->loop == false){?>";
	}
	/**
	 * #-------------------------------------------------------------------
	 *  اغلاق الفواتش
	 * #-------------------------------------------------------------------
	 */
	private function _set_call_End_Foreach($matchs)
	{
		$data = $this->close_tag(array('foreachelse','foreach'));
		return '<?php } ?>';
	}
	/**
	 * #-------------------------------------------------------------------
	 *  تغير الشرط اف
	 * #-------------------------------------------------------------------
	 */
	private function _set_call_If($matchs)
	{
		if(count($matchs) == 4){
			return '<?php echo (('.$this->_replace_var($matchs[1]).'?'.$this->_replace_var($matchs[2]).':'.$this->_replace_var($matchs[3]).'));?>';
		}
		$this->open_tag('if', array('from' => 'if'));
		return '<?php if('.$this->_replace_var($matchs[1]).'){?>';
	}
	/**
	 * #-------------------------------------------------------------------
	 *  تغير الشر الس  اف
	 * #-------------------------------------------------------------------
	 */
	private function _set_call_Elseif($matchs)
	{
		$this->open_tag('if', array('from' => 'if'));
		return '<?php }elseif('.$this->_replace_var($matchs[1]).'){?>';
	}
	
	/**
	 * #-------------------------------------------------------------------
	 *  تغير اللايلس
	 * #-------------------------------------------------------------------
	 */
	private function _set_call_End_Else($matchs)
	{
		return '<?php }else{ ?>';
	}
	/**
	 * #-------------------------------------------------------------------
	 *  اغلاق الشرط
	 * #-------------------------------------------------------------------
	 */
	private function _set_call_End_If($matchs)
	{
		$this->close_tag('if');
		return '<?php } ?>';
	}
	
	/**
	 * #-------------------------------------------------------------------
	 *  الفور
	 * #-------------------------------------------------------------------
	 */
	private function _set_call_For($matchs)
	{
		if(preg_match_all('/(?:(\$[\w]+)\s*([=]+)\s*(?:([^\'";,]+)|(?R)*))+/', $matchs[1],$sub_matchs))
		{
			$for = [];
			$vars = [];
			foreach ($sub_matchs[1] as $var)
			{
				if(!empty($var) && strpos($var, '$') === 0 && !isset($this->varTple[ltrim($var,'$')]))
				{
					$var = ltrim($var,'$');
					$this->assign($var,null);
					$vars[] = "\$_artpl->assign('$var',null);";
					$varval = $this->get_var_tpl($var);
					$for[]  = $varval ."->index++;";
					$for[]  = $varval."->first = ".$varval."->index === 0;";
				}
			}
			return ('<?php '.implode("\n", $vars)."\n".'for('.$this->_replace_var($matchs[1])."){\n".implode("\n", $for)."?>");
		}
	}
	/**
	 * #-------------------------------------------------------------------
	 *  الفور
	 * #-------------------------------------------------------------------
	 */
	private function _set_call_End_For($matchs)
	{
		return '<?php } ?>';
	}
	private function open_tag($tag,$data)
	{
		array_push($this->Syntax, array( 'tag' =>$tag,'data' => $data));
	}
	private function close_tag($tags)
	{
		$data = array_pop($this->Syntax);
		if(in_array($data['tag'],(array)$tags))
		{
			return $data['data'];
		}
	}
	public function clearTemplateObject()
	{
		
	}
}
/**
 * #-------------------------------------------------------------------
 * 	كلاس فرعى للنظام
 * #-------------------------------------------------------------------
 */
class ArabTemplateFile extends ArabTemplate{}
/**
 * #-------------------------------------------------------------------
 * 	كلاس المتغيرات
 * #-------------------------------------------------------------------
 */
class ArabTemplateVar
{
	public $val   = null;
	public $loop  = false;
	public $key   = false;
	public $first = false;
	public $last  = false;
	public $index = -1 ;
	public function __construct(&$val)
	{
		$this->val = &$val;
	}
	public function count()
	{
		return (is_array($this->val) || is_object($this->val)?count($this->val):1);
	}
	public function is_div_by($by)
	{
		return ($this->index > 0 && $this->index % $by);
	}
	public function is_even_by($by)
	{
		return ($this->index > 0 && $this->index / $by);
	}
	public function __toString()
	{
		return (string)$this->val;
	}
	public function __destruct(){}
}
