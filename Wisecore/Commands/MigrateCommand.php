<?php

namespace Wisecore\Commands;

use Wisecore\WiseTrait;

class MigrateCommand
{
    use WiseTrait;

    /**
     * Command name
     * @var string
     */
    public $command = 'migrate';

    /**
     * Command description
     * @var string
     */

    public $description = "Migrations control";

    /**
     * Codeigniter instance
     * @var
     */
    protected $ci;

    /**
     * Codeigniter Config object
     * @var object
     */
    protected $cfg;

    /**
     * Associative array of local keys
     * --key => methodName
     * @var array
     */
    protected $keysAssociate = [
        '--help' => 'showHelp',
        '--list' => 'showList',
        '--current' => 'current',
        '--roll' => 'rolling'
    ];

    /**
     * Command body
     * @param array $params
     * @return $this
     */
    public function handler(array $params)
    {
        if (count($params) == 0) {
            $this->error(['Missing parameters', 'Use --help']);
        }

        $this->initCodeigniter();

        $this->callLocalKeys($params);

        exit;
    }

    private function showList()
    {
        if (version_compare(CI_VERSION, '3.0.0', 'ge')){
            $migrations = $this->ci->migration->find_migrations();
        } else {
            $cfg = $this->cfg->item('migration');
            $iterator = new \DirectoryIterator($cfg['migration_path']);
            $migrations = [];
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $name = $file->getFilename();
                    $key = preg_replace('/^(\d+)_.+/', '$1', $name);
                    $migrations[$key] = $name;
                }
            }
            if (count($migrations) > 0) {
                natsort($migrations);
            }
        }
        if (count($migrations) > 0) {
            $message = [
                str_pad('----- Migrations :', 100, '-'),
            ];
            foreach ($migrations as $key => $migration) {
                $message[] = "$key = $migration";
            }
            $message[] = str_pad('-', 100, '-');
            $this->response($message);
        }
    }

    private function current()
    {
        if (! $this->ci->migration->current()) {
            $this->error($this->ci->migration->error_string());
        }
    }

    /**
     *
     * @param $roll
     */
    private function rolling($roll)
    {
        if (! is_numeric($roll)) {
            $this->error(['Roll value must be numeric', 'Use --help']);
        }
        $this->ci->migration->version($roll);
        exit;
    }

    /**
     *  Help message
     */
    private function showHelp()
    {
        $this->response([
            'Example: php wiseci migrate <command>',
            '--list : Show all migration files',
            '--current : Load current migration',
            '--roll <sequential|timestamp> : roll back changes or step forwards programmatically to specific versions',
            '--roll 5',
            '--roll 201511031245',
        ]);
    }

    /**
     * Initialized Codeigigniter via ass
     */
    private function initCodeigniter()
    {
        require_once BASEPATH .'/core/Common.php';
         define ('VIEWPATH', APPPATH .'views/');
        $load = load_class('Loader', 'core');
        $lang = load_class('Lang', 'core');
        $this->cfg = load_class('Config', 'core');
        $input = load_class('Input', 'core');
        $this->ci = new \CI_Controller();
        $this->cfg->load('migration', true);
        $this->ci->migration = new \CI_Migration($this->cfg->item('migration'));
    }

}