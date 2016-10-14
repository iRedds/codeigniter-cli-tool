<?php


namespace Wisecore;


class CommandIterator
{

    /**
     * Commands namespace
     * @var string
     */
    protected $namespace = 'Wisecore\\Commands\\';

    /**
     * List of commands
     * @var array
     */
    protected $commands = [];

    public function __construct($path)
    {
        $this->fetchCommands($path);
    }

    public function getCommands()
    {
        return $this->commands;
    }

    private function fetchCommands($path)
    {
        $iterator = new \DirectoryIterator($path);
        foreach ($iterator as $file) {
            if ($file->isFile() && preg_match('/.*Command\.php$/', $file->getFilename())) {
                $currentFile = $this->namespace . basename($file->getFilename(), '.php');
                $class = new $currentFile;
                $this->commands[$class->command] = $class;
            }
        }
    }
}