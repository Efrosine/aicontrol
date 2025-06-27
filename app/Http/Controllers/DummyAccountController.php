<?php

namespace App\Http\Controllers;

use App\Models\DummyAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DummyAccountController extends Controller
{
    /**
     * Display a listing of dummy accounts.
     */
    public function index()
    {
        $dummyAccounts = DummyAccount::all();
        return view('dummy-accounts.index', compact('dummyAccounts'));
    }

    /**
     * Show the form for creating a new dummy account.
     */
    public function create()
    {
        return view('dummy-accounts.create');
    }

    /**
     * Store a newly created dummy account in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:dummy_accounts',
            'password' => 'required|string|min:6',
            'platform' => 'required|in:ig,x,twitter',
        ]);

        DummyAccount::create([
            'username' => $validated['username'],
            'password' => $validated['password'], // Not hashing as these are dummy accounts
            'platform' => $validated['platform'],
        ]);

        return redirect()->route('dummy-accounts.index')
            ->with('success', 'Dummy account created successfully.');
    }

    /**
     * Show the form for editing the specified dummy account.
     */
    public function edit(DummyAccount $dummyAccount)
    {
        return view('dummy-accounts.edit', compact('dummyAccount'));
    }

    /**
     * Update the specified dummy account in storage.
     */
    public function update(Request $request, DummyAccount $dummyAccount)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('dummy_accounts')->ignore($dummyAccount->id),
            ],
            'password' => 'sometimes|string|min:6',
            'platform' => 'required|in:ig,x,twitter',
        ]);

        $dummyAccount->username = $validated['username'];

        if ($request->filled('password')) {
            $dummyAccount->password = $validated['password'];
        }

        $dummyAccount->platform = $validated['platform'];
        $dummyAccount->save();

        return redirect()->route('dummy-accounts.index')
            ->with('success', 'Dummy account updated successfully.');
    }

    /**
     * Remove the specified dummy account from storage.
     */
    public function destroy(DummyAccount $dummyAccount)
    {
        $dummyAccount->delete();

        return redirect()->route('dummy-accounts.index')
            ->with('success', 'Dummy account deleted successfully.');
    }
}