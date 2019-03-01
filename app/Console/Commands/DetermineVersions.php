<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SebastianBergmann\CodeCoverage\Report\PHP;

class DetermineVersions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:versions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Determine and cache versions of dependent binaries.';

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
        $java = config('binaries.java');
        if (stripos($java, ' ') !== false) {
            $java = '"'.$java.'"';
        }
        $apktool = escapeshellarg(config('binaries.apktool'));
        exec("$java -jar $apktool --version", $output);
        $versions['apktool'] = trim(implode('', $output));
        unset($output);
        $jadx = config('binaries.jadx');
        if (stripos($jadx, ' ') !== false) {
            $jadx = '"'.$jadx.'"';
        }
        exec("$jadx --version", $output);
        $versions['jadx'] = trim(implode('', $output));
        $versions = '<?php'.PHP_EOL.PHP_EOL.'return '.var_export($versions, true).';'.PHP_EOL;
        file_put_contents(config_path('versions.php'), $versions);
    }
}
