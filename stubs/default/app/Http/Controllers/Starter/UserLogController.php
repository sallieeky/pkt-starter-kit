<?php

namespace App\Http\Controllers\Starter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class UserLogController extends Controller
{
    public function userLogPage(Request $request){
        return Inertia::render('User/UserLogManage',[
            'log_files' => $this->getLogFileList(),
        ]);
    }
    private function getLogFileList(){
        $directoryPath = storage_path('/logs');
        $files = File::files($directoryPath);

        $fileList = [];

        foreach ($files as $file) {
            $fileList[] = $file->getFilename();
        }
        return $fileList;
    }
    public function getLogFileDetail(Request $request, $fileName){
        $filePath = storage_path('/logs').'/'.$fileName;
        if (File::exists($filePath)) {
            $fileContents = File::get($filePath);

            $logEntries = explode("\n", trim($fileContents));
            $collection = collect();

            foreach ($logEntries as $logEntry) {
                if (preg_match('/^\[(.*?)\]\s(.*?):\s(.*?)$/', $logEntry, $matches)) {
                    $timestamp = $matches[1];
                    $logStatus = $matches[2];
                    $logData = json_decode($matches[3], true);

                    if ($logData) {
                        $logData['timestamp'] = $timestamp;
                        $logData['log_status'] = $logStatus;

                        $collection->push($logData);
                    }
                }
            }

            $collection = $collection->sortByDesc('timestamp')->values();

            return response()->json([
                'status' => true,
                'message' => 'Successfully retrieved log detail',
                'data' => [
                    'log_detail' => $collection,
                ]
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve log detail',
                'data' => []
            ]);
        }
    }
}
