<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class EncryptionController extends Controller
{
    /**
     * Get the public encryption key for frontend
     */
    public function getPublicKey()
    {
        // Get the full encryption key
        $fullKey = Config::get('encryption.key');

        // Generate a public key from the full key (this is just an example)
        // In production, you might want to use a more sophisticated method
        $publicKey = substr(hash('sha256', $fullKey), 0, 32);

        return response()->json([
            'public_key' => $publicKey
        ]);
    }
}
