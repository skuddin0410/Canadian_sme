<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventFloorPlanMarker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventFloorPlanController extends Controller
{
    public function edit(Event $event)
    {
        $this->ensureEventAccess($event);

        $booths = Booth::where('event_id', $event->id)
            ->orderBy('booth_number')
            ->get(['id', 'title', 'booth_number']);

        $companies = Company::query()
            ->whereExists(function ($query) use ($event) {
                $query->select(DB::raw(1))
                    ->from('event_and_entity_link')
                    ->whereColumn('event_and_entity_link.entity_id', 'companies.id')
                    ->where('event_and_entity_link.entity_type', 'companies')
                    ->where('event_and_entity_link.event_id', $event->id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $markers = $event->floorPlanMarkers()->get();

        return view('events.floor-plan.edit', compact('event', 'booths', 'companies', 'markers'));
    }

    public function update(Request $request, Event $event)
    {
        $this->ensureEventAccess($event);

        $request->validate([
            'markers' => 'nullable|string',
        ]);

        $markers = json_decode($request->input('markers', '[]'), true);

        if (!is_array($markers)) {
            return back()->withErrors(['markers' => 'Invalid floor plan payload.'])->withInput();
        }

        EventFloorPlanMarker::where('event_id', $event->id)->delete();

        foreach (array_values($markers) as $index => $marker) {
            EventFloorPlanMarker::create([
                'event_id' => $event->id,
                'booth_id' => !empty($marker['booth_id']) ? $marker['booth_id'] : null,
                'company_id' => !empty($marker['company_id']) ? $marker['company_id'] : null,
                'label' => trim($marker['label'] ?? ('Booth ' . ($index + 1))),
                'x_percent' => $this->clampPercent($marker['x_percent'] ?? 10),
                'y_percent' => $this->clampPercent($marker['y_percent'] ?? 10),
                'width_percent' => $this->clampSize($marker['width_percent'] ?? 12),
                'height_percent' => $this->clampSize($marker['height_percent'] ?? 8),
                'color' => $marker['color'] ?? '#4361ee',
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('events.floor-plan.edit', $event)->with('success', 'Floor plan updated successfully.');
    }

    public function show(Event $event)
    {
        $this->ensureEventAccess($event);

        $markers = $event->floorPlanMarkers()->with(['booth', 'company'])->get();

        return view('events.floor-plan.show', compact('event', 'markers'));
    }

    protected function ensureEventAccess(Event $event): void
    {
        if (!isSuperAdmin() && (int) $event->created_by !== (int) auth()->id()) {
            abort(403);
        }
    }

    protected function clampPercent($value): float
    {
        return max(0, min(95, (float) $value));
    }

    protected function clampSize($value): float
    {
        return max(4, min(40, (float) $value));
    }
}
