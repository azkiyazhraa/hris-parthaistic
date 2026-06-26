@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">

        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg">
            <h1 class="text-2xl font-bold text-blue-900 mb-1">Edit Announcement</h1>
            <p class="text-gray-700/80 text-sm">Update the announcement details below.</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-6" style="border: 2px solid #e0eaff;">
            <form method="POST" action="{{ route('admin.pengumuman.update', $pengumuman->id) }}"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul', $pengumuman->judul) }}" required
                            placeholder="Enter the title of the announcement..."
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('judul')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content <span class="text-red-500">*</span></label>
                        <textarea name="konten" rows="8" required placeholder="Write the announcement content here..."
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('konten', $pengumuman->konten) }}</textarea>
                        @error('konten')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="kategori"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Category</option>
                            <option value="umum" {{ old('kategori', $pengumuman->kategori) == 'umum' ? 'selected' : '' }}>General</option>
                            <option value="kebijakan" {{ old('kategori', $pengumuman->kategori) == 'kebijakan' ? 'selected' : '' }}>Policy</option>
                            <option value="pengumuman" {{ old('kategori', $pengumuman->kategori) == 'pengumuman' ? 'selected' : '' }}>Announcement</option>
                            <option value="event" {{ old('kategori', $pengumuman->kategori) == 'event' ? 'selected' : '' }}>Event</option>
                            <option value="penting" {{ old('kategori', $pengumuman->kategori) == 'penting' ? 'selected' : '' }}>Important</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Role</label>
                        <select name="target_role"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all" {{ old('target_role', $pengumuman->target_role) == 'all' ? 'selected' : '' }}>All Roles</option>
                            <option value="admin" {{ old('target_role', $pengumuman->target_role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="hr" {{ old('target_role', $pengumuman->target_role) == 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="karyawan" {{ old('target_role', $pengumuman->target_role) == 'karyawan' ? 'selected' : '' }}>Employee</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Publish Date</label>
                        <input type="datetime-local" name="tanggal_terbit"
                            value="{{ old('tanggal_terbit', $pengumuman->tanggal_terbit ? $pengumuman->tanggal_terbit->format('Y-m-d\TH:i') : '') }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep the current publish date</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                        <input type="date" name="tanggal_berlaku_hingga"
                            value="{{ old('tanggal_berlaku_hingga', $pengumuman->tanggal_berlaku_hingga ? $pengumuman->tanggal_berlaku_hingga->format('Y-m-d') : '') }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment</label>
                        @if ($pengumuman->lampiran)
                            <div class="mb-2">
                                <a href="{{ Storage::url($pengumuman->lampiran) }}" target="_blank"
                                    class="text-blue-600 hover:text-blue-800 text-sm">View current attachment</a>
                            </div>
                        @endif
                        <input type="file" name="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            class="w-full rounded-xl border border-dashed border-gray-300 bg-white px-4 py-3 text-sm
                                file:mr-4 file:rounded-lg file:border-0 file:bg-blue-100 file:px-4 file:py-2
                                file:text-blue-700 hover:file:bg-blue-200">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep the current attachment • Max 5MB</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="status" value="1" id="status"
                            {{ old('status', $pengumuman->status) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 rounded">
                        <label for="status" class="text-sm font-medium text-gray-700">Publish</label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.pengumuman.index') }}"
                        class="px-4 py-2 text-sm rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 text-sm rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        Update Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
