{{-- Global loading indicator component with page blur and pointer-events disabled --}}
<div id="loading-indicator" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 dark:bg-black/60 backdrop-blur-sm">
    <div class="flex flex-col items-center gap-3">
        <div class="h-12 w-12 animate-spin rounded-full border-4 border-white border-t-blue-500"></div>
        <p class="text-white font-medium text-lg">Loading...</p>
    </div>
</div>

<script>
    // Manage global loading indicator visibility and page interactivity via Livewire
    document.addEventListener('livewire:init', function () {
        const indicator = document.getElementById('loading-indicator');
        const body = document.body;

        Livewire.hook('request', ({ succeed, fail }) => {
            indicator.classList.remove('hidden');
            body.style.pointerEvents = 'none';
            body.style.overflow = 'hidden';

            succeed(() => {
                indicator.classList.add('hidden');
                body.style.pointerEvents = 'auto';
                body.style.overflow = 'auto';
            });

            fail(() => {
                indicator.classList.add('hidden');
                body.style.pointerEvents = 'auto';
                body.style.overflow = 'auto';
            });
        });
    });
</script>
