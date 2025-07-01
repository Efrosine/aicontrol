<?php

namespace App\Http\Controllers;

use App\Models\SenderNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SenderNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $senders = SenderNumber::all();
        return view('broadcast.senders.index', compact('senders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('broadcast.senders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'api_key' => 'required|string',
            'number_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        SenderNumber::create($validator->validated());

        return redirect()->route('sender-numbers.index')
            ->with('success', 'Sender number added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(SenderNumber $senderNumber)
    {
        return view('broadcast.senders.show', compact('senderNumber'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SenderNumber $senderNumber)
    {
        return view('broadcast.senders.edit', compact('senderNumber'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SenderNumber $senderNumber)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'api_key' => 'required|string',
            'number_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $senderNumber->update($validator->validated());

        return redirect()->route('sender-numbers.index')
            ->with('success', 'Sender number updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SenderNumber $senderNumber)
    {
        $senderNumber->delete();

        return redirect()->route('sender-numbers.index')
            ->with('success', 'Sender number deleted successfully');
    }
}
