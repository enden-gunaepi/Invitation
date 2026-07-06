<!-- Quote Section -->
<section class="flex flex-col items-center justify-center text-center py-stack-xl px-margin-page relative overflow-hidden my-stack-xl">
    <!-- Decorative Background Element -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-primary/5 via-surface to-surface z-0"></div>
    
    <div class="relative z-10 max-w-sm" data-aos="fade-up" data-aos-duration="1000">
        <!-- Minimalist Icon -->
        <div class="mb-stack-md flex justify-center">
            <span class="material-symbols-outlined text-primary/40 text-4xl" style="font-variation-settings: 'wght' 200;">favorite</span>
        </div>
        
        <!-- Typography Focus -->
        @if($invitation->opening_text)
            <p class="font-headline-md text-headline-md italic text-on-surface leading-snug mb-stack-lg text-balance">
                "{{ $invitation->opening_text }}"
            </p>
        @else
            <p class="font-headline-md text-headline-md italic text-on-surface leading-snug mb-stack-lg text-balance">
                "Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang."
            </p>
            <p class="font-label-sm text-label-sm text-secondary uppercase tracking-[0.2em] relative inline-block">
                <span class="absolute top-1/2 -left-8 w-6 h-[1px] bg-primary/30"></span>
                Ar-Rum: 21
                <span class="absolute top-1/2 -right-8 w-6 h-[1px] bg-primary/30"></span>
            </p>
        @endif
    </div>
</section>
