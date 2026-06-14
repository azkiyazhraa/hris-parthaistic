<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CompressUploadedImage
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        // Cek apakah ada request
        if (!request()->hasFile('foto_profil') && !request()->hasFile('attachment')) {
            return;
        }

        $maxSize = 2048; // 2MB dalam KB

        // Compress foto_profil
        if (request()->hasFile('foto_profil')) {
            $file = request()->file('foto_profil');
            if ($file->isValid()) {
                $this->compressImage($file, $maxSize);
            }
        }

        // Compress attachment (jika ada)
        if (request()->hasFile('attachment')) {
            $file = request()->file('attachment');
            if ($file->isValid()) {
                $this->compressImage($file, $maxSize);
            }
        }
    }

    /**
     * Compress image to target size
     */
    private function compressImage(UploadedFile $file, int $maxSizeKB): void
    {
        // Hanya proses file gambar
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return;
        }

        // Jika ukuran sudah di bawah 2MB, skip
        if ($file->getSize() <= $maxSizeKB * 1024) {
            return;
        }

        try {
            // Buat temporary file untuk hasil compress
            $tempPath = $file->getPathname();

            // Gunakan Intervention Image v3
            $manager = new ImageManager(new Driver());
            $image = $manager->read($tempPath);

            // Hitung ukuran awal
            $originalSize = $file->getSize();

            // Mulai dengan quality 80%
            $quality = 80;

            // Compress sampai di bawah 2MB
            while ($quality > 10) {
                // Simpan ke temporary
                $tempOutput = tempnam(sys_get_temp_dir(), 'compressed_');

                $image->save($tempOutput, quality: $quality);

                // Cek ukuran hasil compress
                $compressedSize = filesize($tempOutput);

                // Jika sudah di bawah 2MB, gunakan file ini
                if ($compressedSize <= $maxSizeKB * 1024) {
                    // Replace original file dengan compressed
                    copy($tempOutput, $tempPath);
                    unlink($tempOutput);
                    break;
                }

                unlink($tempOutput);

                // Kurangi quality 10% untuk iterasi berikutnya
                $quality -= 10;
            }

        } catch (\Exception $e) {
            // Jika gagal, biarkan file original
            \Log::warning('Image compression failed: ' . $e->getMessage());
        }
    }
}
