<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

trait FileUploader
{
    protected function uploadPublicFile($inputFile, string $directory, ?string $oldFile = null): string
    {
        $this->makeDirectoryIfNotExists($directory);

        $newFilePath = Storage::disk('public')->putFileAs(
            $directory,
            $inputFile,
            $this->generateFileNameFromInput($inputFile)
        );

        $this->removePublicFile($oldFile);

        return $newFilePath;
    }

    protected function uploadPublicImage($inputFile, string $directory, ?string $oldImage = null): string
    {
        $this->makeDirectoryIfNotExists($directory);

        $fileName = $this->generateFileNameFromInput($inputFile);

        // Generate thumbnail
        $this->generateThumbnail($inputFile, $directory, $fileName, $oldImage);

        $img = ImageManager::imagick()->read($inputFile);

        if ($img->width() > 1024 || $img->height() > 1024) {
            $img->scale(1024, null);
        }

        $img->toPng();

        $img->save(storage_path("app/public/$directory/$fileName"));

        $this->removePublicFile($oldImage);

        return $directory . DIRECTORY_SEPARATOR . $fileName;
    }

    private function generateFileNameFromInput($inputFile, ?string $extension = null): string
    {
        $extension = !empty($extension) ? $extension : $inputFile->getClientOriginalExtension();
        return sprintf('%s_%s.%s', Str::uuid(), time(), $extension);
    }

    private function generateThumbnail($inputFile, string $directory, string $fileName, ?string $oldImage = null): void
    {
        $this->makeDirectoryIfNotExists("thumbnails/$directory");

        $img = ImageManager::imagick()->read($inputFile);

        $img->scale(285, null);

        $img->toPng();

        $img->save(storage_path("app/public/thumbnails/$directory/$fileName"));

        $this->removePublicFile('thumbnails/' . $oldImage);
    }

    public function removePublicFile(?string $filePath = null): void
    {
        if (
            !empty($filePath)
            && Storage::disk('public')->exists($filePath)
        ) {
            Storage::disk('public')->delete($filePath);
        }
    }

    public function removePublicImage(?string $filePath = null): void
    {
        $this->removePublicFile($filePath);
        $this->removePublicFile('thumbnails/' . $filePath);
    }

    private function makeDirectoryIfNotExists(string $directory): void
    {
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory("$directory");
        }
    }
}
