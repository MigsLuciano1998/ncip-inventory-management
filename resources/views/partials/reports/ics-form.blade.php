            <table class="mt-2 w-full border-collapse border border-black text-[11px] leading-tight">
                <tr>
                    <td class="border border-black px-2 py-1 align-top" colspan="5">
                        <p><span class="font-semibold">Entity Name:</span> National Commission on Indigenous Peoples</p>
                        <div class="mt-1 flex gap-2">
                            <span class="shrink-0 font-semibold leading-tight">Fund<br>Cluster</span>
                            <span class="flex-1 border-b border-black font-semibold">{{ $fundCluster }}</span>
                        </div>
                    </td>
                    <td class="border border-black px-2 py-1 align-top" colspan="2">
                        <p><span class="font-semibold">ICS No :</span> {{ $icsNumber }}</p>
                    </td>
                </tr>
                <tr class="text-center font-semibold">
                    <td class="w-[50px] border border-black px-1 py-1" rowspan="2">Quantity</td>
                    <td class="w-[50px] border border-black px-1 py-1" rowspan="2">Unit</td>
                    <td class="border border-black px-1 py-1" colspan="2">Amount</td>
                    <td class="border border-black px-1 py-1" rowspan="2">Description</td>
                    <td class="w-[90px] border border-black px-1 py-1" rowspan="2">Item<br>No.</td>
                    <td class="w-[80px] border border-black px-1 py-1" rowspan="2">Estimated<br>Useful Life</td>
                </tr>
                <tr class="text-center font-semibold">
                    <td class="w-[80px] border border-black px-1 py-1">Unit<br>Cost</td>
                    <td class="w-[80px] border border-black px-1 py-1">Total Cost</td>
                </tr>
                @forelse($formItems as $item)
                    @php
                        $description = trim((string) $item->description);
                        if ($description === '') {
                            $description = trim(implode(' ', array_filter([
                                $item->brand,
                                $item->model,
                                $item->equipmentType?->name,
                            ])));
                        }
                        $cost = \App\Models\Equipment::parseCostAmount($item->cost);
                        $costDisplay = $cost === null ? '' : number_format($cost, 2);
                        $itemNo = $item->item_id ?: ($item->tag ?: $item->property_no);
                        $life = $item->estimated_useful_life;
                    @endphp
                    <tr class="align-top">
                        <td class="border border-black px-1 py-2 text-center">1</td>
                        <td class="border border-black px-1 py-2 text-center">unit</td>
                        <td class="border border-black px-1 py-2 text-right">{{ $costDisplay }}</td>
                        <td class="border border-black px-1 py-2 text-right">{{ $costDisplay }}</td>
                        <td class="border border-black px-2 py-2 text-left">{{ $description !== '' ? $description : '' }}</td>
                        <td class="border border-black px-1 py-2 text-center">{{ $itemNo }}</td>
                        <td class="border border-black px-1 py-2 text-center">{{ $life ? $life.' year'.($life == 1 ? '' : 's') : '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="border border-black px-1 py-8" colspan="7"></td>
                    </tr>
                @endforelse
                <tr>
                    <td class="border border-black px-2 py-2 align-top" colspan="4">
                        <p class="font-semibold">Received from:</p>
                        <div class="mt-8 text-center">
                            <p class="min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $issuedBy?->last_name_first ?? 'La Madrid, Lloyd Neil C.' }}
                            </p>
                            <p class="mt-0.5 text-[10px]">Signature Over Printed Name</p>
                            <p class="mt-3 min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $issuedBy?->position ?? 'Supply/Officer' }}
                            </p>
                            <p class="mt-0.5 text-[10px]">Position/Office</p>
                            <p class="mt-3 min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $issuedDate?->format('n/j/Y') }}
                            </p>
                            <p class="mt-0.5 text-[10px]">Date</p>
                        </div>
                    </td>
                    <td class="border border-black px-2 py-2 align-top" colspan="3">
                        <p class="font-semibold">Received by:</p>
                        <div class="mt-8 text-center">
                            <p class="min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $receivedBy?->last_name_first ?? '' }}
                            </p>
                            <p class="mt-0.5 text-[10px]">Signature Over Printed Name</p>
                            <p class="mt-3 min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $receivedBy?->position ?? '' }}
                            </p>
                            <p class="mt-0.5 text-[10px]">Position/Office</p>
                            <p class="mt-3 min-h-[1.25rem] font-semibold underline decoration-1 underline-offset-4">
                                {{ $issuedDate?->format('n/j/Y') }}
                            </p>
                            <p class="mt-0.5 text-[10px]">Date</p>
                        </div>
                    </td>
                </tr>
            </table>
