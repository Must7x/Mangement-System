<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('locales.direction.' . app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('receipts.page_title', ['number' => $receiptNumber]) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --color-primary-dark: #0d4a28;
            --color-mtnima-green: #1a6b3c;
            --color-mtnima-gold: #e5a800;
            --color-border: #e2e8f0;
            --color-text: #0f172a;
            --color-muted: #64748b;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 2rem 1.5rem;
            background: #fff;
            color: var(--color-text);
            font-family: 'Cairo', ui-sans-serif, system-ui, sans-serif;
        }

        .receipt-sheet {
            max-width: 48rem;
            margin: 0 auto;
            border: 2px solid var(--color-primary-dark);
            border-radius: 0.75rem;
            background: #fff;
        }

        .receipt-header {
            background: var(--color-primary-dark);
            color: #fff;
            padding: 1.5rem;
            text-align: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .receipt-body {
            padding: 1.75rem;
        }

        .receipt-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem 1.5rem;
            margin: 0 0 1.5rem;
        }

        .receipt-meta dt {
            font-size: 0.75rem;
            color: var(--color-muted);
            margin-bottom: 0.25rem;
        }

        .receipt-meta dd {
            margin: 0;
            font-weight: 600;
        }

        .serial-badge {
            display: inline-block;
            font-family: ui-monospace, monospace;
            font-size: 0.85rem;
            background: #f1f5f9;
            border: 1px solid var(--color-border);
            border-radius: 0.35rem;
            padding: 0.15rem 0.5rem;
        }

        .receipt-signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px dashed var(--color-border);
        }

        .signature-block { text-align: center; }

        .signature-line {
            border-top: 1px solid var(--color-text);
            margin-top: 3rem;
            padding-top: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .receipt-toolbar {
            max-width: 48rem;
            margin: 0 auto 1rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1rem;
            border-radius: 0.5rem;
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--color-mtnima-green);
            color: #fff;
        }

        .btn-ghost {
            background: #fff;
            color: var(--color-text);
            border-color: var(--color-border);
        }

        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }

            body {
                padding: 0;
                background: #fff !important;
            }

            .receipt-toolbar {
                display: none !important;
            }

            .receipt-sheet {
                max-width: none;
                border-width: 1px;
                border-radius: 0;
                box-shadow: none;
                page-break-inside: avoid;
            }

            .receipt-header,
            .serial-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-toolbar">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fa-solid fa-print"></i> {{ __('actions.print_save_pdf') }}
        </button>
        <button type="button" class="btn btn-ghost" onclick="window.close()">{{ __('actions.close') }}</button>
    </div>

    <article class="receipt-sheet">
        <header class="receipt-header">
            <img src="{{ file_exists(public_path('images/mtnima-logo.png')) ? asset('images/mtnima-logo.png') : asset('images/mtnima-logo.svg') }}"
                 alt="{{ __('branding.logo_alt') }}"
                 style="width:4.5rem;height:auto;background:#fff;border-radius:0.5rem;padding:0.35rem;margin:0 auto 1rem;display:block;">
            <p style="margin:0 0 0.35rem;font-size:0.85rem;">{{ __('branding.ministry_name') }}</p>
            <h1 style="margin:0 0 0.35rem;font-size:1.25rem;font-weight:800;">{{ __('branding.app_short') }}</h1>
            <p style="margin:0;font-size:1rem;font-weight:700;color:var(--color-mtnima-gold);">{{ __('receipts.title') }}</p>
        </header>

        <div class="receipt-body">
            <dl class="receipt-meta">
                <div>
                    <dt>{{ __('receipts.receipt_number') }}</dt>
                    <dd>{{ $receiptNumber }}</dd>
                </div>
                <div>
                    <dt>{{ __('receipts.assignment_date') }}</dt>
                    <dd>{{ $assignment->assigned_date->format('Y/m/d') }}</dd>
                </div>
                <div>
                    <dt>{{ __('receipts.device_name') }}</dt>
                    <dd>{{ $assignment->asset->name }}</dd>
                </div>
                <div>
                    <dt>{{ __('receipts.device_type') }}</dt>
                    <dd>{{ $assignment->asset->typeLabel() }}</dd>
                </div>
                <div>
                    <dt>{{ __('receipts.serial_number') }}</dt>
                    <dd><span class="serial-badge">{{ $assignment->asset->serial_number }}</span></dd>
                </div>
                <div>
                    <dt>{{ __('receipts.recipient') }}</dt>
                    <dd>{{ $assignment->employee?->name ?? $assignment->employee_name }}</dd>
                </div>
                <div>
                    <dt>{{ __('receipts.department') }}</dt>
                    <dd>{{ $assignment->employee?->department?->name ?? $assignment->department }}</dd>
                </div>
                <div>
                    <dt>{{ __('receipts.assigned_by') }}</dt>
                    <dd>{{ $assignedBy }}</dd>
                </div>
            </dl>

            <p style="margin:0;line-height:1.8;font-size:0.9rem;">
                {{ __('receipts.acknowledgment') }}
            </p>

            <div class="receipt-signatures">
                <div class="signature-block">
                    <div class="signature-line">{{ __('receipts.signature_storekeeper') }}</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line">{{ __('receipts.signature_recipient') }}</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line">{{ __('receipts.signature_supervisor') }}</div>
                </div>
            </div>

            <p style="margin:2rem 0 0;font-size:0.75rem;color:var(--color-muted);text-align:center;">
                {{ __('receipts.footer', ['app_name' => __('branding.app_short'), 'year' => date('Y')]) }}
            </p>
        </div>
    </article>
</body>
</html>
