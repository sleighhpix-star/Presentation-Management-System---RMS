<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { font-family: DejaVu Sans, sans-serif; font-size: 8px; margin:0; padding:0; }
body { padding: 12px; }
.header { margin-bottom: 12px; border-bottom: 2px solid #0f2544; padding-bottom: 8px; display:flex; justify-content:space-between; align-items:flex-end; }
.header h1 { font-size: 14px; color: #0f2544; font-weight: bold; }
.header .meta { font-size: 9px; color: #6b7a95; text-align: right; }
table { width: 100%; border-collapse: collapse; }
thead th {
    background: #0f2544; color: #fff;
    padding: 6px 5px; text-align: left;
    font-size: 7.5px; text-transform: uppercase; letter-spacing: 0.3px;
}
tbody td { padding: 5px; border-bottom: 1px solid #e4e9f2; vertical-align: top; font-size: 8px; }
tbody tr:nth-child(even) td { background: #f4f6fb; }
.b-completed { background:#d1fae5; color:#065f46; padding:1px 5px; border-radius:8px; font-weight:bold; white-space:nowrap; }
.b-ongoing   { background:#dbeafe; color:#1e40af; padding:1px 5px; border-radius:8px; font-weight:bold; white-space:nowrap; }
.b-proposed  { background:#fef9c3; color:#92400e; padding:1px 5px; border-radius:8px; font-weight:bold; white-space:nowrap; }
.sdg { background:#eef2ff; color:#3730a3; padding:1px 4px; border-radius:6px; font-size:7px; display:inline-block; margin:1px; }
.yes { color:#065f46; font-weight:bold; }
.no  { color:#b91c1c; font-weight:bold; }
.na  { color:#6b7a95; }
.footer { margin-top:10px; color:#9ca3af; font-size:7.5px; text-align:right; }
</style>
</head>
<body>
<div class="header">
    <div>
        <h1>Research Presentation Management System</h1>
        <div style="font-size:9px;color:#6b7a95;margin-top:3px;">Complete Research Records Export</div>
    </div>
    <div class="meta">
        Exported: {{ now()->format('F d, Y h:i A') }}<br>
        Total Records: <strong>{{ $records->count() }}</strong>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Year</th>
            <th>Qtr</th>
            <th>Constituent</th>
            <th>Research Title</th>
            <th>Reporter</th>
            <th>College / Campus</th>
            <th>Project Title</th>
            <th>Authors</th>
            <th>Fund Source</th>
            <th>Status</th>
            <th>SDG</th>
            <th>Pres. Support</th>
            <th>Event Title</th>
            <th>Theme</th>
            <th>Date</th>
            <th>Venue</th>
            <th>Classification</th>
            <th>Organizer</th>
            <th>Website</th>
            <th>Photo</th>
        </tr>
    </thead>
    <tbody>
    @foreach($records as $i => $r)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r->year }}</td>
            <td>{{ $r->quarter }}</td>
            <td>{{ $r->constituent }}</td>
            <td style="max-width:100px;"><strong>{{ $r->research_title }}</strong></td>
            <td style="white-space:nowrap;">{{ $r->reporter }}</td>
            <td>{{ $r->college_campus }}</td>
            <td>{{ $r->title_of_project ?: '—' }}</td>
            <td>{{ $r->authors }}</td>
            <td>{{ $r->source_of_fund ?: '—' }}</td>
            <td>
                @php $sc=['Completed'=>'b-completed','Ongoing'=>'b-ongoing','Proposed'=>'b-proposed']; @endphp
                <span class="{{ $sc[$r->status]??'' }}">{{ $r->status }}</span>
            </td>
            <td>
                @foreach($r->sdg??[] as $s)
                    <span class="sdg">{{ explode(' – ',$s)[0] }}</span>
                @endforeach
            </td>
            <td class="{{ $r->presentation_support==='Yes'?'yes':($r->presentation_support==='No'?'no':'na') }}">
                {{ $r->presentation_support }}
            </td>
            <td>{{ $r->title_of_event ?: '—' }}</td>
            <td>{{ $r->theme ?: '—' }}</td>
            <td style="white-space:nowrap;">{{ $r->date_period?->format('m/d/Y') ?: '—' }}</td>
            <td>{{ $r->venue ?: '—' }}</td>
            <td>{{ $r->classification ?: '—' }}</td>
            <td>{{ $r->organizer_name ?: '—' }}<br>{{ $r->organizer_email ?: '' }}</td>
            <td style="max-width:60px;word-break:break-all;">{{ $r->website ?: '—' }}</td>
            <td style="text-align:center;">{{ $r->photo ? '✓' : '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">Research Presentation Management System &mdash; Exported {{ now()->format('Y-m-d H:i') }} &mdash; Confidential</div>
</body>
</html>
