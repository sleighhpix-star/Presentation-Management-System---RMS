{{--
  Shared form partial for create & edit.
  Variables expected:
    $record  – ResearchRecord instance (or null for create)
    $sdgList – array of SDG strings
--}}
<div class="row g-4">

{{-- ══ SECTION 1: Basic Identifiers ══ --}}
<div class="col-12">
    <div class="section-hdr"><i class="bi bi-info-circle"></i> Basic Information</div>
    <div class="row g-3">
        <div class="col-md-2">
            <label class="form-label">Year</label>
            <select name="year" class="form-select @error('year') is-invalid @enderror">
                <option value="">Select</option>
                @foreach(range(date('Y')+1, date('Y')-10) as $y)
                    <option value="{{ $y }}" {{ old('year', $record->year ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label">Quarter</label>
            <select name="quarter" class="form-select @error('quarter') is-invalid @enderror">
                <option value="">Select</option>
                @foreach(['Q1','Q2','Q3','Q4'] as $q)
                    <option value="{{ $q }}" {{ old('quarter', $record->quarter ?? '') == $q ? 'selected' : '' }}>{{ $q }}</option>
                @endforeach
            </select>
            @error('quarter')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Constituent</label>
            <select name="constituent" class="form-select @error('constituent') is-invalid @enderror">
                <option value="">Select constituent</option>
                @foreach(['Alangilan','Lipa','Malvar','Nasugbu','Pablo Borbon'] as $c)
                    <option value="{{ $c }}" {{ old('constituent', $record->constituent ?? '') == $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
            @error('constituent')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-5">
            <label class="form-label">College / Campus</label>
            <input type="text" name="college_campus" class="form-control @error('college_campus') is-invalid @enderror"
                   value="{{ old('college_campus', $record->college_campus ?? '') }}"
                   placeholder="e.g. College of Engineering">
            @error('college_campus')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- ══ SECTION 2: Research Details ══ --}}
<div class="col-12">
    <div class="section-hdr"><i class="bi bi-journal-text"></i> Research Details</div>
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Research Title</label>
            <input type="text" name="research_title" class="form-control @error('research_title') is-invalid @enderror"
                   value="{{ old('research_title', $record->research_title ?? '') }}"
                   placeholder="Full title of the research">
            @error('research_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Title of Project</label>
            <input type="text" name="title_of_project" class="form-control"
                   value="{{ old('title_of_project', $record->title_of_project ?? '') }}"
                   placeholder="Project name / acronym">
        </div>
        <div class="col-md-4">
            <label class="form-label">Reporter</label>
            <input type="text" name="reporter" class="form-control @error('reporter') is-invalid @enderror"
                   value="{{ old('reporter', $record->reporter ?? '') }}"
                   placeholder="Dr. / Prof. / Engr. Full Name">
            @error('reporter')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-8">
            <label class="form-label">Author/s</label>
            <input type="text" name="authors" class="form-control @error('authors') is-invalid @enderror"
                   value="{{ old('authors', $record->authors ?? '') }}"
                   placeholder="LastName, F., LastName2, F.">
            @error('authors')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- ══ SECTION 3: Funding & Status ══ --}}
<div class="col-12">
    <div class="section-hdr"><i class="bi bi-cash-stack"></i> Funding & Status</div>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Source of Fund</label>
            <select name="source_of_fund" class="form-select @error('source_of_fund') is-invalid @enderror">
                <option value="">Select source</option>
                @foreach(['Externally-Funded','Institutionally-Funded','Non-Funded'] as $f)
                    <option value="{{ $f }}" {{ old('source_of_fund', $record->source_of_fund ?? '') == $f ? 'selected' : '' }}>{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Status of Funded Project</label>
            <select name="status" class="form-select @error('status') is-invalid @enderror">
                @foreach(['Ongoing','Completed'] as $s)
                    <option value="{{ $s }}" {{ old('status', $record->status ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Presentation Support</label>
            <input type="text" name="presentation_support"
                   class="form-control @error('presentation_support') is-invalid @enderror"
                   value="{{ old('presentation_support', $record->presentation_support ?? '') }}"
                   placeholder="Yes / No / N/A or amount e.g. 33,000.00">
            <div style="font-size:10.5px;color:var(--muted);margin-top:3px;">Yes, No, N/A, or support amount</div>
            @error('presentation_support')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="form-label">SDG (Select at least one)</label>
            <select name="sdg[]" class="form-select sdg-select @error('sdg') is-invalid @enderror" multiple>
                @foreach($sdgList as $sdg)
                    <option value="{{ $sdg }}" {{ in_array($sdg, old('sdg', $record->sdg ?? [])) ? 'selected' : '' }}>{{ $sdg }}</option>
                @endforeach
            </select>
            @error('sdg')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- ══ SECTION 4: Event Details ══ --}}
<div class="col-12">
    <div class="section-hdr"><i class="bi bi-calendar-event"></i> Event / Presentation Details</div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Title of Event</label>
            <input type="text" name="title_of_event" class="form-control"
                   value="{{ old('title_of_event', $record->title_of_event ?? '') }}"
                   placeholder="Name of conference, symposium, etc.">
        </div>
        <div class="col-md-6">
            <label class="form-label">Theme</label>
            <input type="text" name="theme" class="form-control"
                   value="{{ old('theme', $record->theme ?? '') }}" placeholder="Event theme or tagline">
        </div>
        <div class="col-md-6">
            <label class="form-label">Date / Period</label>
            @php
                $drFrom = old('date_from', isset($record) && $record->date_from ? $record->date_from->format('Y-m-d') : '');
                $drTo   = old('date_to',   isset($record) && $record->date_to   ? $record->date_to->format('Y-m-d')   : '');
            @endphp
            {{-- Single visible input showing the formatted range --}}
            <div style="position:relative;">
                <input type="text" id="dateRangeDisplay"
                       class="form-control @error('date_from') is-invalid @enderror"
                       placeholder="e.g. February 28 – March 2, 2026"
                       readonly
                       style="cursor:pointer;background:#fff;"
                       value="{{ $drFrom ? (\Carbon\Carbon::parse($drFrom)->format('F j') . ($drTo && $drTo !== $drFrom ? ' – ' . \Carbon\Carbon::parse($drTo)->format('F j, Y') : ', ' . \Carbon\Carbon::parse($drFrom)->format('Y'))) : '' }}">
                <i class="bi bi-calendar3" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;"></i>
            </div>
            {{-- Hidden actual fields submitted to server --}}
            <input type="hidden" name="date_from" id="dateFrom" value="{{ $drFrom }}">
            <input type="hidden" name="date_to"   id="dateTo"   value="{{ $drTo }}">
            @error('date_from')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror

            {{-- Inline date range picker --}}
            <div id="dateRangePicker" style="display:none;position:absolute;z-index:9999;background:#fff;border:1px solid var(--border);border-radius:14px;padding:20px;box-shadow:0 12px 40px rgba(0,0,0,.14);margin-top:6px;min-width:320px;">
                <div style="display:flex;gap:16px;margin-bottom:14px;">
                    <div style="flex:1;">
                        <label style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);display:block;margin-bottom:5px;">From</label>
                        <input type="date" id="pickerFrom" class="form-control form-control-sm">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);display:block;margin-bottom:5px;">To <span style="font-weight:400;text-transform:none;">(optional)</span></label>
                        <input type="date" id="pickerTo" class="form-control form-control-sm">
                    </div>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="button" id="dateRangeApply"
                            style="flex:1;background:var(--teal,#0d9488);color:#fff;border:none;border-radius:8px;padding:8px;font-weight:700;font-size:13px;cursor:pointer;">
                        Apply
                    </button>
                    <button type="button" id="dateRangeClear"
                            style="background:#f1f5f9;color:var(--muted,#64748b);border:none;border-radius:8px;padding:8px 14px;font-weight:600;font-size:13px;cursor:pointer;">
                        Clear
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <label class="form-label">Venue</label>
            <input type="text" name="venue" class="form-control"
                   value="{{ old('venue', $record->venue ?? '') }}" placeholder="Full venue address">
        </div>
        <div class="col-md-4">
            <label class="form-label">Classification</label>
            <select name="classification" class="form-select @error('classification') is-invalid @enderror">
                <option value="">Select classification</option>
                @foreach(['Institutional','International','Multidisciplinary Research Conference','National','Regional'] as $cl)
                    <option value="{{ $cl }}" {{ old('classification', $record->classification ?? '') == $cl ? 'selected' : '' }}>{{ $cl }}</option>
                @endforeach
            </select>
            @error('classification')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- ══ SECTION 5: Organizer ══ --}}
