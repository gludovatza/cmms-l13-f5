<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">

    <title>
        {{ __('module_names.worksheets.label') }}
        #{{ $worksheet->id }}
    </title>

    <style>
        @page {
            margin: 25px 30px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #222;
        }

        h1 {
            margin-bottom: 5px;
            font-size: 22px;
            text-align: center;
        }

        .subtitle {
            margin-bottom: 25px;
            text-align: center;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #bbb;
            text-align: left;
            vertical-align: top;
        }

        th {
            width: 32%;
            background-color: #eee;
            font-weight: bold;
        }

        .description {
            white-space: pre-wrap;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #777;
        }

        .page-break {
            page-break-after: always;
        }

        tr {
            page-break-inside: avoid;
        }

        .keep-together {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <h1>
        {{ __('module_names.worksheets.label') }}
    </h1>

    <div class="subtitle">
        #{{ $worksheet->id }}
    </div>

    <table>
        <tbody>
            <tr>
                <th>Id</th>
                <td>{{ $worksheet->id }}</td>
            </tr>
            <tr>
                <th>{{ __('fields.creator') }}</th>
                <td>{{ $worksheet->creator->name }}</td>
            </tr>
            <tr>
                <th>{{ __('fields.repairer') }}</th>
                <td>{{ $worksheet->repairer?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('fields.due_date') }}</th>
                <td>{{ $worksheet->due_date?->format('Y-m-d') ?? '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('fields.finish_date') }}</th>
                <td>{{ $worksheet->finish_date?->format('Y-m-d') ?? '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('module_names.devices.label') }}</th>
                <td>{{ $worksheet->device?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('fields.priority') }}</th>
                <td>{{ $worksheet->priority->getLabel() }}</td>
            </tr>
            <tr>
                <th>{{ __('fields.description') }}</th>
                <td class="description">
                    {{ $worksheet->description }}
                </td>
            </tr>
            <tr>
                <th>{{ __('fields.created_at') }}</th>
                <td>
                    {{ $worksheet->created_at?->format('Y-m-d H:i') }}
                </td>
            </tr>
            <tr>
                <th>{{ __('fields.updated_at') }}</th>
                <td>
                    {{ $worksheet->updated_at?->format('Y-m-d H:i') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        {{ config('app.name') }} - {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>
