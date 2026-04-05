<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\TokenDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    private function findByPlainToken(string $plainToken): ?Token
    {
        return Token::where('token', hash('sha256', $plainToken))->first();
    }

    public function showLoginPage()
    {
        return view('login');
    }

    public function dashboard()
    {
        return view('token-dashboard');
    }

    public function index()
    {
        $tokens = Token::with('devices')->latest()->get();

        return response()->json(
            $tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'is_active' => $token->is_active,
                    'max_devices' => $token->max_devices,
                    'expired_at' => $token->expires_at,
                    'is_expired' => $token->expires_at
                        ? now()->gt($token->expires_at)
                        : false,
                    'device_count' => $token->devices->count(),
                    'devices' => $token->devices->map(function ($device) {
                        return [
                            'device_id' => $device->device_id,
                            'login_at' => $device->created_at
                        ];
                    })
                ];
            })
        );
    }

    public function generate(Request $request)
    {
        $request->validate([
            'name' => 'string|nullable',
            'max_devices' => 'integer|min:1',
            'expired_hours' => 'integer|min:1',
        ]);

        $plainToken = Str::random(40);

        $token = Token::create([
            'name' => $request->name,
            'token' => hash('sha256', $plainToken),
            'max_devices' => $request->max_devices ?? 1,
            'expires_at' => now()->addHours($request->expired_hours ?? 24),
        ]);

        return response()->json([
            'token' => $plainToken,
            'id' => $token->id,
            'name' => $token->name,
            'is_active' => $token->is_active,
            'max_devices' => $token->max_devices,
            'expired_at' => $token->expires_at,
            'expires_at' => $token->expires_at
                ?->timezone('Asia/Jakarta')
                ?->format('Y-m-d H:i:s'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'device_id' => 'required'
        ]);

        $token = $this->findByPlainToken($request->token);

        if (!$token || !$token->is_active) {
            return response()->json([
                'message' => 'Token tidak valid'
            ], 401);
        }

        if ($token->expires_at && now()->gt($token->expires_at)) {
            return response()->json([
                'message' => 'Token expired'
            ], 401);
        }

        $deviceExists = TokenDevice::where('token_id', $token->id)
            ->where('device_id', $request->device_id)
            ->exists();

        if (!$deviceExists) {
            if ($token->devices()->count() >= $token->max_devices) {
                return response()->json([
                    'message' => 'Melebihi batas device'
                ], 403);
            }

            TokenDevice::create([
                'token_id' => $token->id,
                'device_id' => $request->device_id
            ]);
        }

        return response()->json([
            'message' => 'Login berhasil'
        ]);
    }

    public function logout(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'device_id' => 'required'
        ]);

        $token = $this->findByPlainToken($request->token);

        if (!$token) {
            return response()->json([
                'message' => 'Token tidak ditemukan'
            ], 404);
        }

        TokenDevice::where('token_id', $token->id)
            ->where('device_id', $request->device_id)
            ->delete();

        return response()->json([
            'message' => 'Device berhasil logout'
        ]);
    }

    public function logoutAllDevices(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $token = $this->findByPlainToken($request->token);

        if (!$token) {
            return response()->json([
                'message' => 'Token tidak ditemukan'
            ], 404);
        }

        $token->devices()->delete();

        return response()->json([
            'message' => 'Semua device logout'
        ]);
    }

    public function revoke(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $token = $this->findByPlainToken($request->token);

        if (!$token) {
            return response()->json([
                'message' => 'Token tidak ditemukan'
            ], 404);
        }

        $token->update([
            'is_active' => false
        ]);

        return response()->json([
            'message' => 'Token berhasil dinonaktifkan'
        ]);
    }

    public function checkToken(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $token = $this->findByPlainToken($request->token);

        if (!$token) {
            return response()->json([
                'active' => false,
                'message' => 'Token tidak ditemukan'
            ], 404);
        }

        if (!$token->is_active) {
            return response()->json([
                'active' => false,
                'message' => 'Token tidak aktif'
            ]);
        }

        if ($token->expires_at && now()->gt($token->expires_at)) {
            return response()->json([
                'active' => false,
                'expired' => true,
                'message' => 'Token sudah expired'
            ]);
        }

        return response()->json([
            'active' => true,
            'expired' => false,
            'message' => 'Token valid',
        ]);
    }

    public function destroy(Token $token)
    {
        $token->delete();

        return response()->json([
            'message' => 'Token berhasil dihapus'
        ]);
    }

    public function deactivate(Token $token)
    {
        $token->update([
            'is_active' => false,
        ]);

        return response()->json([
            'message' => 'Token berhasil dinonaktifkan',
        ]);
    }
}
