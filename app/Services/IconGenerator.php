<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;

class IconGenerator
{
    public static function generateSet(string $sourceRelativePath, Tenant $tenant): bool
    {
        $sourceAbsolute = Storage::disk('public')->path($sourceRelativePath);

        $source = self::loadImage($sourceAbsolute);
        if (! $source) {
            return false;
        }

        $dir = 'icons/'.$tenant->id;
        Storage::disk('public')->makeDirectory($dir);

        $primaryColor = self::hexToRgb($tenant->brand_primary_color ?? '#dc2626');

        self::saveResized($source, 192, 192, Storage::disk('public')->path("$dir/icon-192.png"));
        self::saveResized($source, 512, 512, Storage::disk('public')->path("$dir/icon-512.png"));
        self::saveMaskable($source, 512, $primaryColor, Storage::disk('public')->path("$dir/icon-512-maskable.png"));
        self::saveFlattened($source, 180, [255, 255, 255], Storage::disk('public')->path("$dir/apple-touch-icon.png"));

        imagedestroy($source);

        return true;
    }

    private static function loadImage(string $path)
    {
        $info = @getimagesize($path);
        if (! $info) {
            return null;
        }

        return match ($info[2]) {
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default => null,
        };
    }

    private static function saveResized($source, int $width, int $height, string $outPath): void
    {
        $srcW = imagesx($source);
        $srcH = imagesy($source);

        $dst = imagecreatetruecolor($width, $height);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $width, $height, $transparent);

        imagecopyresampled($dst, $source, 0, 0, 0, 0, $width, $height, $srcW, $srcH);
        imagepng($dst, $outPath);
        imagedestroy($dst);
    }

    private static function saveMaskable($source, int $size, array $rgb, string $outPath): void
    {
        $srcW = imagesx($source);
        $srcH = imagesy($source);

        $dst = imagecreatetruecolor($size, $size);
        $bg = imagecolorallocate($dst, $rgb[0], $rgb[1], $rgb[2]);
        imagefilledrectangle($dst, 0, 0, $size, $size, $bg);

        $inner = (int) round($size * 0.8);
        $offset = (int) round(($size - $inner) / 2);
        imagecopyresampled($dst, $source, $offset, $offset, 0, 0, $inner, $inner, $srcW, $srcH);

        imagepng($dst, $outPath);
        imagedestroy($dst);
    }

    private static function saveFlattened($source, int $size, array $rgb, string $outPath): void
    {
        $srcW = imagesx($source);
        $srcH = imagesy($source);

        $dst = imagecreatetruecolor($size, $size);
        $bg = imagecolorallocate($dst, $rgb[0], $rgb[1], $rgb[2]);
        imagefilledrectangle($dst, 0, 0, $size, $size, $bg);

        imagecopyresampled($dst, $source, 0, 0, 0, 0, $size, $size, $srcW, $srcH);
        imagepng($dst, $outPath);
        imagedestroy($dst);
    }

    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            return [220, 38, 38];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
