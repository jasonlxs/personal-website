// pages/bk_newaddr/bk_newaddr.js
var app=getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    //地区选择器默认值
    region: ['广东省', '广州市', '海珠区'],

    //提示tip
    shint:'display:none',
    hint:'',

    //修改地址数据初始化
    contacts:'',
    phone:'',
    postcode:'',
    address:'',
    //辨认新增修改标志
    id:'',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var me=this;
    if(options.id){
      wx.setNavigationBarTitle({
        title: '修改收货地址'
      })
      //请求修改数据
      app.sessRequest(app, {
        url: app.globalData.url + 'mobile/bkaddress/modAddress',
        data: {
           id: options.id
        },
        success: function (res) {
          console.log(res.data);
          me.setData({
            contacts: res.data.addr.contacts,
            phone: res.data.addr.phone,
            postcode: res.data.addr.postcode,
            address: res.data.addr.address,
            region:res.data.region,
            id: options.id
          });

        },
        fail: function () { }
      });

      
    }else{
      wx.setNavigationBarTitle({
        title: '新增收货地址'
      })
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

  //新增地址
  formSubmit:function(e){
      var me=this;
      // console.log(e);
      var event = e.detail.value;

      //数据验证
      if (event.contacts==''){
          app.tip(me,'联系人不能为空');  
          return;
      }
      if (event.phone.length!=11) {
        app.tip(me, '联系电话不能空或不少与11位');
        return;
      }
      if (event.postcode.length!=6) {
        app.tip(me, '邮编不能空或不少与5位');
        return;
      }
      if (event.textarea == '') {
        app.tip(me, '收货详细地址不能空');
        return;
      }

      //组合省市区
      var str='';
      var area=me.data.region;
      for(var i=0;i<area.length;i++){
           str=str+area[i]+'-';
      }

      //判断新增还是修改
     if(me.data.id==''){
       //新增
      app.sessRequest(app, {
        url: app.globalData.url + 'mobile/bkaddress/addAddress',
        data: {
          contacts: event.contacts,
          phone: event.phone,
          postcode: event.postcode,
          area:str,
          address: event.textarea,
        },
        success: function (res) {
          console.log(res.data);
          if(res.data.status==1){
            app.tip(me, res.data.message);
            //跳转地址列表页面
            setTimeout(function () {
              wx.redirectTo({
                 url: '../bk_address/bk_address',
               })
            }, 2000);
          }else{
            app.tip(me,res.data.message);
          }
        },
        fail: function () {},
      });
     }else{
       //修改
       console.log(event);
       app.sessRequest(app, {
         url: app.globalData.url + 'mobile/bkaddress/modAddress',
         data: {
           contacts: event.contacts,
           phone: event.phone,
           postcode: event.postcode,
           area: str,
           address: event.textarea,
           id:me.data.id
         },
         success: function (res) {
           console.log(res.data);
           if (res.data.status == 1) {
             app.tip(me, res.data.message);
             //跳转地址列表页面
             setTimeout(function () {
               wx.redirectTo({
                 url: '../bk_address/bk_address',
               })
             }, 2000);
           } else {
             app.tip(me, res.data.message);
           }
         },
         fail: function () { },
       });
     }
  },


  //省市区选择器
  bindRegionChange:function(e){
    var me=this;
    me.setData({
      region:e.detail.value
    });
  }
})