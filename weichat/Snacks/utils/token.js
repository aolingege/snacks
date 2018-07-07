import {Config} from "config";

class Token{
    constructor(){
        this.verifyUrl = Config.restUrl + 'token/verify';
        this.tokenUrl = Config.restUrl + 'token/user';
    }

    verify(){
        // 得到本地TOKEN令牌
        var token = wx.getStorageSync('token');
        if(!token){
            //没有令牌
            this.getTokenFromServer();
        }else{
            this.veirfyFromServer(token);
        }
    }

    veirfyFromServer(token){
        var that = this;
        wx.request({
            url:that.verifyUrl,
            method:'POST',
            data:{
                token:token
            },
            success:function (res) {
                //token令牌是否存在
                var valid = res.data.isValid;
                if(!valid){
                    //没有令牌，请求服务器
                    that.getTokenFromServer();
                }
            }
        })
    }

    //从服务器获取token
    getTokenFromServer(callBack){
        var that = this;
        wx.login({
            success:function (res) {
                wx.request({
                    url:that.tokenUrl,
                    method:'POST',
                    data:{
                        code:res.code
                    },
                    success:function (res) {
                        wx.setStorageSync('token',res.data.token);
                        callBack && callBack(res.data.token);
                    }
                })
            }
        })
    }

}

export {Token};