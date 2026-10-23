@php
    $money = static function ($value): string {
        if ($value === null || $value === '') {
            return '';
        }

        return number_format((float) $value, 2);
    };

    $items = $formData['items'] ?? [];
    $qtyTotal = collect($items)->sum('qty');
    $costTotal = collect($items)->sum(fn ($item) => (float) ($item['total_cost'] ?? 0));
    $accumulatedTotal = collect($items)->sum(fn ($item) => (float) ($item['accumulated'] ?? 0));
    $carryingTotal = collect($items)->sum(fn ($item) => (float) ($item['carrying'] ?? 0));
    $appraisedTotal = collect($items)->sum(fn ($item) => (float) ($item['appraised'] ?? 0));
    $orAmountTotal = collect($items)->sum(fn ($item) => (float) ($item['or_amount'] ?? 0));
    $disposalTotal = collect($items)->sum(fn ($item) => (float) ($item['disposal_total'] ?? 0));
@endphp

@if(! empty($showTitle))
    <h3 class="mb-0 text-center text-[13px] font-bold uppercase leading-tight tracking-wide">
        Inventory and Inspection Report of Unserviceable PPE Property
    </h3>
@endif
<p class="mb-2 text-center text-[10px] italic leading-tight">As at {{ $formData['asOfDate'] }}</p>

<table class="w-full table-fixed border-collapse border border-black text-[8px] leading-tight">
    <tr>
        <td class="border border-black px-1.5 py-1 align-top" colspan="9">
            <div class="flex items-end gap-1">
                <span class="shrink-0 font-semibold leading-tight">Entity<br>Name:</span>
                <span class="flex-1 border-b border-black px-1 font-semibold">{{ $formData['entityName'] }}</span>
            </div>
        </td>
        <td class="border border-black px-1.5 py-1 align-top" colspan="8">
            <div class="flex items-end gap-1">
                <span class="shrink-0 font-semibold leading-tight">Fund<br>Cluster :</span>
                <span class="flex-1 border-b border-black px-1 font-semibold">{{ $formData['fundCluster'] }}</span>
            </div>
        </td>
    </tr>
    <tr class="text-center">
        <td class="border border-black px-1 py-1.5" colspan="4">
            <p class="min-h-[1rem] font-semibold">{{ $formData['accountableOfficer'] }}</p>
            <p class="text-[7px]">Name of Accountable Officer</p>
        </td>
        <td class="border border-black px-1 py-1.5" colspan="3">
            <p class="min-h-[1rem] font-semibold">{{ $formData['designation'] }}</p>
            <p class="text-[7px]">Designation</p>
        </td>
        <td class="border border-black px-1 py-1.5" colspan="10">
            <p class="min-h-[1rem] font-semibold">{{ $formData['station'] }}</p>
            <p class="text-[7px]">Station</p>
        </td>
    </tr>
    <tr class="text-center font-semibold">
        <td class="border border-black px-1 py-0.5" colspan="9">Inventory</td>
        <td class="border border-black px-1 py-0.5" colspan="6">Inspection and Disposal</td>
        <td class="border border-black px-1 py-0.5" colspan="2">Record of sales</td>
    </tr>
    <tr class="text-center font-semibold">
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 6.5%;">Date<br>Acquired</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 15%;">Particulars/Articles</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 9%;">Semi-expendable<br>Property No.</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 3.5%;">Qty.</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 7%;">Unit Cost</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 7%;">Total Cost</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 7.5%;">Accumulated<br>Depreciation/<br>Accumulated</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 7%;">Carrying<br>Amount</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 11%;">Remarks</td>
        <td class="border border-black px-0.5 py-0.5" colspan="5">Disposal</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 6%;">Appraised<br>Value</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 5%;">O.R. No.</td>
        <td class="border border-black px-0.5 py-1 align-middle" rowspan="2" style="width: 5%;">Amount</td>
    </tr>
    <tr class="text-center font-semibold">
        <td class="border border-black px-0.5 py-1" style="width: 4%;">Sale</td>
        <td class="border border-black px-0.5 py-1" style="width: 4%;">Transfer</td>
        <td class="border border-black px-0.5 py-1" style="width: 5%;">Destruction</td>
        <td class="border border-black px-0.5 py-1" style="width: 5%;">Others<br>(Specify)</td>
        <td class="border border-black px-0.5 py-1" style="width: 4%;">Total</td>
    </tr>

    @forelse($items as $item)
        <tr class="align-top">
            <td class="border border-black px-0.5 py-1 text-center whitespace-nowrap">{{ $item['date_acquired'] }}</td>
            <td class="border border-black px-1 py-1">{{ $item['particulars'] }}</td>
            <td class="border border-black px-0.5 py-1 text-center">{{ $item['property_no'] }}</td>
            <td class="border border-black px-0.5 py-1 text-center">{{ $item['qty'] }}</td>
            <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $money($item['unit_cost']) }}</td>
            <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $money($item['total_cost']) }}</td>
            <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $money($item['accumulated']) }}</td>
            <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $money($item['carrying']) }}</td>
            <td class="border border-black px-1 py-1">{{ $item['remarks'] }}</td>
            <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $money($item['sale']) }}</td>
            <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $money($item['transfer']) }}</td>
            <td class="border border-black px-0.5 py-1 text-center">{{ $item['destruction'] }}</td>
            <td class="border border-black px-0.5 py-1">{{ $item['others'] }}</td>
            <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $money($item['disposal_total']) }}</td>
            <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $money($item['appraised']) }}</td>
            <td class="border border-black px-0.5 py-1 text-center">{{ $item['or_no'] }}</td>
            <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $money($item['or_amount']) }}</td>
        </tr>
    @empty
        <tr>
            <td class="border border-black px-1 py-8" colspan="17"></td>
        </tr>
    @endforelse

    <tr class="font-semibold">
        <td class="border border-black px-1 py-1 text-right" colspan="3">Grand Total</td>
        <td class="border border-black px-0.5 py-1 text-center">{{ $qtyTotal ?: '' }}</td>
        <td class="border border-black px-0.5 py-1"></td>
        <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $items ? $money($costTotal) : '' }}</td>
        <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $items ? $money($accumulatedTotal) : '' }}</td>
        <td class="border border-black px-0.5 py-1 text-right whitespace-nowrap">{{ $items ? $money($carryingTotal) : '' }}</td>
        <td class="border border-black px-0.5 py-1" colspan="5"></td>
        <td class="border border-black px-0.5 py-1 text-right">{{ $disposalTotal ? $money($disposalTotal) : '' }}</td>
        <td class="border border-black px-0.5 py-1 text-right">{{ $appraisedTotal ? $money($appraisedTotal) : '' }}</td>
        <td class="border border-black px-0.5 py-1"></td>
        <td class="border border-black px-0.5 py-1 text-right">{{ $orAmountTotal ? $money($orAmountTotal) : '' }}</td>
    </tr>
    <tr class="align-top">
        <td class="border border-black px-1.5 py-1.5" colspan="9">
            <p>I HEREBY request inspection and disposition, pursuant to Section 79 of P.D. No. 1445, of the property enumerated above.</p>
        </td>
        <td class="border border-black px-1.5 py-1.5" colspan="6">
            <p>I CERTIFY that I have inspected each and every article enumerated in this report, and that the disposition made thereof was in my judgment, the best for the public interest.</p>
        </td>
        <td class="border border-black px-1.5 py-1.5" colspan="2">
            <p>I CERTIFY that I have witnessed the disposition of the articles enumerated on this <span class="inline-block min-w-[2rem] border-b border-black">&nbsp;</span> day of <span class="inline-block min-w-[3.5rem] border-b border-black">&nbsp;</span>.</p>
        </td>
    </tr>
    <tr class="align-top text-center">
        <td class="border border-black px-1 py-2" colspan="5">
            <p class="text-left font-semibold">Requested by:</p>
            <p class="mt-6 min-h-[1rem] font-semibold">{{ $formData['accountableOfficer'] }}</p>
            <p class="border-t border-black pt-0.5 text-[7px]">(Signature over Printed Name of Accountable Officer)</p>
            <p class="mt-2 min-h-[1rem] font-semibold">{{ $formData['designation'] }}</p>
            <p class="text-[7px]">(Designation of Accountable Officer)</p>
        </td>
        <td class="border border-black px-1 py-2" colspan="4">
            <p class="text-left font-semibold">Approved by:</p>
            <p class="mt-6 min-h-[1rem] font-semibold">{{ $formData['approvedBy'] }}</p>
            <p class="border-t border-black pt-0.5 text-[7px]">&nbsp;</p>
            <p class="mt-2 min-h-[1rem] font-semibold">{{ $formData['approvedByPosition'] }}</p>
        </td>
        <td class="border border-black px-1 py-2" colspan="6">
            <p class="mt-8 min-h-[1rem] font-semibold">{{ $formData['inspectedBy'] }}</p>
            <p class="border-t border-black pt-0.5 text-[7px]">(Signature over Printed Name of Inspection Officer)</p>
        </td>
        <td class="border border-black px-1 py-2" colspan="2">
            <p class="mt-8 min-h-[1rem] font-semibold">{{ $formData['witness'] }}</p>
            <p class="border-t border-black pt-0.5 text-[7px]">(Signature over Printed Name)</p>
        </td>
    </tr>
</table>
