//index.js
//获取应用实例
Page({
  data: {
     
  },
  onLoad: function(option){
    
    var that = this
    wx.request({
      url: 'http://localhost/mobile/detail',
      data:{
        aid:option.id
      },
      success: function (res) {
        console.log(res.data);
        that.setData({
          info: res.data
        });
      }
    })
  }
  
})
