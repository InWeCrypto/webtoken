<template>
    <div class="table">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-menu"></i> 文章管理</el-breadcrumb-item>
                <el-breadcrumb-item>列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="z-title">
            <el-button type="primary"
                       @click="addRoleDialog = true;handleAdd()" class="z-right">添加
            </el-button>
        </div>
        <el-table :data="tableData" border style="width: 100%">
            <el-table-column prop="detail.title" label="标题"></el-table-column>
            <el-table-column label="图片">
                <template scope="scope">
                    <img style="height: 60px" :src="scope.row.detail.img" />
                </template>
            </el-table-column>
            <el-table-column prop="detail.desc" label="简介">
            </el-table-column>
            <el-table-column label="是否Banner展示" :formatter="isBanner">
            </el-table-column>
            <el-table-column prop="created_at" label="创建时间"></el-table-column>
            <el-table-column label="操作">
                <template scope="scope">
                    <el-button size="small"
                               @click="handleEdit(scope.$index,scope.row)">编辑
                    </el-button>
                    <el-button size="small" type="danger"
                               @click="handleDelete(scope.$index,scope.row)">删除
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="block z-paginate">
            <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page="options.page"
                    :page-size="options.per_page"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="paginate.total">
            </el-pagination>
        </div>
        <!--添加-->
        <el-dialog title="添加" :visible.sync="addRoleDialog">
            <el-form :model="role" :rules="rules" ref="addRole">
                <el-form-item label="封面图片" prop="img">
                    <el-upload
                            class="avatar-uploader"
                            :action="$apiPath+'/upload'"
                            :show-file-list="false"
                            :on-success="handleAvatarSuccess"
                            :before-upload="beforeAvatarUpload"
                            :headers="headers"
                            :with-credentials="true">
                        <img v-if="role.img" :src="role.img" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item label="标题" prop="title">
                    <el-input v-model="role.title" placeholder="请输入标题"></el-input>
                </el-form-item>
                <el-form-item label="是否Banner展示">
                    <el-switch
                            v-model="role.is_banner"
                            on-text=""
                            off-text="">
                    </el-switch>
                </el-form-item>
                <el-form-item label="简介" prop="desc">
                    <el-input type="textarea" rows="5" v-model="role.desc" placeholder="请输入简介"></el-input>
                </el-form-item>
                <el-form-item label="详情" prop="content">

                </el-form-item>
                <quill-editor ref="myTextEditor" v-model="role.content" :config="editorOption"></quill-editor>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="addRoleDialog = false">取 消</el-button>
                <el-button type="primary" @click="postAdd('addRole')">确 定</el-button>
            </div>
        </el-dialog>
        <!--编辑-->
        <el-dialog title="编辑" :visible.sync="editRoleDialog">
            <el-form :model="role" :rules="rules" ref="editRole">
                <el-form-item label="封面图片" prop="img">
                    <el-upload
                            class="avatar-uploader"
                            :action="$apiPath+'/upload'"
                            :show-file-list="false"
                            :on-success="handleAvatarSuccess"
                            :before-upload="beforeAvatarUpload"
                            :headers="headers"
                            :with-credentials="true">
                        <img v-if="role.img" :src="role.img" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item label="标题" prop="title">
                    <el-input v-model="role.title" placeholder="请输入标题"></el-input>
                </el-form-item>
                <el-form-item label="是否Banner展示">
                    <el-switch
                            v-model="role.is_banner"
                            on-text=""
                            off-text="">
                    </el-switch>
                </el-form-item>

                <el-form-item label="简介" prop="desc">
                    <el-input type="textarea" rows="5" v-model="role.desc" placeholder="请输入简介"></el-input>
                </el-form-item>
                <el-form-item label="详情" prop="content">

                </el-form-item>
                <quill-editor ref="myTextEditor" v-model="role.content" :config="editorOption"></quill-editor>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="editRoleDialog = false">取 消</el-button>
                <el-button type="primary" @click="putEdit('editRole')">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import { quillEditor } from 'vue-quill-editor';
    export default {
        data() {
            return {
                url: '/article',
                tableData: [],
                addRoleDialog: false,
                editRoleDialog: false,
                rightDialog: false,
                role: {},
                allModules: [],
                rules: {
                    title: [
                        {required: true, message: '请输入标题'}
                    ],
                    img: [
                        {required: true, message: '请输入图片'}
                    ],
                    desc: [
                        {required: true, message: '请输入简介'}
                    ],
                    content: [
                        {required: true, message: '请输入详情'}
                    ],
                },
                brands: [{id: 0, name: '全部品牌'}],
                allBrands: [],
                categories: [{id: 0, name: '全部分类'}],
                allCategories: [],
                headers: {
                    ct: ''
                },
                options: {
                    brand_id: '',
                    cate_id: '',
                    keyword: '',
                    status: '',
                    page: 1,
                    per_page: 10
                },
                allStatus: [
                    '无效订单',
                    '进行中',
                    '已完成'
                ],
                pickerOptions: {
                    disabledDate(time) {
                        return time.getTime() < Date.now() - 8.64e7;
                    }
                },
                dialogImageUrl: '',
                dialogVisible: false,
                picList: [],
                paginate: {},
                goodsList: [],
                id: 0,
                editorOption: {
                    // something config
                },
                currentId:'',
            }
        },
        mounted(){
            this.getData();
            this.headers.ct = JSON.parse(localStorage.getItem('user_info')).token;
            this.editor();
        },
        components: {
            quillEditor
        },
        methods: {
            editor() {
                return this.$refs.myTextEditor.quillEditor;
            },
            isBanner(row, column) {
                return row.detail.is_banner?'是':'否';
            },
            handleSizeChange(val) {
                this.options.per_page = val;
                this.getData();
            },
            handleCurrentChange(val) {
                this.options.page = val;
                this.getData();
            },
            getData(){
                let self = this;
                self.$emit('changeLoading');
                self.$axios.get(self.url, {params: self.options}).then((res) => {
                    self.$emit('changeLoading');
                    self.tableData = res.data.data.list.data;
                    self.paginate = res.data.data;
                });
            },
            handleAdd(index, row){
                this.role = {img:'',title:'',is_banner:false};
                this.addRoleDialog = true;
            },
            postAdd(row){
                let self = this;
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

            },
            handleEdit(index, row)
            {
                this.role = Object.assign({}, row.detail);
                this.currentId = row.id;
                this.editRoleDialog = true;
//                this.role.status = this.role.status ? true : false;
//                this.picList = [];
//                this.role.pic_list = [];
//                let picCnt = row.images.length;
//                for (let i = 0; i < picCnt; i++) {
//                    let temp = row.images[i];
//                    this.picList.push({id: temp.id, response: {data: {uri: temp.path}}, url: this.$rootPath + temp.path});
//                    this.role.pic_list.push(temp.path);
//                }
            }
            ,
            putEdit(rule)
            {
                let self = this;
                self.$refs[rule].validate((valid) => {
                    if (valid) {
                        self.$emit('changeLoading');
                        self.$axios.put(self.url + '/' + self.currentId, self.role).then((res) => {
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
                this.getData();
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
                this.role.img = res.data.uri;
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