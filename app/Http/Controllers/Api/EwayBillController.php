<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\EwayBill;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EwayBillController extends Controller
{
    public function index(Request $request)
    {
        $query = EwayBill::whereHas('bill', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->with(['bill.party']);

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->active();
            } elseif ($status === 'expired') {
                $query->expired();
            }
        }

        if ($date = $request->input('date')) {
            $query->whereDate('generated_at', $date);
        }

        $ewayBills = $query->orderBy('generated_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return response()->json($ewayBills);
    }

    public function store(Request $request, Bill $bill)
    {
        $this->authorize('update', $bill);

        // Check if e-way bill already exists
        if ($bill->ewayBill) {
            return response()->json([
                'message' => 'E-Way bill already exists for this bill.',
            ], 422);
        }

        // Check if bill amount is above threshold (₹50,000)
        if ($bill->total_amount < 50000) {
            return response()->json([
                'message' => 'E-Way bill is required only for goods worth more than ₹50,000.',
            ], 422);
        }

        $validated = $request->validate([
            'transport_mode' => ['required', 'in:Road,Rail,Air,Ship'],
            'vehicle_number' => ['required', 'string', 'max:20', 'regex:/^[A-Z]{2}\d{2}[A-Z]{1,2}\d{4}$/i'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'transporter_id' => ['nullable', 'string', 'size:15', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'transporter_name' => ['nullable', 'string', 'max:255'],
            'distance' => ['required', 'integer', 'min:1'],
            'from_address' => ['required', 'string', 'max:500'],
            'from_pincode' => ['nullable', 'string', 'size:6', 'regex:/^\d{6}$/'],
            'to_address' => ['required', 'string', 'max:500'],
            'to_pincode' => ['nullable', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ]);

        $validated['vehicle_number'] = strtoupper($validated['vehicle_number']);

        $ewayBill = $bill->ewayBill()->create($validated);

        return response()->json($ewayBill->load('bill.party'), 201);
    }

    public function show(Request $request, EwayBill $ewayBill)
    {
        // Verify user owns the bill
        if ($ewayBill->bill->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json($ewayBill->load('bill.party'));
    }

    public function pdf(Request $request, EwayBill $ewayBill)
    {
        // Verify user owns the bill
        if ($ewayBill->bill->user_id !== $request->user()->id) {
            abort(403);
        }

        $ewayBill->load(['bill.party', 'bill.items.product', 'bill.user']);

        $pdf = Pdf::loadView('pdf.eway-bill', [
            'ewayBill' => $ewayBill,
            'bill' => $ewayBill->bill,
            'user' => $ewayBill->bill->user,
        ]);

        // Sanitize filename - party name with invoice number (same pattern as bill)
        $partyName = preg_replace('/[^A-Za-z0-9 ]/', '', $ewayBill->bill->party->name ?? 'Unknown');
        $partyName = str_replace(' ', '-', trim($partyName));
        $billNumber = str_replace(['/', '\\'], '-', $ewayBill->bill->bill_number);
        $filename = "{$partyName}-{$billNumber}-EWayBill.pdf";

        $pdfContent = $pdf->output();

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
            'Content-Length' => strlen($pdfContent),
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'Pragma' => 'public',
        ]);
    }
}
