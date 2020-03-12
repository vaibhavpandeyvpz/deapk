<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrowseRequest;
use App\Http\Requests\DecompileRequest;
use App\Http\Requests\DownloadRequest;
use App\Http\Requests\FetchRequest;
use App\Jobs\CreateArchive;
use App\Jobs\DecompileApk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Webpatser\Uuid\Uuid;

class DecompileController extends Controller
{
    public function archive(DownloadRequest $request)
    {
        $data = $request->validated();
        $source = storage_path("app/decompiled/{$data['id']}");
        if (is_dir($source)) {
            CreateArchive::dispatch($data['id']);
            return response('');
        }
        abort(404);
    }

    public function browse(BrowseRequest $request)
    {
        $data = $request->validated();
        $path = str_replace('\\', '/', $data['path']);
        if ((strpos($path, './') !== false) || (strpos($path, '../') !== false)) {
            abort(403);
        }
        $dir = storage_path('app/decompiled/'.$data['id']);
        if ($path !== '/') {
            $dir .= '/'.ltrim($path, '/');
        }
        if (is_dir($dir)) {
            $contents = new \FilesystemIterator($dir);
            $entries = [];
            foreach ($contents as $info) {
                /** @var \SplFileInfo $info */
                $entries[] = [
                    'name' => $info->getFilename(),
                    'size' => $info->isFile() ? $info->getSize() : 0,
                    'type' => $info->isDir() ? 'dir' : 'file',
                ];
            }
            usort($entries, function ($lhs, $rhs) {
                if ($lhs['type'] === $rhs['type']) {
                    return strcmp($lhs['name'], $rhs['name']);
                } else if (($lhs['type'] === 'dir') && ($rhs['type'] !== 'dir')) {
                    return -1;
                } else if (($lhs['type'] !== 'dir') && ($rhs['type'] === 'dir')) {
                    return 1;
                }
                return 0;
            });
            return response()->json($entries);
        } else {
            abort(404);
        }
    }

    public function fetch(FetchRequest $request)
    {
        $data = $request->validated();
        $path = str_replace('\\', '/', $data['path']);
        if ((strpos($path, './') !== false) || (strpos($path, '../') !== false)) {
            abort(403);
        }
        $path = storage_path('app/decompiled/'.$data['id']).'/'.ltrim($path, '/');
        if (is_file($path)) {
            $name = basename($path);
            return response()->file($path, [
                'Content-Disposition' => isset($data['download']) ? "attachment; filename={$name}" : 'inline',
                'Content-Type' => mime_content_type($path),
            ]);
        }
        abort(404);
    }

    public function handle(DecompileRequest $request)
    {
        $data = $request->validated();
        /** @var UploadedFile $apk */
        $apk = $data['apk'];
        $job['id'] = (string) Uuid::generate(4);
        $job['name'] = $apk->getClientOriginalName();
        $job['path'] = storage_path('app/'.$apk->storeAs('uploads', $job['id'].'.apk'));
        $job['sources'] = isset($data['sources']);
        DecompileApk::dispatch($job);
        return response()->json(Arr::only($job, ['id', 'name']));
    }
}
