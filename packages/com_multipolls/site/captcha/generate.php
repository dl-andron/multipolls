<?php

if(isset($_POST['request']))
{	
	if($_POST['request'] =='refresh-captcha')
	{
		echo encrypt_decrypt('encrypt', generateRandomString(6));
	}
	
}

function encrypt_decrypt($action, $string)
{
	$output = false;
	/*$encrypt_method = "AES-256-CBC";
	$secret_key = 'WS-SERVICE-KEY';
	$secret_iv = 'WS-SERVICE-VALUE';
	// hash
	$key = hash('sha256', $secret_key);
	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr(hash('sha256', $secret_iv), 0, 16)*/;

	if ($action == 'encrypt') 
	{
		//$output = base64_encode(openssl_encrypt($string, $фф, $key, 0, $iv));
		$output = base64_encode($string);
	} 
	elseif ($action == 'decrypt') 
	{
	    //$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	    $output = base64_decode($string);
	}
	
	return $output;
}

function generateRandomString($length = 10) 
{
    $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($letters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) 
    {
    	$randomString .= $letters[rand(0, $charactersLength - 1)];
	}
    return mb_strtolower($randomString);
}