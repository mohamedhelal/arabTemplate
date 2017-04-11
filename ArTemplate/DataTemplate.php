<?php
/**
 * -----------------------------------
 * File  : DataTemplate.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArTemplate;


class DataTemplate
{
    /**
     * @var array
     */
    public $varTpl = [];
    /**
     * @var array
     */
    public $shared = [];

    /**
     * pass vars data to template
     * @param $var
     * @param null $value
     * @return $this
     */
    public function with($var, $value = null)
    {
        if (!is_array($var)) {
          $var = [$var => $value];
        }
        foreach ($var as $key => $value) {
            if (!($value instanceof Variables)) {
                $value = new Variables($value);
            }
            $this->varTpl[$key] = $value;
        }
        return $this;
    }

    /**
     * pass vars data to template
     * @param $var
     * @param null $value
     * @return DataTemplate
     */
    public function assign($var, $value = null){
        return $this->with($var, $value );
    }
    /**
     * @param $var
     * @param null $value
     * @return $this
     */
    public function share($var, $value = null)
    {
        if (is_array($var)) {
            foreach ($var as $key => $item) {
                $this->share($key, $item);
            }
        } else {
            if (!($value instanceof Variables)) {
                $value = new Variables($value);
            }
            $this->shared[$var] = $value;
        }
        return $this;
    }

    /**
     * get template vars
     */
    public function get_template_vars(){
        $vars = func_get_args();
        $values = [];
        if(count($vars) == 0 ){
            foreach ($this->varTpl as $item) {
                $values[] = $item->value;
            }
        }else{
            foreach ($this->varTpl as $item) {
                if(in_array($item,$vars)) {
                    $values[] = $item->value;
                }
            }
        }

        return (count($vars) > 1 ? $values : reset($values));
    }
}