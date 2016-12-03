<?php

namespace Wisecore\Commands;

use Wisecore\WiseTrait;

class MakeMigrationCommand
{
    use WiseTrait;

    /**
     * Path to template. Optionaly
     * @var string
     */
    protected $stub = __DIR__.'/Stubs/migration.stub';

    /**
     * Command name
     * @var string
     */
    public $command = 'make:migration';

    /**
     * Command description
     * @var string
     */
    public $description = "Create new migration. make:migration --help";

    /**
     * Associative array of local keys
     * --key => methodName
     * @var array
     */
    protected $keysAssociate = [
        '--help' => 'showHelp',
    ];

    protected $cfg;

    /**
     * Command body
     * @param array $params
     * @return $this
     */
    public function handler($params)
    {
        $this->callLocalKeys($params);

        if (($name = $params['--name']) === false or count($params) == 0) {
            $this->error(['Missing parameters', 'Use --help']);
        }

        require_once BASEPATH .'/core/Common.php';

        $this->cfg = new \CI_Config();
        $this->cfg->load('migration');
        $path = $this->cfg->item('migration_path');

        if ($file = $this->existsName($name)) {
            $this->error('Migration with name "' . $name . '" (' . $file. ') already exists');
        }

        $date = new \DateTime();
        $template =  strtr(
            file_get_contents($this->stub),
            [
                '{@MIGRATION_NAME@}' => ucfirst($name),
                '{@TABLE@}'          => $params['--table'] ? $params['--table'] : 'table',
                '{@DATE@}'           => $date->format('d.m.Y'),
                '{@TIME@}'           => $date->format('H:i:s'),
            ]
        );

        $this->isWritableDirectory($path);

        $prefix = $this->getPrefix();

        if (file_put_contents($path.$prefix . '_' . $name . '.php', $template)) {
            $this->response('Migration ' . $prefix . '_' . $name . ' was created');
        }
        exit;

    }

    /**
     *  Help message
     */
    private function showHelp()
    {
        $this->response([
            'Create new migration',
            'Example: php wiseci make:migration --name=create_users_table',
            'Example: php wiseci make:migration --name=create_users_table --table=users',
            'Example: php wiseci make:migration --name=add_fields_users',

        ]);
    }

    /**
     * Prefix for migration file
     * @return string
     */
    private function getPrefix()
    {
        return ($this->cfg->item('migration_type') == 'timestamp')
            ? (new \DateTime())->format('YmdHis')
            : $this->sequential();
    }

    /**
     * Sequential number for prefix
     * @return string
     */
    private function sequential()
    {
        $iterator = new \DirectoryIterator($this->cfg->item('migration_path'));
        $files = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && preg_match('/^\d{3,4}_.*/', $file->getFilename())) {
                $files[] = preg_replace('/^(\d{3})_.*/', '$1', $file->getFilename());
            }
        }

        if (count($files) == 0) {
            return '001';
        }

        natsort($files);
        $number = end($files);

        if (++$number > 999) {
            return $this->error('The limit numbering. Seriously?  Over 9999 migrations?');
        }

        return str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    private function existsName($name)
    {
        $path = $this->cfg->item('migration_path');
        $iterator = new \GlobIterator($path . '*_'. $name . '.php', \FilesystemIterator::KEY_AS_FILENAME);
        try {
            if ($iterator->count()) {
                return $iterator->key();
            }
        } catch ( \LogicException $e) {
            return false;
        }
    }
}