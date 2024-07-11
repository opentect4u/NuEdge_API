<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div id="my_print">
        <h5>Test </h5>

        <table border="1">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>name</td>
                    <td>email</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Chitta</td>
                    <td>cmaity@gmail.com</td>
                </tr>

            </tbody>
        </table>
    </div>



    <button onclick="printContent('my_print');">Print</button>

    <!-- <input type="button" id="bt" onclick="print()" value="Print PDF" /> -->
    <!-- <input type="button" id="bt" onclick="printContent()" value="Print PDF" /> -->

    <script>
    function printContent(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
    </script>
</body>

</html>