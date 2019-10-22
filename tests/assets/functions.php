<?php
//This comment is not related with any function

fn1('arg1', 'arg2', 3);
fn2($var);
fn3(fn4('arg4'), 'arg5', fn5(6, 7.5));
fn6(['arr']);
fn7(CONSTANT_1);
// fn_8();
/* ALLOW: This is a comment to fn9 */
fn9(ARG_8);

/* Comment to fn10 */ fn10();

//Related comment 1
fn11(/* ALLOW: Related comment 2 */ 'arg9', 'arg10' /* Related comment 3 */);

/* Related comment 
number one */
fn12(
    /* Related comment 2 */
    'arg11',
    /* ALLOW: Related comment 3 */
    'arg12'
    /* Related comment 4 */
); //Related comment 5 (this cannot be detected currently)