<div class="col-12">
    <div class="section-hdr"><i class="bi bi-person-lines-fill"></i> Organizer Information</div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Organizer Name / Email</label>
            <input type="text" name="organizer_name" class="form-control" autocomplete="off"
                   value="{{ old('organizer_name', $record->organizer_name ?? '') }}"
                   placeholder="Organizer name or email address">
        </div>
        <div class="col-md-4">
            <label class="form-label">Website Link</label>
            <input type="text" name="website" class="form-control" autocomplete="off"
                   value="{{ old('website', $record->website ?? '') }}" placeholder="Optional website or reference">
        </div>
    </div>
</div>

{{-- ══ SECTION 6: Media / Documentation ══ --}}
<div class="col-12">
    <div class="section-hdr"><i class="bi bi-image"></i> Media / Documentation</div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Upload Image / File</label>
            <input type="file" name="photo_file" class="form-control" accept="image/*,video/*,.pdf,.mov,.mp4">
            <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Upload an image or video file directly.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Or Paste a Link</label>
            <input type="text" name="photo"
                   class="form-control @error('photo') is-invalid @enderror"
                   value="{{ old('photo', $record->photo ?? '') }}"
                   placeholder="Google Drive, YouTube, image URL, etc.">
            <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">File upload takes priority over this link.</div>
            @error('photo')
                <div style="color:#b91c1c;font-size:12.5px;margin-top:5px;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        {{-- Current media --}}
        @if(isset($record) && $record && $record->photo)
        <div class="col-12">
        <div class="p-3" style="background:var(--surface);border:1px solid var(--border);border-radius:10px;">
            <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Current Media</div>
            @if(str_starts_with($record->photo_url ?? '', 'http') && preg_match('/\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i', $record->photo_url))
                <img src="{{ $record->photo_url }}" style="max-height:120px;border-radius:8px;border:1px solid var(--border);">
            @else
                <a href="{{ $record->photo_url }}" target="_blank" rel="noopener" style="color:var(--teal);font-size:13px;word-break:break-all;">
                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ Str::limit($record->photo, 60) }}
                </a>
            @endif
            <div class="mt-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remove_photo" id="remove_photo" value="1">
                    <label class="form-check-label" for="remove_photo" style="font-size:12px;color:#b91c1c;font-weight:600;">
                        <i class="bi bi-trash me-1"></i> Remove current media
                    </label>
                </div>
            </div>
        </div>
        </div>
        @endif
    </div>
