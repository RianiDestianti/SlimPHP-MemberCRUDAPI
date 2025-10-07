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

        $result = [
            'status'  => true,
            'message' => 'List of groups',
            'data'    => $data
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $group = Group::where('id', $args['id'])->first();

        $result = [
            'status'  => true,
            'message' => 'Group detail',
            'data'    => $group
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $group = Group::create($data);

        $result = [
            'status'  => true,
            'message' => 'Group created successfully',
            'data'    => $group
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $group = Group::where('id', $args['id'])->first();
        $data = $request->getParsedBody();
        $group->update($data);

        $result = [
            'status'  => true,
            'message' => 'Group updated successfully',
            'data'    => $group
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $group = Group::where('id', $args['id'])->first();
        $group->delete();

        $result = [
            'status'  => true,
            'message' => 'Group deleted successfully',
            'data'    => $group
        ];

        return JsonResponse::withJson($response, $result, 200);
    }
}
