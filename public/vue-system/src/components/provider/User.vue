<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-date"></i>用户信息</el-breadcrumb-item>
                <el-breadcrumb-item>修改</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="form-box">
            <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="80px" class="demo-ruleForm">
                <el-form-item label="登录账号">
                    <el-input v-model="ruleForm.name"></el-input>
                </el-form-item>
                <el-form-item label="昵称" prop="nickname">
                    <el-input v-model="ruleForm.nickname"></el-input>
                </el-form-item>
                <el-form-item label="登录密码" prop="old_password">
                    <el-input type="password" placeholder="登录密码" v-model="ruleForm.old_password"></el-input>
                </el-form-item>
                <el-form-item label="新密码" prop="password">
                    <el-input type="password" placeholder="新密码" v-model="ruleForm.password"></el-input>
                </el-form-item>
                <el-form-item label="确认密码" prop="password_confirmation">
                    <el-input type="password" placeholder="确认密码" v-model="ruleForm.password_confirmation"></el-input>
                </el-form-item>
                <el-form-item label="上传头像">
                    <el-upload
                            class="avatar-uploader"
                            :action="$apiPath+'/upload'"
                            :show-file-list="false"
                            :on-success="handleAvatarSuccess"
                            :before-upload="beforeAvatarUpload"
                            :headers="headers"
                            :with-credentials="true">
                        <img v-if="ruleForm.img" :src="ruleForm.img" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-button type="primary" @click="onSubmit('ruleForm')">提交</el-button>
                <el-button>取消</el-button>
                </el-form-item>
            </el-form>
        </div>

    </div>
</template>

<script>
    import Bus from 'Bus';

    export default {
        data: function () {
            var passwordRule = (rule, value, callback) => {
                if (value != '' && value !== this.ruleForm.password_confirmation && this.ruleForm.password_confirmation != '') {
                    callback(new Error('两次输入密码不一致!'));
                } else {
                    callback();
                }
            };
            var password_confirmationRule = (rule, value, callback) => {
                if (value != '' && value !== this.ruleForm.password) {
                    callback(new Error('两次输入密码不一致!'));
                } else {
                    callback();
                }
            };
            return {
                ruleForm: {
                    nickname: '',
                    name: '',
                    old_password: '',
                    password: '',
                    password_confirmation: '',
                    img: '',
                },
                rules: {
                    nickname: [
                        {required: true, message: '请输入昵称', trigger: 'blur'}
                    ],
                    name: [
                        {required: true, message: '请输入登录账号', trigger: 'blur'}
                    ],
                    old_password: [
                        {required: true, message: '请输入登录密码', trigger: 'blur'}
                    ],
                    password: [
                        { trigger: 'blur',min:6,max:30},
                        {validator: passwordRule}
                    ],
                    password_confirmation: [
                        { trigger: 'blur',min:6,max:30},
                        {validator: password_confirmationRule}
                    ]
                },
                headers: {
                    ct: ''
                }

            };
        },
        mounted: function () {
            let user_info = JSON.parse(localStorage.getItem('user_info'));
            let user = user_info.user;
            let token = user_info.token;
            this.ruleForm.nickname = user.nickname;
            this.ruleForm.name = user.name;
            this.ruleForm.img = user.img;
            this.headers.ct = token;
        },
        methods: {
            onSubmit(form) {
                let self = this;
                self.$refs[form].validate((valid) => {
                    if (valid) {
                        self.$emit('changeLoading');
                        self.$axios.post('/user', self.ruleForm).then((res) => {
                            self.$emit('changeLoading');
                            if (res.data.code == 4000) {
                                let userInfo = JSON.parse(localStorage.getItem('user_info'));
                                userInfo.user = res.data.data.user;
                                localStorage.setItem('user_info', JSON.stringify(userInfo));
                                Bus.$emit('updateInfo', {nickname: self.ruleForm.nickname, headUrl: self.$rootPath + self.ruleForm.img});
                                self.ruleForm.old_password = self.ruleForm.password = self.ruleForm.password_confirmation = '';
                                self.$message({
                                    message: res.data.msg,
                                    type: 'success'
                                });
                            } else {
                                self.$message({
                                    message: res.data.msg,
                                    type: 'error'
                                });
                            }
                        });

                    } else {
                        return false;
                    }
                });
            },
            handleAvatarSuccess(res, file) {
                this.ruleForm.img = res.data.uri;
            },
            beforeAvatarUpload(file) {
                const isLt2M = file.size / 1024 / 1024 < 2;
                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isLt2M;
            },
        },
    }
</script>
<style>
    .avatar-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        width: auto;
    }

    .avatar-uploader .el-upload:hover {
        border-color: #20a0ff;
    }

    .avatar-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 178px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }

    .avatar {
        width: 178px;
        height: 178px;
        display: block;
    }


    .el-table .cell, .el-table th>div{
        padding-left: 0;
        padding-right: 0;
    }
    .el-button+.el-button{
        margin-left: 0;
    }
</style>