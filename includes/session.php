<?php
if(!defined('_TAI')) {
    die('Truy cập không hợp lệ');
}

function setSession($key, $value){
    if(!empty(session_id())){ 
        $_SESSION[$key] = $value;
        return true;
    }
    return false;
}

//lấy dữ liệu từ sessions
function getSession($key = ''){
    if(empty($key)){
        return $_SESSION;
    }else {
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
    }
    return false;
}

//xóa session
function removeSession($key = ''){
    if(empty($key)){
        session_destroy();
        return true;
    }else{
        if(isset($_SESSION[$key])){
            unset ($_SESSION[$key]);
        }
        return true;
    }
    return false;
}

// tạo session flash

function setSessionFlash($key, $value){
    $key = $key.'Flash';
    $rel =setSession($key, $value);
    return $rel;
}

//lấy session flash
function getSessionFlash($key){
    $key = $key.'Flash';
    $rel = getSession($key);
    removeSession($key);
    return $rel;
}