//index.js
//获取应用实例
var app = getApp()
Page({
  data: {
    'img':"../../images/logo.png",
    'title':"广东人生塑造有限公司",
    'content':"世界观、人生观、价值观。",
    'addr':"广东人生塑造有限公司",
    'tel':"13202878012",
    'mail':"284533763@qq.com"
  },
  //事件处理函数
  bindViewTap: function() {
    wx.navigateTo({
      url: '../logs/logs'
    })
  },
   viewTap: function() {
     console.log('view tap')
  },
  onLoad: function () {
    console.log('onLoad')
    var that = this
    //调用应用实例的方法获取全局数据
    app.getUserInfo(function(userInfo){
      //更新数据
      that.setData({
        userInfo:userInfo
      })
    })
  }
  
})
