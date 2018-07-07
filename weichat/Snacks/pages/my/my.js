// pages/my/my.js
import {Address} from '../../utils/address.js';
import {Order} from '../order/order_model.js';
import {My} from './my_model.js';

var address=new Address();
var order=new Order();
var my=new My();

Page({

  /**
   * 页面的初始数据
   */
  data: {
      pageIndex:1,
      orderArr:[],
      isLoadedAll:false,
      login:false
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
      order.execSetStorageSync(true);
      this.loadData();
      this.getAddressInfo();
  },

  onShow:function () {
      var newOrderFlag = order.hasNewOrder();
      // 如果有新订单
      if(newOrderFlag){
          this.refresh();
      }
  },

  refresh:function () {
    var that = this;
    this.data.orderArr = [];
    this.getOrders(()=>{
       that.data.isLoadedAll = false;
       that.data.pageIndex = 1;
       wx.stopPullDownRefresh();
        //更新标志位
       order.execSetStorageSync(false);
    });
  },

  getAddressInfo:function () {
      var that=this;
      address.getAddress((addressInfo)=>{
          that.bindAddressInfo(addressInfo);
      });
  },

    /*绑定地址信息*/
  bindAddressInfo:function(addressInfo){
      this.setData({
          addressInfo: addressInfo
      });
  },
  
  loadData:function () {
      var that =this;
      my.getUserInfo((data)=>{
          if (data.avatarUrl != '../../image/icon/user@default.png'){
              that.setData({
                  userInfo:data,
                  login:true
              });
          }else{
              that.setData({
                  userInfo:data
              });
          }
      });
  },

  bindGetUserInfo:function(e) {
      var userJSON = e.detail.rawData;
      var userInfo = JSON.parse(userJSON);
      my.updateUserInfo(userJSON);
      this.setData({
          userInfo:userInfo,
          login:true
      });
  },

  
  getOrders:function (callback) {
      var that = this;
        order.getOrders(this.data.pageIndex,(res)=>{
            var data =res.data;
            if(data.length > 0){
                this.data.orderArr.push.apply(this.data.orderArr,data);
                this.setData({
                    orderArr:this.data.orderArr
                });
            }else{
                that.data.isLoadedAll = true;
            }
            callback && callback();
        });
  },

  onReachBottom:function () {
      if(!this.data.isLoadedAll) {
          this.data.pageIndex++;
          this.getOrders();
      }
  },

  /*显示订单的具体信息*/
  showOrderDetailInfo:function(event){
      var id=order.getDataSet(event,'id');
      wx.navigateTo({
          url:'../order/order?from=order&id='+id
      });
  },
    

  rePay:function (event) {
      //id
      var id=order.getDataSet(event,'id');
      //序号
      var index=order.getDataSet(event,'index');
      this.execPay(id,index);
  },
  
  execPay:function (id,index) {
      var that=this;
      order.execPayVirtual(id,(statusCode)=>{
          if(statusCode>0){
              //支付成功
              var flag = statusCode == 2;
              //更新订单显示状态
              if(flag){
                  that.data.orderArr[index].status = 2;
                  that.setData({
                      orderArr: that.data.orderArr
                  });
              }
              //跳转到 成功页面
              wx.navigateTo({
                  url: '../pay-result/pay-result?id='+id+'&flag='+flag+'&from=my'
              });
          }else{
              that.showTips('支付失败','商品已下架或库存不足');
          }
      });
  },

    /*
   * 提示窗口
   * params:
   * title - {string}标题
   * content - {string}内容
   * flag - {bool}是否跳转到 "我的页面"
   */
   showTips:function(title,content){
       wx.showModal({
           title: title,
           content: content,
           showCancel:false,
           success: function(res) {
           }
       });
   },


    editAddress:function (event) {
        var that =this;
        wx.chooseAddress({
            success:function (res) {
                var addressInfo = {
                    name:res.userName,
                    mobile:res.telNumber,
                    totalDetail:address.setAddressInfo(res)
                };
                //渲染的页面上
                that.bindAddressInfo(addressInfo);
                //保存地址
                address.submitAddress(res,(flag)=>{
                    if(!flag){
                        that.showTips('操作提示','地址信息更新失败!');
                    }
                });
            }
        })
    }

})