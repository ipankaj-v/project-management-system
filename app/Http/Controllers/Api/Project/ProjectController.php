<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\SuccessResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orgId = $request->query('organization_id');
        $query = Project::with('organization', 'owner', 'status', 'members');

        if ($orgId) {
            $query->where('organization_id', $orgId);
        }

        $projects = $query->paginate(15);

        return new SuccessResource([
            'message' => 'Projects retrieved successfully',
            'data' => ProjectResource::collection($projects),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
        // return response()->json([
        //     'message' => 'Project created successfully',
        //     'data' => $request->all(),
        // ]);
        $validated = $request->validated();
        $members = $validated['members'] ?? [];
        unset($validated['members']);

        try {
            $project = Project::create($validated);

            // Attach members if provided
            if (!empty($members)) {
                $syncData = [];
                foreach ($members as $member) {
                    $syncData[$member['user_id']] = ['role' => $member['role'] ?? null];
                }
                $project->members()->sync($syncData);
            }

            $project->load('organization', 'owner', 'status', 'members');

            return new SuccessResource([
                'message' => 'Project created successfully',
                'data' => new ProjectResource($project),
                'status_code' => 201,
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to create project',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::with('organization', 'owner', 'status', 'members')->find($id);

        if (!$project) {
            return new ErrorResource([
                'message' => 'Project not found',
                'status_code' => 404,
            ]);
        }

        return new SuccessResource([
            'message' => 'Project retrieved successfully',
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, string $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return new ErrorResource([
                'message' => 'Project not found',
                'status_code' => 404,
            ]);
        }

        $validated = $request->validated();
        $members = $validated['members'] ?? [];
        unset($validated['members']);

        try {
            $project->update($validated);

            // Update members if provided
            if (!empty($members)) {
                $syncData = [];
                foreach ($members as $member) {
                    $syncData[$member['user_id']] = ['role' => $member['role'] ?? null];
                }
                $project->members()->sync($syncData);
            }

            $project->fresh()->load('organization', 'owner', 'status', 'members');

            return new SuccessResource([
                'message' => 'Project updated successfully',
                'data' => new ProjectResource($project),
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to update project',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return new ErrorResource([
                'message' => 'Project not found',
                'status_code' => 404,
            ]);
        }

        try {
            $project->delete();

            return new SuccessResource([
                'message' => 'Project deleted successfully',
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to delete project',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}
