<?php

spl_autoload_register(function($className) {
//	dumpVar($className,'$className');
//	dumpVar(ROOT_DIR, 'ROOT_DIR');
	$namespace=str_replace("\\","/",__NAMESPACE__);
	$className=str_replace("\\","/",$className);
	$class=ROOT_DIR . "class/{$className}.class.php";


	$root=ROOT_DIR . "./{$className}.class.php";
	if ( is_file($root) ) {
		dumpVar($root,'autoloader: $class');
		require_once($root);
		return;
	} else if ( is_file($class) ) {
		dumpVar($class,'autoloader: $class');
		require_once($class);
		return;
	}

	$tests=ROOT_DIR . "tests/{$className}.class.php";
	if ( is_file($tests) ) {
		dumpVar($tests,'autoloader: $class');
		require_once($tests);
		return;
	} else if ( is_file($class) ) {
		dumpVar($class,'autoloader: $class');
		require_once($class);
		return;
	}

	//include_once($class);
	echo '<hr/>';
});
