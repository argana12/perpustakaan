<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Antrian Persetujuan Member
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 space-y-6">

            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Kode yang baru di-generate --}}
            @if (session('generated'))
                @php $gen = session('generated'); @endphp
                <div class="p-4 bg-green-50 border-2 border-green-400 rounded-lg">
                    <p class="font-bold text-green-800 text-lg mb-1">✅ Kode Aktivasi Berhasil Dibuat!</p>
                    <p class="text-sm text-gray-700">Untuk: <strong>{{ $gen['nama'] }}</strong> ({{ $gen['email'] }})</p>
                    <p class="text-3xl font-mono font-bold tracking-widest text-green-700 mt-2 bg-white border border-green-300 px-4 py-2 rounded inline-block">
                        {{ $gen['kode'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">⚠️ Catat dan berikan kode ini kepada user. Kode hanya berlaku 24 jam.</p>
                </div>
            @endif

            {{-- Info Kelas & Jurusan (petugas hanya bisa lihat, tidak bisa tambah) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        🏫 Daftar Kelas Tersedia
                        <span class="text-xs font-normal bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">View Only</span>
                    </h3>
                    <div class="max-h-36 overflow-y-auto space-y-1">
                        @forelse ($classes as $class)
                            <div class="bg-gray-50 rounded-lg px-3 py-2">
                                <span class="text-sm font-medium text-gray-700">{{ $class->name }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">Belum ada kelas. Hubungi Admin untuk menambahkan.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        📚 Daftar Jurusan Tersedia
                        <span class="text-xs font-normal bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">View Only</span>
                    </h3>
                    <div class="max-h-36 overflow-y-auto space-y-1">
                        @forelse ($majors as $major)
                            <div class="bg-gray-50 rounded-lg px-3 py-2">
                                <span class="text-sm font-medium text-gray-700">{{ $major->name }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">Belum ada jurusan. Hubungi Admin untuk menambahkan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Tabel Antrian --}}
            <div>
                <h3 class="text-base font-semibold text-gray-800 mb-3">👥 Antrian Member Pending</h3>

                @if ($pendingMembers->isEmpty())
                    <div class="text-center py-16 text-gray-400 bg-white rounded-xl shadow">
                        <div class="text-6xl mb-4">📭</div>
                        <p class="text-lg">Tidak ada member yang menunggu persetujuan saat ini.</p>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Profil</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Daftar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($pendingMembers as $index => $member)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $member->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $member->email }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            @if ($member->kelas && $member->jurusan)
                                                <span class="text-green-700 font-medium">{{ $member->kelas }} · {{ $member->jurusan }}</span>
                                            @else
                                                <span class="text-gray-400 italic text-xs">Belum diisi</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $member->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-2">

                                                {{-- Tombol MURID → buka modal --}}
                                                <button type="button"
                                                    onclick="openStudentModal({{ $member->id }}, '{{ addslashes($member->name) }}', '{{ addslashes($member->kelas ?? '') }}', '{{ addslashes($member->jurusan ?? '') }}')"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 rounded-lg transition">
                                                    🎓 Murid
                                                </button>

                                                {{-- Tombol GURU --}}
                                                <form method="POST" action="{{ route('petugas.member.generate.code', $member) }}"
                                                    onsubmit="return confirm('Generate kode GURU untuk {{ addslashes($member->name) }}?')">
                                                    @csrf
                                                    <input type="hidden" name="role" value="teacher">
                                                    <button type="submit"
                                                        class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-3 py-2 rounded-lg transition">
                                                        👨‍🏫 Guru
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ====================================================
         MODAL: Data Murid Sebelum Generate Kode
         ==================================================== --}}
    <div id="studentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 class="text-white font-semibold text-lg">🎓 Data Murid</h3>
                    <p class="text-blue-100 text-xs mt-0.5">Isi data lengkap sebelum generate kode</p>
                </div>
                <button onclick="closeStudentModal()" class="text-white/80 hover:text-white text-2xl leading-none">&times;</button>
            </div>

            {{-- Form --}}
            <form id="studentModalForm" method="POST" action="" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="role" value="student">

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="modal_name" name="name"
                        placeholder="Masukkan nama lengkap"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none"
                        required>
                </div>

                {{-- Kelas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    @if ($classes->isEmpty())
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700 text-sm">
                            ⚠️ Belum ada kelas. Hubungi Admin untuk menambahkan kelas terlebih dahulu.
                        </div>
                        <input type="hidden" name="kelas" value="">
                    @else
                        <select id="modal_kelas" name="kelas"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none bg-white"
                            required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->name }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                {{-- Jurusan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jurusan <span class="text-red-500">*</span>
                    </label>
                    @if ($majors->isEmpty())
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700 text-sm">
                            ⚠️ Belum ada jurusan. Hubungi Admin untuk menambahkan jurusan terlebih dahulu.
                        </div>
                        <input type="hidden" name="jurusan" value="">
                    @else
                        <select id="modal_jurusan" name="jurusan"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none bg-white"
                            required>
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach ($majors as $major)
                                <option value="{{ $major->name }}">{{ $major->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                {{-- Info --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-xs text-blue-700">
                    ℹ️ Setelah menyimpan, kode aktivasi akan langsung di-generate dan user akan mendapat status <strong>approved</strong>.
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeStudentModal()"
                        class="flex-1 border border-gray-300 text-gray-700 text-sm py-2.5 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm py-2.5 rounded-lg transition font-medium">
                        ✅ Simpan & Generate Kode
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStudentModal(userId, name, kelas, jurusan) {
            const form = document.getElementById('studentModalForm');
            form.action = `/petugas/member-approval/${userId}/generate-code`;

            document.getElementById('modal_name').value = name;

            const kelasEl = document.getElementById('modal_kelas');
            if (kelasEl) {
                for (let opt of kelasEl.options) {
                    opt.selected = opt.value === kelas;
                }
            }

            const jurusanEl = document.getElementById('modal_jurusan');
            if (jurusanEl) {
                for (let opt of jurusanEl.options) {
                    opt.selected = opt.value === jurusan;
                }
            }

            const modal = document.getElementById('studentModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeStudentModal() {
            const modal = document.getElementById('studentModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Tutup modal jika klik di luar
        document.getElementById('studentModal').addEventListener('click', function(e) {
            if (e.target === this) closeStudentModal();
        });
    </script>
</x-app-layout>
