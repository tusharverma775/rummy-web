<?php
defined('BASEPATH') or exit('No direct script access allowed');


if (ENVIRONMENT == 'development') {
	$dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
	$dotenv->load();
}