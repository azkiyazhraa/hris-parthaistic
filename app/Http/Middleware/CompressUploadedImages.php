<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CompressUploadedImages
{
    public function handle(Request $request, Closure $next)
    {
        $this->compressImages($request);

        return $next($request);
    }

    private function compressImages(Request $request): void
    {
        $maxSize = 2048; // 2MB in KB

        foreach ($request->allFiles() as $key => $file) {
            if ($file instanceof UploadedFile) {
                $this->compressSingleImage($file, $maxSize);
            } elseif (is_array($file)) {
                foreach ($file as $subFile) {
                    if ($subFile instanceof UploadedFile) {
                        $this->compressSingleImage($subFile, $maxSize);
                    }
                }
            }
        }
    }

    private function compressSingleImage(UploadedFile $file, int $maxSizeKB): void
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return;
        }

        if ($file->getSize() <= $maxSizeKB * 1024) {
            return;
        }

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());

            $quality = 80;

            while ($quality > 10) {
                $tempOutput = tempnam(sys_get_temp_dir(), 'compressed_');
                $image->save($tempOutput, quality: $quality);

                if (filesize($tempOutput) <= $maxSizeKB * 1024) {
                    copy($tempOutput, $file->getPathname());
                    unlink($tempOutput);
                    break;
                }

                unlink($tempOutput);
                $quality -= 10;
            }
        } catch (\Exception $e) {
            \Log::warning('Image compression failed: ' . $e->getMessage());
        }
    }
}
