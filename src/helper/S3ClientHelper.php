<?php
namespace TantraGyan\ImageConvert\helper;

use Aws\S3\S3Client;
use Illuminate\Http\UploadedFile;

class S3ClientHelper
{
    public static function s3Connect()
    {
        return $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => config('ImageConvert.do_spaces.region'),
            'endpoint'    => config('ImageConvert.do_spaces.endpoint'),
            'use_path_style_endpoint' => true, // DO recommends true
            'credentials' => [
                'key'    => config('ImageConvert.do_spaces.key'),
                'secret' => config('ImageConvert.do_spaces.secret'),
            ],
        ]);
    }

    public static function putObject($s3Client, $path, $image)
    {
        $mimeType = mime_content_type($image);

        $s3Client->putObject([
            'Bucket'             => config('ImageConvert.do_spaces.bucket'),
            'Key'                => $path,
            'SourceFile'         => $image,
            'ContentType'        => $mimeType, // e.g. "image/jpeg", "application/pdf", etc.
            'ACL' => 'public-read', // or private if you want to serve with signed URLs
            'ContentDisposition' => 'inline',  // So the browser attempts to display instead of download
        ]);
    }

    public static function putObjectFile($s3Client, $path, $file)
    {
        $mimeType = $file instanceof UploadedFile ? $file->getMimeType() : mime_content_type($file);

        $s3Client->putObject([
            'Bucket'             => config('ImageConvert.do_spaces.bucket'),
            'Key'                => $path,
            'Body'               => file_get_contents($file),
            'ContentType'        => $mimeType,
            'ACL' => 'public-read', // or private if you want to serve with signed URLs
            'ContentDisposition' => 'inline', // So the browser attempts to display instead of download
        ]);
    }

    public static function putObjectBody($s3Client, $path, $image)
    {
        $mimeType = mime_content_type($image);

        $s3Client->putObject([
            'Bucket'             => config('ImageConvert.do_spaces.bucket'),
            'Key'                => $path,
            'Body'               => $image,
            'ContentType'        => $mimeType, // e.g. "image/jpeg", "application/pdf", etc.
            'ACL' => 'public-read', // or private if you want to serve with signed URLs
            'ContentDisposition' => 'inline',  // So the browser attempts to display instead of download
        ]);
    }
}
