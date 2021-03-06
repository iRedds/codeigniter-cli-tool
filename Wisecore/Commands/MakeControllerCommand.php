<?php

namespace Wisecore\Commands;

use Wisecore\WiseTrait;


/**
 * Class MakeControllerCommand
 * @package Wisecore\Commands
 */
class MakeControllerCommand
{
    use WiseTrait;

    /**
     * Path to template. Optionaly
     * @var string
     */
    protected $stub = __DIR__.'/Stubs/controller.stub';

    /**
     * Command name
     * @var string
     */
    public $command = 'make:controller';

    /**
     * Command description
     * @var string
     */
    public $description = "Create new controller";

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

        if (is_string($key = $params['--name']) === false or count($params) == 0) {
            $this->error(['Missing parameters', 'Use --help']);
        }

        $classPath = APPPATH.'controllers/'. $key . '.php';

        if (file_exists($classPath)) {
            $this->error('Controller already exists');
        }

        if (! file_exists($this->stub)) {
            $this->error('Controller stub not exists');
        }

        $path = '';
        if (($slashPos = strrpos($key, '/')) !== false) {
            $path = substr($key, 0, ++$slashPos);
            
        }
        
        $class = substr($key, $slashPos);
        if (version_compare(CI_VERSION, '3.0.0', 'ge')) {
            $class = ucfirst($class);
        }

        $date = new \DateTime();
        $template =  strtr(
            file_get_contents($this->stub),
            [
                '{@CLASSNAME@}' => ucfirst($class),
                '{@DATE@}'      => $date->format('d.m.Y'),
                '{@TIME@}'      => $date->format('H:i:s'),
            ]
        );

        $newPath = APPPATH . 'controllers/' . $path;
        echo $newPath;

        if (! is_dir($newPath)) {
            if (! mkdir($newPath, 0644, true)) {
                $this->error('Can\'t create directories');
            }
        }

        if (file_put_contents($newPath . $class. '.php', $template)) {
            $this->response('Controller ' . $class . ' was created');
        }
        
        exit(0);
    }

    /**
     *  Help message
     */
    private function showHelp()
    {
        $this->response([
            'Create controller examples:',
            'php wiseci make:controller --name=Controllername',
            'php wiseci make:controller --name=Path/To/Controllername',
        ]);
    }

}