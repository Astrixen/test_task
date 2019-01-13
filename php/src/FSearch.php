<?php
namespace FSearch;
class FSearch {
    protected $fpath = null;
    protected $file = null;

    protected $config = null;



    protected function __construct(){}
    public static function create($fpath, FSearchConfig $config = null) {
        if (empty($fpath)) throw new EmptyPathException();
        $object = new static();
        $object->fpath = $fpath;
        $object->file = fopen($fpath, 'r');
        if (!$object->file) throw new FileNotFoundException();
        if (empty($config)) $object->config = new FSearchConfig();
        else $object->config = $config;
        return $object;
    }
    public function __destruct()
    {
        fclose($this->file);
        // TODO: Implement __destruct() method.
    }

    /**
     * @param string $searchString search string
     * @param callable|null $compare function ($haystack, $needle) should return position;
     * @return null
     */
    public function find(string $searchString, callable $compare = null) {
        $line = 0;
        $searchString = $this->prepareString($searchString);
        $countOfStrings = substr_count($searchString, "\n") + 1;
        $queue = [];
        while ($string = fgets($this->file)) {
            $string = $this->prepareString($string);
            $queue[] = $string;

            if (count($queue) > $countOfStrings) {
                array_shift($queue);
            }
            if (count($queue) === $countOfStrings) {
                $string = implode("", $queue);
                if ($compare) $pos = $compare($string, $searchString);
                else $pos = $this->baseCompare($string, $searchString);
                if ($pos !== false) {

                    return [
                        'line' => (int)$line - $countOfStrings - 1,
                        'position' => (int)$pos
                    ];
                }
                $line++;
            }

        }
        return null;
    }

    protected function prepareString($string) {
        if ($this->config->ignoreCase) {
            $string = mb_strtolower($string, $this->config->encoding);
        }
        return $string;
    }

    protected function baseCompare($haystack, $needle) {
        $pos = mb_strpos($haystack, $needle, 0,  $this->config->encoding);
        if ($pos !== false) {
            return $pos;
        }
        return false;
    }




    protected function checkFile() {
    }

    protected function parseConfig() {

    }
}

class EmptyPathException extends \Exception {
    protected $message = 'File path is empty';
}
class FileNotFoundException extends \Exception {
    protected $message = 'File is not found';
}

class FSearchConfig {
    public $encoding = 'UTF-8';
    public $ignoreCase = false;
}
?>