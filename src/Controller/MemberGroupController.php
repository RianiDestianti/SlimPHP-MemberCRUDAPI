<?php

namespace App\Controller;

use App\Model\MemberGroup;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class MemberGroupController
{
    public function index(Request $request, Response $response): Response
    {
        $data = MemberGroup::with(['member', 'group'])->get();

        $result = [
            'status'  => true,
            'message' => 'List of member groups',
            'data'    => $data
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function store(Request $request, Response $response): Response
    {
        $post = $request->getParsedBody();

        $pivot = MemberGroup::create([
            'member_id' => $post['member_id'],
            'group_id'  => $post['group_id'],
            'role'      => $post['role'] ?? null,
            'joined_at' => $post['joined_at'] ?? null
        ]);

        $result = [
            'status'  => (bool) $pivot,
            'message' => $pivot ? 'Pivot created successfully' : 'Failed to create pivot',
            'data'    => $pivot
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function update(Request $request, Response $response): Response
    {
        $params   = $request->getQueryParams();
        $memberId = $params['member_id'] ?? null;
        $groupId  = $params['group_id'] ?? null;
        $post     = $request->getParsedBody();

        $pivot = MemberGroup::where('member_id', $memberId)
            ->where('group_id', $groupId)
            ->first();

        $pivot->role      = $post['role'] ?? $pivot->role;
        $pivot->joined_at = $post['joined_at'] ?? $pivot->joined_at;
        $pivot->save();

        $result = [
            'status'  => true,
            'message' => 'Pivot updated successfully',
            'data'    => $pivot
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function delete(Request $request, Response $response): Response
    {
        $params   = $request->getQueryParams();
        $memberId = $params['member_id'] ?? null;
        $groupId  = $params['group_id'] ?? null;

        $pivot = MemberGroup::where('member_id', $memberId)
            ->where('group_id', $groupId)
            ->first();

        $result = [
            'status'  => true,
            'message' => 'Pivot deleted successfully'
        ];

        return JsonResponse::withJson($response, $result, 200);
    }
}
