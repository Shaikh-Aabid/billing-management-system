<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->bills()->with(['party', 'ewayBill']);

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('bill_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('bill_date', '<=', $dateTo);
        }

        $bills = $query->orderBy('bill_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($request->input('per_page', 10));

        return response()->json($bills);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'party_id' => ['required', 'exists:parties,id'],
            'bill_date' => ['required', 'date'],
            'bill_type' => ['nullable', 'in:invoice,quotation,delivery_challan'],
            'is_inter_state' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.hsn_code' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.gst_rate' => ['required', 'numeric', 'in:0,5,12,18,28'],
        ]);

        // Verify party belongs to user
        $party = $request->user()->parties()->findOrFail($validated['party_id']);

        DB::beginTransaction();
        try {
            $bill = $request->user()->bills()->create([
                'party_id' => $validated['party_id'],
                'bill_date' => $validated['bill_date'],
                'bill_type' => $validated['bill_type'] ?? 'invoice',
                'is_inter_state' => $validated['is_inter_state'] ?? false,
                'notes' => $validated['notes'] ?? null,
            ]);

            $subtotal = 0;
            $cgst = 0;
            $sgst = 0;
            $igst = 0;

            foreach ($validated['items'] as $itemData) {
                $product = $request->user()->products()->findOrFail($itemData['product_id']);

                $itemSubtotal = $itemData['quantity'] * $itemData['price'];
                $itemGst = $itemSubtotal * ($itemData['gst_rate'] / 100);

                $itemCgst = 0;
                $itemSgst = 0;
                $itemIgst = 0;

                if ($bill->is_inter_state) {
                    $itemIgst = $itemGst;
                    $igst += $itemGst;
                } else {
                    $itemCgst = $itemGst / 2;
                    $itemSgst = $itemGst / 2;
                    $cgst += $itemGst / 2;
                    $sgst += $itemGst / 2;
                }

                $subtotal += $itemSubtotal;

                $bill->items()->create([
                    'product_id' => $itemData['product_id'],
                    'hsn_code' => $itemData['hsn_code'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $product->unit,
                    'price' => $itemData['price'],
                    'gst_rate' => $itemData['gst_rate'],
                    'cgst' => $itemCgst,
                    'sgst' => $itemSgst,
                    'igst' => $itemIgst,
                    'amount' => $itemSubtotal + $itemGst,
                ]);
            }

            $gstAmount = $cgst + $sgst + $igst;

            $bill->update([
                'subtotal' => $subtotal,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'gst_amount' => $gstAmount,
                'total_amount' => $subtotal + $gstAmount,
            ]);

            DB::commit();

            return response()->json($bill->load(['party', 'items.product']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Request $request, Bill $bill)
    {
        $this->authorize('view', $bill);

        return response()->json($bill->load(['party', 'items.product', 'ewayBill']));
    }

    public function update(Request $request, Bill $bill)
    {
        $this->authorize('update', $bill);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'in:draft,sent,paid,cancelled'],
        ]);

        $bill->update($validated);

        return response()->json($bill->load(['party', 'items.product']));
    }

    public function destroy(Request $request, Bill $bill)
    {
        $this->authorize('delete', $bill);

        // Check if bill has an e-way bill
        if ($bill->ewayBill) {
            return response()->json([
                'message' => 'Cannot delete bill with associated e-way bill.',
            ], 422);
        }

        $bill->delete();

        return response()->json([
            'message' => 'Bill deleted successfully.',
        ]);
    }

    public function stats(Request $request)
    {
        $user = $request->user();
        $currentYear = now()->year;

        // Get aggregated stats in a single query
        $stats = $user->bills()->selectRaw('
            COUNT(*) as total_bills,
            COALESCE(SUM(total_amount), 0) as total_amount,
            COALESCE(SUM(gst_amount), 0) as total_gst,
            COALESCE(SUM(cgst), 0) as total_cgst,
            COALESCE(SUM(sgst), 0) as total_sgst,
            COALESCE(SUM(igst), 0) as total_igst
        ')->first();

        // Get monthly bills count for current month
        $startOfMonth = now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = now()->endOfMonth()->format('Y-m-d');
        $monthlyBills = $user->bills()
            ->whereBetween('bill_date', [$startOfMonth, $endOfMonth])
            ->count();

        // Get monthly revenue data for the current year (SQLite compatible)
        $startOfYear = "{$currentYear}-01-01";
        $endOfYear = "{$currentYear}-12-31";

        $monthlyRevenue = $user->bills()
            ->whereBetween('bill_date', [$startOfYear, $endOfYear])
            ->selectRaw("
                MONTH(bill_date) as month,
                SUM(total_amount) as revenue,
                SUM(gst_amount) as gst,
                COUNT(*) as bill_count
            ")
            ->groupBy(DB::raw("MONTH(bill_date)"))
            ->get()
            ->keyBy('month');

        // Build monthly arrays with all 12 months
        $revenueData = [];
        $gstData = [];
        $billCountData = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthData = $monthlyRevenue->get($month);
            $revenueData[] = $monthData ? round((float) $monthData->revenue, 2) : 0;
            $gstData[] = $monthData ? round((float) $monthData->gst, 2) : 0;
            $billCountData[] = $monthData ? (int) $monthData->bill_count : 0;
        }

        // Get top 5 parties by revenue
        $topParties = $user->bills()
            ->join('parties', 'bills.party_id', '=', 'parties.id')
            ->selectRaw('parties.id, parties.name, SUM(bills.total_amount) as total_revenue, COUNT(*) as bill_count')
            ->groupBy('parties.id', 'parties.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name ?? 'Unknown',
                    'revenue' => round((float) $item->total_revenue, 2),
                    'bills' => (int) $item->bill_count,
                ];
            });

        // Get recent 5 bills
        $recentBills = $user->bills()
            ->with('party:id,name')
            ->orderBy('bill_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get(['id', 'party_id', 'bill_number', 'bill_date', 'total_amount', 'gst_amount']);

        return response()->json([
            'totalBills' => (int) $stats->total_bills,
            'totalAmount' => round((float) $stats->total_amount, 2),
            'totalGst' => round((float) $stats->total_gst, 2),
            'totalCgst' => round((float) $stats->total_cgst, 2),
            'totalSgst' => round((float) $stats->total_sgst, 2),
            'totalIgst' => round((float) $stats->total_igst, 2),
            'monthlyBills' => $monthlyBills,
            'chartData' => [
                'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'revenue' => $revenueData,
                'gst' => $gstData,
                'billCount' => $billCountData,
            ],
            'topParties' => $topParties,
            'recentBills' => $recentBills,
        ]);
    }

    public function pdf(Request $request, Bill $bill)
    {
        $this->authorize('view', $bill);

        $bill->load(['party', 'items.product', 'user']);

        $pdf = Pdf::loadView('pdf.bill', [
            'bill' => $bill,
            'user' => $bill->user,
        ]);

        // Sanitize filename - party name with invoice number
        $partyName = preg_replace('/[^A-Za-z0-9 ]/', '', $bill->party->name ?? 'Unknown');
        $partyName = str_replace(' ', '-', trim($partyName));
        $billNumber = str_replace(['/', '\\'], '-', $bill->bill_number);
        $filename = "{$partyName}-{$billNumber}.pdf";

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
