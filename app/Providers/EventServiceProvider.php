<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\UploadedFile;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Auto compress uploaded images
        $this->autoCompressUploadedImages();
    }

    /**
     * Auto compress all uploaded images
     */
    private function autoCompressUploadedImages(): void
    {
        // Hook ke event sebelum request diproses
        app()->resolving(function ($object, $app) {
            if (request()->isMethod('POST') || request()->isMethod('PUT') || request()->isMethod('PATCH')) {
                $this->compressAllUploadedImages();
            }
        });
    }

    /**
     * Compress all uploaded images in the request
     */
    private function compressAllUploadedImages(): void
    {
        $maxSize = 2048; // 2MB in KB

        foreach (request()->allFiles() as $key => $file) {
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

    /**
     * Compress single uploaded image
     */
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
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file->getPathname());

            $quality = 80;

            while ($quality > 10) {
                $tempOutput = tempnam(sys_get_temp_dir(), 'compressed_');
                $image->save($tempOutput, quality: $quality);

                $compressedSize = filesize($tempOutput);

                if ($compressedSize <= $maxSizeKB * 1024) {
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
