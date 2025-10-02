<?php

namespace App\Controller;

use App\Model\Member;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class MemberController
{
    public function index(Request $request, Response $response): Response
    {
        $members = Member::all();

        $result = [
            'status' => true,
            'message' => 'Successfully',
            'data' => $members
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function store(Request $request, Response $response): Response
    {
        $post = $request->getParsedBody();

        $member = Member::create($post);

        $result = [
            'status' => (bool) $member,
            'message' => $member ? 'Member created successfully' : 'Failed to create member',
            'data' => $member
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $post = $request->getParsedBody();

        $member = Member::find($id);
        if (!$member) {
            return JsonResponse::withJson($response, [
                'status' => false,
                'message' => 'Member not found'
            ], 404);
        }

        $member->update($post);

        $result = [
            'status' => true,
            'message' => 'Member updated successfully',
            'data' => $member
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $member = Member::find($id);

        if (!$member) {
            return JsonResponse::withJson($response, [
                'status' => false,
                'message' => 'Member not found'
            ], 404);
        }

        $member->delete();

        $result = [
            'status' => true,
            'message' => 'Member deleted successfully'
        ];

        return JsonResponse::withJson($response, $result, 200);
    }
}
