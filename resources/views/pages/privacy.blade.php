<x-pages-layout :page-title="'Kebijakan Privasi'">
    <p class="text-sm text-gray-400 mb-8">Terakhir diperbarui: {{ date('d F Y') }}</p>

    <div class="space-y-6">
        <div class="p-5 bg-skyBlue-50 rounded-2xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="flex items-center justify-center w-7 h-7 bg-skyBlue-500 text-white text-xs font-bold rounded-full">1</span>
                <h2 class="text-base font-bold text-gray-800">Pengumpulan Informasi</h2>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
                ForMysha mengumpulkan informasi yang Anda berikan secara langsung saat membuat akun, termasuk nama, alamat email, dan data profil anak. Kami juga mengumpulkan data yang Anda unggah seperti foto, video, dokumen, dan catatan.
            </p>
        </div>

        <div class="p-5 bg-mintGreen-50 rounded-2xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="flex items-center justify-center w-7 h-7 bg-mintGreen-500 text-white text-xs font-bold rounded-full">2</span>
                <h2 class="text-base font-bold text-gray-800">Penggunaan Informasi</h2>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
                Informasi yang dikumpulkan digunakan untuk menyediakan dan meningkatkan layanan ForMysha, termasuk penyimpanan data, personalisasi pengalaman, dan komunikasi terkait akun Anda.
            </p>
        </div>

        <div class="p-5 bg-softPink-50 rounded-2xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="flex items-center justify-center w-7 h-7 bg-softPink-500 text-white text-xs font-bold rounded-full">3</span>
                <h2 class="text-base font-bold text-gray-800">Keamanan Data</h2>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
                Kami menerapkan langkah-langkah keamanan teknis dan organisasi untuk melindungi data Anda dari akses tidak sah, kerusakan, atau kehilangan. Data dienkripsi saat transit dan saat disimpan.
            </p>
        </div>

        <div class="p-5 bg-lavender-50 rounded-2xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="flex items-center justify-center w-7 h-7 bg-lavender-500 text-white text-xs font-bold rounded-full">4</span>
                <h2 class="text-base font-bold text-gray-800">Berbagi Data</h2>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
                Kami tidak menjual atau membagikan data pribadi Anda kepada pihak ketiga untuk tujuan pemasaran. Data hanya dibagikan sesuai pengaturan privasi yang Anda tentukan, misalnya dengan anggota keluarga yang diundang.
            </p>
        </div>

        <div class="p-5 bg-warmYellow-50 rounded-2xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="flex items-center justify-center w-7 h-7 bg-warmYellow-500 text-white text-xs font-bold rounded-full">5</span>
                <h2 class="text-base font-bold text-gray-800">Hak Anda</h2>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
                Anda memiliki hak untuk mengakses, memperbarui, menghapus, dan mengekspor data Anda kapan saja. Hubungi kami jika Anda memiliki pertanyaan terkait privasi data Anda.
            </p>
        </div>
    </div>

    {{-- Hubungi Kami --}}
    <div class="mt-10 p-6 bg-gray-50 rounded-2xl">
        <h2 class="text-lg font-bold text-gray-800 mb-2 text-center">Pertanyaan Tentang Privasi?</h2>
        <p class="text-sm text-gray-500 text-center mb-5">Jika Anda memiliki pertanyaan mengenai kebijakan privasi kami, silakan hubungi kami.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="mailto:info@formysha.my.id" class="flex items-center gap-2 px-5 py-3 bg-white rounded-xl border border-gray-200 hover:border-softPink-300 hover:shadow-sm transition-all w-full sm:w-auto justify-center no-underline">
                <svg class="w-4 h-4 text-softPink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span class="text-sm font-medium text-gray-700">info@formysha.my.id</span>
            </a>
            <a href="https://wa.me/6281529211963?text=Halo%20ForMysha%2C%20saya%20ingin%20bertanya%20tentang%20kebijakan%20privasi" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-5 py-3 bg-[#25D366] hover:bg-[#20BD5A] text-white rounded-xl hover:shadow-sm transition-all w-full sm:w-auto justify-center no-underline">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span class="text-sm font-medium">Chat WhatsApp</span>
            </a>
        </div>
    </div>
</x-pages-layout>
