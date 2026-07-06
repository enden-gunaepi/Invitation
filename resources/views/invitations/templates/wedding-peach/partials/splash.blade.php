<!-- Splash Screen Container -->
<main x-show="showSplash" 
      x-transition:leave="transition ease-in duration-500" 
      x-transition:leave-start="opacity-100" 
      x-transition:leave-end="opacity-0 -translate-y-full"
      class="fixed inset-0 z-[100] bg-background w-full h-[100svh] overflow-hidden flex flex-col items-center justify-between py-margin-page px-margin-page text-center" 
      id="splash-screen">
    
    <!-- Background Image -->
    <div class="absolute inset-0 z-0 fade-in-scale">
        <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&q=80' }}');"></div>
        <!-- Subtle Dark Gradient for Text Legibility -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/70"></div>
    </div>
    
    <!-- Top Branding -->
    <div class="relative z-10 stagger-up" style="animation-delay: 0.2s;">
        <p class="font-label-sm text-label-sm uppercase tracking-[0.2em] text-white/90 mb-2">The Wedding of</p>
        <h1 class="font-headline-lg-mobile text-headline-lg-mobile italic text-white drop-shadow-md">{{ $invitation->groom_name }} &amp; {{ $invitation->bride_name }}</h1>
    </div>
    
    <!-- Content & Action -->
    <div class="relative z-10 w-full max-w-sm flex flex-col items-center gap-stack-lg stagger-up" style="animation-delay: 0.5s;">
        <!-- Glassmorphism Card for Quote -->
        <div class="glass-overlay rounded-[24px] p-6 border border-white/20 w-full">
            <p class="font-label-md text-label-md text-white/90 tracking-widest uppercase mb-4">{{ $invitation->title ?? 'Pernikahan' }}</p>
            
            <p class="font-label-md text-label-md text-white/90 tracking-widest uppercase">
                @if($invitation->events->count() > 0)
                    {{ \Carbon\Carbon::parse($invitation->events->first()->event_date)->translatedFormat('d F Y') }}
                @else
                    -
                @endif
            </p>
            
            @if(request('to'))
                <div class="w-12 h-[1px] bg-white/40 mx-auto my-4"></div>
                <p class="font-label-sm text-label-sm text-white/80 mb-1">Kepada Yth.</p>
                <p class="font-headline-md text-headline-md text-white">{{ request('to') }}</p>
            @endif
        </div>
        
        <!-- Primary Luxury Action -->
        <button @click="openInvitation" class="group relative w-full bg-primary-container text-on-primary text-[18px] font-semibold py-5 rounded-[24px] btn-glow transition-all duration-300 active:scale-95 hover:opacity-90 flex items-center justify-center gap-3">
            <span class="font-label-md text-label-md tracking-widest uppercase">Buka Undangan</span>
            <span class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-1">mail</span>
        </button>
    </div>
    
    <!-- Scroll Indicator / Hint -->
    <div class="relative z-10 stagger-up" style="animation-delay: 0.8s;">
        <div class="flex flex-col items-center gap-2 text-white/70">
            <span class="material-symbols-outlined animate-bounce">expand_more</span>
        </div>
    </div>
</main>
