<div style="max-width:40rem;margin:0 auto;">
    <a href="{{ route('inventory.index') }}" class="link-action" style="font-size:0.875rem;margin-bottom:1rem;display:inline-flex;">
        <i class="fa-solid fa-arrow-right"></i> {{ __('actions.back_to_inventory') }}
    </a>

    <x-page-header :title="$title" subtitle="{{ __('pages.assets_form_subtitle') }}" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $action }}">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                <div class="form-group">
                    <label for="name" class="form-label">{{ __('fields.asset_name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $asset?->name) }}" required
                           class="form-input" placeholder="{{ __('fields.asset_name_placeholder') }}">
                </div>

                <div class="form-group">
                    <label for="type" class="form-label">{{ __('fields.asset_type') }}</label>
                    <input type="text" name="type" id="type" list="asset-types" value="{{ old('type', $asset?->type) }}" required
                           class="form-input" placeholder="{{ __('fields.asset_type_placeholder') }}">
                    <datalist id="asset-types">
                        @foreach ($assetTypes as $typeKey)
                            <option value="{{ $typeKey }}">{{ __('fields.asset_type_options.'.$typeKey) }}</option>
                        @endforeach
                    </datalist>
                </div>

                <div class="form-group">
                    <label for="serial_number" class="form-label">{{ __('fields.serial_number') }}</label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $asset?->serial_number) }}" required
                           class="form-input serial-badge" style="font-family:ui-monospace,monospace;">
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">{{ __('fields.status') }}</label>
                    <select name="status" id="status" required class="form-select"
                            @if($asset?->status === \App\Enums\AssetStatus::Active || $asset?->status === \App\Enums\AssetStatus::Maintenance) disabled @endif>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}"
                                @selected(old('status', $asset?->status?->value ?? \App\Enums\AssetStatus::Warehouse->value) === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    @if($asset?->status === \App\Enums\AssetStatus::Active)
                        <input type="hidden" name="status" value="{{ $asset->status->value }}">
                        <p class="alert alert-warning" style="margin-top:0.75rem;margin-bottom:0;font-size:0.8rem;">
                            <i class="fa-solid fa-link"></i>
                            {{ __('messages.assets.status_active_locked') }}
                            <a href="{{ route('assignments.index') }}" class="link-action">{{ __('messages.assets.revoke_assignment_link') }}</a>
                            {{ __('messages.assets.status_active_locked_suffix') }}
                        </p>
                    @elseif($asset?->status === \App\Enums\AssetStatus::Maintenance)
                        <input type="hidden" name="status" value="{{ $asset->status->value }}">
                        <p class="alert alert-warning" style="margin-top:0.75rem;margin-bottom:0;font-size:0.8rem;">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                            {{ __('messages.assets.status_maintenance_locked') }}
                            <a href="{{ route('maintenances.index') }}" class="link-action">{{ __('messages.assets.manage_maintenance_link') }}</a>.
                        </p>
                    @else
                        <p style="font-size:0.8rem;color:var(--color-muted);margin:0.5rem 0 0;">
                            {{ __('messages.assets.status_activation_hint') }}
                        </p>
                    @endif
                </div>

                <div style="display:flex;flex-wrap:gap:0.75rem;padding-top:0.5rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> {{ __('actions.save') }}
                    </button>
                </div>
            </form>

            @if ($asset && auth()->user()->canDeleteAssets() && ! $asset->assignment && $asset->status !== \App\Enums\AssetStatus::Active && $asset->status !== \App\Enums\AssetStatus::Maintenance)
                <form method="POST" action="{{ route('assets.destroy', $asset) }}"
                      onsubmit="return confirm(@json(__('messages.confirms.delete_asset')));"
                      style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--color-border);">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can"></i> {{ __('actions.delete') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
