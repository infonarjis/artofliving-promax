<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;

class DownloadBackupController extends Controller
{
    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
    }
    ## Download Databse:
    public function downloadDatabase()
    {
        // $dbHost = env('DB_HOST');
        // $dbUser = env('DB_USERNAME');
        // $dbPass = env('DB_PASSWORD');
        // $dbName = env('DB_DATABASE');
        // $fileName = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        // $filePath = storage_path($fileName);

        // $command = "mysqldump -h$dbHost -u$dbUser -p$dbPass $dbName > $filePath";

        // Execute the command :
        // Process::fromShellCommandline($command)->run();

        // Return the file as a download :
        // if (file_exists($filePath)) {
        //     return response()->download($filePath)->deleteFileAfterSend(true);
        // }

        return response()->json(['error' => 'Database export failed'], 500);
    }
}
