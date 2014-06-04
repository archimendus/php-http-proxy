<?php
/**
 * include/config.dist.php is base config file.
 * Please copy include/config.dist.php to include/config.php and make
 * any needed changes. Do not edit this file.
 */

# this has to be set to string of your choice. This is the password that will be
# required. To generate a simple MD5 password hash, call
# crypt('passwordOfChoice', '$1$saltOfChoice$') and use the value returned here.
$conf['expectedPasswordHash'] = null;

# require a password to be provided when making proxy requests, to not allow just
# anyone to use this service.
$conf['requirePassword'] = true;

# Where debug logs should go. Off by default.
$conf['debugLogFile'] = null;

/**
 * Array of callables accepting {@link Amcsi_HttpProxy_ContentFilterData} and
 * as a parameter, and is expected to return:
 *  1) A string if the response content should be changed to that.
 *  2) NULL, if the response should not be modified
 */
$conf['contentFilters'] = array();
