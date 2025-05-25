<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\Vehicle\VehicleResource;
use App\Http\Resources\Vehicle\VehicleCollection;
use Illuminate\Routing\Controllers\HasMiddleware;

class VehicleController extends BaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . implode('|',  [User::ROLE_ADMIN, User::ROLE_STAFF]), only: ['list', 'show']),
            new Middleware('role:' . User::ROLE_STAFF, only: ['store', 'update', 'destroy']),
        ];
    }

    /**
     * List all vehicles
     *
     * @param Request $request
     */
    public function list()
    {
        $vehicles = Vehicle::managedByUser()
            ->applyFilters()
            ->paginate(request()->input('page_size', $this->perPage));

        return $this->apiResponse(
            'Vehicles list fetched successfully.',
            JsonResponse::HTTP_OK,
            new VehicleCollection($vehicles)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (int) date('Y'),
            'fuel_type' => 'required|string|in:gasoline,diesel,electric,hybrid',
            'address' => 'required|string|max:200',
            'color' => 'required|string|max:20',
            'price' => 'required|integer|min:0',
            'license_plate' => 'required|string|max:20|regex:/^[A-Za-z0-9]+$/',
        ], [
            'year.min' => 'The year must be greater than or equal to 1900.',
            'year.max' => 'The year must be less than or equal to ' . date('Y') . '.',
            'fuel_type.in' => 'The fuel type must be gasoline, diesel, electric, or hybrid.',
            'license_plate.regex' => 'The license plate must contain only letters and numbers.',
        ]);

        $vehicle = Vehicle::create([
            'user_id' => auth()->user()->id,
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'year' => $request->input('year'),
            'fuel_type' => $request->input('fuel_type'),
            'address' => $request->input('address'),
            'color' => $request->input('color'),
            'price' => $request->input('price'),
            'license_plate' => $request->input('license_plate'),
        ]);

        return $this->apiResponse(
            'Vehicle created successfully.',
            JsonResponse::HTTP_CREATED,
            new VehicleResource($vehicle)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  Vehicle $vehicle
     */
    public function show(Vehicle $vehicle)
    {
        return $this->apiResponse(
            'Vehicle details fetched successfully.',
            JsonResponse::HTTP_OK,
            new VehicleResource($vehicle)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Vehicle  $vehicle
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->inspections()?->count() > 0) {
            throw new \Exception('Cannot update a vehicle that has inspections.', JsonResponse::HTTP_FORBIDDEN);
        }

        $request->validate([
            'make' => 'sometimes|required|string|max:50',
            'model' => 'sometimes|required|string|max:50',
            'year' => 'sometimes|required|integer|min:1900|max:' . (int) date('Y'),
            'fuel_type' => 'sometimes|required|string|in:gasoline,diesel,electric,hybrid',
            'address' => 'sometimes|required|string|max:200',
            'color' => 'sometimes|required|string|max:20',
            'price' => 'sometimes|required|integer|min:0',
            'license_plate' => 'sometimes|required|string|max:20|regex:/^[A-Za-z0-9]+$/',
        ]);

        $vehicle->update([
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'year' => $request->input('year'),
            'fuel_type' => $request->input('fuel_type'),
            'address' => $request->input('address'),
            'color' => $request->input('color'),
            'price' => $request->input('price'),
            'license_plate' => $request->input('license_plate'),
        ]);

        return $this->apiResponse(
            'Vehicle updated successfully.',
            JsonResponse::HTTP_OK,
            new VehicleResource($vehicle)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->inspections()?->count() > 0) {
            throw new \Exception('Cannot update a veh$vehicle that has inspections.', JsonResponse::HTTP_FORBIDDEN);
        }

        $vehicle->delete();

        return $this->apiResponse(
            'Vehicle deleted successfully.',
            JsonResponse::HTTP_OK,
        );
    }
}
