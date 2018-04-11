// pages/bk_person/bk_person.js
var app=getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
     user:'lxsptlxs@163.com111111',
     money:10.00,
     nickname:'xxx'
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.setNavigationBarTitle({
      title: '个人中心'
    })
    var me = this;
    var user='';
    var value = wx.getStorageSync('uname');
    console.log(value);
     if(value){
         //请求查询用户信息
         app.sessRequest(app,{
            url:app.globalData.url+'mobile/bkperson',
            data:{
              uname: value
            },
            success:function(res){
               console.log(res.data);
               if(res.data.email==''){
                  user=res.data.phone;
               }else{
                 user = res.data.email;
               }
               me.setData({
                  user:user,
                  nickname:res.data.nickname,
               });
            },
            fail:function(){

            }
         });
     }else{
       wx.showToast({
         title: '检测到您没有登录，正在为您跳转',
         icon: 'success',
         duration: 2000
       })
       setTimeout(function () {
         wx.navigateTo({
           url: '../bk_login/bk_login'
         })
       }, 2000);
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
  
  }
})