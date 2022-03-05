<?php

function debug()
{
    foreach (func_get_args() as $data) {
        echo "\n<pre>\n";
        print_r($data);
        echo "\n</pre>\n";
    }
}