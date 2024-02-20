<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js" ></script>
</head>

<body>
    <h3>ucssgyafgy</h3>
    <script>
    // Copyright (c) 2012 Sutoiku, Inc. (MIT License)

    // Some algorithms have been ported from Apache OpenOffice:

    /**************************************************************
     * 
     * Licensed to the Apache Software Foundation (ASF) under one
     * or more contributor license agreements.  See the NOTICE file
     * distributed with this work for additional information
     * regarding copyright ownership.  The ASF licenses this file
     * to you under the Apache License, Version 2.0 (the
     * "License"); you may not use this file except in compliance
     * with the License.  You may obtain a copy of the License at
     * 
     *   http://www.apache.org/licenses/LICENSE-2.0
     * 
     * Unless required by applicable law or agreed to in writing,
     * software distributed under the License is distributed on an
     * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
     * KIND, either express or implied.  See the License for the
     * specific language governing permissions and limitations
     * under the License.
     * 
     *************************************************************/

    function XIRR(values, dates, guess) {
        // Credits: algorithm inspired by Apache OpenOffice

        // Calculates the resulting amount
        var irrResult = function(values, dates, rate) {
            var r = rate + 1;
            var result = values[0];
            for (var i = 1; i < values.length; i++) {
                result += values[i] / Math.pow(r, moment(dates[i]).diff(moment(dates[0]), 'days') / 365);
            }
            return result;
        }

        // Calculates the first derivation
        var irrResultDeriv = function(values, dates, rate) {
            var r = rate + 1;
            var result = 0;
            for (var i = 1; i < values.length; i++) {
                var frac = moment(dates[i]).diff(moment(dates[0]), 'days') / 365;
                result -= frac * values[i] / Math.pow(r, frac + 1);
            }
            return result;
        }

        // Check that values contains at least one positive value and one negative value
        var positive = false;
        var negative = false;
        for (var i = 0; i < values.length; i++) {
            if (values[i] > 0) positive = true;
            if (values[i] < 0) negative = true;
        }

        // Return error if values does not contain at least one positive value and one negative value
        if (!positive || !negative) return '#NUM!';

        // Initialize guess and resultRate
        var guess = (typeof guess === 'undefined') ? 0.1 : guess;
        var resultRate = guess;

        // Set maximum epsilon for end of iteration
        var epsMax = 1e-10;

        // Set maximum number of iterations
        var iterMax = 50;

        // Implement Newton's method
        var newRate, epsRate, resultValue;
        var iteration = 0;
        var contLoop = true;
        do {
            resultValue = irrResult(values, dates, resultRate);
            newRate = resultRate - resultValue / irrResultDeriv(values, dates, resultRate);
            epsRate = Math.abs(newRate - resultRate);
            resultRate = newRate;
            contLoop = (epsRate > epsMax) && (Math.abs(resultValue) > epsMax);
        } while (contLoop && (++iteration < iterMax));

        if (contLoop) return '#NUM!';

        // Return internal rate of return
        return resultRate;
    }
    </script>


<script>

const values = [
  -1.00250692,
  -100.250692,
  -1.00250692,
  -100.250692,
  53.49105162911925,
  32.55768506535615
];
const dates = [
  "2021-07-09",
  "2021-07-09",
  "2021-07-09",
  "2021-07-09",
  "2022-02-25",
  "2022-02-25"
];

const values = [
    -4999.75,
    -4999.75,
    10246.82,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    65905.17,
    -4999.75,
    -4999.75,
    -4999.75,
    14672.61,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    -4999.75,
    20934.67,
    -4999.75,
    4846.07,
    -4999.75,
    4884,
    -4999.75,
    -4999.75,
    52307
];






var value=XIRR(values, dates);
alert(value);
</script>
</body>

</html>