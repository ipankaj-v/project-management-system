<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\SuccessResource;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('user')->orderByDesc('created_at');

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        // Filter by subject type
        if ($request->has('subject_type')) {
            $query->where('subject_type', $request->query('subject_type'));
        }

        // Filter by subject ID
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->query('subject_id'));
        }

        // Date range filter
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->query('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->query('to_date'));
        }

        $activities = $query->paginate(20);

        return new SuccessResource([
            'message' => 'Activities retrieved successfully',
            'data' => ActivityResource::collection($activities),
        ]);
    }

    public function show($id)
    {
        $activity = Activity::with('user', 'subject')->find($id);

        if (!$activity) {
            return new \App\Http\Resources\ErrorResource([
                'message' => 'Activity not found',
                'status_code' => 404,
            ]);
        }

        return new SuccessResource([
            'message' => 'Activity retrieved successfully',
            'data' => new ActivityResource($activity),
        ]);
    }
}
