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
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script>
            var get_timeout = 0;
            var myGetPath;
            var points = [
                ["22.372081", "114.107877"],
                ["22.314419", "114.199510"],
                ["22.276442", "114.177811"]
            ];
            $(document).ready(function () {
                var st = buildString(points);
                $("#path-wrapper").html(st);

                $("button.token-button").click(function () {
                    getToken();
                });
            });

            function buildString(pointarray) {
                var st = '';
                for (var i = 0; i < pointarray.length; i++) {
                    var pav = pointarray[i];

                    if ($.isArray(pav)) {
                        pav = pointarray[i][0] + ',' + pointarray[i][1];
                        console.log(pav);
                    }
                    st += '[' + pav + ']</br>';
                }

                return st;
            }
            function getToken() {
                console.log(points);
                get_timeout = 0;
                $.ajax({
                    url: '/route',
                    type: 'post',
                    dataType: 'json',
                    data: {points: points},
                    success: function (data) {
                        console.log("Data: " + JSON.stringify(data));
                        $("#token-div").html(data.token);
                        var token_wrapper = $('#token-wrapper');
                        token_wrapper.html('');
                        var result_wrapper = $('#result-wrapper');
                        result_wrapper.html('');
                        for (var ckey in data) {
                            token_wrapper.append('<p class="key"><b>' + ckey + ":</b><br>" + data[ckey] + '</p>');
                            if (ckey == 'token') {
                                getPath(data[ckey]);
                            }
                        }
                    }
                });

            }
            function getPath(key) {
                clearTimeout(myGetPath);
                $.ajax({
                    url: "/route/" + key,

                }).done(function (data) {

                    var result_wrapper = $('#result-wrapper');
                    result_wrapper.html('');
                    var keyvalue = '';
                    for (var ckey in data) {
                        keyvalue = data[ckey];
                        if (ckey == 'path') {
                            keyvalue = buildString(data[ckey]);
                        }
                        result_wrapper.append('<p class="key"><b>' + ckey + ":</b><br>" + keyvalue + '</p>');
                    }
                    if (data['status'] == "in progress" && get_timeout < 60000) {
                        myGetPath = setTimeout(function () {
                            getPath(key);
                            get_timeout += 2000;
                        }, 2000);
                    }
                });
            }
        </script>
    </head>
    <body>
        <div class="main-container left">
            <div><p><b>points</b></p><span id="path-wrapper"></span></div>
            <div id="sumbit-div">
                <button class="token-button">Get Result</button>
            </div>
            <div id="token-wrapper"></div>
            <div id="result-wrapper"></div>
        </div>
    </body>
</html>
