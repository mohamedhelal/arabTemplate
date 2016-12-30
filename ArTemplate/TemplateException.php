<?php
/**
 * -----------------------------------
 * File  : TemplateException.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArTemplate;


class TemplateException extends \Exception
{
    public function __construct($message, $code = 2, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        header("HTTP/1.0 404 Not Found");
        echo '<h1>ArTemplate Error Handler</h1>';
        echo '<div><h3>Error Message</h3><p>'.$this->message.'</p></div>';
        die();
    }
}