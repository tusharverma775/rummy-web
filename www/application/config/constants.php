<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to false, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', false);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  or define('DIR_WRITE_MODE', 0755);

/******************API ERROR CODES **************************/
define('HTTP_OK', 200);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_METHOD_NOT_ALLOWED', 405);
define('HTTP_BLANK', 407);
define('HTTP_INVALID', 411);
define('HTTP_NOT_ACCEPTABLE', 406);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
define('PROJECT_NAME', 'Lets Card');
define('COMPANY_NAME', 'ASAG Androapps Technology Pvt Ltd.');
define('SUPERADMIN', 0);
define('KEY', '2018');
define('URL_ENCRYPT_KEY', 'Housie432423');
define('URL_ENCRYPT_IV', 'DHSKJHD^*$#^IDK*');
define('CIPHER', 'AES-128-ECB');

// RAZOR PAY
// define('API_KEY','rzp_live_EpsOYgHl8D1rbt');         //test
// define('API_SECRET','dP51DUiV9KIU1tzSQFFot43z');

define('API_KEY', 'rzp_live_TzyvQmD1j6o3jP');     //live
define('API_SECRET', 'Qz3zWugj0fTckHFBjd8IjnVO');

define('CLIENT_TEST_URL', 'https://test.cashfree.com'); //test
define('CLIENT_LIVE_URL', 'https://api.cashfree.com'); //live

define('PAYTM_TEST_URL', 'https://securegw-stage.paytm.in'); //test
define('PAYTM_LIVE_URL', 'https://securegw.paytm.in'); //live

define('SMS_API_KEY', 'a7b3595b-9204-11e7-94da-0200cd936042');

define('DEFAULT_OTP', '9999');
define('NO_CHAAL_PERCENT', 25); //DROP PERCENT IF NO CHAAL
define('CHAAL_PERCENT', 50); //DROP PERCENT IF CHAAL
define('MAX_POINTS', 80); //DROP PERCENT IF CHAAL

define('MAX_POINT', 101); //RummyPool Points

define('RUMMY_CARDS', 13); //RummyPool Cards


define('ANDER', 0);
define('BAHAR', 1);

define('DOWN', 0);
define('UP', 1);

define('UP_DOWN_MULTIPLY', 2);
define('UP_DOWN_TIE_MULTIPLY', 5);

define('DRAGON', 0);
define('TIGER', 1);
define('TIE', 2);

define('HEAD', 0);
define('TAIL', 1);

define('DRAGON_MULTIPLY', 2);
// define('TIGER_MULTIPLY', 2);
define('TIE_MULTIPLY', 9);

define('DRAGON_TIME_FOR_BET', 18);
define('DRAGON_TIME_FOR_START_NEW_GAME', 5);

define('HIGH_CARD', 1);
define('PAIR', 2);
define('COLOR', 3);
define('SEQUENCE', 4);
define('PURE_SEQUENCE', 5);
define('SET', 6);

define('HIGH_CARD_MULTIPLY', 3);
define('PAIR_MULTIPLY', 4);
define('COLOR_MULTIPLY', 5);
define('SEQUENCE_MULTIPLY', 6);
define('PURE_SEQUENCE_MULTIPLY', 10);
define('SET_MULTIPLY', 0.2);

define('TOYOTA', 1);
define('MAHINDRA', 2);
define('AUDI', 3);
define('BMW', 4);
define('MERCEDES', 5);
define('PORSCHE', 6);
define('LAMBORGHINI', 7);
define('FERRARI', 8);

define('TOYOTA_MULTIPLY', 5);
define('MAHINDRA_MULTIPLY', 5);
define('AUDI_MULTIPLY', 5);
define('BMW_MULTIPLY', 5);
define('MERCEDES_MULTIPLY', 10);
define('PORSCHE_MULTIPLY', 15);
define('LAMBORGHINI_MULTIPLY', 25);
define('FERRARI_MULTIPLY', 40);

// define('TIGER', 1);
define('SNAKE', 2);
define('SHARK', 3);
define('FOX', 4);
define('CHEETAH', 5);
define('BEAR', 6);
define('WHALE', 7);
define('LION', 8);

define('TIGER_MULTIPLY', 5);
define('SNAKE_MULTIPLY', 5);
define('SHARK_MULTIPLY', 5);
define('FOX_MULTIPLY', 5);
define('CHEETAH_MULTIPLY', 10);
define('BEAR_MULTIPLY', 15);
define('WHALE_MULTIPLY', 25);
define('LION_MULTIPLY', 40);

define('GREEN', 10);
define('VIOLET', 11);
define('RED', 12);

define('NUMBER_MULTIPLE', 9);
define('VIOLET_MULTIPLE', 4.5);
define('GREEN_RED_HALF_MULTIPLE', 1.5);
define('GREEN_RED_MULTIPLE', 2);

define('RB_RED', 1);
define('RB_BLACK', 2);
define('RB_PAIR', 3);
define('RB_COLOR', 4);
define('RB_SEQUENCE', 5);
define('RB_PURE_SEQUENCE', 6);
define('RB_SET', 7);

