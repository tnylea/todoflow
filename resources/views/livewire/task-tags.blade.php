<div>
    <!-- Add Tag Form -->
    <form wire:submit="createTag" class="mb-4">
        <div class="flex items-end gap-2">
            <div class="flex-1">
                <flux:input 
                    wire:model="newTagName" 
                    placeholder="New tag name..." 
                    class="w-full"
                />
            </div>
            <div class="flex-shrink-0">
                <flux:input 
                    type="color" 
                    wire:model="newTagColor" 
                    class="w-10 h-10 p-0 border-0 rounded cursor-pointer"
                />
            </div>
            <flux:button type="submit" size="sm">Add</flux:button>
        </div>
    </form>

    <!-- Available Tags -->
    <div class="space-y-2">
        <h4 class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Available Tags</h4>
        
        @if(empty($availableTags))
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No tags created yet.</p>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach($availableTags as $tag)
                    <button 
                        type="button"
                        wire:click="toggleTag({{ $tag['id'] }})"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium transition-all"
                        style="
                            background-color: {{ $this->isTagAttached($tag['id']) ? $tag['color'] : $tag['color'] . '20' }}; 
                            color: {{ $this->isTagAttached($tag['id']) ? 'white' : $tag['color'] }};
                        "
                    >
                        {{ $tag['name'] }}
                        @if($this->isTagAttached($tag['id']))
                            <flux:icon name="check" class="ml-1 h-3 w-3" />
                        @endif
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</div>
