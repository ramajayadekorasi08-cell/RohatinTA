<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintStatusHistory;
use App\Models\Rating;
use App\Models\NotificationLog;
use App\Models\AhpCriterion;
use App\Models\AhpComparison;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============ USERS ============
        $admin = User::create([
            'name' => 'Rohatin',
            'username' => 'admin',
            'email' => 'admin@sipadu.test',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $principal = User::create([
            'name' => 'Kepala Sekolah SDN Aengbaja Kenek II',
            'username' => 'kepala.sekolah',
            'email' => 'kepala@sipadu.test',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
            'role' => 'principal',
            'is_active' => true,
        ]);

        $parent1 = User::create([
            'name' => 'Ahmad Fauzi',
            'username' => 'orangtua',
            'email' => 'orangtua@sipadu.test',
            'phone' => '081234567892',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'is_active' => true,
        ]);

        $parent2 = User::create([
            'name' => 'Siti Aisyah',
            'username' => 'orangtua2',
            'email' => 'orangtua2@sipadu.test',
            'phone' => '081234567893',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'is_active' => true,
        ]);

        $parent3 = User::create([
            'name' => 'Budi Santoso',
            'username' => 'orangtua3',
            'email' => 'orangtua3@sipadu.test',
            'phone' => '081234567894',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'is_active' => true,
        ]);

        // ============ STUDENTS ============
        $student1 = Student::create([
            'nis' => '20260001',
            'name' => 'Muhammad Rizky Fauzi',
            'class' => 'Kelas 5A',
            'parent_id' => $parent1->id,
        ]);

        $student2 = Student::create([
            'nis' => '20260002',
            'name' => 'Aisyah Putri',
            'class' => 'Kelas 4B',
            'parent_id' => $parent1->id,
        ]);

        $student3 = Student::create([
            'nis' => '20260003',
            'name' => 'Farah Aisyah',
            'class' => 'Kelas 3A',
            'parent_id' => $parent2->id,
        ]);

        $student4 = Student::create([
            'nis' => '20260004',
            'name' => 'Dimas Santoso',
            'class' => 'Kelas 6A',
            'parent_id' => $parent3->id,
        ]);

        $student5 = Student::create([
            'nis' => '20260005',
            'name' => 'Rina Santoso',
            'class' => 'Kelas 2B',
            'parent_id' => $parent3->id,
        ]);

        // ============ CATEGORIES ============
        $cat1 = Category::create([
            'name' => 'Sarana dan Prasarana',
            'description' => 'Pengaduan terkait kerusakan fasilitas, ruang kelas, toilet, meja/kursi, dan lingkungan sekolah.',
            'is_active' => true,
        ]);

        $cat2 = Category::create([
            'name' => 'Proses Akademik',
            'description' => 'Pengaduan terkait kegiatan belajar mengajar, kurikulum, tugas/PR, dan metode pembelajaran.',
            'is_active' => true,
        ]);

        $cat3 = Category::create([
            'name' => 'Non-Akademik/Kesiswaan',
            'description' => 'Pengaduan terkait bullying/perundungan, kedisiplinan, administrasi, dan masalah siswa.',
            'is_active' => true,
        ]);

        // ============ SAMPLE COMPLAINTS ============
        $now = Carbon::now();

        // Complaint 1 - Resolved
        $c1 = Complaint::create([
            'tracking_code' => 'ADU-2026-00001',
            'parent_id' => $parent1->id,
            'student_id' => $student1->id,
            'category_id' => $cat1->id,
            'title' => 'Meja Belajar Rusak di Kelas 5A',
            'description' => 'Meja belajar anak saya di kelas 5A rusak pada bagian kaki meja sehingga tidak stabil dan mengganggu proses belajar. Mohon segera diperbaiki.',
            'status' => 'resolved',
            'priority_score' => 0.75,
            'priority_level' => 'high',
            'assigned_to' => 'Pak Samsul (Bagian Sarana)',
            'resolution_note' => 'Meja telah diganti dengan meja baru. Terima kasih atas laporannya.',
            'submitted_at' => $now->copy()->subDays(14),
            'resolved_at' => $now->copy()->subDays(7),
        ]);

        ComplaintStatusHistory::create(['complaint_id' => $c1->id, 'status' => 'pending', 'note' => 'Pengaduan dikirim oleh orang tua', 'changed_by' => $parent1->id, 'created_at' => $now->copy()->subDays(14)]);
        ComplaintStatusHistory::create(['complaint_id' => $c1->id, 'status' => 'approved', 'note' => 'Pengaduan diterima dan diteruskan ke Bagian Sarana', 'changed_by' => $admin->id, 'created_at' => $now->copy()->subDays(13)]);
        ComplaintStatusHistory::create(['complaint_id' => $c1->id, 'status' => 'on_progress', 'note' => 'Sedang mengganti meja yang rusak', 'changed_by' => $admin->id, 'created_at' => $now->copy()->subDays(10)]);
        ComplaintStatusHistory::create(['complaint_id' => $c1->id, 'status' => 'resolved', 'note' => 'Meja telah diganti dengan meja baru', 'changed_by' => $admin->id, 'created_at' => $now->copy()->subDays(7)]);

        Rating::create(['complaint_id' => $c1->id, 'user_id' => $parent1->id, 'rating' => 5, 'comment' => 'Sangat puas dengan penanganan. Meja sudah diganti dengan cepat.']);

        // Complaint 2 - On Progress
        $c2 = Complaint::create([
            'tracking_code' => 'ADU-2026-00002',
            'parent_id' => $parent2->id,
            'student_id' => $student3->id,
            'category_id' => $cat3->id,
            'title' => 'Anak Saya Mengalami Perundungan',
            'description' => 'Anak saya sering diejek dan dipermalukan oleh teman-temannya di kelas. Sudah berlangsung selama 2 minggu terakhir. Mohon ditindaklanjuti.',
            'status' => 'on_progress',
            'priority_score' => 0.90,
            'priority_level' => 'high',
            'assigned_to' => 'Bu Fatimah (Wali Kelas 3A)',
            'submitted_at' => $now->copy()->subDays(5),
        ]);

        ComplaintStatusHistory::create(['complaint_id' => $c2->id, 'status' => 'pending', 'note' => 'Pengaduan dikirim oleh orang tua', 'changed_by' => $parent2->id, 'created_at' => $now->copy()->subDays(5)]);
        ComplaintStatusHistory::create(['complaint_id' => $c2->id, 'status' => 'approved', 'note' => 'Pengaduan diterima. Diteruskan ke Wali Kelas 3A', 'changed_by' => $admin->id, 'created_at' => $now->copy()->subDays(4)]);
        ComplaintStatusHistory::create(['complaint_id' => $c2->id, 'status' => 'on_progress', 'note' => 'Wali kelas sedang melakukan mediasi antara siswa', 'changed_by' => $admin->id, 'created_at' => $now->copy()->subDays(3)]);

        // Complaint 3 - Approved
        $c3 = Complaint::create([
            'tracking_code' => 'ADU-2026-00003',
            'parent_id' => $parent3->id,
            'student_id' => $student4->id,
            'category_id' => $cat2->id,
            'title' => 'Metode Pembelajaran Kurang Efektif',
            'description' => 'Anak saya merasa sulit memahami pelajaran Matematika karena metode yang digunakan kurang interaktif. Mohon bisa menggunakan metode yang lebih menarik.',
            'status' => 'approved',
            'priority_score' => 0.50,
            'priority_level' => 'medium',
            'assigned_to' => 'Bu Sari (Guru Matematika)',
            'submitted_at' => $now->copy()->subDays(3),
        ]);

        ComplaintStatusHistory::create(['complaint_id' => $c3->id, 'status' => 'pending', 'note' => 'Pengaduan dikirim oleh orang tua', 'changed_by' => $parent3->id, 'created_at' => $now->copy()->subDays(3)]);
        ComplaintStatusHistory::create(['complaint_id' => $c3->id, 'status' => 'approved', 'note' => 'Pengaduan diterima. Diteruskan ke Guru Matematika', 'changed_by' => $admin->id, 'created_at' => $now->copy()->subDays(2)]);

        // Complaint 4 - Pending
        $c4 = Complaint::create([
            'tracking_code' => 'ADU-2026-00004',
            'parent_id' => $parent1->id,
            'student_id' => $student2->id,
            'category_id' => $cat1->id,
            'title' => 'Toilet Sekolah Kotor',
            'description' => 'Toilet yang digunakan siswa kelas 4B sangat kotor dan berbau. Anak saya sering menahan untuk tidak ke toilet di sekolah. Mohon kebersihan toilet ditingkatkan.',
            'status' => 'pending',
            'priority_score' => 0.60,
            'priority_level' => 'medium',
            'submitted_at' => $now->copy()->subDay(),
        ]);

        ComplaintStatusHistory::create(['complaint_id' => $c4->id, 'status' => 'pending', 'note' => 'Pengaduan dikirim oleh orang tua', 'changed_by' => $parent1->id, 'created_at' => $now->copy()->subDay()]);

        // Complaint 5 - Rejected
        $c5 = Complaint::create([
            'tracking_code' => 'ADU-2026-00005',
            'parent_id' => $parent3->id,
            'student_id' => $student5->id,
            'category_id' => $cat3->id,
            'title' => 'Keluhan Mengenai Menu Kantin',
            'description' => 'Makanan di kantin sekolah kurang sehat dan kurang bervariasi.',
            'status' => 'rejected',
            'rejection_reason' => 'Kantin sekolah dikelola oleh pihak ketiga. Pengaduan terkait kantin dapat disampaikan langsung ke penjaga kantin.',
            'submitted_at' => $now->copy()->subDays(10),
        ]);

        ComplaintStatusHistory::create(['complaint_id' => $c5->id, 'status' => 'pending', 'note' => 'Pengaduan dikirim oleh orang tua', 'changed_by' => $parent3->id, 'created_at' => $now->copy()->subDays(10)]);
        ComplaintStatusHistory::create(['complaint_id' => $c5->id, 'status' => 'rejected', 'note' => 'Kantin dikelola pihak ketiga', 'changed_by' => $admin->id, 'created_at' => $now->copy()->subDays(9)]);

        // Complaint 6 - Resolved with rating
        $c6 = Complaint::create([
            'tracking_code' => 'ADU-2026-00006',
            'parent_id' => $parent2->id,
            'student_id' => $student3->id,
            'category_id' => $cat1->id,
            'title' => 'Kipas Angin Kelas 3A Rusak',
            'description' => 'Kipas angin di kelas 3A tidak berfungsi sehingga suasana kelas sangat panas dan tidak nyaman untuk belajar.',
            'status' => 'resolved',
            'priority_score' => 0.65,
            'priority_level' => 'medium',
            'assigned_to' => 'Pak Samsul (Bagian Sarana)',
            'resolution_note' => 'Kipas angin telah diperbaiki dan berfungsi kembali.',
            'submitted_at' => $now->copy()->subDays(20),
            'resolved_at' => $now->copy()->subDays(15),
        ]);

        ComplaintStatusHistory::create(['complaint_id' => $c6->id, 'status' => 'pending', 'note' => 'Pengaduan dikirim oleh orang tua', 'changed_by' => $parent2->id, 'created_at' => $now->copy()->subDays(20)]);
        ComplaintStatusHistory::create(['complaint_id' => $c6->id, 'status' => 'approved', 'note' => 'Diterima, diteruskan ke Bagian Sarana', 'changed_by' => $admin->id, 'created_at' => $now->copy()->subDays(19)]);
        ComplaintStatusHistory::create(['complaint_id' => $c6->id, 'status' => 'on_progress', 'note' => 'Petugas sedang memeriksa kipas angin', 'changed_by' => $admin->id, 'created_at' => $now->copy()->subDays(17)]);
        ComplaintStatusHistory::create(['complaint_id' => $c6->id, 'status' => 'resolved', 'note' => 'Kipas angin telah diperbaiki', 'changed_by' => $admin->id, 'created_at' => $now->copy()->subDays(15)]);

        Rating::create(['complaint_id' => $c6->id, 'user_id' => $parent2->id, 'rating' => 4, 'comment' => 'Penanganan cukup cepat. Terima kasih.']);

        // ============ NOTIFICATION LOGS ============
        NotificationLog::create([
            'user_id' => $parent1->id, 'complaint_id' => $c1->id, 'phone' => '081234567892',
            'type' => 'complaint_created', 'message' => 'Pengaduan ADU-2026-00001 berhasil dikirim.',
            'status' => 'sent', 'provider' => 'gowa', 'sent_at' => $now->copy()->subDays(14),
        ]);
        NotificationLog::create([
            'user_id' => $parent1->id, 'complaint_id' => $c1->id, 'phone' => '081234567892',
            'type' => 'status_resolved', 'message' => 'Pengaduan ADU-2026-00001 telah selesai ditindaklanjuti.',
            'status' => 'sent', 'provider' => 'gowa', 'sent_at' => $now->copy()->subDays(7),
        ]);
        NotificationLog::create([
            'user_id' => $parent2->id, 'complaint_id' => $c2->id, 'phone' => '081234567893',
            'type' => 'complaint_created', 'message' => 'Pengaduan ADU-2026-00002 berhasil dikirim.',
            'status' => 'failed', 'provider' => 'gowa', 'error_message' => 'Connection timeout',
        ]);

        // ============ AHP CRITERIA ============
        AhpCriterion::create(['name' => 'Urgensi', 'code' => 'URG', 'description' => 'Tingkat urgensi pengaduan yang perlu segera ditangani', 'weight' => 0.5396]);
        AhpCriterion::create(['name' => 'Dampak', 'code' => 'DMP', 'description' => 'Seberapa besar dampak pengaduan terhadap proses belajar mengajar', 'weight' => 0.2970]);
        AhpCriterion::create(['name' => 'Waktu Penyelesaian', 'code' => 'WKT', 'description' => 'Estimasi waktu yang dibutuhkan untuk penyelesaian', 'weight' => 0.1634]);

        // Default AHP pairwise comparisons (Saaty scale)
        $criteria = AhpCriterion::all();
        $matrix = [
            [1, 3, 5],
            [1/3, 1, 2],
            [1/5, 1/2, 1],
        ];

        for ($i = 0; $i < count($criteria); $i++) {
            for ($j = 0; $j < count($criteria); $j++) {
                AhpComparison::create([
                    'criteria_row_id' => $criteria[$i]->id,
                    'criteria_col_id' => $criteria[$j]->id,
                    'value' => round($matrix[$i][$j], 4),
                ]);
            }
        }
    }
}
