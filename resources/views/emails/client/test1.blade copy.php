<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample HTML</title>
    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" type="text/css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet"> -->
    <style>
    html {
        -webkit-print-color-adjust: exact;
    }
    </style>
</head>

<body>

    <div class='border w-full mx-2 my-2'>
        <div class="border-bottom w-full d-flex flex-row justify-content-between align-items-center p-2"
            style="background-color: rgba(20, 214, 240, 0.014);">
            <p class="m-0" style="font-size:12px!important;">Valuation Report As On Date - <b>June 22, 2024 </b></p>
            <div class="d-flex align-items-center justify-content-end"
                style="font-size:12px!important;margin:0px 0px 0px 30px!important">
                <p class="fw-bold m-0">Current Sensex - </p><span>20,410</span>
                <img src="https://t3.ftcdn.net/jpg/03/30/10/14/360_F_330101415_Wh3Rrp25iXFkKMj2UjOXiERNUEpgoVNA.jpg"
                    height="20" width="20" />
                <p class="m-0">12,541 (0.24%)</p>
            </div>
        </div>
        <div class="row m-0 border-bottom">
            <div class="col-4 p-2">
                <p class="fw-bold m-0" style="font-size:14px;">SUMAN MITRA</p>
                <p class="fw-bold m-0" style="font-size:12px;">(PAN:ABCDE1234L)</p>
                <p class="m-0" style="font-size:12px;"><b>Address</b> : <span class="text-uppercase">12 A Kabi Bharati
                        Sarani, Lake Road,Kolkata-700029</span></p>
                <p class="m-0" style="font-size:12px;"><b>Mob</b> : <span>8777528909</span></p>
                <p class="m-0" style="font-size:12px;"><b>Email</b> : <span>sumanmitra0096@gmail.com</span></p>
                <p class="m-0" style="font-size:12px;"><b>DOB</b> : <span>31-07-1996</span></p>
            </div>
            <div class="col-4 p-2">
                <p class="fw-bold m-0" style="font-size:14px">NuEdge Corporate Private Limited</p>
                <p class="fw-bold m-0" style="font-size:12px">AMFI Registered Mutual Fund Distributor</p>
                <p class="m-0" style="font-size:12px"><b>Address</b> : <span class="text-uppercase">12 A Kabi Bharati
                        Sarani, Lake Road,Kolkata-700029</span></p>
                <p class="m-0" style="font-size:12px"><b>Tel</b> : <span>033 4602 6618</span></p>
                <p class="m-0" style="font-size:12px"><b>Email</b> : <span>support@nuedgecorporate.com</span></p>
                <p class="m-0" style="font-size:12px"><b>Website</b> : <a href="https://nuedgecorporate.co.in"
                        target="_blank">nuedgecorporate.co.in</a></p>
            </div>
            <div class="col-4 p-2 d-flex justify-content-center align-items-center">
                <img src="https://www.nuedgecorporate.com/images/logo.png" height="60" width="150" />
            </div>

        </div>
        <div class="p-2">
            <p class="fw-bold m-0" style="font-size:14px">Mutual Fund Summary Report</p>
            <!-- class="rounded-1 content d-flex justify-content-between align-items-center p-2 mt-1"  -->
            <div style="background-color: rgba(236, 236, 236, 0.5);"
                class="rounded-1 content d-flex justify-content-between align-items-center p-2 mt-1">

                <div style="font-size:12px;">
                    <p class="m-0">Investment Amount</p>
                    <b>7,20,50102.00</b>
                </div>
                <div style="font-size:12px;">
                    <p class="m-0">Current Amount</p>
                    <b>8,20,50102.00</b>
                </div>
                <div style="font-size:12px">
                    <p class="m-0">Divident Reinvestment</p>
                    <b>0.00</b>
                </div>
                <div style="font-size:12px">
                    <p class="m-0">Divident Payout</p>
                    <b>0.00</b>
                </div>
                <div style="font-size:12px">
                    <p class="m-0">Absolute Return</p>
                    <b>32.21%</b>
                </div>
                <div style="font-size:12px">
                    <p class="m-0">XIRR</p>
                    <b>22.21%</b>
                </div>

            </div>
        </div>
        <div class="p-2">
            <table style="font-family: arial, sans-serif;border-collapse: collapse;width: 100%;">
                <thead>
                    <tr style="background-color: #0a6494;color:#fff">
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:20%!important;font-size:11px;font-weight:500">
                            Scheme</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:6%!important;font-size:11px;font-weight:500">
                            Inv. Since</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:5%!important;font-size:11px;font-weight:500">
                            SENSEX</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:5%!important;font-size:11px;font-weight:500">
                            NIFTY50</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:4%!important;font-size:11px;font-weight:500">
                            Inv. Cost</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:4%!important;font-size:11px;font-weight:500">
                            IDCWR</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:5%!important;font-size:11px;font-weight:500">
                            Pur. NAV</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:6%!important;font-size:11px;font-weight:500">
                            Units</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;font-size:11px;font-weight:500">
                            NAV Date</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:5%!important;font-size:11px;font-weight:500">
                            Curr.NAV</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:20px;font-size:11px;font-weight:500">
                            Curr. Value</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:20px;font-size:11px;font-weight:500">
                            IDCW Reinv.</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:20px;font-size:11px;font-weight:500">
                            IDCWP</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:20px;font-size:11px;font-weight:500">
                            Gain/Loss</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:20px;font-size:11px;font-weight:500">
                            Ret.ABS</th>
                        <th
                            style="border: 1px solid #0a649471;text-align: center;padding: 2px;width:20px;font-size:11px;font-weight:500">
                            XIRR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td
                            style="border: 1px solid #dddddd;text-align: left!important;padding: 2px;width:20%!important;font-size:9px;">
                            <b>HSBC Multicap Fund-Regular-Growth</b>
                            <p style="margin:2px 0px;">Folio - <b>123123342/35</b></p>
                            <p class="m-0">ISIN - <b>INI1234632</b></p>

                        </td>
                        <td
                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;width:6%!important;font-size:9px">
                            12-09-24</td>
                        <td
                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;width:5%!important;font-size:9px">
                            12201</td>
                        <td
                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;width:5%!important;font-size:9px">
                            12201</td>
                        <td
                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;width:4%!important;font-size:9px">
                            12201</td>
                        <td
                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;width:4%!important;font-size:9px">
                            0.00</td>
                        <td
                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;width:5%!important;font-size:9px">
                            5201</td>
                        <td
                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;width:6%!important;font-size:9px">
                            55201.0000</td>
                        <td style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                            12-09-24</td>
                        <td
                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;width:5%!important;font-size:9px">
                            52091.12</td>
                        <td style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">52091.12
                        </td>
                        <td style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">0.00</td>
                        <td style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">0.00</td>
                        <td style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">52091.12
                        </td>
                        <td style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">52.12%</td>
                        <td style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">52.12%</td>
                    </tr>
                    <tr>
                        <td colspan="16" class="p-1 border" style="background-color:rgb(247, 247, 247)">
                            <table style="width:100%!important">
                                <thead>
                                    <tr style="background-color: #0284c7;color:#fff">
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Sl No</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Trans Type</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Trans Date</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Gross Amount</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            S. Duty</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            TDS</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Net Amt</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            IDCWR</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Pur. NAV</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Units</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Cumml.Unit</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            SENSEX</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Nifty50</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Curr.NAV</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Curr. Value</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            IDCW Reinv</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            IDCWP</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Gain/Loss</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Days</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Ret.ABS</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            XIRR</th>
                                        <th
                                            style="border: 1px solid #0285c7ab;text-align: center;padding: 2px;font-size:9px">
                                            Trans. Mode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            2</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>

                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            3</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            SIP Purchase</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            12-02-2014</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.25</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            120125</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1254.1204</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            0.00</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            15451</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            1258</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            21.25%</td>
                                        <td
                                            style="border: 1px solid #dddddd;text-align: center;padding: 2px;font-size:9px">
                                            N</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-2">
            <p class="m-0" style="font-size:9px!important">
                Mutual Fund investments are subject to market risks, read all scheme related documents carefully. The
                NAVs of the schemes may go up or down depending upon the factors and forces affecting the securities
                market including the fluctuations in the interest rates. The past performance of the mutual funds is not
                necessarily indicative of future performance of the schemes. The Mutual Fund is not guaranteeing or
                assuring any dividend under any of the schemes and the same is subject to the availability and adequacy
                of distributable surplus. Investors are requested to review the prospectus carefully and obtain expert
                professional advice with regard to specific legal, tax and financial implications of the
                investment/participation in the scheme.
            </p>
        </div>
    </div>

</body>

</html>