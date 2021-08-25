<?php

namespace Bluebik\Logger\Commands;

use Carbon\Carbon;
use File;
use Illuminate\Console\Command;

/**
 * Class LogBackup
 *
 * @package \App\Console\Commands\Log
 */
class LogBackup extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "logs:backup";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "archive file logs [access, action, command, error]";

    protected $dateFormat = 'Y-m-d';

    protected $logPath;

    protected $backupPath;

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        $this->logPath = storage_path("logs");
        $this->backupPath = storage_path('backups/logs');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $logs = File::directories($this->logPath);

        foreach ($logs as $logDir) {

            $logDir = substr($logDir, strlen($this->logPath) + 1);

            /**
             * skip to backup log in this day.
             */
            if ($this->shouldSkipArchive($logDir)) {
                continue;
            }

            $this->archiveLog($logDir);
            $this->removeOldLog($logDir);

        }

    }

    protected function backupFileName($logName, $count = 1)
    {

        $ext = "tar.gz";
        if ($count === 1) {
            $fullName = "$logName.$ext";
        } else {
            $fullName = "$logName" . "_" . "$count.$ext";
        }

        if (File::exists("$this->backupPath/$fullName")) {
            return $this->backupFileName($logName, $count + 1);
        }

        return $fullName;
    }

    /**
     * @param $logDir
     */
    public function archiveLog($logDir)
    {
        $backupCmd = "cd $this->logPath && tar -zcvf $this->backupPath/{$this->backupFileName($logDir)} $logDir";
        $this->comment("ดำเนินการบีบอัดไฟล์ ... " . $backupCmd);
        exec($backupCmd);
    }

    /**
     * @param $logDir
     */
    public function removeOldLog($logDir)
    {
        $removeCmd = "rm -rf $this->logPath/$logDir";
        $this->comment("ดำเนินการลบไฟล์ ... " . $removeCmd);
        exec($removeCmd);
    }

    /**
     * @param $logDir
     *
     * @return bool
     */
    public function shouldSkipArchive($logDir)
    {
        return $logDir == Carbon::today()->format($this->dateFormat);
    }

}
