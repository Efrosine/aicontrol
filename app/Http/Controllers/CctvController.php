<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CctvController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cctvs = Cctv::latest()->paginate(10);
        return view('cctvs.index', compact('cctvs'));
    }

    /**
     * Display a list of CCTV cameras for non-admin users.
     */
    public function userView()
    {
        $cctvs = Cctv::latest()->get();
        return view('cctvs.user-view', compact('cctvs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cctvs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'origin_url' => 'required|url|max:255',
            'stream_url' => 'required|url|max:255',
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('cctvs.create')
                ->withErrors($validator)
                ->withInput();
        }

        Cctv::create($request->all());

        return redirect()
            ->route('cctvs.index')
            ->with('success', 'CCTV camera added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cctv $cctv)
    {
        return view('cctvs.show', compact('cctv'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cctv $cctv)
    {
        return view('cctvs.edit', compact('cctv'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cctv $cctv)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'origin_url' => 'required|url|max:255',
            'stream_url' => 'required|url|max:255',
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('cctvs.edit', $cctv)
                ->withErrors($validator)
                ->withInput();
        }

        $cctv->update($request->all());

        return redirect()
            ->route('cctvs.index')
            ->with('success', 'CCTV camera updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cctv $cctv)
    {
        $cctv->delete();

        return redirect()
            ->route('cctvs.index')
            ->with('success', 'CCTV camera deleted successfully.');
    }
}
