<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-date"></i>配置信息</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="form-box">
            <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="80px" class="demo-ruleForm">
                <el-form-item label="最小块高">
                    <el-input v-model="ruleForm.min_block_num"></el-input>
                </el-form-item>
                <el-form-item label="订单失败块高阈值">
                    <el-input v-model="ruleForm.check_block_num"></el-input>
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
            return {
                ruleForm: {
                    min_block_num:12,
                    check_block_num:180,
                },
                rules: {
                    min_block_num: [
                        {required: true, message: '请输入最小块高'}
                    ],
                    check_block_num: [
                        {required: true, message: '请输入订单失败块高阈值'}
                    ],
                }

            };
        },
        mounted: function () {
            this.getConfig();
        },
        methods: {
            getConfig(){
                let self = this;
                self.$emit('changeLoading');
                self.$axios.get('/config').then((res) => {
                    self.$emit('changeLoading');
                    if (res.data.code == 4000) {
                        var data = res.data.data;
                        for(var i=0;i<data.length;i++){
                            self.ruleForm[data[i]['name']] = data[i]['value'];
                        }
                    } else {
                        self.$message({
                            message: res.data.msg,
                            type: 'error'
                        });
                    }
                });

            },
            onSubmit(form) {
                let self = this;
                self.$refs[form].validate((valid) => {
                    if (valid) {
                        self.$emit('changeLoading');
                        self.$axios.post('/config', self.ruleForm).then((res) => {
                            self.$emit('changeLoading');
                            if (res.data.code == 4000) {
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