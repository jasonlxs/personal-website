// bk_list.js
var app=getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
     item:[
       { id: 1, name: 'phpmysql', summary: 'phpmysql', price: 89 }
     ],
     imageUrl: app.globalData.imgurl,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var me=this;
    app.sessRequest(app, {
      url: app.globalData.url + 'mobile/bklist',
      data: {
        id: options.id
      },
      success: function (res) {
        console.log(res.data);
        me.setData({
          item: res.data
        });
      },
      fail: function () { },
      complete: {}
    })
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

  //返回
  retOrd:function(){
    wx.switchTab({
      url: '../bk_home/bk_home'
    })
  }
})