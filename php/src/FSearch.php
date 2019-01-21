<?php
namespace FSearch;
require_once 'FSearchConfig.php';
use FSearch\FSearchConfig;
class FSearch {
    protected $fpath = null;
    protected $file = null;

    protected $config = null;



    protected function __construct(){}
    public static function create($fpath, FSearchConfig $config = null) {
        if (empty($fpath)) throw new EmptyPathException();
        $object = new static();

        if (empty($config)) $object->config = new FSearchConfig();
        else $object->config = $config;

        $object->fpath = $fpath;

        $object->checkFile();

        $object->file = fopen($fpath, 'r');
        if (!$object->file) throw new FileNotFoundException();
        return $object;
    }
    public function __destruct()
    {
        if ($this->file) fclose($this->file);
    }

    /**
     * @param string $searchString search string
     * @param callable|null $compare function ($haystack, $needle) should return position;
     * @return null
     */
    public function find(string $searchString, callable $compare = null) {
        if ($searchString === '') return null;
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
                    //Если мы ищем вхождение из нескольких строк
                    //в фрагменте состоящем из такого же количества строк,
                    //позиция найденной строки может существовать только
                    //в первой из них
                    return [
                        'line' => (int)$line,
                        'position' => (int)$pos
                    ];
                }
                //Отсчитываем строки только когда очередь наполнится
                $line++;
            }

        }
        return null;
    }
    protected function searchSingleLine(string $searchString, callable $compare = null) {

    }
    protected function searchMultiLine(string $searchString, callable $compare = null) {

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
        if (!empty($this->config->allowedMimeTypes)) {
            $mimeType = mime_content_type($this->fpath);
            if (!in_array($mimeType, $this->config->allowedMimeTypes)) throw new FileWrongMimeTypeException();
        }
        if (!empty($this->config->maxSize)) {
            $size = filesize($this->fpath);
            if ($size > $this->config->maxSize) throw new FileTooLargeException();
        }
    }
}

class EmptyPathException extends \Exception {
    protected $message = 'File path is empty';
}
class FileNotFoundException extends \Exception {
    protected $message = 'File is not found';
}
class FileWrongMimeTypeException extends \Exception {
    protected $message = 'Mime-type is not supported';
}
class FileTooLargeException extends \Exception {
    protected $message = 'File size is too large';
}
?>