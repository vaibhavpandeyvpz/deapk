<?php

namespace App\Jobs;

use App\Events\DecompileFinished;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DecompileApk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    private $data;

    /**
     * Create a new job instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $code = -1;
        if (is_file($this->data['path'])) {
            $apk = escapeshellarg($this->data['path']);
            $dir = escapeshellarg(storage_path('app/decompiled/'.$this->data['id']));
            $java = config('binaries.java');
            if (stripos($java, ' ') !== false) {
                $java = '"'.$java.'"';
            }
            $apktool = escapeshellarg(config('binaries.apktool'));
            exec("$java -jar $apktool d -o $dir $apk", $output, $code);
            if (($code === 0) && $this->data['sources']) {
                $jadx = config('binaries.jadx');
                if (stripos($jadx, ' ') !== false) {
                    $jadx = '"'.$jadx.'"';
                }
                exec("$jadx -r -d $dir $apk");
            }
            unlink($this->data['path']);
        }
        event(new DecompileFinished($this->data['id'], $code === 0));
    }
}
