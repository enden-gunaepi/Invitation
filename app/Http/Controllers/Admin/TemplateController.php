<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::latest()->paginate(12);
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:wedding,birthday,graduation,corporate,other',
            'thumbnail' => 'nullable|image|max:2048',
            'html_path' => 'nullable|string',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_premium'] = $request->has('is_premium');
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('templates', 'public');
        }

        Template::create($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil ditambahkan!');
    }

    public function edit(Template $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:wedding,birthday,graduation,corporate,other',
            'thumbnail' => 'nullable|image|max:2048',
            'html_path' => 'nullable|string',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_premium'] = $request->has('is_premium');
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('templates', 'public');
        }

        $template->update($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil diupdate!');
    }

    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template berhasil dihapus!');
    }
}
