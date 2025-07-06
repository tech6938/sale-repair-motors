<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

trait FileUploader
{
    /**
     * Uploads a file to the given directory in the public storage.
     * If $oldFile is provided, it will be removed from the storage before uploading the new file.
     * The method returns the path of the newly uploaded file.
     *
     * @param $inputFile
     * @param string $directory
     * @param string|null $oldFile
     * @return string
     */
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

    /**
     * Uploads an image to the specified directory in public storage.
     * If $oldImage is provided, it will be removed from storage before uploading the new image.
     * A thumbnail of the image is also generated and saved.
     * The image is scaled down if its dimensions exceed 1024x1024.
     * Returns the path of the newly uploaded image.
     *
     * @param mixed $inputFile The image file to be uploaded.
     * @param string $directory The directory where the image will be uploaded.
     * @param string|null $oldImage The path of the old image to be removed, if any.
     * @return string The path of the newly uploaded image.
     */
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

    /**
     * Generates a unique filename for an input file.
     * The filename is of the format "{uuid}_{unix_timestamp}.{extension}".
     * If the extension is not provided, it is automatically derived from the input file.
     * 
     * @param mixed $inputFile The input file to generate a filename for.
     * @param string|null $extension The extension to use for the filename. Defaults to null.
     * @return string The generated filename.
     */
    private function generateFileNameFromInput($inputFile, ?string $extension = null): string
    {
        $extension = !empty($extension) ? $extension : $inputFile->getClientOriginalExtension();
        return sprintf('%s_%s.%s', Str::uuid(), time(), $extension);
    }

    /**
     * Generates a thumbnail of an input file.
     * The thumbnail is saved in the same directory as the input file, but with a "thumbnails" prefix.
     * The thumbnail is scaled to a width of 285px. The original image is not modified.
     * If $oldImage is provided, it will be removed from the storage after the thumbnail is generated.
     * 
     * @param mixed $inputFile The input file to generate a thumbnail for.
     * @param string $directory The directory to save the thumbnail in.
     * @param string $fileName The filename to use for the thumbnail.
     * @param string|null $oldImage The path of the old image to remove from the storage. Defaults to null.
     * @return void
     */
    private function generateThumbnail($inputFile, string $directory, string $fileName, ?string $oldImage = null): void
    {
        $this->makeDirectoryIfNotExists("thumbnails/$directory");

        $img = ImageManager::imagick()->read($inputFile);

        $img->scale(285, null);

        $img->toPng();

        $img->save(storage_path("app/public/thumbnails/$directory/$fileName"));

        $this->removePublicFile('thumbnails/' . $oldImage);
    }

    /**
     * Removes a file from the public storage.
     * If the file does not exist, no action is taken.
     * 
     * @param string|null $filePath The path of the file to remove. Defaults to null.
     * @return void
     */
    public function removePublicFile(?string $filePath = null): void
    {
        if (
            !empty($filePath)
            && Storage::disk('public')->exists($filePath)
        ) {
            Storage::disk('public')->delete($filePath);
        }
    }

    /**
     * Removes an image from the public storage, as well as its thumbnail.
     * If the image does not exist, no action is taken.
     * 
     * @param string|null $filePath The path of the image to remove. Defaults to null.
     * @return void
     */
    public function removePublicImage(?string $filePath = null): void
    {
        $this->removePublicFile($filePath);
        $this->removePublicFile('thumbnails/' . $filePath);
    }

    /**
     * Checks if a directory exists in the public storage and creates it if not.
     * 
     * @param string $directory The directory to check/create.
     * @return void
     */
    private function makeDirectoryIfNotExists(string $directory): void
    {
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory("$directory");
        }
    }
}
