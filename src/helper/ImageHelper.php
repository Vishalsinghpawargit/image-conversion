<?php
namespace TantraGyan\ImageConvert\helper;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Images;

class ImageHelper
{
    /**
     * To save the image in the Object Storage
     *
     * @param [String] $path
     * @param Images $uploadedImage
     * @param [String] $unique_name
     * @return void
     */
    public static function saveImage($path, $image, $objectStore = 0, $sufix = null)
    {
        try {
            $extension      = ImageHelper::getExtension($image);
            $imageStoreName = ImageHelper::sanitizeName($image, $sufix, $extension);

            if ($objectStore) {
                return FileHelper::saveImageToObjectStorage($path, $image, $imageStoreName);
            } else {
                return FileHelper::saveImageLocally($path, $image, $imageStoreName);
            }

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * To resize and save the image in the object storage
     *
     * @param [type] $path
     * @param [type] $image
     * @param [type] $unique_name
     * @param [type] $height
     * @param [type] $width
     * @return void
     */
    public static function resizeSaveImage($path, $image, $height, $width, $objectStore = 0, $suffix = null)
    {
        try {

            $extension      = ImageHelper::getExtension($image);
            $imageStoreName = ImageHelper::sanitizeName($image, $suffix, $extension);

            if ($objectStore) {
                return FileHelper::resizeSaveImageToObjectStorage($path, $image, $imageStoreName, $height, $width);
            } else {
                return FileHelper::resizeSaveImageLocally($path, $image, $imageStoreName, $height, $width);
            }

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * To save the webp image in the Object Storage
     *
     * @param [type] $path
     * @param [type] $image
     * @param [type] $unique_name
     * @return void
     */
    public static function saveWebpImage($path, $image, $objectStore = 0, $sufix = null)
    {
        try {

            $imageStoreName = ImageHelper::sanitizeName($image, $sufix, 'webp');

            if ($objectStore) {
                return FileHelper::saveImageToObjectStorage($path, $image, $imageStoreName);
            } else {
                return FileHelper::saveWebpImageLocally($path, $image, $imageStoreName);
            }

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * To Resize, Convert and Save the webp image in the Object Storage
     *
     * @param [type] $path
     * @param [type] $image
     * @param [type] $unique_name
     * @param [type] $dimensionFirst
     * @param [type] $dimensionSecond
     * @return void
     */
    public static function resizeSaveWebpImage($path, $image, $height, $width, $objectStore = 0, $suffix = null)
    {
        try {

            $imageStoreName = ImageHelper::sanitizeName($image, $suffix, 'webp');
            if ($objectStore) {
                return FileHelper::resizeSaveImageToObjectStorage($path, $image, $imageStoreName, $height, $width);
            } else {
                return FileHelper::resizeSaveImageLocally($path, $image, $imageStoreName, $height, $width);
            }

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    public static function base64_to_jpeg($base64_string)
    {

        $data = explode(',', $base64_string);

        return $image = Images::make(base64_decode($data[1]));

    }

    public static function createDirectory($path)
    {
        if (! is_dir($path)) {
            //Directory does not exist, so lets create it.
            mkdir($path, 0755, true);
        }
    }

    public static function uploadBase64Image($image, $imagePath, $name, $objectStore = 0)
    {
        try {
            if ($objectStore) {
                // Initialize S3 client
                $s3Client = S3ClientHelper::s3Connect();

                $storageFolder = config('app.storage_folder');

                $path = $storageFolder . $imagePath . $name . time() . ".png";
                // Decode the base64 data into binary format
                $imageData = base64_decode($image);

                $result = S3ClientHelper::putObjectBody($s3Client, $path, $imageData);

                return  $path; // Assuming you want to return the URL of the uploaded image
            } else {
                if (! is_dir($imagePath)) {
                    //Directory does not exist, so lets create it.
                    mkdir($imagePath, 0755, true);
                }
                $imageName             = $name . time() . '.png';
                $postCoverImageNewName = $imagePath . $imageName;
                $image                 = Images::make(base64_decode($image));
                $file                  = $image->save(public_path($postCoverImageNewName));
                return $postCoverImageNewName;
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    public static function uploadPdf($file, $filePath, $filePrefix = 'pdf', $objectStore = 0)
    {
        try {
            $extension = ImageHelper::getExtension($file);
            $fileName  = ImageHelper::sanitizeName($file, '-pdf', $extension);

            if ($objectStore) {
                // Initialize S3 client
                $s3Client = S3ClientHelper::s3Connect();

                //created path
                $storePath = $filePath . $fileName;

                // Upload the file to S3
                $upload = S3ClientHelper::putObjectFile($s3Client, $storePath, $file);

                $endpoint =  $storePath;
                return ['endpoint' => $endpoint, 'fileName' => $fileName];
            } else {
                $pdfNewName = $filePath . $fileName;

                $attachmentPath = public_path() . $filePath;

                $file->move($attachmentPath, $pdfNewName);

                return ['endpoint' => $pdfNewName, 'fileName' => $fileName];
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    public static function uploadVideo($path, $image, $objectStore = 0)
    {
        try {
            $imageStoreName = str_replace([' ', '\'', '"', ',', ';', '<', '>', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '?', ':'], '', $image->getClientOriginalName());

            if ($objectStore) {
                return FileHelper::saveImageToObjectStorage($path, $image, $imageStoreName);
            } else {
                return FileHelper::saveImageLocally($path, $image, $imageStoreName);
            }

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    public static function sanitizeName($image, $sufix, $extension)
    {
        return pathinfo(str_replace(' ', '', $image->getClientOriginalName()), PATHINFO_FILENAME) . ($sufix ?? '') . '.' . $extension;
    }

    public static function getExtension($image)
    {
        return pathinfo(str_replace(' ', '', $image->getClientOriginalName()), PATHINFO_EXTENSION);
    }
}
