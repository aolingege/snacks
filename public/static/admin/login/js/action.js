$(function () {
    $('#validateText').on('focus',function () {
        $('#validateImg').removeClass('hide');
    });

    $("#loginForm").validate({
        rules: {
            username: {
                required: true,
                rangelength:[4,15]
            },
            password: {
                required: true,
                rangelength:[6,18]
            },
            validate: {
                required: true
            }
        },
        messages: {
            username: {
                required: "请输入用户名",
                minlength: "用户名长度为 4至15 位"
            },
            password: {
                required: "请输入密码",
                minlength: "密码长度为 4至15 位"
            },
            validate:{
                required: "请输入验证码"
            }
        }
    })

});