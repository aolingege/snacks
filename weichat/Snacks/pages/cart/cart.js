// pages/cart/cart.js
import { Cart } from 'cart_model.js';
var cart = new Cart();
Page({

    /**
     * 页面的初始数据
     */
    data: {

    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function (options) {

    },

    onHide:function () {
        cart.execSetStorageSync(this.data.cartData);
    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function () {
        //读取本地购物车信息
        var cartData =cart.getCartDataFromLocal();
        // 返还选中产品数量
        var countsInfo = cart.getCartTotalCounts(true);
        //计算选中的金额
        var cal = this.calcTotalAccountAndCounts(cartData);
        this.setData({
            selectedCounts:cal.selectedCounts,
            selectedTypeCounts:cal.selectedTypeCounts,
            account:cal.account,
            cartData:cartData
        });
    },

    //计算金额
    calcTotalAccountAndCounts:function(data){
        var len = data.length;
        var account = 0;
        var selectedCounts = 0;
        var selectedTypeCounts = 0;
        var multiple =100;

        for(var i = 0;i<len;i++){
            if(data[i].selectStatus){
                account += data[i].counts * multiple * Number(data[i].price) * multiple;
                selectedCounts += data[i].counts;
                selectedTypeCounts++;
            }
        }
        return {
            selectedCounts :selectedCounts,
            selectedTypeCounts : selectedTypeCounts,
            account:account / (multiple * multiple)
         };
    },
    //切换选中和没选的状态
    toggleSelect:function (event) {
      var id = cart.getDataSet(event,'id');
      var status = cart.getDataSet(event,'status');
      //得到需要切换的物品索引
      var index = this.getProductIndexById(id);
      this.data.cartData[index].selectStatus = !status;
      this.resetCarData();
    },

    //重新计算以及渲染
    resetCarData:function () {
      var newData = this.calcTotalAccountAndCounts(this.data.cartData);
      this.setData({
          //钱
         account:newData.account,
         selectedCounts:newData.selectedCounts,
         selectedTypeCounts:newData.selectedTypeCounts,
         cartData:this.data.cartData
      });
    },
    toggleSelectAll:function (event) {
        //获取全选选中状态
        var status = cart.getDataSet(event,'status') == 'true';
        var data = this.data.cartData;
        var len = data.length;
        for(var i= 0;i<len;i++){
          data[i].selectStatus = !status;
        }
        this.resetCarData();
    },
    //得到产品在本地购物车中的索引
    getProductIndexById:function (id) {
      var data = this.data.cartData;
      var len = data.length;
      for (var i=0;i<len;i++){
        if(data[i].id == id){
          return i;
        }
      }
    },

    changeCounts:function (event) {
        var id = cart.getDataSet(event,'id');
        var type = cart.getDataSet(event,'type');
        var index = this.getProductIndexById(id);
        var counts = 1;
        if(type == 'add'){
          cart.addCounts(id);
        }else {
          counts = -1;
          cart.cutCounts(id);
        }
        this.data.cartData[index].counts += counts;
        this.resetCarData();
    },

    delete:function (event) {
        var id = cart.getDataSet(event,'id');
        var index = this.getProductIndexById(id);
        this.data.cartData.splice(index,1);
        this.resetCarData();
        cart.delete(id);
    },

    submitOrder:function (event) {
        wx.navigateTo({
            url:'../order/order?account=' + this.data.account + '&from=cart'
        });
    }

})