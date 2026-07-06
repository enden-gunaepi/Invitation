<!-- Wedding Events Section -->
<section id="events" class="py-stack-xl px-margin-page bg-surface-container-low/50 border-y border-outline-variant/30 my-stack-xl flex flex-col items-center">
    
    <div class="space-y-24 w-full max-w-sm">
        @foreach($invitation->events as $index => $event)
        <div class="flex flex-col items-center" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
            
            <!-- Event Title Block -->
            <div class="text-center mb-6">
                <span class="font-label-md text-[10px] text-primary uppercase tracking-[0.2em] block mb-2 font-bold">
                    @if(stripos($event->event_name, 'akad') !== false)
                        The Solemnization
                    @elseif(stripos($event->event_name, 'resepsi') !== false)
                        The Reception
                    @else
                        The Event
                    @endif
                </span>
                <h3 class="font-display-lg text-[40px] text-on-surface mb-4 leading-none">{{ $event->event_name }}</h3>
                <div class="w-12 h-[1px] bg-outline-variant mx-auto"></div>
            </div>
            
            <!-- Event Card -->
            <div class="w-full bg-surface-container-lowest rounded-[24px] overflow-hidden border border-outline-variant/50 shadow-[0px_8px_24px_rgba(47,47,47,0.04)]">
                
                <!-- Card Image -->
                @php
                    $photo = $invitation->photos->skip($index)->first() ?? $invitation->photos->first();
                @endphp
                <div class="w-full aspect-[4/3] bg-surface-variant overflow-hidden relative">
                    <img src="{{ $photo ? asset('storage/' . $photo->file_path) : 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&q=80' }}" 
                         alt="{{ $event->event_name }}" 
                         class="w-full h-full object-cover"/>
                    <!-- Subtle overlay to blend top edges -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent pointer-events-none"></div>
                </div>
                
                <!-- Card Body -->
                <div class="p-8">
                    
                    <!-- Date & Time Grid -->
                    <div class="grid grid-cols-2 relative pb-6 mb-6 border-b border-outline-variant/30">
                        <!-- Vertical Divider -->
                        <div class="absolute left-1/2 top-0 bottom-6 w-[1px] bg-outline-variant/30 -translate-x-1/2"></div>
                        
                        <!-- Date -->
                        <div class="flex flex-col pr-4">
                            <div class="flex items-center gap-1.5 text-secondary mb-2">
                                <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                                <span class="font-label-sm text-[10px] uppercase tracking-widest font-semibold">Date</span>
                            </div>
                            <span class="font-headline-md text-2xl text-on-surface leading-none mb-1">
                                {{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('d M') }}
                            </span>
                            <span class="font-headline-md text-xl text-secondary leading-none">
                                {{ \Carbon\Carbon::parse($event->event_date)->format('Y') }}
                            </span>
                        </div>
                        
                        <!-- Time -->
                        <div class="flex flex-col pl-6">
                            <div class="flex items-center gap-1.5 text-secondary mb-2">
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                <span class="font-label-sm text-[10px] uppercase tracking-widest font-semibold">Time</span>
                            </div>
                            <span class="font-headline-md text-2xl text-on-surface leading-none mb-1">
                                {{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }}
                            </span>
                            <span class="font-headline-md text-xl text-secondary leading-none">
                                WIB
                            </span>
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div class="flex items-start gap-3 mb-8">
                        <div class="mt-0.5 text-secondary">
                            <span class="material-symbols-outlined text-[18px]">location_on</span>
                        </div>
                        <div class="flex flex-col flex-1">
                            <span class="font-label-sm text-[10px] text-secondary uppercase tracking-widest font-semibold mb-1">Location</span>
                            <span class="font-body-md font-semibold text-on-surface leading-tight mb-1">
                                {{ $event->venue_name }}
                            </span>
                            <span class="font-body-md text-[14px] text-secondary leading-relaxed">
                                {{ $event->venue_address }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    @if($event->venue_maps_url)
                    <div class="mt-2">
                        <a href="{{ $event->venue_maps_url }}" target="_blank" 
                           class="inline-flex items-center gap-2 text-[#D4AF37] font-label-md text-[11px] uppercase tracking-[0.1em] font-bold hover:opacity-70 transition-opacity border-b border-[#D4AF37]/30 pb-1">
                            <span class="material-symbols-outlined text-[16px]">map</span>
                            Buka Google Maps
                        </a>
                    </div>
                    @endif
                    
                </div>
            </div>
            
        </div>
        @endforeach
    </div>
</section>
