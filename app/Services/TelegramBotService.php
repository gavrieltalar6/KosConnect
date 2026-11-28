<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Menangani pesan masuk ke bot Telegram KosConnect untuk menghubungkan
 * akun Telegram penghuni (menyimpan telegram_chat_id).
 *
 * Catatan: Telegram TIDAK bisa mengirim pesan hanya dari nomor HP. Bot baru boleh
 * mengirim pesan ke user yang sudah menekan Start / membuka chat dengan bot.
 * Dua cara menghubungkan yang didukung:
 *  1. Deep link  : https://t.me/<bot>?start=<telegram_link_token>  (satu klik)
 *  2. Nomor HP   : penghuni menekan tombol "Bagikan Nomor HP", lalu nomor dicocokkan
 *                  dengan kolom tenants.phone.
 */
class TelegramBotService
{
    public function handleUpdate(array $update): void
    {
        $message = $update['message'] ?? null;

        if (! $message || ! isset($message['chat']['id'])) {
            return;
        }

        // Hanya chat pribadi (bukan grup/channel)
        if (($message['chat']['type'] ?? 'private') !== 'private') {
            return;
        }

        $chatId = (int) $message['chat']['id'];

        // Penghuni membagikan kontak lewat tombol
        if (isset($message['contact'])) {
            $this->handleContact($chatId, $message);

            return;
        }

        $text = trim((string) ($message['text'] ?? ''));

        if (str_starts_with($text, '/start')) {
            $payload = trim(substr($text, strlen('/start')));
            $payload !== ''
                ? $this->linkByToken($chatId, $payload)
                : $this->askForPhone($chatId);

            return;
        }

        if (str_starts_with($text, '/stop')) {
            $this->unlink($chatId);

            return;
        }

        $this->reply($chatId, "Halo! Saya bot pengingat iuran *KosConnect*.\n\nKetik /start untuk menghubungkan akun Anda, atau /stop untuk berhenti menerima pengingat.");
    }

    protected function linkByToken(int $chatId, string $token): void
    {
        $tenant = Tenant::where('telegram_link_token', $token)->first();

        if (! $tenant) {
            $this->reply($chatId, 'Tautan tidak valid atau sudah kedaluwarsa. Silakan buka lagi halaman *Pengingat Iuran* di aplikasi KosConnect dan tekan tombol hubungkan.');

            return;
        }

        $this->completeLink($tenant, $chatId);
    }

    protected function handleContact(int $chatId, array $message): void
    {
        $contact = $message['contact'];

        // Pastikan kontak yang dibagikan adalah milik pengirim sendiri, bukan kontak orang lain
        if (($contact['user_id'] ?? null) !== ($message['from']['id'] ?? null)) {
            $this->reply($chatId, 'Mohon bagikan nomor HP *Anda sendiri* lewat tombol yang disediakan.', true);

            return;
        }

        $normalized = self::normalizePhone((string) ($contact['phone_number'] ?? ''));

        $matches = Tenant::where('status', 'active')->get()
            ->filter(fn (Tenant $t) => self::normalizePhone((string) $t->phone) === $normalized)
            ->values();

        if ($normalized === '' || $matches->count() !== 1) {
            $this->reply($chatId, $matches->count() > 1
                ? 'Nomor HP ini terdaftar lebih dari satu penghuni. Silakan hubungkan lewat tombol di halaman *Pengingat Iuran* aplikasi KosConnect.'
                : 'Nomor HP Anda belum terdaftar sebagai penghuni aktif. Hubungi pengurus kos untuk memastikan nomor HP Anda sudah benar.');

            return;
        }

        $this->completeLink($matches->first(), $chatId);
    }

    protected function askForPhone(int $chatId): void
    {
        $this->post('sendMessage', [
            'chat_id' => $chatId,
            'text' => "Halo! Saya bot pengingat iuran *KosConnect*.\n\nUntuk menghubungkan akun, tekan tombol *Bagikan Nomor HP* di bawah. Nomor akan dicocokkan dengan nomor yang terdaftar di data penghuni.",
            'parse_mode' => 'Markdown',
            'reply_markup' => [
                'keyboard' => [[['text' => '📱 Bagikan Nomor HP', 'request_contact' => true]]],
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ],
        ]);
    }

    protected function completeLink(Tenant $tenant, int $chatId): void
    {
        // Satu chat Telegram hanya boleh terhubung ke satu penghuni
        Tenant::where('telegram_chat_id', $chatId)->where('id', '!=', $tenant->id)
            ->update(['telegram_chat_id' => null]);

        $tenant->update(['telegram_chat_id' => $chatId]);

        $this->reply($chatId,
            "✅ Akun Telegram berhasil terhubung, *{$this->escape($tenant->full_name)}*!\n\n"
            . "Anda akan menerima pengingat iuran kos pada *H-7*, *H-1*, dan *hari jatuh tempo*.",
            true
        );
    }

    protected function unlink(int $chatId): void
    {
        $count = Tenant::where('telegram_chat_id', $chatId)->update(['telegram_chat_id' => null]);

        $this->reply($chatId, $count
            ? 'Akun Telegram diputuskan. Anda tidak akan menerima pengingat lagi. Ketik /start untuk menghubungkan kembali.'
            : 'Akun Telegram ini belum terhubung ke penghuni mana pun.',
            true
        );
    }

    /** 0812..., +62812..., 62812... -> 62812... */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        return $digits;
    }

    protected function reply(int $chatId, string $text, bool $removeKeyboard = false): void
    {
        $payload = ['chat_id' => $chatId, 'text' => $text, 'parse_mode' => 'Markdown'];

        if ($removeKeyboard) {
            $payload['reply_markup'] = ['remove_keyboard' => true];
        }

        $this->post('sendMessage', $payload);
    }

    protected function post(string $method, array $payload): void
    {
        $token = config('services.telegram.bot_token');

        if (! $token) {
            Log::warning('TelegramBotService: TELEGRAM_BOT_TOKEN belum diset.');

            return;
        }

        try {
            $response = Http::timeout(8)->post("https://api.telegram.org/bot{$token}/{$method}", $payload);

            if (! $response->successful()) {
                Log::warning("TelegramBotService {$method} gagal", ['body' => $response->body()]);
            }
        } catch (\Throwable $e) {
            Log::error("TelegramBotService {$method} exception: " . $e->getMessage());
        }
    }

    /** Escape karakter khusus Markdown (legacy) Telegram. */
    protected function escape(string $text): string
    {
        return preg_replace('/([_*`\[])/', '\\\\$1', $text) ?? $text;
    }
}
