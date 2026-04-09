<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            👥 {{ __('Semua User') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Status Messages --}}
            @if (session('status'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        {{-- Search --}}
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Nama / Email</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>

                        {{-- Filter Kelas --}}
                        <div>
                            <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                            <select name="kelas" id="kelas" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->name }}" {{ request('kelas') == $class->name ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Jurusan --}}
                        <div>
                            <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                            <select name="jurusan" id="jurusan" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">-- Semua Jurusan --</option>
                                @foreach ($majors as $major)
                                    <option value="{{ $major->name }}" {{ request('jurusan') == $major->name ? 'selected' : '' }}>{{ $major->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4 flex justify-end gap-2 mt-2">
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center">
                                Reset Filter
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 flex items-center">
                                Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-sm border border-gray-200 rounded">
                        <thead class="bg-gray-100 text-gray-700 text-left">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Kelas / Jurusan</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $index => $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500">{{ $users->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        @if ($user->kelas || $user->jurusan)
                                            {{ $user->kelas ?? '-' }} / {{ $user->jurusan ?? '-' }}
                                        @else
                                            <span class="text-xs text-gray-400 italic">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold uppercase tracking-wider">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($user->status === 'active')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">✅ Active</span>
                                        @elseif ($user->status === 'suspended')
                                            <span class="px-2 py-1 bg-red-800 text-white rounded text-xs font-bold">❌ Suspend</span>
                                        @elseif ($user->status === 'pending')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">⏳ Pending</span>
                                        @elseif ($user->status === 'approved')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">✓ Approved</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold capitalize">{{ $user->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus permanen akun {{ $user->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-xs rounded bg-red-500 hover:bg-red-600 text-white font-semibold transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-400">Tidak ada user yang ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
