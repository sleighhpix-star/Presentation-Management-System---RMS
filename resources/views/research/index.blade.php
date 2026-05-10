@extends('layouts.app')
@section('title','Research Records')
@section('page-title','Research Records')

@push('styles')
<style>
.tbl-wrap {
    overflow: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
    max-height: calc(100vh - 280px);
    position: relative;
}
/* Sticky header inside the scrollable wrapper */
.data-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}
.data-table {
    border-collapse: separate;
    border-spacing: 0;
    min-width: max-content;
}
.data-table th,
.data-table td {
    white-space: nowrap;
    padding: 10px 14px;
    vertical-align: middle;
    font-size: 13px;
}

/* ALL thead cells get ink background */
.data-table thead th {
    background: var(--ink, #0a0f1e);
    color: rgba(255,255,255,.75);
    z-index: 2;
}

/* Sticky LEFT */
.data-table th:first-child,
.data-table td:first-child {
    position: sticky;
    left: 0;
    z-index: 3;
    box-shadow: 2px 0 6px rgba(0,0,0,.08);
}
.data-table thead th:first-child { z-index: 5; }
.data-table tbody td:first-child  { background: #fff; border-right: 2px solid var(--border); }
.data-table tbody tr:hover td:first-child { background: #f8faff; }

/* Sticky RIGHT */
.data-table th:last-child,
.data-table td:last-child {
    position: sticky;
    right: 0;
    z-index: 3;
    box-shadow: -2px 0 6px rgba(0,0,0,.1);
    min-width: 110px;
}
.data-table thead th:last-child { z-index: 5; }
.data-table tbody td:last-child  { background: #fff; border-left: 2px solid var(--border); }
.data-table tbody tr:hover td:last-child { background: #f8faff; }

/* Body rows */
.data-table tbody td { border-bottom: 1px solid var(--border); background: #fff; }
.data-table tbody tr:hover td { background: #f8faff; }
.data-table tbody tr:last-child td { border-bottom: none; }

/* Wrapping columns */
.data-table td.col-title   { min-width: 220px; max-width: 250px; white-space: normal; line-height: 1.4; }
.data-table td.col-author  { min-width: 160px; max-width: 180px; white-space: normal; line-height: 1.4; }
.data-table td.col-event   { min-width: 180px; max-width: 220px; white-space: normal; line-height: 1.4; }
.data-table td.col-theme   { min-width: 160px; max-width: 200px; white-space: normal; line-height: 1.4; }
.data-table td.col-venue   { min-width: 160px; max-width: 200px; white-space: normal; line-height: 1.4; }
.data-table td.col-project { min-width: 160px; max-width: 180px; white-space: normal; line-height: 1.4; }
.data-table td.col-college { min-width: 160px; max-width: 180px; white-space: normal; line-height: 1.4; }
</style>
@endpush

@section('content')

{{-- ── Top Bar ──────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <p style="font-size:13px;color:var(--text-muted);margin:0;">
        Showing <strong style="color:var(--ink);">{{ count($records) }}</strong> record(s)
    </p>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('research.create') }}" class="btn btn-navy btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Add Record
        </a>
        <a id="btnExcelFiltered"
           href="{{ route('research.export.excel') }}?{{ http_build_query(request()->all()) }}"
           class="btn btn-sm px-3" style="background:#1d6f42;color:#fff;border-radius:8px;font-weight:600;font-size:13px;"
           title="Download filtered results as Excel">
            <i class="bi bi-file-earmark-excel me-1"></i> Excel
            <span id="excelCount" class="badge ms-1" style="background:rgba(255,255,255,.25);font-size:10px;">{{ count($records) }}</span>
        </a>
    </div>
</div>

{{-- ── Filter Bar ───────────────────────────────────── --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('research.index') }}" id="filterForm">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label" style="font-size:11px;">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" style="background:var(--surface);"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Title, reporter, authors, event…"
                           value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label" style="font-size:11px;">Year</label>
                <select name="year" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ ($filters['year']??'')==$y?'selected':'' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label" style="font-size:11px;">Quarter</label>
                <select name="quarter" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($quarters as $q)
                        <option value="{{ $q }}" {{ ($filters['quarter']??'')==$q?'selected':'' }}>{{ $q }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label" style="font-size:11px;">Constituent</label>
                <select name="constituent" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="__empty__" {{ ($filters['constituent']??'')=='__empty__'?'selected':'' }} style="color:#b91c1c;">(Empty)</option>
                    @foreach($constituents as $c)
                        <option value="{{ $c }}" {{ ($filters['constituent']??'')==$c?'selected':'' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label" style="font-size:11px;">Source of Fund</label>
                <select name="source_of_fund" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="__empty__" {{ ($filters['source_of_fund']??'')=='__empty__'?'selected':'' }} style="color:#b91c1c;">(Empty)</option>
                    @foreach($funds as $f)
                        <option value="{{ $f }}" {{ ($filters['source_of_fund']??'')==$f?'selected':'' }}>{{ $f }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label" style="font-size:11px;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="__empty__" {{ ($filters['status']??'')=='__empty__'?'selected':'' }} style="color:#b91c1c;">(Empty)</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ ($filters['status']??'')==$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label" style="font-size:11px;">Classification</label>
                <select name="classification" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="__empty__" {{ ($filters['classification']??'')=='__empty__'?'selected':'' }} style="color:#b91c1c;">(Empty)</option>
                    @foreach($classifications as $cl)
                        <option value="{{ $cl }}" {{ ($filters['classification']??'')==$cl?'selected':'' }}>{{ $cl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label" style="font-size:11px;">Pres. Support</label>
                <select name="presentation_support" class="form-select form-select-sm">
                    <option value="">All</option>
                    @php
                        $fixedOpts = ['Yes','No','N/A'];
                        $amountOpts = collect($presSupports)->filter(fn($p) => !in_array(trim($p), $fixedOpts) && trim($p) !== '')->values();
                    @endphp
                    @foreach($fixedOpts as $p)
                        <option value="{{ $p }}" {{ ($filters['presentation_support']??'')==$p?'selected':'' }}>{{ $p }}</option>
                    @endforeach
                    @if($amountOpts->count())
                        <option disabled>── Amounts ──</option>
                        @foreach($amountOpts as $p)
                            <option value="{{ $p }}" {{ ($filters['presentation_support']??'')==$p?'selected':'' }}>{{ $p }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label" style="font-size:11px;">SDG</label>
                <select name="sdg" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($sdgList as $sdg)
                        <option value="{{ $sdg }}" {{ ($filters['sdg']??'')==$sdg?'selected':'' }}>{{ $sdg }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label" style="font-size:11px;">Date From</label>
                <input type="date" name="date_from_filter" class="form-control form-control-sm"
                       value="{{ $filters['date_from_filter'] ?? '' }}"
                       title="Filter records where date starts on or after this date">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label" style="font-size:11px;">Date To</label>
                <input type="date" name="date_to_filter" class="form-control form-control-sm"
                       value="{{ $filters['date_to_filter'] ?? '' }}"
                       title="Filter records where date ends on or before this date">
            </div>
            <div class="col-12 col-md-auto d-flex align-items-end gap-2">
                <div id="filterSpinner" style="display:none;align-items:center;gap:6px;color:var(--text-muted);font-size:12.5px;">
                    <div class="spinner-border spinner-border-sm" style="color:var(--navy);width:14px;height:14px;"></div>
                    <span>Filtering…</span>
                </div>
                <button type="submit" class="btn btn-navy btn-sm px-3" onclick="submit()">
                    <i class="bi bi-search me-1"></i> Search
                </button>
                @if(array_filter($filters))
                <a href="{{ route('research.index') }}" class="btn btn-sm btn-outline-secondary px-3" title="Clear all filters">
                    <i class="bi bi-x-lg me-1"></i> Clear
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- ── Table ────────────────────────────────────────── --}}
<div class="card">
    <div class="card-body p-0">
        <div class="tbl-wrap">
            <table class="table data-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Media</th>
                        <th>Year</th>
                        <th>Quarter</th>
                        <th>Constituent</th>
                        <th>Research Title</th>
                        <th>Reporter</th>
                        <th>College / Campus</th>
                        <th>Title of Project</th>
                        <th>Author/s</th>
                        <th>Source of Fund</th>
                        <th>Status</th>
                        <th>SDG</th>
                        <th>Pres. Support</th>
                        <th>Title of Event</th>
                        <th>Theme</th>
                        <th>Date / Period</th>
                        <th>Venue</th>
                        <th>Classification</th>
                        <th>Organizer</th>
                        <th>Website</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($records as $rec)
                <tr>
                    {{-- # --}}
                    <td>{{ $loop->iteration }}</td>

                    {{-- Media --}}
                    <td>
                        @if($rec->photo_url)
                            @php
                                $mu = $rec->photo_url;
                                $mIsImg = preg_match('/\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i', $mu);
                                $mIsVid = preg_match('/\.(mp4|mov|avi|webm)(\?.*)?$/i', $mu);
                                $mIsMedia = $mIsImg || $mIsVid;
                            @endphp
                            @if($mIsMedia)
                                <button onclick="openMediaModal('{{ $mu }}', '{{ $mIsImg ? 'image' : 'video' }}')"
                                        title="View media"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:#f0fdfa;border-radius:8px;color:var(--teal);border:1px solid #5eead4;cursor:pointer;">
                                    <i class="bi bi-{{ $mIsImg ? 'image-fill' : 'play-circle-fill' }}" style="font-size:16px;"></i>
                                </button>
                            @else
                                <a href="{{ $mu }}" target="_blank" rel="noopener"
                                   title="{{ $mu }}"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:#f0fdfa;border-radius:8px;color:var(--teal);border:1px solid #5eead4;">
                                    <i class="bi bi-link-45deg" style="font-size:16px;"></i>
                                </a>
                            @endif
                        @else
                            <span style="color:var(--text-muted);font-size:11px;">—</span>
                        @endif
                    </td>

                    {{-- Year --}}
                    <td>
                        <span class="pill b-proposed" style="font-size:10.5px;padding:2px 8px;">{{ $rec->year }}</span>
                    </td>

                    {{-- Quarter --}}
                    <td>{{ $rec->quarter }}</td>

                    {{-- Constituent --}}
                    <td>{{ $rec->constituent }}</td>

                    {{-- Research Title --}}
                    <td class="col-title">
                        <a href="{{ route('research.show',$rec) }}"
                           style="color:var(--navy);font-weight:600;text-decoration:none;"
                           title="{{ $rec->research_title }}"
                           data-bs-toggle="tooltip" data-bs-placement="top">
                            {{ Str::limit($rec->research_title, 60) }}
                        </a>
                    </td>

                    {{-- Reporter --}}
                    <td>{{ $rec->reporter }}</td>

                    {{-- College / Campus --}}
                    <td class="col-college" title="{{ $rec->college_campus }}" data-bs-toggle="tooltip" data-bs-placement="top">{{ Str::limit($rec->college_campus, 40) }}</td>

                    {{-- Title of Project --}}
                    <td class="col-project" title="{{ $rec->title_of_project }}" data-bs-toggle="tooltip" data-bs-placement="top">{{ $rec->title_of_project ? Str::limit($rec->title_of_project, 40) : '—' }}</td>

                    {{-- Author/s --}}
                    <td class="col-author" title="{{ $rec->authors }}" data-bs-toggle="tooltip" data-bs-placement="top">{{ Str::limit($rec->authors, 40) }}</td>

                    {{-- Source of Fund --}}
                    <td>{{ $rec->source_of_fund ?: '—' }}</td>

                    {{-- Status --}}
                    <td>@include('research.partials.status-badge',['status'=>$rec->status])</td>

                    {{-- SDG --}}
                    <td style="min-width:120px;">
                        @foreach(array_slice($rec->sdg ?? [], 0, 2) as $s)
                            <span class="sdg-chip">{{ explode(' – ', $s)[0] }}</span>
                        @endforeach
                        @if(count($rec->sdg ?? []) > 2)
                            <span style="font-size:10.5px;color:var(--text-muted);">+{{ count($rec->sdg) - 2 }}</span>
                        @endif
                    </td>

                    {{-- Presentation Support --}}
                    <td>
                        @php
                            $pmap = [
                                'Yes' => ['bg'=>'#d1fae5','c'=>'#065f46'],
                                'No'  => ['bg'=>'#fee2e2','c'=>'#b91c1c'],
                                'N/A' => ['bg'=>'#f3f4f6','c'=>'#374151'],
                            ];
                            $pm = $pmap[$rec->presentation_support] ?? $pmap['N/A'];
                        @endphp
                        <span style="background:{{ $pm['bg'] }};color:{{ $pm['c'] }};font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;white-space:nowrap;">
                            {{ $rec->presentation_support }}
                        </span>
                    </td>

                    {{-- Title of Event --}}
                    <td class="col-event" title="{{ $rec->title_of_event }}" data-bs-toggle="tooltip" data-bs-placement="top">{{ $rec->title_of_event ? Str::limit($rec->title_of_event, 50) : '—' }}</td>

                    {{-- Theme --}}
                    <td class="col-theme" title="{{ $rec->theme }}" data-bs-toggle="tooltip" data-bs-placement="top">{{ $rec->theme ? Str::limit($rec->theme, 40) : '—' }}</td>

                    {{-- Date / Period --}}
                    <td style="font-size:12px;white-space:nowrap;">
                        {{ $rec->date_range ?? '—' }}
                    </td>

                    {{-- Venue --}}
                    <td class="col-venue" title="{{ $rec->venue }}" data-bs-toggle="tooltip" data-bs-placement="top">{{ $rec->venue ? Str::limit($rec->venue, 40) : '—' }}</td>

                    {{-- Classification --}}
                    <td>
                        @if($rec->classification)
                            @php $cmap=['International'=>'b-intl','National'=>'b-natl','Regional'=>'b-regl','Local'=>'b-local','Institutional'=>'b-inst','Multidisciplinary Research Conference'=>'b-multi']; @endphp
                            <span class="pill {{ $cmap[$rec->classification] ?? '' }}" style="font-size:11px;">
                                {{ $rec->classification }}
                            </span>
                        @else
                            <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>

                    {{-- Organizer --}}
                    <td>{{ $rec->organizer_name ?: ($rec->organizer_email ?: '—') }}</td>

                    {{-- Website --}}
                    <td>
                        @if($rec->website)
                            @if(str_starts_with($rec->website, 'http'))
                                <a href="{{ $rec->website }}" target="_blank" rel="noopener" style="color:var(--navy);">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ Str::limit($rec->website, 28) }}
                                </a>
                            @else
                                {{ Str::limit($rec->website, 28) }}
                            @endif
                        @else —
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('research.show',$rec) }}"
                               class="btn-icon" style="background:#e8f0fb;color:var(--navy);" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('research.edit',$rec) }}"
                               class="btn-icon" style="background:#fef9c3;color:#92400e;" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('research.destroy',$rec) }}" id="del-{{ $rec->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-icon"
                                        style="background:#fee2e2;color:#b91c1c;" title="Delete"
                                        onclick="confirmDelete('del-{{ $rec->id }}', '{{ addslashes(Str::limit($rec->research_title ?? 'this record', 50)) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="23" class="text-center py-5" style="color:var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:10px;opacity:.4;"></i>
                        No records found matching your filters.
                        <a href="{{ route('research.create') }}" style="color:var(--navy);">Add one now →</a>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        </div><!-- /tbl-wrap -->
    </div>
</div>


@push('scripts')
<script>
// Initialize Bootstrap tooltips
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el, { trigger: 'hover', boundary: 'window' });
});
</script>
<script>
(function () {
    const form       = document.getElementById('filterForm');
    const spinner    = document.getElementById('filterSpinner');
    const excelBtn   = document.getElementById('btnExcelFiltered');
    const excelBase  = '{{ route("research.export.excel") }}';
    let   timer      = null;

    // Build query string from current form values
    function getQueryString() {
        const data = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, value] of data.entries()) {
            if (value.trim() !== '') params.append(key, value);
        }
        return params.toString();
    }

    // Update export button hrefs to match current filters
    function syncExportLinks() {
        const qs = getQueryString();
        if (excelBtn) excelBtn.href = qs ? excelBase + '?' + qs : excelBase;
    }

    function submit() {
        syncExportLinks();
        if (spinner) spinner.style.display = 'flex';
        form.submit();
    }

    // Sync immediately on page load (in case filters are pre-filled)
    syncExportLinks();

    // Search box — debounce 600ms, restore cursor after reload
    const searchInput = form.querySelector('input[name="search"]');

    // Auto-focus search if it has a value and no cursor was saved
    if (searchInput && searchInput.value && !sessionStorage.getItem('searchCursor')) {
        searchInput.focus();
        const len = searchInput.value.length;
        searchInput.setSelectionRange(len, len);
    }

    if (searchInput) {
        // Submit on Enter key
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submit();
            }
        });
    }
})();
</script>
@endpush

{{-- ── Media Lightbox Modal ─────────────────────── --}}
<div id="mediaModal" onclick="closeMediaModal()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:99999;align-items:center;justify-content:center;cursor:zoom-out;">
    <div onclick="event.stopPropagation()" style="position:relative;max-width:90vw;max-height:90vh;">
        <button onclick="closeMediaModal()"
                style="position:absolute;top:-40px;right:0;background:none;border:none;color:#fff;font-size:28px;cursor:pointer;line-height:1;">
            <i class="bi bi-x-circle-fill"></i>
        </button>
        <img id="modalImg" src="" alt=""
             style="display:none;max-width:90vw;max-height:85vh;border-radius:10px;object-fit:contain;">
        <video id="modalVideo" controls
               style="display:none;max-width:90vw;max-height:85vh;border-radius:10px;background:#000;">
        </video>
    </div>
</div>
<script>
function openMediaModal(url, type) {
    var modal = document.getElementById('mediaModal');
    var img   = document.getElementById('modalImg');
    var video = document.getElementById('modalVideo');
    img.style.display   = 'none';
    video.style.display = 'none';
    if (type === 'image') {
        img.src = url;
        img.style.display = 'block';
    } else {
        video.src = url;
        video.style.display = 'block';
    }
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeMediaModal() {
    var video = document.getElementById('modalVideo');
    try { video.pause(); video.src = ''; } catch(e){}
    document.getElementById('mediaModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeMediaModal(); });
</script>

{{-- ── Delete Confirm Modal ─────────────────────── --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:99998;align-items:center;justify-content:center;backdrop-filter:blur(3px);">
    <div style="background:#fff;border-radius:20px;padding:32px 28px 24px;max-width:380px;width:90%;box-shadow:0 24px 60px rgba(0,0,0,.2);text-align:center;animation:delIn .25s cubic-bezier(.34,1.56,.64,1);">
        <div style="width:56px;height:56px;background:#fef2f2;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i class="bi bi-trash3-fill" style="font-size:24px;color:#ef4444;"></i>
        </div>
        <div style="font-family:'Outfit',sans-serif;font-size:17px;font-weight:700;color:#0f172a;margin-bottom:8px;">Delete Record?</div>
        <div id="deleteModalMsg" style="font-size:13px;color:#64748b;margin-bottom:24px;line-height:1.5;"></div>
        <div style="display:flex;gap:10px;">
            <button onclick="closeDeleteModal()"
                    style="flex:1;padding:11px;border-radius:12px;border:1.5px solid #e2e8f0;background:#fff;font-size:13.5px;font-weight:600;color:#475569;cursor:pointer;">
                Cancel
            </button>
            <button id="deleteConfirmBtn" onclick="submitDelete()"
                    style="flex:1;padding:11px;border-radius:12px;border:none;background:#ef4444;font-size:13.5px;font-weight:700;color:#fff;cursor:pointer;">
                Yes, Delete
            </button>
        </div>
    </div>
</div>
<style>
@keyframes delIn {
    from { opacity:0; transform:scale(.9) translateY(10px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}
</style>
<script>
var _deleteFormId = null;
function confirmDelete(formId, title) {
    _deleteFormId = formId;
    document.getElementById('deleteModalMsg').textContent = 'This will permanently delete "' + title + '". This action cannot be undone.';
    var m = document.getElementById('deleteModal');
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function submitDelete() {
    if (_deleteFormId) document.getElementById(_deleteFormId).submit();
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    document.body.style.overflow = '';
    _deleteFormId = null;
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
document.addEventListener('keydown', function(e) { if(e.key==='Escape') closeDeleteModal(); });
</script>

@endsection