<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Petugas Perpustakaan
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4">
            <p class="text-gray-600 mb-6">Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Gunakan menu di bawah untuk mengelola anggota.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('petugas.member.approval') }}"
                   class="block p-6 bg-white rounded-xl shadow hover:shadow-md transition border-l-4 border-yellow-400">
                    <div class="text-3xl mb-2">⏳</div>
                    <h3 class="font-bold text-gray-800">Antrian Persetujuan</h3>
                    <p class="text-sm text-gray-500 mt-1">Lihat dan setujui member yang menunggu kode aktivasi.</p>
                </a>

                <a href="{{ route('petugas.users') }}"
                   class="block p-6 bg-white rounded-xl shadow hover:shadow-md transition border-l-4 border-blue-400">
                    <div class="text-3xl mb-2">👥</div>
                    <h3 class="font-bold text-gray-800">Semua Anggota</h3>
                    <p class="text-sm text-gray-500 mt-1">Lihat daftar seluruh anggota perpustakaan.</p>
                </a>

                <a href="{{ route('petugas.circulation.loan') }}"
                   class="block p-6 bg-white rounded-xl shadow hover:shadow-md transition border-l-4 border-green-400">
                    <div class="text-3xl mb-2">📚</div>
                    <h3 class="font-bold text-gray-800">Mode Peminjaman</h3>
                    <p class="text-sm text-gray-500 mt-1">Scan kode/QR buku lalu konfirmasi pinjam.</p>
                </a>

                <a href="{{ route('petugas.circulation.return') }}"
                   class="block p-6 bg-white rounded-xl shadow hover:shadow-md transition border-l-4 border-emerald-500">
                    <div class="text-3xl mb-2">✅</div>
                    <h3 class="font-bold text-gray-800">Mode Pengembalian</h3>
                    <p class="text-sm text-gray-500 mt-1">Scan buku untuk proses pengembalian dan denda.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
