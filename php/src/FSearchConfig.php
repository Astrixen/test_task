<?php
/**
 * Created by PhpStorm.
 * User: Vaara
 * Date: 21.01.2019
 * Time: 11:59
 */

namespace FSearch;


class FSearchConfig
{
    public $encoding = 'UTF-8';
    public $ignoreCase = false;
    public $allowedMimeTypes = null;
    public $maxSize = null;

    public function setValue($name, $value) {
        if (property_exists($this, $name)) $this->$name = $value;
        return $this;
    }

    public function getValue($name) {
        if (property_exists($this, $name)) return $this->$name;
        return false;
    }

    public function __construct($path = null)
    {
        $this->parseConfig($path);
    }

    public function parseConfig($path) {
        if ($path !== null) {
            $config = yaml_parse_file($path);
            foreach ($config as $parameter => $value) {
                $this->setValue($parameter, $value);
            }
        }
    }
}