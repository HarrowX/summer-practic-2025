<?php

namespace App\Http\Controllers;

use App\Services\PromoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PromoController extends Controller
{
    protected PromoService $promoService;

    public function __construct(PromoService $promoService)
    {
        $this->promoService = $promoService;
    }

    /**
     * Apply promo code
     */
    public function apply(Request $request): JsonResponse
    {
        $this->validate($request, [
            'code' => 'required|string|max:50|regex:/^[A-Z0-9_\-]+$/i'
        ]);

        $result = $this->promoService->applyPromo(
            Auth::user(),
            $request->input('code')
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function index()
    {
        return response()->json([
            'message' => 'Promo code system is working'
        ]);
    }
}
