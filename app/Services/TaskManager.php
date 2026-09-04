<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class TaskManager
{
    public function createTask(
        User $user,
        string $title,
        string $description
    ): Task {
        return $user->tasks()->create([
            'title' => $title,
            'description' => $description,
            'completed' => false,
        ]);
    }

    public function completeTask(Task $task): Task
    {
        $task->update([
            'completed' => true,
        ]);

        return $task->fresh();
    }

    public function listUserTasks(
        User $user,
        ?bool $completed = null,
        string $sort = 'date'
    ): Collection {
        $query = $user->tasks();

        if ($completed !== null) {
            $query->where('completed', $completed);
        }

        if ($sort === 'title') {
            $query->orderBy('title');
        } else {
            $query->latest();
        }

        return $query->get();
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();
    }
}
