//list.js
//获取应用实例
var app = getApp()
Page({
  data: {
       listarr:[],
  },
  //页面自动加载
  onLoad: function () {
    var that = this
    wx.request({
      url: 'http://localhost/mobile/lists', //仅为示例，并非真实的接口地址
      data: {
      },
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        console.log('list:'+res.data);
        that.setData({
          listarr:res.data
        });
      }
    });
  }
  
})
