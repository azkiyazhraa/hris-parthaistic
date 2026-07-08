@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">
                    Notifications
                    @if ($unreadCount > 0)
                        <span class="ml-2 align-middle text-xs bg-red-500 text-white rounded-full px-2 py-1">{{ $unreadCount }} unread</span>
                    @endif
                </h1>
                <p class="text-sm text-gray-700/80">All your notifications in one place.</p>
            </div>

            @if ($unreadCount > 0)
                <form action="{{ route('notifikasi.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <div class="p-6 bg-white shadow-lg rounded-2xl">
            <div class="space-y-3">
                @forelse ($notifikasi as $item)
                    <div class="flex items-start justify-between gap-4 p-4 border border-gray-100 rounded-xl {{ !$item->status ? 'bg-blue-50' : '' }}">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                @if (!$item->status)
                                    <span class="w-2 h-2 bg-blue-500 rounded-full shrink-0"></span>
                                @endif
                                <h3 class="font-semibold text-gray-800">{{ $item->judul }}</h3>
                                <span class="text-xs text-gray-400">{{ $item->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $item->pesan }}</p>
                            <span class="inline-block mt-2 px-2 py-1 text-xs text-gray-500 bg-gray-100 rounded">
                                {{ match($item->tipe_notifikasi) {
                                    'cuti'       => 'LEAVE',
                                    'absensi'    => 'ATTENDANCE',
                                    'change_day' => 'CHANGE DAY',
                                    'pengumuman' => 'ANNOUNCEMENT',
                                    'performa'   => 'PERFORMANCE',
                                    'penggajian' => 'PAYROLL',
                                    default      => strtoupper($item->tipe_notifikasi),
                                } }}
                            </span>
                        </div>

                        @if (!$item->status)
                            <button onclick="markAsRead({{ $item->id }})"
                                class="text-sm text-blue-600 hover:text-blue-800 shrink-0">
                                Mark as read
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400">
                        <p class="text-sm">No notifications yet.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $notifikasi->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function markAsRead(id) {
            fetch(`/notifikasi/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
        }
    </script>
@endpush
