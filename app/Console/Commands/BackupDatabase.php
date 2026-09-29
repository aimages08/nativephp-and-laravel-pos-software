<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'pos:backup';
    protected $description = 'Create an automatic backup of the database';

    public function handle(): int
    {
        $src = config('database.connections.nativephp.database') ?? database_path('nativephp.sqlite');
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        if (!file_exists($src)) {
            $this->error("DB not found: $src");
            return 1;
        }

        $name = 'auto-' . date('Y-m-d-His') . '.sqlite';
        copy($src, $dir . '/' . $name);

        // Google Drive copy
        $gd = \App\Models\Setting::get('backup_google_drive_path');
        if ($gd && is_dir($gd)) {
            copy($dir . '/' . $name, rtrim($gd, '/\\') . DIRECTORY_SEPARATOR . $name);
            $this->info("Copied to Google Drive: $gd");
        }

        // Keep only 30 most recent
        $all = collect(glob($dir . '/*.sqlite'))->sortByDesc(fn($f) => filemtime($f))->values();
        foreach ($all->slice(30) as $old) @unlink($old);

        $this->info("Backup created: $name");
        return 0;
    }
}