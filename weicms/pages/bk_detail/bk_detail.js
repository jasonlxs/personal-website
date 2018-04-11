// bk_detail.js
var app=getApp();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    //书籍图片轮播
    imgUrls: [
      '../../images/node.png',
      'http://img06.tooopen.com/images/20160818/tooopen_sy_175866434296.jpg',
      'http://img06.tooopen.com/images/20160818/tooopen_sy_175833047715.jpg'
    ],
    indicatorDots: true,
    autoplay: true,
    interval: 2000,
    duration: 1000,
    //产品详情
    item: { name:'phpmysql',price:89,summary:'xxxx' },
    //购物车数量
    num:0,
    //书籍ID
    bookId:'1',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
      var me=this;
      var num=0;
      //接受某本书籍ID
      me.setData({
        bookId:options.id
      });
       //获取某一产品信息
      app.sessRequest(app, {
        url: app.globalData.url + 'mobile/bkdetail',
        data: {
          id: options.id
        },
        success: function (res) {
          //console.log(res.data);
          me.setData({
            imgUrls: res.data.pimages,
            item: res.data.product
          });
        },
        fail: function () { },
        complete: {}
      })

     
      var value=wx.getStorageSync('uname')
      var cart = wx.getStorageSync('cart');
      //console.log(cart);
      if(value){
        //缓存购物车和数据购物车同步
        if(cart!=''){
          console.log(JSON.stringify(cart));
          app.sessRequest(app, {
            url: app.globalData.url + 'mobile/cart/syncCart',
            data: {
              cart: JSON.stringify(cart)
            },
            success: function (res) {
              console.log(res.data);
              //wx.removeStorageSync('cart');
            },
            fail: function () {
              console.log('购物车数据同步失败');
              return;
            },
            complete: {}
          });
        }
        //统计购物车数量(包括在线离线)
        app.sessRequest(app, {
          url: app.globalData.url + 'mobile/cart/total',
          data: {},
          success: function (res) {
            //console.log(res.data);
            me.setData({
              num: res.data
            });
          },
          fail: function () { },
          complete: {}
        })  
      }else{
          try {
            var value = wx.getStorageSync('cart');
            console.log(value);
            if (value) {
              for (var prop in value) {
                num = value[prop] + num;
              }
            } 
            me.setData({
              num: num
            });
          } catch (e) {
            console.log('统计购物车数量错误');
          }
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

  //添加购物车
  addCart:function(){
     var me=this;
     var find;
     var bookId = me.data.bookId;
     var bookNum = me.data.num;
     var count={};
     count[bookId]=bookNum+1;


     try{
       
       var uuid = wx.getStorageSync('uname');

       if(uuid){
       //在线购物车添加
         app.sessRequest(app,{
          url: app.globalData.url+'mobile/cart/addCart',
          data:{
            pid:bookId
          },
          success:function(res){
            console.log(res.data);
            console.log('添加成功');
          },
          fail:function(){},
          complete: {}
        })
        //提示数量加+1
         me.setData({
           num: me.data.num + 1
         })
       }else{
          //离线购物车添加
          try {
            var value = wx.getStorageSync('cart')
            if (value) {
              for (var prop in value) {
                if (prop == bookId) {
                  //查找到值，就更新数据
                  value[prop] += 1;
                  find = 1;
                }
              }
              //查找不到值，就插入新记录
              if (find != 1) {
                value[bookId] = 1;
              }
              //将数据更新到缓存
              wx.setStorageSync('cart', value);
            } else {
              wx.setStorageSync('cart', count);
            }

            //提示数量加+1
            me.setData({
              num: me.data.num + 1
            })
          } catch (e) {
            // Do something when catch error
            console.log('获取购物车数量失败');
          }
       }
     }catch(e){
       console.log(e);
     }
  },

  //查看购物车
  amount:function(){
        var me=this;
        var value = wx.getStorageSync('uname');
        
     
        if(value){
            //过场提示
            wx.showToast({
              title: '加载中...',
              icon: 'success',
              duration: 2000
            })
            //跳转到购物车页面
            setTimeout(function () {
              wx.navigateTo({
                url: '../bk_cart/bk_cart',
              })},2000);
        }else{
          wx.showToast({
            title: '请先登录!跳转',
            icon: 'success',
            duration: 2000
          })
          setTimeout(function () {
            wx.navigateTo({
              url: '../bk_login/bk_login?id=' + me.data.bookId,
            })
          }, 2000);
        }   
  },
})