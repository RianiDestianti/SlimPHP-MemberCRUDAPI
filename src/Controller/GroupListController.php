<?php

namespace App\Controller;

use App\Model\Group;
use App\Model\Member;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GroupListController
{
    public function index(Request $request, Response $response): Response
    {
        $groups = Group::with('members:id,name,email')->get();

        $result = [
            'status'  => true,
            'message' => 'List of groups with members',
            'data'    => $groups
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $group = Group::with('members:id,name,email')->where('id', $args['id'])->first();

        $result = [
            'status'  => true,
            'message' => 'Group details',
            'data'    => $group
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function addMember(Request $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();

        $group  = Group::where('id', $data['group_id'])->first();
        $member = Member::where('id', $data['member_id'])->first();

        $group->members()->attach($member->id, [
            'role'      => $data['role'] ?? 'member',
            'joined_at' => now()
        ]);

        $member = $group->members()->where('members.id', $member->id)->first();

        $result = [
            'status'  => true,
            'message' => 'Member added to group',
            'data'    => $member
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function updateMember(Request $request, Response $response, array $args): Response
    {
        $group    = Group::where('id', $args['group_id'])->first();
        $memberId = $args['member_id'];
        $data     = (array)$request->getParsedBody();

        $group->members()->updateExistingPivot($memberId, [
            'role' => $data['role'] ?? 'member'
        ]);

        $member = $group->members()->where('members.id', $memberId)->first();

        $result = [
            'status'  => true,
            'message' => 'Member updated in group',
            'data'    => $member
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function deleteMember(Request $request, Response $response, array $args): Response
    {
        $group    = Group::where('id', $args['group_id'])->first();
        $memberId = $args['member_id'];

        $member = $group->members()->where('members.id', $memberId)->first();

        $group->members()->detach($memberId);

        $result = [
            'status'  => true,
            'message' => 'Member removed from group',
            'data'    => $member
        ];

        return JsonResponse::withJson($response, $result, 200);
    }
}
