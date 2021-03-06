<?php

namespace Tinkeshwar\Imager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearCache extends Command
{
    protected $signature = 'imager:clear';
    protected $description = 'Clear Image Cache';

    public function handle()
    {
        if ($this->shouldClearCache()) {
            $this->info('Clearing cache...');
            Storage::disk(config('filesystems.default'))->deleteDirectory('image-cache');
            $this->info('Cache cleared...');
        }
    }

    private function shouldClearCache()
    {
        return $this->confirm('Are you sure want to clear image config?', false);
    }
}
