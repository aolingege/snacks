/**
 * 公用基本操作类
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/27
 * Time: 17:10
 */

var baseClass = function () {
    /**
     * 通用ajax操作
     * @param url 访问的网址 默认当前网址
     * @param data 数据字符串 默认为空
     * @param method 数据提交方式 默认get提交
     * @param callback 回调函数
     */
    this.commonAjax = function (url, data, method, callback) {
        url = (url) ? url : window.location.href;
        data = (data) ? data : '';
        method = (method == 'POST') ? 'POST' : 'GET';
        $.ajax({
            url: url,
            cache: false,
            type: method,
            data: data,
            dataType: 'json',
            success: function (result) {
                if (callback) {
                    callback(result);
                }
                else {
                    status = result.status ? 'succeed' : 'warning';
                    tips(result.info);
                    result.status && !result.refresh && setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                    result.url && setTimeout(function () {
                        window.location.href = result.url;
                    }, 2000);
                }
            },
            error: function () {
                alert('貌似服务器出现了异常！', '错误');
            }
        });
    };

    /**
     * 表单异步提交
     * @param obj jQuery Object
     */
    this.ajaxSubmit = function (obj) {
        //延迟执行 让jquery的submit先执行
        obj = $(obj);
        setTimeout(function () {
            $.ajax({
                url: obj.attr('action'),
                data: obj.serialize(),
                cache: 'false',
                dataType: 'json',
                type: obj.attr('method'),
                success: function (result) {
                    tips(result.info, 4);
                    if (result.url) {
                        setTimeout(function () {
                            parent.openDialog ? parent.openDialog.remove() : window.location.href = result.url;
                        }, 2000);
                    }
                    else if (result.status) {
                        $('input[type="submit"]').attr({'class': 'reset', 'disabled': 'disabled'});
                        setTimeout(function () {
                            parent.openDialog ? parent.openDialog.remove() : window.location.reload();
                        }, 2000);
                    }
                },
                error: function (erro) {
                    console.log(erro);
                    alert('貌似服务器出现了异常！', '错误');
                }
            });
        }, 100);
    };
    
    
    this.noTipsAjax =function (url,data,callback,errorcallback) {
        if(!url)return false;
        $.ajax({
            url: url,
            cache: false,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (result) {
                if (callback) {
                    callback(result);
                }
            },
            error: function (result) {
                if (errorcallback) {
                    errorcallback(result);
                }
            }
        });
    };

};

var MyBase = new baseClass();


//检查是否有购物车信息
$(function () {
    savelocalStorageCart();
});

function savelocalStorageCart(noreload) {
    var cart = localStorage.getItem('myCart');
    var url = "/Index/cart/savecart.html";
    if( cart && cart.length > 2){
        //更新到服务器
        $.ajax({
            url: url,
            cache: false,
            type: 'POST',
            data: {cart:cart},
            dataType: 'json',
            success: function (res) {
                //删除本地的购物车信息
                if (res.status == 'ok'){
                    localStorage.removeItem('myCart');
                    console.log('成功更新购物车');
                    if (!noreload){
                        var pathName = window.location.pathname;
                        pathName = pathName.split('/');
                        //如果是购物车就刷新
                        if (pathName[2] == 'cart'){
                            location.reload();
                        }
                    }

                }
            },
            error: function () {
                console.log('购物车更新失败!');
            }
        });
    }
}

//重新系统的弹出组件


var d = null;

/**
 * 重写系统函数alert
 * @param message 提示消息内容
 * @param title 提示标题
 * @param bYes 点击确认按钮时触发的事件
 * @param modal 是否以模态窗口显示
 * @returns {boolean}
 */
window.alert = function (message, title, bYes, modal) {
    typeof(modal) == 'undefined' && (modal = true);
    d = dialog({
        title: title ? title : '提示',
        content: message ? message : 'null',
        okValue: '确定',
        ok: function () {
            return bYes ? bYes() : true;
        }
    });
    modal ? d.showModal() : d.show();
    window.alertDialog = d;
    return false;
};


/**
 * 重写系统函数confirm
 * @param message 提示消息内容
 * @param title 提示标题
 * @param bYes 点击确认按钮时触发的事件
 * @param bNo 点击取消按钮时触发的事件
 * @param modal 是否以模态窗口显示
 * @returns {boolean}
 */
window.confirm = function (message, title, bYes, bNo, modal) {
    typeof(modal) == 'undefined' && (modal = true);

    d = dialog({
        title: title ? title : '提示',
        content: message ? message : 'null',
        okValue: '确定',
        ok: function () {
            return bYes ? bYes() : true;
        },
        cancelValue: '取消',
        cancel: function () {
            return bNo ? bNo() : true;
        }
    });
    modal ? d.showModal() : d.show();
    window.confirmDialog = d;
    return false;
};



/**
 * 重写系统函数open
 * @param url
 * @param title
 * @param width
 * @param height
 * @param modal
 * @returns {boolean}
 */
window.opend = function (url, title, width, height, modal) {
    typeof(modal) == 'undefined' && (modal = true);

    openDialog = dialog({
        url: url.match('http') ? url : __WEBROOT__ + url,
        title: title ? title : '',
        okValue: '提交',
        width: width ? width : 1000,
        height: height ? height : $(window).height() - 100,
    });
    modal ? openDialog.showModal() : openDialog.show();

    return false;
};


/**
 * 添加系统函数show
 * @param message 提示消息内容
 * @param title 窗口持续显示的时间
 * @param modal 是否以模态窗口显示
 * @returns {boolean}
 */
window.show = function (message, title, modal) {
    typeof(modal) == 'undefined' && (modal = true);

    d = dialog({
        title: title ? title : '提示',
        content: message ? message : 'null',
    });
    modal ? d.showModal() : d.show();

    return false;
};



/**
 * 添加系统函数tips
 * @param message 提示消息内容
 * @param time 窗口持续显示的时间
 * @param modal 是否以模态窗口显示
 * @returns {boolean}
 */
window.tips = function (message, time, modal) {
    typeof(modal) == 'undefined' && (modal = true);
    time && (time = parseFloat(time));

    d = dialog({
        content: message ? message : 'null'
    });

    setTimeout(function () {
        d.close().remove();
    }, (time ? time : 0.5) * 1000);
    modal ? d.showModal() : d.show();
    window.tipsDialog = d;
    return false;
};
