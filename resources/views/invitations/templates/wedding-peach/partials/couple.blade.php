<!-- Couple Profiles Section -->
<section id="couple" class="space-y-stack-xl py-stack-lg px-margin-page my-stack-lg">
    
    <!-- Profil Mempelai Pria -->
    <div class="flex flex-col items-center">
        <!-- Floating Name Tag -->
        <div class="relative z-10 -mb-6" data-aos="fade-up">
            <div class="glass-card px-6 py-3 rounded-full flex flex-col items-center shadow-lg">
                <span class="font-label-sm text-label-sm text-primary uppercase tracking-[0.2em] mb-1">The Groom</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">{{ $invitation->groom_name }}</h3>
            </div>
        </div>
        
        <!-- Portrait Image -->
        <div class="w-full max-w-sm aspect-[3/4] rounded-[24px] overflow-hidden shadow-2xl relative group" data-aos="fade-up" data-aos-delay="100">
            <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 grayscale hover:grayscale-0" src="{{ $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80' }}" alt="{{ $invitation->groom_name }}"/>
            <!-- Elegant Inner Border -->
            <div class="absolute inset-4 border border-white/20 rounded-[12px] pointer-events-none"></div>
        </div>
        
        <!-- Details & Parents -->
        <div class="mt-stack-md text-center max-w-xs" data-aos="fade-up" data-aos-delay="200">
            <p class="font-body-md text-body-md text-secondary leading-relaxed mb-4">
                Putra dari Bapak {{ $invitation->groom_father ?? 'Fulan' }}<br/>&amp; Ibu {{ $invitation->groom_mother ?? 'Fulanah' }}
            </p>
            @if($invitation->groom_ig)
            <a href="https://instagram.com/{{ ltrim($invitation->groom_ig, '@') }}" target="_blank" class="inline-flex items-center gap-2 font-label-sm text-label-sm text-primary uppercase tracking-widest hover:opacity-70 transition-opacity">
                <span>@</span> {{ ltrim($invitation->groom_ig, '@') }}
            </a>
            @endif
        </div>
    </div>

    <!-- The Ampersand Divider -->
    <div class="flex justify-center items-center gap-4 py-8 opacity-40" data-aos="fade-up">
        <div class="h-[1px] w-12 bg-primary"></div>
        <span class="font-display-lg text-display-lg text-primary italic">&amp;</span>
        <div class="h-[1px] w-12 bg-primary"></div>
    </div>

    <!-- Profil Mempelai Wanita -->
    <div class="flex flex-col items-center">
        <!-- Floating Name Tag -->
        <div class="relative z-10 -mb-6" data-aos="fade-up">
            <div class="glass-card px-6 py-3 rounded-full flex flex-col items-center shadow-lg">
                <span class="font-label-sm text-label-sm text-primary uppercase tracking-[0.2em] mb-1">The Bride</span>
                <h3 class="font-headline-md text-headline-md text-on-surface">{{ $invitation->bride_name }}</h3>
            </div>
        </div>
        
        <!-- Portrait Image -->
        <div class="w-full max-w-sm aspect-[3/4] rounded-[24px] overflow-hidden shadow-2xl relative group" data-aos="fade-up" data-aos-delay="100">
            <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 grayscale hover:grayscale-0" src="{{ $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : 'https://images.unsplash.com/photo-1532712938310-34cb3982ef74?auto=format&fit=crop&q=80' }}" alt="{{ $invitation->bride_name }}"/>
            <div class="absolute inset-4 border border-white/20 rounded-[12px] pointer-events-none"></div>
        </div>
        
        <!-- Details & Parents -->
        <div class="mt-stack-md text-center max-w-xs" data-aos="fade-up" data-aos-delay="200">
            <p class="font-body-md text-body-md text-secondary leading-relaxed mb-4">
                Putri dari Bapak {{ $invitation->bride_father ?? 'Fulan' }}<br/>&amp; Ibu {{ $invitation->bride_mother ?? 'Fulanah' }}
            </p>
            @if($invitation->bride_ig)
            <a href="https://instagram.com/{{ ltrim($invitation->bride_ig, '@') }}" target="_blank" class="inline-flex items-center gap-2 font-label-sm text-label-sm text-primary uppercase tracking-widest hover:opacity-70 transition-opacity">
                <span>@</span> {{ ltrim($invitation->bride_ig, '@') }}
            </a>
            @endif
        </div>
    </div>
</section>
