@extends('layouts.app')
@section('title', $research->research_title)
@section('page-title','Record Detail')

@section('content')
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
    var modal = document.getElementById('mediaModal');
    var video = document.getElementById('modalVideo');
    try { video.pause(); video.src = ''; } catch(e){}
    modal.style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeMediaModal(); });
</script>

{{-- ── Breadcrumb + Actions ─────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0" style="font-size:13px;">
            <li class="breadcrumb-item"><a href="{{ route('research.index') }}" style="color:var(--navy);">Records</a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width:340px;">{{ $research->research_title }}</li>
        </ol>
    </nav>
    <div class="d-flex gap-2">
        <a href="{{ route('research.edit',$research) }}" class="btn btn-gold btn-sm px-3">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <form method="POST" action="{{ route('research.destroy',$research) }}" onsubmit="return confirm('Permanently delete this record?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm px-3" style="background:#fee2e2;color:#b91c1c;border-radius:8px;font-weight:600;font-size:13px;border:none;">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row g-3">

    {{-- ── Left: Main Info ──────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body p-4">

                {{-- Title + Status --}}
                <div class="d-flex align-items-start justify-content-between gap-3 mb-3 flex-wrap">
                    <h4 style="font-family:'Lora',serif;color:var(--navy);font-weight:700;line-height:1.4;margin:0;flex:1;">
                        {{ $research->research_title }}
                    </h4>
                    @include('research.partials.status-badge',['status'=>$research->status])
                </div>
                @if($research->title_of_project)
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
                        <i class="bi bi-folder me-1"></i> Project: <strong style="color:var(--navy);">{{ $research->title_of_project }}</strong>
                    </div>
                @endif

                {{-- Detail Grid --}}
                <div class="row g-2">
                    @php
                    $details = [
                        ['icon'=>'bi-calendar3',       'label'=>'Year / Quarter',     'val'=>$research->year.' – '.$research->quarter],
                        ['icon'=>'bi-people',           'label'=>'Constituent',        'val'=>$research->constituent],
                        ['icon'=>'bi-person-badge',     'label'=>'Reporter',           'val'=>$research->reporter],
                        ['icon'=>'bi-building',         'label'=>'College / Campus',   'val'=>$research->college_campus],
                        ['icon'=>'bi-pencil',           'label'=>'Author/s',           'val'=>$research->authors],
                        ['icon'=>'bi-cash',             'label'=>'Source of Fund',     'val'=>$research->source_of_fund ?: '—'],
                    ];
                    @endphp
                    @foreach($details as $d)
                    <div class="col-md-6">
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:13px 15px;">
                            <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;">
                                <i class="bi {{ $d['icon'] }} me-1"></i>{{ $d['label'] }}
                            </div>
                            <div style="font-size:13.5px;color:var(--navy);font-weight:600;">{{ $d['val'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- SDG --}}
                <div class="mt-3 p-3" style="background:#eef2ff;border:1px solid #c7d2fe;border-radius:10px;">
                    <div style="font-size:10.5px;color:#3730a3;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">
                        <i class="bi bi-award me-1"></i>SDG Goals
                    </div>
                    @foreach($research->sdg as $s)
                        <span class="sdg-chip" style="font-size:12.5px;padding:4px 11px;">{{ $s }}</span>
                    @endforeach
                </div>

                {{-- Presentation Support --}}
                <div class="mt-3 p-3" style="background:var(--surface);border:1px solid var(--border);border-radius:10px;">
                    <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">
                        <i class="bi bi-person-video3 me-1"></i>Presentation Support Requested
                    </div>
                    @php $pc=['Yes'=>'#065f46','No'=>'#b91c1c','N/A'=>'#374151']; @endphp
                    <span style="font-size:16px;font-weight:700;color:{{ $pc[$research->presentation_support]??'#374151' }};">
                        {{ $research->presentation_support }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Event Details --}}
        <div class="card">
            <div class="card-header py-3 px-4">
                <i class="bi bi-calendar-event me-2" style="color:var(--gold);"></i>Event / Presentation Details
            </div>
            <div class="card-body p-3">
                @php
                $evDetails = [
                    ['label'=>'Title of Event', 'val'=>$research->title_of_event],
                    ['label'=>'Theme',          'val'=>$research->theme],
                    ['label'=>'Date / Period',  'val'=>$research->date_range],
                    ['label'=>'Venue',          'val'=>$research->venue],
                    ['label'=>'Classification', 'val'=>$research->classification],
                ];
                @endphp
                <div class="row g-2">
                @foreach($evDetails as $ev)
                    @if($ev['val'])
                    <div class="col-md-6">
                        <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;">{{ $ev['label'] }}</div>
                        <div style="font-size:13.5px;color:var(--navy);font-weight:500;">{{ $ev['val'] }}</div>
                    </div>
                    @endif
                @endforeach
                </div>
                @if(!$research->title_of_event && !$research->venue)
                    <p style="font-size:13px;color:var(--text-muted);margin:0;">No event details recorded.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Right: Photo + Organizer ─────────────────── --}}
    <div class="col-lg-4">

        {{-- Media / Documentation --}}
        <div class="card mb-3">
            <div class="card-header py-3 px-4">
                <i class="bi bi-link-45deg me-2" style="color:var(--teal);"></i>Media / Documentation
            </div>
            <div class="card-body p-3">
                @if($research->photo_url)
                    @php
                        $url = $research->photo_url;
                        $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i', $url);
                        $isYoutube = str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be');
                        $isDrive = str_contains($url, 'drive.google.com');
                        $isVideo = preg_match('/\.(mp4|mov|avi|webm)(\?.*)?$/i', $url);
                    @endphp

                    @if($isImage)
                        <img src="{{ $url }}" alt="Media"
                             style="width:100%;border-radius:10px;object-fit:cover;max-height:220px;border:1px solid var(--border);cursor:zoom-in;"
                             onclick="openMediaModal('{{ $url }}', 'image')">
                    @elseif($isYoutube)
                        @php
                            preg_match('/(?:v=|youtu\.be\/)([^&\?]+)/', $url, $m);
                            $vid = $m[1] ?? '';
                        @endphp
                        @if($vid)
                        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:10px;">
                            <iframe src="https://www.youtube.com/embed/{{ $vid }}"
                                    style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;"
                                    allowfullscreen></iframe>
                        </div>
                        @endif
                    @else
                        {{-- Generic link for Drive, .mov, video, etc. --}}
                        <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:20px;text-align:center;">
                            <i class="bi bi-{{ $isDrive ? 'google' : ($isVideo ? 'camera-video' : 'file-earmark-play') }}"
                               style="font-size:36px;color:var(--teal);display:block;margin-bottom:10px;"></i>
                            @if($isVideo)
                                <button onclick="openMediaModal('{{ $url }}', 'video')"
                                        class="btn btn-sm w-100"
                                        style="background:var(--teal);color:#fff;font-weight:600;border-radius:8px;">
                                    <i class="bi bi-play-circle me-1"></i> Play Video
                                </button>
                            @else
                                <a href="{{ $url }}" target="_blank" rel="noopener"
                                   class="btn btn-sm w-100"
                                   style="background:var(--teal);color:#fff;font-weight:600;border-radius:8px;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>
                                    Open {{ $isDrive ? 'Google Drive' : 'Media Link' }}
                                </a>
                            @endif
                            <div style="font-size:10px;color:var(--text-muted);margin-top:8px;word-break:break-all;">{{ Str::limit($url, 60) }}</div>
                        </div>
                    @endif

                    <a href="{{ route('research.edit',$research) }}" style="font-size:11.5px;color:var(--text-muted);display:block;margin-top:8px;text-align:center;">
                        <i class="bi bi-pencil me-1"></i> Change link
                    </a>
                @else
                    <div style="width:100%;height:120px;background:var(--surface);border:2px dashed var(--border);border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--text-muted);">
                        <i class="bi bi-link-45deg" style="font-size:28px;opacity:.4;"></i>
                        <p style="font-size:12px;margin:6px 0 0;">No media link added</p>
                    </div>
                    <a href="{{ route('research.edit',$research) }}" class="btn btn-sm btn-outline-secondary w-100 mt-2" style="font-size:12.5px;">
                        <i class="bi bi-plus me-1"></i> Add Media Link
                    </a>
                @endif
            </div>
        </div>

        {{-- Organizer --}}
        <div class="card">
            <div class="card-header py-3 px-4">
                <i class="bi bi-person-lines-fill me-2" style="color:var(--gold);"></i>Organizer
            </div>
            <div class="card-body p-3">
                @if($research->organizer_name || $research->organizer_email || $research->website)
                    @if($research->organizer_name || $research->organizer_email)
                    <div class="mb-3">
                        <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Organizer Name / Email</div>
                        <div style="font-size:13.5px;font-weight:600;color:var(--navy);">{{ $research->organizer_name ?: $research->organizer_email }}</div>
                    </div>
                    @endif
                    @if($research->website)
                    <div>
                        <div style="font-size:10.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Website</div>
                        @if(str_starts_with($research->website, 'http'))
                            <a href="{{ $research->website }}" target="_blank" rel="noopener" style="font-size:13px;color:var(--navy);word-break:break-all;">
                                <i class="bi bi-box-arrow-up-right me-1"></i>{{ $research->website }}
                            </a>
                        @else
                            <span style="font-size:13px;color:var(--navy);word-break:break-all;">{{ $research->website }}</span>
                        @endif
                    </div>
                    @endif
                @else
                    <p style="font-size:13px;color:var(--text-muted);margin:0;">No organizer info provided.</p>
                @endif
            </div>
        </div>

        {{-- Meta --}}
        <div class="mt-3 p-3" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);">
            <div style="font-size:11px;color:var(--text-muted);">
                <div><i class="bi bi-clock me-1"></i>Created: {{ $research->created_at?->format('M d, Y h:i A') ?? '—' }}</div>
                <div class="mt-1"><i class="bi bi-pencil me-1"></i>Updated: {{ $research->updated_at?->format('M d, Y h:i A') ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Media Lightbox Modal ─────────────────────── --}}
<div id="mediaModal" onclick="closeMediaModal()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:99999;align-items:center;justify-content:center;cursor:zoom-out;">
    <div onclick="event.stopPropagation()" style="position:relative;max-width:90vw;max-height:90vh;">
        <button onclick="closeMediaModal()"
                style="position:absolute;top:-36px;right:0;background:none;border:none;color:#fff;font-size:26px;cursor:pointer;line-height:1;">
            <i class="bi bi-x-circle-fill"></i>
        </button>
        <img id="modalImg" src="" alt=""
             style="display:none;max-width:90vw;max-height:85vh;border-radius:10px;object-fit:contain;">
        <video id="modalVideo" controls
               style="display:none;max-width:90vw;max-height:85vh;border-radius:10px;">
        </video>
    </div>
</div>

@endsection