<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerService
{
    protected $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default', 's3');
    }

    public function storeFile($model, array|UploadedFile $files, $folderPath, $relationName = 'attachments', ?callable $typeResolver = null)
    {
        $files = is_array($files) ? $files : [$files];
        $storedRecords = [];

        foreach ($files as $fileData) {
            $file = $fileData instanceof UploadedFile ? $fileData : ($fileData['file'] ?? null);

            if (!$file instanceof UploadedFile) {
                continue;
            }

            $passedType = is_array($fileData) ? ($fileData['type'] ?? null) : null;
            $customProps = is_array($fileData) ? ($fileData['custom_properties'] ?? []) : [];

            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            try {
                $path = $folderPath . '/' . $fileName;

                Storage::disk($this->disk)->put(
                    $path,
                    file_get_contents($file->getRealPath()),
                    'public'
                );
            } catch (\Exception $e) {
                return response()->json([
                    'error_message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ], 500);
            }

            $customProps['extension'] = $file->getClientOriginalExtension();
            $customProps['file_size'] = $file->getSize();

            $finalType = $passedType ?: ($typeResolver ? $typeResolver($file) : $this->detectFileType($file));

            $storedRecords[] = $model->{$relationName}()->create([
                'uuid'              => (string) Str::uuid(),
                'path'              => $path,
                'original_name'     => $file->getClientOriginalName(),
                'type'              => $finalType,
                'custom_properties' => $customProps,
            ]);
        }

        return $storedRecords;
    }

    public function deleteFile($model, $fileId, $relationName = 'attachments')
    {
        $fileRecord = $model->{$relationName}()->find($fileId);
        if (!$fileRecord) {
            throw new NotFoundException("File");
        }

        if (Storage::disk($this->disk)->exists($fileRecord->path)) {
            Storage::disk($this->disk)->delete($fileRecord->path);
        }

        return $fileRecord->delete();
    }

    public function detectFileType($file, $passedType = null)
    {
        if ($passedType === '360_panorama') {
            return '360_panorama';
        }

        $mime = $file->getMimeType();
        if (str_contains($mime, 'image')) return 'image';
        if (str_contains($mime, 'video')) return 'video';
        return 'document';
    }
}
