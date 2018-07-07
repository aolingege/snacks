import {Base} from "base.js";
import {Config} from "config.js";

class Address extends Base{
    constructor(){
       super();
    }

    setAddressInfo(res){
        var province = res.provinceName || res.province;
        var city = res.cityName || res.city;
        var country = res.countyName || res.country;
        var detail = res.detailInfo || res.detail;

        var totalDetail = city + country + detail;
        if(!this.isCenterCity(province)){
            totalDetail = province + totalDetail;
        }
        return totalDetail;
    }

    getAddress(callback){
        var that = this;
        var param = {
            url:'address',
            sCallback:function (res) {
                if (res){
                    res.totalDetail = that.setAddressInfo(res);
                    callback && callback(res);
                }
            }
        };
        this.request(param);
    }

    // 如果是直辖市
    isCenterCity(name){
        var centerCitys = ['北京市','天津市','上海市','重庆市'];
        var flag = centerCitys.indexOf(name) >= 0;
        return flag;
    }

    //提交地址信息
    submitAddress(data,callback){
        //过滤微信组件上的地址信息
        data = this.setUpAddress(data);
        var param = {
            url:'address',
            type:'POST',
            data:data,
            sCallback:function (res) {
                callback && callback(true,res);
            },
            eCallback(res){
                callback && callback(false,res);
            }
        };
        this.request(param);
    }

    //因为使用的是微信的组件
    //将微信的字段过滤成我们的字段
    setUpAddress(res){
        var formData = {
            name:res.userName,
            province:res.provinceName,
            city:res.cityName,
            country:res.countyName,
            mobile:res.telNumber,
            detail:res.detailInfo
        };
        return formData;
    }
}

export {Address}