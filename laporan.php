<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Laporan Komplain</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
        }
            /* Background Image */
        body {
            background-image: url('rm222batch2-mind-03.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .filter-panel {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        #resolutionTimeChart {
            height: 500px !important; /* Ubah sesuai kebutuhan, bisa jadi 600px jika perlu */
        }


        .filter-item {
            /* Removed flex properties for grid layout */
        }

        @media (max-width: 768px) {
            .filter-panel {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        .metric-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            transition: transform 0.3s;
        }

        .metric-card:hover {
            transform: translateY(-5px);
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            position: relative;
            width: 100%;
            height: auto;
        }

        .chart-container canvas {
            width: 100% !important;
            height: auto !important;
        }

        .complaint-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .complaint-table th {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
        }

        .complaint-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #eee;
        }

        .complaint-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-done {
            background-color: #e6f7ee;
            color: #00a854;
        }

        .status-progress {
            background-color: #fff7e6;
            color: #fa8c16;
        }

        .status-pending {
            background-color: #fff1f0;
            color: #f5222d;
        }

        .btn-export {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            margin-right: 0.5rem;
            transition: all 0.3s;
        }

        .btn-export:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .insight-card {
            background: white;
            border-left: 4px solid var(--primary-color);
            border-radius: 5px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .theme-switch {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        /* Dark mode styles */
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        body.dark-mode .metric-card,
        body.dark-mode .chart-container,
        body.dark-mode .complaint-table,
        body.dark-mode .filter-panel,
        body.dark-mode .insight-card {
            background-color: #1e1e1e;
            color: #e0e0e0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        body.dark-mode .complaint-table th {
            background-color: #2d3748;
        }

        body.dark-mode .complaint-table tr:hover {
            background-color: #2d3748;
        }

        @media (max-width: 768px) {
            .filter-panel {
                flex-direction: column;
            }
            
            .filter-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-exclamation-circle me-2"></i>Laporan Komplain</h1>
                    <p class="mb-0">Analisis dan monitoring keluhan pelanggan & internal</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="dropdown">
                        <?php
                        $resultUserGroup = $conn->query("SELECT DISTINCT user_group FROM user_info ORDER BY user_group ASC");
                        $userGroups = [];
                        while ($rowUserGroup = $resultUserGroup->fetch_assoc()) {
                            $userGroups[] = $rowUserGroup['user_group'];
                        }
                        // Set default group to 'it' if exists, else first group or 'No Group'
                        $defaultGroup = 'No Group';
                        if (in_array('it', array_map('strtolower', $userGroups))) {
                            $defaultGroup = 'it';
                        } elseif (count($userGroups) > 0) {
                            $defaultGroup = $userGroups[0];
                        }
                        ?>
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-users me-1"></i> <span id="selectedUserGroup"><?php echo htmlspecialchars($defaultGroup); ?></span>
                    </button>
                    <ul class="dropdown-menu" id="userGroupDropdownMenu">
                        <?php foreach ($userGroups as $group): ?>
                            <li><a class="dropdown-item user-group-item" href="#" data-group="<?php echo htmlspecialchars($group); ?>"><?php echo htmlspecialchars($group); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Panel -->
        <?php include 'db.php'; ?>
        <div class="filter-panel row g-2 align-items-end">
            <!-- Periode -->
            <div>
                <label class="form-label">Periode</label>
                <div class="d-flex align-items-center">
                    <input type="date" class="form-control" id="start-date">
                    <span class="mx-2">s/d</span>
                    <input type="date" class="form-control" id="end-date">
                </div>
            </div>

            <!-- Cabang di kanan -->
            <div class="col-md-10 offset-md-2">
                <label class="form-label">Cabang</label>
                <select class="form-select" id="branch-select">
                    <option value="all">Semua Cabang</option>
                    <?php
                    $resultCabang = $conn->query("SELECT DISTINCT cabang FROM komplain ORDER BY cabang ASC");
                    while ($rowCabang = $resultCabang->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($rowCabang['cabang']) . '">' . htmlspecialchars($rowCabang['cabang']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="filter-item">
                <label class="form-label">Status</label>
                <select class="form-select" id="status-select">
                    <option value="all">Semua Status</option>
                    <option value="completed">Completed</option>
                    <option value="on progress">On Progress</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="filter-item">
                <button class="btn btn-primary w-100" id="apply-filter">
                    <i class="fas fa-filter me-1"></i> Terapkan Filter
                </button>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="row">
            <div class="col-md-2 me-3">
                <div class="metric-card">
                <div class="d-flex align-items-top">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-2 px-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-ticket-alt text-primary fs-5"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Total Komplain</p>
                            <h4 class="mb-0 fw-bold metric-value" id="total-komplain">0</h4>
                        </div>
                    </div>
                </div>
            </div>   
            <div class="col-md-3 me-3">
                <div class="metric-card">
                    <div class="d-flex align-items-top">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-2  d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Komplain Selesai</p>
                            <h2 class="metric-value" id="komplain-selesai">
                                0 <small class="text-success">(0%)</small>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 me-5">
                <div class="metric-card">
                    <div class="d-flex align-items-top">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-spinner fa-spin text-warning"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Komplain Berjalan</p>
                            <h2 class="metric-value" id="komplain-berjalan">
                                0
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 me-4">
                <div class="metric-card">
                    <div class="d-flex align-items-top">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-clock text-dark"></i> 
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Komplain Pending</p>
                            <h2 class="metric-value" id="komplain-pending">
                                0
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="metric-card">
                    <div class="d-flex align-items-top">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted">Komplain Tolak</p>
                            <h2 class="metric-value" id="komplain-Tolak">0</h2>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Charts Row 1 -->
        <div class="row">
            <div">
                <div class="chart-container">
                    <h5><i class="fas fa-chart-line me-2"></i>Tren Komplain Bulanan</h5>
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="chart-container">
                    <h5><i class="fas fa-chart-pie me-2"></i>Jenis Komplain</h5>
                    <canvas id="complaintTypeChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5><i class="fas fa-stopwatch me-2"></i>Waktu Penyelesaian</h5>
                    <canvas id="resolutionTimeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- New Chart: Pencapaian Teknisi per Jenis Komplain -->
        <div class="row">
            <div class="col-sm-12 p-4">
                <div class="chart-container bg-white shadow-sm rounded">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-bar me-8"></i>Pencapaian Teknisi per Jenis Komplain
                    </h5>
                    <canvas id="techAchievementChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Complaint Table -->
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-table me-5"></i>Daftar Komplain</h5>
                <div>
                    <input type="text" id="search-input" class="form-control form-control-sm d-inline-block w-auto" placeholder="Cari...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table complaint-table">
                    <thead>
                        <tr>
                            <th>ID Tiket</th>
                            <th>Tanggal</th>
                            <th>Cabang</th>
                            <th>Jenis</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="complaint-table-body">
            </tbody>
        </table>
    </div>
    <nav class="mt-3">
        <ul class="pagination justify-content-end">
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
                <a class="page-link" href="#">Next</a>
            </li>
        </ul>
    </nav>
</div>

<script>
    // Search filter for complaint table
    document.getElementById('search-input').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#complaint-table-body tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

        <!-- Export Options -->
        <div class="text-end mb-4">
            <button class="btn-export"><i class="fas fa-file-pdf me-1"></i> Export PDF</button>
            <button class="btn-export"><i class="fas fa-file-excel me-1"></i> Export Excel</button>
            <button class="btn-export"><i class="fas fa-print me-1"></i> Print</button>
        </div>
    </div>

    <!-- Theme Switch -->
    <div class="theme-switch">
        <button class="btn btn-dark rounded-circle p-3" id="themeToggle">
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Trend Chart
            const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
            const monthlyTrendChart = new Chart(monthlyTrendCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Total Komplain',
                            data: [25, 30, 22, 28, 20],
                            borderColor: '#4361ee',
                            backgroundColor: 'rgba(67, 97, 238, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Selesai',
                            data: [18, 25, 15, 22, 18],
                            borderColor: '#4cc9f0',
                            backgroundColor: 'rgba(76, 201, 240, 0.1)',
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Complaint Type Chart
            const complaintTypeCtx = document.getElementById('complaintTypeChart').getContext('2d');
            const complaintTypeChart = new Chart(complaintTypeCtx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Function to fetch and update complaint type chart data
            function fetchComplaintTypeData(start, end, cabang, status, user_group) {
                const formData = new URLSearchParams();
                formData.append('start', start);
                formData.append('end', end);
                formData.append('cabang', cabang);
                formData.append('status', status);
                // Only append user_group if not empty
                if (user_group !== '') {
                    formData.append('user_group', user_group);
                }

                fetch('get_jenis_komplain.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.labels && data.data) {
                        complaintTypeChart.data.labels = data.labels;

                        complaintTypeChart.data.datasets[0].data = data.data;

                        // Generate background colors dynamically based on number of labels
                        const baseColors = [
                            '#4361ee',
                            '#4cc9f0',
                            '#7209b7',
                            '#f72585',
                            '#adb5bd',
                            '#ff6f61',
                            '#6b5b95',
                            '#88b04b',
                            '#f7cac9',
                            '#92a8d1'
                        ];
                        const colors = [];
                        for (let i = 0; i < data.labels.length; i++) {
                            colors.push(baseColors[i % baseColors.length]);
                        }
                        complaintTypeChart.data.datasets[0].backgroundColor = colors;

                        complaintTypeChart.update();
                    } else {
                        console.error('Data jenis komplain tidak lengkap:', data);
                    }
                })
                .catch(error => {
                    console.error('Gagal fetch data jenis komplain:', error);
                });
            }

            // Variable to store selected user group
            // Initialize to empty string to load all data initially
            let selectedUserGroup = '';

            // Set dropdown text to "Semua" initially
            document.getElementById('selectedUserGroup').textContent = 'Semua';

            // Add event listeners to user group dropdown items
            document.querySelectorAll('.user-group-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectedUserGroup = this.getAttribute('data-group').toLowerCase();
                    document.getElementById('selectedUserGroup').textContent = selectedUserGroup;

                    // Trigger filter update with new user group
                    document.getElementById('apply-filter').click();
                });
            });

            // Modify apply-filter click event to also update complaint type chart and resolution time chart
            document.getElementById('apply-filter').addEventListener('click', function () {
                const start = document.getElementById('start-date').value;
                const end = document.getElementById('end-date').value;
                const cabang = document.getElementById('branch-select').value;
                const status = document.getElementById('status-select').value;

                // Fetch and update monthly complaint trend chart
                fetchMonthlyComplaintData(start, end, cabang, status, selectedUserGroup);

                // Fetch and update complaint type chart
                fetchComplaintTypeData(start, end, cabang, status, selectedUserGroup);

                // Fetch and update resolution time chart
                fetchResolutionTimeData(start, end, selectedUserGroup, cabang, status);

                // Fetch and update total complaint metrics
                const formData = new URLSearchParams();
                formData.append('start', start);
                formData.append('end', end);
                formData.append('cabang', cabang);
                formData.append('status', status);
                // Only append user_group if selectedUserGroup is not empty
                if (selectedUserGroup !== '') {
                    formData.append('user_group', selectedUserGroup);
                }

                fetch('get_total_komplain.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.total !== undefined && data.selesai !== undefined) {
                        // Update Total Komplain
                        document.getElementById('total-komplain').innerText = data.total;

                        // Update Komplain Selesai
                        document.getElementById('komplain-selesai').innerHTML = `
                            ${data.selesai} <small class="text-success">(${data.persentase}%)</small>
                        `;
                         // Update Komplain Berjalan
                        document.getElementById('komplain-berjalan').innerText = data.berjalan;
                        // Update Komplain Pending
                        document.getElementById('komplain-pending').innerText = data.pending;
                        // Update Komplain Tolak
                        document.getElementById('komplain-Tolak').innerText = data.tolak;
                    } else {
                        console.error('Data tidak lengkap:', data);
                    }
                })
                .catch(error => {
                    console.error('Gagal fetch data komplain:', error);
                });
            });

            // Trigger grafik pertama kali with default user group
            document.getElementById('apply-filter').click();

            // Resolution Time Chart
            const resolutionTimeCtx = document.getElementById('resolutionTimeChart').getContext('2d');
            const resolutionTimeChart = new Chart(resolutionTimeCtx, {
                type: 'bar',
                data: {
                    labels: ['< 1 jam', '1 - 4 jam', '4 - 8 jam', '8 - 24 jam', '> 24 jam'],
                    datasets: [{
                        label: 'Jumlah Komplain',
                        data: [0, 0, 0, 0, 0],
                        backgroundColor: [
                            '#4cc9f0',
                            '#4361ee',
                            '#7209b7',
                            '#f72585',
                            '#adb5bd'
                        ],
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Function to fetch and update resolution time chart data
            function fetchResolutionTimeData(start, end, user_group, cabang, status) {
                const formData = new URLSearchParams();
                formData.append('start', start);
                formData.append('end', end);
                // Only append user_group if not empty
                if (user_group !== '') {
                    formData.append('user_group', user_group);
                }
                formData.append('cabang', cabang);
                formData.append('status', status);

                fetch('get_resolution_time.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        resolutionTimeChart.data.datasets[0].data = [
                            data.kurang_1_jam,
                            data.antara_1_4_jam,
                            data.antara_4_8_jam,
                            data.antara_8_24_jam,
                            data.lebih_24_jam
                        ];
                        resolutionTimeChart.update();
                    } else {
                        console.error('Data waktu penyelesaian tidak lengkap:', data);
                    }
                })
                .catch(error => {
                    console.error('Gagal fetch data waktu penyelesaian:', error);
                });
            }

            // Theme Toggle
            const themeToggle = document.getElementById('themeToggle');
            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                
                if (document.body.classList.contains('dark-mode')) {
                    themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
                    themeToggle.classList.remove('btn-dark');
                    themeToggle.classList.add('btn-light');
                } else {
                    themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
                    themeToggle.classList.remove('btn-light');
                    themeToggle.classList.add('btn-dark');
                }
                
                // Update charts for dark mode
                const charts = [monthlyTrendChart, complaintTypeChart, resolutionTimeChart];
                charts.forEach(chart => {
                    chart.options.scales.x.grid.color = document.body.classList.contains('dark-mode') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                    chart.options.scales.y.grid.color = document.body.classList.contains('dark-mode') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                    chart.update();
                });
            });

            // Set default dates
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 0);
            
            document.getElementById('start-date').valueAsDate = firstDay;
            document.getElementById('end-date').valueAsDate = today;

            // Update dropdown button text to current month and year (display only)
            // Disabled to prevent overwriting user_group dropdown
            // const dropdownButton = document.getElementById('dropdownMenuButton');
            // const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            // dropdownButton.innerHTML = `<i class="fas fa-calendar-alt me-1"></i> ${monthNames[today.getMonth()]} ${today.getFullYear()}`;

            // Update dropdown menu to show only current month and year
            // const dropdownMenu = dropdownButton.nextElementSibling;
            // dropdownMenu.innerHTML = '';
            // const li = document.createElement('li');
            // const a = document.createElement('a');
            // a.className = 'dropdown-item';
            // a.href = '#';
            // a.textContent = `${monthNames[today.getMonth()]} ${today.getFullYear()}`;
            // li.appendChild(a);
            // dropdownMenu.appendChild(li);
        
            // Tombol Filter diklik
            document.getElementById('apply-filter').addEventListener('click', function () {
                const start = document.getElementById('start-date').value;
                const end = document.getElementById('end-date').value;
                const cabang = document.getElementById('branch-select').value;
                const status = document.getElementById('status-select').value;

                // Fetch and update monthly complaint trend chart with user group
                fetchMonthlyComplaintData(start, end, cabang, status, selectedUserGroup);

                // Fetch and update total complaint metrics
                const formData = new URLSearchParams();
                formData.append('start', start);
                formData.append('end', end);
                formData.append('cabang', cabang);
                formData.append('status', status);
                formData.append('user_group', selectedUserGroup);

                fetch('get_total_komplain.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.total !== undefined && data.selesai !== undefined) {
                        // Update Total Komplain
                        document.getElementById('total-komplain').innerText = data.total;

                        // Update Komplain Selesai
                        document.getElementById('komplain-selesai').innerHTML = `
                            ${data.selesai} <small class="text-success">(${data.persentase}%)</small>
                        `;
                         // Update Komplain Berjalan
                        document.getElementById('komplain-berjalan').innerText = data.berjalan;
                        // Update Komplain Pending
                        document.getElementById('komplain-pending').innerText = data.pending;
                        // Update Komplain Tolak
                        document.getElementById('komplain-Tolak').innerText = data.tolak;
                    } else {
                        console.error('Data tidak lengkap:', data);
                    }
                })
                .catch(error => {
                    console.error('Gagal fetch data komplain:', error);
                });
            });

            // Function to fetch and update monthly complaint trend chart
            function fetchMonthlyComplaintData(start, end, cabang, status, user_group) {

                const formData = new URLSearchParams();
                formData.append('start', start);
                formData.append('end', end);
                formData.append('cabang', cabang);
                formData.append('status', status);
                // Only append user_group if not empty
                if (user_group !== '') {
                    formData.append('user_group', user_group);
                }

                fetch('get_monthly_komplain.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData
                })										
                .then(response => response.json())
                .then(data => {
                    if (data.labels && data.total && data.selesai) {
                        monthlyTrendChart.data.labels = data.labels;
                        monthlyTrendChart.data.datasets[0].data = data.total;
                        monthlyTrendChart.data.datasets[1].data = data.selesai;
                        monthlyTrendChart.update();
                    } else {
                        console.error('Data tren komplain bulanan tidak lengkap:', data);
                    }
                })
                .catch(error => {
                    console.error('Gagal fetch data tren komplain bulanan:', error);
                });
            }

            // Tech Achievement Chart
            const techAchievementCtx = document.getElementById('techAchievementChart').getContext('2d');
            const baseColors = [
                '#4361ee', '#4cc9f0', '#7209b7', '#f72585', '#adb5bd',
                '#ff6f61', '#6b5b95', '#88b04b', '#f7cac9', '#92a8d1'
            ];
            let techAchievementChart = new Chart(techAchievementCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed.y;
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: false,
                            title: {
                                display: true,
                                text: 'Teknisi'
                            }
                        },
                        y: {
                            stacked: false,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Komplain'
                            }
                        }
                    }
                }
            });

            // Function to fetch and update tech achievement chart data
            function fetchTechAchievementData(start, end, cabang, user_group, status) {
                const formData = new URLSearchParams();
                formData.append('start', start);
                formData.append('end', end);
                formData.append('cabang', cabang);
                formData.append('user_group', user_group);
                formData.append('status', status);

                fetch('get_tech_achievement.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.labels && data.datasets) {
                        // Normalize teknisi names to lowercase and aggregate data
                        const normalizedLabelsMap = new Map(); // Map lowercase teknisi -> original display name (first occurrence)
                        const aggregatedDataMap = new Map(); // Map lowercase teknisi -> array of counts per jenis_komplain

                        // Initialize aggregatedDataMap keys with normalized teknisi names
                        data.labels.forEach(label => {
                            const lowerLabel = label.toLowerCase();
                            if (!normalizedLabelsMap.has(lowerLabel)) {
                                normalizedLabelsMap.set(lowerLabel, label);
                            }
                        });

                        // Initialize aggregatedDataMap with empty arrays for each normalized teknisi
                        normalizedLabelsMap.forEach((displayName, lowerLabel) => {
                            aggregatedDataMap.set(lowerLabel, new Array(data.datasets.length).fill(0));
                        });

                        // Aggregate counts from datasets
                        data.datasets.forEach((dataset, datasetIndex) => {
                            dataset.data.forEach((count, labelIndex) => {
                                const originalLabel = data.labels[labelIndex];
                                const lowerLabel = originalLabel.toLowerCase();
                                const aggArray = aggregatedDataMap.get(lowerLabel);
                                aggArray[datasetIndex] += count;
                            });
                        });

                        // Prepare final labels and datasets
                        const finalLabels = Array.from(normalizedLabelsMap.values());
                        const finalDatasets = data.datasets.map((dataset, index) => {
                            return {
                                label: dataset.label,
                                data: [],
                                backgroundColor: baseColors[index % baseColors.length],
                                borderRadius: 5
                            };
                        });

                        // Fill finalDatasets data arrays
                        finalLabels.forEach((displayName, idx) => {
                            const lowerLabel = displayName.toLowerCase();
                            const aggArray = aggregatedDataMap.get(lowerLabel);
                            aggArray.forEach((count, datasetIndex) => {
                                finalDatasets[datasetIndex].data.push(count);
                            });
                        });

                        techAchievementChart.data.labels = finalLabels;
                        techAchievementChart.data.datasets = finalDatasets;
                        techAchievementChart.update();
                    } else {
                        console.error('Data pencapaian teknisi tidak lengkap:', data);
                    }
                })
                .catch(error => {
                    console.error('Gagal fetch data pencapaian teknisi:', error);
                });
            }

            // Pagination variables
            let currentPage = 0;
            let totalPages = 1;

            // Modify apply-filter click event to also update tech achievement chart and complaint table
            document.getElementById('apply-filter').addEventListener('click', function () {
                currentPage = 1; // Reset to first page on filter apply
                loadDataPage(currentPage);
                fetchTechAchievementData(
                    document.getElementById('start-date').value,
                    document.getElementById('end-date').value,
                    document.getElementById('branch-select').value,
                    // Pass selectedUserGroup only if not empty
                    selectedUserGroup !== '' ? selectedUserGroup : '',
                    document.getElementById('status-select').value
                );
            });

            // Function to load complaint data for a specific page
            function loadDataPage(page) {
                const start = document.getElementById('start-date').value;
                const end = document.getElementById('end-date').value;
                const cabang = document.getElementById('branch-select').value;
                const status = document.getElementById('status-select').value;

                const formData = new URLSearchParams();
                formData.append('start', start);
                formData.append('end', end);
                formData.append('cabang', cabang);
                formData.append('status', status);
                // Only append user_group if not empty
                if (selectedUserGroup !== '') {
                    formData.append('user_group', selectedUserGroup);
                }
                formData.append('page', page);
                formData.append('limit', 10);

                fetch('get_komplain_list.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.complaints) {
                        const tbody = document.getElementById('complaint-table-body');
                        tbody.innerHTML = '';

                        data.complaints.forEach(item => {
                            let statusClass = '';
                            let statusText = '';

                            switch (item.status.toLowerCase()) {
                                case 'completed':
                                case 'selesai':
                                    statusClass = 'status-done';
                                    statusText = 'Selesai';
                                    break;
                                case 'on progress':
                                case 'dalam proses':
                                    statusClass = 'status-progress';
                                    statusText = 'Dalam Proses';
                                    break;
                                case 'pending':
                                case 'belum ditangani':
                                    statusClass = 'status-pending';
                                    statusText = 'Belum Ditangani';
                                    break;
                                case 'cancelled':
                                case 'batal':
                                    statusClass = 'status-pending';
                                    statusText = 'Batal';
                                    break;
                                default:
                                    statusClass = '';
                                    statusText = item.status;
                            }

                            const row = document.createElement('tr');

                            row.innerHTML = `
                                <td>${item.id_tiket}</td>
                                <td>${item.tanggal}</td>
                                <td>${item.cabang}</td>
                                <td>${item.jenis_komplain}</td>
                                <td>${item.teknisi}</td>
                                <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                                <td><button class="btn btn-sm btn-outline-primary detail-btn" data-filepath="${item.file_path}">Detail</button></td>
                            `;

                            tbody.appendChild(row);

            // Add event listener for detail button after appending row
            row.querySelector('.detail-btn').addEventListener('click', function() {
                const filePath = this.getAttribute('data-filepath');
                if (filePath && filePath.trim().toLowerCase() !== 'null') {
                    const extension = filePath.split('.').pop().toLowerCase();
                    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    const fileUrl = `https://tascominimart.co.id/tiket/uploads/${filePath}`;
                    if (imageExtensions.includes(extension)) {
                        const modalBody = document.getElementById('imageModalBody');
                        modalBody.innerHTML = `<img src="${fileUrl}" alt="Gambar Komplain" class="img-fluid">`;
                        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                        imageModal.show();
                    } else {
                        // For non-image files, trigger download
                        window.location.href = fileUrl;
                    }
                } else {
                    alert('Detail tidak ada.');
                }
            });
                        });

                        // Update pagination
                        if (data.total !== undefined) {
                            totalPages = Math.ceil(data.total / 10);
                            updatePagination();
                        }
                    } else {
                        console.error('Data komplain tidak lengkap:', data);
                    }
                })
                .catch(error => {
                    console.error('Gagal fetch data komplain:', error);
                });
            }

            // Function to update pagination UI
            function updatePagination() {
                const pagination = document.querySelector('.pagination');
                pagination.innerHTML = '';

                // Previous button
                const prevLi = document.createElement('li');
                prevLi.className = 'page-item' + (currentPage === 1 ? ' disabled' : '');
                const prevLink = document.createElement('a');
                prevLink.className = 'page-link';
                prevLink.href = '#';
                prevLink.textContent = 'Previous';
                prevLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        loadDataPage(currentPage);
                    }
                });
                prevLi.appendChild(prevLink);
                pagination.appendChild(prevLi);

                // Page numbers (show max 5 pages for simplicity)
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, startPage + 4);
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    const li = document.createElement('li');
                    li.className = 'page-item' + (i === currentPage ? ' active' : '');
                    const link = document.createElement('a');
                    link.className = 'page-link';
                    link.href = '#';
                    link.textContent = i;
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        if (i !== currentPage) {
                            currentPage = i;
                            loadDataPage(currentPage);
                        }
                    });
                    li.appendChild(link);
                    pagination.appendChild(li);
                }

                // Next button
                const nextLi = document.createElement('li');
                nextLi.className = 'page-item' + (currentPage === totalPages ? ' disabled' : '');
                const nextLink = document.createElement('a');
                nextLink.className = 'page-link';
                nextLink.href = '#';
                nextLink.textContent = 'Next';
                nextLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (currentPage < totalPages) {
                        currentPage++;
                        loadDataPage(currentPage);
                    }
                });
                nextLi.appendChild(nextLink);
                pagination.appendChild(nextLi);
            }

            // Initial load
            loadDataPage(currentPage);

            // Trigger grafik pertama kali
            document.getElementById('apply-filter').click();
        });
    </script>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="imageModalLabel">Bukti Komplain</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="imageModalBody" style="text-align:center;">
            <!-- Image will be injected here -->
          </div>
        </div>
      </div>
    </div>
</body>
