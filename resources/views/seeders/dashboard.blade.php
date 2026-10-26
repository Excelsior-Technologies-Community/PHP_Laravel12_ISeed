@extends('layouts.app')

@section('title', 'iSeed Enterprise Studio & Seeder Control Center')

@section('content')
<div class="row g-3 mb-4">
    <!-- Top Stats Cards -->
    <div class="col-md-3 col-sm-6">
        <div class="card bg-white p-3 border-start border-primary border-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Discovered Tables</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_tables'] }}</h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                    <i class="bi bi-database fs-4"></i>
                </div>
            </div>
            <small class="text-muted mt-2">Active database entities</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card bg-white p-3 border-start border-success border-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Seeders Generated</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $stats['total_generated'] }}</h3>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-circle">
                    <i class="bi bi-file-earmark-code fs-4"></i>
                </div>
            </div>
            <small class="text-muted mt-2">{{ number_format($stats['total_records_seeded']) }} total records tracked</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card bg-white p-3 border-start border-info border-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Executed Runs</span>
                    <h3 class="fw-bold mb-0 text-info">{{ $stats['total_executed'] }}</h3>
                </div>
                <div class="p-3 bg-info-subtle text-info rounded-circle">
                    <i class="bi bi-play-circle fs-4"></i>
                </div>
            </div>
            <small class="text-muted mt-2">Database seeds applied</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card bg-white p-3 border-start border-warning border-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">GDPR Masked</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $stats['masked_seeders'] }}</h3>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-circle">
                    <i class="bi bi-shield-check fs-4"></i>
                </div>
            </div>
            <small class="text-muted mt-2">Sanitized against privacy leaks</small>
        </div>
    </div>
</div>

