@extends('layouts.admin')
@section('title', 'Edit Template')
@section('page-title', 'Edit Template')
@section('page-subtitle', 'Kelola mode render dan konfigurasi template')

@section('content')
<div class="max-w-5xl">
    <div class="card p-6">
        <form method="POST" action="{{ route('admin.templates.update', $template) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.templates._form')
        </form>
    </div>
</div>
@endsection
