<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Leave Request Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 gap-4 mb-6 md:grid-cols-5">
                <div class="p-4 bg-white rounded-lg shadow">
                    <div class="text-sm text-gray-500">Total Requests</div>
                    <div class="text-2xl font-bold">{{ $statistics['total'] }}</div>
                </div>
                <div class="p-4 rounded-lg shadow bg-yellow-50">
                    <div class="text-sm text-yellow-600">Pending</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $statistics['pending'] }}</div>
                </div>
                <div class="p-4 rounded-lg shadow bg-green-50">
                    <div class="text-sm text-green-600">Approved</div>
                    <div class="text-2xl font-bold text-green-600">{{ $statistics['disetujui'] }}</div>
                </div>
                <div class="p-4 rounded-lg shadow bg-red-50">
                    <div class="text-sm text-red-600">Rejected</div>
                    <div class="text-2xl font-bold text-red-600">{{ $statistics['ditolak'] }}</div>
                </div>
                <div class="p-4 rounded-lg shadow bg-blue-50">
                    <div class="text-sm text-blue-600">Total Days</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_hari'] }}</div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="text-sm leading-normal text-gray-600 uppercase bg-gray-200">
                                    <th class="px-6 py-3 text-left">Employee Name</th>
                                    <th class="px-6 py-3 text-left">Leave Type</th>
                                    <th class="px-6 py-3 text-left">Date</th>
                                    <th class="px-6 py-3 text-left">Total Days</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                    <th class="px-6 py-3 text-left">Reason</th>
                                    <th class="px-6 py-3 text-left">Notes</th>
                                    <th class="px-6 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm font-light text-gray-600">
                                @forelse($cuti as $item)
                                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="px-6 py-3 text-left">
                                            {{ $item->nama_karyawan }}<br>
                                            <small class="text-gray-500">{{ $item->karyawan->nip ?? '-' }}</small>
                                        </td>
                                        <td class="px-6 py-3 text-left">{{ $item->jenis_cuti_label }}</td>
                                        <td class="px-6 py-3 text-left">{{ $item->tanggal_mulai->format('d/m/Y') }} -
                                            {{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                                        <td class="px-6 py-3 text-left">{{ $item->total_hari }} hari</td>
                                        <td class="px-6 py-3 text-left">
                                            <form action="{{ route('admin.cuti.update-status', $item->id) }}"
                                                method="POST" class="inline-block"
                                                id="status-form-{{ $item->id }}">
                                                @csrf
                                                @method('PUT')
                                                <select name="status"
                                                    onchange="document.getElementById('status-form-{{ $item->id }}').submit()"
                                                    class="text-xs rounded-full py-1 px-3 border-0 focus:ring-2 focus:ring-blue-500
                                                @if ($item->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($item->status == 'disetujui') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                                    <option value="pending"
                                                        {{ $item->status == 'pending' ? 'selected' : '' }}>Pending
                                                    </option>
                                                    <option value="disetujui"
                                                        {{ $item->status == 'disetujui' ? 'selected' : '' }}>Approved
                                                    </option>
                                                    <option value="ditolak"
                                                        {{ $item->status == 'ditolak' ? 'selected' : '' }}>Rejected
                                                    </option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-3 text-left">{{ Str::limit($item->alasan, 50) }}</td>
                                        <td class="px-6 py-3 text-left">
                                            @if ($item->catatan)
                                                <button onclick="showCatatan('{{ addslashes($item->catatan) }}')"
                                                    class="text-xs text-blue-600 hover:text-blue-800">
                                                    View Notes
                                                </button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <button onclick="showDetail({{ $item->id }})"
                                                class="px-3 py-1 text-xs font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                                Detail & Update
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-4 text-center">No Leave Requests Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $cuti->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
        <div class="relative w-full max-w-2xl p-5 mx-auto bg-white border rounded-md shadow-lg top-20">
            <div class="mt-3">
                <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900">Leave Request Detail</h3>
                <div id="detailContent"></div>
                <div class="mt-4">
                    <form id="catatanForm" method="POST">
                        @csrf
                        @method('PUT')
                        <label class="block mb-2 text-sm font-bold text-gray-700">Notes (Optional)</label>
                        <textarea name="catatan" id="catatanText" rows="3"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"></textarea>
                        <div class="mt-2">
                            <label class="block mb-2 text-sm font-bold text-gray-700">Update Status</label>
                            <select name="status" id="statusSelect"
                                class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                                <option value="pending">Pending</option>
                                <option value="disetujui">Approved</option>
                                <option value="ditolak">Rejected</option>
                            </select>
                        </div>
                        <div class="flex justify-end mt-4 space-x-2">
                            <button type="button" onclick="closeDetailModal()"
                                class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-700">
                                Close
                            </button>
                            <button type="submit"
                                class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                Save Notes & Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Catatan -->
    <div id="catatanModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
        <div class="relative p-5 mx-auto bg-white border rounded-md shadow-lg top-20 w-96">
            <div class="mt-3">
                <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900">Notes</h3>
                <div id="catatanContent" class="text-gray-600"></div>
                <div class="flex justify-end mt-4">
                    <button type="button" onclick="closeCatatanModal()"
                        class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentCutiId = null;

        function showDetail(id) {
            currentCutiId = id;
            fetch(`/admin/cuti/${id}`)
                .then(response => response.json())
                .then(data => {
                    const content = `
                        <div class="mb-4 space-y-2">
                            <p><strong>Nama Karyawan:</strong> ${data.nama_karyawan}</p>
                            <p><strong>NIP:</strong> ${data.karyawan?.nip || '-'}</p>
                            <p><strong>Jenis Cuti:</strong> ${data.jenis_cuti_label}</p>
                            <p><strong>Tanggal:</strong> ${new Date(data.tanggal_mulai).toLocaleDateString('id-ID')} - ${new Date(data.tanggal_selesai).toLocaleDateString('id-ID')}</p>
                            <p><strong>Total Hari:</strong> ${data.total_hari} hari</p>
                            <p><strong>Alasan:</strong> ${data.alasan? data.alasan.replace(/\n/g, '<br>'): '-'}</p>
                            ${data.lampiran ? `<p><strong>Lampiran:</strong> <a href="/storage/${data.lampiran}" target="_blank" class="text-blue-600 hover:text-blue-800">Lihat Lampiran</a></p>` : ''}
                            ${data.catatan ? `
                                    <p>
                                        <strong>Catatan Sebelumnya:</strong><br>
                                        ${data.catatan.replace(/\n/g, '<br>')}
                                    </p>
                                ` : ''}
                        </div>
                    `;
                    document.getElementById('detailContent').innerHTML = content;
                    document.getElementById('catatanText').value = data.catatan || '';
                    document.getElementById('statusSelect').value = data.status;
                    document.getElementById('catatanForm').action = `/admin/cuti/${id}/status`;
                    document.getElementById('detailModal').classList.remove('hidden');
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            currentCutiId = null;
        }

        function showCatatan(catatan) {
            document.getElementById('catatanContent').innerHTML = catatan;
            document.getElementById('catatanModal').classList.remove('hidden');
        }

        function closeCatatanModal() {
            document.getElementById('catatanModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
