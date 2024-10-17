<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use libphonenumber\PhoneNumberUtil;

class GuestController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Guest::all());
    }

    public function store(StoreGuestRequest $request): JsonResponse
    {
        $country = $this->getCountryByPhone($request->phone);

        $guest = Guest::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'country'    => $country,
        ]);

        return response()->json($guest, 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(Guest::findOrFail($id));
    }

    public function update(UpdateGuestRequest $request, $id): JsonResponse
    {
        $guest = Guest::findOrFail($id);
        $country = $this->getCountryByPhone($request->phone ?? $guest->phone);
        $guest->update($request->validated() + ['country' => $country]);

        return response()->json($guest);
    }

    public function destroy($id): Response
    {
        $guest = Guest::findOrFail($id);
        $guest->delete();

        return response()->noContent();
    }

    private function getCountryByPhone($phone): ?string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($phone, null);
            $regionCode = $phoneUtil->getRegionCodeForNumber($numberProto);
            return $regionCode ?: null;
        } catch (\libphonenumber\NumberParseException $e) {
            return null;
        }
    }
}
