<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->parties();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        $parties = $query->orderBy('name')
            ->paginate($request->input('per_page', 10));

        return response()->json($parties);
    }

    public function all(Request $request)
    {
        $parties = $request->user()->parties()
            ->orderBy('name')
            ->get(['id', 'name', 'gstin', 'address', 'city', 'state', 'pincode']);

        return response()->json($parties);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gstin' => ['nullable', 'string', 'size:15', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'size:6', 'regex:/^\d{6}$/'],
            'contact_number' => ['nullable', 'string', 'size:10', 'regex:/^\d{10}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'party_type' => ['nullable', 'in:customer,supplier'],
        ]);

        // Extract state code from GSTIN if provided
        if (!empty($validated['gstin'])) {
            $validated['state_code'] = substr($validated['gstin'], 0, 2);
        }

        $party = $request->user()->parties()->create($validated);

        return response()->json($party, 201);
    }

    public function show(Request $request, Party $party)
    {
        $this->authorize('view', $party);

        return response()->json($party);
    }

    public function update(Request $request, Party $party)
    {
        $this->authorize('update', $party);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gstin' => ['nullable', 'string', 'size:15', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'size:6', 'regex:/^\d{6}$/'],
            'contact_number' => ['nullable', 'string', 'size:10', 'regex:/^\d{10}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'party_type' => ['nullable', 'in:customer,supplier'],
        ]);

        // Extract state code from GSTIN if provided
        if (!empty($validated['gstin'])) {
            $validated['state_code'] = substr($validated['gstin'], 0, 2);
        }

        $party->update($validated);

        return response()->json($party);
    }

    public function destroy(Request $request, Party $party)
    {
        $this->authorize('delete', $party);

        // Check if party has any bills
        if ($party->bills()->exists()) {
            return response()->json([
                'message' => 'Cannot delete party with existing bills.',
            ], 422);
        }

        $party->delete();

        return response()->json([
            'message' => 'Party deleted successfully.',
        ]);
    }
}
