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
    private $fuelTypes = [
        Vehicle::FUEL_TYPE_GASOLINE,
        Vehicle::FUEL_TYPE_DIESEL,
        Vehicle::FUEL_TYPE_ELECTRIC,
        Vehicle::FUEL_TYPE_HYBRID,
    ];

    /**
     * Define the middleware for the VehicleController.
     *
     * @return array The middleware configurations for the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('role:' . implode('|',  [User::ROLE_ADMIN, User::ROLE_STAFF]), only: ['list', 'show']),
            new Middleware('role:' . User::ROLE_STAFF, only: ['store', 'update', 'destroy']),
        ];
    }

    /**
     * Fetch a paginated list of vehicles.
     *
     * Applies role and request filters to the vehicles query before paginating the results.
     * Returns a JSON response with a VehicleCollection resource.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $vehicles = Vehicle::applyRoleFilter()
            ->applyRequestFilters()
            ->paginate(request()->input('page_size', $this->perPage));

        return $this->apiResponse(
            'Vehicles list fetched successfully.',
            JsonResponse::HTTP_OK,
            new VehicleCollection($vehicles)
        );
    }

    /**
     * Store a newly created vehicle in storage.
     *
     * Validates the request data and creates a new vehicle record associated
     * with the authenticated user. Sends a notification to the manager
     * indicating that a new inspection has been started.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (int) date('Y'),
            'fuel_type' => 'required|string|in:' . implode(',', $this->fuelTypes),
            'color' => 'required|string|max:20',
            'milage' => 'required|numeric',
            'registration' => 'required|string|max:20',
        ], [
            'year.min' => 'The year must be greater than or equal to 1900.',
            'year.max' => 'The year must be less than or equal to ' . date('Y') . '.',
            'fuel_type.in' => 'The fuel type must be ' . implode(', ', $this->fuelTypes) . '.',
        ]);

        $vehicle = Vehicle::create([
            'user_id' => auth()->user()->id,
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'year' => $request->input('year'),
            'fuel_type' => $request->input('fuel_type'),
            'color' => $request->input('color'),
            'milage' => $request->input('milage'),
            'registration' => $request->input('registration'),
        ]);

        auth()->user()->manager->sendFirebaseNotification(
            'Inspection Started!',
            sprintf(
                'A new inspection has been started by %s on %s %s %s.',
                auth()->user()->name,
                $vehicle->make,
                $vehicle->model,
                $vehicle->year
            ),
        );

        return $this->apiResponse(
            'Vehicle created successfully.',
            JsonResponse::HTTP_CREATED,
            new VehicleResource($vehicle)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\JsonResponse
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
     * Validates the request data and updates the specified vehicle record.
     * Only the authenticated user who created the vehicle can update the vehicle.
     * Vehicles that have inspections cannot be updated.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // dd($vehicle->inspections());
        if ($vehicle->inspections()?->count() > 0) {
            throw new \Exception('Cannot update a vehicle that has inspections.', JsonResponse::HTTP_FORBIDDEN);
        }

        $request->validate([
            'make' => 'sometimes|required|string|max:50',
            'model' => 'sometimes|required|string|max:50',
            'year' => 'sometimes|required|integer|min:1900|max:' . (int) date('Y'),
            'fuel_type' => 'sometimes|string|in:' . implode(',', $this->fuelTypes),
            'color' => 'sometimes|required|string|max:20',
            'milage' => 'sometimes|numeric',
            'registration' => 'sometimes|string|max:20',
        ], [
            'year.min' => 'The year must be greater than or equal to 1900.',
            'year.max' => 'The year must be less than or equal to ' . date('Y') . '.',
            'fuel_type.in' => 'The fuel type must be ' . implode(', ', $this->fuelTypes) . '.',
        ]);

        $vehicle->update([
            'make' => $request->input('make', $vehicle->make),
            'model' => $request->input('model', $vehicle->model),
            'year' => $request->input('year', $vehicle->year),
            'fuel_type' => $request->input('fuel_type', $vehicle->fuel_type),
            'color' => $request->input('color', $vehicle->color),
            'milage' => $request->input('milage', $vehicle->milage),
            'registration' => $request->input('registration', $vehicle->registration),
        ]);

        return $this->apiResponse(
            'Vehicle updated successfully.',
            JsonResponse::HTTP_OK,
            new VehicleResource($vehicle)
        );
    }

    /**
     * Remove the specified vehicle from storage.
     *
     * Checks if the vehicle has any associated inspections and throws an exception
     * if it does. Deletes the vehicle if no inspections are found and returns a
     * success response.
     *
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception If the vehicle has inspections.
     */
    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->inspections()?->count() > 0) {
            throw new \Exception('Cannot update a vehicle that has inspections.', JsonResponse::HTTP_FORBIDDEN);
        }

        $vehicle->delete();

        return $this->apiResponse(
            'Vehicle deleted successfully.',
            JsonResponse::HTTP_OK,
        );
    }
}
