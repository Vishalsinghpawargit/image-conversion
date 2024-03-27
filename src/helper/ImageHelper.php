<?php
namespace TantraGyan\ImageConvert\helper;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Images;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use PDO;

//constant define
define("BUCKETNAME" ,     config('ImageConvert.do_spaces.bucket') ,         true);
define("REGION" ,         config('ImageConvert.do_spaces.region') ,         true);
define("DOKEY" ,          config('ImageConvert.do_spaces.key') ,            true);
define("DOSECRET" ,       config('ImageConvert.do_spaces.secret') ,         true);
define("DOENDPOINT" ,     config('ImageConvert.do_spaces.endpoint') ,       true);
define("DOFULLENDPOINT" , config('ImageConvert.do_spaces.originendpoint') , true);


class ImageHelper{

    /**
     * To save the image in the Object Storage
     *
     * @param [String] $path
     * @param Images $uploadedImage
     * @param [String] $unique_name
     * @return void
     */
    public static function saveImage($path , $image , $objectStore=0 )
    {
        try{
            $imageStoreName = str_replace(' ','',$image->getClientOriginalName());

            if($objectStore){

                $s3Client = ImageHelper::s3Connect();

                $path = $path . "/" . $imageStoreName;

                $result = ImageHelper::putObject($s3Client , $path , $image->getRealPath());

                return DOFULLENDPOINT.$path;

            }else{
                $newPath = $path.'/';
                
                ImageHelper::createDirectory($newPath);

                $imageStoreName = str_replace(' ','',$image->getClientOriginalName());
    
                $image = Images::make($image->getRealPath());
    
                $image->save(public_path($newPath . $imageStoreName));
    
                return $newPath.$imageStoreName;

            }

        }catch(\Exception $e){
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
    public static function resizeSaveImage($path, $image, $height, $width , $objectStore=0)
    {
        try{
            $imageStoreName = str_replace(' ','',$image->getClientOriginalName());

            if($objectStore){
                
                $path = $path . "/" . $imageStoreName;
                
                $resizedImage = Images::make($image->getRealPath())->resize($height , $width);
                
                $tempImagePath = tempnam(sys_get_temp_dir(), 'resized_image');
                $resizedImage->save($tempImagePath);
                
                $s3Client = ImageHelper::s3Connect();
                $result = ImageHelper::putObject($s3Client , $path , $tempImagePath);
                
                unlink($tempImagePath);
                return DOFULLENDPOINT.$path;

            }else{

            $newPath = $path.'/';
            //create Directory
            ImageHelper::createDirectory($newPath);

            //save mobile list image
            $image = Images::make($image->getRealPath());
            $image->resize($height , $width);
            $image->save(public_path($newPath . $imageStoreName));

            return $newPath.$imageStoreName;
            }

        }catch(\Exception $e){
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
    public static function saveWebpImage($path , $image , $objectStore=0 )
    {
        try{ 

            $imageStoreName  = pathinfo(str_replace(' ', '', $image->getClientOriginalName()), PATHINFO_FILENAME).'.'.'webp';
            if($objectStore){

                $s3Client = ImageHelper::s3Connect();

                $path = $path . "/" . $imageStoreName;

                $result = ImageHelper::putObject($s3Client , $path , $image->getRealPath());

                return DOFULLENDPOINT.$path;

            }else{

                $newPath = $path.'/';

                //create Directory
                ImageHelper::createDirectory($newPath);

                //save mobile list image webp
                $saveImage = Images::make($image);
                $saveImage->encode('webp');
                $saveImage->save(public_path($newPath . $imageStoreName));
                
                return $newPath.$imageStoreName;
            }

        }catch(\Exception $e){
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
    public static function resizeSaveWebpImage($path , $image , $height, $width , $objectStore=0)
    {
        try{ 

            $imageStoreName  =  pathinfo(str_replace(' ', '', $image->getClientOriginalName()), PATHINFO_FILENAME).'.'.'webp';
            if($objectStore){
                    
                $path = $path . "/" . $imageStoreName;
                
                $resizedImage = Images::make($image->getRealPath())->resize($height , $width);
                
                $tempImagePath = tempnam(sys_get_temp_dir(), 'resized_image');
                $resizedImage->save($tempImagePath);
                
                
                $s3Client = ImageHelper::s3Connect();
                $result = ImageHelper::putObject($s3Client , $path , $tempImagePath);
                
                unlink($tempImagePath);
                return DOFULLENDPOINT.$path;

            }else{

            $newPath = $path.'/';
            
            //create Directory
            ImageHelper::createDirectory($newPath);


            //save mobile list image webp
            $imageStore = Images::make($image);
            $imageStore->encode('webp')->resize($height, $width);
            $imageStore->save(public_path($newPath . $imageStoreName));
            
            return $newPath.$imageStoreName;
            }

        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    public static function base64_to_jpeg($base64_string) {
        
        $data = explode( ',', $base64_string );

        return  $image = Images::make(base64_decode($data[1])) ;

    }

    public static function s3Connect()
    {
        return $s3Client = new S3Client([
            'version' => 'latest',
            'region' => REGION,
            'credentials' => [
                'key' => DOKEY,
                'secret' => DOSECRET,
            ],
            'endpoint' => DOENDPOINT,
        ]);
    }

    public static function putObject( $s3Client , $path , $image )
    {
        $result = $s3Client->putObject([
            'Bucket' => BUCKETNAME,
            'Key' => $path,
            'SourceFile' => $image,
            'ACL' => 'public-read', // Optional: Set appropriate ACL permissions
        ]);
    }

    public static function putObjectBody( $s3Client , $path , $image)
    {
        $s3Client->putObject([
            'Bucket' => BUCKETNAME,
            'Key' => $path,
            'Body' => $image,
            'ACL' => 'public-read', // Optional: Set appropriate ACL permissions
        ]);
    }

    public static function putObjectFile($s3Client , $path , $file )
    {
        $storage = $s3Client->putObject([
            'Bucket' => BUCKETNAME,
            'Key' => $path,
            'Body' => file_get_contents($file),
            'ACL' => 'public-read', // Optional: Set appropriate ACL permissions
        ]);
    }

    public static function createDirectory($path)
    {
        if (!is_dir($path)) {
            //Directory does not exist, so lets create it.
            mkdir($path, 0755, true);
        }
    }

    public static function uploadBase64Image($image, $imagePath, $name, $objectStore = 0)
    {
        try {
            if ($objectStore) {
                // Initialize S3 client
                $s3Client = ImageHelper::s3Connect();

                $storageFolder = config('app.storage_folder');

                $path = $storageFolder . $imagePath . $name . time() . ".png";
                // Decode the base64 data into binary format
                $imageData = base64_decode($image);

                $result = ImageHelper::putObjectBody($s3Client , $path , $imageData);

                return $result['ObjectURL']; // Assuming you want to return the URL of the uploaded image
            } else {
                if (!is_dir($imagePath)) {
                    //Directory does not exist, so lets create it.
                    mkdir($imagePath, 0755, true);
                }
                $imageName = $name . time() . '.png';
                $postCoverImageNewName = $imagePath . $imageName;
                $image = Images::make(base64_decode($image));
                $file = $image->save(public_path($postCoverImageNewName)); 
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
            $attachmentName = str_replace(' ', '', $file->getClientOriginalName());

            $fileName = $filePrefix . "-"  . $attachmentName;

            if ($objectStore) {
               // Initialize S3 client
               $s3Client = ImageHelper::s3Connect();

               //created path
                $storePath =  $filePath . $fileName;

                // Upload the file to S3
                $upload = ImageHelper::putObjectFile($s3Client, $storePath,$file);

                $endpoint = DOFULLENDPOINT . $storePath;
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


}
?>