            <table class="mt-4 w-full border-collapse border border-black text-[12px]">
                <tr>
                    <td class="border border-black p-2 align-top" colspan="4">
                        <div class="flex gap-2">
                            <span class="shrink-0 font-semibold">Entity Name :</span>
                            <span class="flex-1 border-b border-black font-semibold">National Commission on Indigenous Peoples</span>
                        </div>
                        <div class="mt-2 flex gap-2">
                            <span class="shrink-0 font-semibold">Fund Cluster :</span>
                            <span class="flex-1 border-b border-black font-semibold">{{ $fundCluster }}</span>
                        </div>
                    </td>
                    <td class="border border-black p-2 align-top" colspan="2">
                        <div class="flex gap-2">
                            <span class="shrink-0 font-semibold">PAR No. :</span>
                            <span class="flex-1 border-b border-black font-semibold">{{ $parNumber }}</span>
                        </div>
                    </td>
                </tr>
                <tr class="text-center font-semibold">
                    <td class="w-[70px] border border-black px-1 py-2">Quantity</td>
                    <td class="w-[70px] border border-black px-1 py-2">Unit</td>
                    <td class="border border-black px-1 py-2">Description</td>
                    <td class="w-[150px] border border-black px-1 py-2">Property<br>Number</td>
                    <td class="w-[100px] border border-black px-1 py-2">Date<br>Acquired</td>
                    <td class="w-[100px] border border-black px-1 py-2">Amount</td>
                </tr>
                <tr class="align-top">
                    <td class="border border-black px-1 py-3 text-center">1</td>
                    <td class="border border-black px-1 py-3 text-center">unit</td>
                    <td class="border border-black px-2 py-3 text-left leading-snug">{{ $itemDescription }}</td>
                    <td class="border border-black px-2 py-3 text-center font-semibold">{{ $equipment->property_no }}</td>
                    <td class="border border-black px-1 py-3 text-center">{{ $equipment->date_acquired?->format('n/j/Y') ?? $equipment->date_purchased?->format('n/j/Y') ?? '—' }}</td>
                    <td class="border border-black px-2 py-3 text-right">{{ $equipment->cost ? number_format((float) preg_replace('/[^\d.]/', '', $equipment->cost), 2) : '—' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-3 align-top" colspan="3">
                        <p class="font-semibold">Received by:</p>
                        <div class="mt-10 text-center">
                            <p class="min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $receivedBy?->last_name_first ?? '' }}
                            </p>
                            <p class="mt-1 text-[11px]">Signature over Printed Name of End User</p>
                            <p class="mt-4 min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $receivedBy?->position ?? '' }}
                            </p>
                            <p class="mt-1 text-[11px]">Position/Office</p>
                            <p class="mt-4 min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $issuedDate?->format('n/j/Y') }}
                            </p>
                            <p class="mt-1 text-[11px]">Date</p>
                        </div>
                    </td>
                    <td class="border border-black p-3 align-top" colspan="3">
                        <p class="font-semibold">Issued by:</p>
                        <div class="mt-10 text-center">
                            <p class="min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $selectedIssuedBy?->last_name_first ?? 'La Madrid, Lloyd Neil C.' }}
                            </p>
                            <p class="mt-1 text-[11px]">Signature over Printed Name of Supply and/or Property Custodian</p>
                            <p class="mt-4 min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $selectedIssuedBy?->position ?? 'Administrative Officer III' }}
                            </p>
                            <p class="mt-1 text-[11px]">Position/Office</p>
                            <p class="mt-4 min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $issuedDate?->format('n/j/Y') }}
                            </p>
                            <p class="mt-1 text-[11px]">Date</p>
                        </div>
                    </td>
                </tr>
            </table>
