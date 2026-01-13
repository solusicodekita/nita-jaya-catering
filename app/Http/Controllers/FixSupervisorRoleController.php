<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class FixSupervisorRoleController extends Controller
{
    /**
     * Memberikan role admin kepada user yang memiliki is_supervisor = 1
     * tapi belum memiliki role admin
     */
    public function fix()
    {
        // Ambil role admin
        $roleAdmin = Role::where('name', 'admin')->first();
        
        if (!$roleAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Role "admin" tidak ditemukan. Silakan buat role admin terlebih dahulu.'
            ], 404);
        }

        // Ambil semua user dengan is_supervisor = 1
        $supervisors = User::where('is_supervisor', 1)->get();

        $fixed = [];
        $alreadyHasRole = [];

        foreach ($supervisors as $user) {
            // Cek apakah user sudah memiliki role admin
            if (!$user->hasRole('admin')) {
                $user->assignRole($roleAdmin);
                $fixed[] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->firstname . ' ' . $user->lastname
                ];
            } else {
                $alreadyHasRole[] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->firstname . ' ' . $user->lastname
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Role admin berhasil diberikan kepada user supervisor',
            'data' => [
                'fixed' => $fixed,
                'already_has_role' => $alreadyHasRole,
                'total_fixed' => count($fixed),
                'total_already_has_role' => count($alreadyHasRole)
            ]
        ]);
    }
}

