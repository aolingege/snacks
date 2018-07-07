import { Base } from '../../utils/base.js';

class Product extends Base{
  constructor(){
    super();
  }

  //商品ID  获取详细参数
  getDetailInfo(id,callback){
    var param = {
      url:'product/' + id,
      sCallback:function(data){
          callback && callback(data);
      }
    };
    this.request(param);
  }
}

export { Product }