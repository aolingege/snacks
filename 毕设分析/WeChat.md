####wechat开放接口

 - 名称：登录接口
 - 类型：小程序
 - 作用：获取登录用户的登录凭证code值
 - @action：wx.login(OBJECT)
 - Object{code:"the code"  errMsg:"login:ok"}
 
 - 名称：AJAX请求
 - 类型：小程序
 - 作用：发起AJAX请求
 - @action：wx.request(OBJECT)
 
 - 名称：添加地址
 - 类型：小程序
 - 作用：编辑或者添加一个地址信息
 - @action：wx.chooseAddress(OBJECT)
 
 - 名称：弹出框
 - 类型：小程序
 - 作用：弹出个对话框
 - @action：wx.showModal(OBJECT)
 
 - 名称：拉起支付
 - 类型：小程序
 - 作用：通过预订单拉起付款
 - @action：wx.requestPayment(OBJECT)
 
 - 名称：获取用户信息
 - 类型：小程序
 - @action：wx.getUserInfo(OBJECT)