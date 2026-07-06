<!-- Wedding Gift Section -->
@php
    $bgPhoto = $invitation->photos->first();
    $bgUrl = $bgPhoto ? asset('storage/' . $bgPhoto->file_path) : 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&q=80';
@endphp
<section id="gifts" class="relative mb-stack-xl mt-stack-xl py-stack-xl text-center flex flex-col items-center overflow-hidden">
    
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ $bgUrl }}" alt="Gift Background" class="w-full h-full object-cover"/>
        <!-- Dark overlay to ensure readability -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"></div>
    </div>

    <div class="relative z-10 w-full px-margin-page">
        <!-- Header Section -->
        <div class="text-center mb-stack-xl" data-aos="fade-up">
            <span class="font-label-md text-[10px] text-[#D4AF37] uppercase tracking-[0.2em] mb-2 block font-bold">The Wedding Gift</span>
            <h2 class="font-display-lg text-[40px] text-white mb-4 leading-none drop-shadow-md">Kirim Kado</h2>
            <div class="w-12 h-[1px] bg-[#D4AF37] mx-auto"></div>
        </div>
        
        <!-- Cards Layout -->
        <div class="space-y-6 w-full max-w-sm mx-auto" x-data="{ copiedBank: null, copiedAddress: false }">
            
            <!-- Card: Alamat Mempelai -->
            @if($invitation->gift_address)
            <div class="p-4" data-aos="fade-up">
                <div class="flex flex-col items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-[#D4AF37]/20 flex items-center justify-center mb-4 border border-[#D4AF37]/30">
                        <span class="material-symbols-outlined text-[#D4AF37] text-[20px]">location_on</span>
                    </div>
                    <h3 class="font-headline-md text-2xl text-white mb-2">Alamat Mempelai</h3>
                    <p class="font-body-md text-[14px] text-white/80 leading-relaxed text-center">
                        {{ $invitation->gift_address }}
                    </p>
                </div>
                
                <button @click="navigator.clipboard.writeText('{{ addslashes($invitation->gift_address) }}'); copiedAddress = true; setTimeout(() => copiedAddress = false, 2000)"
                        class="w-full h-12 rounded-full font-label-md text-[13px] font-semibold flex items-center justify-center gap-2 transition-all active:scale-95"
                        :class="copiedAddress ? 'bg-[#D4AF37]/20 text-white border border-[#D4AF37]' : 'border border-[#D4AF37] text-white hover:bg-[#D4AF37] hover:text-black'">
                    
                    <span class="material-symbols-outlined text-[16px]" x-text="copiedAddress ? 'check_circle' : 'content_copy'">content_copy</span>
                    <span x-text="copiedAddress ? 'Tersalin!' : 'Salin Alamat'">Salin Alamat</span>
                </button>
            </div>
            @endif

            <!-- Card: Bank Transfer -->
            @foreach($invitation->bankAccounts as $account)
            <div class="p-4" data-aos="fade-up">
                <div class="flex flex-col items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-[#D4AF37]/20 flex items-center justify-center mb-4 border border-[#D4AF37]/30">
                        <span class="material-symbols-outlined text-[#D4AF37] text-[20px]">account_balance</span>
                    </div>
                    <span class="font-label-sm text-[10px] text-[#D4AF37] uppercase tracking-[0.2em] font-bold mb-2">{{ $account->bank_name }}</span>
                    <h3 class="font-headline-md text-2xl text-white mb-1 tracking-wider">{{ $account->account_number }}</h3>
                    <p class="font-body-md text-[14px] text-white/80">a/n {{ $account->account_name }}</p>
                </div>
                
                <button @click="navigator.clipboard.writeText('{{ $account->account_number }}'); copiedBank = '{{ $account->id }}'; setTimeout(() => copiedBank = null, 2000)"
                        class="w-full h-12 rounded-full font-label-md text-[13px] font-semibold flex items-center justify-center gap-2 transition-all active:scale-95"
                        :class="copiedBank === '{{ $account->id }}' ? 'bg-[#D4AF37]/20 text-white border border-[#D4AF37]' : 'border border-[#D4AF37] text-white hover:bg-[#D4AF37] hover:text-black'">
                    
                    <span class="material-symbols-outlined text-[16px]" x-text="copiedBank === '{{ $account->id }}' ? 'check_circle' : 'content_copy'">content_copy</span>
                    <span x-text="copiedBank === '{{ $account->id }}' ? 'Tersalin!' : 'Salin Rekening'">Salin Rekening</span>
                </button>
            </div>
            @endforeach
        </div>
    </div>
</section>
