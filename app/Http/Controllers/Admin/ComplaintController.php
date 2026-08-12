<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintStatusHistory;
use App\Services\AhpService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['parentUser', 'student', 'category', 'ahpResults'])
            ->latest()
            ->paginate(15);

        return view('admin.complaints.index', compact('complaints'));
    }

    /**
     * Verifikasi pengaduan: Terima (approve) atau Tolak (reject).
     * - Jika approve: jalankan AHP otomatis, kirim WA ke orang tua.
     * - Jika reject: simpan alasan, kirim WA ke orang tua.
     */
    public function verify(Request $request, Complaint $complaint)
    {
        $request->validate([
            'action'           => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:500',
        ]);

        if ($complaint->status !== Complaint::STATUS_PENDING) {
            return back()->with('error', 'Pengaduan ini sudah diverifikasi sebelumnya.');
        }

        $ahpService = new AhpService();
        $waService  = new WhatsappService();
        $parent     = $complaint->parentUser;
        $phone      = $parent?->phone;

        if ($request->action === 'approve') {
            // 1. Ubah status
            $complaint->update(['status' => Complaint::STATUS_APPROVED]);

            // 2. Catat riwayat status
            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'status'       => Complaint::STATUS_APPROVED,
                'note'         => 'Pengaduan diterima oleh Admin. Proses AHP dijalankan otomatis.',
                'changed_by'   => auth()->id(),
            ]);

            // 3. Jalankan AHP otomatis
            $ahpService->calculate($complaint->fresh());

            // 4. Notifikasi WA (fail-safe)
            if ($phone) {
                $studentName = $complaint->student?->name;
                $waService->notifyComplaintApproved($phone, $complaint->tracking_code, $parent->id, $complaint->id, $studentName);
            }

            return back()->with('success', "Pengaduan {$complaint->tracking_code} telah diterima. Nilai AHP dihitung otomatis.");
        }

        // REJECT
        $complaint->update([
            'status'           => Complaint::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        ComplaintStatusHistory::create([
            'complaint_id' => $complaint->id,
            'status'       => Complaint::STATUS_REJECTED,
            'note'         => 'Pengaduan ditolak: ' . $request->rejection_reason,
            'changed_by'   => auth()->id(),
        ]);

        if ($phone) {
            $studentName = $complaint->student?->name;
            $waService->notifyComplaintRejected($phone, $complaint->tracking_code, $request->rejection_reason, $parent->id, $complaint->id, $studentName);
        }

        return back()->with('success', "Pengaduan {$complaint->tracking_code} telah ditolak.");
    }

    /**
     * Update status pengaduan yang sudah diterima (approved → on_progress → resolved).
     */
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status'          => 'required|in:on_progress,resolved',
            'resolution_note' => 'nullable|string|max:1000',
        ]);

        $allowedTransitions = [
            Complaint::STATUS_APPROVED    => [Complaint::STATUS_ON_PROGRESS],
            Complaint::STATUS_ON_PROGRESS => [Complaint::STATUS_RESOLVED],
        ];

        if (!isset($allowedTransitions[$complaint->status]) || !in_array($request->status, $allowedTransitions[$complaint->status])) {
            return back()->with('error', 'Perubahan status tidak valid dari status saat ini.');
        }

        $updateData = ['status' => $request->status];
        if ($request->status === Complaint::STATUS_RESOLVED) {
            $updateData['resolved_at']      = now();
            $updateData['resolution_note']  = $request->resolution_note;
        }

        $complaint->update($updateData);

        ComplaintStatusHistory::create([
            'complaint_id' => $complaint->id,
            'status'       => $request->status,
            'note'         => $request->resolution_note ?? 'Status diperbarui oleh Admin.',
            'changed_by'   => auth()->id(),
        ]);

        // WA Notification
        $parent = $complaint->parentUser;
        if ($parent?->phone) {
            $waLabel = $complaint->fresh()->statusLabel;
            $studentName = $complaint->student?->name;
            (new WhatsappService())->notifyStatusChanged($parent->phone, $complaint->tracking_code, $waLabel, $parent->id, $complaint->id, $studentName);
        }

        return back()->with('success', "Status pengaduan berhasil diubah ke: {$complaint->fresh()->statusLabel}");
    }
}
