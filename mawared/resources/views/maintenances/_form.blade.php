<div style="max-width:40rem;margin:0 auto;">
    <a href="{{ route('maintenances.index') }}" class="link-action" style="font-size:0.875rem;margin-bottom:1rem;display:inline-flex;">
        <i class="fa-solid fa-arrow-right"></i> {{ __('actions.back_to_maintenances') }}
    </a>

    <x-page-header :title="$title" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $action }}">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                @if (! $maintenance)
                    <div class="form-group">
                        <label for="asset_id" class="form-label">الجهاز</label>
                        <select name="asset_id" id="asset_id" required class="form-select" @disabled($assets->isEmpty())>
                            <option value="">— اختر جهازاً من المخزن —</option>
                            @foreach ($assets as $asset)
                                <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>
                                    {{ $asset->name }} ({{ $asset->serial_number }})
                                </option>
                            @endforeach
                        </select>
                        @if ($assets->isEmpty())
                            <p class="alert alert-warning" style="margin-top:0.75rem;margin-bottom:0;font-size:0.8rem;">
                                لا توجد أجهزة متاحة في المخزن للصيانة.
                            </p>
                        @endif
                    </div>
                @else
                    <div class="form-group">
                        <label class="form-label">الجهاز</label>
                        <p style="margin:0;font-weight:600;">{{ $maintenance->asset?->name }} — {{ $maintenance->asset?->serial_number }}</p>
                    </div>
                @endif

                <div class="form-group">
                    <label for="issue_description" class="form-label">وصف العطل</label>
                    <textarea name="issue_description" id="issue_description" rows="3" required class="form-input"
                              placeholder="وصف المشكلة أو العطل">{{ old('issue_description', $maintenance?->issue_description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="priority" class="form-label">الأولوية</label>
                    <select name="priority" id="priority" required class="form-select">
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->value }}"
                                @selected(old('priority', $maintenance?->priority?->value ?? \App\Enums\MaintenancePriority::Medium->value) === $priority->value)>
                                {{ $priority->value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="technician_name" class="form-label">اسم الفني</label>
                    <input type="text" name="technician_name" id="technician_name"
                           value="{{ old('technician_name', $maintenance?->technician_name) }}"
                           required class="form-input" placeholder="اسم الفني المسؤول">
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">حالة الطلب</label>
                    <select name="status" id="status" required class="form-select"
                            @if($maintenance?->isClosed()) disabled @endif>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}"
                                @selected(old('status', $maintenance?->status?->value ?? \App\Enums\MaintenanceStatus::Pending->value) === $status->value)>
                                {{ $status->value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="maintenance_start_date" class="form-label">تاريخ بدء الصيانة</label>
                    <input type="date" name="maintenance_start_date" id="maintenance_start_date"
                           value="{{ old('maintenance_start_date', $maintenance?->maintenance_start_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                           required class="form-input">
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea name="notes" id="notes" rows="2" class="form-input"
                              placeholder="ملاحظات إضافية (اختياري)">{{ old('notes', $maintenance?->notes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" @if(! $maintenance && $assets->isEmpty()) disabled @endif>
                    <i class="fa-solid fa-floppy-disk"></i> {{ __('actions.save') }}
                </button>
            </form>

            @if ($maintenance && $maintenance->isOpen())
                <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--color-border);display:flex;flex-wrap:gap:0.75rem;">
                    <form method="POST" action="{{ route('maintenances.complete', $maintenance) }}"
                          onsubmit="return confirm('تأكيد إكمال الصيانة وإرجاع الجهاز للمخزن؟');">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-circle-check"></i> {{ __('actions.complete_maintenance') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('maintenances.cancel', $maintenance) }}"
                          onsubmit="return confirm('تأكيد إلغاء طلب الصيانة؟');">
                        @csrf
                        <button type="submit" class="btn btn-ghost">
                            <i class="fa-solid fa-ban"></i> {{ __('actions.cancel_maintenance') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
