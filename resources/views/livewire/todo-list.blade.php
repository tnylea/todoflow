<div class="w-full max-w-3xl mx-auto">
    <!-- Clean, minimal container -->
    <div class="bg-white dark:bg-black rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        <div class="p-6 sm:p-8">
            <!-- Minimal header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl sm:text-2xl font-semibold text-black dark:text-white">Tasks</h2>
                <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400">
                    <span class="text-sm font-medium">{{ count(array_filter($tasks ?? [], fn($task) => $task['completed'] ?? false)) }}/{{ count($tasks ?? []) }}</span>
                </div>
            </div>
            
            <!-- Clean, minimal task form -->
            <form wire:submit="addTask" class="mb-6">
                <div class="flex items-center border border-zinc-900 p-0.5 dark:border-zinc-800 rounded-[0.625rem] overflow-hidden ring-1 focus-within:ring-1 focus-within:ring-zinc-900 dark:focus-within:ring-zinc-600 transition-all duration-200">
                    <div class="flex-1 overflow-hidden">
                        <input 
                            wire:model="newTaskTitle" 
                            placeholder="What needs to be done?" 
                            class="w-full h-10 px-3 text-sm text-black dark:text-white bg-white dark:bg-black border-0 focus:outline-none focus:ring-0"
                            autofocus
                        />
                    </div>
                    <flux:button type="submit"  variant="primary">
                        Add Task
                    </flux:button>
                </div>
            </form>
            
            <!-- Clean, minimal task list -->
            <div class="space-y-3">
                @if(empty($tasks))
                    <div class="py-16 flex flex-col items-center justify-center text-center">
                        <flux:icon name="clipboard-document-check" class="h-10 w-10 text-zinc-300 dark:text-zinc-700 mb-3" />
                        <h3 class="text-base font-medium text-zinc-700 dark:text-zinc-300 mb-1">Your task list is empty</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-500">Add your first task to get started</p>
                    </div>
                @else
                    @foreach($tasks as $task)
                        <div class="group {{ $task['completed'] ? 'opacity-75' : '' }}">
                            <div class="flex items-start gap-3 p-3 bg-white dark:bg-black rounded-lg border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all duration-200">
                                <!-- Simple checkbox -->
                                <div class="pt-0.5 relative z-10">
                                    <flux:checkbox 
                                        wire:click="toggleComplete('{{ $task['id'] }}')"
                                        x-on:checked="$task['completed'] ?? false"
                                        class="h-4 w-4 text-black dark:text-white rounded-sm transition-all duration-200"
                                    />
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    @if($editingTaskId === $task['id'])
                                        <form wire:submit="updateTask" class="flex items-center gap-2">
                                            <input 
                                                wire:model="editingTaskTitle" 
                                                class="flex-1 h-8 px-2 text-xs border border-zinc-300 dark:border-zinc-700 rounded focus:outline-none focus:ring-1 focus:ring-zinc-500 dark:focus:ring-zinc-500 bg-white dark:bg-black text-black dark:text-white"
                                                autofocus
                                            />
                                            <div class="flex gap-2">
                                                <button type="submit" class="h-8 px-2 bg-black dark:bg-white text-white dark:text-black text-xs font-medium rounded transition-colors hover:bg-zinc-800 dark:hover:bg-zinc-200">
                                                    Save
                                                </button>
                                                <button 
                                                    type="button" 
                                                    class="h-8 px-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-medium rounded transition-colors hover:bg-zinc-200 dark:hover:bg-zinc-700"
                                                    wire:click="$set('editingTaskId', null)"
                                                >
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-sm font-medium text-black dark:text-white {{ $task['completed'] ? 'line-through text-zinc-400 dark:text-zinc-500' : '' }} transition-all duration-200">
                                                    {{ $task['title'] }}
                                                </h3>
                                                
                                                @if(!empty($task['notes']))
                                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                                        {{ Str::limit($task['notes'], 120) }}
                                                    </p>
                                                @endif
                                                
                                                <!-- Tags with minimal styling -->
                                                @if(Auth::check() && isset($task['id']) && is_numeric($task['id']))
                                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                                        @foreach(App\Models\Task::find($task['id'])->tags as $tag)
                                                            <span 
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded-sm text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300"
                                                            >
                                                                {{ $tag->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Clean, minimal action buttons -->
                                            <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                                <button 
                                                    type="button" 
                                                    wire:click="openDrawer('{{ $task['id'] }}')"
                                                    title="Add notes"
                                                    class="p-1 text-zinc-400 hover:text-black dark:text-zinc-500 dark:hover:text-white transition-colors rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                                >
                                                    <flux:icon name="document-text" class="h-3.5 w-3.5" />
                                                </button>
                                                
                                                <button 
                                                    type="button" 
                                                    wire:click="startEditing('{{ $task['id'] }}')"
                                                    title="Edit task"
                                                    class="p-1 text-zinc-400 hover:text-black dark:text-zinc-500 dark:hover:text-white transition-colors rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                                >
                                                    <flux:icon name="pencil-square" class="h-3.5 w-3.5" />
                                                </button>
                                                
                                                <button 
                                                    type="button" 
                                                    wire:click="deleteTask('{{ $task['id'] }}')"
                                                    title="Delete task"
                                                    wire:confirm="Are you sure you want to delete this task?"
                                                    class="p-1 text-zinc-400 hover:text-red-500 dark:text-zinc-500 dark:hover:text-red-400 transition-colors rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                                >
                                                    <flux:icon name="trash" class="h-3.5 w-3.5" />
                                                </button>
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

    <!-- Clean, minimal task notes modal -->
    <div x-data="{ open: @entangle('isDrawerOpen') }">
        <flux:modal x-model="open" variant="flyout" position="right" class="w-full max-w-md">
            <div class="h-full bg-white dark:bg-black">
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-black dark:text-white">Task Details</h2>
                        <button x-on:click="open = false" class="p-1 text-zinc-400 hover:text-black dark:hover:text-white transition-colors rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <flux:icon name="x-mark" class="h-5 w-5" />
                        </button>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Notes Section with minimal styling -->
                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Notes</h3>
                            <textarea 
                                wire:model.live="taskNotes" 
                                placeholder="Add notes about this task..."
                                rows="5"
                                wire:change="saveNotes"
                                class="w-full resize-none border border-zinc-200 dark:border-zinc-800 rounded p-2 text-sm text-black dark:text-white bg-white dark:bg-black focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:focus:ring-zinc-600 transition-all duration-200"
                            ></textarea>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500">Changes are saved automatically</p>
                        </div>
                        
                        <!-- Tags Section with minimal styling -->
                        @if(Auth::check() && $activeTaskId)
                            <div class="space-y-2 pt-2">
                                <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Tags</h3>
                                <div class="p-3 border border-zinc-200 dark:border-zinc-800 rounded">
                                    <livewire:task-tags :task-id="$activeTaskId" :key="'tags-'.$activeTaskId" />
                                </div>
                            </div>
                        @endif
                        
                        <!-- Clean, minimal actions -->
                        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <div class="flex justify-end gap-2">
                                <button 
                                    type="button" 
                                    class="px-3 py-1.5 text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded transition-colors hover:bg-zinc-200 dark:hover:bg-zinc-700"
                                    x-on:click="open = false"
                                >
                                    Close
                                </button>
                                <button 
                                    type="button" 
                                    class="px-3 py-1.5 text-xs font-medium text-white dark:text-black bg-black dark:bg-white rounded transition-colors hover:bg-zinc-800 dark:hover:bg-zinc-200"
                                    x-on:click="open = false"
                                >
                                    Done
                                </button>
                            </div>
                        </div>
                    </div>
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
