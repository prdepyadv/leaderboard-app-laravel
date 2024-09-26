<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Jobs\SaveUserAddressInQR;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $sortby = $request->query('sortby') ?? 'points';
            $order = $request->query('order') ?? 'desc';

            $users = User::orderBy($sortby, $order)->get();
            return response()->json([
                'error' => false,
                'message' => 'Users retrieved successfully',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving users: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function findOne($id)
    {
        try {
            $user = User::where('id', $id)->first();
            if (!$user) {
                return response()->json([
                    'error' => true,
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'error' => false,
                'message' => 'User retrieved successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving user: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function add(Request $request)
    {
        try {
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
                'name' => $request->name,
                'age' => $request->age,
                'address' => $request->address,
                'points' => 0,
            ]);
            SaveUserAddressInQR::dispatch($user->id);

            return response()->json([
                'error' => false,
                'message' => 'User added successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error adding user: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function addPoints($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->points += 1;
            $user->save();

            return response()->json([
                'error' => false,
                'message' => 'User points incremented successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error incrementing user points: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function subPoints($id)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error decrementing user points: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $user = User::where('id', $id)->first();
            if (!$user) {
                return response()->json([
                    'error' => true,
                    'message' => 'User not found',
                ], 404);
            }
            $user->delete();

            return response()->json([
                'error' => false,
                'message' => 'User deleted successfully',
                'data' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function resetScores()
    {
        try {
            User::where('points', '>', 0)->update(['points' => 0]);
            return response()->json([
                'error' => false,
                'message' => 'All user scores have been reset to 0',
                'data' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting user scores: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function scoreReport()
    {
        try {
            $users = User::orderBy("points", "desc")->get();
            $groupedUsersByPoints = $users->groupBy('points')->map(function ($group) {
                return [
                    'names' => $group->pluck('name'),
                    'average_age' => round($group->avg('age'))
                ];
            });
            return response()->json([
                'error' => false,
                'message' => 'Score report generated successfully',
                'data' => $groupedUsersByPoints
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating score report: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong. Please try again later.',
            ], 500);
        }
    }
}
