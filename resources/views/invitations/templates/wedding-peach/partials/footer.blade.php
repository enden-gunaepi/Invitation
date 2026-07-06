<!-- Penutup Section -->
@php
    $footerPhoto = $invitation->photos->count() > 2 ? $invitation->photos->get(2) : $invitation->photos->first();
    $bgUrl = $footerPhoto ? asset('storage/' . $footerPhoto->file_path) : 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&q=80';
@endphp
<section id="closing" class="relative flex flex-col items-center text-center py-stack-xl px-margin-page overflow-hidden min-h-[80vh] justify-center pb-32">
    
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="{{ $bgUrl }}" alt="Footer Background" class="w-full h-full object-cover"/>
        <!-- Soft white gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-surface-container-lowest/90 via-surface-container-lowest/80 to-surface-container-lowest/95 backdrop-blur-[2px]"></div>
    </div>
    
    <div class="relative z-10 w-full max-w-sm mx-auto flex flex-col items-center" data-aos="fade-up" data-aos-duration="1000">
        
        <!-- Heart Icon -->
        <div class="mb-8">
            <span class="material-symbols-outlined text-[#D4AF37] text-[32px] opacity-80" style="font-variation-settings: 'wght' 200;">favorite_border</span>
        </div>
        
        <!-- Typography -->
        <h2 class="font-display-lg text-[40px] text-[#D4AF37] mb-6 italic leading-none drop-shadow-sm">Terima Kasih</h2>
        
        <p class="font-body-md text-[14px] text-secondary leading-[1.8] max-w-[280px] mx-auto mb-12">
            Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu kepada kedua mempelai.
        </p>
        
        <!-- Couple Names -->
        <div class="mb-16">
            <h3 class="font-display-lg text-[40px] text-gray-900 mb-4 leading-none">{{ $invitation->groom_name }} &amp; {{ $invitation->bride_name }}</h3>
            <div class="w-16 h-[1px] bg-[#D4AF37] mx-auto opacity-70"></div>
        </div>
        
        <!-- Back to Top Button -->
        <div class="flex flex-col items-center gap-4 cursor-pointer group" @click="window.scrollTo({ top: 0, behavior: 'smooth' })">
            <div class="w-12 h-12 rounded-full border border-[#D4AF37]/40 flex items-center justify-center text-[#D4AF37] group-hover:bg-[#D4AF37]/10 transition-colors">
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'wght' 300;">keyboard_double_arrow_up</span>
            </div>
            <span class="font-label-md text-[10px] text-[#D4AF37] uppercase tracking-[0.2em] font-bold">Kembali ke Atas</span>
        </div>
        
    </div>
    
    <!-- Credit -->
    <div class="absolute bottom-8 left-0 right-0 z-10 w-full text-center">
        <p class="font-body-md text-[12px] text-secondary/60">Created with love for our special day</p>
    </div>
</section>
