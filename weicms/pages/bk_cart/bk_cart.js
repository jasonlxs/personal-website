// bk_billing.js
var app=getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    items: [
      
    ],
    imgurl:app.globalData.imgurl,
    //小提示
    shint:'display:none',
    hint:'',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var me = this;
    wx.setNavigationBarTitle({
      title: '购物车'
    })
   //获取购车数据
    me.getCart();
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
  //获取购物车数据
  getCart:function(){
    var me=this;
    app.sessRequest(app, {
        url: app.globalData.url + 'mobile/cart',
        data: {
        },
        success: function (res) {
          me.setData({
            items: res.data.cart_pro
          });
          wx.setStorage({
            key: 'carted',
            data: res.data
          })
          return;
        },
        fail: function () {
          console.log('请求购物车数据失败');
          return;
        },
        complete: {}
      });
  },
  //调整购物车中产品数量
  saveCount:function(e){
      var me =this;
      var id=e.currentTarget.id;
      var count=e.detail.value;
      var cart='{"'+id+'":'+count+'}';
     
      // console.log(cart);
      //请求修改购物车数量
      app.sessRequest(app, {
        url: app.globalData.url + 'mobile/cart/modCart',
        data: {
          cart:cart
        },
        success: function (res) {
          console.log(res.data);
          app.tip(me, res.data.message);
          if(res.data.status==3){
            me.getCart();
          }
        },
        fail: function () { },
        complete: {}
      })
  },
  // 结算操作
  formSubmit:function(e){
    var me=this;
    if(me.data.items==''){
        app.tip(me,'购物车为空');
        return;
    }
    try{
       wx.redirectTo({
          url: '../bk_order/bk_order?id=' + e.detail.value.checkbox
        }) 
    }catch(e){
        console.log('结算失败');
    }
    
  },

  // 删除付款条目
  del:function(){

  },
  
})