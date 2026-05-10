<?php

namespace App\Http\Controllers;

use App\Models\ResearchRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['year', 'quarter', 'constituent', 'college_campus', 'classification', 'status', 'source_of_fund']);

        // Base query with filters applied
        $base = ResearchRecord::filter($filters);

        $total       = (clone $base)->count();
        $completed   = (clone $base)->whereRaw("LOWER(status) = 'completed'")->count();
        $ongoing     = (clone $base)->whereRaw("LOWER(status) = 'ongoing'")->count();
        $withPresent = (clone $base)->where('presentation_support', 'Yes')->count();

        $byStatus = (clone $base)->select(
                DB::raw("INITCAP(LOWER(status)) as status"),
                DB::raw('count(*) as total')
            )->groupBy(DB::raw("INITCAP(LOWER(status))"))->pluck('total', 'status');

        $byConstituent    = (clone $base)->select('constituent', DB::raw('count(*) as total'))->groupBy('constituent')->pluck('total', 'constituent');
        $byClassification = (clone $base)->select('classification', DB::raw('count(*) as total'))->whereNotNull('classification')->groupBy('classification')->pluck('total', 'classification');
        $byFund           = (clone $base)->select(DB::raw("INITCAP(LOWER(TRIM(source_of_fund))) as source_of_fund"), DB::raw('count(*) as total'))->whereNotNull('source_of_fund')->whereRaw("TRIM(source_of_fund) != ''") ->groupBy(DB::raw('INITCAP(LOWER(TRIM(source_of_fund)))'))->orderByDesc('total')->limit(8)->get();
        $byCollege        = (clone $base)->selectRaw('TRIM(constituent) as constituent, count(*) as total')->whereNotNull('constituent')->where('constituent','!=','')->groupByRaw('TRIM(constituent)')->orderByDesc('total')->get();
        $byPresentSupport = (clone $base)->select('presentation_support', DB::raw('count(*) as total'))->groupBy('presentation_support')->pluck('total', 'presentation_support');

        $byYearQuarter = (clone $base)->select('year', 'quarter', DB::raw('count(*) as total'))
            ->groupBy('year', 'quarter')->orderBy('year')->orderBy('quarter')->get();

        // SDG frequency
        $sdgCounts = [];
        (clone $base)->select('sdg')->get()->each(function ($r) use (&$sdgCounts) {
            $raw = $r->getRawOriginal('sdg');
            if (is_array($raw)) {
                $sdg = $raw;
            } elseif (is_string($raw) && str_starts_with(trim($raw), '[')) {
                $sdg = json_decode($raw, true) ?? [];
            } elseif (is_string($raw) && !empty($raw)) {
                $sdg = array_map('trim', explode(',', $raw));
            } else {
                $sdg = [];
            }
            foreach ($sdg as $s) {
                $s = trim($s);
                if (!$s) continue;
                // Normalize numeric to full name
                if (is_numeric($s)) {
                    $sdgMap = [
                        1=>'SDG 1 – No Poverty', 2=>'SDG 2 – Zero Hunger',
                        3=>'SDG 3 – Good Health and Well-being', 4=>'SDG 4 – Quality Education',
                        5=>'SDG 5 – Gender Equality', 6=>'SDG 6 – Clean Water and Sanitation',
                        7=>'SDG 7 – Affordable and Clean Energy', 8=>'SDG 8 – Decent Work and Economic Growth',
                        9=>'SDG 9 – Industry, Innovation and Infrastructure', 10=>'SDG 10 – Reduced Inequalities',
                        11=>'SDG 11 – Sustainable Cities and Communities', 12=>'SDG 12 – Responsible Consumption and Production',
                        13=>'SDG 13 – Climate Action', 14=>'SDG 14 – Life Below Water',
                        15=>'SDG 15 – Life on Land', 16=>'SDG 16 – Peace, Justice and Strong Institutions',
                        17=>'SDG 17 – Partnerships for the Goals',
                    ];
                    $s = $sdgMap[(int)$s] ?? ('SDG ' . $s);
                }
                $sdgCounts[$s] = ($sdgCounts[$s] ?? 0) + 1;
            }
        });
        uksort($sdgCounts, function($a, $b) {
            preg_match('/\d+/', $a, $ma);
            preg_match('/\d+/', $b, $mb);
            return (int)($ma[0] ?? 0) <=> (int)($mb[0] ?? 0);
        });

        $recentRecords = (clone $base)->orderByDesc('id')->limit(6)->get();

        // Fund counts per status group
        $fundByStatus = (clone $base)
            ->select(DB::raw("INITCAP(LOWER(TRIM(source_of_fund))) as fund"), DB::raw("INITCAP(LOWER(status)) as status"), DB::raw('count(*) as total'))
            ->whereNotNull('source_of_fund')->whereRaw("TRIM(source_of_fund) != ''")
            ->groupBy(DB::raw('INITCAP(LOWER(TRIM(source_of_fund)))'), DB::raw('INITCAP(LOWER(status))'))
            ->get()
            ->groupBy('status'); // ['Completed' => [...], 'Ongoing' => [...]]
        $fundTotal = (clone $base)
            ->select(DB::raw("INITCAP(LOWER(TRIM(source_of_fund))) as fund"), DB::raw('count(*) as total'))
            ->whereNotNull('source_of_fund')->whereRaw("TRIM(source_of_fund) != ''")
            ->groupBy(DB::raw('INITCAP(LOWER(TRIM(source_of_fund)))'))
            ->orderByDesc('total')->get();

        // Filter options
        $years      = ResearchRecord::distinctValues('year');
        $quarters   = ['Q1','Q2','Q3','Q4'];
        $constituents = ResearchRecord::distinctValues('constituent');
        $colleges   = ResearchRecord::distinctValues('college_campus');
        $classifications = ResearchRecord::distinctValues('classification');
        $statuses   = ['Ongoing','Completed'];
        $funds      = ResearchRecord::distinctValues('source_of_fund');

        return view('analytics.index', compact(
            'total', 'completed', 'ongoing', 'withPresent',
            'byStatus', 'byConstituent', 'byYearQuarter', 'byCollege',
            'byClassification', 'byFund', 'sdgCounts', 'recentRecords',
            'byPresentSupport', 'filters', 'fundByStatus', 'fundTotal',
            'years', 'quarters', 'constituents', 'colleges', 'classifications', 'statuses', 'funds'
        ));
    }

    public function data(Request $request)
    {
        $filters = $request->only(['year', 'quarter', 'constituent', 'college_campus', 'classification', 'status', 'source_of_fund']);
        $base = ResearchRecord::filter($filters);

        $total       = (clone $base)->count();
        $completed   = (clone $base)->whereRaw("LOWER(status) = 'completed'")->count();
        $ongoing     = (clone $base)->whereRaw("LOWER(status) = 'ongoing'")->count();
        $withPresent = (clone $base)->where('presentation_support', 'Yes')->count();

        $byStatus = (clone $base)->select(
                DB::raw("INITCAP(LOWER(status)) as status"), DB::raw('count(*) as total')
            )->groupBy(DB::raw("INITCAP(LOWER(status))"))->pluck('total', 'status');

        $byConstituent    = (clone $base)->select('constituent', DB::raw('count(*) as total'))->groupBy('constituent')->pluck('total', 'constituent');
        $byClassification = (clone $base)->select('classification', DB::raw('count(*) as total'))->whereNotNull('classification')->groupBy('classification')->pluck('total', 'classification');
        $byFund           = (clone $base)->select(DB::raw("INITCAP(LOWER(TRIM(source_of_fund))) as source_of_fund"), DB::raw('count(*) as total'))->whereNotNull('source_of_fund')->whereRaw("TRIM(source_of_fund) != ''")->groupBy(DB::raw('INITCAP(LOWER(TRIM(source_of_fund)))'))->orderByDesc('total')->limit(8)->get();
        $byCollege        = (clone $base)->selectRaw('TRIM(constituent) as constituent, count(*) as total')->whereNotNull('constituent')->where('constituent','!=','')->groupByRaw('TRIM(constituent)')->orderByDesc('total')->get();
        $byYearQuarter    = (clone $base)->select('year', 'quarter', DB::raw('count(*) as total'))->groupBy('year', 'quarter')->orderBy('year')->orderBy('quarter')->get();

        $sdgCounts = [];
        (clone $base)->select('sdg')->get()->each(function ($r) use (&$sdgCounts) {
            $raw = $r->getRawOriginal('sdg');
            $sdg = is_array($raw) ? $raw : (is_string($raw) && str_starts_with(trim($raw),'[') ? (json_decode($raw,true)??[]) : (is_string($raw)&&!empty($raw) ? array_map('trim',explode(',',$raw)) : []));
            $sdgMap=[1=>'SDG 1 – No Poverty',2=>'SDG 2 – Zero Hunger',3=>'SDG 3 – Good Health and Well-being',4=>'SDG 4 – Quality Education',5=>'SDG 5 – Gender Equality',6=>'SDG 6 – Clean Water and Sanitation',7=>'SDG 7 – Affordable and Clean Energy',8=>'SDG 8 – Decent Work and Economic Growth',9=>'SDG 9 – Industry, Innovation and Infrastructure',10=>'SDG 10 – Reduced Inequalities',11=>'SDG 11 – Sustainable Cities and Communities',12=>'SDG 12 – Responsible Consumption and Production',13=>'SDG 13 – Climate Action',14=>'SDG 14 – Life Below Water',15=>'SDG 15 – Life on Land',16=>'SDG 16 – Peace, Justice and Strong Institutions',17=>'SDG 17 – Partnerships for the Goals'];
            foreach ($sdg as $s) {
                $s = trim($s); if (!$s) continue;
                if (is_numeric($s)) { $s=$sdgMap[(int)$s]??('SDG '.$s); }
                $sdgCounts[$s] = ($sdgCounts[$s] ?? 0) + 1;
            }
        });
        uksort($sdgCounts, function($a,$b){ preg_match('/\d+/',$a,$ma); preg_match('/\d+/',$b,$mb); return (int)($ma[0]??0)<=>(int)($mb[0]??0); });

        $recentRecords = (clone $base)->orderByDesc('id')->limit(6)->get()->map(fn($r) => [
            'id' => $r->id, 'research_title' => $r->research_title,
            'authors' => $r->authors, 'year' => $r->year, 'quarter' => $r->quarter,
            'status' => $r->status, 'constituent' => $r->constituent,
            'college_campus' => $r->college_campus, 'sdg' => $r->sdg ?? [],
        ]);

        $fundTotal = (clone $base)->select(DB::raw("INITCAP(LOWER(TRIM(source_of_fund))) as fund"), DB::raw('count(*) as total'))->whereNotNull('source_of_fund')->whereRaw("TRIM(source_of_fund) != ''")->groupBy(DB::raw('INITCAP(LOWER(TRIM(source_of_fund)))'))->orderByDesc('total')->get();

        return response()->json([
            'total' => $total, 'completed' => $completed, 'ongoing' => $ongoing, 'withPresent' => $withPresent,
            'byStatus' => $byStatus, 'byConstituent' => $byConstituent,
            'byClassification' => $byClassification, 'byFund' => $byFund,
            'byCollege' => $byCollege, 'byYearQuarter' => $byYearQuarter,
            'sdgCounts' => $sdgCounts, 'recentRecords' => $recentRecords,
            'fundTotal' => $fundTotal,
        ]);
    }

}