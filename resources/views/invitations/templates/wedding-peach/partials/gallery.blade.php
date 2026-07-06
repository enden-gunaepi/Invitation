<!-- Galeri Foto Section -->
<section id="gallery" class="mb-stack-xl mt-stack-xl px-margin-page">
    <div class="mb-stack-xl text-center" data-aos="fade-up">
        <p class="font-label-md text-label-md text-primary uppercase tracking-widest mb-stack-sm">The Memories</p>
        <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-on-surface mb-stack-md">Galeri Foto</h2>
        <div class="w-12 h-[1px] bg-primary/40 mx-auto"></div>
    </div>
    
    @if($invitation->photos->count() > 0)
    <div class="masonry-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
        @foreach($invitation->photos as $index => $photo)
            @php
                // Logic to alternate tall and standard aspect ratios for masonry look
                $aspect = ($index % 3 == 0) ? 'aspect-[4/6]' : 'aspect-[4/5]';
                // Add staggered translation for even items (CSS class defined in layout)
                $masonryClass = ($index % 2 == 1) ? 'translate-y-8' : '';
            @endphp
            <div class="masonry-item {{ $aspect }} {{ $masonryClass }} overflow-hidden rounded-[24px] shadow-[0px_8px_24px_rgba(47,47,47,0.08)] group" data-aos="fade-up" data-aos-delay="{{ ($index % 2) * 100 }}">
                <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                     src="{{ asset('storage/' . $photo->file_path) }}" 
                     alt="Gallery Photo {{ $index + 1 }}"/>
            </div>
        @endforeach
    </div>
    @else
    <p class="text-center font-body-md text-secondary italic">Belum ada foto yang ditambahkan.</p>
    @endif
    
    <!-- CTA Video/Live (if any) -->
    @if($invitation->live_streaming_url)
    <div class="flex justify-center mt-stack-xl pt-stack-md" data-aos="fade-up">
        <a class="group flex flex-col items-center gap-2" href="{{ $invitation->live_streaming_url }}" target="_blank">
            <span class="font-label-md text-label-md text-primary uppercase tracking-widest group-hover:opacity-70 transition-opacity">Tonton Live Streaming</span>
            <span class="material-symbols-outlined text-primary text-[20px] transition-transform duration-300 group-hover:translate-x-1">arrow_forward</span>
        </a>
    </div>
    @endif
</section>
