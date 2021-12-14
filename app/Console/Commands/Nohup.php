<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

class Nohup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nohup {cmd} {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nohup commands';

    protected $artisan;
    protected $log_file;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->artisan = base_path('artisan');
        $this->log_file = storage_path('logs/nohup.log');

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sh = $this->argument('cmd');

        $key = 'processes/'.md5($sh);

        $file = Storage::exists($key) ? json_decode(Storage::get($key)) : null;

        $process_id = $file ? (posix_getpgid($file->pid) ? $file->pid : null) : null;

        $type = $this->option('type');

        $types = [
            'start' => 'Start process',
            'restart' => 'Restart process',
            'kill' => 'Kill process'
        ];

        if (!$type)
        {
            $type = $this->choice('What would you like to do?', $types, $type);
        }

        if ($type == 'kill' || $type == 'restart')
        {
            if ($process_id)
                $this->error($this->kill($process_id));
            else
                $this->error('Process is not running');
        }

        if ($type == 'start' || $type == 'restart')
        {
            $process_id = $file ? (posix_getpgid($file->pid) ? $file->pid : null) : null;

            if ($process_id)
                $this->error('['.$process_id.'] process already running');
            else
                $this->info($this->start($key, $sh));
        }
    }

    /**
     * Kill process
     * 
     * @param int $process_id
     * @return string
     */
    public function kill(int $process_id)
    {
        $cmd = "kill -9 $process_id >> $this->log_file 2>&1 & echo \$!";

        $pid = trim(shell_exec($cmd));

        sleep(1);

        return '['.$process_id.'] process killed ('.$pid.')';
    }

    /**
     * Start process
     *
     * @param string $key
     * @param string $sh
     * @return string
     */
    public function start(string $key, string $sh)
    {
        $cmd = "nohup php $this->artisan $sh >> $this->log_file 2>&1 & echo \$!";

        $pid = trim(shell_exec($cmd));

        Storage::put($key, json_encode([ 'pid' => trim($pid), 'command' => $sh ]));

        return '['.$pid.'] process started';
    }
}
