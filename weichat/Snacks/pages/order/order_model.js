import {Base} from "../../utils/base.js";

class Order extends Base{

    constructor(){
        super();
        this.storageKeyName = 'newOrder';
    }

    //下订单
    doOrder(param,callback){
        var that = this;
        var allParams = {
            url:'order',
            type:'post',
            data:{products:param},
            sCallback:function (data) {
                that.execSetStorageSync(true);
                callback && callback(data);
            },
            eCallback:function () {

            }
        };
        this.request(allParams);
    }
    //拉起支付
    execPay(orderNumber,callback){
        var allParams = {
            url:'pay/pre_order',
            type:'post',
            data:{id:orderNumber},
            sCallback:function (data) {
                var timeStamp = data.timeStamp;
                if (timeStamp){
                    //拉起支付....
                    wx.requestPayment({
                        'timeStamp':timeStamp.toString(),
                        'nonceStr':data.nonceStr,
                        'package':data.package,
                        'signType':data.signType,
                        'paySign':data.paySign,
                        success:function () {
                            callback && callback(2);
                        },
                        fail:function () {
                            callback && callback(1);
                        }
                    });
                }else{
                    callback && callback(0);
                }
            }
        };
        this.request(allParams);
    }
    //模拟支付
    //订单号和回调
    execPayVirtual(orderNumber,callback){
        var that = this;
        var allParams = {
            url:'pay/pay_virtual',
            type:'post',
            data:{id:orderNumber},
            sCallback: function (data) {
                if(data.pass){
                    //库存够
                    callback &&callback(2);
                }else{
                    //库存不够
                    callback &&callback(1);
                }
            },
            eCallback:function(){
                callback &&callback(0);
            }
        };
        that.request(allParams);
    }



    /*获得订单的具体内容*/
    getOrderInfoById(id,callback){
        var that=this;
        var allParams = {
            url: 'order/'+id,
            sCallback: function (data) {
                callback &&callback(data);
            },
            eCallback:function(){

            }
        };
        this.request(allParams);
    }

    getOrders(pageIndex,callback){
        var allParams = {
            url: 'order/by_user',
            data:{page:pageIndex},
            type:'get',
            sCallback: function (data) {
                callback && callback(data);  //1 未支付  2，已支付  3，已发货，4已支付，但库存不足
            }
        };
        this.request(allParams);
    }

    execSetStorageSync(data){
      wx.setStorageSync(this.storageKeyName,data);
    }

    /*是否有新的订单*/
    hasNewOrder(){
        var flag = wx.getStorageSync(this.storageKeyName);
        return flag==true;
    }

}

export {Order};