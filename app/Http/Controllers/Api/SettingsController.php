<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'business' => [
                'business_name' => $user->business_name,
                'gstin' => $user->gstin,
                'address' => $user->address,
                'city' => $user->city,
                'state' => $user->state,
                'state_code' => $user->state_code,
                'pincode' => $user->pincode,
                'phone' => $user->phone,
                'email' => $user->email,
            ],
            'bill' => [
                'bill_prefix' => $user->getSetting('bill_prefix', 'INV'),
                'bill_start_number' => $user->getSetting('bill_start_number', 1),
                'terms_conditions' => $user->getSetting('terms_conditions', ''),
                'bank_details' => $user->getSetting('bank_details', ''),
            ],
            'images' => [
                'business_logo' => $user->getSetting('business_logo'),
                'business_signature' => $user->getSetting('business_signature'),
            ]
        ]);
    }

    public function updateBusiness(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'gstin' => ['nullable', 'string', 'size:15', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'state_code' => ['nullable', 'string', 'size:2', 'regex:/^\d{2}$/'],
            'pincode' => ['nullable', 'string', 'size:6', 'regex:/^\d{6}$/'],
            'phone' => ['nullable', 'string', 'max:15'],
        ]);

        $request->user()->update($validated);

        return response()->json([
            'message' => 'Business settings updated successfully.',
        ]);
    }

    public function updateAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $request->user()->id],
        ]);

        $request->user()->update($validated);

        return response()->json([
            'message' => 'Account updated successfully.',
            'user' => $request->user(),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }

    public function updateBillSettings(Request $request)
    {
        $validated = $request->validate([
            'bill_prefix' => ['nullable', 'string', 'max:10'],
            'bill_start_number' => ['nullable', 'integer', 'min:1'],
            'terms_conditions' => ['nullable', 'string', 'max:2000'],
            'bank_details' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                $user->setSetting($key, $value);
            }
        }

        return response()->json([
            'message' => 'Bill settings updated successfully.',
        ]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'type' => ['required', 'string', 'in:logo,signature'],
        ]);

        $user = $request->user();
        $type = $request->input('type');
        $settingKey = $type === 'logo' ? 'business_logo' : 'business_signature';

        $file = $request->file('image');
        $path = $file->store('uploads/' . $type, 'public');

        $user->setSetting($settingKey, $path);

        return response()->json([
            'message' => ucfirst($type) . ' uploaded successfully.',
            'path' => $path,
            'url' => asset('storage/' . $path)
        ]);
    }
}
