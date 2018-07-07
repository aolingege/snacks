####毕设URL说明

#####小程序URL

 - @url：/banner/:id	
 - 说明：根据banner位置ID得到完整banner信息 
 - 数据库路径：banner => banner-items => img
 - 错误：code:404,errorCode:40004,msg:请求的banner不存在


 - @url：/theme?ids=
 - 说明：根据传入的?ids参数得到对应的主题信息
 - 数据库路径：theme=>img
 - 错误：error_code:10000,msg:ids参数必须是以逗号分隔的多个正整数
 
 
 - @url：/theme/:id
 - 说明：根据传入的单一ID参数得到对应的主题的 详细 信息
 - 数据库路径：product=>img
 - 错误：error_code:10000,msg:id规则错误
 
 
 - @url: /product/recent?count=20
 - 说明：根据传入的数量参数得到对应产品信息
 - 数据库路径：product
 - 错误：error_code:10000,msg:count只能在 1 - 20 之间
 
 
 - @url：/product/by_category?id=3
 - 说明：根据传入的产品分类ID得到分类下的产品信息
 - 数据库路径：product
 - 错误：error_code:50004,msg:产品数据缺失
 
 
 - @url：/product/:id
 - 说明：根据传入的产品ID得到 单一 产品的详细信息
 - 数据库路径：product=>img
 - 错误：error_code:50004,msg:产品数据缺失
 
 
 - @url: /category/all
 - 说明：获取所有的分类信息
 - 数据库路径：category
 - 错误：error_code:50003,msg:暂无分类信息
 
 
 - @url：/token/user
 - 说明：获取用户令牌
 - 数据库路径：user
 - 缓存：key:token	value:微信openID,微信session_key,uid,小程序权限
 - 错误：没有code无法获取Token
 - 错误：获取session_key及openID时异常，微信内部错误
 - 错误：服务器缓存异常,errorCode 10005
 - 错误：微信服务器接口调用失败,errorCode:999,code:400
 
 
 - @url：/token/verify
 - 说明：验证令牌是否存在，并获得缓存中的令牌。
 - 错误：token不允许为空
 
 
 - @url: /address	(GET)
 - 说明：得到用户的地址信息
 - 数据库路径：user=>user_address
 - 错误：error_code:10001,msg:Token已过期或无效Token,code:401
 - 错误：msg:尝试获取的Token变量并不存在
 - 错误：msg:用户地址不存在,errorCode:60001,code:404
 
 
 - @url: /address	(POST)
 - 说明：创建用户的地址信息
 - 数据库路径：user=>user_address
 - 错误：msg：用户不存在,code：404,errorCode：60000
 - 错误：msg：参数中包含有非法的参数名user_id或者uid
 
 
 - @url: /order		(POST)
 - 说明：提交用户订单，生成订单
 - 数据库路径：product,order,order_product
 - 错误：msg：商品不存在，创建订单失败,code：404,errorCode：80000
 - 错误：msg：用户收货地址不存在，下单失败,errorCode：60001
 
 
 - @url： /pay/pre_order
 - 说明：拉起微信支付
 - 数据库路径：order order_product,product
 - 错误：code：404,errorCode：80000,msg：订单不存在，请检查ID
 - 错误：msg：检查UID时必须传入一个被检查的UID
 - 错误：code：401,errorCode：10003,msg：订单与用户不匹配
 - 错误：code：400,errorCode：80003,msg：订单已支付过
 - 错误：code：401,errorCode：10001,msg：Token已过期或无效Token
 
 
 - @url： /pay/notify
 - 说明：接收微信通知


 - @url： /pay/pay_virtual
 - 说明：模拟微信支付
 - 数据库路径：order order_product,product
 - 错误：code：404,errorCode：80000,msg：订单不存在，请检查ID
 - 错误：msg：检查UID时必须传入一个被检查的UID
 - 错误：code：401,errorCode：10003,msg：订单与用户不匹配
 - 错误：code：400,errorCode：80003,msg：订单已支付过
 - 错误：code：401,errorCode：10001,msg：Token已过期或无效Token
 
 
 - @url：/user/saveinfo
 - 说明：更新用户信息
 - 数据库路径：user
 - 错误：msg：提交信息不能为空
 
 
 - @url：/order/by_user
 - 说明：根据UID得到订单的分页信息
 - 数据库路径：order
 - 错误：msg：分页参数必须是正整数
 
 
 - @url：/order/paginate
 - 说明：获取全部订单简要信息（分页）
 - 数据库路径：order
 - 错误：msg：分页参数必须是正整数

 
 - @url：/order/delivery
 - 说明：更新订单为已发货，并调用支付凭证通知用户
 - 错误：code：404,errorCode：80000,msg：订单不存在，请检查ID
 - 错误：code：403,errorCode：80002,msg：未付款或已发货
 
 
 
 
 
 