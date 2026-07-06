<!-- RSVP & Guestbook Section -->
<section id="rsvp" class="bg-surface-container-low/50 py-stack-xl px-margin-page border-y border-outline-variant/30 my-stack-xl" data-aos="fade-up">
    
    <!-- Header -->
    <div class="text-center mb-stack-xl">
        <span class="font-label-md text-[10px] text-primary uppercase tracking-[0.2em] mb-2 block font-bold">Confirmation & Wishes</span>
        <h2 class="font-display-lg text-[40px] text-on-surface mb-4 leading-none">Kehadiran & Doa</h2>
        <div class="w-12 h-[1px] bg-outline-variant mx-auto"></div>
    </div>

    <!-- Info Kehadiran -->
    @if($invitation->rsvps->count() > 0)
    <div class="mb-stack-lg text-center max-w-sm mx-auto">
        <div class="inline-flex items-center justify-center w-full gap-2 bg-surface-container-lowest px-4 py-3 rounded-full border border-outline-variant/50 shadow-sm">
            <span class="font-label-sm text-[11px] text-on-secondary-fixed-variant uppercase tracking-widest font-semibold">
                {{ $invitation->rsvps->where('status', 'attending')->count() }} tamu telah mengonfirmasi kehadiran
            </span>
        </div>
    </div>
    @endif

    <!-- Form Gabungan -->
    <div class="bg-surface-container-lowest rounded-[24px] p-8 border border-outline-variant/50 shadow-[0px_8px_24px_rgba(47,47,47,0.04)] max-w-sm mx-auto mb-12">
        <form class="space-y-6" id="rsvpForm" 
              x-data="{ 
                  status: 'attending', 
                  loading: false, 
                  success: false,
                  async submitCombined() {
                      if(this.loading) return;
                      this.loading = true;
                      
                      const formData = new FormData($refs.form);
                      
                      try {
                          // Submit RSVP
                          await fetch('{{ route('invitation.rsvp', $invitation->slug) }}', {
                              method: 'POST',
                              body: formData,
                              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                          });
                          
                          // Submit Wish (only if there is a message)
                          if (formData.get('message').trim() !== '') {
                              await fetch('{{ route('invitation.wish', $invitation->slug) }}', {
                                  method: 'POST',
                                  body: formData,
                                  headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                              });
                          }
                          
                          this.loading = false;
                          this.success = true;
                          $refs.form.reset();
                          this.status = 'attending';
                          
                          // Reload after a delay to show the new wish in the list
                          setTimeout(() => { window.location.reload(); }, 2000);
                          
                      } catch (err) {
                          this.loading = false;
                          alert('Terjadi kesalahan saat mengirim. Silakan coba lagi.');
                      }
                  }
              }" 
              x-ref="form"
              @submit.prevent="submitCombined">
              
            <!-- Nama Lengkap (Readonly) -->
            <div class="space-y-2">
                <label class="font-label-md text-[11px] text-secondary uppercase tracking-widest block font-bold">Nama Tamu</label>
                <input name="name" 
                       class="w-full bg-surface-container-low/50 rounded-xl px-4 py-3 border border-outline-variant/50 focus:outline-none focus:border-primary font-body-md text-on-surface opacity-80 cursor-not-allowed" 
                       type="text" 
                       value="{{ isset($guest) ? $guest->name : request('to', '') }}" 
                       readonly required/>
            </div>
            
            <!-- Hidden Fields required by Backend -->
            <input type="hidden" name="pax" value="1" />
            <input type="hidden" name="phone" value="" />
            
            <!-- Status Kehadiran -->
            <div class="space-y-2">
                <label class="font-label-md text-[11px] text-secondary uppercase tracking-widest block font-bold mb-3">Konfirmasi Hadir</label>
                <div class="grid grid-cols-2 gap-3">
                    <div class="relative">
                        <input class="sr-only" id="hadir" name="status" type="radio" value="attending" x-model="status"/>
                        <label class="flex items-center justify-center h-12 rounded-full border text-[13px] font-semibold cursor-pointer transition-all duration-300" 
                               :class="status === 'attending' ? 'bg-[#7B5E2A] text-white border-transparent' : 'border-outline-variant text-secondary bg-transparent'"
                               for="hadir">
                            Hadir
                        </label>
                    </div>
                    <div class="relative">
                        <input class="sr-only" id="tidak-hadir" name="status" type="radio" value="not_attending" x-model="status"/>
                        <label class="flex items-center justify-center h-12 rounded-full border text-[13px] font-semibold cursor-pointer transition-all duration-300" 
                               :class="status === 'not_attending' ? 'bg-[#7B5E2A] text-white border-transparent' : 'border-outline-variant text-secondary bg-transparent'"
                               for="tidak-hadir">
                            Tidak Hadir
                        </label>
                    </div>
                </div>
            </div>

            <!-- Ucapan & Doa -->
            <div class="space-y-2">
                <label class="font-label-md text-[11px] text-secondary uppercase tracking-widest block font-bold">Ucapan &amp; Doa</label>
                <textarea name="message" class="w-full bg-surface-container-low rounded-xl px-4 py-3 border border-outline-variant/50 focus:outline-none focus:border-primary font-body-md text-on-surface min-h-[100px] resize-none" placeholder="Berikan doa restu Anda..." required></textarea>
            </div>
            
            <!-- Submit Button -->
            <button class="w-full h-12 rounded-full text-white font-label-md text-[13px] font-semibold flex items-center justify-center gap-2 mt-8 transition-all duration-300 shadow-md" 
                    :class="success ? 'bg-green-600' : 'bg-[#7B5E2A] hover:bg-[#6A5023] active:scale-95'"
                    type="submit" 
                    :disabled="loading || success">
                
                <template x-if="loading">
                    <span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                </template>
                <template x-if="!loading && !success">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                </template>
                <template x-if="success">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </template>
                
                <span x-text="loading ? 'Mengirim...' : (success ? 'Terkirim' : 'Kirim')"></span>
            </button>
        </form>
    </div>

    <!-- List Ucapan (Guestbook) -->
    <div class="space-y-4 max-w-sm mx-auto max-h-[500px] overflow-y-auto pr-2" data-aos="fade-up" style="scrollbar-width: thin; scrollbar-color: var(--primary) transparent;">
        @forelse($invitation->wishes()->latest()->get() as $wish)
        <div class="p-5 bg-surface-container-lowest rounded-[24px] border border-outline-variant/50 shadow-[0px_4px_12px_rgba(47,47,47,0.02)]">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="font-headline-md text-primary text-sm uppercase">{{ substr($wish->name, 0, 1) }}</span>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-1">
                        <h4 class="font-label-md text-[13px] text-on-surface font-bold">{{ $wish->name }}</h4>
                        <span class="text-[10px] text-secondary">{{ $wish->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="font-body-md text-on-surface/80 text-[13px] leading-relaxed">{{ $wish->message }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center p-8 bg-surface-container-lowest border border-dashed border-outline-variant/50 rounded-[24px]">
            <span class="material-symbols-outlined text-outline-variant text-[32px] mb-2">favorite_border</span>
            <p class="font-body-md text-[13px] text-secondary">Jadilah yang pertama memberikan ucapan dan doa restu.</p>
        </div>
        @endforelse
    </div>

</section>
