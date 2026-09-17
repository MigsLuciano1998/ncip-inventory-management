{{--
    NCIP-CAR letter footer for printable reports.

    @include('partials.reports.footer')
--}}
<div class="report-footer par-footer mt-auto flex items-center gap-4 pt-8 pl-20" style="font-family: Arial, Helvetica, sans-serif;">
    <img src="{{ asset('images/bagong-pilipinas-logo.png') }}" alt="Bagong Pilipinas" class="mt-0.5 h-[72px] w-[72px] shrink-0 object-contain">
    <div class="min-w-0 flex-1 text-[11px] leading-snug text-[#4a4a4a]">
        <p class="text-[13px] font-semibold uppercase tracking-wide text-[#333333]">Cordillera Administrative Region</p>
        <p class="mt-0.5">Unit 311, Lyman Ogilby Centrum, 358 Magsaysay Avenue, Baguio City</p>
        <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1">
            <span class="inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5 text-[#333333]"><path fill-rule="evenodd" d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z" clip-rule="evenodd" /></svg>
                (074)422-4173 (ORD/Admin) | 424-3074 (TMSD)
            </span>
            <span class="text-[#333333]">|</span>
            <span class="inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5 text-[#333333]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3c2.5 3 3.75 6 3.75 9S14.5 18 12 21c-2.5-3-3.75-6-3.75-9S9.5 6 12 3Z" />
                </svg>
                <span class="font-semibold text-[#333333]">www.ncip.gov.ph</span>
            </span>
            <span class="inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5 text-[#333333]"><path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" /><path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" /></svg>
                car@ncip.gov.ph
            </span>
        </p>
        <p class="mt-1.5 text-[13px] font-bold italic leading-tight">
            <span class="text-[#1d3f91]">“Masaganang Katutubong Pamayanan:</span>
            <span class="text-[#c41230]"> Sandigan ng Pambansang Kaunlaran”</span>
        </p>
    </div>
</div>
