<template>
    <div class="table">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-menu"></i> 资产分类管理</el-breadcrumb-item>
                <el-breadcrumb-item>列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="z-title">
            <el-select v-model="options.category_id" placeholder="请选择钱包类型">
                <el-option
                        v-for="item in [{id: 0, name: '全部分类'}].concat(allCategories)"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id">
                </el-option>
            </el-select>
            <el-button slot="append" icon="search" @click="handleIconClick"></el-button>
            <el-button type="primary"
                       @click="addRoleDialog = true;handleAdd()" class="z-right">添加
            </el-button>
        </div>
        <el-table :data="tableData" border style="width: 100%">
            <el-table-column prop="wallet_category.name" label="所属钱包类型"></el-table-column>
            <el-table-column label="icon">
                <template scope="scope">
                    <img style="width: 35px;height: 35px" :src="scope.row.icon"/>
                </template>
            </el-table-column>
            <el-table-column prop="name" label="名称"></el-table-column>
            <el-table-column prop="gas" label="gas"></el-table-column>
            <el-table-column prop="address" label="合约地址"></el-table-column>
            <el-table-column prop="created_at" label="创建时间">
            </el-table-column>
            <el-table-column label="操作">
                <template scope="scope">
                    <el-button size="small"
                               @click="editRoleDialog = true;handleEdit(scope.$index, scope.row)">编辑
                    </el-button>
                    <el-button size="small" type="danger"
                               @click="handleDelete(scope.$index, scope.row)">删除
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <!--添加-->
        <el-dialog title="分类添加" :visible.sync="addRoleDialog">
            <el-form :model="role" :rules="rules" ref="addRole">
                <el-form-item label="icon" prop="icon">
                    <el-upload
                            class="avatar-uploader"
                            :action="$apiPath+'/upload'"
                            :show-file-list="false"
                            :on-success="handleAvatarSuccess"
                            :before-upload="beforeAvatarUpload"
                            :headers="headers"
                            :with-credentials="true">
                        <img v-if="role.icon" :src="role.icon" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item label="所属钱包类型" prop="category_id">
                    <el-select v-model="role.category_id" placeholder="请选择分类">
                        <el-option
                                v-for="item in allCategories"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="分类名称:" prop="name">
                    <el-input v-model="role.name" placeholder="请输入分类名称"></el-input>
                </el-form-item>
                <el-form-item label="gas:" prop="gas">
                    <el-input v-model="role.gas" placeholder="请输入gas"></el-input>
                </el-form-item>
                <el-form-item label="address:" prop="address">
                    <el-input v-model="role.address" placeholder="请输入合约地址"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="addRoleDialog = false">取 消</el-button>
                <el-button type="primary" @click="postAdd('addRole')">确 定</el-button>
            </div>
        </el-dialog>

        <!--编辑-->
        <el-dialog title="分类编辑" :visible.sync="editRoleDialog">
            <el-form :model="role" :rules="rules" ref="editRole">
                <el-form-item label="icon" prop="icon">
                    <el-upload
                            class="avatar-uploader"
                            :action="$apiPath+'/upload'"
                            :show-file-list="false"
                            :on-success="handleAvatarSuccess"
                            :before-upload="beforeAvatarUpload"
                            :headers="headers"
                            :with-credentials="true">
                        <img v-if="role.icon" :src="role.icon" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item label="所属钱包类型" prop="category_id">
                    <el-select v-model="role.category_id" placeholder="请选择分类">
                        <el-option
                                v-for="item in allCategories"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="分类名称:" prop="name">
                    <el-input v-model="role.name" placeholder="请输入分类名称"></el-input>
                </el-form-item>
                <el-form-item label="gas:" prop="gas">
                    <el-input v-model="role.gas" placeholder="请输入gas"></el-input>
                </el-form-item>
                <el-form-item label="address:" prop="address">
                    <el-input v-model="role.address" placeholder="请输入合约地址"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="editRoleDialog = false">取 消</el-button>
                <el-button type="primary" @click="putEdit('editRole')">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                keyword: '',
                url: '/gnt-category',
                tableData: [],
                addRoleDialog: false,
                editRoleDialog: false,
                rightDialog: false,
                allCategories: [],
                role: {
                    id: '',
                    name: '',
                    category_id: '',
                    icon:'',
                    gas:'',
                    address:''
                },
                rules: {
                    name: [
                        {required: true, message: '请输入分类名称', trigger: 'blur'}
                    ],
                    category_id: [
                        {required: true, message: '请选择所属钱包类型'}
                    ],
                },
                options: {
                    category_id: 0
                },
                headers: {
                    ct: ''
                },
                defaultProps: {
                    children: 'child',
                    label: 'name',
                    value: 'id'
                },
            }
        },
        mounted(){
            this.getData();
            this.getWalletCategory();
            this.headers.ct = JSON.parse(localStorage.getItem('user_info')).token;
        },
        methods: {
            getData(){
                let self = this;
                self.$emit('changeLoading');
                self.$axios.get(self.url, {params: self.options}).then((res) => {
                    self.$emit('changeLoading');
                    self.tableData = res.data.data.list;
                });
            },
            getWalletCategory: function () {
                let self = this;
                self.$axios.get('/wallet-category').then((res) => {
                    self.allCategories = res.data.data.list;
//                    self.categories = self.categories.concat(self.allCategories);
                });
            },
            handleAdd(){
                this.role = {name: '', category_id: '',icon:'',gas:'',address:''};
            },
            postAdd(rule){
                let self = this;
                self.$refs[rule].validate((valid) => {
                    if (valid) {
                        self.$emit('changeLoading');
                        self.$axios.post(self.url, self.role).then((res) => {
                            self.$emit('changeLoading');
                            if (res.data.code == 4000) {
                                self.$message({
                                    message: res.data.msg,
                                    type: 'success'
                                });
                                self.getData();
                            } else {
                                self.$message({
                                    message: res.data.msg,
                                    type: 'error'
                                });
                                return false;
                            }
                            self.addRoleDialog = false;

                        });
                    } else {
                        return false;
                    }
                });
            },
            handleEdit(index, row)
            {
                this.role = Object.assign({}, row);
            }
            ,
            putEdit(rule)
            {
                let self = this;
                self.$refs[rule].validate((valid) => {
                    if (valid) {
                        self.$emit('changeLoading');
                        self.$axios.put(self.url + '/' + self.role.id, self.role).then((res) => {
                            self.$emit('changeLoading');
                            if (res.data.code == 4000) {
                                self.$message({
                                    message: res.data.msg,
                                    type: 'success'
                                });
                                self.getData();
                            } else {
                                self.$message({
                                    message: res.data.msg,
                                    type: 'error'
                                });
                                return false;
                            }
                            self.editRoleDialog = false;
                        });
                    } else {
                        return false;
                    }
                });
            },
            handleIconClick(){
                this.getData(this.keyword);
            },
            clickNode(data){
                this.role = Object.assign({}, data);
                this.editRoleDialog = true;
            },
            handleChange(data){
                console.log(data)
            },
            handleDelete(index, row)
            {
                let self = this;
                self.$emit('changeLoading');
                self.$axios.delete(self.url + '/' + row.id).then((res) => {
                    self.$emit('changeLoading');
                    if (res.data.code == 4000) {
                        self.$message({
                            message: res.data.msg,
                            type: 'success'
                        });
                        self.getData();
                    } else {
                        self.$message({
                            message: res.data.msg,
                            type: 'error'
                        });
                    }

                });
            },
            beforeAvatarUpload(file) {
                const isLt2M = file.size / 1024 / 1024 < 2;
                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isLt2M;
            },
            handleAvatarSuccess(res, file) {
                this.role.icon = res.data.uri;
            },
            handleRemove(file, fileList) {
                let file_name = file.response.data.uri;
                let list = this.role.pic_list;
                let len = list.length;
                for (let i = 0; i < len; i++) {
                    if (list[i] == file_name) {
                        list.splice(i, 1);
                        break;
                    }
                }
            },
            handlePicListRemove(file, fileList){
                let file_name = file.response.data.uri;
                let list = this.role.pic_list;
                let len = list.length;
                for (let i = 0; i < len; i++) {
                    if (list[i] == file_name) {
                        this.picList.splice(i, 1);
                        list.splice(i, 1);
                        break;
                    }
                }
            },
            handlePictureCardPreview(file) {
                this.dialogImageUrl = file.url;
                this.dialogVisible = true;
            },
        }
    };
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
        width: 200px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }

    .avatar {
        width: 200px;
        height: 178px;
        display: block;
    }

    .demo-table-expand {
        font-size: 0;
    }

    .demo-table-expand label {
        width: 90px;
        color: #99a9bf;
    }

    .demo-table-expand .el-form-item {
        margin-right: 0;
        margin-bottom: 0;
        width: 50%;
    }

    .demo-table-expand .el-form-item__content {
        top: 4px;
    }

    .el-table .cell, .el-table th > div {
        padding-left: 0;
        padding-right: 0;
    }

    .el-button + .el-button {
        margin-left: 0;
    }

    .el-table td, .el-table th {
        padding-right: 5px;
    }
</style>