</div>

</div>{{-- /row --}}

@push('scripts')
<script>
// SDG Select2
$(document).ready(function(){
    $('.sdg-select').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select one or more SDG goals…',
        allowClear: true,
        closeOnSelect: true,
    });
});

// Photo preview
function previewPhoto(e) {
    const file = e.target.files[0];
    if (!file) return;
    const preview = document.getElementById('uploadPreview');
    const prompt  = document.getElementById('uploadPrompt');
    const reader  = new FileReader();
    reader.onload = ev => {
        preview.src = ev.target.result;
        preview.style.display = 'block';
        prompt.style.display  = 'none';
    };
    reader.readAsDataURL(file);
}

// Drag & drop styles
const zone = document.getElementById('uploadZone');
if (zone) {
    zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', ()  => zone.classList.remove('dragover'));
    zone.addEventListener('drop', e => {
        e.preventDefault(); zone.classList.remove('dragover');
        const dt = e.dataTransfer;
        if (dt.files.length) {
            document.getElementById('photoInput').files = dt.files;
            previewPhoto({ target: { files: dt.files } });
        }
    });
}

// ── Date Range Picker ─────────────────────────────────────────
(function() {
    const display   = document.getElementById('dateRangeDisplay');
    const picker    = document.getElementById('dateRangePicker');
    const fromInput = document.getElementById('pickerFrom');
    const toInput   = document.getElementById('pickerTo');
    const hidFrom   = document.getElementById('dateFrom');
    const hidTo     = document.getElementById('dateTo');
    const applyBtn  = document.getElementById('dateRangeApply');
    const clearBtn  = document.getElementById('dateRangeClear');

    if (!display) return;

    // Pre-fill picker inputs from hidden values
    if (hidFrom.value) fromInput.value = hidFrom.value;
    if (hidTo.value)   toInput.value   = hidTo.value;

    // Show/hide picker
    display.addEventListener('click', function(e) {
        e.stopPropagation();
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    });

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!picker.contains(e.target) && e.target !== display) {
            picker.style.display = 'none';
        }
    });

    // Auto-set To min when From changes
    fromInput.addEventListener('change', function() {
        toInput.min = this.value;
        if (toInput.value && toInput.value < this.value) toInput.value = '';
    });

    function formatRange(from, to) {
        if (!from) return '';
        const f = new Date(from + 'T00:00:00');
        const months = ['January','February','March','April','May','June',
                        'July','August','September','October','November','December'];
        const fStr = months[f.getMonth()] + ' ' + f.getDate();
        if (!to || to === from) {
            return fStr + ', ' + f.getFullYear();
        }
        const t = new Date(to + 'T00:00:00');
        const tStr = months[t.getMonth()] + ' ' + t.getDate() + ', ' + t.getFullYear();
        return fStr + ' – ' + tStr;
    }

    applyBtn.addEventListener('click', function() {
        const from = fromInput.value;
        const to   = toInput.value;
        if (!from) { fromInput.focus(); return; }
        hidFrom.value   = from;
        hidTo.value     = to;
        display.value   = formatRange(from, to);
        picker.style.display = 'none';
    });

    clearBtn.addEventListener('click', function() {
        fromInput.value = '';
        toInput.value   = '';
        hidFrom.value   = '';
        hidTo.value     = '';
        display.value   = '';
        picker.style.display = 'none';
    });
})();

</script>
@endpush