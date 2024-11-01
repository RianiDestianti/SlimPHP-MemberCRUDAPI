<?php

namespace App\Controller;

use App\Model\User;
use Pimple\Psr11\Container;
use App\Helper\JsonResponse;
use App\Repository\UploadFile;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HomeController
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container    = $container;
        $this->upload       = new UploadFile();
    }

    public function index(Request $request, Response $response): Response
    {
        $result['status']   = true;
        $result['data']     = [];
        
        return JsonResponse::withJson($response, $result, 200);
    }

    public function uploadFile(Request $request, Response $response): Response
    {
        $upload         = '';

        if (isset($_FILES['image']) && $_FILES['image']['size'] != 0) {
            $targetFolder   = "";
            $validateFile   = $this->upload->validateFile('image', $targetFolder, true);

            if ($validateFile['status']) {
                $upload = $this->upload->moveUploadedOneS3('image', $this->auth->generateRandom(25).'.'.$validateFile['extension'], true);
            }
        }

        $result['status']    = true;
        $result['message']   = 'Successfully';
        $result['data']      = $upload;

        return JsonResponse::withJson($response, $result, 200);
    }
}