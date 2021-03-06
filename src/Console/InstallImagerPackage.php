<?php

namespace Tinkeshwar\Imager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallImagerPackage extends Command
{
    protected $signature = 'imager:install';
    protected $description = 'Install the Imager Package';

    public function handle()
    {
        $this->info('Installing Imager Package...');
        $this->info('Publishing configuration...');

        if (!$this->configExists('image.php')) {
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }
        $this->call('migrate');
        $this->info('Installed Imager Package');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm('Config file already exists. Do you want to overwrite it?', false);
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Tinkeshwar\Imager\ImagerServiceProvider",
            '--tag' => "config"
        ];
        if ($forcePublish === true) {
            $params['--force'] = '';
        }
        $this->call('vendor:publish', $params);
    }
}
