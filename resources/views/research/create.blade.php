{{-- CREATE --}}
@extends('layouts.app')
@section('title','Add Research Record')
@section('page-title','Add Research Record')

@section('content')
<div class="card">
    <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between">
        <span><i class="bi bi-plus-circle me-2" style="color:var(--gold);"></i>New Research Record</span>
        <a href="{{ route('research.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('research.store') }}" enctype="multipart/form-data">
            @csrf
            @php $record = null; @endphp
            @include('research.partials.form-fields')
            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                <a href="{{ route('research.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-navy px-5">
                    <i class="bi bi-save me-2"></i> Save Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
