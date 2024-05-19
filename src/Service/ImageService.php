<?php
declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    private const UPLOADS_PATH = DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads';

    private const ALLOWED_MIME_TYPES_MAP = [
        'image/jpeg' => '.jpg',
        'image/webp' => '.webp',
    ];

    public function moveImageToUploadsAndGetPath(UploadedFile $file): ?string
    {
        if ($file->getError() === UPLOAD_ERR_NO_FILE)
        {
            return null;
        }

        $name = $file->getClientOriginalName();
        $type = $file->getMimeType();
        $imageExt = self::ALLOWED_MIME_TYPES_MAP[$type] ?? null;

        if ($imageExt === null)
        {
            throw new InvalidArgumentException("File '$name' is not an image");
        }

        $destFileName = uniqid('image', true) . $imageExt;
        return $this->moveFileToUploads($file, $destFileName);
    }

    public function getUploadUrlPath(string $fileName): string
    {
        return "/uploads/$fileName";
    }

    private function getUploadPath(string $fileName): string
    {
        $uploadPath = dirname(__DIR__, 2) . self::UPLOADS_PATH;
        if (!$uploadPath || !is_dir($uploadPath))
        {
            throw new RuntimeException('Invalid uploads path: ' . self::UPLOADS_PATH);
        }

        return $uploadPath . DIRECTORY_SEPARATOR . $fileName;
    }

    private function moveFileToUploads(UploadedFile $file, string $destFileName): string
    {
        $fileName = $file->getClientOriginalName();
        $destPath = $this->getUploadPath($destFileName);
        $srcPath = $file->getRealPath();

        if (!@move_uploaded_file($srcPath, $destPath))
        {
            throw new RuntimeException("Failed to uploads file $fileName");
        }

        return $destFileName;
    }
}
