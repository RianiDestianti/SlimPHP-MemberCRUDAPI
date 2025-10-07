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
            'role'      => $post['role'] ?? 'member',
            'joined_at' => $post['joined_at'] ?? date('Y-m-d H:i:s'),
        ]);

        $result = [
            'status'  => true,
            'message' => 'Member group berhasil disimpan',
            'data'    => $pivot
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $memberId = $args['member_id'] ?? null;
        $groupId  = $args['group_id'] ?? null;
        $post     = $request->getParsedBody();

        $pivot = MemberGroup::where('member_id', $memberId)
                            ->where('group_id', $groupId)
                            ->first();
        $pivot->update([
            'role'      => $post['role'] ?? $pivot->role,
            'joined_at' => $post['joined_at'] ?? $pivot->joined_at,
        ]);

        $result = [
            'status'  => true,
            'message' => 'Member group berhasil diperbarui',
            'data'    => $pivot
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $memberId = $args['member_id'] ?? null;
        $groupId  = $args['group_id'] ?? null;

        $deleted = MemberGroup::where('member_id', $memberId)
                              ->where('group_id', $groupId)
                              ->delete();
        $result = [
            'status'  => true,
            'message' => 'Member group berhasil dihapus',
            'data'    => $deleted
        ];

        return JsonResponse::withJson($response, $result, 200);
    }
}