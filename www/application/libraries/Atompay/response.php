<?php

require_once 'TransactionResponse.php';
$transactionResponse = new TransactionResponse();

$transactionResponse->setRespHashKey("KEYRESP123657234");
$transactionResponse->setResponseEncypritonKey("8E41C78439831010F81F61C344B7BFC7");
$transactionResponse->setSalt("8E41C78439831010F81F61C344B7BFC7");

$arrayofdata = $transactionResponse->decryptResponseIntoArray($_POST['encdata']);

print_r($arrayofdata);


// Array ( [date] => Tue Aug 23 17:45:48 IST 2022 [CardNumber] => null [surcharge] => 0.00 [clientcode] => NAVIN [scheme] => null [udf15] => null [udf14] => null [signature] => e46e3ec714bec1fc2df103e207555eafbfcd6633db45674c9b2ea7c309f90fc2bee1d3cbc0cae930d25b578568b46b6871ba2feee4f10ae47e5063391e361491 [udf13] => null [udf12] => null [udf11] => null [amt] => 1.00 [udf10] => null [merchant_id] => 192 [mer_txn] => 0010 [f_code] => Ok [bank_txn] => 1661256939196 [udf9] => null [ipg_txn_id] => 11000000262379 [bank_name] => Hdfc Bank [prod] => NSE [mmp_txn] => 11000000262379 [udf5] => null [udf6] => null [udf3] => 9999999999 [udf4] => Mumbai [udf1] => Test Name [udf2] => test@test.com [discriminator] => UP [auth_code] => 9961661256939196 [desc] => APPROVED OR COMPLETED SUCCESSFULLY )