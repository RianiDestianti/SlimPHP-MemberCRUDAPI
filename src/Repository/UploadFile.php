<?php

declare(strict_types=1);

namespace App\Repository;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

final class UploadFile {

    public function validateFile($source_file, $target_folder, $randomize_filename = false)
    {
        $result['status']     = false;
        $result['file_path']  = "";
        $result['file_name']  = "";
        $result['extension']  = "";

        if (isset($_FILES[$source_file]) && $_FILES[$source_file]['size'] != 0) {
            $result['status']       = true;
            $result['extension']    = strtolower(pathinfo($_FILES[$source_file]["name"], PATHINFO_EXTENSION));

            $randomize_data       = "";

            if ($randomize_filename) {
                $randomize_data = "_" . bin2hex(random_bytes(32));
            }

            $file_name = str_replace(".", '', $_FILES[$source_file]['name']);
            $file_name = preg_replace('/[^A-Za-z0-9\-]/', '', $_FILES[$source_file]['name']) . $randomize_data . '.' . $result['extension'];
            $result['file_path']   = $target_folder . '/' . str_replace(" ", "-", $file_name);
            $result['file_name']   = $file_name;
        }

        return $result;
    }

    public function moveUploadedS3($key, $post_name, $target_path, $public=false, $is_path=false, $path='') {

        $ext = strtolower(pathinfo($_FILES[$post_name]["name"][$key], PATHINFO_EXTENSION));
        
        $img = ['jpg', 'jpeg', 'png'];
        // $img = [];

        if(in_array($ext, $img)){

            $filepath = '';
            $filesize = 0;

            if ($is_path) {
                $filepath = $path;
                $filesize = filesize($filepath);
            } else {
                $filepath = $_FILES[$post_name]['tmp_name'][$key];
                $filesize = $_FILES[$post_name]['size'][$key];
            }

            $folder = "images/";

            // tentukan di mana image akan ditempatkan setelah diupload
            $original = $this->generateRandomString(26) . '.'.$ext;
            $filesave = $folder . $original;
            move_uploaded_file($_FILES[$post_name]['tmp_name'][$key], $filesave);

            // return $extension = strtolower(pathinfo($upl, PATHINFO_EXTENSION));

            // menentukan nama image setelah dibuat
            $fileName = $this->generateRandomString(26) . '.'.$ext;
            $resize_image = $folder . $fileName;

            // mendapatkan ukuran width dan height dari image
            list( $width, $height ) = getimagesize($filesave);

            // menentukan width yang baru
            $newwidth = (int) ($width > 933 ? 933 : $width);

            // menentukan height yang baru
            $newheight = (int) ($width > 933 ? (int) ceil((($height / $width)) * 933) : $height);

            $firstBytes = bin2hex(file_get_contents($filesave,false,null,0,2));

            // fungsi untuk membuat image yang baru
            $thumb = imagecreatetruecolor( $newwidth,  $newheight);
            $extension =  isset(pathinfo($filesave)['extension']) ? pathinfo($filesave)['extension'] : 'jpg';

            if($firstBytes == 8950){
                $extension = 'png';
            }

            $status = true;

            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);

            if($extension == 'jpg' || $extension == "jpeg" ){
                $source = imagecreatefromjpeg($filesave);
            } else if($extension == "png"){
                // $source = imagecreatefromjpeg($filesave);
                $source = imagecreatefrompng($filesave);
            } else if($extension == 'gif') {
                $source = imagecreatefromgif($filesave);
            } else {
                $status = false;
                $source = null;
                unlink($filesave);
            }
            
            if($status){

                // men-resize image yang baru
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                // menyimpan image yang baru
                
                if($extension == 'png'){
                    imagepng($thumb, $resize_image);
                }else {
                    imagejpeg($thumb, $resize_image);
                }

                imagedestroy($thumb);
                imagedestroy($source);

                if ($filesize != 0) {
                    
                    $S3 = S3Client::factory([
                        'version' => $_ENV['CLOUD_VERSION'],
                        'region' => $_ENV['CLOUD_REGION'],
                        'endpoint' => $_ENV['CLOUD_ENDPOINT'],
                        'credentials' => array(
                            'key' => $_ENV['CLOUD_KEY'],
                            'secret' => $_ENV['CLOUD_SECRET']
                        )
                    ]);
                    try {
                        $res = $S3->putObject([
                            'Bucket' => $_ENV['CLOUD_BUCKET'],
                            'Key' => "example/".$fileName,
                            'Body' => fopen($resize_image, 'rb'),
                            'ACL' => 'public-read'
                        ]);
                    } catch (S3Exception $e) {
                        echo $e->getMessage() . "\n";
                    }
                }

                unlink($resize_image);
                unlink($filesave);

                return 'https://sobat.sgp1.digitaloceanspaces.com/example/'.$fileName;
            } else {
                return null;
            }
        } else {
            return $this->uploadFile($_FILES[$post_name]['tmp_name'][$key], $target_path);
        }
    }

    public function generateRandomString($length = 64) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function moveUploadedOneS3($post_name, $target_path, $public=false, $is_path=false, $path='') {
        $result = array('status' => false, 'message' => '');

        $filepath = '';
        $filesize = 0;

        if ($is_path) {
            $filepath = $path;
            $filesize = filesize($filepath);
        } else {
            $filepath = $_FILES[$post_name]['tmp_name'];
            $filesize = $_FILES[$post_name]['size'];
        }

        if ($filesize != 0) {
            
            $client = S3Client::factory(array(
                'region' => $_ENV['CLOUD_REGION'],
                'version' => $_ENV['CLOUD_VERSION'],
                'endpoint' => $_ENV['CLOUD_ENDPOINT'],
                'credentials' => [
                    'key'       => $_ENV['CLOUD_KEY'],
                    'secret'    => $_ENV['CLOUD_SECRET']
                ],
            ));

    
            try {
                $source_path = $filepath;
                $content_type = mime_content_type($filepath);
                $ACL = 'public-read';
                $upload = $client->putObject(array(
                    'Bucket' => $_ENV['CLOUD_BUCKET'],
                    'Key' => 'example/'.str_replace(' ' ,'-',$target_path),
                    'SourceFile' => $source_path,
                    'ContentType' => $content_type,
                    'StorageClass' => 'STANDARD',
                    'ACL' => $ACL
                ));

                if($upload){
                    $result = $client->getObjectUrl($_ENV['CLOUD_BUCKET'], 'example/'.str_replace(' ' ,'-',$target_path));
                }else{
                    $result = 'gagal';
                }
                
            } catch (S3Exception $e) {
                
            }

        }

        return $result;
    } 

    public function uploadFile($filepath, $target_path)
    {
        $client = S3Client::factory(array(
            'region' => $_ENV['CLOUD_REGION'],
            'version' => $_ENV['CLOUD_VERSION'],
            'endpoint' => $_ENV['CLOUD_ENDPOINT'],
            'credentials' => [
                'key'       => $_ENV['CLOUD_KEY'],
                'secret'    => $_ENV['CLOUD_SECRET']
            ],
        ));


        try {
            $source_path = $filepath;
            $content_type = mime_content_type($filepath);
            $ACL = 'public-read';
            $upload = $client->putObject(array(
                'Bucket' => $_ENV['CLOUD_BUCKET'],
                'Key' => 'example/'.str_replace(' ' ,'-',$target_path),
                'SourceFile' => $source_path,
                'ContentType' => $content_type,
                'StorageClass' => 'STANDARD',
                'ACL' => $ACL
            ));

            if($upload){
                $result = $client->getObjectUrl($_ENV['CLOUD_BUCKET'], 'example/'.str_replace(' ' ,'-',$target_path));
            }else{
                $result = 'gagal';
            }
            
        } catch (S3Exception $e) {
            
        }

        return $result;

    }
}
