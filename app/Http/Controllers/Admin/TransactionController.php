<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'reference'])->latest();

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by description
        if ($request->search) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        // Get statistics before pagination
        $totalTransactions = (clone $query)->count();
        $totalCredits = (clone $query)->where('type', 'credit')->sum('amount');
        $totalDebits = (clone $query)->where('type', 'debit')->sum('amount');
        $netAmount = $totalCredits - $totalDebits;

        $transactions = $query->paginate(50);

        // Get users for filter dropdown
        $users = User::where('is_admin', false)
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        // Get payment IDs for eager loading
        $paymentIds = $transactions->getCollection()
            ->where('reference_type', \App\Models\Payment::class)
            ->pluck('reference_id')
            ->filter()
            ->unique();

        $payments = [];
        if ($paymentIds->isNotEmpty()) {
            $payments = \App\Models\Payment::whereIn('id', $paymentIds)
                ->get()
                ->keyBy('id');
        }

        // Attach payments to transactions
        $transactions->getCollection()->each(function($transaction) use ($payments) {
            if ($transaction->reference_type === \App\Models\Payment::class && 
                $transaction->reference_id && 
                isset($payments[$transaction->reference_id])) {
                $transaction->payment = $payments[$transaction->reference_id];
            }
        });

        return view('admin.transactions.index', compact('transactions', 'users', 'totalTransactions', 'totalCredits', 'totalDebits', 'netAmount'));
    }
}
