<?php

function getToken()
{
    $data = "sadda89d893jkh**($&#*isdfhkjsdhf89334324";
    $token_number = hash('sha512', $data);
    return $token_number;
}

function min_redeem()
{
    $CI =& get_instance();

    $CI->db->select('min_redeem');
    $CI->db->from('tbl_admin');
    return $CI->db->get()->row()->min_redeem;
}

function push_notification_android($device_id, $message)
{
    //API URL of FCM
    $url = 'https://fcm.googleapis.com/fcm/send';

    /*api_key available in:
    Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/    $api_key = SERVER_KEY;

    $fields = array(
        'registration_ids' => array(
                $device_id
        ),
        'data' => array(
                "message" => $message
        )
    );

    //header includes Content type and api key
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key='.$api_key
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === false) {
        die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    // echo $result;
    // exit;
    return $result;
}

function Send_SMS($MobileNo, $MSZ)
{
    // <editor-fold defaultstate="collapsed" desc="Send SMS">
    $msz = urlencode($MSZ);
    // $url = "http://www.makemysms.in/api/sendsms.php?username=AndroOTP&password=Sms@123&sender=ANDROP&mobile=$MobileNo&message=$msz&type=1&product=1";
    // $url = "http://sms53.hakimisolution.com/api/sendhttp.php?authkey=8707A7FvZhWH0QH5ee4bcf4P11&mobiles=$MobileNo&message=$msz&sender=TITANI&route=4&country=0";
    $url = "https://securesmpp.com/api/sendmessage.php?usr=tiktokrummy&apikey=CCBB75C3EFECEB0C17CB&sndr=TIKTOK&ph=".$MobileNo."&Template_ID=template_id&message=".$msz;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookie.txt');
    curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie.txt');
    // curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0');
    $strc = curl_exec($curl);
    SMS_Log($MobileNo, $url, $strc);
    return $strc;
    // </editor-fold>
}

function Send_OTP($MobileNo, $OTP)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://2factor.in/API/V1/'.OTP_API_KEY.'/SMS/'.$MobileNo.'/'.$OTP,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}

function SMS_Log($mobile, $url, $response)
{
    // <editor-fold defaultstate="collapsed" desc="Upload to EMR">
    $ci = & get_instance();
    $data = [
        'mobile' => $mobile,
        'url' => $url,
        'response' => $response,
        'added_date' => date('Y-m-d H:i:s')
    ];
    $ci->db->insert('tbl_sms_log', $data);
    return $ci->db->last_query();
    // </editor-fold>
}

function upload_image($file, $path, $i = '')
{
    $ci = &get_instance();
    if ($i !== '') {
        $_FILES['file']['name'] = $file['name'][$i];
        $_FILES['file']['type'] = $file['type'][$i];
        $_FILES['file']['tmp_name'] = $file['tmp_name'][$i];
        $_FILES['file']['error'] = $file['error'][$i];
        $_FILES['file']['size'] = $file['size'][$i];
        $ext = pathinfo($file['name'][$i], PATHINFO_EXTENSION);
    } else {
        $_FILES['file']['name'] = $file['name'];
        $_FILES['file']['type'] = $file['type'];
        $_FILES['file']['tmp_name'] = $file['tmp_name'];
        $_FILES['file']['error'] = $file['error'];
        $_FILES['file']['size'] = $file['size'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    }
    $config['upload_path'] = $path;
    $config['allowed_types'] = '*';
    $file_name =  date("Ymd_Hi") . "_" . uniqid() . "." . $ext;
    $config['file_name'] = $file_name;
    $ci->load->library('upload', $config);
    $ci->upload->initialize($config);
    if ($ci->upload->do_upload('file')) {
        $ci->upload->data();
        return $file_name;
    }
}

function upload_apk($file, $path, $i = '')
{
    $ci = &get_instance();

    $_FILES['file']['name'] = $file['name'];
    $_FILES['file']['type'] = $file['type'];
    $_FILES['file']['tmp_name'] = $file['tmp_name'];
    $_FILES['file']['error'] = $file['error'];
    $_FILES['file']['size'] = $file['size'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

    $config['upload_path'] = $path;
    $config['allowed_types'] = '*';
    $file_name =  "game." . $ext;
    $config['file_name'] = $file_name;
    $ci->load->library('upload', $config);
    $ci->upload->initialize($config);
    if ($ci->upload->do_upload('file')) {
        $ci->upload->data();
        return $file_name;
    }
}

function word_to_digit($word)
{
    $warr = explode(';', $word);
    $result = '';
    foreach ($warr as $value) {
        switch(trim(strtolower($value))) {
            case 'zero':
                $result .= '0';
                break;
            case 'one':
                $result .= '1';
                break;
            case 'two':
                $result .= '2';
                break;
            case 'three':
                $result .= '3';
                break;
            case 'four':
                $result .= '4';
                break;
            case 'five':
                $result .= '5';
                break;
            case 'six':
                $result .= '6';
                break;
            case 'seven':
                $result .= '7';
                break;
            case 'eight':
                $result .= '8';
                break;
            case 'nine':
                $result .= '9';
                break;
        }
    }
    return $result;
}

function shuffle_assoc($my_array)
{
    $keys = array_keys($my_array);

    shuffle($keys);

    foreach ($keys as $key) {
        $new[$key] = $my_array[$key];
    }

    $my_array = $new;

    return $my_array;
}