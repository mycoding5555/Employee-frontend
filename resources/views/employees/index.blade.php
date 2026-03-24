<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Directory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/employees.css') }}" rel="stylesheet">
</head>
<body>
    {{-- Navbar --}}
    <nav class="app-navbar">
        <div class="container">
            <div class="brand" onclick="window.location='{{ route('employees.index') }}'">
                <span class="brand-icon"><i class="bi bi-people-fill"></i></span>
                មុខងារទាញយករូបថត
            </div>
        </div>
    </nav>

    <div class="container">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="app-alert app-alert-success mt-3 alert alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(52%) sepia(52%) saturate(5000%) hue-rotate(130deg);"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="app-alert app-alert-danger mt-3 alert alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(26%) sepia(89%) saturate(5000%) hue-rotate(355deg);"></button>
            </div>
        @endif

        {{-- Search Section --}}
        <div class="search-section">
            <div class="section-title"><i class="bi bi-funnel me-1"></i> Search &amp; Filter</div>
            <div class="app-card">
                <div class="card-body-custom">
                    <form id="search-form" action="{{ route('employees.search') }}" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="name" class="form-label-custom">គោត្តនាម និងនាម</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="border-radius:8px 0 0 8px; background:var(--bg); border-color:var(--border); color:var(--text-muted);">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ $filters['name'] ?? '' }}"
                                           placeholder="Search by name..."
                                           style="border-left:none; border-radius:0 8px 8px 0;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="department_id" class="form-label-custom">អគ្គនាយកដ្ឋាន</label>
                                <select class="form-select" id="department_id" name="department_id">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept['id'] }}"
                                            {{ (isset($filters['department_id']) && $filters['department_id'] == $dept['id']) ? 'selected' : '' }}>
                                            {{ $dept['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary-custom grow">
                                    <i class="bi bi-search me-1"></i> Search
                                </button>
                                <a href="{{ route('employees.index') }}" class="btn btn-outline-custom" title="Reset filters">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Results --}}
        <div id="results-container">
        @if(isset($employees))
            <div class="mt-4 mb-4">
                <div class="app-card">
                    <div class="card-header-custom">
                        <span class="result-count">
                            <i class="bi bi-people"></i> {{ count($employees) }} employee{{ count($employees) !== 1 ? 's' : '' }} found
                        </span>

                        @if(isset($filters['department_id']) && $filters['department_id'])
                            <a href="{{ route('employees.download-department', $filters['department_id']) }}"
                               class="btn btn-success-custom">
                                <i class="bi bi-file-earmark-zip me-1"></i> ទាញយកតាមអគ្គនាយកដ្ឋាន
                            </a>
                        @endif
                    </div>

                    @if(count($employees) > 0)
                        <div class="table-responsive">
                            <table class="table-custom table">
                                <thead>
                                    <tr>
                                        <th style="width:60px">#</th>
                                        <th>គោត្តនាម និងនាម</th>
                                        <th>ភេទ</th>
                                        <th>អគ្គនាយកដ្ឋាន</th>
                                        <th style="width:160px">រូបថត</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $avatarColors = ['#4f46e5','#7c3aed','#2563eb','#0891b2','#059669','#d97706','#dc2626','#db2777']; @endphp
                                    @foreach($employees as $i => $emp)
                                        <tr>
                                            <td class="text-muted fw-medium">{{ $i + 1 }}</td>
                                            <td>
                                                <div class="emp-name-group">
                                                    <span class="emp-avatar" style="background:{{ $avatarColors[$i % count($avatarColors)] }}">
                                                        {{ strtoupper(substr($emp['name'], 0, 1)) }}
                                                    </span>
                                                    <span class="emp-name">{{ $emp['name'] }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-gender {{ $emp['sex'] === 'Male' ? 'badge-male' : 'badge-female' }}">
                                                    <i class="bi {{ $emp['sex'] === 'Male' ? 'bi-gender-male' : 'bi-gender-female' }}"></i>
                                                    {{ $emp['sex'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-dept">
                                                    <i class="bi bi-building"></i>
                                                    {{ $emp['department']['name'] ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($emp['photo'])
                                                    <a href="{{ route('employees.download-photo', $emp['id']) }}" class="photo-link">
                                                        <i class="bi bi-download"></i> ទាញយក
                                                    </a>
                                                @else
                                                    <span class="no-photo"><i class="bi bi-image"></i> គ្មានរូបថត</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="bi bi-person-slash"></i></div>
                            <h5>No employees found</h5>
                            <p>Try adjusting your search or filter criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            {{-- Initial state before search --}}
            <div class="mt-4 mb-4">
                <div class="app-card">
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-search"></i></div>
                        <h5>Search for employees</h5>
                        <p>Use the filters above to find employees by name or department.</p>
                    </div>
                </div>
            </div>
        @endif
        </div>
    </div>
    
    {{-- Footer --}}
    <footer class="app-footer">
        <div class="container">
            &copy; {{ date('Y') }} នាយកដ្ឋានបុគ្គលិក នៃអគ្គលេខាធិការដ្ឋាន &middot; សិទ្ធិគ្រប់យ៉ាងបានរក្សា។ &middot; <a href="
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        const nameInput = document.getElementById('name');
        const deptSelect = document.getElementById('department_id');
        const resultsContainer = document.getElementById('results-container');
        const searchForm = document.getElementById('search-form');
        const avatarColors = ['#4f46e5','#7c3aed','#2563eb','#0891b2','#059669','#d97706','#dc2626','#db2777'];
        let debounceTimer;

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchEmployees();
        });

        nameInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchEmployees, 300);
        });

        deptSelect.addEventListener('change', fetchEmployees);

        function fetchEmployees() {
            const params = new URLSearchParams();
            const name = nameInput.value.trim();
            const deptId = deptSelect.value;

            if (name) params.append('name', name);
            if (deptId) params.append('department_id', deptId);

            fetch('{{ route("employees.ajax-search") }}?' + params.toString())
                .then(r => r.json())
                .then(employees => renderResults(employees, deptId))
                .catch(() => {
                    resultsContainer.innerHTML = `
                        <div class="mt-4 mb-4"><div class="app-card"><div class="empty-state">
                            <div class="empty-state-icon"><i class="bi bi-exclamation-triangle"></i></div>
                            <h5>Error fetching employees</h5>
                            <p>Please check your connection and try again.</p>
                        </div></div></div>`;
                });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function renderResults(employees, deptId) {
            if (!employees || employees.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="mt-4 mb-4"><div class="app-card">
                        <div class="card-header-custom">
                            <span class="result-count"><i class="bi bi-people"></i> 0 employees found</span>
                        </div>
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="bi bi-person-slash"></i></div>
                            <h5>No employees found</h5>
                            <p>Try adjusting your search or filter criteria.</p>
                        </div>
                    </div></div>`;
                return;
            }

            let downloadBtn = '';
            if (deptId) {
                downloadBtn = `<a href="/download-department/${encodeURIComponent(deptId)}" class="btn btn-success-custom">
                    <i class="bi bi-file-earmark-zip me-1"></i> ទាញយកតាមអគ្គនាយកដ្ឋាន</a>`;
            }

            let rows = '';
            employees.forEach(function(emp, i) {
                const color = avatarColors[i % avatarColors.length];
                const initial = emp.name ? escapeHtml(emp.name.charAt(0).toUpperCase()) : '?';
                const name = escapeHtml(emp.name || '');
                const sex = escapeHtml(emp.sex || '');
                const isMale = emp.sex === 'Male';
                const deptName = emp.department ? escapeHtml(emp.department.name) : 'N/A';
                const photoCell = emp.photo
                    ? `<a href="/download-photo/${encodeURIComponent(emp.id)}" class="photo-link"><i class="bi bi-download"></i> ទាញយក</a>`
                    : `<span class="no-photo"><i class="bi bi-image"></i> គ្មានរូបថត</span>`;

                rows += `<tr>
                    <td class="text-muted fw-medium">${i + 1}</td>
                    <td><div class="emp-name-group">
                        <span class="emp-avatar" style="background:${color}">${initial}</span>
                        <span class="emp-name">${name}</span>
                    </div></td>
                    <td><span class="badge-gender ${isMale ? 'badge-male' : 'badge-female'}">
                        <i class="bi ${isMale ? 'bi-gender-male' : 'bi-gender-female'}"></i> ${sex}
                    </span></td>
                    <td><span class="badge-dept"><i class="bi bi-building"></i> ${deptName}</span></td>
                    <td>${photoCell}</td>
                </tr>`;
            });

            resultsContainer.innerHTML = `
                <div class="mt-4 mb-4"><div class="app-card">
                    <div class="card-header-custom">
                        <span class="result-count"><i class="bi bi-people"></i> ${employees.length} employee${employees.length !== 1 ? 's' : ''} found</span>
                        ${downloadBtn}
                    </div>
                    <div class="table-responsive">
                        <table class="table-custom table">
                            <thead><tr>
                                <th style="width:60px">#</th>
                                <th>គោត្តនាម និងនាម</th>
                                <th>ភេទ</th>
                                <th>អគ្គនាយកដ្ឋាន</th>
                                <th style="width:160px">រូបថត</th>
                            </tr></thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                </div></div>`;
        }
    })();
    </script>
</body>
</html>