<!-- Main Seeder Generator Form -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-diagram-3-fill text-primary me-2"></i> Multi-Table & Dependency-Aware Seeding Studio
            </h5>
            <small class="text-muted">Batch select tables. Tables are topologically sorted to satisfy foreign key relationships before seeding.</small>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleSelectAll()">
                <i class="bi bi-check2-all me-1"></i> Toggle All
            </button>
            <form method="POST" action="{{ route('seeder.generate') }}" style="display:inline;" onsubmit="return confirm('Generate seeders for ALL candidate database tables in dependency order?');">
                @csrf
                <input type="hidden" name="seed_all_tables" value="1">
                <input type="hidden" name="mask_sensitive_data" value="1">
                <button type="submit" class="btn btn-success btn-sm shadow-sm">
                    <i class="bi bi-lightning-charge-fill me-1"></i> 1-Click Seed All Tables (GDPR Safe)
                </button>
            </form>
        </div>
    </div>

    <div class="card-body p-4">
        <form method="POST" action="{{ route('seeder.generate') }}" id="batchSeederForm">
            @csrf

            <!-- Table Selection Grid -->
            <label class="form-label fw-bold text-dark mb-2">Select Target Tables to Seed:</label>
            <div class="row g-3 mb-4">
                @forelse($tables as $table)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border p-3 table-selection-card hover-shadow" style="transition: all 0.2s;">
                        <div class="form-check d-flex align-items-start gap-2 mb-0">
                            <input class="form-check-input table-checkbox mt-1" 
                                   type="checkbox" 
                                   name="tables[]" 
                                   value="{{ $table['name'] }}" 
                                   id="tbl_{{ $table['name'] }}">
                            <div class="w-100">
                                <label class="form-check-label fw-bold text-dark d-block cursor-pointer" for="tbl_{{ $table['name'] }}">
                                    <code>{{ $table['name'] }}</code>
                                    <span class="badge bg-secondary-subtle text-secondary border float-end">
                                        {{ number_format($table['count']) }} rows
                                    </span>
                                </label>
                                <small class="text-muted d-block mt-1">
                                    {{ count($table['columns']) }} cols: {{ implode(', ', array_slice($table['columns'], 0, 4)) }}{{ count($table['columns']) > 4 ? '...' : '' }}
                                </small>
                                @if($table['has_dependencies'])
                                    <span class="badge bg-info-subtle text-info border mt-1">
                                        <i class="bi bi-arrow-right-short"></i> Depends on: {{ implode(', ', $table['depends_on']) }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border mt-1">Root Entity</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-warning">No user database tables discovered.</div>
                </div>
                @endforelse
            </div>

            <!-- Privacy & GDPR Masking Section -->
            <div class="p-3 bg-light rounded border mb-4">
                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="mask_sensitive_data" value="1" id="maskSwitch" checked>
                    <label class="form-check-label fw-bold text-dark" for="maskSwitch">
                        <i class="bi bi-shield-lock-fill text-warning me-1"></i> Enable Sensitive Data Masking & GDPR Anonymization
                    </label>
                </div>
                <small class="text-muted ps-4 d-block">
                    Automatically masks sensitive production fields (passwords $\rightarrow$ bcrypt('password'), emails $\rightarrow$ <code>user{id}@example.test</code>, phone numbers, tokens, credit cards) with realistic dummy values while preserving primary & foreign keys.
                </small>
            </div>

            <!-- Advanced Filtering & Large Table Slicing Accordion -->
            <div class="accordion mb-4" id="advancedOptionsAccordion">
                <div class="accordion-item border rounded">
                    <h2 class="accordion-header" id="headingAdvanced">
                        <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdvanced" aria-expanded="false" aria-controls="collapseAdvanced">
                            <i class="bi bi-sliders me-2 text-primary"></i> <strong>Data Slicing, Row Filtering & Large Table Chunking</strong>
                        </button>
                    </h2>
                    <div id="collapseAdvanced" class="accordion-collapse collapse" aria-labelledby="headingAdvanced" data-bs-parent="#advancedOptionsAccordion">
                        <div class="accordion-body bg-light">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Max Records Limit (--max)</label>
                                    <input type="number" name="max_records" class="form-control form-control-sm" placeholder="e.g. 50 (Leave blank for all)" min="1">
                                    <div class="form-text small">Caps output rows to prevent giant files.</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Offset Skip (--skip)</label>
                                    <input type="number" name="skip_records" class="form-control form-control-sm" placeholder="e.g. 10" min="0">
                                    <div class="form-text small">Skip first $N$ records.</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Chunk Size (--chunksize)</label>
                                    <input type="number" name="chunk_size" class="form-control form-control-sm" placeholder="e.g. 100" min="1">
                                    <div class="form-text small">Rows per insert array batch.</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Exclude Columns (--exclude)</label>
                                    <input type="text" name="exclude_columns" class="form-control form-control-sm" placeholder="e.g. remember_token, binary_data">
                                    <div class="form-text small">Comma-separated column names to omit.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">SQL Where Clause Filter (--where)</label>
                                    <input type="text" name="where_clause" class="form-control form-control-sm" placeholder="e.g. status = 'active' AND price > 100">
                                    <div class="form-text small">Custom SQL filter applied prior to dump.</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Order By Column (--orderby)</label>
                                    <input type="text" name="order_by" class="form-control form-control-sm" placeholder="e.g. id or created_at">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Order Direction (--direction)</label>
                                    <select name="order_direction" class="form-select form-select-sm">
                                        <option value="desc">DESC (Latest rows first)</option>
                                        <option value="asc">ASC (Oldest rows first)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="d-flex justify-content-end align-items-center gap-2">
                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                    <i class="bi bi-play-fill me-1"></i> Generate Selected Seeders
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Seeder Backup History Ledger -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-clock-history text-secondary me-2"></i> Seeder Backup History & Code Studio
        </h5>
        <span class="badge bg-secondary-subtle text-secondary border">
            {{ $histories->total() }} Total Entries
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%">Table</th>
                        <th style="width: 25%">Generated Seeder Class</th>
                        <th style="width: 10%">Records</th>
                        <th style="width: 10%">File Size</th>
                        <th style="width: 12%">Status & Privacy</th>
                        <th style="width: 13%">Timestamp</th>
                        <th style="width: 15%" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                    <tr>
                        <td>
                            <code>{{ $history->table_name }}</code>
                        </td>
                        <td>
                            <strong>{{ $history->seeder_name }}</strong>
                            <small class="text-muted d-block font-monospace" style="font-size: 11px;">
                                {{ $history->file_path ?? 'database/seeders/' . $history->seeder_name . '.php' }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ number_format($history->records) }} rows
                            </span>
                        </td>
                        <td>
                            <small class="text-muted font-monospace">{{ $history->file_size_kb }} KB</small>
                        </td>
                        <td>
                            @if($history->status === 'executed')
                                <span class="badge bg-success">
                                    <i class="bi bi-check2"></i> Executed
                                </span>
                            @else
                                <span class="badge bg-secondary">Generated</span>
                            @endif

                            @if($history->masking_applied)
                                <span class="badge bg-warning text-dark border" title="GDPR Data Masking Applied">
                                    <i class="bi bi-shield-check"></i> Masked
                                </span>
                            @endif

                            @if($history->execution_time_ms)
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    Latency: {{ $history->execution_time_ms }} ms
                                </small>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $history->created_at->format('d M Y, H:i') }}</small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                <!-- Inspect Code Studio Button -->
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary" 
                                        title="In-Browser Code Inspector & Editor"
                                        onclick="openCodeStudio('{{ $history->seeder_name }}')">
                                    <i class="bi bi-code-slash"></i> Inspect
                                </button>

                                <!-- Live Diff Button -->
                                <button type="button" 
                                        class="btn btn-sm btn-outline-info" 
                                        title="Compare with Previous Seeder Snapshot"
                                        onclick="openDiffViewer({{ $history->id }}, '{{ $history->seeder_name }}')">
                                    <i class="bi bi-file-diff"></i> Diff
                                </button>

                                <!-- Download Button -->
                                <a href="{{ route('seeder.download', $history->seeder_name) }}" 
                                   class="btn btn-sm btn-outline-secondary" 
                                   title="Download .php File">
                                    <i class="bi bi-download"></i>
                                </a>

                                <!-- Run Seeder Button -->
                                <form method="POST" action="{{ route('seeder.run') }}" style="display:inline" onsubmit="return confirm('Run {{ $history->seeder_name }}? This will execute database inserts.');">
                                    @csrf
                                    <input type="hidden" name="seeder" value="{{ $history->seeder_name }}">
                                    <button class="btn btn-sm btn-warning text-dark" title="Run Database Seed">
                                        <i class="bi bi-play-fill"></i>
                                    </button>
                                </form>

                                <!-- Delete Button -->
                                <form method="POST" action="{{ route('seeder.destroy', $history->id) }}" style="display:inline" onsubmit="return confirm('Delete this seeder file and record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                            No seeder backup history found. Select tables above to generate your first seeds.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($histories->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $histories->links() }}
    </div>
    @endif
