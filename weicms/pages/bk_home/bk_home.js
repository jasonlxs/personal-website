// bk_home.js
var app=getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    array: ['中国'],
    index:0,
    user:0,
    item:[
      {id:3,cname:'woowwo'}
    ],
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
     var me=this;
    //设置用户名
    me.setData({
       user:wx.getStorageSync('uname'),
    })
    var cateData = wx.getStorageSync('catedata')
    if (cateData==''){
      //请求一级书籍类别
      app.sessRequest(app, {
        url: app.globalData.url + 'mobile/bkcate',
        data: {},
        success: function (res) {
          console.log(res.data);
          me.setData({
            array: res.data.cate,
            item: res.data.child
          });
          wx.setStorageSync('catedata', res.data)
        },
        fail: function () { },
        complete: {}
      })
    }else{
      me.setData({
        array: cateData.cate,
        item: cateData.child
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
  //选择器值的改变
  bindPickerChange: function (e) {
    var me=this;
    var id = e.detail.value;
    console.log('picker发送选择改变，携带值为', id)
    app.sessRequest(app, {
      url: app.globalData.url + 'mobile/bkcate/change',
      data: {
        id: id
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
    this.setData({
      index: e.detail.value
    })
  },

  //测试
  cs: function () {
    wx.showActionSheet({
      itemList: ['主页', '个人中心'],
      success: function (res) {
        console.log(res.tapIndex)
        if (res.tapIndex == 1) {
          wx.redirectTo({
            url: '../bk_person/bk_person'
          })
        }
      },
      fail: function (res) {
        console.log(res.errMsg)
      }
    })
  }


})