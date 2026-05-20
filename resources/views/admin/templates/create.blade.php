@extends('layouts.admin')
@section('title', 'Tambah Template')
@section('page-title', 'Tambah Template')
@section('page-subtitle', 'Pilih mode Legacy Blade atau Builder CMS sebelum menyimpan')

@section('content')
<div class="max-w-5xl">
    <div class="card p-6">
        <form method="POST" action="{{ route('admin.templates.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.templates._form')
        </form>
    </div>
</div>
@endsection
