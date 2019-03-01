<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CleanupOldFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup decompiled APKs, archives, data older than 1 day.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $maxage = time() - (3600 * 24 * 1);
        $archives = Storage::disk()->files('archives');
        foreach ($archives as $archive) {
            if (Str::endsWith($archive, '.gitignore')) {
                echo 'Skipping ', $archive, PHP_EOL;
                continue;
            }
            $mtime = filemtime(Storage::path($archive));
            if ($mtime < $maxage) {
                Storage::delete($archive);
                echo 'Deleted ', $archive, PHP_EOL;
            }
        }
        $decompiled = Storage::disk()->directories('decompiled');
        foreach ($decompiled as $directory) {
            $mtime = filemtime(Storage::path($directory));
            if ($mtime < $maxage) {
                Storage::deleteDirectory($directory);
                echo 'Deleted ', $directory, PHP_EOL;
            }
        }
        $uploads = Storage::disk()->files('uploads');
        foreach ($uploads as $upload) {
            if (Str::endsWith($upload, '.gitignore')) {
                echo 'Skipping ', $upload, PHP_EOL;
                continue;
            }
            $mtime = filemtime(Storage::path($upload));
            if ($mtime < $maxage) {
                Storage::delete($upload);
                echo 'Deleted ', $upload, PHP_EOL;
            }
        }
    }
}
