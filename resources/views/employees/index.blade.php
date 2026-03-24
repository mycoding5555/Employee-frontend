@extends('layout.main')

@section('title', 'បោះពុម្ភរូបថត')
@section('page-title', 'បោះពុម្ភរូបថត')
@section('page-subtitle', 'ស្វែងរក និងទាញយករូបថតបុគ្គលិក')

@section('content')
        {{-- Search Section --}}
        <div class="search-section">
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
                            <i class="bi bi-people"></i> {{ $employees->total() }} បុគ្គលិក{{ $employees->total() !== 1 ? '' : '' }}រកឃើញ
                        </span>

                        @if(isset($filters['department_id']) && $filters['department_id'])
                            <a href="{{ route('employees.download-department', $filters['department_id']) }}"
                               class="btn btn-success-custom">
                                <i class="bi bi-file-earmark-zip me-1"></i> ទាញយកតាមអគ្គនាយកដ្ឋាន
                            </a>
                        @endif
                    </div>

                    @if($employees->total() > 0)
                        <div class="table-responsive">
                            <table class="table-custom table">
                                <thead>
                                    <tr>
                                        <th style="width:60px">អត្តលេខ</th>
                                        <th>គោត្តនាម និងនាម</th>
                                        <th>ភេទ</th>
                                        <th>អគ្គនាយកដ្ឋាន</th>
                                        <th style="width:160px">ទាញយករូបថត</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $avatarColors = ['#4f46e5','#7c3aed','#2563eb','#0891b2','#059669','#d97706','#dc2626','#db2777']; @endphp
                                    @foreach($employees as $i => $emp)
                                        <tr>
                                            <td class="text-muted fw-medium">{{ $employees->firstItem() + $i }}</td>
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

                        {{-- Pagination --}}
                        @if($employees->hasPages())
                        <div class="pagination-wrapper">
                            <div class="pagination-info">
                                បង្ហាញ {{ $employees->firstItem() }}-{{ $employees->lastItem() }} នៃ {{ $employees->total() }}
                            </div>
                            <div class="pagination-controls">
                                {{ $employees->links() }}
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="bi bi-person-slash"></i></div>
                            <h5>រកមិនឃើញបុគ្គលិក</h5>
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
    @endsection

    @push('scripts')
    <script>
    (function() {
        const nameInput = document.getElementById('name');
        const deptSelect = document.getElementById('department_id');
        const resultsContainer = document.getElementById('results-container');
        const searchForm = document.getElementById('search-form');
        const avatarColors = ['#4f46e5','#7c3aed','#2563eb','#0891b2','#059669','#d97706','#dc2626','#db2777'];
        const perPage = 9;
        let debounceTimer;
        let allEmployees = [];
        let currentPage = 1;
        let currentDeptId = '';

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            fetchEmployees();
        });

        nameInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() { currentPage = 1; fetchEmployees(); }, 300);
        });

        deptSelect.addEventListener('change', function() { currentPage = 1; fetchEmployees(); });

        function fetchEmployees() {
            const params = new URLSearchParams();
            const name = nameInput.value.trim();
            currentDeptId = deptSelect.value;

            if (name) params.append('name', name);
            if (currentDeptId) params.append('department_id', currentDeptId);

            fetch('{{ route("employees.ajax-search") }}?' + params.toString())
                .then(r => r.json())
                .then(function(employees) {
                    allEmployees = employees || [];
                    renderPage();
                })
                .catch(() => {
                    resultsContainer.innerHTML = `
                        <div class="mt-4 mb-4"><div class="app-card"><div class="empty-state">
                            <div class="empty-state-icon"><i class="bi bi-exclamation-triangle"></i></div>
                            <h5>កំហុសក្នុងការទាញយកទិន្នន័យ</h5>
                            <p>សូមពិនិត្យការតភ្ជាប់ហើយព្យាយាមម្តងទៀត។</p>
                        </div></div></div>`;
                });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function renderPage() {
            const total = allEmployees.length;
            const totalPages = Math.ceil(total / perPage);
            if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
            const start = (currentPage - 1) * perPage;
            const pageItems = allEmployees.slice(start, start + perPage);

            if (total === 0) {
                resultsContainer.innerHTML = `
                    <div class="mt-4 mb-4"><div class="app-card">
                        <div class="card-header-custom">
                            <span class="result-count"><i class="bi bi-people"></i> 0 បុគ្គលិករកឃើញ</span>
                        </div>
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="bi bi-person-slash"></i></div>
                            <h5>រកមិនឃើញបុគ្គលិក</h5>
                            <p>សូមកែប្រែលក្ខខណ្ឌស្វែងរក។</p>
                        </div>
                    </div></div>`;
                return;
            }

            let downloadBtn = '';
            if (currentDeptId) {
                downloadBtn = `<a href="/employees/download-department/${encodeURIComponent(currentDeptId)}" class="btn btn-success-custom">
                    <i class="bi bi-file-earmark-zip me-1"></i> ទាញយកតាមអគ្គនាយកដ្ឋាន</a>`;
            }

            let rows = '';
            pageItems.forEach(function(emp, i) {
                const globalIndex = start + i + 1;
                const color = avatarColors[(start + i) % avatarColors.length];
                const initial = emp.name ? escapeHtml(emp.name.charAt(0).toUpperCase()) : '?';
                const name = escapeHtml(emp.name || '');
                const sex = escapeHtml(emp.sex || '');
                const isMale = emp.sex === 'Male';
                const deptName = emp.department ? escapeHtml(emp.department.name) : 'N/A';
                const photoCell = emp.photo
                    ? `<a href="/employees/download-photo/${encodeURIComponent(emp.id)}" class="photo-link"><i class="bi bi-download"></i> ទាញយក</a>`
                    : `<span class="no-photo"><i class="bi bi-image"></i> គ្មានរូបថត</span>`;

                rows += `<tr>
                    <td class="text-muted fw-medium">${globalIndex}</td>
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

            let paginationHtml = '';
            if (totalPages > 1) {
                const firstItem = start + 1;
                const lastItem = Math.min(start + perPage, total);
                paginationHtml = `<div class="pagination-wrapper">
                    <div class="pagination-info">បង្ហាញ ${firstItem}-${lastItem} នៃ ${total}</div>
                    <div class="pagination-controls"><nav><ul class="pagination">`;

                paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">&laquo;</a></li>`;

                let startP = Math.max(1, currentPage - 2);
                let endP = Math.min(totalPages, currentPage + 2);
                if (startP > 1) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                    if (startP > 2) paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
                for (let p = startP; p <= endP; p++) {
                    paginationHtml += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
                }
                if (endP < totalPages) {
                    if (endP < totalPages - 1) paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
                }

                paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">&raquo;</a></li>`;
                paginationHtml += `</ul></nav></div></div>`;
            }

            resultsContainer.innerHTML = `
                <div class="mt-4 mb-4"><div class="app-card">
                    <div class="card-header-custom">
                        <span class="result-count"><i class="bi bi-people"></i> ${total} បុគ្គលិករកឃើញ</span>
                        ${downloadBtn}
                    </div>
                    <div class="table-responsive">
                        <table class="table-custom table">
                            <thead><tr>
                                <th style="width:60px">អត្តលេខ</th>
                                <th>គោត្តនាម និងនាម</th>
                                <th>ភេទ</th>
                                <th>អគ្គនាយកដ្ឋាន</th>
                                <th style="width:160px">ទាញយករូបថត</th>
                            </tr></thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                    ${paginationHtml}
                </div></div>`;

            // Attach pagination click handlers
            resultsContainer.querySelectorAll('.page-link[data-page]').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.getAttribute('data-page'));
                    if (page >= 1 && page <= totalPages) {
                        currentPage = page;
                        renderPage();
                        resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        }
    })();
    </script>
    @endpush
