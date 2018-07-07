import {Base} from "../../utils/base";

class Cart extends Base{

    constructor(){
        super();
        this.storageKeyName = 'cart';
    }


    add(item,counts){
        //获取本地购物车信息
        var cartData =this.getCartDataFromLocal();
        if(!cartData){
            cartData=[];
        }
        //是否要继续添加到已有购物车
        var isHasInfo = this.isHasThatOne(item.id,cartData);
        //用户购物车里面没有要添加的产品
        if(isHasInfo.index ==-1){
            //购物车没有选的信息
            item.counts = counts;
            item.selectStatus = true;//添加进后默认选中
            cartData.push(item);
        }else{
            //购物车有选的信息
            cartData[isHasInfo.index].counts += counts;
        }
        wx.setStorageSync(this.storageKeyName,cartData);
    }

    //本地购物车信息,返回本地选中的信息
    getCartDataFromLocal(flag) {
        var res = wx.getStorageSync(this.storageKeyName);
        if (!res){
            res = [];
        }
        if(flag){
            var newRes = [];
            for(var i=0;i<res.length;i++){
                if(res[i].selectStatus){
                    newRes.push(res[i]);
                }
            }
            res = newRes;
        }
        return res;
    }

    //true 考虑商品选择状态
    /**
     * 得到本地购物车总共数量
     * @param flag
     * @returns {number}
     */
    getCartTotalCounts(flag){
        var data = this.getCartDataFromLocal();
        var counts = 0;
        //所有产品所以要循环
        for (var i=0;i<data.length;i++){
            if(flag){
                if(data[i].selectStatus){
                    counts += data[i].counts;
                }
            }else{
                counts += data[i].counts;
            }
        }
        return counts;
    }

    /**
     * 购物车是否有添加数据(是否要继续添加到已有购物车)
     * @param id
     * @param arr
     * @returns {{Index: number}}
     */
    isHasThatOne(id,arr){
        var item;
        var result={index:-1};
        for(var i=0;i<arr.length;i++){
            item=arr[i];
            if(item.id == id) {
                result = {
                    index:i,
                    data:item
                };
                break;
            }
        }
        return result;
    }

    //改变产品在购物车中的数量
    changeCounts(id,counts){
        var cartData=this.getCartDataFromLocal();
        var hasInfo=this.isHasThatOne(id,cartData);
        if(hasInfo.index!=-1){
            if(hasInfo.data.counts>1){
                cartData[hasInfo.index].counts+=counts;
            }
        }
        wx.setStorageSync(this.storageKeyName,cartData);
    }


    addCounts(id){
        this.changeCounts(id,1);
    }

    cutCounts(id){
        this.changeCounts(id,-1);
    }

    //
    delete(ids){
        if (!(ids instanceof Array)){
            ids = [ids];
        }
        var cartData = this.getCartDataFromLocal();
        for(var i=0;i<ids.length;i++){
            var hasInfo =this.isHasThatOne(ids[i],cartData);
            if(hasInfo.index != -1){
                //删数组中的对应元素
                cartData.splice(hasInfo.index,1);
            }
        }
        //保存
        wx.setStorageSync(this.storageKeyName,cartData);
    }


    execSetStorageSync(data){
        wx.setStorageSync(this.storageKeyName,data);
    }

}

export {Cart};