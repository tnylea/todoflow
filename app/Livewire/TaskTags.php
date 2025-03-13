<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Tag;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class TaskTags extends Component
{
    public ?int $taskId = null;
    public ?string $newTagName = '';
    public ?string $newTagColor = '#3b82f6'; // Default blue color
    public array $availableTags = [];
    public array $taskTags = [];
    
    public function mount(int $taskId): void
    {
        $this->taskId = $taskId;
        $this->loadTags();
    }
    
    public function render()
    {
        return view('livewire.task-tags');
    }
    
    public function loadTags(): void
    {
        if (Auth::check()) {
            // Load user's tags
            $this->availableTags = Auth::user()->tags()->get()->toArray();
            
            // Load task's tags
            $task = Task::findOrFail($this->taskId);
            $this->taskTags = $task->tags()->get()->toArray();
        }
    }
    
    public function createTag(): void
    {
        if (empty(trim($this->newTagName)) || !Auth::check()) {
            return;
        }
        
        $slug = Str::slug($this->newTagName);
        
        // Check if tag with this slug already exists for this user
        $existingTag = Auth::user()->tags()->where('slug', $slug)->first();
        
        if (!$existingTag) {
            // Create new tag
            $tag = Auth::user()->tags()->create([
                'name' => $this->newTagName,
                'slug' => $slug,
                'color' => $this->newTagColor,
            ]);
            
            // Attach tag to task
            $task = Task::findOrFail($this->taskId);
            $task->tags()->attach($tag->id);
            
            $this->newTagName = '';
            $this->newTagColor = '#3b82f6';
            $this->loadTags();
        } else {
            // Tag already exists, just attach it if not already attached
            $task = Task::findOrFail($this->taskId);
            if (!$task->tags()->where('tag_id', $existingTag->id)->exists()) {
                $task->tags()->attach($existingTag->id);
                $this->loadTags();
            }
        }
    }
    
    public function toggleTag(int $tagId): void
    {
        if (!Auth::check()) {
            return;
        }
        
        $task = Task::findOrFail($this->taskId);
        
        // Check if tag is already attached
        if ($task->tags()->where('tag_id', $tagId)->exists()) {
            // Detach tag
            $task->tags()->detach($tagId);
        } else {
            // Attach tag
            $task->tags()->attach($tagId);
        }
        
        $this->loadTags();
    }
    
    public function isTagAttached(int $tagId): bool
    {
        foreach ($this->taskTags as $tag) {
            if ($tag['id'] === $tagId) {
                return true;
            }
        }
        
        return false;
    }
}
