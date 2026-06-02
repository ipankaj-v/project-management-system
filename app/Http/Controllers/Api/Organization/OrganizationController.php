<?php

namespace App\Http\Controllers\Api\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\OrganizationRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\Organization\OrganizationResource;
use App\Http\Resources\SuccessResource;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organizations = Organization::with('owner', 'members')->paginate(15);

        return new SuccessResource([
            'message' => 'Organizations retrieved successfully',
            'data' => OrganizationResource::collection($organizations),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrganizationRequest $request)
    {

        $validated = $request->validated();

        try {
            $org = Organization::create($validated);

            return new SuccessResource([
                'message' => 'Organization created successfully',
                'data' => new OrganizationResource($org->load('owner', 'members')),
                'status_code' => 201,
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to create organization',
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
        $org = Organization::with('owner', 'members')->find($id);

        if (!$org) {
            return new ErrorResource([
                'message' => 'Organization not found',
                'status_code' => 404,
            ]);
        }

        return new SuccessResource([
            'message' => 'Organization retrieved successfully',
            'data' => new OrganizationResource($org),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrganizationRequest $request, string $id)
    {
        $org = Organization::find($id);

        if (!$org) {
            return new ErrorResource([
                'message' => 'Organization not found',
                'status_code' => 404,
            ]);
        }

        $validated = $request->validated();

        try {
            $org->update($validated);

            return new SuccessResource([
                'message' => 'Organization updated successfully',
                'data' => new OrganizationResource($org->fresh()->load('owner', 'members')),
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to update organization',
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
        $org = Organization::find($id);

        if (!$org) {
            return new ErrorResource([
                'message' => 'Organization not found',
                'status_code' => 404,
            ]);
        }

        try {
            $org->delete();

            return new SuccessResource([
                'message' => 'Organization deleted successfully',
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to delete organization',
                'errors' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}
