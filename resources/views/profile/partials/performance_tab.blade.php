<div class="space-y-6">
    <!-- Latest Performance Score Card -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-700 rounded-xl p-6 text-white shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-purple-200 text-sm">Latest Performance Score</p>
                <p class="text-5xl font-bold mt-2">{{ $latestPerformance?->performance_score ?? $latestPerformance?->kpi_score ?? 0 }}</p>
                <div class="flex items-center mt-2">
                    @php
                        $scoreChange = $performanceChange;
                        $changeClass = $scoreChange >= 0 ? 'text-green-300' : 'text-red-300';
                    @endphp
                    <span class="{{ $changeClass }} font-semibold">
                        {{ $scoreChange >= 0 ? '+' : '' }}{{ $scoreChange }}
                    </span>
                    <span class="text-purple-200 ml-1">from last month</span>
                </div>
            </div>
            <div class="text-right">
                @php
                    $ratingLabel = $latestPerformance?->rating['label'] ?? 'No Data';
                    $ratingColor = $latestPerformance?->rating['color'] ?? 'gray';
                @endphp
                <div class="bg-white/20 rounded-lg px-4 py-2">
                    <p class="text-sm">Rating</p>
                    <p class="text-xl font-bold">{{ $ratingLabel }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Task Progress -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Task Summary</h3>
            <div class="relative">
                <div class="w-32 h-32 mx-auto">
                    <svg viewBox="0 0 36 36" class="w-full h-full">
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#E5E7EB" stroke-width="3"/>
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#8B5CF6" stroke-width="3" stroke-dasharray="{{ $taskCompletionRate }}, 100"/>
                        <text x="18" y="20.5" class="text-xs font-bold" text-anchor="middle" fill="#4B5563">{{ $taskCompletionRate }}%</text>
                    </svg>
                </div>
            </div>
            <div class="flex justify-around mt-6">
                <div class="text-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full inline-block"></div>
                    <p class="text-xs text-gray-500 mt-1">To-Do</p>
                    <p class="font-semibold">{{ $todoTasks }}</p>
                </div>
                <div class="text-center">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full inline-block"></div>
                    <p class="text-xs text-gray-500 mt-1">In Progress</p>
                    <p class="font-semibold">{{ $inProgressTasks }}</p>
                </div>
                <div class="text-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full inline-block"></div>
                    <p class="text-xs text-gray-500 mt-1">Done</p>
                    <p class="font-semibold">{{ $doneTasks }}</p>
                </div>
            </div>
        </div>

        <!-- Latest KPI Score -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Latest KPI Score</h3>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600 text-sm">Quality</span>
                        <span class="font-semibold text-sm">{{ $latestPerformance?->quality ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $latestPerformance?->quality ?? 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600 text-sm">Productivity</span>
                        <span class="font-semibold text-sm">{{ $latestPerformance?->productivity ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $latestPerformance?->productivity ?? 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600 text-sm">Teamwork</span>
                        <span class="font-semibold text-sm">{{ $latestPerformance?->teamwork ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $latestPerformance?->teamwork ?? 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600 text-sm">Discipline</span>
                        <span class="font-semibold text-sm">{{ $latestPerformance?->discipline ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $latestPerformance?->discipline ?? 0 }}%"></div>
                    </div>
                </div>
                <div class="border-t pt-3 mt-3">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-800">KPI Score</span>
                        <span class="font-bold text-xl text-blue-600">{{ $latestPerformance?->kpi_score ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Score History Chart -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Score History</h3>
        <div class="relative">
            <div class="mb-4">
                <select id="yearSelect" class="px-3 py-1 border rounded-lg text-sm">
                    <option value="{{ date('Y') }}" selected>This year</option>
                    <option value="{{ date('Y') - 1 }}">Last year</option>
                </select>
            </div>
            <div class="overflow-x-auto">
                <canvas id="performanceChart" class="w-full" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let performanceChart = null;

        function loadChart(year) {
            fetch(`/profile/performance-chart-data?year=${year}`)
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('performanceChart').getContext('2d');

                    if (performanceChart) {
                        performanceChart.destroy();
                    }

                    performanceChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.months,
                            datasets: [{
                                label: 'Performance Score',
                                data: data.scores,
                                borderColor: '#8B5CF6',
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#8B5CF6',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `Score: ${context.raw}`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    grid: {
                                        color: '#E5E7EB'
                                    },
                                    title: {
                                        display: true,
                                        text: 'Score',
                                        color: '#6B7280'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    title: {
                                        display: true,
                                        text: 'Month',
                                        color: '#6B7280'
                                    }
                                }
                            }
                        }
                    });
                });
        }

        // Load initial chart
        loadChart({{ date('Y') }});

        // Handle year change
        document.getElementById('yearSelect').addEventListener('change', function() {
            loadChart(this.value);
        });
    });
</script>
@endpush
