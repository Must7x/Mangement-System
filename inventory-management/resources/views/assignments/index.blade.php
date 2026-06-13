@extends('layouts.app')

@section('title', 'سجل التخصيصات والعهد')

@section('content')
    <x-page-header
        title="إدارة التخصيص"
        subtitle="ربط الأجهزة المتاحة في المخزن بالموظفين وإدارة الإرجاع"
    >
        <x-slot:actions>
            <button type="button"
                    id="open-assign-modal"
                    @disabled($warehouseAssets->isEmpty())
                    class="btn btn-primary {{ $warehouseAssets->isEmpty() ? '' : '' }}"
                    @if($warehouseAssets->isEmpty()) disabled @endif>
                <i class="fa-solid fa-file-circle-plus"></i>
                تخصيص عهدة جديدة
            </button>
        </x-slot:actions>
    </x-page-header>

    @if ($warehouseAssets->isEmpty())
        <div class="alert alert-warning">
            <i class="fa-solid fa-circle-info"></i>
            <span>لا توجد أجهزة بحالة «مخزن» — زر التخصيص معطّل حتى يتوفر عتاد جاهز للإسناد.</span>
        </div>
    @else
        <div class="alert" style="background:#f0fdfa;border-color:#99f6e4;color:#0f766e;margin-bottom:1.25rem;">
            <i class="fa-solid fa-box"></i>
            <span><strong>{{ $warehouseAssets->count() }}</strong> جهاز متاح في المخزن للتخصيص.</span>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 style="margin:0;font-size:1rem;font-weight:700;">
                <i class="fa-solid fa-list-check" style="color:var(--color-primary-light);margin-left:0.5rem;"></i>
                العهد النشطة
            </h3>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الجهاز</th>
                        <th>S/N</th>
                        <th>الموظف المستلم</th>
                        <th>القسم</th>
                        <th>تاريخ الإسناد</th>
                        <th>إجراء</th>
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
                                    {{ $assignment->employee_name }}
                                </span>
                            </td>
                            <td>{{ $assignment->department }}</td>
                            <td>
                                <span style="font-variant-numeric:tabular-nums;">{{ $assignment->assigned_date->format('Y/m/d') }}</span>
                            </td>
                            <td>
                                <form method="POST"
                                      action="{{ route('assignments.destroy', $assignment) }}"
                                      onsubmit="return confirm('تأكيد سحب العهدة وإرجاع الجهاز إلى المخزن؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-rotate-left"></i> سحب العهدة
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fa-solid fa-file-signature"></i>
                                    <p>لا توجد عهد نشطة حالياً.</p>
                                    @if ($warehouseAssets->isNotEmpty())
                                        <button type="button" id="open-assign-modal-empty" class="btn btn-primary" style="margin-top:1rem;">
                                            تخصيص أول عهدة
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

    <div id="assign-modal" class="hidden">
        <div class="modal-backdrop" data-close-modal></div>
        <div class="modal-panel">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 style="margin:0;font-weight:700;">تخصيص عهدة جديدة</h3>
                    <button type="button" data-close-modal class="btn btn-ghost btn-sm" style="padding:0.35rem 0.5rem;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('assignments.store') }}" class="card-body">
                    @csrf
                    <div class="form-group">
                        <label for="asset_id" class="form-label">الجهاز (مخزن فقط)</label>
                        <select name="asset_id" id="asset_id" required class="form-select">
                            <option value="">— اختر جهازاً —</option>
                            @foreach ($warehouseAssets as $asset)
                                <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>
                                    {{ $asset->name }} — {{ $asset->serial_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="employee_name" class="form-label">اسم الموظف المستلم</label>
                        <input type="text" name="employee_name" id="employee_name" value="{{ old('employee_name') }}" required
                               class="form-input" placeholder="مثال: محمد ولد أحمد">
                    </div>
                    <div class="form-group">
                        <label for="department" class="form-label">القسم / الإدارة</label>
                        <input type="text" name="department" id="department" value="{{ old('department') }}" required
                               class="form-input" placeholder="مثال: مديرية البنية التحتية">
                    </div>
                    <div class="form-group">
                        <label for="assigned_date" class="form-label">تاريخ إسناد العهدة</label>
                        <input type="date" name="assigned_date" id="assigned_date"
                               value="{{ old('assigned_date', now()->format('Y-m-d')) }}" required class="form-input">
                    </div>
                    <div style="display:flex;gap:0.75rem;padding-top:0.5rem;">
                        <button type="submit" class="btn btn-primary" style="flex:1;">
                            <i class="fa-solid fa-check"></i> حفظ العهدة
                        </button>
                        <button type="button" data-close-modal class="btn btn-ghost">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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

    @if ($errors->has('asset_id') || $errors->has('employee_name') || $errors->has('department') || $errors->has('assigned_date'))
        openModal();
    @endif
</script>
@endpush
