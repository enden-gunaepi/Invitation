@php
    $giftAccounts = $invitation->bankAccounts;
    if ($giftAccounts->isEmpty() && ($invitation->bank_name || $invitation->bank_account_number || $invitation->bank_account_name)) {
        $giftAccounts = collect([
            (object) [
                'bank_name' => $invitation->bank_name,
                'account_number' => $invitation->bank_account_number,
                'account_name' => $invitation->bank_account_name,
            ],
        ]);
    }
@endphp
@if ($giftAccounts->isNotEmpty() || !empty($invitation->gift_address))
@if (!empty($isGnv2))
<section class="builder-section builder-gnv2-panel">
    <div class="builder-gnv2-content" style="width:min(100%, 920px);">
        <div class="builder-kicker">Wedding Gift</div>
        <h2 class="builder-heading" style="font-size: clamp(34px, 7vw, 58px); color:#fff;">Tanda kasih</h2>
        <div class="builder-grid builder-gnv2-grid-2" style="margin-top: 28px; text-align:left;">
            @foreach ($giftAccounts as $account)
                <article class="builder-card" style="padding: 22px;">
                    <div style="font-size:12px; letter-spacing:.14em; text-transform:uppercase; opacity:.82;">Transfer</div>
                    <h3 class="builder-heading" style="font-size: 28px; color:#fff; margin-top:10px;">{{ $account->bank_name }}</h3>
                    <p style="margin:8px 0 0; font-size:20px; font-weight:700;">{{ $account->account_number }}</p>
                    <p style="margin:6px 0 0; opacity:.9;">{{ $account->account_name }}</p>
                </article>
            @endforeach
            @if (!empty($invitation->gift_address))
                <article class="builder-card" style="padding: 22px;">
                    <div style="font-size:12px; letter-spacing:.14em; text-transform:uppercase; opacity:.82;">Kirim Hadiah</div>
                    <p style="margin:10px 0 0; opacity:.9;">{{ $invitation->gift_address }}</p>
                </article>
            @endif
        </div>
    </div>
</section>
@else
<section class="builder-section">
    <div class="builder-card" style="padding: 28px;">
        <div class="builder-kicker">Hadiah</div>
        <h2 class="builder-heading" style="font-size: clamp(28px, 5vw, 44px);">Doa terbaik adalah hadiah terindah</h2>
        <div class="builder-grid builder-grid-2" style="margin-top: 24px;">
            @foreach ($giftAccounts as $account)
                <article style="padding: 20px; border-radius: 24px; background: rgba(183, 110, 121, 0.06);">
                    <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.14em; color: var(--builder-accent); font-weight: 700;">Transfer</div>
                    <h3 class="builder-heading" style="font-size: 26px; margin-top: 12px;">{{ $account->bank_name }}</h3>
                    <p style="margin: 8px 0 0; font-weight: 700; font-size: 20px;">{{ $account->account_number }}</p>
                    <p style="margin: 6px 0 0;">{{ $account->account_name }}</p>
                </article>
            @endforeach
            @if (!empty($invitation->gift_address))
                <article style="padding: 20px; border-radius: 24px; background: rgba(122, 78, 87, 0.06);">
                    <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.14em; color: var(--builder-accent); font-weight: 700;">Kirim Hadiah</div>
                    <p style="margin: 12px 0 0;">{{ $invitation->gift_address }}</p>
                </article>
            @endif
        </div>
    </div>
</section>
@endif
@endif
