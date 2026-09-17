            <table class="mt-1 w-full border-collapse text-[11px] leading-tight">
                <tr>
                    <td class="py-0 align-baseline leading-tight" colspan="2">
                        <span class="font-semibold">Entity Name:</span>
                        National Commission on Indigenous Peoples
                    </td>
                    <td class="w-[220px]"></td>
                </tr>
                <tr>
                    <td class="py-0 align-baseline leading-tight" colspan="2">
                        <span class="font-semibold">From Accountable Officer/Agency Fund Cluster:</span>
                        {{ $fromName !== '' ? $fromName : '—' }}
                    </td>
                    <td class="py-0 align-baseline leading-tight">
                        <span class="font-semibold">PTR No. :</span>
                        {{ $ptrNumber }}
                    </td>
                </tr>
                <tr>
                    <td class="py-0 align-baseline leading-tight" colspan="2">
                        <span class="font-semibold">To Accountable Officer/Agency Fund Cluster :</span>
                        {{ $toName !== '' ? $toName : '—' }}
                    </td>
                    <td class="py-0 align-baseline leading-tight">
                        <span class="font-semibold">Date :</span>
                        {{ $printLog->created_at->format('n/j/Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="pt-1 pb-0.5 align-top leading-tight" colspan="3">
                        <p class="font-semibold leading-tight">Transfer Type: <span class="font-normal italic">(check only one)</span></p>
                        <div class="mt-0.5 grid max-w-xl grid-cols-2 gap-x-10 gap-y-0">
                            @php
                                $box = fn ($checked) => $checked
                                    ? '<span class="inline-flex h-3 w-3 items-center justify-center border border-black text-[8px] leading-none">✓</span>'
                                    : '<span class="inline-block h-3 w-3 border border-black"></span>';
                            @endphp
                            <p class="flex items-center gap-1.5 leading-tight">{!! $box($transferType === 'donation') !!} Donation</p>
                            <p class="flex items-center gap-1.5 leading-tight">{!! $box($transferType === 'relocate') !!} Relocate</p>
                            <p class="flex items-center gap-1.5 leading-tight">{!! $box($transferType === 'reassignment') !!} Reassignment</p>
                            <p class="flex items-center gap-1.5 leading-tight">
                                {!! $box($transferType === 'others') !!}
                                Others(Specify)
                                <span class="min-w-[8rem] flex-1 border-b border-black leading-tight">
                                    @if($transferType === 'others')
                                        {{ $transferTypeOther ?: $printLog->actionLabel() }}
                                    @endif
                                </span>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="mt-1 w-full border-collapse border border-black text-[11px] leading-tight">
                <tr class="text-center font-semibold">
                    <td class="w-[90px] border border-black px-1 py-1 leading-tight">Date<br>Acquired</td>
                    <td class="w-[80px] border border-black px-1 py-1 leading-tight">Item<br>No.</td>
                    <td class="w-[140px] border border-black px-1 py-1 leading-tight">ICS No./Date</td>
                    <td class="border border-black px-1 py-1 leading-tight">Description</td>
                    <td class="w-[120px] border border-black px-1 py-1 leading-tight">Serial Number</td>
                    <td class="w-[90px] border border-black px-1 py-1 leading-tight">Amount</td>
                    <td class="w-[90px] border border-black px-1 py-1 leading-tight">Condition of<br>Inventory</td>
                </tr>
                <tr class="align-top">
                    <td class="border border-black px-1 py-1.5 text-center leading-tight">{{ $equipment->date_acquired?->format('n/j/Y') ?? $equipment->date_purchased?->format('n/j/Y') ?? '—' }}</td>
                    <td class="border border-black px-1 py-1.5 text-center leading-tight">{{ $equipment->item_id ?: $equipment->tag ?: '—' }}</td>
                    <td class="border border-black px-1 py-1.5 text-center leading-tight">{{ $icsNoDate }}</td>
                    <td class="border border-black px-2 py-1.5 text-left leading-tight">{{ $itemDescription }}</td>
                    <td class="border border-black px-1 py-1.5 text-center leading-tight">{{ $equipment->serial_no ?: '—' }}</td>
                    <td class="border border-black px-1 py-1.5 text-right leading-tight">{{ $equipment->cost ? number_format((float) preg_replace('/[^\d.]/', '', $equipment->cost), 2) : '—' }}</td>
                    <td class="border border-black px-1 py-1.5 text-center leading-tight">{{ $condition }}</td>
                </tr>
            </table>

            <div class="mt-1.5 text-[11px] leading-tight">
                <p class="font-semibold leading-tight">Reason/s for Transfer:</p>
                <p class="mt-0.5 min-h-[1.1rem] border-b border-black leading-tight">{{ $reason }}</p>
                <p class="mt-1 min-h-[1.1rem] border-b border-black"></p>
                <p class="mt-1 min-h-[1.1rem] border-b border-black"></p>
            </div>

            <table class="mt-4 w-full border-collapse text-[11px] leading-tight">
                <tr class="text-center font-semibold">
                    <td class="w-[90px] py-0 text-left"></td>
                    <td class="py-0">Approved by:</td>
                    <td class="py-0">Released/Issued by:</td>
                    <td class="py-0">Received by:</td>
                </tr>
                <tr class="text-center align-bottom">
                    <td class="py-1 text-left font-semibold">Signature:</td>
                    <td class="px-3 py-1"><div class="mx-4 border-b border-black">&nbsp;</div></td>
                    <td class="px-3 py-1"><div class="mx-4 border-b border-black">&nbsp;</div></td>
                    <td class="px-3 py-1"><div class="mx-4 border-b border-black">&nbsp;</div></td>
                </tr>
                <tr class="text-center align-bottom">
                    <td class="py-1 text-left font-semibold">Printed Name:</td>
                    <td class="px-3 py-1">
                        <div class="mx-4 border-b border-black font-semibold leading-tight">{{ $approvedBy?->last_name_first ? 'ATTY. '.strtoupper($approvedBy->first_name.' '.($approvedBy->middle_name ?? '').' '.$approvedBy->last_name) : 'ATTY. ATANACIO D. ADDOG' }}</div>
                    </td>
                    <td class="px-3 py-1">
                        <div class="mx-4 border-b border-black font-semibold leading-tight">{{ $issuedBy?->last_name_first ?? 'La Madrid, Lloyd Neil C.' }}</div>
                    </td>
                    <td class="px-3 py-1">
                        <div class="mx-4 border-b border-black font-semibold leading-tight">{{ $toName !== '' ? $toName : '—' }}</div>
                    </td>
                </tr>
                <tr class="text-center align-bottom">
                    <td class="py-1 text-left font-semibold">Designation:</td>
                    <td class="px-3 py-1">
                        <div class="mx-4 border-b border-black leading-tight">Regional Director</div>
                    </td>
                    <td class="px-3 py-1">
                        <div class="mx-4 border-b border-black leading-tight">Supply Officer</div>
                    </td>
                    <td class="px-3 py-1">
                        <div class="mx-4 border-b border-black leading-tight">{{ $toEmployee?->position ?: ($printLog->office?->office_name ?: '') }}</div>
                    </td>
                </tr>
                <tr class="text-center align-bottom">
                    <td class="py-1 text-left font-semibold">Date:</td>
                    <td class="px-3 py-1"><div class="mx-4 border-b border-black">&nbsp;</div></td>
                    <td class="px-3 py-1"><div class="mx-4 border-b border-black">&nbsp;</div></td>
                    <td class="px-3 py-1">
                        <div class="mx-4 border-b border-black leading-tight">{{ $printLog->created_at->format('n/j/Y') }}</div>
                    </td>
                </tr>
            </table>
