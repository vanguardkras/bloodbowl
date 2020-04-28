<?php


namespace App\Services;

use Gumlet\ImageResize;
use Gumlet\ImageResizeException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploader
{
    /**
     * Allowed file extensions.
     */
    private const MIMES = 'jpeg,jpg,jpe,png,bmp';

    /**
     * Validate and store an image file.
     *
     * @param string $fieldName
     * @param string $directory
     * @param string|null $oldImage
     * @return string File web path
     * @throws ImageResizeException
     */
    public static function store(string $fieldName, string $directory, string $oldImage = null)
    {
        request()->validate([$fieldName => 'required|file|mimes:' . self::MIMES]);
        $image = request()->$fieldName->path();

        $fileRandomName = Str::random(40) . '.jpg';
        $filePath = Storage::disk('public')->path($directory);

        $imageResize = new ImageResize($image);
        $imageResize->crop(200, 200);
        $imageResize->save($filePath . DIRECTORY_SEPARATOR . $fileRandomName, IMAGETYPE_JPEG, 90);

        if ($oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return "$directory/$fileRandomName";
    }
}
