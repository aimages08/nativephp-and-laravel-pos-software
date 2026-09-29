<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackupController extends Controller
{
    protected function backupDir(): string
    {
        return storage_path('app/backups');
    }

    public function index()
    {
        $dir = $this->backupDir();
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $files = collect(glob($dir . '/*.sqlite'))
            ->map(fn($f) => [
                'name' => basename($f),
                'size' => filesize($f),
                'time' => filemtime($f),
            ])
            ->sortByDesc('time')
            ->values();

        return view('backups.index', [
            'files' => $files,
            'googleDrivePath' => setting('backup_google_drive_path', ''),
        ]);
    }

    public function create()
    {
        $dir = $this->backupDir();
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $src = config('database.connections.nativephp.database')
            ?? database_path('nativephp.sqlite');

        if (!file_exists($src)) {
            return back()->with('success', 'Database file not found.');
        }

        $name = 'backup-' . date('Y-m-d-His') . '.sqlite';
        copy($src, $dir . '/' . $name);

        // Copy to Google Drive folder if configured
        $gd = setting('backup_google_drive_path');
        if ($gd && is_dir($gd)) {
            copy($dir . '/' . $name, rtrim($gd, '/\\') . DIRECTORY_SEPARATOR . $name);
        }

        // Keep only 30 most recent
        $all = collect(glob($dir . '/*.sqlite'))->sortByDesc(fn($f) => filemtime($f))->values();
        foreach ($all->slice(30) as $old) @unlink($old);

        return back()->with('success', "Backup created: $name");
    }

    public function download(string $name)
    {
        $path = $this->backupDir() . '/' . basename($name);
        abort_unless(file_exists($path), 404);
        return response()->download($path);
    }

    public function restore(Request $request, string $name)
    {
        $path = $this->backupDir() . '/' . basename($name);
        abort_unless(file_exists($path), 404);

        $target = config('database.connections.nativephp.database')
            ?? database_path('nativephp.sqlite');

        // Backup current first
        $safety = $this->backupDir() . '/pre-restore-' . date('Y-m-d-His') . '.sqlite';
        copy($target, $safety);

        copy($path, $target);

        return redirect()->route('backups.index')
            ->with('success', "Restored $name. Safety copy saved as " . basename($safety));
    }

    public function destroy(string $name)
    {
        $path = $this->backupDir() . '/' . basename($name);
        if (file_exists($path)) @unlink($path);
        return back()->with('success', 'Backup deleted.');
    }

    public function saveGoogleDrivePath(Request $request)
    {
        $data = $request->validate([
            'backup_google_drive_path' => 'nullable|string|max:500',
        ]);
        \App\Models\Setting::set('backup_google_drive_path', $data['backup_google_drive_path'] ?? '');
        return back()->with('success', 'Google Drive path saved.');
    }
}