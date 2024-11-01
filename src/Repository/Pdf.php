<?php

namespace App\Repository;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Pdf {

    public function generate($filename, $items)
    {
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->WriteHTML('
          <div>PDF</div>
        ');
        $mpdf->Output('file/filename.pdf', 'F'); 

        $S3 = S3Client::factory([
            'region' => $_ENV['CLOUD_REGION'],
            'version' => $_ENV['CLOUD_VERSION'],
            'endpoint' => $_ENV['CLOUD_ENDPOINT'],
            'credentials' => [
                'key'       => $_ENV['CLOUD_KEY'],
                'secret'    => $_ENV['CLOUD_SECRET']
            ],
        ]);
        try {
            $res = $S3->putObject([
                'Bucket' => $_ENV['CLOUD_BUCKET'],
                'Key' => 'example/'.str_replace(' ' ,'-',$target_path),
                'Body' => fopen('file/filename.pdf', 'rb'),
                'ACL' => 'public-read'
            ]);
        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }
}