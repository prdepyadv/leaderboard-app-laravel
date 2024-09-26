<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $sortby = $request->query('sortby') ?? 'points';
        $order = $request->query('order') ?? 'desc';

        $users = User::orderBy($sortby, $order)->get();
        return response()->json([
            'error' => false,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ]);
    }

    public function findOne($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'error' => false,
            'message' => 'User retrieved successfully',
            'data' => $user
        ]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users',
            'age' => 'required|integer|min:1|max:60',
            'address' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Validation error occurred.',
                'data' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'age' => $validated['age'],
            'address' => $validated['address'],
            'points' => 0,
        ]);

        return response()->json([
            'error' => false,
            'message' => 'User added successfully',
            'data' => $user
        ], 201);
    }

    public function addPoints($id)
    {
        $user = User::findOrFail($id);
        $user->points += 1;
        $user->save();

        return response()->json([
            'error' => false,
            'message' => 'User points incremented successfully',
            'data' => $user
        ]);
    }

    public function subPoints($id)
    {
        $user = User::findOrFail($id);
        if ($user->points > 0) {
            $user->points -= 1;
        }
        $user->save();

        return response()->json([
            'error' => false,
            'message' => 'User points decremented successfully',
            'data' => $user
        ]);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'error' => false,
            'message' => 'User deleted successfully',
            'data' => null
        ]);
    }

    public function resetScores()
    {
        User::query()->update(['points' => 0]);
        return response()->json([
            'error' => false,
            'message' => 'All user scores have been reset to 0',
            'data' => null
        ]);
    }

    public function scoreReport()
    {
        $users = User::orderBy("points", "desc")->get();
        $groupedUsersByPoints = $users->groupBy('points')->map(function ($group) {
            return [
                'names' => $group->pluck('name'),
                'average_age' => round($group->avg('age'))
            ];
        });

        $groupedUsersByPoints = $groupedUsersByPoints->sortKeysDesc();
        return response()->json([
            'error' => false,
            'message' => 'Score report generated successfully',
            'data' => $groupedUsersByPoints
        ]);
    }
}
