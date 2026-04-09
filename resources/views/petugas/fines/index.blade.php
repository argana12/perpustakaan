<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Denda</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-xl shadow p-5 overflow-x-auto">
                <table class="w-full text-sm border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Peminjam</th>
                            <th class="px-3 py-2 text-left">Buku</th>
                            <th class="px-3 py-2 text-left">Tanggal Kembali</th>
                            <th class="px-3 py-2 text-left">Denda</th>
                            <th class="px-3 py-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($fines as $fine)
                            <tr>
                                <td class="px-3 py-2">{{ $fine->user->name }}</td>
                                <td class="px-3 py-2">{{ $fine->book->title }} ({{ $fine->book->code }})</td>
                                <td class="px-3 py-2">{{ optional($fine->return_date)->format('d M Y H:i') }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($fine->fine, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">
                                    <form method="POST" action="{{ route('petugas.borrows.pay-fine', $fine) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 text-white text-xs rounded">Lunasi</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">Tidak ada denda aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $fines->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
