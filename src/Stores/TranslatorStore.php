<?php

namespace LaraForm\Stores;

use Illuminate\Support\Facades\File;
use LaraForm\Core\BaseStore;
use LaraForm\Traits\StrParser;

/**
 * Class TranslatorStore
 *
 * @package LaraForm\Stores
 */
class TranslatorStore extends BaseStore
{
    use StrParser;
    
    /**
     * TAB
     */
    const TAB = '    ';

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
     * @var array
     */
    protected $items = [];

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
        $this->path = str_ireplace('.', DIRECTORY_SEPARATOR, config('lara_form.translator.directive'));
        $this->path = resource_path($this->path);
        $this->fileName = config('lara_form.translator.file_name');
        $this->fullPath = $this->path . DIRECTORY_SEPARATOR . $this->fileName . '.php';
    }

    /**
     * @param $events
     *
     * @return string
     */
    protected function generateContent($items)
    {
        $content = "<?php" . PHP_EOL . "return [" . PHP_EOL;
        foreach (array_filter($items) as $key => $item) {
            $content .= self::TAB . "'" . $key . "' => '" . $item . "'," . PHP_EOL;
        }
        return $content . "];";
    }

    /**
     * @param $string
     */
    public function put()
    {
        if (empty($this->fullPath)) {
            return;
        }
        $this->firstPut();
        $oldItems = require $this->fullPath;
        foreach ($this->items as $key => $item) {
            if (!is_string($key)) {
              $key = $item;
            }
            $oldItems[$this->parseKey($key)] = $this->parseName($item);
        }
        File::put($this->fullPath, $this->generateContent($oldItems));
        $this->items = [];
    }

    /**
     * @param $attr
     * @param null $key
     */
    public function add($attr, $key = null)
    {
        if ($key) {
            $this->items[$key] = $attr;
        }else{
            $this->items[] = $attr;
        }
    }

    /**
     *
     */
    protected function firstPut()
    {
        if (!File::exists($this->fullPath)) {
            File::put($this->fullPath, $this->generateContent([]));
        }
    }
}