<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ResearchRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'year','quarter','constituent','research_title','reporter',
        'college_campus','title_of_project','authors','source_of_fund',
        'status','sdg','presentation_support','title_of_event','theme',
        'date_from','date_to','venue','classification','organizer_name',
        'organizer_email','website','photo',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to'   => 'date',
    ];

    private static array $sdgMap = [
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

    /** Always return sdg as an array of full SDG strings */
    public function getSdgAttribute($value): array
    {
        if (empty($value)) return [];
        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (!is_array($decoded)) return [];
        return array_map(function($item) {
            $item = trim($item);
            // If numeric, map to full name
            if (is_numeric($item)) return self::$sdgMap[(int)$item] ?? 'SDG ' . $item;
            // If already full name, return as-is
            return $item;
        }, $decoded);
    }

    /** Always store sdg as JSON */
    public function setSdgAttribute($value): void
    {
        $this->attributes['sdg'] = is_array($value) ? json_encode($value) : $value;
    }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when($filters['search'] ?? null, function ($q, $s) {
            $q->where(function ($q) use ($s) {
                $q->where('research_title', 'like', "%$s%")
                  ->orWhere('reporter', 'like', "%$s%")
                  ->orWhere('authors', 'like', "%$s%")
                  ->orWhere('college_campus', 'like', "%$s%")
                  ->orWhere('title_of_project', 'like', "%$s%")
                  ->orWhere('title_of_event', 'like', "%$s%");
            });
        });
        $query->when($filters['year'] ?? null,                 fn($q,$v)=>$q->where('year', $v));
        $query->when($filters['quarter'] ?? null, function($q,$v) {
            $upper = strtoupper(trim($v));               // e.g. 'Q3'
            $num   = ltrim($upper, 'Q');                 // e.g. '3'
            $q->whereRaw("TRIM(UPPER(quarter)) IN (?,?)", [$upper, $num]);
        });
        $query->when($filters['constituent'] ?? null,          fn($q,$v)=>$v==='__empty__' ? $q->whereNull('constituent')->orWhere('constituent','') : $q->whereRaw('LOWER(constituent) = LOWER(?)',[$v]));
        $query->when($filters['college_campus'] ?? null,       fn($q,$v)=>$v==='__empty__' ? $q->whereNull('college_campus')->orWhere('college_campus','') : $q->whereRaw('LOWER(college_campus) = LOWER(?)',[$v]));
        $query->when($filters['status'] ?? null,               fn($q,$v)=>$v==='__empty__' ? $q->whereNull('status')->orWhere('status','') : $q->whereRaw('LOWER(status) = LOWER(?)',[$v]));
        $query->when($filters['classification'] ?? null,       fn($q,$v)=>$v==='__empty__' ? $q->whereNull('classification')->orWhere('classification','') : $q->whereRaw('LOWER(classification) = LOWER(?)',[$v]));
        $query->when($filters['presentation_support'] ?? null, fn($q,$v)=>$v==='__empty__' ? $q->whereNull('presentation_support')->orWhere('presentation_support','') : $q->whereRaw('LOWER(presentation_support) = LOWER(?)',[$v]));
        $query->when($filters['source_of_fund'] ?? null,       fn($q,$v)=>$v==='__empty__' ? $q->whereNull('source_of_fund')->orWhere('source_of_fund','') : $q->whereRaw('LOWER(source_of_fund) = LOWER(?)',[$v]));
        $query->when($filters['sdg'] ?? null, function($q, $v) {
            preg_match('/\d+/', $v, $m);
            $num = $m[0] ?? null;
            if ($num) {
                // Match "2" exactly — not "12" or "20"
                // PostgreSQL regex: "2" not followed by a digit
                $q->whereRaw("sdg::text ~ ?", ['"' . $num . '"[^0-9]']);
            }
        });
        $query->when($filters['date_from_filter'] ?? null, function($q,$v) {
            $q->whereRaw("date_from >= ?", [$v]);
        });
        $query->when($filters['date_to_filter'] ?? null, function($q,$v) {
            $q->whereRaw("COALESCE(date_to, date_from) <= ?", [$v]);
        });
        $query->when(isset($filters['has_photo']) && $filters['has_photo'] !== '',
            fn($q,$v)=> $filters['has_photo']==='1' ? $q->whereNotNull('photo') : $q->whereNull('photo'));
        return $query;
    }

    // ── Helpers ────────────────────────────────────────────────────
    public static function distinctValues(string $column): array
    {
        return static::selectRaw("DISTINCT TRIM($column) as $column")->orderBy($column)->pluck($column)->filter()->values()->toArray();
    }

    /** Formatted date range string e.g. "February 28 – March 2, 2026" */
    public function getDateRangeAttribute(): ?string
    {
        if (!$this->date_from) return null;
        $from = $this->date_from;
        $to   = $this->date_to;
        if (!$to || $to->format('Y-m-d') === $from->format('Y-m-d')) {
            return $from->format('F j, Y');
        }
        return $from->format('F j') . ' – ' . $to->format('F j, Y');
    }

    /** Return the media link directly */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) return null;
        // If it's already a full URL, return as-is
        if (str_starts_with($this->photo, 'http')) return $this->photo;
        // Otherwise it's a stored file path — return storage URL
        return \Illuminate\Support\Facades\Storage::url($this->photo);
    }

    /** No-op kept for compatibility */
    public function deletePhoto(): void {}
}