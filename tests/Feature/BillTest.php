<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Party;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Party $party;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'business_name' => 'Test Business',
            'gstin' => '27AAPFU0939F1ZV',
        ]);

        $this->party = Party::create([
            'user_id' => $this->user->id,
            'name' => 'Test Party',
            'gstin' => '27AAPFU0939F1ZV',
            'address' => '123 Test Street',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
        ]);

        $this->product = Product::create([
            'user_id' => $this->user->id,
            'name' => 'Test Product',
            'hsn_code' => '1234',
            'unit' => 'Nos',
            'price' => 1000,
            'gst_rate' => 18,
        ]);
    }

    public function test_user_can_create_bill(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/bills', [
                'party_id' => $this->party->id,
                'bill_date' => now()->format('Y-m-d'),
                'is_inter_state' => false,
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'hsn_code' => '1234',
                        'quantity' => 2,
                        'price' => 1000,
                        'gst_rate' => 18,
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'bill_number',
                'subtotal',
                'cgst',
                'sgst',
                'gst_amount',
                'total_amount',
            ]);

        // Verify calculations
        $bill = $response->json();
        $this->assertEquals(2000, $bill['subtotal']); // 2 * 1000
        $this->assertEquals(180, $bill['cgst']); // 9% of 2000
        $this->assertEquals(180, $bill['sgst']); // 9% of 2000
        $this->assertEquals(360, $bill['gst_amount']); // 18% of 2000
        $this->assertEquals(2360, $bill['total_amount']); // 2000 + 360
    }

    public function test_user_can_list_bills(): void
    {
        Bill::create([
            'user_id' => $this->user->id,
            'party_id' => $this->party->id,
            'bill_number' => 'INV/2024/0001',
            'bill_date' => now(),
            'subtotal' => 1000,
            'gst_amount' => 180,
            'total_amount' => 1180,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/bills');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'bill_number', 'total_amount'],
                ],
            ]);
    }

    public function test_user_can_view_bill(): void
    {
        $bill = Bill::create([
            'user_id' => $this->user->id,
            'party_id' => $this->party->id,
            'bill_number' => 'INV/2024/0001',
            'bill_date' => now(),
            'subtotal' => 1000,
            'gst_amount' => 180,
            'total_amount' => 1180,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/bills/{$bill->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'bill_number',
                'party',
                'items',
            ]);
    }

    public function test_user_cannot_view_other_users_bill(): void
    {
        $otherUser = User::factory()->create();
        $otherParty = Party::create([
            'user_id' => $otherUser->id,
            'name' => 'Other Party',
            'address' => 'Other Address',
        ]);

        $otherBill = Bill::create([
            'user_id' => $otherUser->id,
            'party_id' => $otherParty->id,
            'bill_number' => 'INV/2024/0002',
            'bill_date' => now(),
            'subtotal' => 1000,
            'gst_amount' => 180,
            'total_amount' => 1180,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/bills/{$otherBill->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_get_bill_stats(): void
    {
        Bill::create([
            'user_id' => $this->user->id,
            'party_id' => $this->party->id,
            'bill_number' => 'INV/2024/0001',
            'bill_date' => now(),
            'subtotal' => 1000,
            'gst_amount' => 180,
            'total_amount' => 1180,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/bills/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'totalBills',
                'totalAmount',
                'totalGst',
                'monthlyBills',
            ]);
    }
}
