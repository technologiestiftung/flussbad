<?php

spl_autoload_register(function($className) {
//	dumpVar($className,'$className');
//	dumpVar(ROOT_DIR, 'ROOT_DIR');
    $namespace=str_replace("\\","/",__NAMESPACE__);
    $className=str_replace("\\","/",$className);
    $class=ROOT_DIR . "class/{$className}.class.php";
    
    if ( is_file($class) ) {
		dumpVar($class,'autoloader: $class');
		require_once($class);
	}
    //include_once($class);
    echo '<hr/>';
});
