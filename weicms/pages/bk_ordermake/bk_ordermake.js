// pages/bk_orderItem/bk_orderItem.js
var app=getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    item: [
      { count: 1, product: { name: '深入浅出Node.js', price: 10.00 } }
    ],
    //图片基础路径
    imgurl: app.globalData.imgurl,
    //订单信息初始化
    total:0,
    length:0,
    status:'未支付',
    order_no:'E23423we',
    //提示信息动态
    hint: '',
    shint: 'display:none',
    //订单状态按钮判断
    pay_status:'',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.setNavigationBarTitle({
      title: '付款'
    })
    var me=this;
    //请求订单信息
    app.sessRequest(app, {
        url: app.globalData.url + 'mobile/ordermake/orderMes',
        data: {
          id: options.oid
        },
        success: function (res) {
          console.log(res.data);
          if(res.data.status==1){
            var status = res.data.orderSta;
            var pay_status='';
             if (status!='未付款'){
               pay_status='display:none';
             }
              me.setData({
                item: res.data.cart,
                total: res.data.total,
                length: res.data.length,
                order_no:res.data.order_no,
                status:status,
                pay_status:pay_status,
              });
              app.tip(me, res.data.message);
          }else{
              app.tip(me,res.data.message);
          }
        },
        fail: {},
        complete: {}
    });
    
    
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
    wx.setNavigationBarTitle({
      title: '付款'
    })
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  },
  
  // 付款
  formSubmit:function(e){
    console.log(e);
     //获取发起微信支付的凭证
     

    //  //发起微信支付
    // wx.requestPayment({
    //   'timeStamp': '',
    //   'nonceStr': '',
    //   'package': '',
    //   'signType': 'MD5',
    //   'paySign': '',
    //   'success': function (res) {
    //     //支付成功的请求
    //   },
    //   'fail': function (res) {
    //     //支付失败的请求 
    //   }
    // })


     
  }
})