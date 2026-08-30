<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $baseUrl;
    protected string $deviceId;
    protected string $username;
    protected string $password;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.gowa.base_url', 'http://localhost:3000');
        $this->deviceId = config('services.gowa.device_id', '');
        $this->username = config('services.gowa.username', '');
        $this->password = config('services.gowa.password', '');
        $this->timeout = config('services.gowa.timeout', 15);
    }

    /**
     * Send a WhatsApp message using GOWA API
     *
     * @param string $phone The phone number in international format (e.g., 6281234567890)
     * @param string $message The message text to send
     * @return bool
     */
    public function send(string $phone, string $message): bool
    {
        if (empty($this->deviceId) || empty($phone)) {
            Log::warning('WhatsApp Service: Device ID or phone number is missing', [
                'phone' => $phone
            ]);
            return false;
        }

        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            
            // GOWA (go-whatsapp-web-multidevice) menggunakan format JID: nomor@s.whatsapp.net
            $phoneJid = $formattedPhone . '@s.whatsapp.net';
            
            $url = rtrim($this->baseUrl, '/') . '/send/message?device_id=' . $this->deviceId;
            $response = Http::withBasicAuth($this->username, $this->password)
                ->asJson()
                ->timeout($this->timeout)
                ->post($url, [
                    'phone' => $phoneJid,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $phoneJid,
                ]);
                return true;
            }

            Log::error('WhatsApp message failed', [
                'phone' => $phoneJid,
                'url' => $url,
                'status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            return false;
            
        } catch (\Exception $e) {
            Log::error('WhatsApp Service Error', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send notification for approved complaint
     */
    public function notifyComplaintApproved(string $phone, string $trackingCode, ?int $parentId = null, ?int $complaintId = null, ?string $studentName = null): bool
    {
        $siswaInfo = $studentName ? " untuk siswa *{$studentName}*" : "";
        $message = "Halo Bapak/Ibu,\n\nPengaduan Anda{$siswaInfo} dengan nomor tiket *{$trackingCode}* telah *DITERIMA* oleh Admin dan masuk ke sistem perhitungan prioritas kami.\n\nTerima kasih atas laporan Anda.";
        return $this->send($phone, $message);
    }

    /**
     * Send notification for rejected complaint
     */
    public function notifyComplaintRejected(string $phone, string $trackingCode, string $reason, ?int $parentId = null, ?int $complaintId = null, ?string $studentName = null): bool
    {
        $siswaInfo = $studentName ? " untuk siswa *{$studentName}*" : "";
        $message = "Halo Bapak/Ibu,\n\nMohon maaf, pengaduan Anda{$siswaInfo} dengan nomor tiket *{$trackingCode}* telah *DITOLAK* oleh Admin.\n\nAlasan: {$reason}\n\nTerima kasih.";
        return $this->send($phone, $message);
    }

    /**
     * Send notification for status change
     */
    public function notifyStatusChanged(string $phone, string $trackingCode, string $statusLabel, ?int $parentId = null, ?int $complaintId = null, ?string $studentName = null): bool
    {
        $siswaInfo = $studentName ? " untuk siswa *{$studentName}*" : "";
        $message = "Halo Bapak/Ibu,\n\nStatus pengaduan Anda{$siswaInfo} dengan nomor tiket *{$trackingCode}* telah diperbarui menjadi:\n\n*{$statusLabel}*\n\nSilakan cek aplikasi untuk detail selengkapnya.";
        return $this->send($phone, $message);
    }

    /**
     * Send notification for resolved complaint
     */
    public function notifyComplaintResolved(string $phone, string $trackingCode, string $resolutionNote, ?int $parentId = null, ?int $complaintId = null, ?string $studentName = null): bool
    {
        $siswaInfo = $studentName ? " untuk siswa *{$studentName}*" : "";
        $message = "Halo Bapak/Ibu,\n\nPengaduan Anda{$siswaInfo} dengan nomor tiket *{$trackingCode}* telah *SELESAI* kami proses dan tindak lanjuti.\n\nCatatan Penyelesaian:\n{$resolutionNote}\n\nTerima kasih atas laporan Anda. Silakan cek aplikasi untuk detail selengkapnya.";
        return $this->send($phone, $message);
    }

    /**
     * Format phone number to start with 62 instead of 0 or +62
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        return $phone;
    }
}
