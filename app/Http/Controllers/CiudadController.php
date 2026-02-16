<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('query', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $ciudades = Ciudad::query()
            ->select(['citycodigo', 'citynomb', 'citydepto', 'cityNdepto'])
            ->where('citynomb', 'like', '%'.$query.'%')
            ->orderBy('citynomb')
            ->limit(15)
            ->get();

        return response()->json($ciudades);
    }
}
