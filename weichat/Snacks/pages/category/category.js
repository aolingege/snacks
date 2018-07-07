// pages/category/category.js
import {Category} from "category_model.js";
var category = new Category();
Page({

  /**
   * 页面的初始数据
   */
  data: {
      transClassArr:['tanslate0','tanslate1','tanslate2','tanslate3','tanslate4','tanslate5'],
      currentMenuIndex:0,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function () {
      this.loadData();
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },
    
  loadData:function () {
      //左边分类数据
          category.getCategoryType((categoryData)=>{
          this.setData({
              categoryTypeArr:categoryData
          });
          //回调函数为异步
          //右边产品数据
          category.getProductsByCategory(categoryData[0].id,(data)=>{
              var dataobj = {
                procucts:data,//右边产品信息
                topImgUrl:categoryData[0].img.url,//右边顶图
                title:categoryData[0].name//分类标题
              };
              this.setData({
                  categoryInfo0:dataobj
              });
          });
      });//回调结束
  },


    /*切换分类*/
  changeCategory:function(event){
      var index=category.getDataSet(event,'index');
          //获取data-set
      var id=category.getDataSet(event,'id');
      this.setData({
          currentMenuIndex:index
      });
      if(!this.isLoadedData(index)) {
          var that=this;
          this.getProductsByCategory(id, (data)=> {
              that.setData(that.getDataObjForBind(index,data));
          });
      }
  },

  isLoadedData:function(index){
      if(this.data['categoryInfo'+index]){
          return true;
      }
      return false;
  },

  getDataObjForBind:function(index,data){
      var obj={};
      var arr=[0,1,2,3,4,5];
      var baseData=this.data.categoryTypeArr[index];
      for(var item in arr){
          if(item==arr[index]) {
              obj['categoryInfo' + item]={
                  procucts:data,
                  topImgUrl:baseData.img.url,
                  title:baseData.name
              };
              return obj;
          }
        }},

    getProductsByCategory:function(id,callback){
        category.getProductsByCategory(id,(data)=> {
            callback&&callback(data);
        });
    },

    //跳转到商品详情
    onProductsItemTap: function (event) {
        var id = category.getDataSet(event, 'id');
        wx.navigateTo({
            url: '../product/product?id=' + id
        })
    },

    //下拉刷新页面
    onPullDownRefresh: function(){
        this._loadData(()=>{
            wx.stopPullDownRefresh()
        });
    },

    //分享效果
    onShareAppMessage: function () {
        return {
            title: '美味等你来',
            path: 'pages/category/category'
        }
    }

});