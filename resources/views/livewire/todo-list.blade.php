<div class="w-full max-w-3xl mx-auto">
    <div class="bg-white dark:bg-black rounded-xl shadow-lg border border-zinc-100 dark:border-zinc-800 overflow-hidden">
        <div class="p-8">
            <h2 class="text-3xl font-bold text-black dark:text-white mb-8">Tasks</h2>
            
            <!-- Add Task Form -->
            <form wire:submit="addTask" class="mb-8">
                <div class="flex items-center gap-3">
                    <flux:input 
                        wire:model="newTaskTitle" 
                        placeholder="What needs to be done?" 
                        class="flex-1 text-base"
                        autofocus
                    />
                    <flux:button type="submit" variant="primary">Add</flux:button>
                </div>
            </form>
            
            <!-- Task List -->
            <div class="space-y-4">
                @if(empty($tasks))
                    <div class="py-16 flex flex-col items-center justify-center text-center">
                        <flux:icon name="clipboard-document-check" class="h-12 w-12 text-zinc-300 dark:text-zinc-700 mb-4" />
                        <p class="text-lg text-zinc-500 dark:text-zinc-400">Your task list is empty</p>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 mt-1">Add your first task to get started</p>
                    </div>
                @else
                    @foreach($tasks as $task)
                        <div class="group transition-all duration-200 {{ $task['completed'] ? 'opacity-70' : '' }}">
                            <div class="flex items-start gap-4 p-5 bg-zinc-50 dark:bg-zinc-900 hover:bg-white dark:hover:bg-black rounded-xl border border-zinc-100 dark:border-zinc-800 hover:border-zinc-200 dark:hover:border-zinc-700 hover:shadow-sm transition-all duration-200">
                                <div class="pt-1">
                                    <flux:checkbox 
                                        wire:click="toggleComplete('{{ $task['id'] }}')"
                                        :checked="$task['completed'] ?? false"
                                        class="h-5 w-5"
                                    />
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    @if($editingTaskId === $task['id'])
                                        <form wire:submit="updateTask" class="flex items-center gap-3">
                                            <flux:input 
                                                wire:model="editingTaskTitle" 
                                                class="flex-1"
                                                autofocus
                                            />
                                            <div class="flex gap-2">
                                                <flux:button type="submit" variant="primary" size="sm">Save</flux:button>
                                                <flux:button 
                                                    type="button" 
                                                    size="sm" 
                                                    variant="secondary" 
                                                    wire:click="$set('editingTaskId', null)"
                                                >
                                                    Cancel
                                                </flux:button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-base font-medium text-black dark:text-white {{ $task['completed'] ? 'line-through text-zinc-500 dark:text-zinc-400' : '' }}">
                                                    {{ $task['title'] }}
                                                </h3>
                                                
                                                @if(!empty($task['notes']))
                                                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                                        {{ Str::limit($task['notes'], 120) }}
                                                    </p>
                                                @endif
                                                
                                                <!-- Tags display -->
                                                @if(Auth::check() && isset($task['id']) && is_numeric($task['id']))
                                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                                        @foreach(App\Models\Task::find($task['id'])->tags as $tag)
                                                            <span 
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                                style="background-color: {{ $tag->color }}15; color: {{ $tag->color }};"
                                                            >
                                                                {{ $tag->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                                <flux:button 
                                                    type="button" 
                                                    size="xs" 
                                                    variant="ghost" 
                                                    wire:click="openDrawer('{{ $task['id'] }}')"
                                                    title="Add notes"
                                                    class="text-zinc-400 hover:text-zinc-900 dark:text-zinc-500 dark:hover:text-white"
                                                >
                                                    <flux:icon name="document-text" class="h-4 w-4" />
                                                </flux:button>
                                                
                                                <flux:button 
                                                    type="button" 
                                                    size="xs" 
                                                    variant="ghost" 
                                                    wire:click="startEditing('{{ $task['id'] }}')"
                                                    title="Edit task"
                                                    class="text-zinc-400 hover:text-zinc-900 dark:text-zinc-500 dark:hover:text-white"
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
                                                    class="text-zinc-400 hover:text-red-500 dark:text-zinc-500 dark:hover:text-red-400"
                                                >
                                                    <flux:icon name="trash" class="h-4 w-4" />
                                                </flux:button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
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
            <div class="space-y-8 p-8">
                <div class="flex items-center justify-between">
                    <flux:heading size="xl" class="text-black dark:text-white">Task Details</flux:heading>
                    <flux:button variant="ghost" size="sm" x-on:click="open = false" class="text-zinc-500 hover:text-black dark:hover:text-white transition-colors">
                        <flux:icon name="x-mark" class="h-5 w-5" />
                    </flux:button>
                </div>
                
                <div class="space-y-8">
                    <!-- Notes Section -->
                    <div class="space-y-3">
                        <h3 class="text-base font-semibold text-black dark:text-white">Notes</h3>
                        <flux:textarea 
                            wire:model.live="taskNotes" 
                            placeholder="Add notes about this task..."
                            rows="6"
                            wire:change="saveNotes"
                            class="w-full resize-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200"
                        />
                    </div>
                    
                    <!-- Tags Section (only for authenticated users) -->
                    @if(Auth::check() && $activeTaskId)
                        <div class="space-y-3 pt-2">
                            <h3 class="text-base font-semibold text-black dark:text-white">Tags</h3>
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
