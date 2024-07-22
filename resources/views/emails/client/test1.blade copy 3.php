<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Document</title>

    <!-- <link rel="stylesheet" href="./assets/bootstrap.min.css"> -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet"> -->

    <style>
    /* body {
        margin: 5px;
        font-family: "Roboto Condensed", sans-serif;
    } */

    p,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-family: "Roboto Condensed", sans-serif;
    }

    .outerDiv {
        border: 1px solid rgb(233, 233, 233);
        /* padding:5px; */
    }

    .box_1 {
        width: 40.33%;
        padding: 10px 3px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        /* justify-content: center; */
    }

    .box_1:nth-child(2) {
        width: 50.33%;
        padding: 10px 24px;
    }

    .box_1:nth-child(3) {
        /* text-align: center; */
        align-items: center;
        justify-content: center;
    }

    .header {
        /* display: flex !important;
        justify-content: space-between;
        align-items: center; */
        display: block;
        background-color: rgba(20, 214, 240, 0.014);
        /* width:100%; */
        border-bottom: 1px solid rgb(233, 233, 233);
        padding: 5px;
    }

    .title {
        float: left;
    }

    .title>p {
        margin: 0px;
        font-size: 12px !important;
    }

    .rightContent {
        display: flex;
        align-items: center;
        justify-content: end;
        font-size: 12px;
        margin: 0px 0px 0px 30px;
    }

    .rightContent {
        float: right;
        /* width: 200px; */
    }

    .rightContent>p {
        font-weight: bold;
        margin: 0px;
    }

    .body_client_dtls {
        padding: 0px 3px;
        display: flex;
        /* align-items: center; */
        justify-content: space-between;
    }

    .body_client_dtls>.box_1>h4,
    .body_client_dtls>.box_1>h5 {
        margin: 0px;
    }

    .body_client_dtls>.box_1>div>h5 {
        /* margin:5px 0px!important; */
        margin: 2px 0px !important;
    }

    .body_client_dtls>.box_1>div>h5>span.box_value,
    .body_client_dtls>.box_1>div>h5>a.box_value {
        font-weight: 400 !important;
        font-size: 13px;
        color: rgb(77, 77, 77);
    }

    .body_client_dtls>.box_1>div>h5>a.box_value {
        text-decoration: underline;
        color: rgb(51 174 190);
    }

    .address {
        width: 100px;
    }
    </style>

</head>

<body>
    <div class="outerDiv">
        <div class="header">
            <div class="title">
                <p>Valuation Report As On Date - <b>June 22, 2024 </b></p>
            </div>
            <div class="rightContent">
                <p class="fw-bold m-0">Current Sensex : <span>20,410</span>
                    <img src="https://t3.ftcdn.net/jpg/03/30/10/14/360_F_330101415_Wh3Rrp25iXFkKMj2UjOXiERNUEpgoVNA.jpg"
                        height="20" width="20" />
                    <span>
                        12,541 (0.24%)
                    </span>
                </p>
                <!-- <img src="https://t3.ftcdn.net/jpg/03/30/10/14/360_F_330101415_Wh3Rrp25iXFkKMj2UjOXiERNUEpgoVNA.jpg"
                    height="20" width="20" />
                <p class="m-0">12,541 (0.24%)</p> -->
            </div>
        </div>
        <!-- <div class="body_client_dtls">
            <div class="box_1">
                <h4>Soumitra Chakrabarty</h4>
                <h5>PAN:(ABCDE12345L)</h5>
                <div style="margin: 5px 0px;">
                    <h5>Address: <span class="box_value address">
                            1A, Dr.Sarat Banerjee Rd, Hemanta Mukherjee Sarani, lake Terrace, Kalighat, Kolkata, West
                            Bengal 700029
                        </span></h5>
                    <h5>Mobile: <span class="box_value">8777528909</span></h5>
                    <h5>Email: <span class="box_value">soumitrachakrabarty@gmail.com</span></h5>
                    <h5>Dob: <span class="box_value">31/07/1996</span></h5>
                </div>

            </div>
            <div class="box_1">
                <h4>Nuedge Corporate Pvt Ltd</h4>
                <h5>AMFI Registered Mutual Fund Distributor</h5>
                <div style="margin: 5px 0px;">
                    <h5>Address: <span class="box_value">
                            1A, Dr.Sarat Banerjee Rd, Hemanta Mukherjee Sarani, lake Terrace, Kalighat, Kolkata, West
                            Bengal 700029
                        </span></h5>
                    <h5>Contact: <span class="box_value">03346026618</span></h5>
                    <h5>Email: <span class="box_value">support@nuedgecorporate.com</span></h5>
                    <h5>Website: <a class="box_value">nuedgecorporate.co.in</a></h5>
                </div>
            </div>
            <div class="box_1">
                <img style="object-fit: contain;" src="https://www.nuedgecorporate.com/images/logo.png" width="180" />
            </div>
        </div> -->
    </div>
</body>

</html>