</div>

<!-- ========================================== -->
<!-- Feature 3: In-Browser Code Inspector Modal -->
<!-- ========================================== -->
<div class="modal fade" id="codeStudioModal" tabindex="-1" aria-labelledby="codeStudioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-filetype-php fs-3 text-warning"></i>
                    <div>
                        <h5 class="modal-title mb-0" id="codeStudioModalLabel">Seeder Code Inspector</h5>
                        <small class="text-muted font-monospace" id="codeStudioFilePath">database/seeders/...</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary-subtle text-light border" id="codeStudioSize">0 KB</span>
                    <span class="badge bg-secondary-subtle text-light border" id="codeStudioLines">0 lines</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-0">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between align-items-center px-3 py-2 bg-light border-bottom">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-primary" id="btnViewMode" onclick="toggleEditMode(false)">
                            <i class="bi bi-eye me-1"></i> Highlighted View
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btnEditMode" onclick="toggleEditMode(true)">
                            <i class="bi bi-pencil me-1"></i> Quick Edit Mode
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyCodeToClipboard()">
                            <i class="bi bi-clipboard me-1"></i> Copy to Clipboard
                        </button>
                    </div>
                </div>

                <!-- Highlighted Prism Code Viewer -->
                <div id="codeViewContainer" class="p-3 bg-dark" style="max-height: 520px; overflow-y: auto;">
                    <pre class="m-0"><code class="language-php" id="codeStudioPre"></code></pre>
                </div>

                <!-- Edit Textarea -->
                <div id="codeEditContainer" class="p-3 d-none">
                    <textarea class="form-control font-monospace" id="codeStudioTextarea" rows="20" style="font-size: 13px; line-height: 1.5;"></textarea>
                    <div class="alert alert-danger mt-2 d-none" id="syntaxErrorAlert"></div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2">
                <span class="text-muted small me-auto" id="copyNotification"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success btn-sm d-none" id="btnSaveCode" onclick="saveCodeChanges()">
                    <i class="bi bi-floppy me-1"></i> Save Changes to Disk
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- Feature 3: Live Diff Viewer Modal          -->
<!-- ========================================== -->
<div class="modal fade" id="diffModal" tabindex="-1" aria-labelledby="diffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-diff fs-3 text-info"></i>
                    <div>
                        <h5 class="modal-title mb-0" id="diffModalLabel">Historical Seeder Diff Inspector</h5>
                        <small class="text-muted font-monospace" id="diffModalSubtitle">Comparing current vs previous snapshot</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <div id="diffLoading" class="p-4 text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2 mb-0">Computing unified diff...</p>
                </div>

                <div id="diffContent" class="p-3 bg-dark font-monospace small d-none" style="max-height: 520px; overflow-y: auto;">
                    <div class="d-flex justify-content-between align-items-center text-light pb-2 mb-2 border-bottom border-secondary">
                        <span id="diffStats"></span>
                        <span class="small text-muted">Red = Previous Version, Green = Current Version</span>
                    </div>
                    <div id="diffLinesContainer"></div>
                </div>

                <div id="diffEmptyAlert" class="alert alert-info m-3 d-none">
                    <i class="bi bi-info-circle me-1"></i> No prior snapshot exists for this entry. Multiple seeder runs on the same table will display historical row-level diffs here.
                </div>
            </div>

            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Select All Tables Toggle
