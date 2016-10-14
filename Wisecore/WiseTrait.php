<?php


namespace Wisecore;


trait WiseTrait
{
    /**
     * Show error message;
     * @param $message
     * @return $this
     */
    private function error($message)
    {
        $message = is_array($message) ? $message : [$message];
        array_unshift($message, PHP_EOL . str_pad('--- Error ', 100, '-') . PHP_EOL);
        $this->response($message);
    }

    /**
     * Show message
     * @param $message
     * @param bool $exit
     * @return $this
     */
    private function response($message, $exit = true)
    {
        echo PHP_EOL;
        $message = is_array($message) ? $message : [$message];
        foreach ($message as $msg) {
            echo $msg . PHP_EOL;
        }
        if ($exit) {
            exit;
        } else {
            return $this;
        }
    }

    private function isWritableDirectory($path)
    {
        if (! is_writable($path)) {
            if (! chmod($path, 0644)) {
                $this->error([
                    'Can\'t write in directory "' . $path . '"',
                    'Change access rights for this directory, please'
                ]);
            }
        }
        return true;
    }

    private function callLocalKeys($arguments)
    {
        foreach ($this->keysAssociate as $key => $method) {
            if (array_key_exists($key, $arguments) !== false) {
                return call_user_func([$this, $method], $arguments[$key]);
                exit(0);
            }
        }
    }
}