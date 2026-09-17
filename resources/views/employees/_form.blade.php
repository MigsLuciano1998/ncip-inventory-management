<div class="space-y-6">
    @if(session()->has('message'))
        <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="pmms-card p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">EE Sequence Number</label>
                <input type="text" name="ee_sequence_number" value="{{ old('ee_sequence_number', $employee->ee_sequence_number ?? '') }}" class="pmms-input w-full" placeholder="e.g. NCIP-001">
                @error('ee_sequence_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Position</label>
                <input type="text" name="position" value="{{ old('position', $employee->position ?? '') }}" class="pmms-input w-full" placeholder="Job title or role">
                @error('position') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name ?? '') }}" class="pmms-input w-full" placeholder="First name">
                @error('first_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Middle Name</label>
                <input type="text" name="middle_name" value="{{ old('middle_name', $employee->middle_name ?? '') }}" class="pmms-input w-full" placeholder="Middle name">
                @error('middle_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name ?? '') }}" class="pmms-input w-full" placeholder="Last name">
                @error('last_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">Office</label>
                <select name="office_id" class="pmms-select w-full">
                    <option value="">Select office</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ old('office_id', $employee->office_id ?? '') == $office->id ? 'selected' : '' }}>{{ $office->office_name }}</option>
                    @endforeach
                </select>
                @error('office_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                <select name="status" class="pmms-select w-full">
                    <option value="1" {{ old('status', $employee->status ?? 1) ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $employee->status ?? 1) ? '' : 'selected' }}>Inactive</option>
                </select>
                @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('employees.index') }}" class="pmms-btn-secondary">Cancel</a>
            <button type="submit" class="pmms-btn-primary">{{ $buttonText }}</button>
        </div>
    </div>
</div>
