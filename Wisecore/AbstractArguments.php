<?php


namespace Wisecore;


abstract class AbstractArguments
{
    protected $arguments = [];

    public function __construct()
    {
        $this->arguments = array_slice($this->fetchArguments(), 1);
    }

    abstract protected function fetchArguments();

    public function getCommand()
    {
        if (count($this->arguments) > 0) {
            return array_shift($this->arguments);
        } else {
            return null;
        }
    }

    public function getKeys()
    {
        if (count($this->arguments) > 0) {
            $arguments = [];
            foreach ($this->arguments as $args) {
                if (preg_match('/^(--\w*)=?(.*)$/', $args, $matches)) {
                    $arguments[$matches[1]] = isset($matches[2]) ? $matches[2] : false;
                }
            }
            return $arguments;
        } else {
            return null;
        }
    }
}