// register.js
//获取应用实例
var app = getApp();
//获取缓存session_id
var session_id = app.getSessionidFrom();

//页面函数
Page({
  /**
   * 页面的初始数据
   */
  data: {
     //注册方式动态效果
     mobile:'',
     mail:'display:none',
     //提示信息动态
     hint:'',
     shint:'display:none',
     verify: app.globalData.url 
     + 'service/Validatecode/create?seid='
     + session_id,
     //发送验证码动态效果
     send:'',
     re_send:1,
     num:10,
     //邮箱手机属性名
     phoneAttr:'phone',
     emailAttr:'',
     //手机号码默认空，用于判断
     phone:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.setNavigationBarTitle({
      title: '注册'
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

  //注册方式切换动画
  register_mobile:function(e){
      this.setData({
        mobile:'display:block',
        mail:'display:none',
        phoneAttr:'phone',
        emailAttr:''
      });
  },
  register_mail:function(e){
    this.setData({
      mobile: 'display:none',
      mail: 'display:block',
      phoneAttr: '',
      emailAttr: 'mail'
    });
  },
  
  
  //注册提交
  formSubmit:function(e){
    var me=this;
    var event = e.detail.value;
    var pattern=/\d+/;
    
    //手机注册验证
    if (event.mail==undefined){
      if (event.code == '') {
        app.tip(me, '请输入验证码');
        return;
      }
      if (event.phone.length != 11 || event.phone[0]!='1') {
        app.tip(me, '手机号不为空或长度等于11位或首位等于1');
        return;
      }
      if (event.upwd.length < 6 || event.cf_upwd.length < 6) {
        app.tip(me, '密码不少于6位');
        return;
      }
      if (event.upwd != event.cf_upwd){
        app.tip(me, '密码不一致');
        return;
      }
      var uname = event.phone;
      var upwd = event.upwd;
      var code = event.code;
    }
    //邮箱注册验证
    if (event.phone == undefined){
        if (event.m_code == '') {
          app.tip(me, '请输入验证码');
          return;
        }
        if (event.mail == '') {
          app.tip(me, '邮箱不为空，请输入');
          return;
        }
        if (event.m_upwd.length < 6 || event.mcf_upwd.length < 6) {
          app.tip(me, '密码不少于6位');
          return;
        }
        if (event.m_upwd != event.mcf_upwd) {
          app.tip(me, '密码不一致');
          return;
        }
        var uname = event.mail;
        var upwd = event.m_upwd;
        var code = event.m_code;
    }
    //注册请求
    wx.request({
      url: app.globalData.url + 'service/Memberregister/Memberresgister',
      method: 'POST',
      data: {
        uname:uname,
        upwd:upwd,
        code:code,
      },
      header: {
        'content-type': 'application/json'
      },
      
      success: function (res) {
         if (res.data == null) { app.tip(me, '服务器端错误'); }
         if (res.data.status == 7 || res.data.status == 9){
           app.tip(me,res.data.message);
           //跳转回主页
           wx.switchTab({
             url: '../bk_home/bk_home'
           })
         }else{
           app.tip(me, res.data.message);
         }
      },
      fail: function () {
        app.tip(me, '请求注册失败');
      }
    }); 
  },

  //获取手机号码
  mobileInputEvent:function(e){
     this.setData({
        phone:e.detail.value
     });
  },

  //给手机发送验证码
  send_verify:function(){
        var me=this;
        var num=10;
        if(me.data.phone=''){
            app.tip(me,'手机号不能为空');
            return;
        }
        //请求发送验证码
        wx.request({
          url: app.globalData.url + 'service/Sendcode/sendPhoneCode',
          data: {
              phone:me.data.phone
          },
          header: {
            'content-type': 'application/json'
          },
          success: function (res) { 
            if (res.data == null) { app.tip(me, '服务器端错误');}
            if (res.data.status != 1){ 
              app.tip(me, res.data.message);
            }else{
              app.tip(me, '发送成功');
              //设置发送时间倒计时
                me.setData({
                  send: 1,
                  re_send: ''
                });

                var interval = setInterval(function () {
                  num = --num;
                  me.setData({ num: num })
                  if (num == 0) {
                    clearInterval(interval);
                    me.setData({
                      send: '',
                      re_send: 1
                    });
                  };
                }, 1000);
            }
          },
          fail:function(){
            app.tip(me, '请求发送手机验证失败');
          }
        });
        
         
  },
   //刷新验证码
   update_verify:function(){
     this.setData({
       verify: app.globalData.url 
       + 'service/Validatecode/create?seid='
       + session_id+'&&'+Math.random(0,1)
     });
   }
})

