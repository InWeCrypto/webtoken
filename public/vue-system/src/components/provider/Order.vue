<template>
    <div class="table">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-menu"></i> 订单管理</el-breadcrumb-item>
                <el-breadcrumb-item>列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="z-title">
            <el-select v-model="options.status" placeholder="订单状态">
                <el-option
                        v-for="(item,key) in allStatus"
                        :key="key"
                        :label="item"
                        :value="key">
                </el-option>
            </el-select>
            <el-input
                    placeholder="请输入订单号"
                    icon="search"
                    v-model="options.keyword" class="z-search" style="width: 25%">
                <el-button slot="append" icon="search" @click="handleIconClick"></el-button>
            </el-input>
            <!--<el-button type="primary"-->
            <!--@click="addRoleDialog = true;handleAdd()" class="z-right">添加-->
            <!--</el-button>-->
        </div>
        <el-table :data="tableData" border style="width: 100%">
            <el-table-column type="expand" label="详情">
                <template scope="props">
                    <el-form label-position="right" inline class="demo-table-expand">
                        <el-form-item label="ID">
                            <span>{{ props.row.id }}</span>
                        </el-form-item>
                        <el-form-item label="订单编号">
                            <span>{{ props.row.order_no }}</span>
                        </el-form-item>
                        <el-form-item label="状态">
                            <span>{{ formatStatusZh(props.row) }}</span>
                        </el-form-item>
                        <el-form-item label="是否已发货">
                            <span>{{ formatterIsSend(props.row) }}</span>
                        </el-form-item>
                        <el-form-item label="总价">
                            <span>{{ props.row.total_price }}</span>
                        </el-form-item>
                        <el-form-item label="备注">
                            <span>{{ props.row.remark }}</span>
                        </el-form-item>
                        <el-form-item label="创建时间">
                            <span>{{ props.row.created_at }}</span>
                        </el-form-item>
                        <el-form-item label="发票类型" v-if="props.row.has_bill">
                            <span>{{ ['个人','公司'][props.row.invoice.type] }}</span>
                        </el-form-item>
                        <el-form-item label="发票抬头" v-if="props.row.has_bill">
                            <span>{{ props.row.invoice.title }}</span>
                        </el-form-item>
                        <el-form-item label="发票接受人" v-if="props.row.has_bill">
                            <span>{{ props.row.invoice.name }}</span>
                        </el-form-item>
                        <el-form-item label="发票接受人电话" v-if="props.row.has_bill">
                            <span>{{ props.row.invoice.phone }}</span>
                        </el-form-item>
                        <el-form-item label="发票接受地址" v-if="props.row.has_bill">
                            <span>{{ props.row.invoice.address }}</span>
                        </el-form-item>
                        <el-form-item label="是否出票" v-if="props.row.has_bill">
                            <span>{{ ['否','是'][props.row.invoice.status] }}</span>
                        </el-form-item>
                        <el-form-item label="出票时间" v-if="props.row.has_bill && props.row.bill_status">
                            <span>{{ ['否','是'][props.row.invoice.send_at] }}</span>
                        </el-form-item>
                    </el-form>
                    <el-form v-for="(item,index) in props.row.relation_goods" label-position="right" inline class="demo-table-expand" :key="index">
                        <el-form-item label="商品图片">
                            <img :src="$rootPath+item.goods.img" alt="" style="width: 150px;height: 100px;">
                        </el-form-item>
                        <el-form-item label="商品名称">
                            <span>{{ item.goods_name }}</span>
                        </el-form-item>
                        <el-form-item label="数量">
                            <span>{{ item.num }}({{  item.unit }})</span>
                        </el-form-item>
                        <el-form-item label="是否有赠送">
                            <span>{{ item.has_free?'是':'否' }}</span>
                        </el-form-item>
                        <el-form-item label="赠送数量" v-if="item.has_free">
                            <span>{{ item.free_num }}({{  item.unit }})</span>
                        </el-form-item>
                    </el-form>
                </template>
            </el-table-column>
            <el-table-column prop="order_no" label="订单号"></el-table-column>
            <el-table-column prop="total_price" label="总价"></el-table-column>
            <el-table-column prop="is_send" :formatter="formatterIsSend" label="是否发货"></el-table-column>
            <el-table-column prop="has_bill" :formatter="formatterHasBill" label="是否需要发票"></el-table-column>
            <el-table-column prop="status" :formatter="formatStatusZh" label="状态"></el-table-column>
            <el-table-column prop="created_at" label="创建时间"></el-table-column>
            <el-table-column label="操作">
                <template scope="scope">
                    <!--<el-button size="small"-->
                    <!--@click="handleAdd(scope.$index, scope.row)" v-if="!scope.row.is_send">发货-->
                    <!--</el-button>-->
                    <el-button size="small"
                               @click="postAdd(scope.row)" v-if="!scope.row.is_send">发货
                    </el-button>
                    <el-button size="small" type="danger"
                               @click="handleDelete(scope$index,scope.row)" v-if="scope.row.status != 1">删除
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
        <el-dialog title="选择发货商品批次号" :visible.sync="addRoleDialog">
            <el-form :model="role" :rules="rules" ref="addRole">
                <el-form-item v-for="(item,key) in goodsList" :label="item.goods_name" :key="key" :prop="'goods_'+item.id">
                    <el-select v-model="role['goods_'+item.id]" placeholder="请选择批次">
                        <el-option
                                v-for="item in item.list"
                                :key="item.id"
                                :label="item.serial_no"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="addRoleDialog = false">取 消</el-button>
                <el-button type="primary" @click="postAdd('addRole')">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                url: '/order',
                tableData: [],
                addRoleDialog: false,
                editRoleDialog: false,
                rightDialog: false,
                role: {},
                allModules: [],
                rules: {
                    cate_id: [
                        {required: true, message: '请选择商品批次'}
                    ],
                },
                brands: [{id: 0, name: '全部品牌'}],
                allBrands: [],
                categories: [{id: 0, name: '全部分类'}],
                allCategories: [],
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
            }
        },
        mounted(){
            this.getData();
            this.getOptions();
        },
        methods: {
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
                    self.tableData = res.data.data.data;
                    self.paginate = res.data.data;
                });
            },
            getOptions(){
                let self = this;
                self.$axios.get('/brand').then((res) => {
                    self.allBrands = res.data.data.list;
                    self.brands = self.brands.concat(self.allBrands);
                });
                self.$axios.get('/category').then((res) => {
                    self.allCategories = res.data.data.list;
                    self.categories = self.categories.concat(self.allCategories);
                });
            },
            formatterHasBill(row, column) {
                return row.has_bill ? '是' : '否';
            },
            formatterIsSend(row, column) {
                return row.is_send ? '是' : '否';
            },
            formatStatusZh(row, column) {
                return this.allStatus[row.status];
            },
            formatWarning(row, column) {
                let cnt = 0;
                for (let i = 0; i < row.warning.length; i++) {
                    cnt += row.warning[i].surplus
                }
                return cnt;
            },
            handleAdd(index, row){
                let self = this;
                for (let i = 0; i < row.relation_goods.length; i++) {
                    let val = 'goods_' + row.relation_goods[i].id;
                    self.role[val] = '';
                    self.rules[val] = [
                        {required: true, message: '请选择商品批次'}
                    ];
                }
                self.role = Object.assign({}, self.role);
                self.rules = Object.assign({}, self.rules);
                self.id = row.id;
                self.$emit('changeLoading');
                self.$axios.get('/list-for-out', {params: {id: row.id}}).then((res) => {
                    self.$emit('changeLoading');
                    if (res.data.code == 4000) {
                        self.goodsList = res.data.data;
                    } else {
                        self.$message({
                            message: res.data.msg,
                            type: 'error'
                        });
                        return false;
                    }
                    self.addRoleDialog = true;
                });
            },
            postAdd(row){
                let self = this;
                self.$confirm('请确认是否发货?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    self.$emit('changeLoading');
                    self.$axios.post(self.url, {id: row.id}).then((res) => {
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
//                        self.addRoleDialog = false;
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });


            },
            handleEdit(index, row)
            {
                this.role = Object.assign({}, row);
                this.role.status = this.role.status ? true : false;
                this.picList = [];
                this.role.pic_list = [];
                let picCnt = row.images.length;
                for (let i = 0; i < picCnt; i++) {
                    let temp = row.images[i];
                    this.picList.push({id: temp.id, response: {data: {uri: temp.path}}, url: this.$rootPath + temp.path});
                    this.role.pic_list.push(temp.path);
                }
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
                this.getData();
            },
            handleDelete(index, row)
            {
                let self = this;
                self.$confirm('请确定删除该订单么?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
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
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            }
            ,
            handleRole(row)
            {
                setTimeout(() => {
                    this.$refs.tree.setCheckedKeys(row.module_ids.split(','));
                }, 0);
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
            inList(row){
                this.$router.push('in/' + row.id)
            },
            warningList(row){
                this.$router.push('warning/' + row.id)
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