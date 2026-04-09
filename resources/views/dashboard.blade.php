<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @hasrole('member')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 overflow-x-auto">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daftar Buku Perpustakaan</h3>
                        <table class="w-full text-sm border border-gray-200 rounded">
                            <thead class="bg-gray-100 text-gray-700 text-left">
                                <tr>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">Judul</th>
                                    <th class="px-4 py-3">Penulis</th>
                                    <th class="px-4 py-3">ISBN</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($books as $index => $book)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-500">{{ $books->firstItem() + $index }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $book->title }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $book->author ?: '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $book->isbn ?: '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if ($book->status === 'available')
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Tersedia</span>
                                            @else
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">Dipinjam</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada data buku.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $books->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
            @endhasrole
        </div>
    </div>
</x-app-layout>
