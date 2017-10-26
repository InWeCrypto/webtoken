<template>
    <div class="header">
        <div class="logo">CryptoBox Admin</div>
        <div class="user-info">
            <el-dropdown trigger="click" @command="handleCommand" v-loading="is_loading" element-loading-text="msg">
                <span class="el-dropdown-link">
                    <img class="user-logo" :src="user.headUrl">
                    {{ user.nickname }}
                </span>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item command="loginout">退出</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
    </div>
</template>
<script>
    import Bus from 'Bus';
    export default {
        data() {
            return {
                user: {
                    nickname: '',
                    headUrl: ''
                },
                is_loading: false,
                msg: '',
                is_updated: false
            }
        },
        mounted: function () {
            let userInfo = JSON.parse(localStorage.getItem('user_info'));
//            if (!userInfo) {
//                this.is_loading = true;
//                this.$router.push('/login');
//                return false;
//            }

            let user = userInfo.user;
            this.user.nickname = user.nickname ? user.nickname : user.name;
            this.user.headUrl = user.img  ? user.img : '../../../static/img/img.jpg';
//            //添加全局token
//            this.$axios.defaults.headers.common['ct'] = userInfo.ct;

            //监听总线通信
            Bus.$on('updateInfo', userInfo => {
                this.user = userInfo;
            })
        },
        methods: {
            handleCommand(command)
            {
                if (command == 'loginout') {
                    localStorage.removeItem('user_info');
                    localStorage.removeItem('user_token');
                    this.$router.push('/login');
                }
            }
        }
    }
</script>
<style scoped>
    .header {
        position: relative;
        box-sizing: border-box;
        width: 100%;
        height: 70px;
        font-size: 22px;
        line-height: 70px;
        color: #fff;
    }

    .header .logo {
        float: left;
        width: 250px;
        text-align: center;
    }

    .user-info {
        float: right;
        padding-right: 50px;
        font-size: 16px;
        color: #fff;
    }

    .user-info .el-dropdown-link {
        position: relative;
        display: inline-block;
        padding-left: 50px;
        color: #fff;
        cursor: pointer;
        vertical-align: middle;
    }

    .user-info .user-logo {
        position: absolute;
        left: 0;
        top: 15px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .el-dropdown-menu__item {
        text-align: center;
    }
</style>
