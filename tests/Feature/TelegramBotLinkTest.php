<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\TenantStatus;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramBotLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.telegram.bot_token' => 'test-token', 'services.telegram.webhook_secret' => 'rahasia']);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);
    }

    private function makeTenant(string $phone = '081234567890', string $token = 'tok123'): Tenant
    {
        $user = User::create(['name' => 'Budi', 'email' => uniqid() . '@test.com', 'password' => bcrypt('x'), 'role' => Role::PENGGUNI->value, 'is_active' => true]);

        return Tenant::create([
            'user_id' => $user->id, 'full_name' => 'Budi_Santoso', 'phone' => $phone, 'origin_address' => 'Bandung',
            'guardian_phone' => '0822222222', 'check_in_date' => now(), 'due_day' => 10,
            'telegram_link_token' => $token, 'status' => TenantStatus::ACTIVE,
        ]);
    }

    public function test_start_with_token_links_chat_id(): void
    {
        $tenant = $this->makeTenant();

        $this->postJson('/telegram/webhook', [
            'message' => ['chat' => ['id' => 555, 'type' => 'private'], 'text' => '/start tok123'],
        ], ['X-Telegram-Bot-Api-Secret-Token' => 'rahasia'])->assertOk();

        $this->assertSame(555, $tenant->fresh()->telegram_chat_id);
    }

    public function test_invalid_token_does_not_link(): void
    {
        $tenant = $this->makeTenant();

        $this->postJson('/telegram/webhook', [
            'message' => ['chat' => ['id' => 555, 'type' => 'private'], 'text' => '/start salah'],
        ], ['X-Telegram-Bot-Api-Secret-Token' => 'rahasia'])->assertOk();

        $this->assertNull($tenant->fresh()->telegram_chat_id);
    }

    public function test_webhook_rejects_wrong_secret(): void
    {
        $this->postJson('/telegram/webhook', ['message' => []], ['X-Telegram-Bot-Api-Secret-Token' => 'salah'])
            ->assertForbidden();
    }

    public function test_shared_contact_matches_phone_in_any_format(): void
    {
        $tenant = $this->makeTenant('0812-3456-7890');

        app(TelegramBotService::class)->handleUpdate([
            'message' => [
                'chat' => ['id' => 777, 'type' => 'private'],
                'from' => ['id' => 777],
                'contact' => ['phone_number' => '+6281234567890', 'user_id' => 777],
            ],
        ]);

        $this->assertSame(777, $tenant->fresh()->telegram_chat_id);
    }

    public function test_contact_of_another_person_is_rejected(): void
    {
        $tenant = $this->makeTenant();

        app(TelegramBotService::class)->handleUpdate([
            'message' => [
                'chat' => ['id' => 777, 'type' => 'private'],
                'from' => ['id' => 777],
                'contact' => ['phone_number' => '081234567890', 'user_id' => 999],
            ],
        ]);

        $this->assertNull($tenant->fresh()->telegram_chat_id);
    }

    public function test_normalize_phone(): void
    {
        $this->assertSame('6281234567890', TelegramBotService::normalizePhone('0812-3456-7890'));
        $this->assertSame('6281234567890', TelegramBotService::normalizePhone('+62 812 3456 7890'));
    }
}
