@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">Leave Detail</h1>
                <p class="text-gray-700/80 text-sm">{{ $cuti->nama_karyawan }}</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg max-w-2xl mx-auto p-6" style="border: 2px solid #e0eaff;">

            {{-- Profile Section --}}
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center border-2 border-blue-300">
                    @php
                        $initial = substr($cuti->nama_karyawan, 0, 1);
                    @endphp
                    <span class="text-blue-600 text-2xl font-bold">{{ strtoupper($initial) }}</span>
                </div>
                <div>
                    <h2 class="text-md font-semibold text-blue-900">{{ $cuti->nama_karyawan }}</h2>
                    <p class="text-sm text-gray-500">{{ $cuti->karyawan->role ?? 'Employee' }}</p>
                    <p class="text-xs text-gray-400">{{ $cuti->karyawan->email ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $cuti->karyawan->telepon ?? '-' }}</p>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-gray-400 text-sm">Start Date</p>
                    <p class="font-medium text-gray-700">{{ $cuti->tanggal_mulai->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">End Date</p>
                    <p class="font-medium text-gray-700">{{ $cuti->tanggal_selesai->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Leave Type</p>
                    <p class="font-medium text-gray-700">{{ $cuti->jenis_cuti_label }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Attachment</p>
                    @if ($cuti->lampiran)
                        <a href="{{ Storage::url($cuti->lampiran) }}" target="_blank"
                            class="text-blue-600 text-sm hover:underline">View File</a>
                    @else
                        <p class="text-gray-400 text-sm">-</p>
                    @endif
                </div>
            </div>

            {{-- Notes / Alasan --}}
            <div class="mb-6">
                <p class="text-sm text-gray-400 mb-1">Notes</p>
                <div class="bg-gray-100 text-gray-700 text-sm rounded-lg px-3 py-2 min-h-[60px]">
                    {{ $cuti->alasan }}
                </div>
            </div>

            {{-- Status --}}
            <div class="mb-6">
                <p class="text-sm text-gray-400 mb-1">Status</p>
                <div>
                    {!! $cuti->status_badge !!}
                </div>
            </div>

            {{-- Admin Notes (if any) --}}
            @if ($cuti->catatan)
                <div class="mb-6">
                    <p class="text-sm text-gray-400 mb-1">Admin Notes</p>
                    <div class="bg-yellow-50 text-yellow-800 text-sm rounded-lg px-3 py-2 border-l-4 border-yellow-500">
                        {{ $cuti->catatan }}
                    </div>
                </div>
            @endif

            {{-- Action Buttons for Admin --}}
            @if ($cuti->status == 'pending')
                <div class="flex gap-3 mt-4 pt-4 border-t">
                    <form action="{{ route('admin.leave.update-status', $cuti->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="disetujui">
                        <button type="submit" onclick="return confirm('Setujui pengajuan cuti ini?')"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition">
                            ✅ Approve
                        </button>
                    </form>
                    <button onclick="openRejectModal()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition">
                        ❌ Reject
                    </button>
                    <a href="{{ route('admin.leave.index') }}"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm font-semibold transition">
                        Back
                    </a>
                </div>
            @else
                <div class="flex gap-3 mt-4 pt-4 border-t">
                    <a href="{{ route('admin.leave.index') }}"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm font-semibold transition">
                        Back
                    </a>
                </div>
            @endif
        </div>

        {{-- Modal Reject --}}
        <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-blue-900">Reject Leave Request</h3>
                    <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form action="{{ route('admin.leave.update-status', $cuti->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="ditolak">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Rejection Notes</label>
                        <textarea name="catatan" rows="3" class="w-full border rounded-lg px-3 py-2"
                            placeholder="Berikan alasan penolakan..." required></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeRejectModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancel</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg">Reject</button>
                    </div>
                </form>
            </div>
        </div>

        <style>
            #rejectModal.show {
                display: flex;
            }

            #rejectModal {
                display: none;
            }
        </style>

        <script>
            function openRejectModal() {
                document.getElementById('rejectModal').classList.add('show');
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.remove('show');
            }
        </script>
    </div>
@endsection
