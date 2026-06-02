<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Models\Audit;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')->orderByDesc('created_at');

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        // Filter by event
        if ($request->has('event')) {
            $query->where('event', $request->query('event'));
        }

        // Filter by auditable type (model)
        if ($request->has('auditable_type')) {
            $query->where('auditable_type', $request->query('auditable_type'));
        }

        // Filter by auditable ID
        if ($request->has('auditable_id')) {
            $query->where('auditable_id', $request->query('auditable_id'));
        }

        // Date range filter
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->query('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->query('to_date'));
        }

        $audits = $query->paginate(20);

        return new SuccessResource([
            'message' => 'Audits retrieved successfully',
            'data' => AuditResource::collection($audits),
        ]);
    }

    public function show($id)
    {
        $audit = Audit::with('user', 'auditable')->find($id);

        if (!$audit) {
            return new ErrorResource([
                'message' => 'Audit not found',
                'status_code' => 404,
            ]);
        }

        return new SuccessResource([
            'message' => 'Audit retrieved successfully',
            'data' => new AuditResource($audit),
        ]);
    }
}
