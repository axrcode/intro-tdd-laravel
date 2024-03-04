<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();

            $data = $request->validate([
                'title' => 'required',
                'description' => 'required',
                'status' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $data['image'] = Storage::put('tasks', $request->file('image'));

            $task = new Task;
            $task->title = $data['title'];
            $task->description = $data['description'];
            $task->status = $data['status'];
            $task->image = $data['image'];
            $task->user_id = $user->id;
            $task->save();

            $codeStatus = 200;
            $response = [
                'status' => 'success',
                'data' => $task
            ];

        } catch (ValidationException  $ex) {

            $codeStatus = $ex->status;
            $response = [
                'status' => 'not-valid',
                'data' => [
                    'message' => $ex->getMessage(),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                ],
                'errors' => $ex->validator->errors(),
            ];

        } catch (Exception $ex) {

            $codeStatus = 401;
            $response = [
                'status' => 'error',
                'data' => [
                    'message' => $ex->getMessage(),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                ]
            ];
        }

        return response()->json($response, $codeStatus);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
