@extends('layouts.admin.layout')

@section('content')
<div class="flex flex-row justify-between items-center mb-4">
    <h1 class="admin-h1">Prayer Categories</h1>
    <a href="{{ url('/admin/prayercategory/create') }}" class="btn btn-primary">+ Add Category</a>
</div>

@if(session('success'))
<div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger mb-4">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CSS Class</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prayers</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($categories as $cat)
            <tr>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $cat->sort_order }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">{{ $cat->emoji }}</span>
                        <div>
                            <div class="font-medium text-gray-900">{{ $cat->name }}</div>
                            @if($cat->description)
                            <div class="text-xs text-gray-500">{{ Str::limit($cat->description, 60) }}</div>
                            @endif
                        </div>
                        <span class="ml-2 w-5 h-5 rounded-full border border-gray-200 flex-shrink-0"
                              style="background-color: {{ $cat->display_color }}"></span>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $cat->css_class }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    <span class="text-yellow-700">{{ $cat->prayers()->where('status', 'PENDING')->count() }} pending</span> /
                    <span class="text-green-700">{{ $cat->prayers()->where('status', 'ACTIVE')->count() }} active</span>
                </td>
                <td class="px-4 py-3">
                    @if($cat->is_active)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="{{ url('/admin/prayercategory/edit/' . $cat->id) }}" class="btn btn-sm btn-secondary mr-2">Edit</a>
                    <form method="POST" action="{{ url('/admin/prayercategory/delete/' . $cat->id) }}" class="inline"
                          onsubmit="return confirm('Delete category \'{{ addslashes($cat->name) }}\'? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">No categories yet.
                    <a href="{{ url('/admin/prayercategory/create') }}" class="text-indigo-600 hover:underline">Add one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
