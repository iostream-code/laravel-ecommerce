<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class Webp
{
    /**
     * Simpan gambar upload sebagai WebP (dikecilkan bila melebihi $maxLebar px).
     * Mengembalikan path relatif pada disk, mis. "produk/abc123.webp".
     */
    public static function simpan(
        UploadedFile $file,
        string $folder,
        string $disk = 'public',
        int $maxLebar = 1600,
        int $kualitas = 82,
    ): string {
        $sumber = @imagecreatefromstring($file->getContent());
        if ($sumber === false) {
            throw new RuntimeException('File bukan gambar yang valid.');
        }

        // Pertahankan transparansi (PNG/WebP sumber)
        imagepalettetotruecolor($sumber);
        imagealphablending($sumber, true);
        imagesavealpha($sumber, true);

        $lebar = imagesx($sumber);
        if ($lebar > $maxLebar) {
            $sumber = imagescale($sumber, $maxLebar, -1, IMG_BICUBIC);
        }

        ob_start();
        imagewebp($sumber, null, $kualitas);
        $isi = ob_get_clean();
        imagedestroy($sumber);

        $path = trim($folder, '/') . '/' . Str::random(24) . '.webp';
        if (!Storage::disk($disk)->put($path, $isi)) {
            throw new RuntimeException('Gagal menyimpan gambar.');
        }

        return $path;
    }
}
