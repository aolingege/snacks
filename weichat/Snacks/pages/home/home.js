// pages/home/home.js
import {Home} from 'home_model.js'
var home = new Home();
Page({

  /**
   * 页面的初始数据
   */
  data: {

  },

  onLoad:function(){
    this.loadDate();
  },

  //小程序初始化
  loadDate:function(){
    var id = 1;
    home.getBannerData(id,(res)=>{
      this.setData({
        'bannerArr':res
      });
    });

    home.getThemeData((res)=>{
        this.setData({
            'themeArr':res
        });
    });

    home.getProductsData((data)=>{
      this.setData({
        'products':data
      });
    });
  },

  onProductsItemTap:function(event){
      var id = home.getDataSet(event,'id');
      wx.navigateTo({
        url: '../product/product?id='+id
      });
  },

  onThemesItemTap:function(event){
    var id = home.getDataSet(event,'id');
    var name = home.getDataSet(event,'name');
    wx.navigateTo({
      url: '../theme/theme?id='+ id +'&name=' + name
    }) 
  }



  

  
})