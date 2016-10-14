<?php

namespace Wisecore\Commands;

use Wisecore\WiseTrait;

class MakeConsoleCommand
{
    use WiseTrait;


    /**
     * Path to template. Optionaly
     * @var string
     */
    protected $stub = __DIR__ . '/Stubs/consoleCommand.stub';

    /**
     * Command name
     * @var string
     */
    public $command = 'make:console';

    /**
     * Command description
     * @var string
     */
    public $description = "Create new console command";

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
     * @param array $params
     * @return $this
     */
    public function handler($params)
    {
        $this->callLocalKeys($params);

        if (is_string($name = $params['--name']) === false or count($params) == 0) {
            $this->error([$this->command . ' Missing parameters', 'Use --help']);
        }

        $this->isWritableDirectory(__DIR__);

        $class = ucfirst($name) . 'Command';
        $classPath = __DIR__ . '/' . $class . '.php';

        if (file_exists($classPath)) {
            $this->error('Command already exists');
        }

        if (!file_exists($this->stub)) {
            $this->error('Command stub not exists');
        }

        $stub = str_replace('{@CLASSNAME@}', $class, file_get_contents($this->stub));

        if (file_put_contents($classPath, $stub)) {
            $this->response('Command ' . $class . ' was created');
        }
        exit;
    }

    /**
     *  Help message
     */
    private function showHelp()
    {
        $this->response([
            'Create Wiseci console command',
            'Example: php wiseci make:console --name=CommandName',
        ]);
    }
}
