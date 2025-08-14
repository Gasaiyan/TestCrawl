<?php
if(!defined('_TAI')){
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Đăng nhập hệ thống'
];

if(isLogin()){
    $token = getSession('token_login');
    $removeToken = delete('token_login', "token = '$token'");

    if ($removeToken){
        removeSession('token_login');
        redirect('?module=auth&action=login');
    }else {
        setSessionFlash('msg', 'Lỗi hệ thống, xin vui lòng thử lại sau. ');
        setSessionFlash('msg_type', 'danger');
    }
}else{
    setSessionFlash('msg', 'Lỗi hệ thống, xin vui lòng thử lại sau. ');
    setSessionFlash('msg_type', 'danger');
}