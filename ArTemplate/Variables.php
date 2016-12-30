<?php
/**
 * -----------------------------------
 * File  : Variables.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArTemplate;

/**
 * Class Variables
 * all template Variables
 * @package ArTemplate
 */
class Variables
{
    /**
     * object value
     * @var mixed
     */
    public $value;
    /**
     * @var bool
     */
    public $isLoop = false;
    /**
     * @var null
     */
    public $key    = null;
    /**
     * is first row
     * @var bool
     */
    public $first = false;
    /**
     * is last row
     * @var bool
     */
    public $last = false;
    /**
     * index number
     * @var int
     */
    public $index = -1;
    /**
     * Variables constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function count() {
        return (is_array($this->value) || is_object($this->value) ? count($this->value) : 1);
    }

    /**
     * @param $by
     * @return bool
     */
    public function is_div_by($by) {
        return ($this->index > 0 && $this->index % $by);
    }

    /**
     * @param $by
     * @return bool
     */
    public function is_even_by($by) {
        return ($this->index > 0 && $this->index / $by);
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string) $this->value;
    }

    /**
     *
     */
    public function __destruct() {
        $this->isLoop = false;
        $this->key = null;
        $this->value = null;
        $this->index = -1;
        $this->first = false;
        $this->last = false;
    }
}