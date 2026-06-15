<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\AuditService;
use App\Services\EscalationService;
use App\Events\{TransactionApproved, TransactionRejected, TransactionCreated};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TransactionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private AuditService $auditService,
        private EscalationService $escalationService
    ) {}

    public function index(Request $request)
    {
        $query = Transaction::with(['creator', 'approver']);

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->has('start_date')) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $transactions = $query->latest()->paginate(15);

        /** @var \Illuminate\Pagination\LengthAwarePaginator $transactions */
        // Attach permission flags per transaction so the frontend can show/hide actions
        $transactions->setCollection(
            $transactions->getCollection()->map(function ($t) use ($request) {
                $user = $request->user();
                // If current user is MD (managing director), grant all actions.
                $isMd = $user && isset($user->role) && (strtolower($user->role->name) === 'md' || strtolower($user->role->name) === 'managing_director');

                if ($isMd) {
                    $t->can_view = true;
                    $t->can_update = true;
                    $t->can_delete = true;
                    $t->can_approve = $t->status === 'pending';
                } else {
                    $t->can_view = $user ? $user->can('view', $t) : false;
                    $t->can_update = $user ? $user->can('update', $t) : false;
                    $t->can_delete = $user ? $user->can('delete', $t) : false;
                    $t->can_approve = $user && $t->status === 'pending' && $user->can('approve', $t);
                }

                return $t;
            })->values()
        );

        $this->auditService->log($request->user(), 'viewed', 'finance', null);

        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:revenue,expense',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'category' => 'nullable|string',
            'client_name' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $transaction = DB::transaction(function () use ($validated, $request) {
            $transaction = Transaction::create([
                ...$validated,
                'created_by' => $request->user()->id,
                'status' => 'pending',
            ]);

            $this->auditService->log(
                $request->user(),
                'created',
                'finance',
                $transaction,
                null,
                $validated
            );

            return $transaction;
        });

        event(new TransactionCreated($transaction));

        return response()->json($transaction, 201);
    }

    public function show(Transaction $transaction)
    {
        // Allow MD (managing director) to view any transaction regardless of
        // fine-grained permissions. Other users must pass the policy check.
        $user = request()->user();
        $isMd = $user && isset($user->role) && (strtolower($user->role->name) === 'md' || strtolower($user->role->name) === 'managing_director');

        if ($isMd) {
            return response()->json($transaction->load(['creator', 'approver']));
        }

        // Enforce policy for non-MD users
        if ($user && $user->can('view', $transaction)) {
            return response()->json($transaction->load(['creator', 'approver']));
        }

        abort(403, 'This action is unauthorized.');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'description' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'transaction_date' => 'sometimes|date',
            'category' => 'nullable|string',
            'client_name' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $transaction->toArray();

        $transaction->update($validated);

        $this->auditService->log(
            $request->user(),
            'updated',
            'finance',
            $transaction,
            $oldValues,
            $validated
        );

        return response()->json($transaction);
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $oldValues = $transaction->toArray();
        $transaction->delete();

        $this->auditService->log(
            $request->user(),
            'deleted',
            'finance',
            $transaction,
            $oldValues,
            null
        );

        return response()->json(['message' => 'Transaction deleted successfully']);
    }

    public function approve(Request $request, Transaction $transaction)
    {
        $this->authorize('approve', $transaction);

        if ($transaction->status !== 'pending') {
            return response()->json(['message' => 'Transaction already processed'], 400);
        }

        $transaction->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $this->auditService->log(
            $request->user(),
            'approved',
            'finance',
            $transaction
        );

        event(new TransactionApproved($transaction->fresh(), $request->user()));

        return response()->json($transaction);
    }

    public function reject(Request $request, Transaction $transaction)
    {
        $this->authorize('approve', $transaction);

        if ($transaction->status !== 'pending') {
            return response()->json(['message' => 'Transaction already processed'], 400);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $transaction->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $this->auditService->log(
            $request->user(),
            'rejected',
            'finance',
            $transaction
        );

        event(new TransactionRejected($transaction->fresh(), $request->user(), $validated['reason'] ?? ''));

        return response()->json($transaction);
    }

    public function statistics(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $approved = Transaction::where('status', 'approved')
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        return response()->json([
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_revenue' => (clone $approved)->revenue()->sum('amount'),
            'total_expenses' => (clone $approved)->expense()->sum('amount'),
            'pending_count' => Transaction::where('status', 'pending')->count(),
            'approved_count' => Transaction::where('status', 'approved')->count(),
            'rejected_count' => Transaction::where('status', 'rejected')->count(),
        ]);
    }

    public function checkEscalation()
    {
        // Check for pending transactions older than 24 hours
        $pendingTransactions = Transaction::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        foreach ($pendingTransactions as $transaction) {
            $this->escalationService->escalate(
                $transaction,
                $transaction->creator,
                'Transaction pending approval for over 24 hours'
            );
        }
    }
}
