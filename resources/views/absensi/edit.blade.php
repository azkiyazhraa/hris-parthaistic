@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">

        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">
                    Edit Change Day Request 
                </h1>
                <p class="text-gray-700/80 text-sm">
                    Edit your change day request
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('absensi.update', $absensi->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Change Day Start Date*</label>
                            <input type="date" name="change_day_tanggal_awal" value="{{ old('change_day_tanggal_awal', $absensi->change_day_tanggal_awal ? $absensi->change_day_tanggal_awal->format('Y-m-d') : '') }}" required 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('change_day_tanggal_awal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Change Day End Date*</label>
                            <input type="date" name="change_day_tanggal_akhir" value="{{ old('change_day_tanggal_akhir', $absensi->change_day_tanggal_akhir ? $absensi->change_day_tanggal_akhir->format('Y-m-d') : '') }}" required 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('change_day_tanggal_akhir')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Start Time *</label>
                            <input type="time" name="change_day_jam_mulai" value="{{ old('change_day_jam_mulai', $absensi->change_day_jam_mulai ? substr($absensi->change_day_jam_mulai, 0, 5) : '') }}" required 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('change_day_jam_mulai')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">End Time *</label>
                            <input type="time" name="change_day_jam_selesai" value="{{ old('change_day_jam_selesai', $absensi->change_day_jam_selesai ? substr($absensi->change_day_jam_selesai, 0, 5) : '') }}" required 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('change_day_jam_selesai')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Change Day Reason *</label>
                            <textarea name="change_day_alasan" rows="4" required 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('change_day_alasan', $absensi->change_day_alasan) }}</textarea>
                            @error('change_day_alasan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Attachment (Optional)</label>
                            @if ($absensi->attachment && \Illuminate\Support\Facades\Storage::disk('public')->exists($absensi->attachment))
                                <p class="text-sm text-gray-600 mb-2">
                                    File saat ini:
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($absensi->attachment) }}"
                                        target="_blank" class="text-blue-600 hover:underline">Lihat File</a>
                                </p>
                            @endif
                            <input type="file" name="attachment" accept="image/jpeg,image/png,image/jpg"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <p class="text-xs text-gray-500 mt-1">Leave blank if not replacing attachment (Max 2MB, JPG/PNG)</p>
                            @error('attachment')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Additional Notes (Optional)</label>
                            <textarea name="keterangan" rows="3" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('keterangan', $absensi->keterangan) }}</textarea>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mt-4">
                        <p class="text-yellow-700 text-sm">
                            <strong>Information:</strong> The change day request will replace your work schedule on the selected date. Work hour changes will be recalculated
                            automatically after approval by HR or Admin
                        </p>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <a href="{{ route('absensi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection