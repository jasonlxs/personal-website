//app.js
App({
  onLaunch: function () {
    var me=this;
    //调用API从本地缓存中获取数据
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs);
    //获取会话ID
      wx.request({
        url: me.globalData.url + '/mobile/Sessid/getId',
        success: function (res) {
          console.log('请求会话ID成功');
          try {
            wx.setStorageSync('sess_id', res.data)
            console.log('设置会话缓存成功,ID如下:');
            console.log(wx.getStorageSync('sess_id'));
          } catch (e) {
            console.log('设置会话缓存失败');
          }
        },
        fail: function () {
          console.log('fail');
        }
      })
  },
  getUserInfo:function(cb){
    var that = this
    if(this.globalData.userInfo){
      typeof cb == "function" && cb(this.globalData.userInfo)
    }else{
      //调用登录接口
      wx.login({
        success: function () {
          wx.getUserInfo({
            success: function (res) {
              that.globalData.userInfo = res.userInfo
              typeof cb == "function" && cb(that.globalData.userInfo)
            }
          })
        }
      })
    }
  },
  globalData:{
    userInfo:null,
    url:'http://localhost/',
    imgurl:'http://localhost/static/uploads/'
  },
  //请求获取一个sessionid，并存于数据缓存中
  //getSessionid函数因为调用wx.login()造成函数加载慢,竟然造成app主程序的onlaunch加载速度比
  //其它页面的加载速度还慢
  getSessionid:function(){
    var me=this;
    wx.login({
      success: function (res) {
        if (res.code) {
          //发起网络请求
          wx.request({
            url: me.globalData.url + 'mobile/Sessid/getId',
            data: {
              code: res.code
            },
            success: function (res) {
              console.log('请求会话ID成功');
              try {
                wx.setStorageSync('sess_id', res.data)
                console.log('设置会话缓存成功,ID如下:');
                console.log(wx.getStorageSync('sess_id'));
              } catch (e) {
                console.log('设置会话缓存失败');
              }
            },
            fail: function () {
              console.log('fail');
            }
          })
        } else {
          console.log('获取用户登录态失败！' + res.errMsg)
        }
      }
    }); 
  },
  //从缓存中获取session_id
  getSessionidFrom:function(){
    try {
      var value = wx.getStorageSync('sess_id')
      if (value) {
        return value;
      }
    } catch (e) {
        return 0;
    }  
  },
  //提示信息
  tip:function(me, str) {
    me.setData({
      shint: 'display:block',
      hint: str,
    });
    setTimeout(function () { me.setData({ shint: 'display:none' }); }, 2000);
  },
  //附带session_id的请求方法
  sessRequest: function (me,{url, data, success, fail, complete, method = "POST"}){
      var me=me;
      var session_id = me.getSessionidFrom();
      if(session_id==''){
        me.getSessionid();
        session_id = me.getSessionidFrom();
      }
      //console.log(session_id);
      wx.request({
        url: url,
        method:method,
        data:data,
        header: {
          'content-type': 'application/json',
          'Cookie': 'PHPSESSID=' + session_id
        },
        success:success,
        fail: fail,
        complete: complete
      })
  },
})