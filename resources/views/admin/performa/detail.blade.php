<div id="detail-modal-{{ $item->id }}" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50">

    <div class="flex items-start justify-center min-h-screen p-4">

        <div class="relative w-full">

            <!-- MODAL CARD -->
            <div class="bg-white rounded-xl shadow-lg max-h-[90vh] overflow-y-auto">

                <!-- HEADER -->
                <div class="sticky top-0 bg-white z-10 flex justify-between items-center border-b p-6">
                    <h3 class="text-lg font-semibold text-blue-900">
                        Detail Performance - {{ $item->info->name }}
                    </h3>

                    <button data-modal-hide="detail-modal-{{ $item->id }}"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">
                        ✕
                    </button>
                </div>

                <!-- TAB NAVIGATION -->
                <div class="mb-4 border-b border-gray-200">
                    <ul class="flex flex-wrap text-sm font-medium text-center" id="default-tab-{{ $item->id }}"
                        data-tabs-toggle="#default-tab-content-{{ $item->id }}" role="tablist">

                        <!-- EMPLOYEE SCOREING -->
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-4 border-b-2 border-blue-600 text-blue-600 rounded-t-lg active"
                                id="profile-tab-{{ $item->id }}" data-tabs-target="#profile-{{ $item->id }}"
                                type="button" role="tab" aria-controls="profile-{{ $item->id }}"
                                aria-selected="true">
                                Employee Scoring
                            </button>
                        </li>

                        <!-- TASK TRACKER FROM TRELLO -->
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-blue-600 hover:border-blue-300"
                                id="dashboard-tab-{{ $item->id }}" data-tabs-target="#dashboard-{{ $item->id }}"
                                type="button" role="tab" aria-controls="dashboard-{{ $item->id }}"
                                aria-selected="false">
                                Task Tracker From Trello
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- TAB CONTENT -->
                <div id="default-tab-content-{{ $item->id }}">
                    <!-- OVERVIEW -->
                    <div class="p-6 rounded-lg" id="profile-{{ $item->id }}" role="tabpanel"
                        aria-labelledby="profile-tab-{{ $item->id }}">
                        <!-- TOP SECTION -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                            <!-- LEFT CONTENT -->
                            <div class="lg:col-span-2">
                                <!-- EMPLOYEE INFO -->
                                <h2 class="text-lg font-bold text-blue-900 mb-5">
                                    Employee's Info
                                </h2>

                                <div class="grid grid-cols-2 gap-y-3 gap-x-10">

                                    <div>
                                        <label class="block text-xs font-semibold text-blue-900">
                                            Name
                                        </label>

                                        <p class="text-gray-400 text-[12px]">
                                            {{ $item->info->name }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-blue-900">
                                            Email
                                        </label>

                                        <p class="text-gray-400 text-[12px]">
                                            {{ $item->info->email }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-blue-900">
                                            Department
                                        </label>

                                        <p class="text-gray-400 text-[12px]">
                                            {{ $item->info->departemen }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-blue-900">
                                            Phone
                                        </label>

                                        <p class="text-gray-400 text-[12px]">
                                            {{ $item->info->phone }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-blue-900">
                                            Position
                                        </label>

                                        <p class="text-gray-400 text-[12px]">
                                            {{ $item->info->position }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-blue-900">
                                            Join Date
                                        </label>

                                        <p class="text-gray-400 text-[12px]">
                                            {{ \Carbon\Carbon::parse($item->info->join_date)->format('M Y') }}
                                        </p>
                                    </div>

                                </div>

                            </div>

                            <!-- RIGHT AVATAR -->
                            <div class="flex justify-center lg:justify-end">
                                <div
                                    class="w-52 h-52 rounded-full border-[10px] border-blue-900 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-blue-900"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- BOTTOM SECTION -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-10">

                            <!-- ATTENDANCE -->
                            <div>

                                <h2 class="text-lg font-bold text-blue-900 mb-4">
                                    Attendance Summary
                                </h2>

                                <div class="space-y-2">
                                    <div>
                                        <p class="font-semibold text-blue-900">
                                            Attendance Rate
                                        </p>

                                        <p class="text-sky-500 font-semibold">
                                            {{ $item->attendance_summary->attendance_rate }}%
                                        </p>
                                    </div>

                                    <div>
                                        <p class="font-semibold text-blue-900">
                                            Present
                                        </p>

                                        <p class="text-emerald-500">
                                            {{ $item->attendance_summary->present }}
                                        </p>
                                    </div>

                                    {{-- <div>
                                        <p class="font-semibold text-blue-900">
                                            Late
                                        </p>

                                        <p class="text-gray-400">
                                            {{ $item->attendance_summary->late }}
                                        </p>
                                    </div> --}}

                                    <div>
                                        <p class="font-semibold text-blue-900">
                                            Absent
                                        </p>

                                        <p class="text-pink-500">
                                            {{ $item->attendance_summary->absent }}
                                        </p>
                                    </div>

                                </div>

                                <!-- PERFORMANCE -->
                                <div class="mt-10">

                                    <h2 class="text-lg font-bold text-blue-900 mb-3">
                                        Performance Score
                                    </h2>

                                    <h3
                                        class="text-2xl font-semibold
                                            @if ($item->status_performance == 'Excellent') text-emerald-400
                                            @elseif($item->status_performance == 'Good')
                                                text-sky-400
                                            @elseif($item->status_performance == 'Average')
                                                text-yellow-400
                                            @else
                                                text-red-400 @endif">
                                        {{ $item->status_performance }}
                                    </h3>

                                    <div class="flex items-end gap-2 mt-2">

                                        <span class="text-2xl font-bold text-blue-900">
                                            {{ $item->performance_score }}
                                        </span>

                                        <span class="text-emerald-400 text-xs">
                                            +5 from last month
                                        </span>

                                    </div>

                                </div>

                            </div>

                            <!-- KPI TABLE -->
                            <div class="md:col-span-2">

                                <!-- HEADER -->
                                <div class="flex items-center gap-2 mb-4">
                                    <h2 class="text-lg font-bold text-blue-900">
                                        KPI Breakdown
                                    </h2>

                                    <span class="text-sky-400 text-[12px]">
                                        ✏️
                                    </span>
                                </div>

                                <!-- CARD -->
                                <div class="rounded-xl border border-gray-200 overflow-hidden">

                                    <!-- HEAD -->
                                    <div
                                        class="grid grid-cols-2 bg-gray-100 text-gray-500 text-sm font-medium px-5 py-3">

                                        <div>KPI</div>
                                        <div>Score</div>

                                    </div>

                                    <!-- BODY -->
                                    <div class="divide-y divide-gray-200 text-blue-900 text-sm">

                                        <div class="grid grid-cols-2 px-5 py-4">
                                            <div>Quality</div>
                                            <div>{{ $item->kpi->quality }}</div>
                                        </div>

                                        <div class="grid grid-cols-2 px-5 py-4">
                                            <div>Productivity</div>
                                            <div>{{ $item->kpi->productivity }}</div>
                                        </div>

                                        <div class="grid grid-cols-2 px-5 py-4">
                                            <div>Teamwork</div>
                                            <div>{{ $item->kpi->teamwork }}</div>
                                        </div>

                                        <div class="grid grid-cols-2 px-5 py-4">
                                            <div>Discipline</div>
                                            <div>{{ $item->kpi->discipline }}</div>
                                        </div>

                                        <div class="grid grid-cols-2 px-5 py-4 font-bold">
                                            <div>KPI Score</div>

                                            <div>
                                                {{ number_format($item->kpi->kpi_score, 0) }}
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ATTENDANCE -->
                    <div class="hidden p-4 rounded-lg" id="dashboard-{{ $item->id }}" role="tabpanel"
                        aria-labelledby="dashboard-tab-{{ $item->id }}">
                        CONTENT ATTENDANCE
                    </div>

                </div>
            </div>
        </div>
    </div>
