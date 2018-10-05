<?php

namespace LaraForm\Stores;

use Illuminate\Support\Facades\File;
use LaraForm\Core\BaseStore;

/**
 * Class TranslatorStore
 *
 * @package LaraForm\Stores
 */
class TranslatorStore extends BaseStore
{
    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string
     */
    protected $fileName = '';

    /**
     * @var string
     */
    protected $fullPath = '';
    
    /**
     * TAB
     */
    const TAB = '    ';

    /**
     * TranslatorStore constructor.
     *
     * @param \LaraForm\Stores\Filesystem $filesystem
     */
    public function __construct()
    {
        $this->setPath();
    }

    /**
     *
     */
    protected function setPath()
    {
        $this->path = str_ireplace('.',DIRECTORY_SEPARATOR, config('lara_form.translate_directive'));
        $this->path = resource_path($this->path);
        $this->fileName = config('lara_form.translate_file');
        $this->fullPath = $this->path.DIRECTORY_SEPARATOR.$this->fileName.'.php';
    }

    /**
     * @param $events
     *
     * @return string
     */
    protected function generateContent($items)
    {
        $content = "<?php".PHP_EOL."return [" . PHP_EOL;
        foreach ($items as $key => $item) {
            $content .= self::TAB . "'" . $key . "' => '".$item."'," . PHP_EOL;
        }
        return $content . "];";
    }

    /**
     * @param $string
     */
    public function put($str)
    {
        $items = require $this->fullPath;
        $items[$this->parseKey($str)] = $this->parseName($str);
        File::put($this->fullPath, $this->generateContent($items)) ;
    }

    /**
     * @param $str
     */
    public function addTranslate($str)
    {     
          if (env('APP_ENV') === config('lara_form.env') && !empty($this->fullPath)) {
              $this->firstPut();
              $this->put($str);
          };
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return ucwords($this->parse($name));
    }

    /**
     * @param $key
     *
     * @return string
     */
    protected function parseKey($key)
    {
        $str = snake_case($this->parse($key));
        return str_slug($str,'_');
    }

    /**
     * @return string
     */
    protected function parse($str)
    {
        return trim(preg_replace('/[^a-zA-Z]/', ' ', $str));
    }

    /**
     * 
     */
    protected function firstPut()
    {
        if (!File::exists($this->fullPath)) {
            File::put($this->fullPath, $this->generateContent([])) ;
        }
    }
}