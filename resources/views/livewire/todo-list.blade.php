<div class="w-full max-w-4xl mx-auto">
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-zinc-900 dark:text-white mb-6">My Tasks</h2>
            
            <!-- Add Task Form -->
            <form wire:submit="addTask" class="mb-6">
                <div class="flex items-center gap-2">
                    <flux:input 
                        wire:model="newTaskTitle" 
                        placeholder="Add a new task..." 
                        class="flex-1"
                        autofocus
                    />
                    <flux:button type="submit">Add Task</flux:button>
                </div>
            </form>
            
            <!-- Task List -->
            <div class="space-y-3">
                @if(empty($tasks))
                    <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                        <p>No tasks yet. Add your first task above!</p>
                    </div>
                @else
                    @foreach($tasks as $task)
                        <div class="flex items-start gap-3 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 group">
                            <div class="pt-1">
                                <flux:checkbox 
                                    wire:click="toggleComplete('{{ $task['id'] }}')"
                                    :checked="$task['completed'] ?? false"
                                />
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                @if($editingTaskId === $task['id'])
                                    <form wire:submit="updateTask" class="flex items-center gap-2">
                                        <flux:input 
                                            wire:model="editingTaskTitle" 
                                            class="flex-1"
                                            autofocus
                                        />
                                        <flux:button type="submit" size="sm">Save</flux:button>
                                        <flux:button 
                                            type="button" 
                                            size="sm" 
                                            variant="secondary" 
                                            wire:click="$set('editingTaskId', null)"
                                        >
                                            Cancel
                                        </flux:button>
                                    </form>
                                @else
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-base font-medium text-zinc-900 dark:text-white {{ $task['completed'] ? 'line-through text-zinc-500 dark:text-zinc-400' : '' }}">
                                                {{ $task['title'] }}
                                            </h3>
                                            
                                            @if(!empty($task['notes']))
                                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400 truncate">
                                                    {{ Str::limit($task['notes'], 100) }}
                                                </p>
                                            @endif
                                            
                                            <!-- Tags display -->
                                            @if(Auth::check() && isset($task['id']) && is_numeric($task['id']))
                                                <div class="mt-2 flex flex-wrap gap-1">
                                                    @foreach(App\Models\Task::find($task['id'])->tags as $tag)
                                                        <span 
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" 
                                                            style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};"
                                                        >
                                                            {{ $tag->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <flux:button 
                                                type="button" 
                                                size="xs" 
                                                variant="ghost" 
                                                wire:click="openDrawer('{{ $task['id'] }}')"
                                                title="Add notes"
                                            >
                                                <flux:icon name="document-text" class="h-4 w-4" />
                                            </flux:button>
                                            
                                            <flux:button 
                                                type="button" 
                                                size="xs" 
                                                variant="ghost" 
                                                wire:click="startEditing('{{ $task['id'] }}')"
                                                title="Edit task"
                                            >
                                                <flux:icon name="pencil-square" class="h-4 w-4" />
                                            </flux:button>
                                            
                                            <flux:button 
                                                type="button" 
                                                size="xs" 
                                                variant="ghost" 
                                                wire:click="deleteTask('{{ $task['id'] }}')"
                                                title="Delete task"
                                                wire:confirm="Are you sure you want to delete this task?"
                                            >
                                                <flux:icon name="trash" class="h-4 w-4" />
                                            </flux:button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Task Notes Modal (Flyout) -->
    <div x-data="{ open: @entangle('isDrawerOpen') }">
        <flux:modal x-model="open" variant="flyout" position="right" class="w-full max-w-md">
            <div class="space-y-6 p-6">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Task Details</flux:heading>
                    <flux:button variant="ghost" size="sm" x-on:click="open = false">
                        <flux:icon name="x-mark" class="h-5 w-5" />
                    </flux:button>
                </div>
                
                <div class="space-y-6">
                    <!-- Notes Section -->
                    <div>
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Notes</h3>
                        <flux:textarea 
                            wire:model.live="taskNotes" 
                            placeholder="Add notes about this task..."
                            rows="6"
                            wire:change="saveNotes"
                        />
                    </div>
                    
                    <!-- Tags Section (only for authenticated users) -->
                    @if(Auth::check() && $activeTaskId)
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Tags</h3>
                            <livewire:task-tags :task-id="$activeTaskId" :key="'tags-'.$activeTaskId" />
                        </div>
                    @endif
                </div>
            </div>
        </flux:modal>
    </div>

    <!-- Alpine.js script for local storage handling -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('todoStorage', () => ({
                init() {
                    // Load tasks from local storage on init
                    const storedTasks = localStorage.getItem('todoflow_tasks');
                    if (storedTasks) {
                        this.$wire.loadTasksFromLocalStorage(JSON.parse(storedTasks));
                    }
                    
                    // Listen for save events
                    this.$wire.on('save-to-local-storage', ({ tasks }) => {
                        localStorage.setItem('todoflow_tasks', JSON.stringify(tasks));
                    });
                    
                    // Listen for clear events
                    this.$wire.on('clear-local-storage', () => {
                        localStorage.removeItem('todoflow_tasks');
                    });
                    
                    // Check if user just logged in and has local tasks to migrate
                    if (@js(Auth::check())) {
                        this.$nextTick(() => {
                            this.$wire.migrateLocalStorageToDatabase();
                        });
                    }
                }
            }));
        });
    </script>
    <div x-data="todoStorage"></div>
</div>