function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.table-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

// In-Browser Code Inspector
let currentSeederName = '';
let currentCodeRaw = '';

function openCodeStudio(seederName) {
    currentSeederName = seederName;
    const modal = new bootstrap.Modal(document.getElementById('codeStudioModal'));
    
    document.getElementById('codeStudioModalLabel').textContent = seederName + '.php';
    document.getElementById('codeStudioFilePath').textContent = 'database/seeders/' + seederName + '.php';
    document.getElementById('codeStudioPre').textContent = '// Loading seeder file...';
    document.getElementById('syntaxErrorAlert').classList.add('d-none');
    toggleEditMode(false);
    modal.show();

    fetch('{{ url("seeder-code") }}/' + encodeURIComponent(seederName))
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                currentCodeRaw = data.code;
                document.getElementById('codeStudioPre').textContent = data.code;
                document.getElementById('codeStudioTextarea').value = data.code;
                document.getElementById('codeStudioSize').textContent = data.size_kb + ' KB';
                document.getElementById('codeStudioLines').textContent = data.lines + ' lines';
                Prism.highlightElement(document.getElementById('codeStudioPre'));
            } else {
                document.getElementById('codeStudioPre').textContent = '// Error: ' + (data.message || 'File not found');
            }
        })
        .catch(err => {
            document.getElementById('codeStudioPre').textContent = '// Network error: ' + err;
        });
}

