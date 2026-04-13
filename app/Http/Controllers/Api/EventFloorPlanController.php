<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventFloorPlanController extends Controller
{
    public function show(Request $request, Event $event)
    {
        $event->load([
            'mapImage',
            'floorPlanMarkers.booth',
            'floorPlanMarkers.company',
        ]);

        $search = trim((string) $request->query('search', ''));

        $markers = $event->floorPlanMarkers;

        if ($search !== '') {
            $needle = mb_strtolower($search);

            $markers = $markers->filter(function ($marker) use ($needle) {
                $haystacks = [
                    $marker->label,
                    optional($marker->booth)->booth_number,
                    optional($marker->booth)->title,
                    optional($marker->company)->name,
                ];

                foreach ($haystacks as $value) {
                    if ($value !== null && mb_stripos((string) $value, $needle) !== false) {
                        return true;
                    }
                }

                return false;
            })->values();
        }

        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'map_image_url' => optional($event->mapImage)->file_path
                    ? asset($event->mapImage->file_path)
                    : null,
                'has_floor_plan' => !empty(optional($event->mapImage)->file_path),
            ],
            'search' => $search !== '' ? $search : null,
            'total_markers' => $markers->count(),
            'markers' => $markers->map(function ($marker) {
                return [
                    'id' => $marker->id,
                    'label' => $marker->label,
                    'booth_id' => $marker->booth_id,
                    'booth_number' => optional($marker->booth)->booth_number,
                    'booth_title' => optional($marker->booth)->title,
                    'company_id' => $marker->company_id,
                    'company_name' => optional($marker->company)->name,
                    'x_percent' => (float) $marker->x_percent,
                    'y_percent' => (float) $marker->y_percent,
                    'width_percent' => (float) $marker->width_percent,
                    'height_percent' => (float) $marker->height_percent,
                    'color' => $marker->color,
                    'sort_order' => (int) $marker->sort_order,
                ];
            })->values(),
        ]);
    }
}
