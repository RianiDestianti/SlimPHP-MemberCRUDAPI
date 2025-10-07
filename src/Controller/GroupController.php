<?php

namespace App\Controller;

use App\Model\Group;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GroupController
{
    public function index(Request $request, Response $response): Response
    {
        $data = Group::all();
        return JsonResponse::withJson($response, [
            'status'  => true,
            'message' => 'List of groups',
            'data'    => $data
        ]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $group = Group::find($args['id']);
  
        return JsonResponse::withJson($response, [
            'status'  => true,
            'message' => 'Group detail',
            'data'    => $group
        ]);
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $group = Group::create($data);

        return JsonResponse::withJson($response, [
            'status'  => true,
            'message' => 'Group created successfully',
            'data'    => $group
        ]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {

        $data = $request->getParsedBody();
        $group->update($data);

        return JsonResponse::withJson($response, [
            'status'  => true,
            'message' => 'Group updated successfully',
            'data'    => $group
        ]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $group = Group::find($args['id']);
        $group->delete();

        return JsonResponse::withJson($response, [
            'status'  => true,
            'message' => 'Group deleted successfully'
        ]);
    }
}
