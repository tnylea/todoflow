<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Task;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;

class TodoList extends Component
{
    public ?string $newTaskTitle = '';
    public ?array $tasks = [];
    public ?int $editingTaskId = null;
    public ?string $editingTaskTitle = '';
    public ?string $taskNotes = '';
    public ?int $activeTaskId = null;
    public bool $isDrawerOpen = false;

    public function mount(): void
    {
        if (Auth::check()) {
            // Load tasks from database for authenticated users
            $this->loadTasksFromDatabase();
        }
    }

    public function render()
    {
        return view('livewire.todo-list');
    }

    public function addTask(): void
    {
        if (empty(trim($this->newTaskTitle))) {
            return;
        }

        if (Auth::check()) {
            // Store in database for authenticated users
            $task = Auth::user()->tasks()->create([
                'title' => $this->newTaskTitle,
                'order' => $this->getNextTaskOrder(),
            ]);

            $this->loadTasksFromDatabase();
        } else {
            // Create task for local storage
            $task = [
                'id' => uniqid(),
                'title' => $this->newTaskTitle,
                'completed' => false,
                'notes' => '',
                'order' => $this->getNextTaskOrder(),
                'tags' => [],
                'created_at' => now()->toIso8601String(),
            ];

            $this->tasks[] = $task;
            $this->dispatch('save-to-local-storage', tasks: $this->tasks);
        }

        $this->newTaskTitle = '';
    }

    public function startEditing(string|int $taskId): void
    {
        $this->editingTaskId = is_string($taskId) ? $taskId : (int) $taskId;
        
        if (Auth::check()) {
            $task = Auth::user()->tasks()->findOrFail($taskId);
            $this->editingTaskTitle = $task->title;
        } else {
            foreach ($this->tasks as $task) {
                if ($task['id'] == $taskId) {
                    $this->editingTaskTitle = $task['title'];
                    break;
                }
            }
        }
    }

    public function updateTask(): void
    {
        if (empty(trim($this->editingTaskTitle)) || !$this->editingTaskId) {
            return;
        }

        if (Auth::check()) {
            // Update in database
            $task = Auth::user()->tasks()->findOrFail($this->editingTaskId);
            $task->update(['title' => $this->editingTaskTitle]);
            $this->loadTasksFromDatabase();
        } else {
            // Update in local array
            foreach ($this->tasks as $index => $task) {
                if ($task['id'] == $this->editingTaskId) {
                    $this->tasks[$index]['title'] = $this->editingTaskTitle;
                    break;
                }
            }
            $this->dispatch('save-to-local-storage', tasks: $this->tasks);
        }

        $this->editingTaskId = null;
        $this->editingTaskTitle = '';
    }

    public function toggleComplete(string|int $taskId): void
    {
        if (Auth::check()) {
            // Toggle in database
            $task = Auth::user()->tasks()->findOrFail($taskId);
            $task->update(['completed' => !$task->completed]);
            $this->loadTasksFromDatabase();
        } else {
            // Toggle in local array
            foreach ($this->tasks as $index => $task) {
                if ($task['id'] == $taskId) {
                    $this->tasks[$index]['completed'] = !$task['completed'];
                    break;
                }
            }
            $this->dispatch('save-to-local-storage', tasks: $this->tasks);
        }
    }

    public function deleteTask(string|int $taskId): void
    {
        if (Auth::check()) {
            // Delete from database
            $task = Auth::user()->tasks()->findOrFail($taskId);
            $task->delete();
            $this->loadTasksFromDatabase();
        } else {
            // Delete from local array
            foreach ($this->tasks as $index => $task) {
                if ($task['id'] == $taskId) {
                    array_splice($this->tasks, $index, 1);
                    break;
                }
            }
            $this->dispatch('save-to-local-storage', tasks: $this->tasks);
        }

        if ($this->activeTaskId == $taskId) {
            $this->closeDrawer();
        }
    }

    public function openDrawer(string|int $taskId): void
    {
        $this->activeTaskId = is_string($taskId) ? $taskId : (int) $taskId;
        
        if (Auth::check()) {
            $task = Auth::user()->tasks()->findOrFail($taskId);
            $this->taskNotes = $task->notes ?? '';
        } else {
            foreach ($this->tasks as $task) {
                if ($task['id'] == $taskId) {
                    $this->taskNotes = $task['notes'] ?? '';
                    break;
                }
            }
        }
        
        $this->isDrawerOpen = true;
    }

    public function closeDrawer(): void
    {
        $this->isDrawerOpen = false;
        $this->activeTaskId = null;
        $this->taskNotes = '';
    }

    public function saveNotes(): void
    {
        if (!$this->activeTaskId) {
            return;
        }

        if (Auth::check()) {
            // Save to database
            $task = Auth::user()->tasks()->findOrFail($this->activeTaskId);
            $task->update(['notes' => $this->taskNotes]);
        } else {
            // Save to local array
            foreach ($this->tasks as $index => $task) {
                if ($task['id'] == $this->activeTaskId) {
                    $this->tasks[$index]['notes'] = $this->taskNotes;
                    break;
                }
            }
            $this->dispatch('save-to-local-storage', tasks: $this->tasks);
        }
    }

    public function loadTasksFromLocalStorage(array $tasks): void
    {
        if (!Auth::check()) {
            $this->tasks = $tasks;
        }
    }

    public function loadTasksFromDatabase(): void
    {
        if (Auth::check()) {
            $this->tasks = Auth::user()->tasks()
                ->orderBy('order')
                ->get()
                ->toArray();
        }
    }

    public function migrateLocalStorageToDatabase(): void
    {
        if (Auth::check() && !empty($this->tasks)) {
            foreach ($this->tasks as $task) {
                Auth::user()->tasks()->create([
                    'title' => $task['title'],
                    'notes' => $task['notes'] ?? '',
                    'completed' => $task['completed'] ?? false,
                    'order' => $task['order'] ?? 0,
                ]);
            }

            // Clear local storage
            $this->dispatch('clear-local-storage');
            
            // Reload tasks from database
            $this->loadTasksFromDatabase();
        }
    }

    private function getNextTaskOrder(): int
    {
        if (Auth::check()) {
            return Auth::user()->tasks()->max('order') + 1;
        } else {
            $maxOrder = 0;
            foreach ($this->tasks as $task) {
                $maxOrder = max($maxOrder, $task['order'] ?? 0);
            }
            return $maxOrder + 1;
        }
    }
}
