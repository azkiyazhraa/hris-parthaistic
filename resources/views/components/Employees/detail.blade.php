<!-- Main modal -->
<div id="default-modal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white border border-default rounded-lg shadow-sm p-4 md:p-6">
            <!-- Modal header -->
            <div class="flex items-center justify-between border-default">
                <h3 class="text-lg font-medium text-heading">
                    Employee Detail
                </h3>
                <button type="button"
                    class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-md text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                    data-modal-hide="default-modal">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <div class="mb-4 border-b border-default">
                <ul class="flex flex-wrap text-sm font-medium justify-between text-center w-full" id="default-tab"
                    data-tabs-toggle="#default-tab-content" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-base" id="profile-tab"
                            data-tabs-target="#profile" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">Overview</button>
                    </li>

                    <li class="me-2" role="presentation">
                        <button
                            class="inline-block p-4 border-b-2 rounded-t-base hover:text-fg-brand hover:border-brand"
                            id="dashboard-tab" data-tabs-target="#dashboard" type="button" role="tab"
                            aria-controls="dashboard" aria-selected="false">Attendance & Leave</button>
                    </li>

                    <li class="me-2" role="presentation">
                        <button
                            class="inline-block p-4 border-b-2 rounded-t-base hover:text-fg-brand hover:border-brand"
                            id="performance-tab" data-tabs-target="#performance" type="button" role="tab"
                            aria-controls="performance" aria-selected="false">Performance</button>
                    </li>
                </ul>
            </div>
            <div id="default-tab-content">
                <div class="hidden" id="profile" role="tabpanel">
                    <div class="grid md:grid-cols-3 gap-8">
                        <!-- LEFT CONTENT -->
                        <div class="md:col-span-2 space-y-8">
                            <!-- PERSONAL INFO -->
                            <div>
                                <h4 class="text-lg font-semibold text-blue-900 mb-4">
                                    Personal Info
                                </h4>

                                <div class="grid md:grid-cols-2 gap-x-10 gap-y-4 text-sm">
                                    <!-- LEFT -->
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-gray-400">Full Name</p>
                                            <p class="font-medium text-gray-700" id="detail_nama_lengkap">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">NIP</p>
                                            <p class="font-medium text-gray-700" id="detail_nip">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">Address</p>
                                            <p class="font-medium text-gray-700" id="detail_alamat">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">Place of Birth</p>
                                            <p class="font-medium text-gray-700" id="detail_tempat_lahir">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">Date of Birth</p>
                                            <p class="font-medium text-gray-700" id="detail_tanggal_lahir">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">Gender</p>
                                            <p class="font-medium text-gray-700" id="detail_jenis_kelamin">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">Religion</p>
                                            <p class="font-medium text-gray-700" id="detail_agama">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">Marital Status</p>
                                            <p class="font-medium text-gray-700" id="detail_status_pernikahan">-</p>
                                        </div>
                                    </div>

                                    <!-- RIGHT -->
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-gray-400">Email</p>
                                            <p class="font-medium text-gray-700" id="detail_email">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">Phone Number</p>
                                            <p class="font-medium text-gray-700" id="detail_nomor_telepon">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">NIK</p>
                                            <p class="font-medium text-gray-700" id="detail_nik">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">NPWP</p>
                                            <p class="font-medium text-gray-700" id="detail_npwp">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">Emergency Contact Name</p>
                                            <p class="font-medium text-gray-700" id="detail_nama_kontak_darurat">-</p>
                                        </div>

                                        <div>
                                            <p class="text-gray-400">Emergency Contact Phone</p>
                                            <p class="font-medium text-gray-700" id="detail_telepon_kontak_darurat">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- EMPLOYMENT INFO -->
                            <div>
                                <h4 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                                    Employment Info
                                </h4>

                                <div class="grid md:grid-cols-2 gap-4 text-sm">
                                    <div class="bg-gray-100 p-2 rounded">
                                        <span class="text-gray-400">Role:</span> 
                                        <span class="font-medium" id="detail_role">-</span>
                                    </div>
                                    <div class="bg-gray-100 p-2 rounded">
                                        <span class="text-gray-400">Join Date:</span> 
                                        <span class="font-medium" id="detail_tanggal_bergabung">-</span>
                                    </div>

                                    <div class="bg-gray-100 p-2 rounded">
                                        <span class="text-gray-400">Status:</span> 
                                        <span class="font-medium" id="detail_status">-</span>
                                    </div>
                                    <div class="bg-gray-100 p-2 rounded">
                                        <span class="text-gray-400">Last Education:</span> 
                                        <span class="font-medium" id="detail_pendidikan_terakhir">-</span>
                                    </div>

                                    <div class="bg-gray-100 p-2 rounded">
                                        <span class="text-gray-400">University:</span> 
                                        <span class="font-medium" id="detail_universitas">-</span>
                                    </div>
                                    <div class="bg-gray-100 p-2 rounded">
                                        <span class="text-gray-400">Major:</span> 
                                        <span class="font-medium" id="detail_jurusan">-</span>
                                    </div>

                                    <div class="bg-gray-100 p-2 rounded">
                                        <span class="text-gray-400">Graduation Year:</span> 
                                        <span class="font-medium" id="detail_tahun_lulus">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT AVATAR -->
                        <div class="flex justify-center items-start md:items-center">
                            <div
                                class="w-40 h-40 md:w-52 md:h-52 rounded-full border-[6px] border-blue-900 flex items-center justify-center overflow-hidden bg-gray-100">
                                <img id="detail_foto_profil" src="" alt="Profile Photo" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden p-4" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                    <div class="text-center text-gray-500 py-8">
                        <p>Attendance & Leave features coming soon...</p>
                    </div>
                </div>

                <div class="hidden p-4" id="performance" role="tabpanel" aria-labelledby="performance-tab">
                    <div class="text-center text-gray-500 py-8">
                        <p>Performance features coming soon...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Store karyawan data globally
    let karyawanData = {};

    @foreach ($karyawans as $karyawan)
        karyawanData[{{ $karyawan->id }}] = {
            id: {{ $karyawan->id }},
            nip: "{{ $karyawan->nip }}",
            nama_lengkap: "{{ addslashes($karyawan->nama_lengkap) }}",
            email: "{{ $karyawan->email }}",
            role: "{{ $karyawan->role }}",
            status: "{{ $karyawan->status ?? 'Aktif' }}",
            nomor_telepon: "{{ $karyawan->nomor_telepon ?? '-' }}",
            alamat: "{{ addslashes($karyawan->alamat ?? '-') }}",
            nik: "{{ $karyawan->nik ?? '-' }}",
            npwp: "{{ $karyawan->npwp ?? '-' }}",
            tempat_lahir: "{{ $karyawan->tempat_lahir ?? '-' }}",
            tanggal_lahir: "{{ $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('d F Y') : '-' }}",
            jenis_kelamin: "{{ $karyawan->jenis_kelamin ?? '-' }}",
            agama: "{{ $karyawan->agama ?? '-' }}",
            status_pernikahan: "{{ $karyawan->status_pernikahan ?? '-' }}",
            pendidikan_terakhir: "{{ $karyawan->pendidikan_terakhir ?? '-' }}",
            universitas: "{{ $karyawan->universitas ?? '-' }}",
            jurusan: "{{ $karyawan->jurusan ?? '-' }}",
            tahun_lulus: "{{ $karyawan->tahun_lulus ?? '-' }}",
            nama_kontak_darurat: "{{ $karyawan->nama_kontak_darurat ?? '-' }}",
            telepon_kontak_darurat: "{{ $karyawan->telepon_kontak_darurat ?? '-' }}",
            tanggal_bergabung: "{{ $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('d F Y') : '-' }}",
            foto_profil: "{{ $karyawan->foto_profil && Storage::disk('public')->exists($karyawan->foto_profil) ? Storage::url($karyawan->foto_profil) : '' }}"
        };
    @endforeach

    function showEmployeeDetail(id) {
        const data = karyawanData[id];
        if (!data) return;

        // Update personal info
        document.getElementById('detail_nama_lengkap').innerText = data.nama_lengkap;
        document.getElementById('detail_nip').innerText = data.nip;
        document.getElementById('detail_alamat').innerText = data.alamat;
        document.getElementById('detail_tempat_lahir').innerText = data.tempat_lahir;
        document.getElementById('detail_tanggal_lahir').innerText = data.tanggal_lahir;
        document.getElementById('detail_jenis_kelamin').innerText = data.jenis_kelamin;
        document.getElementById('detail_agama').innerText = data.agama;
        document.getElementById('detail_status_pernikahan').innerText = data.status_pernikahan;
        
        // Update contact info
        document.getElementById('detail_email').innerText = data.email;
        document.getElementById('detail_nomor_telepon').innerText = data.nomor_telepon;
        document.getElementById('detail_nik').innerText = data.nik;
        document.getElementById('detail_npwp').innerText = data.npwp;
        document.getElementById('detail_nama_kontak_darurat').innerText = data.nama_kontak_darurat;
        document.getElementById('detail_telepon_kontak_darurat').innerText = data.telepon_kontak_darurat;
        
        // Update employment info
        document.getElementById('detail_role').innerHTML = `<span class="text-gray-400">Role:</span> <span class="font-medium">${data.role.toUpperCase()}</span>`;
        document.getElementById('detail_tanggal_bergabung').innerText = data.tanggal_bergabung;
        document.getElementById('detail_status').innerText = data.status;
        document.getElementById('detail_pendidikan_terakhir').innerText = data.pendidikan_terakhir;
        document.getElementById('detail_universitas').innerText = data.universitas;
        document.getElementById('detail_jurusan').innerText = data.jurusan;
        document.getElementById('detail_tahun_lulus').innerText = data.tahun_lulus;
        
        // Update photo
        const fotoImg = document.getElementById('detail_foto_profil');
        if (data.foto_profil && data.foto_profil !== '') {
            fotoImg.src = data.foto_profil;
            fotoImg.parentElement.classList.remove('flex', 'items-center', 'justify-center');
        } else {
            fotoImg.src = `https://ui-avatars.com/api/?background=0D8F81&color=fff&size=200&name=${encodeURIComponent(data.nama_lengkap)}`;
            fotoImg.parentElement.classList.remove('flex', 'items-center', 'justify-center');
        }
        fotoImg.style.display = 'block';
    }

    // ✅ DONUT CHART
    var donutOptions = {
        chart: {
            type: 'donut',
            height: 250
        },
        series: [80, 10, 10],
        labels: ['Done', 'In Progress', 'To-Do'],
        colors: ['#06b6d4', '#4ade80', '#f43f5e'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: false
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Progress',
                            formatter: function() {
                                return '80%'
                            }
                        }
                    }
                }
            }
        }
    };

    var donutChart = new ApexCharts(document.querySelector("#chartTask"), donutOptions);
    if (document.querySelector("#chartTask")) donutChart.render();

    // ✅ LINE CHART
    var lineOptions = {
        chart: {
            type: 'line',
            height: 300,
            toolbar: {
                show: false
            }
        },
        series: [{
            name: 'Performance',
            data: [80, 90, 85, 75, 80]
        }],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            labels: {
                style: {
                    colors: '#6b7280'
                }
            }
        },
        yaxis: {
            min: 72,
            max: 92,
            tickAmount: 5,
            labels: {
                style: {
                    colors: '#6b7280'
                }
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        colors: ['#22c1f1'],
        grid: {
            borderColor: '#e5e7eb',
            strokeDashArray: 4
        },
        markers: {
            size: 0
        },
        dataLabels: {
            enabled: false
        },
        tooltip: {
            theme: 'light'
        }
    };

    var lineChart = new ApexCharts(document.querySelector("#chartPerformance"), lineOptions);
    if (document.querySelector("#chartPerformance")) lineChart.render();
</script>
@endpush