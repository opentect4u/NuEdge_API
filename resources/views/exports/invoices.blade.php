

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div>
        <div> 
            <h3>Table </h3>
            {{$image}}
        </div>
        <div>
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                @foreach($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->name }}</td>
                        <td>{{ $invoice->email }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>