<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private TaskManager $taskManager
    ) {}

    public function index(User $user, Request $request): JsonResponse
    {
        $completed = null;

        if ($request->has('completed')) {
            $completed = $request->boolean('completed');
        }

        $tasks = $this->taskManager->listUserTasks(
            $user,
            $completed,
            $request->input('sort', 'date')
        );

        return response()->json([
            'data' => $tasks,
        ]);
    }

    public function store(
        StoreTaskRequest $request,
        User $user
    ): JsonResponse {
        $task = $this->taskManager->createTask(
            $user,
            $request->string('title')->toString(),
            $request->string('description')->toString()
        );

        return response()->json([
            'message' => 'Tarea creada correctamente.',
            'data' => $task,
        ], 201);
    }

    public function complete(Task $task): JsonResponse
    {
        abort_unless(
            $task->user_id === auth()->id(),
            403
        );

        $task = $this->taskManager->completeTask($task);

        return response()->json([
            'message' => 'Tarea completada correctamente.',
            'data' => $task,
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        abort_unless(
            $task->user_id === auth()->id(),
            403
        );
        $this->taskManager->deleteTask($task);

        return response()->json([
            'message' => 'Tarea eliminada correctamente.',
        ]);
    }
}
