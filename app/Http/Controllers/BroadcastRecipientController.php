<?php

namespace App\Http\Controllers;

use App\Models\BroadcastRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BroadcastRecipientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recipients = BroadcastRecipient::all();
        return view('broadcast.recipients.index', compact('recipients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('broadcast.recipients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_no' => 'required|string',
            'receive_cctv' => 'boolean',
            'receive_social' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle checkboxes
        $data = $validator->validated();
        $data['receive_cctv'] = $request->has('receive_cctv');
        $data['receive_social'] = $request->has('receive_social');

        BroadcastRecipient::create($data);

        return redirect()->route('broadcast-recipients.index')
            ->with('success', 'Recipient added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(BroadcastRecipient $broadcastRecipient)
    {
        return view('broadcast.recipients.show', compact('broadcastRecipient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BroadcastRecipient $broadcastRecipient)
    {
        return view('broadcast.recipients.edit', compact('broadcastRecipient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BroadcastRecipient $broadcastRecipient)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_no' => 'required|string',
            'receive_cctv' => 'boolean',
            'receive_social' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle checkboxes
        $data = $validator->validated();
        $data['receive_cctv'] = $request->has('receive_cctv');
        $data['receive_social'] = $request->has('receive_social');

        $broadcastRecipient->update($data);

        return redirect()->route('broadcast-recipients.index')
            ->with('success', 'Recipient updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BroadcastRecipient $broadcastRecipient)
    {
        $broadcastRecipient->delete();

        return redirect()->route('broadcast-recipients.index')
            ->with('success', 'Recipient deleted successfully');
    }
}
