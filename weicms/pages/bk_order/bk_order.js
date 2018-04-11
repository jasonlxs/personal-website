// bk_order.js
var app=getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    index:0,
    array:['微信支付','支付宝支付','银联支付'],
    item:[
      { count: 1, product: { name: '深入浅出Node.js', price: 10.00}}
    ],
    //购物车总计
    total:0,
    //购物车ID
    id:0,
    //提示信息动态
    hint: '',
    shint: 'display:none',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    //接收购物车id
    console.log(options.id);
    var me=this;
    wx.setNavigationBarTitle({
      title: '生成订单'
    })
    //购物车需付款条目
     var uid = wx.getStorageSync('uname');
     if(uid){
       app.sessRequest(app,{
           url: app.globalData.url+'mobile/cart/cart_pay',
           data:{
             id: options.id
           },
           success:function(res){
             me.setData({
               item: res.data.cart,
               total: res.data.total,
               id: options.id
             });
           },
           fail: {},
           complete: {}
       });
     }
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

  //支付选择器
  bindPickerChange: function (e) {
    console.log('picker发送选择改变，携带值为', e.detail.value)
    this.setData({
      index: e.detail.value
    })
  },

  //订单提交并生成
    formSubmit:function(e){
      var me=this;
      app.sessRequest(app, {
        url: app.globalData.url + 'mobile/ordermake',
        data: {
          id: me.data.id
        },
        success: function (res) {
          console.log(res.data);
          if (res.data.status == 1) {
            app.tip(me, res.data.message);
            setTimeout(function () {
              wx.showToast({
                title: '加载中',
                icon: 'success',
                duration: 1000
              })
            }, 1000);
            setTimeout(function(){
              //跳转到订单详情页面
              wx.redirectTo({
                url: '../bk_ordermake/bk_ordermake?oid=' + res.data.oid
              })  
            },2000);
          } else {
            app.tip(me, res.data.message);
          }
        },
        fail: {},
        complete: {}
      });
    },
    //设置收货地址
    setaddr:function(){
      var me=this;
      wx.navigateTo({
        url: '../bk_address/bk_address?id='+me.data.id
      })
    }
})