<?php

namespace App\Http\Controllers;

use App\Models\SuspectedAccount;
use Illuminate\Http\Request;

class SuspectedAccountController extends Controller
{
    /**
     * Display a listing of the suspected accounts.
     */
    public function index()
    {
        $suspectedAccounts = SuspectedAccount::latest()->paginate(10);
        return view('admin.suspected_accounts.index', compact('suspectedAccounts'));
    }

    /**
     * Show the form for creating a new suspected account.
     */
    public function create()
    {
        return view('admin.suspected_accounts.create');
    }

    /**
     * Store a newly created suspected account in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|string|unique:suspected_accounts',
            'platform' => 'required|in:ig,x,twitter',
        ]);

        SuspectedAccount::create($validated);

        return redirect()->route('suspected-accounts.index')
            ->with('success', 'Suspected account created successfully.');
    }

    /**
     * Show the form for editing the specified suspected account.
     */
    public function edit(SuspectedAccount $suspectedAccount)
    {
        return view('admin.suspected_accounts.edit', compact('suspectedAccount'));
    }

    /**
     * Update the specified suspected account in storage.
     */
    public function update(Request $request, SuspectedAccount $suspectedAccount)
    {
        $validated = $request->validate([
            'data' => 'required|string|unique:suspected_accounts,data,' . $suspectedAccount->id,
            'platform' => 'required|in:ig,x,twitter',
        ]);

        $suspectedAccount->update($validated);

        return redirect()->route('suspected-accounts.index')
            ->with('success', 'Suspected account updated successfully.');
    }

    /**
     * Remove the specified suspected account from storage.
     */
    public function destroy(SuspectedAccount $suspectedAccount)
    {
        $suspectedAccount->delete();

        return redirect()->route('suspected-accounts.index')
            ->with('success', 'Suspected account deleted successfully.');
    }
}
