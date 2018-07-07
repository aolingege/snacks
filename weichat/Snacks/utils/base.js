import {Config} from '../utils/config.js'
import {Token} from "./token";
class Base{
  constructor(){
    this.baseRequestUrl = Config.restUrl;
  }

  request(params,noRefetch){
    var that = this;
    var url = this.baseRequestUrl + params.url;
    params.type = params.type ? params.type : 'GET';  
    wx.request({
      url: url,
      data: params.data,
      method:params.type,
      header:{
        'content-type':'application/json',
        'token':wx.getStorageSync('token')
      },
      success:function(res){
        var code = res.statusCode.toString();
        var startChar = code.charAt(0);
        if(startChar == '2'){
            params.sCallback && params.sCallback(res.data);
        }else{
            if(code == '401'){
                if(!noRefetch){
                    that.refetch(params);
                }
            }
            if(noRefetch){
                params.eCallback && params.eCallback(res.data);
            }
        }
      },
      fail:function(err){
        console.log(err);
      }
    })
  }

  refetch(params){
    var token = new Token();
    token.getTokenFromServer((token)=>{
      this.request(params,true);
    });
  }

  /*获得元素上的绑定的值*/
  getDataSet(event,key){
    return event.currentTarget.dataset[key];
  }

}

export{Base};