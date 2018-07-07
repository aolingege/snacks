// pages/order/order.js
import {Cart} from "../cart/cart_model.js";
import {Order} from "../order/order_model.js";
import {Address} from "../../utils/address.js";

var cart = new Cart();
var order = new Order();
var address = new Address();
Page({

  /**
   * 页面的初始数据
   */
  data: {
        id:null
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
        var from =options.from;
        //订单是从哪里生成
        if (from == 'cart'){
            this.fromCart(options.account);
        }else{
            var id = options.id;
            this.fromOrder(id);
        }
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
      if(this.data.id){
          this.fromOrder(this.data.id);
      }
  },

  //订单来自购物车的新订单
  fromCart:function (account) {
        var productsArr;
        this.data.account = account;
        productsArr = cart.getCartDataFromLocal(true);
        //未付款
        this.setData({
           productArr:productsArr,
            account:account,
            orderStatus:0
        });
        //发出一个地址请求，得到该用户的地址信息
        address.getAddress((res)=>{
           //渲染地址信息
           this.bindAddressInfo(res);
        });
  },

  //添加或编辑一个地址
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
              }else{
                that.showTips('成功','地址信息更新成功!');
              }
          });
      }
    })
  },

  //弹出的对话框
  showTips:function (title,content,flag) {
      wx.showModal({
         title:title,
         content:content,
         showCancel:false,
         success:function (res) {
             if(flag){
               wx.switchTab({
                  url:'/pages/my/my'
               });
             }
         }
      });
  },

  //渲染地址信息到页面上
  bindAddressInfo:function (addressInfo) {
      this.setData({
         addressInfo:addressInfo
      });
  },

  //去付款
  pay:function () {
    if(!this.data.addressInfo){
        this.showTips('下单提示','请填写您的收货地址');
        return;
    }
    if(this.data.orderStatus == 0){
        this.firstTimePay();
    }else {
        this.oneMoresTimePay();
    }
  },

  //第一次付款
  firstTimePay:function () {
    var orderInfo = [];
    var procuctInfo = this.data.productArr;
    var order = new Order();
    for (var i = 0;i< procuctInfo.length;i++){
        orderInfo.push({
           product_id:procuctInfo[i].id,
           count:procuctInfo[i].counts
        });
    }
    var that = this;
    //1.生成订单号 2.根据订单号支付
    //下订单
    order.doOrder(orderInfo,(data)=>{
        //订单生成成功
        if (data.pass){
            //更新订单状态
            var id = data.order_id;
            that.data.id = id;
            that.data.fromCartFlag = false;
            //执行支付
            that.execPay(id);
        }else {
            that.orderFail(data);
        }
    });
  },

  execPay:function (id) {
      var that = this;
      order.execPayVirtual(id,(statusCode)=>{
          if (statusCode != 0){
              //将已经下单的商品删除
              that.deleteProducts();
              var flag = statusCode == 2;
              wx.navigateTo({
                  url:'../pay-result/pay-result?id='+ id + '&flag=' + flag
                  + '&from=order'
              });
          }
      });
  },

  //删除已经下单的商品(本地)
  deleteProducts:function () {
      var ids = [];
      var arr = this.data.productArr;
      for (var i=0;i<arr.length;i++){
          ids.push(arr[i].id);
      }
      cart.delete(ids);
  },

     //下单失败,data订单结果
  orderFail:function(data){
      var nameArr=[],
          name='',
          str='',
          pArr=data.pStatusArray;
      for(let i=0;i<pArr.length;i++){
          if(!pArr[i].haveStock){
              name=pArr[i].name;
              if(name.length>15){
                  name = name.substr(0,12)+'...';
              }
              nameArr.push(name);
              if(nameArr.length>=2){
                  break;
              }
          }
      }
      str+=nameArr.join('、');
      if(nameArr.length>2){
          str+=' 等';
      }
      str+=' 缺货';
      wx.showModal({
          title: '下单失败',
          content: str,
          showCancel:false,
          success: function(res) {

          }
      });
  },

    /* 再次次支付*/
    oneMoresTimePay:function(){
        this.execPay(this.data.id);
    },


    /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },


    
  fromOrder:function (id) {
      if(id) {
          var that = this;
          //下单后，支付成功或者失败后，点左上角返回时能够更新订单状态 所以放在onshow中
          //加载服务器中的订单
          order.getOrderInfoById(id, (data)=> {
              that.setData({
                  orderStatus: data.status,
                  productArr: data.snap_items,
                  account: data.total_price,
                  basicInfo: {
                      orderTime: data.create_time,
                      orderNo: data.order_no
                  },
              });

              // 快照地址
              var addressInfo=data.snap_address;
              addressInfo.totalDetail = address.setAddressInfo(addressInfo);
              that.bindAddressInfo(addressInfo);
          });
      }
  },
  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  }
})