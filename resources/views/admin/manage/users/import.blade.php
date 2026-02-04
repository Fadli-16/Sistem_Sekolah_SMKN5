@extends('admin.layouts.superadmin')

@section('content')
<div class="sa-card">
    <div class="sa-card-header">
        <h5>Import Users from CSV</h5>
    </div>
    <div class="sa-card-body">
        <form action="{{ route('admin.manage.users.import.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="csv_file" class="form-label">CSV File</label>
                <input type="file" name="csv_file" id="csv_file" class="form-control @error('csv_file') is-invalid @enderror" required>
                @error('csv_file')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="sa-btn sa-btn-primary">
                <i class="bi bi-upload"></i> Upload & Import
            </button>
            <a href="{{ route('admin.manage.users') }}" class="sa-btn sa-btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection