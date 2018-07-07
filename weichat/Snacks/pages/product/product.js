// pages/product/product.js
import { Product } from 'product_model.js'
import { Cart } from "../cart/cart_model.js";

var product = new Product();
var cart = new Cart();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    countsArray:[1,2,3,4,5,6,7,8,9,10],
    productCount:1,
    productParam:['商品详情','产品参数','售后保障'],
    currentTabsIndex:0
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var id = options.id;
    this.data.id = id;
    this.loadData();
  },

  loadData: function () {
    //获得产品详细信息,包括详情页
    product.getDetailInfo(this.data.id, (data) => {
      this.setData({
        cartTotalCounts:cart.getCartTotalCounts(),
        product:data
      });
    });
  },

  //picker数据更新
  bindPickerChange:function (event) {
      var index = event.detail.value;
      var selectedCount = this.data.countsArray[index];
      this.setData({
        productCount:selectedCount
      });
  },

  //切换TAB
  onTabsItemTap:function (event) {
      var index =product.getDataSet(event,'index');
      this.setData({
         currentTabsIndex:index
      });
  },

  //添加到购物车,将总数渲染到页面
  onAddingToCartTap:function (event) {
      this.addToCart();
      //本来的数量加上要添加的数量
      // var counts = this.data.cartTotalCounts + this.data.productCount;
      this.setData({
         cartTotalCounts:cart.getCartTotalCounts(),
      });
  },

  //添加到本地购物车
  addToCart:function () {
     var tempObj = {};
     var keys = ['id','name','main_img_url','price'];
     for (var key in this.data.product){
         //检查我们想要的KEY字符是否出现
         if( keys.indexOf(key) >= 0 ){
                tempObj[key] = this.data.product[key];
         }
     }
     //添加到本地购物车storage
     cart.add(tempObj,this.data.productCount);
  },

  //跳转到购物车
  onCartTap:function (event) {
      wx.switchTab({
          url:'/pages/cart/cart'
      });
  }

})