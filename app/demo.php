<?php
include("test/testCase.php");
?>

<html>
    <head>
        <title>
            Backend Challenge
        </title>
        <style>
            html, body {
                font-family :  Arial, sans-serif;
                font-size : 14px;
            }

            div{            
                padding:10px;
            }

            .testcase_message{
                color: #4b6d4e;
            }
            .data_message{
                color: #4e78af;
            }
            .rd{
                border:1px solid;
                padding:10px;
                margin:10px;
            }

            .incorrect{
                background: #fff4f4;
            }

            .correct{
                background: #ebf7f5;
            }
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    </head>
    <body>
        <div id="sumbit-div">
            <p><b>Test Case Result</b></p>
            <button class="test-button">Auto Run Test</button>
        </div>
        <div class="result-container">

        </div>

    </body>

    <script>
        var testcase = <?php echo json_encode($testcase); ?>;
        var get_timeout = [];
        var myGetPath = [];

        $(document).ready(function () {
            $("button.test-button").click(function () {
                runTestCase();
                $(this).attr('disabled', 'disabled');

            });
        });

        function runTestCase() {
            for (var i = 0; i < testcase.length; i++) {
                console.log(testcase[i]['points']);
                getToken(testcase[i]['points'], i);
            }
        }

        function buildString(pointarray) {
            var st = '';
            for (var i = 0; i < pointarray.length; i++) {
                var pav = pointarray[i];

                if ($.isArray(pav) && pav.length == 2) {
                    pav = pointarray[i][0] + ',' + pointarray[i][1];
                    console.log(pav);
                }
                st += '[' + pav + ']</br>';
            }

            return st;
        }
        function getToken(points, array_id) {

            $.ajax({
                url: '/route',
                type: 'post',
                dataType: 'json',
                data: {points: points},
                success: function (data) {
                    console.log("Data: " + JSON.stringify(data));
                    var result_container = $('.result-container');
                    result_container.append('<div class="rd" id="rd-' + array_id + '"></div>');
                    var rd = $("#rd-" + array_id);
                    rd.append('<p class="key"><b>points:</b><br>' + buildString(points) + '</p>');
                    rd.append('<div class="rr"></div>');
                    var rr = $("#rd-" + array_id + ' .rr');
                    for (var ckey in data) {
                        rr.append('<p class="key"><b>' + ckey + ":</b><br>" + data[ckey] + '</p>');
                        if (ckey == 'token') {
                            var tk = data[ckey];
                            rd.addClass("t-" + tk);
                            rd.append('<div class="rv-' + tk + '"></div>');
                            get_timeout[tk] = 0;
                            getPath(tk, array_id);
                        } else {
                            checkTestCase(array_id, data);
                        }
                    }
//                  
                }
            }
            );

        }
        function getPath(key, array_id) {
            console.log(get_timeout[key]);
            if (get_timeout[key] > 60000) {
                rd.css("display", "none");
                clearTimeout(myGetPath[key]);
            }

            var rv = $(".rv-" + key);
            $.ajax({
                url: "/route/" + key,
            }).done(function (data) {
                console.log(data);
                rv.html('');
                for (var ckey in data) {
                    keyvalue = data[ckey];
                    if (ckey == 'path') {
                        keyvalue = buildString(data[ckey]);
                    }
                    rv.append('<p class="key"><b>' + ckey + ":</b><br>" + keyvalue + '</p>');
                }
                if (data['status'] == "in progress") {
                    myGetPath[key] = setTimeout(function () {
                        getPath(key, array_id);
                        get_timeout[key] += 2000;
                    }, 2000);
                } else {
                    checkTestCase(array_id, data);
                }


            });
        }

        function checkTestCase(array_id, data) {
            var bg = 'correct';
            var rd = $("#rd-" + array_id);
            var tc = testcase[array_id];
            $.each(tc, function (index, value) {
                if (index != 'points') {
                    rd.append('<p class="testcase_message">TC [' + index + '] : ' + value + '</p>');
                    if (data[index] && data[index] == value) {
                        rd.append('<p class="data_message">DT [' + index + '] : ' + data[index] + '</p>');
                    } else {
                        rd.append('<p class="data_message">Failed : ' + index + ' Not Match</p>');
                        bg = 'incorrect';
                    }
                }
            });
            $('#rd-' + array_id).addClass(bg);

        }
    </script>
</html>
