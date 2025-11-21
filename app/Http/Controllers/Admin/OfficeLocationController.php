<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;

class OfficeLocationController extends Controller
{
    public function index()
    {
        $officeLocations = OfficeLocation::all();
        return view('admin.office-locations.index', compact('officeLocations'));
    }

    public function create()
    {
        return view('admin.office-locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        OfficeLocation::create($request->all());

        \App\Models\AuditLog::log('office_location_created', 'OfficeLocation', null);

        return redirect()
            ->route('admin.office-locations.index')
            ->with('success', 'Office location created successfully');
    }

    public function show(OfficeLocation $officeLocation)
    {
        return view('admin.office-locations.show', compact('officeLocation'));
    }

    public function edit(OfficeLocation $officeLocation)
    {
        return view('admin.office-locations.edit', compact('officeLocation'));
    }

    public function update(Request $request, OfficeLocation $officeLocation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        $oldValues = $officeLocation->toArray();

        $officeLocation->update($request->all());

        \App\Models\AuditLog::log('office_location_updated', 'OfficeLocation', $officeLocation->id, $oldValues, $officeLocation->toArray());

        return redirect()
            ->route('admin.office-locations.index')
            ->with('success', 'Office location updated successfully');
    }

    public function destroy(OfficeLocation $officeLocation)
    {
        $officeLocation->delete();

        \App\Models\AuditLog::log('office_location_deleted', 'OfficeLocation', $officeLocation->id);

        return redirect()
            ->route('admin.office-locations.index')
            ->with('success', 'Office location deleted successfully');
    }
}
