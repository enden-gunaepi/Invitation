<!-- Hero Section -->
<section id="hero" class="relative min-h-[100svh] flex flex-col items-center justify-center px-margin-page pt-16 pb-8 overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&q=80' }}');"></div>
        <!-- Dark Gradient for Text Legibility -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
    </div>
    
    <!-- Content Area -->
    <div class="relative z-10 text-center w-full max-w-md mt-auto" data-aos="fade-up">
        <span class="font-label-md text-label-md text-white/90 tracking-[0.2em] uppercase block mb-2 drop-shadow-md">The Wedding Of</span>
        <h2 class="font-display-lg text-display-lg text-white mb-4 drop-shadow-lg">{{ $invitation->groom_name }} &amp; {{ $invitation->bride_name }}</h2>
        <div class="flex items-center justify-center gap-4 text-white/90 mb-stack-lg drop-shadow-md">
            <div class="h-[1px] w-8 bg-white/50"></div>
            <p class="font-body-md text-body-md tracking-wider">
                @if($invitation->events->count() > 0)
                    {{ \Carbon\Carbon::parse($invitation->events->first()->event_date)->format('d.m.y') }} | {{ explode(' ', $invitation->venue_name ?? 'Jakarta')[0] }}
                @else
                    TBA
                @endif
            </p>
            <div class="h-[1px] w-8 bg-white/50"></div>
        </div>
        
        <!-- Countdown Timer -->
        @if($invitation->events->count() > 0)
        <div class="grid grid-cols-4 gap-3 mb-stack-xl" 
             x-data="{ 
                 days: '00', hours: '00', minutes: '00', seconds: '00', 
                 targetDate: new Date('{{ \Carbon\Carbon::parse($invitation->events->first()->event_date)->toIso8601String() }}').getTime(),
                 init() { 
                     setInterval(() => { 
                         const distance = this.targetDate - new Date().getTime(); 
                         if(distance < 0) return; 
                         this.days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0'); 
                         this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0'); 
                         this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0'); 
                         this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0'); 
                     }, 1000); 
                 } 
             }">
            <div class="flex flex-col items-center p-3 glass-card rounded-2xl border-white/20">
                <span class="font-display-lg text-headline-lg text-white" x-text="days">00</span>
                <span class="font-label-sm text-label-sm text-white/80 uppercase tracking-tighter">Hari</span>
            </div>
            <div class="flex flex-col items-center p-3 glass-card rounded-2xl border-white/20">
                <span class="font-display-lg text-headline-lg text-white" x-text="hours">00</span>
                <span class="font-label-sm text-label-sm text-white/80 uppercase tracking-tighter">Jam</span>
            </div>
            <div class="flex flex-col items-center p-3 glass-card rounded-2xl border-white/20">
                <span class="font-display-lg text-headline-lg text-white" x-text="minutes">00</span>
                <span class="font-label-sm text-label-sm text-white/80 uppercase tracking-tighter">Menit</span>
            </div>
            <div class="flex flex-col items-center p-3 glass-card rounded-2xl border-white/20">
                <span class="font-display-lg text-headline-lg text-white" x-text="seconds">00</span>
                <span class="font-label-sm text-label-sm text-white/80 uppercase tracking-tighter">Detik</span>
            </div>
        </div>
        @endif
        
        <!-- Action Buttons -->
        <div class="flex flex-col gap-4 w-full mb-8">
            <a href="#rsvp" class="h-14 bg-primary text-on-primary font-label-md text-label-md rounded-full shadow-lg shadow-black/30 flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">calendar_month</span>
                RSVP Sekarang
            </a>
            @if($invitation->events->count() > 0 && $invitation->events->first()->maps_url)
            <a href="{{ $invitation->events->first()->maps_url }}" target="_blank" class="h-14 glass-overlay border border-white/30 text-white font-label-md text-label-md rounded-full flex items-center justify-center gap-2 hover:bg-white/20 active:scale-95 transition-all backdrop-blur-md">
                <span class="material-symbols-outlined text-lg">location_on</span>
                Lihat Lokasi
            </a>
            @endif
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="relative z-10 mt-auto flex flex-col items-center opacity-80 text-white pb-8">
        <span class="text-[10px] font-label-sm uppercase tracking-widest mb-2">Scroll</span>
        <div class="w-[1px] h-12 bg-gradient-to-b from-white to-transparent"></div>
    </div>
</section>

<!-- Aesthetic Divider -->
<div class="w-full h-32 flex items-center overflow-hidden pointer-events-none opacity-20 bg-surface">
    <div class="flex whitespace-nowrap scrolling-text">
        <span class="font-display-lg text-display-lg text-primary mx-8">{{ strtoupper($invitation->groom_name) }} &amp; {{ strtoupper($invitation->bride_name) }}</span>
        <span class="font-display-lg text-display-lg text-primary mx-8">{{ strtoupper($invitation->groom_name) }} &amp; {{ strtoupper($invitation->bride_name) }}</span>
        <span class="font-display-lg text-display-lg text-primary mx-8">{{ strtoupper($invitation->groom_name) }} &amp; {{ strtoupper($invitation->bride_name) }}</span>
    </div>
</div>
