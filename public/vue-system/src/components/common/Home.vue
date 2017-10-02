<template>
    <div class="wrapper" v-loading="is_loading" element-loading-text="处理中...">
        <v-head></v-head>
        <v-sidebar></v-sidebar>
        <div class="content">
            <transition name="move" mode="out-in">
                <router-view @changeLoading="changeLoading"></router-view>
            </transition>
        </div>
    </div>
</template>

<script>
    import vHead from './Header.vue';
    import vSidebar from './Sidebar.vue';
    export default {
        components: {
            vHead, vSidebar
        },
//        beforeRouteEnter (to, from, next) {
//            if (!localStorage.getItem('user_info')) {
//                next('login');
//            }
//            next(true);
//        },
        data: function () {
            return {
                is_loading: false,
            }
        },
        mounted:function () {
            this.$on('changeLoading',msg=>{
                this.is_loading = !this.is_loading;
                this.loading_msg = msg;
            });
        },
        methods: {
            changeLoading(){
                this.is_loading = !this.is_loading;
            }
        }
    }
</script>
