<?php


namespace Wisecore;


class ServerArguments extends AbstractArguments
{

    protected function fetchArguments()
    {
        return $_SERVER['argv'];
    }
}