import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

export default new Router({
     //mode: 'history',
     mode: 'hash',
    routes: [
        {
            path: '/',
            redirect: '/login'
        },
        {
            path: '/login',
            component: resolve => require(['../components/provider/Login.vue'], resolve)
        },
        {
            path: '/index',
            component: resolve => require(['../components/common/Home.vue'], resolve),
            children: [
                {
                    path: '/admin',
                    component: resolve => require(['../components/provider/Admin.vue'], resolve)
                },
                {
                    path: '/role',
                    component: resolve => require(['../components/provider/Role.vue'], resolve)
                },
                {
                    path: '/user',
                    component: resolve => require(['../components/provider/User.vue'], resolve)
                },
                {
                    path: '/config',
                    component: resolve => require(['../components/provider/Config.vue'], resolve)
                },
                {
                    path: '/wallet-category',
                    component: resolve => require(['../components/provider/WalletCategory.vue'], resolve)
                },
                {
                    path: '/gnt-category',
                    component: resolve => require(['../components/provider/GntCategory.vue'], resolve)
                },
                {
                    path: '/monetary-unit',
                    component: resolve => require(['../components/provider/MonetaryUnit.vue'], resolve)
                },
                {
                    path: '/ico-category',
                    component: resolve => require(['../components/provider/IcoCategory.vue'], resolve)
                },
                {
                    path: '/article',
                    component: resolve => require(['../components/provider/Article.vue'], resolve)
                },
                {
                    path: '/market-category',
                    component: resolve => require(['../components/provider/MarketCategory.vue'], resolve)
                },
                {
                    path: '/custom',
                    component: resolve => require(['../components/provider/Custom.vue'], resolve)
                },
                {
                    path: '/wallet',
                    component: resolve => require(['../components/provider/Wallet.vue'], resolve)
                },
                {
                    path: '/wallet-order',
                    component: resolve => require(['../components/provider/WalletOrder.vue'], resolve)
                },
                {
                    path: '/ico-order',
                    component: resolve => require(['../components/provider/IcoOrder.vue'], resolve)
                },
                {
                    path: '/ico',
                    component: resolve => require(['../components/provider/Ico.vue'], resolve)
                },
            ]
        },
    ]
})
