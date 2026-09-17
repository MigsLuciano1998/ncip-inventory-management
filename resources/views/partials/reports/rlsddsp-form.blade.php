@php
    $checked = fn (bool $on) => $on
        ? '<span class="inline-block w-3 h-3 border border-black leading-none text-center text-[9px]">✓</span>'
        : '<span class="inline-block w-3 h-3 border border-black"></span>';
@endphp

<table class="w-full border-collapse border border-black text-[11px] leading-tight">
    <tr>
        <td class="border border-black p-1.5 align-top" colspan="2" style="width: 68%;">
            <table class="w-full border-collapse">
                <tr>
                    <td class="w-[38%] py-0.5 align-bottom font-semibold">Entity Name:</td>
                    <td class="border-b border-black px-1 py-0.5 font-semibold">{{ $formData['entityName'] }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-bottom font-semibold">Department/Office :</td>
                    <td class="border-b border-black px-1 py-0.5">{{ $formData['departmentOffice'] }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-bottom font-semibold">Accountable Officer :</td>
                    <td class="border-b border-black px-1 py-0.5">{{ $formData['accountableOfficer'] }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-bottom font-semibold">Designation :</td>
                    <td class="border-b border-black px-1 py-0.5">{{ $formData['designation'] }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-top font-semibold">Police Notified :</td>
                    <td class="px-1 py-0.5">
                        <div class="flex items-center gap-6">
                            <span class="inline-flex items-center gap-1">{!! $checked(false) !!} Yes</span>
                            <span class="inline-flex items-center gap-1">{!! $checked(false) !!} No</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="py-0.5 align-bottom font-semibold">Police Station:</td>
                    <td class="border-b border-black px-1 py-0.5">&nbsp;</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-bottom font-semibold">Date:</td>
                    <td class="border-b border-black px-1 py-0.5">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td class="border border-black p-1.5 align-top" style="width: 32%;">
            <table class="w-full border-collapse">
                <tr>
                    <td class="w-[42%] py-0.5 align-bottom font-semibold">Fund Cluster:</td>
                    <td class="border-b border-black px-1 py-0.5 font-semibold">{{ $formData['fundCluster'] }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-bottom font-semibold">RLSDDSP No. :</td>
                    <td class="border-b border-black px-1 py-0.5">{{ $formData['rlsddspNo'] }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-bottom font-semibold">RLSDDSP Date :</td>
                    <td class="border-b border-black px-1 py-0.5">{{ $formData['rlsddspDate'] }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-bottom font-semibold">PAR/ICS No. :</td>
                    <td class="border-b border-black px-1 py-0.5">{{ $formData['parIcsNo'] }}</td>
                </tr>
                <tr>
                    <td class="py-0.5 align-bottom font-semibold">ICS Date:</td>
                    <td class="border-b border-black px-1 py-0.5">{{ $formData['icsDate'] }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="border border-black p-1.5" colspan="3">
            <p class="mb-1 font-semibold">Status of Semi-expendable Property: (check applicable box)</p>
            <div class="ml-8 grid grid-cols-2 gap-x-8 gap-y-1">
                <span class="inline-flex items-center gap-1">{!! $checked($formData['status'] === 'lost') !!} Lost</span>
                <span class="inline-flex items-center gap-1">{!! $checked($formData['status'] === 'damaged') !!} Damaged</span>
                <span class="inline-flex items-center gap-1">{!! $checked($formData['status'] === 'stolen') !!} Stolen</span>
                <span class="inline-flex items-center gap-1">{!! $checked($formData['status'] === 'destroyed') !!} Destroyed</span>
            </div>
        </td>
    </tr>
    <tr class="text-center font-semibold">
        <td class="border border-black px-1 py-1" style="width: 28%;">Property No.</td>
        <td class="border border-black px-1 py-1">Description</td>
        <td class="border border-black px-1 py-1" style="width: 22%;">Acquisition Cost</td>
    </tr>
    @foreach($formData['items'] as $item)
        <tr class="align-top">
            <td class="border border-black px-1 py-1.5">{{ $item['propertyNo'] }}</td>
            <td class="border border-black px-1 py-1.5">{{ $item['description'] }}</td>
            <td class="border border-black px-1 py-1.5 text-right">{{ $item['acquisitionCost'] }}</td>
        </tr>
    @endforeach
    <tr>
        <td class="border border-black px-1 py-3">&nbsp;</td>
        <td class="border border-black px-1 py-3">&nbsp;</td>
        <td class="border border-black px-1 py-3">&nbsp;</td>
    </tr>
    <tr>
        <td class="border border-black p-1.5 align-top" colspan="3" style="height: 90px;">
            <p class="font-semibold">Circumstances:</p>
            <p class="mt-1 whitespace-pre-wrap">{{ $formData['circumstances'] }}</p>
        </td>
    </tr>
    <tr>
        <td class="border border-black p-1.5 align-top" colspan="2">
            <p>I hereby certify that the item/s and circumstances stated above are true and correct.</p>
            <div class="mt-10 text-center">
                <p class="min-h-[1.1rem] font-semibold">{{ $formData['accountableOfficer'] }}</p>
                <p class="mx-auto mt-0 w-4/5 border-t border-black pt-0.5 text-[10px]">Signature over Printed Name of the Accountable Officer</p>
                <p class="mt-6 min-h-[1.1rem]">&nbsp;</p>
                <p class="mx-auto w-2/5 border-t border-black pt-0.5 text-[10px]">Date</p>
            </div>
        </td>
        <td class="border border-black p-1.5 align-top">
            <p>Noted by:</p>
            <div class="mt-10 text-center">
                <p class="min-h-[1.1rem]">&nbsp;</p>
                <p class="mx-auto mt-0 w-4/5 border-t border-black pt-0.5 text-[10px]">Signature over Printed Name of the Immediate Supervisor</p>
                <p class="mt-6 min-h-[1.1rem]">&nbsp;</p>
                <p class="mx-auto w-2/5 border-t border-black pt-0.5 text-[10px]">Date</p>
            </div>
        </td>
    </tr>
    <tr>
        <td class="border border-black p-1.5 align-top" colspan="2">
            <p>Government Issued</p>
            <p>ID No.</p>
            <p>Date Issued :</p>
            <p class="mt-3">SUBSCRIBED AND SWORN to before me this ________ day of ______________, affiant exhibiting the above government issued identification card.</p>
            <p class="mt-2">Doc. No. ______________</p>
            <p>Page No. ______________</p>
            <p>Book No. ______________</p>
            <p>Series of ______________</p>
        </td>
        <td class="border border-black p-1.5 align-bottom text-center">
            <p class="mb-8">&nbsp;</p>
            <p class="mx-auto w-4/5 border-t border-black pt-0.5">Notary Public</p>
        </td>
    </tr>
</table>
