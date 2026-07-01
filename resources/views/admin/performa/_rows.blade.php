@forelse($performances as $item)
    <tr class="transition border-t border-gray-100 hover:bg-blue-50/40">
        <td class="py-3 pl-2">
            <div class="flex items-center gap-3">
                @php $initial = substr($item->info->name, 0, 1); @endphp
                <div
                    class="flex items-center justify-center overflow-hidden bg-blue-100 border-2 border-blue-300 rounded-full w-9 h-9">
                    @if (isset($item->info->foto_profil) && $item->info->foto_profil)
                        <img src="{{ Storage::url($item->info->foto_profil) }}" alt="profile"
                            class="object-cover rounded-full w-9 h-9">
                    @else
                        <span class="text-sm font-bold text-blue-500">{{ strtoupper($initial) }}</span>
                    @endif
                </div>
                <div>
                    <span class="font-medium text-gray-800">{{ $item->info->name }}</span>
                    <br>
                    <small class="text-gray-400 capitalize">{{ $item->info->role ?? '-' }}</small>
                </div>
            </div>
        </td>
        <td class="py-3 text-gray-600 whitespace-nowrap">
            @php
                $monthNames = [
                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                    5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
                    9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
                ];
            @endphp
            {{ $monthNames[$item->bulan] ?? '-' }} {{ $item->tahun }}
        </td>
        <td class="py-3">
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#0052CC] opacity-40 flex-shrink-0" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z" />
                </svg>
                <span class="font-medium text-gray-700">{{ $item->task_done }}</span>
                <span class="text-xs text-gray-400">tasks</span>
            </div>
            <span class="text-[10px] bg-gray-100 text-gray-400 px-1.5 py-0.5 rounded mt-0.5 inline-block">preview</span>
        </td>
        <td class="py-3 text-gray-700">{{ $item->attendance_summary->attendance_rate }}%</td>
        <td class="py-3 font-semibold text-gray-700">{{ $item->kpi->kpi_score }}</td>
        <td class="py-3">
            <span class="text-base font-bold text-purple-700">{{ $item->performance_score }}</span>
            <span class="text-xs text-gray-400">/ 100</span>
        </td>
        <td class="py-3">
            <span class="px-3 py-1 rounded-full text-xs font-semibold
                @if ($item->status_performance == 'Excellent') bg-green-100 text-green-700
                @elseif($item->status_performance == 'Good') bg-blue-100 text-blue-700
                @elseif($item->status_performance == 'Average') bg-yellow-100 text-yellow-700
                @elseif($item->status_performance == 'Poor') bg-orange-100 text-orange-700
                @elseif($item->status_performance == 'Very Poor') bg-red-100 text-red-700
                @else bg-gray-100 text-gray-700 @endif
            ">
                {{ $item->status_performance }}
            </span>
        </td>
        <td class="py-3">
            <div class="flex items-center gap-2">
                @if ($item->id)
                    <button onclick="openDetailModal({{ $item->karyawan_id }})"
                        class="text-blue-500 hover:text-blue-700" title="Detail">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    <a href="{{ route('admin.performa.edit', $item->id) }}"
                        class="text-yellow-500 hover:text-yellow-700" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </a>
                    <form action="{{ route('admin.performa.destroy', $item->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('Hapus penilaian ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700" title="Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                @else
                    <span class="text-xs text-gray-400">No data</span>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="py-12 text-center">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-500">No Performance Data Yet</h3>
                <p class="text-sm text-gray-400">Start by adding your first employee performance assessment.</p>
                <div class="flex gap-2 mt-2">
                    <a href="{{ route('admin.performa.create') }}"
                        class="px-4 py-2 text-sm text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">+
                        Add Assessment</a>
                    <a href="{{ route('admin.performa.bulk') }}"
                        class="px-4 py-2 text-sm text-white transition bg-green-600 rounded-lg hover:bg-green-700">Bulk
                        Assessment</a>
                </div>
            </div>
        </td>
    </tr>
@endforelse
