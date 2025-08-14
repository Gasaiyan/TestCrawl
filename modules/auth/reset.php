<?php
if(!defined('_TAI')){
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Đặt lại mật khẩu'
];
layout('header-auth',$data);
$filterGet = filterData('get');

if(!empty ($filterGet['token'])){
    $tokenReset = $filterGet['token'];
}


if(!empty($tokenReset)){
    //Check token có chính xác hay ko
    $checkToken = getOne("SELECT * FROM users WHERE forget_token = '$tokenReset'");
    if(!empty($checkToken)){
        if(isPost()){
            $filter = filterData();
            $errors = [];
           
         //Validate password
         if(empty(trim($filter['password']))){
            $errors['password']['required'] = 'Mật khẩu bắt buộc phải nhập';
        }else {
            if (strlen(trim($filter['password']))<6){
                $errors['password']['length'] = 'Mật khẩu phải lớn hơn 6 kí tự';
            }
        }
        if(empty($errors)){
            $password = $filter ['password'];   
        }
        
        //Validate confirm password
        if(empty(trim($filter['password']))){
            $errors['confirm_password']['required'] = 'Vui lòng nhập lại mật khẩu';
        }else {
            if (trim($filter['password']) !== trim($filter['confirm_password'])){
                $errors['confirm_password']['like'] = 'Mật khẩu nhập vào không khớp';
            }
        }
        if (empty($errors)){
            $password = password_hash($filter['password'], PASSWORD_DEFAULT);
            $data = [
                'password' => $password,
                'forget_token' => null,
                'updated_at' => date('Y:m:d H:i:s')
            ];
            $condition = "id=". $checkToken['id'];
            $updateStatus = update('users', $data, $condition);

            if($updateStatus){

                $emailTo = $checkToken['email'];
                $subject = 'Đổi mật khẩu thành công !!';
                $content = 'Chúc mừng bạn đã đổi thành công trên TAI. </br>';
                $content .= 'Nếu không phải bạn thao tác hãy liên hệ ngay với admin </br>';
                $content .= 'Cảm ơn bạn đã ủng hộ Tai!!!';
                
                sendMail($emailTo, $subject, $content);
                setSessionFlash('msg', 'Đổi mật khẩu thành công.');
                setSessionFlash('msg_type', 'success');

            }else{
                setSessionFlash('msg', 'Đã có lỗi xảy ra vui lòng thử lại sau');
                setSessionFlash('msg_type', 'danger');
            }

        }else {
            setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào');
            setSessionFlash('msg_type', 'danger');
            setSessionFlash('oldData', $filter);
            setSessionFlash('errors', $errors);
        }
    }
    }else {
        getMsg('Liên kết đã hết hạn hoặc không tồn tại', 'danger');
    }
} else {
    getMsg('Liên kết đã hết hạn hoặc không tồn tại', 'danger');
}   


$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
$errorsArr =  getSessionFlash('errors');


?>

<section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="<?php echo _HOST_URL_TEMPLATES;?>/assets/image/draw2.webp" class="img-fluid"
                    alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <?php 
                if(!empty($msg) && !empty($msg_type)){
                    getMsg($msg,$msg_type);
                }
                
                ?>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <h2 class="fw-normal mb-5 me-3">Đặt lại mật khẩu</h2>

                    </div>

                    <!-- Pass mới input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="password" name="password" class="form-control form-control-lg"
                            placeholder="Nhập mật khẩu mới" />
                        <?php
                        if(!empty($errorsArr)){
                            echo formError($errorsArr, 'password');
                        }
                        ?>
                    </div>

                    <!-- Nhập lại pass -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="password" name="confirm_password" class="form-control form-control-lg"
                            placeholder="Nhập lại mật khẩu mới" />
                        <?php
                        if(!empty($errorsArr)){
                            echo formError($errorsArr, 'confirm_password');
                        }
                        ?>
                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" type="button" data-mdb-button-init data-mdb-ripple-init
                            class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Gửi</button>
                    </div>


                </form>
                <p style="margin-top: 15px;"> <a href="<?php echo _HOST_URL;?>?module=auth&action=login"
                        class="link-danger">Đăng nhập
                    </a></p>
            </div>
        </div>
    </div>

</section>

<?php 
    layout('footer');