<?php
/**
 * Created by: tholforty
 * Purpose: Tail a log file in the browser
 * Date: 10/9/2018
 * Time: 8:11 AM
 */

session_start();

$log = '/path/to/log/error_log';

if ( isset( $_GET['ajax'] ) ) {
	$handle = fopen( $log, 'rb' );

	if ( isset( $_SESSION['offset'] ) ) {
		// check if log was reset
	    fseek( $handle, 0, SEEK_END );
		$test_offset = ftell( $handle );

		if ($test_offset < $_SESSION['offset']) {
			// Rewind the session offset if log was reset
		    $_SESSION['offset'] = $test_offset;
			echo '<br>== log reset ==<br>';
        } else {
			// Collect new log entries and set new session log offset
			$data               = stream_get_contents( $handle, - 1, $_SESSION['offset'] );
			$_SESSION['offset'] = ftell( $handle );
			echo nl2br( $data );
		}

		//error_log( $_SESSION['offset'] . "\n", 3, $log );
	} else {
		// new session so start session log offset
	    fseek( $handle, 0, SEEK_END );
		$_SESSION['offset'] = ftell( $handle );
	}

	fclose( $handle );
	exit(1);

} elseif ( isset( $_GET['reset'] ) ) {
	file_put_contents($log, "Log reset...\n");
	session_destroy();
	session_start();
	echo '<br>== log reset ==<br>';
	exit(1);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://code.jquery.com/jquery-1.8.2.min.js"></script>
    <script>
        let delay = 1000;
        let tid = setTimeout(repeater, delay);
        $( document ).ready(function() {
            $.ajax({
                crossDomain: true
            });
        });

        function repeater() {
            let d = new Date();
            $.get('<?php echo basename( __file__ ); ?>?ajax&' + d.getTime(), function (data) {
                $('#tail').append(data);
                window.scrollTo(0, document.body.scrollHeight);
                //console.log(data);
            });
            tid = setTimeout(repeater, delay);
        }

        function reset() {
            let d = new Date();
            $.get('<?php echo basename( __file__ ); ?>?reset&' + d.getTime(), function (data) {
                $('#tail').append(data);
                window.scrollTo(0, document.body.scrollHeight);
                //console.log("reset log...\n");
            });
        }
    </script>
    <style>
        .log-link-container {
            position: fixed;
            top: 20px;
            right: 100px;
        }

        .log_link {
            text-decoration: none;
            color: #0a78d1;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="log-link-container"><a href="javascript: reset();" class="log_link">reset</a><br>
    <a href="error_log" target="_blank" class="log_link">view log</a></div>
<div id="tail">Starting up...<br></div>
</body>
</html>
