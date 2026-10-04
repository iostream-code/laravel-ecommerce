<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_landing_dan_katalog_terbuka_untuk_publik(): void
    {
        $this->get('/')->assertOk();
        $this->get('/katalog')->assertOk();
    }

    public function test_pembeli_tidak_bisa_akses_panel_admin(): void
    {
        $pembeli = User::where('email', 'budi@demo.test')->first();

        $this->actingAs($pembeli)->get('/admin')->assertForbidden();
        $this->actingAs($pembeli)->get('/admin/produk')->assertForbidden();
        $this->actingAs($pembeli)->get('/admin/pesanan')->assertForbidden();
    }

    public function test_admin_bisa_akses_panel_admin(): void
    {
        $admin = User::where('email', 'admin@demo.test')->first();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/produk')->assertOk();
        $this->actingAs($admin)->get('/admin/pesanan')->assertOk();
    }

    public function test_pesanan_user_lain_tidak_bisa_dilihat(): void
    {
        $pembeli = User::where('email', 'budi@demo.test')->first();
        $lain = User::create([
            'name' => 'Orang Lain', 'email' => 'lain@demo.test',
            'password' => bcrypt('password123'),
        ]);
        $order = $lain->orders()->create(['status' => 'menunggu_pembayaran', 'total' => 1000]);

        $this->actingAs($pembeli)->get("/order/{$order->id}")->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_login_saat_akses_keranjang(): void
    {
        $this->get('/cart')->assertRedirect('/login');
    }

    public function test_bukti_bayar_hanya_untuk_pemilik_dan_admin(): void
    {
        $pembeli = User::where('email', 'budi@demo.test')->first();
        $admin = User::where('email', 'admin@demo.test')->first();
        $lain = User::create([
            'name' => 'Orang Lain', 'email' => 'lain@demo.test',
            'password' => bcrypt('password123'),
        ]);

        \Illuminate\Support\Facades\Storage::fake('local');
        \Illuminate\Support\Facades\Storage::disk('local')->put('bukti-bayar/tes.png', 'isi');

        $order = $pembeli->orders()->create([
            'status' => 'menunggu_verifikasi', 'total' => 1000,
            'payment_receipt' => 'bukti-bayar/tes.png',
        ]);

        $this->actingAs($lain)->get("/order/{$order->id}/bukti")->assertForbidden();
        $this->actingAs($pembeli)->get("/order/{$order->id}/bukti")->assertOk();
        $this->actingAs($admin)->get("/order/{$order->id}/bukti")->assertOk();
    }

    public function test_bukti_bayar_dikonversi_ke_webp(): void
    {
        $pembeli = User::where('email', 'budi@demo.test')->first();
        $order = $pembeli->orders()->create(['status' => 'menunggu_pembayaran', 'total' => 1000]);

        \Illuminate\Support\Facades\Storage::fake('local');
        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti.jpg', 800, 600);

        $this->actingAs($pembeli)
            ->post("/order/{$order->id}/pay", ['payment_receipt' => $file])
            ->assertRedirect();

        $order->refresh();
        $this->assertStringEndsWith('.webp', $order->payment_receipt);
        \Illuminate\Support\Facades\Storage::disk('local')->assertExists($order->payment_receipt);
        $this->assertSame('menunggu_verifikasi', $order->status);
    }
}
