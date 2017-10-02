import Vue from "vue";
import App from "./App";
import router from "./router";
import axios from "axios";
import ElementUI from "element-ui";
// import 'element-ui/lib/theme-default/index.css';    // 默认主题
import "../static/css/theme-green/index.css"; // 浅绿色主题
import "babel-polyfill";
Vue.use(ElementUI);
Vue.prototype.$axios = axios;
// let root = 'http://webtoken.dev';
// let root = 'http://120.77.208.222:8000';
// let root = 'https://wallet.unichain.io';
// Vue.prototype.$rootPath = root;
// Vue.prototype.$apiPath = root + '/back';
// Vue.prototype.$apiPath = window.location.host;
Vue.prototype.$apiPath = '/back';
// axios.defaults.baseURL = root + '/back';
axios.defaults.baseURL = '/back';
router.beforeEach((to, from, next) => {
    if (!to.path.match('/login')) {
        if (!localStorage.getItem('user_info')) {
            next('/login');
        } else {
            //设置ct
            axios.defaults.headers.common['ct'] = JSON.parse(localStorage.getItem('user_info')).token;
            next();
        }
    } else {
        next();
    }
});
// 响应拦截器
axios.interceptors.response.use(function (response) {
    // 对响应数据做点什么
    if (response.data.code == 4001 || response.data.code == 4009) {
        //跳转登录
        localStorage.clear();
        router.replace({
            path: '/login'
        });
    }
    return response;
}, function (error) {
    // 对响应错误做点什么
    return Promise.reject(error);
});
new Vue({
    router,
    render: h => h(App)
}).$mount('#app');