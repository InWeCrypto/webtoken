<template>
    <div class="table">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-menu"></i> 员工管理</el-breadcrumb-item>
                <el-breadcrumb-item>列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="z-title">
            <el-button type="primary"
                       @click="handleAdd()" class="z-right">添加
            </el-button>
        </div>
        <el-table :data="tableData" border style="width: 100%" header-align="center">
            <el-table-column prop="id" label="序号" width="80"></el-table-column>
            <el-table-column prop="name" label="登录账号"></el-table-column>
            <el-table-column prop="nickname" label="昵称"></el-table-column>
            <el-table-column prop="role.name" label="角色"></el-table-column>
            <el-table-column prop="status_zh" label="状态">
            </el-table-column>
            <el-table-column prop="created_at" label="创建时间" :formatter="formatter"></el-table-column>
            <el-table-column label="操作" width="200">
                <template scope="scope">
                    <el-button size="small"
                               @click="handleEdit(scope.$index, scope.row)">编辑
                    </el-button>
                    <!--<el-button size="small" type="primary"-->
                    <!--@click="rightDialog = true;handleRole(scope.row)">权限-->
                    <!--</el-button>-->
                    <!--<el-button size="small" type="danger"-->
                    <!--@click="handleDelete(scope.$index, scope.row)">删除-->
                    <!--</el-button>-->
                </template>
            </el-table-column>
        </el-table>
        <!--添加-->
        <el-dialog title="管理员添加" :visible.sync="addDialog">
            <el-form :model="record" label-width="100px" :rules="rules" ref="add">
                <el-form-item label="登录账号" prop="name">
                    <el-input v-model="record.name"></el-input>
                </el-form-item>
                <el-form-item label="昵称" prop="nickname">
                    <el-input v-model="record.nickname"></el-input>
                </el-form-item>
                <!--<el-form-item label="邮箱">-->
                    <!--<el-input v-model="record.email"></el-input>-->
                <!--</el-form-item>-->
                <!--<el-form-item label="职务">-->
                    <!--<el-input v-model="record.duty"></el-input>-->
                <!--</el-form-item>-->
                <el-form-item label="密码" prop="password">
                    <el-input type="password" placeholder="密码" v-model="record.password"></el-input>
                </el-form-item>
                <el-form-item label="确认密码" prop="password_confirmation">
                    <el-input type="password" placeholder="确认密码" v-model="record.password_confirmation"></el-input>
                </el-form-item>
                <el-form-item label="角色" prop="role_id">
                    <el-select v-model="record.role_id" placeholder="请选择角色">
                        <el-option
                                v-for="item in roles"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="状态">
                    <el-switch
                            v-model="record.status"
                            on-color="#13ce66"
                            off-color="#ff4949">
                    </el-switch>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="addDialog = false">取 消</el-button>
                <el-button type="primary" @click="postAdd('add')">确 定</el-button>
            </div>
        </el-dialog>

        <!--编辑-->
        <el-dialog title="角色编辑" :visible.sync="editDialog">
            <el-form :model="record" label-width="100px" :rules="rules" ref="add">
                <el-form-item label="登录账号" prop="name">
                    <el-input v-model="record.name"></el-input>
                </el-form-item>
                <el-form-item label="昵称" prop="nickname">
                    <el-input v-model="record.nickname"></el-input>
                </el-form-item>

                <!--<el-form-item label="邮箱">-->
                    <!--<el-input v-model="record.email"></el-input>-->
                <!--</el-form-item>-->
                <!--<el-form-item label="职务">-->
                    <!--<el-input v-model="record.duty"></el-input>-->
                <!--</el-form-item>-->
                <el-form-item label="密码" prop="password">
                    <el-input type="password" placeholder="密码" v-model="record.password"></el-input>
                </el-form-item>
                <el-form-item label="确认密码" prop="password_confirmation">
                    <el-input type="password" placeholder="确认密码" v-model="record.password_confirmation"></el-input>
                </el-form-item>
                <el-form-item label="角色" prop="role_id">
                    <el-select v-model="record.role_id" placeholder="请选择角色">
                        <el-option
                                v-for="item in roles"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="状态">
                    <el-switch
                            v-model="record.status"
                            on-color="#13ce66"
                            off-color="#ff4949">
                    </el-switch>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="editDialog = false">取 消</el-button>
                <el-button type="primary" @click="editDialog = false;putEdit(record)">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        data() {
            var passwordRule = (rule, value, callback) => {
                if (value != '' && value !== this.record.password_confirmation && this.role.password_confirmation != '') {
                    callback(new Error('两次输入密码不一致!'));
                } else {
                    callback();
                }
            };
            var password_confirmationRule = (rule, value, callback) => {
                if (value != '' && value !== this.record.password) {
                    callback(new Error('两次输入密码不一致!'));
                } else {
                    callback();
                }
            };
            return {
                url: '/admin',
                tableData: [],
                addDialog: false,
                editDialog: false,
                record: {},
                roles: [],
                defaultProps: {
                    children: 'child',
                    label: 'name'
                },
                rules: {
                    nickname: [
                        {required: true, message: '请输入昵称', trigger: 'blur'}
                    ],
                    name: [
                        {required: true, message: '请输入登录账号', trigger: 'blur'}
                    ],
                    role_id: [
                        {required: true, message: '请选择角色'}
                    ],
                    password: [
                        {message: '请输入密码,长度6~30位', min: 6, max: 30, validator: passwordRule, trigger: 'blur'}
                    ],
                    password_confirmation: [
                        {message: '请输入密码,长度6~30位', min: 6, max: 30, validator: password_confirmationRule, trigger: 'blur'}
                    ]
                },
            }
        },
        mounted(){
            this.getData();
            this.getRoles();
            this.resetRecord();
        },
        methods: {
            resetRecord(){
                this.record = Object.assign({}, {
                    nickname: '',
                    name: '',
                    email: '',
                    duty: '',
                    role_id: '',
                    status: 1,
                    password: '',
                    password_confirmation: '',
                });
            },
            getData(){
                let self = this;
                self.$emit('changeLoading');
                self.$axios.get(self.url).then((res) => {
                    self.$emit('changeLoading');
                    self.tableData = res.data.data.list;
                });
            },
            getRoles(){
                let self = this;
                self.$axios.get('/role').then((res) => {
                    self.roles = res.data.data.list;
                });
            },
            formatter(row, column) {
                return row.created_at;
            },
            handleAdd(){
                this.addDialog = true;
                this.resetRecord();
            },
            postAdd(ref){
                let self = this;
                self.$refs[ref].validate(valid => {
                    if (valid) {
                        self.$emit('changeLoading');
                        self.$axios.post(self.url, self.record).then((res) => {
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
                            self.addDialog = false;
                        });
                    } else {
                        return false;
                    }
                });
            },
            handleEdit(index, row) {
                this.editDialog = true;
                console.log(row);
                this.record = Object.assign({}, row);
                if (!this.record.role_id) {
                    this.record.role_id = '';
                }
                this.record.status = this.record.status ? true : false;
            },
            putEdit(role){
                let self = this;
                self.$emit('changeLoading');
                self.$axios.put(self.url + '/' + role.id, role).then((res) => {
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
            handleDelete(index, row) {
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
            handleRole(row){
                setTimeout(() => {
                    this.$refs.tree.setCheckedKeys(row.module_ids.split(','));
                }, 0);
                this.role = Object.assign({}, row);
            },
            postRole(){
                let self = this;
                self.$emit('changeLoading');
                self.$axios.put('/module/' + self.role.id, this.$refs.tree.getCheckedKeys()).then((res) => {
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
            }
        }
    }
</script>

<style>
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