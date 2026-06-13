@extends('layouts.app')
@section('content')
<div class="container mx-auto py-4 space-y-4">
    <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-900 mb-1">Detail Performance</h1>
            <p class="text-gray-700/80 text-sm">{{ $performa->bulan_text }} {{ $performa->tahun }}</p>
        </div>
        <div class="hidden md:block">
            <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg max-w-2xl mx-auto p-6" style="border: 2px solid #e0eaff;">
        {{-- Employee Info --}}
        <div class="flex items-center gap-4 mb-6">
            @php 
                $initial = substr($performa->nama_karyawan, 0, 1);
                $karyawan = $performa->karyawan;
            @endphp
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center border-2 border-blue-300 overflow-hidden">
                @if($karyawan && $karyawan->foto_profil)
                    <img src="{{ Storage::url($karyawan->foto_profil) }}" alt="profile" class="w-16 h-16 rounded-full object-cover">
                @else
                    <span class="text-blue-600 text-2xl font-bold">{{ strtoupper($initial) }}</span>
                @endif
            </div>
            <div>
                <h2 class="text-md font-semibold text-blue-900">{{ $performa->nama_karyawan }}</h2>
                <p class="text-sm text-gray-500">{{ $performa->departemen }} - {{ $performa->position }}</p>
                <p class="text-xs text-gray-400">{{ $performa->email }}</p>
            </div>
        </div>

        {{-- Attendance Summary --}}
        @php
            $presentCount = \App\Models\Performa::calculatePresentCount($performa->karyawan_id, $performa->bulan, $performa->tahun);
            $absentCount = \App\Models\Performa::calculateAbsentCount($performa->karyawan_id, $performa->bulan, $performa->tahun);
            $totalWorkingDays = \App\Models\Performa::getRelevantWorkingDays($karyawan, $performa->bulan, $performa->tahun);
        @endphp

        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Attendance Summary - {{ $performa->bulan_text }} {{ $performa->tahun }}</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Total Working Days</p>
                    <p class="text-xl font-bold text-blue-900">{{ $totalWorkingDays }} Days</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Attendance Rate</p>
                    <p class="text-xl font-bold text-blue-900">{{ $performa->attendance_rate }}%</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-4">
                <div class="bg-white rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Present</p>
                    <p class="text-lg font-bold text-green-600">{{ $presentCount }} Days</p>
                </div>

                <div class="bg-white rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Absent</p>
                    <p class="text-lg font-bold text-red-600">{{ $absentCount }} Days</p>
                </div>
            </div>
        </div>

        {{-- KPI Components --}}
        <div class="space-y-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">KPI Components</h3>
            @php
                $kpiFields = [
                    'quality' => ['label' => 'Quality', 'color' => 'green'],
                    'productivity' => ['label' => 'Productivity', 'color' => 'yellow'],
                    'teamwork' => ['label' => 'Teamwork', 'color' => 'purple'],
                    'discipline' => ['label' => 'Discipline', 'color' => 'indigo'],
                ];
            @endphp
            @foreach($kpiFields as $field => $config)
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-gray-700 font-medium">{{ $config['label'] }}</span>
                    <span class="text-gray-600">{{ $performa->$field }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-{{ $config['color'] }}-600 rounded-full h-2" style="width: {{ $performa->$field }}%"></div>
                </div>
            </div>
            @endforeach
            
            <div class="pt-4 border-t">
                <div class="flex justify-between mb-1">
                    <span class="text-gray-700 font-bold">KPI Score</span>
                    <span class="text-blue-600 font-semibold">{{ $performa->kpi_score }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 rounded-full h-2" style="width: {{ $performa->kpi_score }}%"></div>
                </div>
            </div>
        </div>

        {{-- Total Score --}}
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 mb-6 text-center">
            <p class="text-white text-sm">Total Performance Score</p>
            <p class="text-white text-5xl font-bold">{{ $performa->performance_score }}</p>
            <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white">
                {{ $performa->rating['label'] }}
            </span>
        </div>

        {{-- Notes --}}
        @if($performa->catatan)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Admin Notes</h3>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                <p class="text-gray-700">{{ $performa->catatan }}</p>
            </div>
        </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex justify-end space-x-2 pt-4 border-t">
            <a href="{{ route('performa.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                Back
            </a>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                Print Report
            </button>
        </div>
    </div>
</div>
@endsection