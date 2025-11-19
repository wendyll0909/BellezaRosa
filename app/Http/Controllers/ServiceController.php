<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        // Check if user has access
        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            abort(403, 'Unauthorized access.');
        }

        $services = Service::with('category')->get();
        $categories = ServiceCategory::all();

        return view('dashboard.services.index', compact('services', 'categories'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $categories = ServiceCategory::all();
        return view('dashboard.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:service_categories,id',
            'duration_minutes' => 'required|integer|min:1',
            'price_regular' => 'required|numeric|min:0',
            'price_premium' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Service::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'duration_minutes' => $request->duration_minutes,
            'price_regular' => $request->price_regular,
            'price_premium' => $request->price_premium,
            'is_premium' => $request->has('is_premium'),
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('dashboard.services.index')
            ->with('success', 'Service created successfully!');
    }

    public function show(Service $service)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isStaff()) {
            abort(403, 'Unauthorized access.');
        }

        return view('dashboard.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $categories = ServiceCategory::all();
        return view('dashboard.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:service_categories,id',
            'duration_minutes' => 'required|integer|min:1',
            'price_regular' => 'required|numeric|min:0',
            'price_premium' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $service->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'duration_minutes' => $request->duration_minutes,
            'price_regular' => $request->price_regular,
            'price_premium' => $request->price_premium,
            'is_premium' => $request->has('is_premium'),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('dashboard.services.index')
            ->with('success', 'Service updated successfully!');
    }

    public function destroy(Service $service)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $service->delete();

        return redirect()->route('dashboard.services.index')
            ->with('success', 'Service deleted successfully!');
    }
}