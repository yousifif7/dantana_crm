<?php

namespace App\Http\Controllers;

use App\Models\Escalation;
use App\Http\Resources\EscalationResource;
use App\Services\EscalationService;
use Illuminate\Http\Request;

class EscalationController extends Controller
{
    public function __construct(private EscalationService $escalationService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = in_array(strtolower($user->role->name ?? ''), ['md', 'managing_director', 'chairman']);

        $query = Escalation::with(['fromUser', 'toUser', 'escalatable']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if (!$isAdmin) {
            $query->where(function ($q) use ($request) {
                $q->where('from_user_id', $request->user()->id)
                  ->orWhere('to_user_id', $request->user()->id);
            });
        }

        return EscalationResource::collection(
            $query->latest('escalated_at')->paginate(15)
        );
    }

    public function myPending(Request $request)
    {
        $escalations = Escalation::with(['fromUser', 'escalatable'])
            ->where('to_user_id', $request->user()->id)
            ->where('status', 'pending')
            ->latest('escalated_at')
            ->get();

        return EscalationResource::collection($escalations);
    }

    public function resolve(Request $request, Escalation $escalation)
    {
        if (!$this->escalationService->resolve($escalation, $request->user())) {
            return response()->json([
                'message' => 'You are not authorized to resolve this escalation'
            ], 403);
        }

        return response()->json([
            'message' => 'Escalation resolved successfully',
            'escalation' => new EscalationResource($escalation->fresh(['fromUser', 'toUser']))
        ]);
    }
}

