<?php


namespace App\cache;


class Cache
{
    private $_cacheData = [];
    private static $_instance;
    private $_modifyTimes = [];
    private $_path = 'data/';


    private function __construct()
    {

    }

    public static function init()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Cache();
        }

        return self::$_instance;
    }

    public function setProp($name, $value)
    {
        $this->_cacheData[$name] = $value;
        if (!file_exists($this->_path)) {
            mkdir($this->_path);
        }
        $serialized = serialize($value);
        file_put_contents($this->_path . $name, $serialized);
        $this->_modifyTimes[$name] = time();
    }

    public function getProp($name_prop)
    {
        $cache_file = $this->_path . "/" . $name_prop;
        if (file_exists($cache_file)) {
            clearstatcache();
            $mtime = filemtime($cache_file);
            if (!isset($this->_modifyTimes[$name_prop])) {
                $this->_modifyTimes[$name_prop] = 0;
            }
            if ($mtime > $this->_modifyTimes[$name_prop]) {
                $this->_modifyTimes[$name_prop] = $mtime;
                return (
                    $this->_cacheData[$name_prop]
                        = unserialize(file_get_contents($cache_file))
                    );
            }
        }
        if (isset($this->_cacheData[$name_prop])) {
            return $this->_cacheData[$name_prop];
        }
    }

    public function getPartNumber(string $product) : ?int
    {
        return $this->getProp($product . 'partNumber') ?? null;
    }

    public function setPartNumber(string $product, int$number)
    {
        $this->setProp($product . 'partNumber', $number);
    }
}