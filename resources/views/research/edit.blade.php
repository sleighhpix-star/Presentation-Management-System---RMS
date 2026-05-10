@extends('layouts.app')
@section('title','Edit – '.$record->research_title)
@section('page-title','Edit Research Record')

@section('content')
<div class="card">
    <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span>
            <i class="bi bi-pencil-square me-2" style="color:var(--gold);"></i>
            Editing: <em style="font-family:'DM Sans',sans-serif;font-style:normal;color:var(--text-muted);">{{ Str::limit($record->research_title,55) }}</em>
        </span>
        <div class="d-flex gap-2">
            <a href="{{ route('research.show',$record) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-eye me-1"></i> View
            </a>
            <a href="{{ route('research.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('research.update',$record) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('research.partials.form-fields')
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('research.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-navy px-5">
                    <i class="bi bi-save me-2"></i> Update Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
