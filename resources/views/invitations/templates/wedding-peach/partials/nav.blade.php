<!-- Bottom Navigation Bar -->
<nav class="fixed bottom-8 left-0 w-full z-50 flex justify-center px-4 transition-transform duration-500" 
     x-show="!showSplash" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-8"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-cloak>
    <div class="bg-secondary/70 dark:bg-secondary-container/70 backdrop-blur-xl border border-primary/20 shadow-[0px_8px_24px_rgba(47,47,47,0.08)] flex justify-between items-center w-[90%] max-w-md rounded-full px-6 py-3">
        <a class="relative flex flex-col items-center justify-center text-primary dark:text-primary-fixed-dim after:content-[''] after:w-1 after:h-1 after:bg-primary after:rounded-full after:absolute after:-bottom-1 hover:text-primary transition-colors active:scale-90 duration-300" href="#hero">
            <span class="material-symbols-outlined text-2xl">home</span>
        </a>
        <a class="flex flex-col items-center justify-center text-secondary dark:text-secondary-fixed-dim hover:text-primary transition-colors active:scale-90 duration-300" href="#couple">
            <span class="material-symbols-outlined text-2xl">favorite</span>
        </a>
        <a class="flex flex-col items-center justify-center text-secondary dark:text-secondary-fixed-dim hover:text-primary transition-colors active:scale-90 duration-300" href="#gallery">
            <span class="material-symbols-outlined text-2xl">photo_library</span>
        </a>
        <a class="flex flex-col items-center justify-center text-secondary dark:text-secondary-fixed-dim hover:text-primary transition-colors active:scale-90 duration-300" href="#events">
            <span class="material-symbols-outlined text-2xl">event_available</span>
        </a>
        <a class="flex flex-col items-center justify-center text-secondary dark:text-secondary-fixed-dim hover:text-primary transition-colors active:scale-90 duration-300" href="#gifts">
            <span class="material-symbols-outlined text-2xl">featured_seasonal_and_gifts</span>
        </a>
    </div>
</nav>
