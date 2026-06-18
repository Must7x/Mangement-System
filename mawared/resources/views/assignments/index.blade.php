@extends('layouts.app')

@section('title', __('pages.assignments'))

@section('content')
    <x-page-header
        title="{{ __('pages.assignments_header') }}"
        subtitle="{{ __('pages.assignments_subtitle') }}"
    >
        <x-slot:actions>
            @if (auth()->user()->hasPermission('assignments.create'))
            <button type="button"
                    id="open-assign-modal"
                    @disabled($warehouseAssets->isEmpty() || $employees->isEmpty())
                    class="btn btn-primary"
                    @if($warehouseAssets->isEmpty() || $employees->isEmpty()) disabled @endif>
                <i class="fa-solid fa-file-circle-plus"></i>
                {{ __('actions.new_assignment') }}
            </button>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if ($warehouseAssets->isEmpty())
        <div class="alert alert-warning">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ __('messages.assignments.no_warehouse_assets') }}</span>
        </div>
    @elseif ($employees->isEmpty())
        <div class="alert alert-warning">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ __('messages.assignments.no_employees') }}</span>
        </div>
    @else
        <div class="alert" style="background:#f0fdfa;border-color:#99f6e4;color:#0f766e;margin-bottom:1.25rem;">
            <i class="fa-solid fa-box"></i>
            <span>{{ __('messages.assignments.available_devices', ['count' => $warehouseAssets->count()]) }}</span>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">
                <i class="fa-solid fa-list-check" style="color:var(--color-primary-light);margin-left:0.5rem;"></i>
                {{ __('messages.assignments.active_custody_title') }}
            </h3>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('tables.device') }}</th>
                        <th>{{ __('tables.serial_number') }}</th>
                        <th>{{ __('tables.recipient') }}</th>
                        <th>{{ __('tables.department') }}</th>
                        <th>{{ __('tables.assigned_date') }}</th>
                        <th>{{ __('tables.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        <tr>
                            <td><strong>{{ $assignment->asset->name }}</strong></td>
                            <td><span class="serial-badge">{{ $assignment->asset->serial_number }}</span></td>
                            <td>
                                <span style="display:inline-flex;align-items:center;gap:0.4rem;">
                                    <span style="width:1.75rem;height:1.75rem;border-radius:50%;background:#ecfdf5;color:#059669;display:inline-flex;align-items:center;justify-content:center;font-size:0.7rem;">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    {{ $assignment->employee?->name ?? $assignment->employee_name }}
                                </span>
                            </td>
                            <td>{{ $assignment->employee?->department?->name ?? $assignment->department }}</td>
                            <td>
                                <span style="font-variant-numeric:tabular-nums;">{{ $assignment->assigned_date->format('Y/m/d') }}</span>
                            </td>
                            <td>
                                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                                    @if (auth()->user()->hasPermission('custody_receipts.view'))
                                    <a href="{{ route('assignments.receipt', $assignment) }}"
                                       target="_blank"
                                       class="btn btn-ghost btn-sm">
                                        <i class="fa-solid fa-print"></i> {{ __('actions.custody_receipt') }}
                                    </a>
                                    @endif
                                    @if (auth()->user()->hasPermission('assignments.return'))
                                    <form method="POST"
                                          action="{{ route('assignments.destroy', $assignment) }}"
                                          onsubmit="return confirm(@json(__('messages.confirms.revoke_assignment')));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-rotate-left"></i> {{ __('actions.revoke_assignment') }}
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fa-solid fa-file-signature"></i>
                                    <p>{{ __('messages.empty.no_active_assignments') }}</p>
                                    @if ($warehouseAssets->isNotEmpty() && $employees->isNotEmpty() && auth()->user()->hasPermission('assignments.create'))
                                        <button type="button" id="open-assign-modal-empty" class="btn btn-primary" style="margin-top:1rem;">
                                            {{ __('actions.first_assignment') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (auth()->user()->hasPermission('assignments.create'))
    <div id="assign-modal" class="hidden">
        <div class="modal-backdrop" data-close-modal></div>
        <div class="modal-panel">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 style="margin:0;font-weight:700;">{{ __('messages.assignments.modal_title') }}</h3>
                    <button type="button" data-close-modal class="btn btn-ghost btn-sm" style="padding:0.35rem 0.5rem;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('assignments.store') }}" class="card-body">
                    @csrf
                    <div class="form-group">
                        <label for="asset_id" class="form-label">{{ __('fields.device_warehouse_only') }}</label>
                        <select name="asset_id" id="asset_id" required class="form-select">
                            <option value="">{{ __('common.select_device') }}</option>
                            @foreach ($warehouseAssets as $asset)
                                <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>
                                    {{ $asset->name }} {{ __('common.em_dash') }} {{ $asset->serial_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="employee_id" class="form-label">{{ __('fields.recipient_employee') }}</label>
                        <select name="employee_id" id="employee_id" required class="form-select">
                            <option value="">{{ __('common.select_employee') }}</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                                    {{ $employee->name }}
                                    @if ($employee->department)
                                        {{ __('common.em_dash') }} {{ $employee->department->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assigned_date" class="form-label">{{ __('fields.assignment_date') }}</label>
                        <input type="date" name="assigned_date" id="assigned_date"
                               value="{{ old('assigned_date', now()->format('Y-m-d')) }}" required class="form-input">
                    </div>
                    <div style="display:flex;gap:0.75rem;padding-top:0.5rem;">
                        <button type="submit" class="btn btn-primary" style="flex:1;">
                            <i class="fa-solid fa-check"></i> {{ __('actions.save_assignment') }}
                        </button>
                        <button type="button" data-close-modal class="btn btn-ghost">{{ __('actions.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
@if (auth()->user()->hasPermission('assignments.create'))
<script>
    const modalRoot = document.getElementById('assign-modal');
    const backdrop = modalRoot?.querySelector('.modal-backdrop');
    const panel = modalRoot?.querySelector('.modal-panel');

    function openModal() {
        if (!modalRoot) return;
        modalRoot.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop?.classList.add('is-open');
            panel?.classList.add('is-open');
        });
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        backdrop?.classList.remove('is-open');
        panel?.classList.remove('is-open');
        setTimeout(() => {
            modalRoot?.classList.add('hidden');
            document.body.style.overflow = '';
        }, 200);
    }

    document.getElementById('open-assign-modal')?.addEventListener('click', openModal);
    document.getElementById('open-assign-modal-empty')?.addEventListener('click', openModal);
    modalRoot?.querySelectorAll('[data-close-modal]').forEach(el => el.addEventListener('click', closeModal));

    @if ($errors->has('asset_id') || $errors->has('employee_id') || $errors->has('assigned_date'))
        openModal();
    @endif
</script>
@endif
@endpush
