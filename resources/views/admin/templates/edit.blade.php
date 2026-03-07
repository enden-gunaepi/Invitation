@extends('layouts.admin')
@section('title', 'Edit Template')
@section('page-title', 'Edit Template')

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        <form method="POST" action="{{ route('admin.templates.update', $template) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-5">
                <label class="form-label">Nama Template</label>
                <input type="text" name="name" value="{{ old('name', $template->name) }}" class="form-input" required>
            </div>
            <div class="grid grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-input">
                        @foreach(['wedding', 'birthday', 'graduation', 'corporate', 'other'] as $cat)
                            <option value="{{ $cat }}" {{ $template->category === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">HTML Path</label>
                    <input type="text" name="html_path" value="{{ old('html_path', $template->html_path) }}" class="form-input">
                </div>
            </div>
            <div class="mb-5">
                <label class="form-label">Thumbnail</label>
                <input type="file" name="thumbnail" class="form-input" accept="image/*">
                @if($template->thumbnail)
                    <p class="text-xs text-slate-500 mt-1">Sudah ada: {{ $template->thumbnail }}</p>
                @endif
            </div>
            <div class="flex items-center gap-6 mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_premium" value="1" {{ $template->is_premium ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-indigo-500">
                    <span class="text-sm text-slate-400">Premium</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-indigo-500">
                    <span class="text-sm text-slate-400">Aktif</span>
                </label>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary text-sm"><i class="fas fa-save mr-2"></i> Update</button>
                <a href="{{ route('admin.templates.index') }}" class="btn-outline text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
