@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">Edit Leave Request</h1>
                <p class="text-gray-700/80 text-sm">Update your leave request data</p>
            </div>
            <!-- IMAGE / ILLUSTRATION -->
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6" style="border: 2px solid #e0eaff;">
            <form method="POST" action="{{ route('cuti.update', $cuti->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Leave Type *</label>
                        <select name="jenis_cuti" required class="w-full border rounded-lg px-3 py-2">
                            <option value="tahunan"
                                {{ old('jenis_cuti', $cuti->jenis_cuti) == 'tahunan' ? 'selected' : '' }}>Annual Leave
                                (Sisa: {{ $sisaKuota }} hari)</option>
                            <option value="sakit" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'sakit' ? 'selected' : '' }}>
                                Cuti Sakit</option>
                            <option value="melahirkan"
                                {{ old('jenis_cuti', $cuti->jenis_cuti) == 'melahirkan' ? 'selected' : '' }}>Maternity Leave
                            </option>
                            <option value="penting"
                                {{ old('jenis_cuti', $cuti->jenis_cuti) == 'penting' ? 'selected' : '' }}>Personal Leave
                            </option>
                            <option value="ibadah" {{ old('jenis_cuti', $cuti->jenis_cuti) == 'ibadah' ? 'selected' : '' }}>
                                Cuti Ibadah</option>
                            <option value="lainnya"
                                {{ old('jenis_cuti', $cuti->jenis_cuti) == 'lainnya' ? 'selected' : '' }}>Other Leave
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Start Date *</label>
                        <input type="date" name="tanggal_mulai"
                            value="{{ old('tanggal_mulai', $cuti->tanggal_mulai->format('Y-m-d')) }}" required
                            class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">End Date *</label>
                        <input type="date" name="tanggal_selesai"
                            value="{{ old('tanggal_selesai', $cuti->tanggal_selesai->format('Y-m-d')) }}" required
                            class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Reason *</label>
                        <textarea name="alasan" rows="4" required class="w-full border rounded-lg px-3 py-2">{{ old('alasan', $cuti->alasan) }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Attachment</label>
                        @if ($cuti->lampiran)
                            <div class="mb-2">
                                <a href="{{ Storage::url($cuti->lampiran) }}" target="_blank"
                                    class="text-blue-600 hover:text-blue-800 text-sm">📎 Current Attachment</a>
                            </div>
                        @endif
                        <input type="file" name="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            class="w-full border rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-500 mt-1">Leave blank if you don't want to change the attachment</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <a href="{{ route('cuti.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">Cancel</a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
