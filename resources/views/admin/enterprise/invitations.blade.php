<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                ✉️ {{ __('Undangan Pengguna') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Enterprise'],
                    ['label' => 'Undangan'],
                ]" />

                {{-- Invite Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Kirim Undangan Baru</h3>
                    <form x-data="{ sending: false }" @submit.prevent="
                        sending = true;
                        fetch('{{ route('enterprise.invite') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                email: $refs.email.value,
                                role: $refs.role.value
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            sending = false;
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Gagal mengirim undangan.');
                            }
                        })
                        .catch(err => {
                            sending = false;
                            alert('Terjadi kesalahan.');
                        })
                    ">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input
                                    x-ref="email"
                                    type="email"
                                    required
                                    placeholder="user@example.com"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Peran</label>
                                <select
                                    x-ref="role"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm"
                                >
                                    <option value="parent">Orang Tua (Parent)</option>
                                    <option value="tenant_admin">Admin Tenant</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button
                                    type="submit"
                                    :disabled="sending"
                                    class="w-full min-h-[44px] px-4 py-2 bg-gradient-to-r from-skyBlue-500 to-mintGreen-500 text-white rounded-xl font-medium text-sm hover:from-skyBlue-600 hover:to-mintGreen-600 transition-all disabled:opacity-50"
                                >
                                    <span x-show="!sending">✉️ Kirim Undangan</span>
                                    <span x-show="sending">Mengirim...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Invitations List --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Undangan Aktif</h3>

                    @if ($invitations->isEmpty())
                        <div class="text-center py-8">
                            <div class="text-4xl mb-3">📭</div>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada undangan aktif.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Email</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Peran</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Dikirim Oleh</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Kedaluwarsa</th>
                                        <th class="text-right py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invitations as $invitation)
                                        <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="py-3 px-4 font-medium text-gray-800 dark:text-gray-100">{{ $invitation->email }}</td>
                                            <td class="py-3 px-4">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium {{ $invitation->role === 'tenant_admin' ? 'bg-lavender-100 dark:bg-lavender-950/30 text-lavender-700 dark:text-lavender-400' : 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400' }}">
                                                    {{ $invitation->role === 'tenant_admin' ? 'Admin Tenant' : 'Orang Tua' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $invitation->invitedBy?->name ?? '—' }}</td>
                                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $invitation->expires_at->format('d M Y H:i') }}</td>
                                            <td class="py-3 px-4 text-right">
                                                <button
                                                    x-data="{ revoking: false }"
                                                    @click="
                                                        if (confirm('Batalkan undangan ini?')) {
                                                            revoking = true;
                                                            fetch('{{ route('enterprise.revoke-invitation', $invitation) }}', {
                                                                method: 'DELETE',
                                                                headers: {
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                    'Accept': 'application/json'
                                                                }
                                                            })
                                                            .then(r => r.json())
                                                            .then(data => {
                                                                if (data.success) window.location.reload();
                                                            });
                                                        }
                                                    "
                                                    :disabled="revoking"
                                                    class="min-h-[44px] px-3 py-1 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/30 rounded-lg hover:bg-red-100 dark:hover:bg-red-950/50 transition-colors disabled:opacity-50"
                                                >
                                                    Batalkan
                                                </button>
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
    </div>
</x-app-layout>
