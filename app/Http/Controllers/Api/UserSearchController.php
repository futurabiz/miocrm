<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserSearchController extends Controller
{
    /**
     * Cerca utenti per nome o email.
     * Restituisce un JSON formattato per Select2.
     */
    public function search(Request $request): JsonResponse
    {
        $searchTerm = $request->input('q');

        if (!$searchTerm) {
            return response()->json(['results' => []]);
        }

        $users = User::where('name', 'LIKE', '%' . $searchTerm . '%')
                     ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                     ->limit(10)
                     ->get(['id', 'name']);

        $results = $users->map(function ($user) {
            return [
                'id'   => $user->id,
                'text' => $user->name,
            ];
        });

        return response()->json(['results' => $results]);
    }
}