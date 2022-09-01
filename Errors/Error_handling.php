<?php

//error_reporting(0); -- no error reporting
//error_reporting(E_ALL); --reporting all error including warnings
//error_reporting(E_ALL & ~E_WARNING); --reporting all error and  warnings
// ini_set('display_errors', 0);// this line disable errors to be shown//-- recommended to set 0 to not display sensitive message
var_dump(ini_get('display_errors'));

/**
 * manully trigger errors
 */
/*
trigger_error("This is a custom Deprecated msg", E_USER_DEPRECATED);
trigger_error("This is a custom Warning msg", E_USER_WARNING);
trigger_error("This is a custom Notice msg", E_USER_NOTICE);
*/
// this line stop executing as php encounters a E_USER_ERROR
//trigger_error("This is a custom Error msg", E_USER_ERROR); 

// making an error function
function erroHandler(int $type, string $msg,  ?string $file = null, ?int $line = null)
{
    echo $type. ': '. $msg. ' in '. $file. ' on line '. $line;
    // return; //-->return  something like true to contune with the script executing or false to fall back to php standard error handling
    // this might be used in different situating like different error types 
    // exit;//-->if you want to stop the php from execution when error encounted is fatal or importanr errors
    // return false;

}

// error_reporting(E_ALL & ~E_WARNING);

// register error function
set_error_handler('erroHandler', E_ALL);// this line of code overrides the error_reporting 


//custom error handers do dot handle all type of error like(syntax error,etc..)
echo $x;

//can restore previous error_handler by calling the restore_error_handler function function

restore_error_handler();

echo $y;
