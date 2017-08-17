<?php

function dumpVar( $var, $name, $color = null ) {
	$trace = debug_backtrace();
	$line = $trace[0]['line'];
	$file = $trace[0]['file'];

	$bgColor="#3d83ef";
	if ( !is_null( $color ) ) {
		$bgColor=$color;
	}

	$style = "style='margin: .5em 0;padding: .5em;background-color:" . $bgColor .';\'';

	echo("<div $style>");
	echo('<p style=\'margin: .3em 0;\'><b>file: </b>' . $file .' <b>on line: </b>'. $line . '</p>');
	echo('<p style=\'font-size: 115%;border-bottom: 3px solid black;margin:0;\'>' . $name . ': </p>');
	echo('<pre style=\'margin: 0 2em;border-left: 1px solid black;padding: 1em;\'>');
	var_dump( $var );
	echo('</pre>');

	/*
	echo('<div><pre>');
	var_dump($trace);
	echo('</pre></div>');
	*/

	/*
	echo('<div>');
	echo('<p>stack trace:</p>');
	foreach ( $trace as $entry ) {
		if ( isset($entry['file']) && isset($entry['line']) ) {
			echo('<div>');
			echo('<p>');

			echo('file: ');
			echo('<span>');
			echo($entry['file']);
			echo('</span>');

			echo('on line: ');
			echo('<span style=\'margin-left: .3em\'>');
			echo($entry['line']);
			echo('</span>');

			echo('function: ');
			echo('<span style=\'margin-left: .3em\'>');
			echo($entry['function']);
			echo('</span>');

			if ( isset($entry['class']) ) {
				echo('of class: ');
				echo('<span>');
				echo($entry['class']);
				echo('</span>');
			}
			echo('</p></div>');
		}
	}
	echo('</div>');
	*/


	echo('</div>');

}


/**
* Checks for a fatal error, work around for set_error_handler not working on fatal errors.
*/
function check_for_fatal()
{
    $error = error_get_last();
    if ( !is_null($error) ) {
		dumpVar($error, '$error','#ff4545');

		$errorString = "";

		switch ( $error['type'] ) {
		case E_ERROR:				$errorString = "E_ERROR"; break;
		case E_WARNING:				$errorString = "E_WARNING"; break;
		case E_PARSE:				$errorString = "Parse error"; break;
		case E_NOTICE:				$errorString = "E_NOTICE"; break;
		case E_CORE_ERROR:			$errorString = "E_CORE_ERROR"; break;
		case E_CORE_WARNING:		$errorString = "E_CORE_WARNING"; break;
		case E_COMPILE_ERROR:		$errorString = "E_COMPILE_ERROR"; break;
		case E_COMPILE_WARNING:		$errorString = "E_COMPILE_WARNING"; break;
		case E_USER_ERROR:			$errorString = "E_USER_ERROR"; break;
		case E_USER_WARNING:		$errorString = "E_USER_WARNING"; break;
		case E_USER_NOTICE:			$errorString = "E_USER_NOTICE"; break;
		case E_STRICT:				$errorString = "E_STRICT"; break;
		case E_RECOVERABLE_ERROR:	$errorString = "E_RECOVERABLE_ERROR"; break;
		case E_DEPRECATED:			$errorString = "E_DEPRECATED"; break;
		case E_USER_DEPRECATED:		$errorString = "E_USER_DEPRECATED"; break;
		case E_ALL:					$errorString = "E_ALL"; break;
		}

		echo('<div style=\'background-color: #ff4545; padding:.2em 1em;\'>');
		echo("<p style='border-bottom: 2px solid #000;'><b>{$errorString}:</b></p>");
		echo('<p style=\'margin:.1em .17em .7em .17em;padding-left: 3em;\'>' . $error["message"] . '</p>');
		echo('<p style=\'margin:.1em .17em;\'> in <b>' . $error["file"] . '</b></p>');
		echo('<p style=\'margin:.1em .17em;\'> on line <b>' . $error["line"] . '</b></p>');
		echo('</div>');

		//exit();
	}
}
register_shutdown_function( "check_for_fatal" );

function exception_handler($exception) {
	dumpVar($exception,'$exception', '#ff4545');
	echo '<div class="alert alert-danger">';
	echo '<b>Fatal error</b>:  Uncaught exception \'' . get_class($exception) . '\' with message ';
	echo $exception->getMessage() . '<br>';
	echo 'Stack trace:<pre>' . $exception->getTraceAsString() . '</pre>';
	echo 'thrown in <b>' . $exception->getFile() . '</b> on line <b>' . $exception->getLine() . '</b><br>';
	echo '</div>';
}
set_exception_handler('exception_handler');

ini_set( "display_errors", "off" );
error_reporting( E_ALL );


