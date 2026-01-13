<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->transactions()->latest();
        
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
        
        // Get transactions with pagination
        $transactions = $query->paginate(20);
        
        // Get all payment IDs from current page transactions
        $paymentIds = $transactions->getCollection()
            ->where('reference_type', \App\Models\Payment::class)
            ->pluck('reference_id')
            ->filter()
            ->unique()
            ->values();
        
        // Eager load all payments at once to avoid N+1 queries
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
        
        // Get statistics from all transactions (not just current page)
        $statsQuery = $user->transactions();
        if ($request->type) {
            $statsQuery->where('type', $request->type);
        }
        if ($request->date_from) {
            $statsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $statsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        $totalTransactions = $statsQuery->count();
        $totalCredits = (clone $statsQuery)->where('type', 'credit')->sum('amount');
        $totalDebits = (clone $statsQuery)->where('type', 'debit')->sum('amount');
        
        return view('user.transactions.index', compact('transactions', 'totalTransactions', 'totalCredits', 'totalDebits'));
    }
}
