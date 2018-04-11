// pages/bk_address/bk_address.js
var app=getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
     items:[],

     //提示tip
     shint: 'display:none',
     hint:'',
     //购物车id
     bid:'',
     //选择收货地址ID
     id:'',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var me = this;
    wx.setNavigationBarTitle({
      title: '收货地址'
    })
    //保存购物车id
    me.setData({
      bid: options.id
    });
     //请求用户的收货地址
    app.sessRequest(app, {
      url: app.globalData.url + 'mobile/bkaddress',
      data: {},
      success: function (res) {
        console.log(res.data);
        if(res.data.status==1){
          me.setData({
            items: res.data.address,
            id: res.data.def_id
          });
        }else{
          app.tip(me,res.data.message);
        }
      },
      fail: function () {}
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
  //改变选项时获取值
  radioChange:function(e){
    console.log(e);
     var me=this;
     me.setData({
       id:e.detail.value
     });
  },
  //返回生成订单页面
  retOrd:function(){
    var me=this;
    console.log(me.data.bid);
    wx.redirectTo({
      url: '../bk_order/bk_order?id='+me.data.bid,
    })
  },
  //设置默认
  setDef:function(){
    var me = this;
      app.sessRequest(app, {
        url: app.globalData.url + 'mobile/bkaddress/setDefault',
        data: {
          id:me.data.id
        },
        success: function (res) {
          app.tip(me,res.data);
        },
        fail: function () { }
      });
    
  },
  //新增地址
  setAdd:function(){
       wx.navigateTo({
         url: '../bk_newaddr/bk_newaddr'
       })
  },
  //修改地址
  setMod:function(){
    var me = this;
    wx.navigateTo({
      url: '../bk_newaddr/bk_newaddr?id=' + me.data.id,
    })
  },
  //删除地址
  setDel:function(){
    var me=this;
    // console.log(me.data.id);
    app.sessRequest(app, {
      url: app.globalData.url + 'mobile/bkaddress/delAddress',
      data: {
        id: me.data.id
      },
      success: function (res) {
        app.tip(me, res.data);
        setTimeout(function(){
          app.sessRequest(app, {
            url: app.globalData.url + 'mobile/bkaddress',
            data: {},
            success: function (res) {
              console.log(res.data);
              if (res.data.status == 1) {
                me.setData({
                  items: res.data.address,
                  id: res.data.def_id
                });
              } else {
                app.tip(me, res.data.message);
                me.setData({
                  items:'' 
                });
              }
            },
            fail: function () { }
          });
        },2000);
      },
      fail: function () {}
    });
  }
})