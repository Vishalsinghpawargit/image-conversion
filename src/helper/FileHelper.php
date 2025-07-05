<?php
namespace TantraGyan\ImageConvert\helper;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class FileHelper
{
    protected static function imageManager()
    {
        return new ImageManager(new GdDriver());
    }

    public static function saveImageLocally($path, $image, $imageStoreName)
    {
        $newPath = public_path($path . '/');
        self::createDirectory($newPath);

        $imageStoreName = str_replace(' ', '', $image->getClientOriginalName());

        $manager     = self::imageManager();
        $imageObject = $manager->read($image->getRealPath());

        $imageObject->save($newPath . $imageStoreName);

        return $path . '/' . $imageStoreName;
    }

    public static function saveWebpImageLocally($path, $image, $imageStoreName)
    {
        $newPath = public_path($path . '/');
        self::createDirectory($newPath);

        $manager     = self::imageManager();
        $imageObject = $manager->read($image->getRealPath());

        $imageObject->encode(new WebpEncoder())->save($newPath . $imageStoreName);

        return $path . '/' . $imageStoreName;
    }

    public static function resizeSaveImageLocally($path, $image, $imageStoreName, $height, $width)
    {
        $newPath = public_path($path . '/');
        self::createDirectory($newPath);

        $manager     = self::imageManager();
        $imageObject = $manager->read($image->getRealPath())->resize($width, $height); // width first

        $imageObject->save($newPath . $imageStoreName);

        return $path . '/' . $imageStoreName;
    }

    public static function saveImageToObjectStorage($path, $image, $imageStoreName)
    {
        $s3Client = S3ClientHelper::s3Connect();
        $fullPath = $path . '/' . $imageStoreName;

        S3ClientHelper::putObject($s3Client, $fullPath, $image->getRealPath());

        return $fullPath;
    }

    public static function resizeSaveImageToObjectStorage($path, $image, $imageStoreName, $height, $width)
    {
        $fullPath = $path . '/' . $imageStoreName;

        $manager     = self::imageManager();
        $imageObject = $manager->read($image->getRealPath())->resize($width, $height);

        $tempImagePath = tempnam(sys_get_temp_dir(), 'resized_image_');
        $imageObject->save($tempImagePath);

        $s3Client = S3ClientHelper::s3Connect();
        S3ClientHelper::putObject($s3Client, $fullPath, $tempImagePath);

        unlink($tempImagePath);

        return $fullPath;
    }

    public static function createDirectory($path)
    {
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
