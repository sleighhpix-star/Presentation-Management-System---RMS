<?php

namespace App\Http\Controllers;

use App\Exports\ResearchExport;
use App\Models\ResearchRecord;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class ResearchController extends Controller
{
    // ── SDG master list ────────────────────────────────────────────
    private array $sdgList = [
        'SDG 1 – No Poverty',
        'SDG 2 – Zero Hunger',
        'SDG 3 – Good Health and Well-being',
        'SDG 4 – Quality Education',
        'SDG 5 – Gender Equality',
        'SDG 6 – Clean Water and Sanitation',
        'SDG 7 – Affordable and Clean Energy',
        'SDG 8 – Decent Work and Economic Growth',
        'SDG 9 – Industry, Innovation and Infrastructure',
        'SDG 10 – Reduced Inequalities',
        'SDG 11 – Sustainable Cities and Communities',
        'SDG 12 – Responsible Consumption and Production',
        'SDG 13 – Climate Action',
        'SDG 14 – Life Below Water',
        'SDG 15 – Life on Land',
        'SDG 16 – Peace, Justice and Strong Institutions',
        'SDG 17 – Partnerships for the Goals',
    ];

    // ── Index / list ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $filters = $request->only([
            'search','year','quarter','constituent','college_campus',
            'status','classification','presentation_support','source_of_fund','sdg','has_photo',
            'date_from_filter','date_to_filter',
        ]);

        $records = ResearchRecord::filter($filters)
            ->orderByDesc('year')->orderBy('quarter')
            ->get();

        return view('research.index', [
            'records'        => $records,
            'filters'        => $filters,
            'years'          => ResearchRecord::distinctValues('year'),
            'quarters'       => ResearchRecord::distinctValues('quarter'),
            'constituents'   => ['Alangilan','Lipa','Malvar','Nasugbu','Pablo Borbon'],
            'colleges'       => ResearchRecord::distinctValues('college_campus'),
            'statuses'       => ['Ongoing','Completed'],
            'presSupports'   => ResearchRecord::distinctValues('presentation_support'),
            'funds'          => ['Externally-Funded','Institutionally-Funded','Non-Funded'],
            'classifications' => ['Institutional','International','Multidisciplinary Research Conference','National','Regional'],
            'sdgList'        => $this->sdgList,
        ]);
    }

    // ── Create ─────────────────────────────────────────────────────
    public function create()
    {
        return view('research.create', ['sdgList' => $this->sdgList]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRecord($request);
        if ($request->hasFile('photo_file')) {
            $data['photo'] = $request->file('photo_file')->store('photos', 'public');
        }
        ResearchRecord::create($data);
        return redirect()->route('research.index')
            ->with('success', 'Research record created successfully.');
    }

    // ── Show ───────────────────────────────────────────────────────
    public function show(ResearchRecord $research)
    {
        return view('research.show', compact('research'));
    }

    // ── Edit ───────────────────────────────────────────────────────
    public function edit(ResearchRecord $research)
    {
        return view('research.edit', [
            'record'  => $research,
            'sdgList' => $this->sdgList,
        ]);
    }

    public function update(Request $request, ResearchRecord $research)
    {
        $data = $this->validateRecord($request, $research->id);

        // Handle media: file upload takes priority, then URL, then keep existing
        $photoChanged = false;
        if ($request->boolean('remove_photo')) {
            $data['photo'] = null;
            $photoChanged = true;
        } elseif ($request->hasFile('photo_file')) {
            $data['photo'] = $request->file('photo_file')->store('photos', 'public');
            $photoChanged = true;
        } elseif (!empty($data['photo'])) {
            $photoChanged = ($data['photo'] !== $research->photo);
        } else {
            unset($data['photo']); // keep existing if blank
        }

        // Check if anything actually changed
        $dirty = collect($data)->filter(function ($value, $key) use ($research) {
            $current = $research->getAttribute($key);
            if (is_array($value) || is_array($current)) {
                return json_encode($value) !== json_encode($current);
            }
            if ($current instanceof \Carbon\Carbon) {
                return $current->format('Y-m-d') !== ($value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null);
            }
            return (string)$value !== (string)($current ?? '');
        })->isNotEmpty();

        if (!$dirty && !$photoChanged) {
            return redirect()->route('research.index')
                ->with('info', 'No changes were made.');
        }

        $research->update($data);
        return redirect()->route('research.index')
            ->with('success', 'Research record updated successfully.');
    }

    // ── Delete ─────────────────────────────────────────────────────
    public function destroy(ResearchRecord $research)
    {
        $research->delete();
        return redirect()->route('research.index')
            ->with('success', 'Record deleted.');
    }

    // ── Export Excel ───────────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $filters = $request->only([
            'search','year','quarter','constituent','college_campus',
            'status','classification','presentation_support','source_of_fund','sdg',
            'date_from_filter','date_to_filter','has_photo',
        ]);
        return Excel::download(
            new ResearchExport($filters),
            'research-records-'.now()->format('Ymd_His').'.xlsx'
        );
    }

    // ── Validation ─────────────────────────────────────────────────
    private function validateRecord(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'year'                  => 'nullable|string|max:10',
            'quarter'               => 'nullable|in:Q1,Q2,Q3,Q4',
            'constituent'           => 'nullable|string|max:255',
            'research_title'        => 'nullable|string|max:500',
            'reporter'              => 'nullable|string|max:250',
            'college_campus'        => 'nullable|string|max:250',
            'title_of_project'      => 'nullable|string|max:500',
            'authors'               => 'nullable|string',
            'source_of_fund'        => 'nullable|string|max:200',
            'status'                => 'nullable|in:Ongoing,Completed',
            'sdg'                   => 'nullable|array',
            'sdg.*'                 => 'string',
            'presentation_support'  => 'nullable|string|max:100',
            'title_of_event'        => 'nullable|string|max:500',
            'theme'                 => 'nullable|string|max:500',
            'date_from'             => 'nullable|date',
            'date_to'               => 'nullable|date|after_or_equal:date_from',
            'venue'                 => 'nullable|string|max:300',
            'classification'        => 'nullable|string|max:200',
            'organizer_name'        => 'nullable|string|max:250',
            'organizer_email'       => 'nullable|email|max:250',
            'website'               => 'nullable|string|max:500',
            'photo'                 => 'nullable|string|max:1000',
        ]);
    }
}