<div class="w-full max-w-3xl mx-auto">
    <!-- Subtle gradient background for the container -->
    <div class="relative bg-gradient-to-b from-white to-zinc-50 dark:from-zinc-900 dark:to-black rounded-2xl shadow-xl border border-zinc-100/50 dark:border-zinc-800/50 overflow-hidden backdrop-blur-sm">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/20 dark:bg-blue-900/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-100/20 dark:bg-purple-900/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl pointer-events-none"></div>
        
        <div class="relative p-10 z-10">
            <!-- Header with subtle animation -->
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-3xl font-bold bg-gradient-to-r from-black to-zinc-700 dark:from-white dark:to-zinc-400 bg-clip-text text-transparent">Tasks</h2>
                <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400">
                    <span class="text-sm font-medium">{{ count(array_filter($tasks ?? [], fn($task) => $task['completed'] ?? false)) }}/{{ count($tasks ?? []) }}</span>
                    <div class="h-4 w-4 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center">
                        <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                    </div>
                </div>
            </div>
            
            <!-- Add Task Form with subtle animation -->
            <form wire:submit="addTask" class="mb-10 group">
                <div class="flex items-center p-0.5 bg-gradient-to-r from-blue-500/80 via-indigo-500/80 to-purple-500/80 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg">
                    <div class="flex-1 bg-white dark:bg-zinc-900 rounded-l-xl overflow-hidden">
                        <input 
                            wire:model="newTaskTitle" 
                            placeholder="What needs to be done?" 
                            class="flex-1 text-base w-full focus:outline-none border-0 focus:ring-0 h-12 px-4"
                            autofocus
                        />
                    </div>
                    <flux:button type="submit" variant="primary" class="m-0 rounded-l-none rounded-r-xl h-12 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 border-0 px-6 font-medium transition-all duration-300 shadow-inner">
                        Add Task
                    </flux:button>
                </div>
            </form>
            
            <!-- Task List with elegant animation -->
            <div class="space-y-4">
                @if(empty($tasks))
                    <div class="py-20 flex flex-col items-center justify-center text-center">
                        <div class="relative mb-6">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-full blur-xl"></div>
                            <flux:icon name="clipboard-document-check" class="h-16 w-16 text-zinc-400 dark:text-zinc-500 relative z-10" />
                        </div>
                        <h3 class="text-xl font-medium text-zinc-700 dark:text-zinc-300 mb-2">Your task list is empty</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-sm">Add your first task above to start organizing your day with style</p>
                    </div>
                @else
                    @foreach($tasks as $task)
                        <div class="group {{ $task['completed'] ? 'opacity-80' : '' }} hover:scale-[1.01] transition-all duration-300 ease-out">
                            <div class="relative overflow-hidden flex items-start gap-4 p-5 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800/80 hover:border-blue-200 dark:hover:border-blue-900/30 shadow-sm hover:shadow-md transition-all duration-300">
                                <!-- Subtle gradient background on hover -->
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-950/20 dark:to-purple-950/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                                
                                <!-- Checkbox with custom animation -->
                                <div class="pt-1 relative z-10">
                                    <div class="relative">
                                        <flux:checkbox 
                                            wire:click="toggleComplete('{{ $task['id'] }}')"
                                            :checked="$task['completed'] ?? false"
                                            class="h-5 w-5 text-blue-500 dark:text-blue-400 rounded-md transition-all duration-300 ease-in-out"
                                        />
                                        @if($task['completed'] ?? false)
                                            <span class="absolute top-0 left-0 h-5 w-5 flex items-center justify-center pointer-events-none">
                                                <span class="h-1.5 w-1.5 bg-white dark:bg-zinc-900 rounded-full animate-ping"></span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex-1 min-w-0 relative z-10">
                                    @if($editingTaskId === $task['id'])
                                        <form wire:submit="updateTask" class="flex items-center gap-3">
                                            <flux:input 
                                                wire:model="editingTaskTitle" 
                                                class="flex-1 border-blue-200 dark:border-blue-900/50 focus:border-blue-500 dark:focus:border-blue-400 focus:ring focus:ring-blue-500/20 dark:focus:ring-blue-400/20"
                                                autofocus
                                            />
                                            <div class="flex gap-2">
                                                <flux:button type="submit" variant="primary" size="sm" class="bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 border-0">Save</flux:button>
                                                <flux:button 
                                                    type="button" 
                                                    size="sm" 
                                                    variant="filled" 
                                                    wire:click="$set('editingTaskId', null)"
                                                >
                                                    Cancel
                                                </flux:button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-base font-medium text-black dark:text-white {{ $task['completed'] ? 'line-through text-zinc-500 dark:text-zinc-400' : '' }} transition-all duration-300">
                                                    {{ $task['title'] }}
                                                </h3>
                                                
                                                @if(!empty($task['notes']))
                                                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                                        {{ Str::limit($task['notes'], 120) }}
                                                    </p>
                                                @endif
                                                
                                                <!-- Tags display with elegant styling -->
                                                @if(Auth::check() && isset($task['id']) && is_numeric($task['id']))
                                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                                        @foreach(App\Models\Task::find($task['id'])->tags as $tag)
                                                            <span 
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-all duration-200 hover:scale-105" 
                                                                style="background-color: {{ $tag->color }}10; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}30;"
                                                            >
                                                                {{ $tag->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Action buttons with elegant hover effects -->
                                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-in-out translate-x-2 group-hover:translate-x-0">
                                                <flux:button 
                                                    type="button" 
                                                    size="xs" 
                                                    variant="ghost" 
                                                    wire:click="openDrawer('{{ $task['id'] }}')"
                                                    title="Add notes"
                                                    class="text-zinc-400 hover:text-blue-500 dark:text-zinc-500 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-full transition-all duration-200"
                                                >
                                                    <flux:icon name="document-text" class="h-4 w-4" />
                                                </flux:button>
                                                
                                                <flux:button 
                                                    type="button" 
                                                    size="xs" 
                                                    variant="ghost" 
                                                    wire:click="startEditing('{{ $task['id'] }}')"
                                                    title="Edit task"
                                                    class="text-zinc-400 hover:text-indigo-500 dark:text-zinc-500 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-full transition-all duration-200"
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
                                                    class="text-zinc-400 hover:text-red-500 dark:text-zinc-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full transition-all duration-200"
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

    <!-- Task Notes Modal (Flyout) with premium design -->
    <div x-data="{ open: @entangle('isDrawerOpen') }">
        <flux:modal x-model="open" variant="flyout" position="right" class="w-full max-w-md">
            <div class="relative overflow-hidden">
                <!-- Decorative gradient background -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/30 dark:bg-blue-900/20 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-100/30 dark:bg-purple-900/20 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl pointer-events-none"></div>
                
                <div class="relative space-y-8 p-8 z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:heading size="xl" class="text-black dark:text-white mb-1">Task Details</flux:heading>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Add notes and tags to organize your task</p>
                        </div>
                        <flux:button variant="ghost" size="sm" x-on:click="open = false" class="text-zinc-500 hover:text-black dark:hover:text-white transition-colors rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <flux:icon name="x-mark" class="h-5 w-5" />
                        </flux:button>
                    </div>
                    
                    <div class="space-y-8">
                        <!-- Notes Section with elegant styling -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <flux:icon name="document-text" class="h-4 w-4 text-blue-500 dark:text-blue-400" />
                                <h3 class="text-base font-semibold text-black dark:text-white">Notes</h3>
                            </div>
                            <div class="relative group">
                                <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl blur opacity-30 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                                <div class="relative">
                                    <flux:textarea 
                                        wire:model.live="taskNotes" 
                                        placeholder="Add notes about this task..."
                                        rows="6"
                                        wire:change="saveNotes"
                                        class="w-full resize-none border-zinc-200 dark:border-zinc-700 focus:border-blue-500 dark:focus:border-blue-400 focus:ring focus:ring-blue-500/20 dark:focus:ring-blue-400/20 rounded-lg transition-all duration-300 bg-white dark:bg-zinc-900"
                                    />
                                </div>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 italic">Changes are saved automatically as you type</p>
                        </div>
                        
                        <!-- Tags Section with premium styling -->
                        @if(Auth::check() && $activeTaskId)
                            <div class="space-y-3 pt-2">
                                <div class="flex items-center gap-2">
                                    <flux:icon name="tag" class="h-4 w-4 text-purple-500 dark:text-purple-400" />
                                    <h3 class="text-base font-semibold text-black dark:text-white">Tags</h3>
                                </div>
                                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/80 rounded-xl border border-zinc-200 dark:border-zinc-800">
                                    <livewire:task-tags :task-id="$activeTaskId" :key="'tags-'.$activeTaskId" />
                                </div>
                            </div>
                        @endif
                        
                        <!-- Quick Actions -->
                        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <div class="flex justify-end gap-3">
                                <flux:button 
                                    type="button" 
                                    variant="filled" 
                                    size="sm"
                                    x-on:click="open = false"
                                >
                                    Close
                                </flux:button>
                                <flux:button 
                                    type="button" 
                                    variant="primary" 
                                    size="sm"
                                    class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 border-0"
                                    x-on:click="open = false"
                                >
                                    Done
                                </flux:button>
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
