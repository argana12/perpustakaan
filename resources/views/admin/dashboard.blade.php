<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Administrator Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8">
                <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
                <p class="mt-2 text-md text-gray-600">Pusat kendali utama untuk persetujuan akun, manajemen OTP, dan keamanan perpustakaan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Card 1: Antrian Persetujuan CRM -->
                <a href="{{ route('admin.pending.users') }}" class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-transparent hover:border-indigo-100 overflow-hidden flex flex-col">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-white opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="p-8 relative z-10 flex flex-col flex-grow">
                        <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300">
                            <span class="text-3xl">👥</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-indigo-700 transition-colors">Antrian Persetujuan</h3>
                        <p class="text-gray-500 mb-6 flex-grow">Kelola antrian pendaftar (Murid, Guru, Petugas). Di sini Anda dapat membuka akses sistem dan mencetak Kode Aktivasi 1-to-User yang aman.</p>
                        <div class="flex items-center text-indigo-600 font-semibold text-sm">
                            Buka Antrian <span class="ml-2 group-hover:translate-x-2 transition-transform">→</span>
                        </div>
                    </div>
                </a>

                <!-- Card 2: Manajemen OTP & Suspend -->
                <a href="{{ route('admin.otp.index') }}" class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-transparent hover:border-rose-100 overflow-hidden flex flex-col">
                    <div class="absolute inset-0 bg-gradient-to-br from-rose-50 to-white opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="p-8 relative z-10 flex flex-col flex-grow">
                        <div class="w-14 h-14 bg-rose-100 rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300">
                            <span class="text-3xl">🛡️</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-rose-700 transition-colors">Manajemen Keamanan OTP</h3>
                        <p class="text-gray-500 mb-6 flex-grow">Pantau percobaan limit login/OTP seluruh user. Buka blokir (Unlock) bagi user yang ter-suspend karena percobaan gagal kode berturut-turut.</p>
                        <div class="flex items-center text-rose-600 font-semibold text-sm">
                            Buka Keamanan <span class="ml-2 group-hover:translate-x-2 transition-transform">→</span>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
