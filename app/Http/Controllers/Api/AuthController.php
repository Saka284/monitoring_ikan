<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Fungsi login untuk API (digunakan oleh mobile atau perangkat).
     * Menghasilkan token yang digunakan untuk mengakses endpoint yang dilindungi.
     */
    public function login(Request $request)
    {
        // 1. Validasi input email dan password
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Jika validasi gagal, kirim error dalam format JSON
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. Coba mencocokkan email & password dengan data di database
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            
            // 4. Jika cocok, buatkan Token baru menggunakan Laravel Sanctum
            $token = $user->createToken('api-token')->plainTextToken;

            // 5. Kirim data user dan token balik ke pengakses
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 200);
        }

        // 6. Jika email/password tidak cocok
        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);
    }
}
