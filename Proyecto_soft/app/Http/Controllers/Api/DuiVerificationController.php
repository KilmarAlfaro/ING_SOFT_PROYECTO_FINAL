<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\ValidDui;
use App\Services\DuiService;
use Illuminate\Http\Request;

class DuiVerificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'dui' => ['required', 'string', new ValidDui],
        ]);

        $formatted = DuiService::format($validated['dui']);

        return response()->json([
            'dui' => $formatted,
            'valid' => true,
            'message' => 'El DUI es válido y puede continuar con el registro.',
        ]);
    }
}
