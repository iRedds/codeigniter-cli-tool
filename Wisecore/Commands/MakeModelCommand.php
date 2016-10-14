<?php


namespace Wisecore\Commands;



use Wisecore\WiseTrait;

class MakeModelCommand
{
    use WiseTrait;

    /**
     * Path to template. Optionaly
     * @var string
     */
    protected $stub = __DIR__.'/Stubs/model.stub';

    /**
     * Command name
     * @var string
     */
    public $command = 'make:model';

    /**
     * Command description
     * @var string
     */
    public $description = "Create new model";

    /**
     * Associative array of local keys
     * --key => methodName
     * @var array
     */
    protected $keysAssociate = [
        '--help' => 'showHelp',
    ];

    /**
     * Command body
     * @param $params
     * @return $this
     */
    public function handler($params)
    {
        $this->callLocalKeys($params);

        if (is_string($name = $params['--name']) === false or count($params) == 0) {
            $this->error(['Missing parameters', 'Use --help']);
        }

        $classPath = APPPATH.'models/'. $name . '.php';

        if (file_exists($classPath)) {
            $this->error('Model already exists');
        }

        if (! file_exists($this->stub)) {
            $this->error('Model stub not exists');
        }

        $path = '';

        if (($slashPos = strrpos($name, '/')) !== false) {
            $path = substr($name, 0, $slashPos++);
        }

        $class = ucfirst(substr($name, $slashPos));

        $stub = str_replace('{@CLASSNAME@}', $class, file_get_contents($this->stub));

        $newPath = APPPATH . 'models/' . $path;

        if (! is_dir($newPath)) {
            if (! mkdir($newPath, 0644, true)) {
                $this->error('Can\'t create directories');
            }
        }

        if (file_put_contents($newPath . '/' . $class. '.php', $stub)) {
            $this->response('Model ' . $class . ' was created');
        }
        exit(0);
    }

    /**
     *  Help message
     */
    private function showHelp()
    {
        $this->response([
            'Create model examples:',
            'php wiseci make:model --name=Modelname',
            'php wiseci make:model --name=Path/To/Modelname',
        ]);
    }
}