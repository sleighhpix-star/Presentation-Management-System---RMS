<?php

namespace App\Exports;

use App\Models\ResearchRecord;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ResearchExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private array $filters = []) {}

    public function query()
    {
        return ResearchRecord::filter($this->filters)->orderByDesc('year');
    }

    public function title(): string { return 'Research Records'; }

    public function headings(): array
    {
        return [
            'ID','Year','Quarter','Constituent','Research Title','Reporter',
            'College / Campus','Title of Project','Author/s','Source of Fund',
            'Status','SDG','Presentation Support Requested',
            'Title of Event','Theme','Date From','Date To','Date Period','Venue','Classification',
            'Organizer Name','Organizer Email','Website','Has Photo','Created At',
        ];
    }

    public function map($r): array
    {
        return [
            $r->id, $r->year, $r->quarter, $r->constituent,
            $r->research_title, $r->reporter, $r->college_campus,
            $r->title_of_project, $r->authors, $r->source_of_fund,
            $r->status, implode(', ', $r->sdg ?? []),
            $r->presentation_support, $r->title_of_event, $r->theme,
            $r->date_from?->format('Y-m-d'),
            $r->date_to?->format('Y-m-d'),
            $r->date_from ? ($r->date_from->format('M d') . ($r->date_to && $r->date_to->format('Y-m-d') !== $r->date_from->format('Y-m-d') ? '–' . $r->date_to->format('M d, Y') : ', ' . $r->date_from->format('Y'))) : null,
            $r->venue, $r->classification,
            $r->organizer_name, $r->organizer_email, $r->website,
            $r->photo ? 'Yes' : 'No',
            $r->created_at?->format('m/d/Y') ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            ],
        ];
    }
}