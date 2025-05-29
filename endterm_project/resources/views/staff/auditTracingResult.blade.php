<!DOCTYPE html>
<html>
<head>
    <title>Outgoing Transferees Report</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 50px;
        }
        .center {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #e0e0e0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }
        .col {
            width: 32%;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="center">
        <h3>Republic of the Philippines<br>PANGASINAN STATE UNIVERSITY<br>URDANETA CAMPUS</h3>
        <h4>OUTGOING TRANSFEREES REPORT<br>OFFICE OF THE REGISTRAR</h4>
    </div>
    
    <div class="center">
        <p><strong>{{ $semester }} of {{ $schoolyear }}</strong><br>
        As of {{ $date_from }} to {{ $date_to }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Program</th>
                <th>Graduate</th>
                <th>Undergraduate</th>
                <th>Total</th>
                @if ($includeGenders)
                    <th>Male</th>
                    <th>Female</th>
                    <th>Other</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $program => $values)
                <tr>
                    <td>{{ strtoupper($program) }}</td>
                    <td>{{ $values['graduate'] }}</td>
                    <td>{{ $values['undergraduate'] }}</td>
                    <td>{{ $values['graduate'] + $values['undergraduate'] }}</td>
                    @if ($includeGenders)
                        <td>{{ $values['male'] }}</td>
                        <td>{{ $values['female'] }}</td>
                        <td>{{ $values['other'] }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th>{{ $totals['graduate'] }}</th>
                <th>{{ $totals['undergraduate'] }}</th>
                <th>{{ $totals['graduate'] + $totals['undergraduate'] }}</th>
                @if ($includeGenders)
                    <th>{{ $totals['male'] }}</th>
                    <th>{{ $totals['female'] }}</th>
                    <th>{{ $totals['other'] }}</th>
                @endif
            </tr>
        </tfoot>
    </table>

    <table style="width: 100%; text-align: center; border-collapse: collapse; border: none;">
    <tr>
        <td style="width: 33%; border: none;">
        <strong>Prepared by:</strong><br>HANNA MIKAELA B. GARCES<br>Clerk
        </td>
        <td style="width: 33%; border: none;">
        <strong>Checked by:</strong><br>MARICEL A. BONGOLAN, MIT<br>Registrar I
        </td>
        <td style="width: 33%; border: none;">
        <strong>Noted by:</strong><br>ROY C. FERRER, Ph.D.<br>Campus Executive Director
        </td>
    </tr>
    </table>

</body>
</html>