define('RB_RED_MULTIPLE', 2);
define('RB_BLACK_MULTIPLE', 2);
define('RB_PAIR_MULTIPLE', 3.5);
define('RB_COLOR_MULTIPLE', 10);
define('RB_SEQUENCE_MULTIPLE', 15);
define('RB_PURE_SEQUENCE_MULTIPLE', 100);
define('RB_SET_MULTIPLE', 100);

define('HEART', 1);
define('SPADE', 2);
define('DIAMOND', 3);
define('CLUB', 4);
define('FACE', 5);
define('FLAG', 6);

define('ONE_DICE', 0);
define('TWO_DICE', 3);
define('THREE_DICE', 5);
define('FOUR_DICE', 10);
define('FIVE_DICE', 20);
define('SIX_DICE', 100);

define('PLAYER', 0);
define('BANKER', 1);
// define('TIE', 2);
define('PLAYER_PAIR', 3);
define('BANKER_PAIR', 4);

define('PLAYER_MULTIPLE', 2);
define('BANKER_MULTIPLE', 1.95);
define('TIE_MULTIPLE', 8);
define('PLAYER_PAIR_MULTIPLE', 11);
define('BANKER_PAIR_MULTIPLE', 11);

// ROULETTE

define('R_TWELFTH_1ST', 37);
define('R_TWELFTH_2ND', 38);
define('R_TWELFTH_3RD', 39);
define('R_EIGHTEENTH_1ST', 40);
define('R_EIGHTEENTH_2ND', 41);
define('R_ODD', 42);
define('R_EVEN', 43);
define('R_RED', 44);
define('R_BLACK', 45);
define('R_ROW_1', 46);
define('R_ROW_2', 47);
define('R_ROW_3', 48);


define('R_NUMBER_MULTIPLE', 35);
define('R_COLOR_MULTIPLE', 2);
define('R_ODD_EVEN_MULTIPLE', 2);
define('R_TWELFTH_MULTIPLE', 3);
define('R_EIGHTEENTH_MULTIPLE', 2);
define('R_ROW_MULTIPLE', 3);

define('APP_URL', './');
define('BANNER_URL', 'uploads/banner/');
define('LOGO', 'uploads/logo/');
define('IMAGE_URL', 'uploads/images/');

define('USER_MANAGEMENT', true);
define('USER_CATEGORY', true);
define('WITHDRAWL_DASHBOARD', true);
define('CHIPS_MANAGEMENT', true);
define('GIFT_MANAGEMENT', true);
define('PURCHASE_HISTORY', true);
define('LEAD_BOARD', true);
define('NOTIFICATION', true);
define('WELCOME_BONUS', true);
define('SETTING', true);
define('REEDEM_MANAGEMENT', true);
define('WITHDRAWAL_LOG', true);
define('COMISSION', true);
define('BANNER', true);

define('TEENPATTI', true);
define('POINT_RUMMY', true);
define('RUMMY_POOL', true);
define('RUMMY_DEAL', true);
define('ANDER_BAHAR', true);
define('DRAGON_TIGER', true);
define('SEVEN_UP_DOWN', true);
define('CAR_ROULETTE', true);
define('COLOR_PREDICTION', true);
define('JACKPOT', true);
define('ANIMAL_ROULETTE', true);
define('LUDO', true);
define('LUDO_LOCAL', true);
define('LUDO_COMPUTER', true);
define('BACCARAT', true);
define('POKER', true);
define('RED_VS_BLACK', true);
define('HEAD_TAILS', true);
define('JHANDI_MUNDA', true);
define('ROULETTE', true);
define('RUMMY_TOURNAMENT', true);

define('TEENPATTI_LOG', 1);
define('POINT_RUMMY_LOG', 2);
define('RUMMY_POOL_LOG', 3);
define('RUMMY_DEAL_LOG', 4);
define('ANDER_BAHAR_LOG', 5);
define('DRAGON_TIGER_LOG', 6);
define('SEVEN_UP_DOWN_LOG', 7);
define('JACKPOT_LOG', 8);
define('CAR_ROULETTE_LOG', 9);
define('COLOR_PREDICTION_LOG', 10);
define('ANIMAL_ROULETTE_LOG', 11);
define('LUDO_LOG', 12);
define('LUDO_LOCAL_LOG', 13);
define('LUDO_COMPUTER_LOG', 14);
define('BACCARAT_LOG', 15);
define('POKER_LOG', 16);
define('RED_VS_BLACK_LOG', 17);
define('HEAD_TAILS_LOG', 18);
define('JHANDI_MUNDA_LOG', 19);
define('ROULETTE_LOG', 20);
// FCM Notification
define('SERVER_KEY', 'AAAA2bfIo_E:APA91bGFMoeE0wGoYy6q95ImATZ8KofZjx0yXi6ARfBkFzyHJ23Vi6tbV-gJ0kSbL_dzshsR_oVSomIsYP60RJAxzu3QeprGe9H62vEIpzBmI9IH5-6b5W-AFE2DjxiRjN8-2EoU7o03');

define('OTP_API_KEY', '3a2a9957-8028-11ed-9158-0200cd936042');