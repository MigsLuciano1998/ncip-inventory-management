            <table class="mt-1 w-full border-collapse border border-black text-[11px] leading-tight pt-8">
                <tr>
                    <td class="border border-black px-2 py-0.5" colspan="4">
                        <span class="font-semibold">Entity Name :</span>
                        National Commission on Indigenous Peoples
                    </td>
                    <td class="w-[180px] border border-black px-2 py-0.5 align-top">
                        <p class="leading-tight"><span class="font-semibold">Date :</span> {{ $returnDate?->format('n/j/Y') }}</p>
                        <p class="leading-tight"><span class="font-semibold">RRSP No. :</span> {{ $rrspNumber }}</p>
                    </td>
                </tr>
                <tr>
                    <td class="border border-black px-2 py-0.5 text-center italic" colspan="5">
                        This is to acknowledge receipt of the returned Semi-expendable Property
                    </td>
                </tr>
                <tr class="text-center font-semibold">
                    <td class="border border-black px-1 py-1">Item Description</td>
                    <td class="w-[70px] border border-black px-1 py-1">Quantity</td>
                    <td class="w-[130px] border border-black px-1 py-1">ICF No.</td>
                    <td class="w-[140px] border border-black px-1 py-1">End-user</td>
                    <td class="w-[160px] border border-black px-1 py-1">Remarks</td>
                </tr>
                <tr class="align-top">
                    <td class="border border-black px-2 py-1.5 text-left leading-tight">{{ $itemDescription }}</td>
                    <td class="border border-black px-1 py-1.5 text-center">1</td>
                    <td class="border border-black px-1 py-1.5 text-center leading-tight">{{ $equipment->par_ics ?: '—' }}</td>
                    <td class="border border-black px-1 py-1.5 text-center leading-tight">{{ $endUserName !== '' ? $endUserName : '—' }}</td>
                    <td class="border border-black px-1 py-1.5 text-left leading-tight">{{ $printLog->remarks ?: ($equipment->remarks ?: '') }}</td>
                </tr>
                <tr>
                    <td class="border border-black px-2 py-2 align-top" colspan="2">
                        <p class="font-semibold">Returned by:</p>
                        <div class="mt-6 text-center">
                            <p class="font-semibold underline decoration-1 underline-offset-4">{{ $endUserName }}</p>
                            <p class="mt-0.5 text-[10px]">End User</p>
                            <p class="mt-4 font-semibold underline decoration-1 underline-offset-4">{{ $returnDate?->format('n/j/Y') }}</p>
                            <p class="mt-0.5 text-[10px]">Date</p>
                        </div>
                    </td>
                    <td class="border border-black px-2 py-2 align-top" colspan="3">
                        <p class="font-semibold">Received by:</p>
                        <div class="mt-6 text-center">
                            <p class="font-semibold underline decoration-1 underline-offset-4">
                                {{ $receivedBy ? trim($receivedBy->first_name.' '.($receivedBy->middle_name ?? '').' '.$receivedBy->last_name) : 'Lloyd Neil C. La Madrid' }}
                            </p>
                            <p class="mt-0.5 text-[10px]">Head, Property and/or Supply Division/Unit</p>
                            <p class="mt-4 font-semibold underline decoration-1 underline-offset-4">{{ $returnDate?->format('n/j/Y') }}</p>
                            <p class="mt-0.5 text-[10px]">Date</p>
                        </div>
                    </td>
                </tr>
            </table>
