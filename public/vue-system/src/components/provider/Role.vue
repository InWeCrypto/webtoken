<template>
    <div class="table">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-menu"></i> 角色管理</el-breadcrumb-item>
                <el-breadcrumb-item>列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="z-title">
            <el-button type="primary"
                       @click="addRoleDialog = true;handleAdd()" class="z-right">添加
            </el-button>
        </div>
        <el-table :data="tableData" border style="width: 100%">
            <el-table-column prop="name" label="角色名称">
            </el-table-column>
            <el-table-column prop="created_at" label="创建时间" :formatter="formatter">
            </el-table-column>
            <el-table-column label="操作">
                <template scope="scope">
                    <el-button size="small"
                               @click="editRoleDialog = true;handleEdit(scope.$index, scope.row)">编辑
                    </el-button>
                    <el-button size="small" type="primary"
                               @click="rightDialog = true;handleRole(scope.row)">权限
                    </el-button>
                    <el-button size="small" type="danger"
                               @click="handleDelete(scope.$index, scope.row)">删除
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <!--添加-->
        <el-dialog title="角色添加" :visible.sync="addRoleDialog">
            <el-form :model="role" :rules="rules" ref="addRole">
                <el-form-item label="角色名称" prop="name">
                    <el-input v-model="role.name"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="addRoleDialog = false">取 消</el-button>
                <el-button type="primary" @click="postAdd('addRole')">确 定</el-button>
            </div>
        </el-dialog>

        <!--编辑-->
        <el-dialog title="角色编辑" :visible.sync="editRoleDialog">
            <el-form :model="role" :rules="rules" ref="editRole">
                <el-form-item label="角色名称" prop="name">
                    <el-input v-model="role.name"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="editRoleDialog = false">取 消</el-button>
                <el-button type="primary" @click="putEdit('editRole')">确 定</el-button>
            </div>
        </el-dialog>


        <!--权限管理-->
        <el-dialog title="权限管理" :visible.sync="rightDialog">
            <el-tree
                    :data="allModules"
                    show-checkbox
                    :default-expand-all="true"
                    :default-checked-keys="selectedKey"
                    node-key="id"
                    ref="tree"
                    highlight-current
                    :props="defaultProps">
            </el-tree>
            <div slot="footer" class="dialog-footer">
                <el-button @click="rightDialog = false">取 消</el-button>
                <el-button type="primary" @click="rightDialog = false;postRole()">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                url: '/role',
                tableData: [],
                addRoleDialog: false,
                editRoleDialog: false,
                rightDialog: false,
                role: {
                    id: '',
                    name: '',
                },
                allModules: [],
                defaultProps: {
                    children: 'child',
                    label: 'name'
                },
                selectedKey: [],
                rules: {
                    name: [
                        {required: true, message: '请输入角色名称', trigger: 'blur'}
                    ],
                }
            }
        },
        mounted(){
            this.getData();
            this.getModules();
        },
        methods: {
            getData(){
                let self = this;
                self.$emit('changeLoading');
                self.$axios.get(self.url).then((res) => {
                    self.$emit('changeLoading');
                    self.tableData = res.data.data.list;
                });
            },
            getModules(){
                let self = this;
                self.$axios.get('/module').then((res) => {
                    self.allModules = res.data.data.list;
                });
            },
            formatter(row, column) {
                return row.created_at;
            },
            handleAdd(){
                this.role = {};
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
            handleRole(row)
            {
                if(row.module_ids){
                    setTimeout(() => {
                        this.$refs.tree.setCheckedKeys(row.module_ids.split(','));
                    }, 0);
                }

                this.role = Object.assign({}, row);
            }
            ,
            postRole()
            {
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