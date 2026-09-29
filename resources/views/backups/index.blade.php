@extends('layouts.app')
@section('title', 'Backups')

@section('content')
<div class="card" style="margin-bottom:16px;">
    <h2 style="margin-top:0;">Backups</h2>
    <p style="color:#64748b;font-size:14px;">Manual backups + auto-backups run hourly (30 kept).</p>
    <form action="{{ route('backups.create') }}" method="POST">
        @csrf
        <button class="btn btn-primary">💾 Backup Now</button>
    </form>
</div>

<div class="card" style="margin-bottom:16px;">
    <h3 style="margin-top:0;">Google Drive Backup Folder</h3>
    <p style="color:#64748b;font-size:13px;">Install Google Drive for Desktop, then paste the synced folder path here (e.g. <code>G:\My Drive\POS-Backups</code>). Every backup is copied here → auto-synced to cloud.</p>
    <form action="{{ route('backups.google-drive') }}" method="POST" style="display:flex;gap:12px;align-items:end;">
        @csrf
        <div style="flex:1;">
            <label>Google Drive Path</label>
            <input name="backup_google_drive_path" value="{{ $googleDrivePath }}" placeholder="G:\My Drive\POS-Backups">
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>

<div class="card">
    <h3 style="margin-top:0;">Recent Backups ({{ $files->count() }})</h3>
    <table>
        <thead><tr>
            <th>File</th>
            <th style="text-align:right;">Size</th>
            <th>Created</th>
            <th></th>
        </tr></thead>
        <tbody>
        @forelse($files as $f)
            <tr>
                <td><code style="font-size:12px;">{{ $f['name'] }}</code></td>
                <td style="text-align:right;">{{ number_format($f['size'] / 1024, 1) }} KB</td>
                <td>{{ date('Y-m-d H:i', $f['time']) }}</td>
                <td style="text-align:right;white-space:nowrap;">
                    <a href="{{ route('backups.download', $f['name']) }}" style="color:#3b82f6;">Download</a>
                    <form action="{{ route('backups.restore', $f['name']) }}" method="POST" style="display:inline;" onsubmit="return confirm('Restore this backup? Current DB will be saved as pre-restore.')">
                        @csrf
                        <button style="background:none;border:none;color:#f59e0b;cursor:pointer;margin-left:8px;">Restore</button>
                    </form>
                    <form action="{{ route('backups.destroy', $f['name']) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this backup?')">
                        @csrf @method('DELETE')
                        <button style="background:none;border:none;color:#ef4444;cursor:pointer;margin-left:8px;">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:30px;">No backups yet. Click "Backup Now".</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection