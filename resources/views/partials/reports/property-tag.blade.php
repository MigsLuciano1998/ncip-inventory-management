<table class="w-full table-fixed border-collapse border border-black">
    <tr>
        <td class="border border-black px-1.5 py-1" colspan="2">
            <div class="flex items-center gap-1.5">
                <img src="{{ asset('images/logo.png') }}" alt="NCIP" class="h-8 w-8 shrink-0 object-contain">
                <div class="min-w-0 flex-1 text-center font-bold uppercase">
                    <p>National Commission on Indigenous Peoples</p>
                    <p>Cordillera Administrative Region</p>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="border border-black px-1.5 py-1 align-top" colspan="2">
            <p><span class="font-normal">Description:</span> <span class="font-bold">{{ $description }}</span></p>
        </td>
    </tr>
    <tr>
        <td class="w-1/2 border border-black px-1.5 py-1 align-top">
            <p class="font-normal">Property Number:</p>
            <p class="break-all pt-0.5 text-center font-bold">{{ $equipment->property_no ?: '' }}</p>
        </td>
        <td class="w-1/2 border border-black px-1.5 py-1 align-top">
            <p class="font-normal">Series No.</p>
            <p class="pt-0.5 text-center font-bold">{{ $seriesNo }}</p>
        </td>
    </tr>
    <tr>
        <td class="border border-black px-1.5 py-1 align-top">
            <p class="font-normal">Serial Number:</p>
            @php
                $serial = trim((string) $equipment->serial_no);
                if ($serial !== '' && ! preg_match('/^sn[- ]?/i', $serial)) {
                    $serial = 'sn-'.$serial;
                }
            @endphp
            <p class="break-all pt-0.5 font-bold">{{ $serial }}</p>
        </td>
        <td class="border border-black px-1.5 py-1 align-top">
            <p class="font-normal">Model Number:</p>
            <p class="pt-0.5 font-bold">{{ $equipment->model ?: '' }}</p>
        </td>
    </tr>
    <tr>
        <td class="border border-black px-1.5 py-1 align-top">
            <p class="font-normal">Accountable Person:</p>
            <p class="pt-0.5 text-center font-bold">{{ $accountablePerson }}</p>
        </td>
        <td class="border border-black px-1.5 py-1 align-top">
            <p class="font-normal">Office:</p>
            <p class="pt-0.5 text-center font-bold">{{ $officeLine }}</p>
        </td>
    </tr>
    <tr>
        <td class="border border-black px-1.5 py-1 align-top">
            <p class="font-normal">Acquisition Cost:</p>
            <p class="pt-0.5 text-center font-bold">{{ $acquisitionCost }}</p>
        </td>
        <td class="border border-black px-1.5 py-1 align-top">
            <p class="font-normal">Acquisition Date:</p>
            <p class="pt-0.5 text-center font-bold">{{ $acquisitionDate }}</p>
        </td>
    </tr>
    <tr>
        <td class="border border-black px-1.5 py-2 align-top" colspan="2">
            <p class="font-normal">Signature of Inventory Committee:</p>
        </td>
    </tr>
    <tr>
        <td class="border border-black px-1.5 py-1 text-center font-bold uppercase" colspan="2">
            Tampering of this sticker is prohibited
        </td>
    </tr>
</table>