function toggleEditMode(isEdit) {
    const viewContainer = document.getElementById('codeViewContainer');
    const editContainer = document.getElementById('codeEditContainer');
    const btnView = document.getElementById('btnViewMode');
    const btnEdit = document.getElementById('btnEditMode');
    const btnSave = document.getElementById('btnSaveCode');

    if (isEdit) {
        viewContainer.classList.add('d-none');
        editContainer.classList.remove('d-none');
        btnView.className = 'btn btn-outline-primary';
        btnEdit.className = 'btn btn-primary';
        btnSave.classList.remove('d-none');
    } else {
        viewContainer.classList.remove('d-none');
        editContainer.classList.add('d-none');
        btnView.className = 'btn btn-primary';
        btnEdit.className = 'btn btn-outline-primary';
        btnSave.classList.add('d-none');
    }
}

function copyCodeToClipboard() {
    const textToCopy = currentCodeRaw || document.getElementById('codeStudioTextarea').value;
    navigator.clipboard.writeText(textToCopy).then(() => {
        const notif = document.getElementById('copyNotification');
        notif.textContent = '✓ Copied to clipboard!';
        setTimeout(() => { notif.textContent = ''; }, 3000);
    });
}

function saveCodeChanges() {
    const updatedCode = document.getElementById('codeStudioTextarea').value;
    const alertBox = document.getElementById('syntaxErrorAlert');
    alertBox.classList.add('d-none');

    fetch('{{ route("seeder.update.code") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            seeder_name: currentSeederName,
            code: updatedCode
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            currentCodeRaw = updatedCode;
            document.getElementById('codeStudioPre').textContent = updatedCode;
            Prism.highlightElement(document.getElementById('codeStudioPre'));
            document.getElementById('codeStudioSize').textContent = data.size_kb + ' KB';
            toggleEditMode(false);
            alert('Seeder file saved successfully!');
        } else {
            alertBox.textContent = data.message || 'Syntax validation failed.';
            alertBox.classList.remove('d-none');
        }
    })
    .catch(err => {
        alertBox.textContent = 'Failed to save: ' + err;
        alertBox.classList.remove('d-none');
    });
}

// Live Diff Inspector
function openDiffViewer(historyId, seederName) {
    const modal = new bootstrap.Modal(document.getElementById('diffModal'));
    document.getElementById('diffModalLabel').textContent = 'Diff: ' + seederName + '.php';
    document.getElementById('diffLoading').classList.remove('d-none');
    document.getElementById('diffContent').classList.add('d-none');
    document.getElementById('diffEmptyAlert').classList.add('d-none');
    modal.show();

    fetch('{{ url("seeder-diff") }}/' + historyId)
        .then(res => res.json())
        .then(data => {
            document.getElementById('diffLoading').classList.add('d-none');
            if (!data.success) {
                document.getElementById('diffEmptyAlert').textContent = data.message;
                document.getElementById('diffEmptyAlert').classList.remove('d-none');
                return;
            }

            const diff = data.diff;
            document.getElementById('diffStats').innerHTML = 
                `<span class="badge bg-success me-1">+${diff.added} added</span> ` +
                `<span class="badge bg-danger">-${diff.removed} removed</span>`;

            const container = document.getElementById('diffLinesContainer');
            container.innerHTML = '';

            diff.lines.forEach(item => {
                const lineDiv = document.createElement('div');
                let cssClass = 'text-light';
                let symbol = ' ';
                if (item.type === 'added') {
                    cssClass = 'diff-added';
                    symbol = '+';
                } else if (item.type === 'removed') {
                    cssClass = 'diff-removed';
                    symbol = '-';
                }

                const lineNum = item.new_no || item.old_no || '';
                lineDiv.className = cssClass;
                lineDiv.innerHTML = `<span class="diff-line-num">${lineNum}</span><span>${symbol} ${escapeHtml(item.line)}</span>`;
                container.appendChild(lineDiv);
            });

            document.getElementById('diffContent').classList.remove('d-none');
        })
        .catch(err => {
            document.getElementById('diffLoading').classList.add('d-none');
            document.getElementById('diffEmptyAlert').textContent = 'Error computing diff: ' + err;
            document.getElementById('diffEmptyAlert').classList.remove('d-none');
        });
}

function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
</script>
@endpush
@endsection
