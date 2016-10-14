<?php

namespace Wisecore;

use Wisecore\WiseTrait;

class Wiseci
{
    use WiseTrait;
    /**
     * Path to commands directory
     * @var string
     */
    protected $commandPath = __DIR__ . '/Commands';

    /**
     * Current command
     * @var mixed|null
     */
    protected $command = null;

    /**
     * List available commands
     * @var array
     */
    protected $commandsList = [];
    /**
     * List of command keys
     * @var array|null
     */
    protected $keys = [];

    /**
     * Associative array of local keys
     * --key => methodName
     * @var array
     */
    protected $keysAssociate = [
        '--list' => 'showCommandsList',
    ];

    public function __construct(AbstractArguments $arguments)
    {
        $this->command = $arguments->getCommand();
        $keys = $arguments->getKeys();
        $this->keys = ($keys !== null) ? $keys : [];
    }

    public function handler()
    {
        $this->boot(new CommandIterator($this->commandPath));
    }

    public function boot(CommandIterator $cIterator)
    {
        if (! is_dir(APPPATH) or ! is_dir(BASEPATH)) {
            $this->error([
                'Wrong default paths to directory application "' . APPPATH . '" or "' . BASEPATH . '"',
                'Set correctly constant APPPATH or BASEPATH in wiseci, please.'
            ]);
        }

        $this->commandsList = $cIterator->getCommands();
        ksort($this->commandsList);

        if ($this->command === null) {
            $this->error(['Missing name command', 'Use --list to get list available commands']);
        } elseif (array_key_exists($this->command, $this->keysAssociate)) {
            call_user_func([$this, $this->keysAssociate[$this->command]]);
        } elseif (array_key_exists($this->command, $this->commandsList)) {
            $this->commandsList[$this->command]->handler($this->keys);
        } else {
            $this->error(['Wrong name command', 'Use --list key to get list available commands']);
        }
    }

    protected function showCommandsList()
    {
        if (count($this->commandsList) == 0) {
            $this->response(['There are no available commands']);
        }
        $response = [];
        $response[] = 'List available commands: '. PHP_EOL;
        foreach ($this->commandsList as $cmd => $data) {
            $response[] = str_repeat(' ', 5) . str_pad($cmd, 25, ' ') . '- ' . $data->description;
        }
        $this->response($response);
    }

}