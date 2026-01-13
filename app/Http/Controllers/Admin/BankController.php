<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::ordered()->get();
        return view('admin.banks.index', compact('banks'));
    }

    public function show($id)
    {
        $bank = Bank::findOrFail($id);
        return response()->json($bank);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'qr_code' => 'nullable|image|max:2048', // 2MB
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') || $request->input('is_active') === 'on',
        ];

        if ($request->hasFile('qr_code')) {
            $data['qr_code'] = $request->file('qr_code')->store('banks/qr-codes', 'public');
        }

        Bank::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Bank added successfully',
        ]);
    }

    public function update(Request $request, $id)
    {
        $bank = Bank::findOrFail($id);

        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'qr_code' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('qr_code')) {
            // Delete old QR code
            if ($bank->qr_code) {
                Storage::disk('public')->delete($bank->qr_code);
            }
            $data['qr_code'] = $request->file('qr_code')->store('banks/qr-codes', 'public');
        }

        $bank->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Bank updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $bank = Bank::findOrFail($id);
        
        // Delete QR code if exists
        if ($bank->qr_code) {
            Storage::disk('public')->delete($bank->qr_code);
        }
        
        $bank->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank deleted successfully',
        ]);
    }

    public function toggle($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->update(['is_active' => !$bank->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Bank status updated',
        ]);
    }
}
