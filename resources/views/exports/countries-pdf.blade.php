<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Countries Data Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            color: #007bff;
            font-size: 24px;
        }

        .meta {
            text-align: right;
            font-size: 11px;
            color: #666;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table thead {
            background-color: #f8f9fa;
        }

        table th {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            color: #333;
        }

        table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            font-size: 11px;
        }

        table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Countries Data Export</h1>
        <p>Global Countries Information Report</p>
    </div>

    <div class="meta">
        <p>
            <strong>Total Records:</strong> {{ $totalCount }}<br>
            <strong>Exported:</strong> {{ $exportedAt }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Country</th>
                <th>Code</th>
                <th>Continent</th>
                <th>Region</th>
                <th>Population</th>
                <th>Capital City</th>
                <th>Life Expectancy</th>
                <th>Surface Area (km²)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($countries as $country)
                <tr>
                    <td><strong>{{ $country->Name }}</strong></td>
                    <td>{{ $country->Code }}</td>
                    <td>{{ $country->Continent }}</td>
                    <td>{{ $country->Region }}</td>
                    <td>{{ number_format($country->Population) }}</td>
                    <td>{{ $country->capitalCity?->Name ?? 'N/A' }}</td>
                    <td>{{ $country->LifeExpectancy ?? 'N/A' }}</td>
                    <td>{{ number_format($country->SurfaceArea) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No countries found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated from the Country Index database.</p>
    </div>
</body>
</html>
