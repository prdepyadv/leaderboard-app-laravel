<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $sortby = $request->query('sortby') ?? 'points';
        $order = $request->query('order') ?? 'desc';

        $users = User::orderBy($sortby, $order)->get();
        return response()->json($users);
    }

    public function findOne($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'age' => 'required|integer|min:1|max:60',
            'address' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'age' => $validated['age'],
            'address' => $validated['address'],
            'points' => 0,
        ]);

        return response()->json($user, 201);
    }

    public function addPoints($id)
    {
        $user = User::findOrFail($id);
        $user->points += 1;
        $user->save();

        return response()->json($user);
    }

    public function subPoints($id)
    {
        $user = User::findOrFail($id);
        if ($user->points > 0) {
            $user->points -= 1;
        }
        $user->save();

        return response()->json($user);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function resetScores()
    {
        User::query()->update(['points' => 0]);
        return response()->json(['message' => 'All user scores have been reset to 0']);
    }
}
