import './bootstrap.js';

/**
 * Global Livewire loading state handler
 * Adds visual feedback for page navigation and Livewire requests
 */
document.addEventListener('livewire:init', function () {
    // Track loading state for page navigation
    let isLoading = false;

    // Show loading bar on Livewire requests
    Livewire.hook('request', ({ succeed, fail }) => {
        isLoading = true;
        showLoadingBar();

        succeed(({ component }) => {
            isLoading = false;
            hideLoadingBar();
        });

        fail(({ component }) => {
            isLoading = false;
            hideLoadingBar();
        });
    });
});

/**
 * Show a loading bar at the top of the page
 */
function showLoadingBar() {
    let bar = document.getElementById('loading-bar');

    if (!bar) {
        bar = document.createElement('div');
        bar.id = 'loading-bar';
        bar.className = 'fixed top-0 left-0 h-1 bg-gradient-to-r from-blue-500 to-purple-500 z-50 animate-pulse';
        document.body.prepend(bar);
    }

    bar.style.width = '0%';
    bar.style.opacity = '1';
    bar.style.transition = 'width 0.3s ease';

    // Simulate progress
    let width = 10;
    const interval = setInterval(() => {
        width = Math.min(width + Math.random() * 30, 90);
        bar.style.width = width + '%';

        if (!document.getElementById('loading-bar')) {
            clearInterval(interval);
        }
    }, 200);
}

/**
 * Hide and reset the loading bar
 */
function hideLoadingBar() {
    const bar = document.getElementById('loading-bar');

    if (bar) {
        bar.style.width = '100%';
        bar.style.transition = 'opacity 0.5s ease';

        setTimeout(() => {
            bar.style.opacity = '0';
            setTimeout(() => {
                bar.remove();
            }, 500);
        }, 200);
    }
}

/**
 * Show loading indicator on page navigation (non-Livewire links)
 */
document.addEventListener('click', function (e) {
    // Check if clicked element or its parent is a navigation link (but not Livewire-controlled)
    const link = e.target.closest('a:not([wire\\:click]):not([wire\\:navigate])');

    if (link && link.href && !link.href.includes('#')) {
        showLoadingBar();
    }
});

