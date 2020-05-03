<?php


namespace App\cache;


class Cache
{
    const PART_NUMBER = 'partNumber';
    const PART_NUMBER_ERROR = 'не задан номер партии';

    private array $_cacheData = [];
    private array $_modifyTimes = [];
    private string $_path = 'cache/';


    public function __construct()
    {
        if (!file_exists($this->_path)) {
            mkdir($this->_path);
        }
    }

    public function setProp($name, $value)
    {
        $this->_cacheData[$name] = $value;

        $serialized = serialize($value);
        file_put_contents($this->_path . $name, $serialized);
        $this->_modifyTimes[$name] = time();
    }

    public function getProp($name_prop)
    {
        if (isset($this->_cacheData[$name_prop])) {
            return $this->_cacheData[$name_prop];
        }
        $cache_file = $this->_path . DIRECTORY_SEPARATOR . $name_prop;
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
        return false;
    }

    public function getPartNumber(string $product) : ?int
    {
        return $this->getProp($product .self::PART_NUMBER) ?? null;
    }

    public function setPartNumber(string $product, int$number)
    {
        $this->setProp($product . self::PART_NUMBER, $number);
    }
}