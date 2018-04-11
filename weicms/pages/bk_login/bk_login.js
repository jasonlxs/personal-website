// login.js
var app = new getApp();


Page({

  /**
   * 页面的初始数据
   */
  data: {
    verify: '',
    //提示信息动态
    hint: '',
    shint: 'display:none',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var me=this;
    if(options.id){
      me.setData({
        id: options.id
      });
    }
    wx.setNavigationBarTitle({
      title: '登录'
    })
    var session_id = app.getSessionidFrom();
    //设置请求验证码
    this.setData({
      verify: app.globalData.url
      + 'service/Validatecode/create?seid='
      + session_id,
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
  //登录表单提交
  formSubmit:function(e){
    var me = this;
    var event = e.detail.value;
    if (event.code == '') {
        app.tip(me, '请输入验证码');
        return;
      }
    if (event.uname == '') {
      app.tip(me, '请输入账号');
      return;
    }
    if (event.upwd.length <6) {
      app.tip(me, '密码必须大于6位');
      return;
    }

     //发起网络请求
    app.sessRequest(app,
    {
        url: app.globalData.url + 'service/Memberlogin/login',
        data: {
          uname: event.uname,
          upwd: event.upwd,
          code: event.code,
        },
        success: function (res) {
          try{
              console.log(res.data);
              if(res.data.status==14){
                wx.setStorageSync('uname', res.data.uname);
                app.tip(me, res.data.message);
              }else{
                app.tip(me, res.data.message);
                return;
              }
            
              
              //刷新上一页的用户名显示
              var pages = getCurrentPages();
              if (pages.length > 1) {
                //上一个页面实例对象
                var prePage = pages[pages.length - 2];
                //关键在这里
                if(me.data.id){
                  prePage.onLoad({ id: me.data.id});
                }else{
                  prePage.onLoad();
                }
                
              }
          
              //返回上一页
              setTimeout(function () {
                wx.navigateBack({
                  delta: 1
                })
                },2000);
          }catch(e){
            app.tip(me, '登录超时，请重新输入');
          }
        },
        fail: function () {
          app.tip(me, '请求注册失败');
        },
        complete:{}
      });
    },

  //刷新验证码
  update_verify: function () {
    var session_id = app.getSessionidFrom();
    this.setData({
      verify: app.globalData.url
      + 'service/Validatecode/create?seid='
      + session_id + '&&' + Math.random(0, 1)
    });
  }
})