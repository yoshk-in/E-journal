<?php


namespace App\cache;


class Cache
{
    private $cacheData = [];
    private static $instance;
    private $mtimes = [];
    private $path = 'data/';


    private function __construct()
    {

    }

    public static function init()
    {
        if (is_null(self::$instance)) self::$instance = new Cache();

        return self::$instance;
    }

    public function set($name, $value)
    {
        $this->cacheData[$name] = $value;
        if (!file_exists($this->path)) {
            mkdir($this->path);
        }
        $serialized = serialize($value);
        file_put_contents($this->path . $name, $serialized);
        $this->mtimes[$name] = time();
    }

    public function get($name)
    {
        $file = $this->path . "/" . $name;
        if (file_exists($name)) {
            clearstatcache();
            $mtime = filemtime($file);
            if (!isset($this->mtimes[$name])) {
                $this->mtimes[$name] = 0;
            }
            if ($mtime > $this->mtimes[$name]) {
                $this->mtimes[$name] = $mtime;
                return ($this->cacheData[$name] = unserialize(file_get_contents($file)));
            }
        }
        if (isset($this->cacheData[$name])) return $this->cacheData[$name];
    }

    public function getPartNumber()
    {
        return $this->get('partNumber');
    }

    public function setPartNumber($number)
    {
        $this->set('partNumber', $number);
    }
}