<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;

class ServeCommand extends BaseServeCommand
{
    /**
     * The environment variables that should be passed from host machine to the PHP server process.
     * Overridden to include TEMP and TMP on Windows so the built-in server can bind correctly.
     *
     * @var string[]
     */
    public static $passthroughVariables = [
        'APP_ENV',
        'HERD_PHP_81_INI_SCAN_DIR',
        'HERD_PHP_82_INI_SCAN_DIR',
        'HERD_PHP_83_INI_SCAN_DIR',
        'HERD_PHP_84_INI_SCAN_DIR',
        'IGNITION_LOCAL_SITES_PATH',
        'LARAVEL_SAIL',
        'PATH',
        'PHP_IDE_CONFIG',
        'SYSTEMROOT',
        'XDEBUG_CONFIG',
        'XDEBUG_MODE',
        'XDEBUG_SESSION',
        'TEMP',
        'TMP',
    ];

    /**
     * Start a new server process using a minimal environment (avoid false-valued keys on Windows).
     *
     * @param  bool  $hasEnvironment
     * @return \Symfony\Component\Process\Process
     */
    protected function startProcess($hasEnvironment)
    {
        $env = [];

        if ($hasEnvironment) {
            foreach (static::$passthroughVariables as $key) {
                if (array_key_exists($key, $_ENV)) {
                    $env[$key] = $_ENV[$key];
                }
            }
        }

        $env['PHP_CLI_SERVER_WORKERS'] = $this->phpServerWorkers;

        $process = new \Symfony\Component\Process\Process(
            $this->serverCommand(),
            public_path(),
            $env
        );

        $this->trap(fn () => [SIGTERM, SIGINT, SIGHUP, SIGUSR1, SIGUSR2, SIGQUIT], function ($signal) use ($process) {
            if ($process->isRunning()) {
                $process->stop(10, $signal);
            }

            exit;
        });

        $process->start($this->handleProcessOutput());

        return $process;
    }
}
