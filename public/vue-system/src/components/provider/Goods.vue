<template>
    <div class="table">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-menu"></i> 商品管理</el-breadcrumb-item>
                <el-breadcrumb-item>列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="z-title">
            <el-select v-model="options.cate_id" placeholder="请选择分类">
                <el-option
                        v-for="item in [{id: 0, name: '全部分类'}].concat(allCategories)"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id">
                </el-option>
            </el-select>
            <el-select v-model="options.brand_id" placeholder="请选择品牌">
                <el-option
                        v-for="item in [{id: 0, name: '全部品牌'}].concat(allBrands)"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id">
                </el-option>
            </el-select>
            <el-select v-model="options.status" placeholder="请选择状态">
                <el-option
                        v-for="item in allStatus"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id">
                </el-option>
            </el-select>
            <el-input
                    placeholder="请输入商品名称"
                    icon="search"
                    v-model="options.keyword" class="z-search">
                <el-button slot="append" icon="search" @click="handleIconClick"></el-button>
            </el-input>
            <el-button type="primary"
                       @click="addRoleDialog = true;handleAdd()" class="z-right">添加
            </el-button>
        </div>
        <el-table :data="tableData" border style="width: 100%" class="z-table">
            <el-table-column type="expand" label="详情">
                <template scope="props">
                    <el-form label-position="left" inline class="demo-table-expand">
                        <el-form-item label="ID">
                            <span>{{ props.row.id }}</span>
                        </el-form-item>
                        <el-form-item label="名称">
                            <span>{{ props.row.name }}</span>
                        </el-form-item>
                        <el-form-item label="分类">
                            <span>{{ props.row.cate.name }}</span>
                        </el-form-item>
                        <el-form-item label="品牌">
                            <span>{{ props.row.brand.name }}</span>
                        </el-form-item>
                        <el-form-item label="图片">
                            <img :src="$rootPath+props.row.img" alt="" style="width: 150px;height: 100px;">
                        </el-form-item>
                        <el-form-item label="轮播图片">
                            <img v-for="val in props.row.images" :src="$rootPath+val.path" alt="" style="width: 150px;height: 100px;">
                        </el-form-item>
                        <el-form-item label="条码">
                            <span>{{ props.row.bar_code }}</span>
                        </el-form-item>
                        <el-form-item label="保质期">
                            <span>{{ props.row.shelf_life }}个月</span>
                        </el-form-item>
                        <el-form-item label="销售价">
                            <span>{{ props.row.sell_price }}</span>
                        </el-form-item>
                        <el-form-item label="优惠价">
                            <span>{{ props.row.discount_price }}</span>
                        </el-form-item>
                        <el-form-item label="折扣率">
                            <span>{{ props.row.discount_rate }}</span>
                        </el-form-item>
                        <el-form-item label="库存">
                            <span>{{ props.row.stock }}</span>
                        </el-form-item>
                        <el-form-item label="状态">
                            <span>{{ formatStatusZh(props.row) }}</span>
                        </el-form-item>
                        <el-form-item label="简介">
                            <span>{{ props.row.intro }}</span>
                        </el-form-item>
                        <el-form-item label="预售时间">
                            <span>{{ props.row.pre_sell_at }}</span>
                        </el-form-item>
                        <el-form-item label="创建时间">
                            <span>{{ props.row.created_at }}</span>
                        </el-form-item>
                        <el-form-item label="修改时间">
                            <span>{{ props.row.updated_at }}</span>
                        </el-form-item>
                    </el-form>
                </template>
            </el-table-column>
            <el-table-column prop="cate.name" label="分类"></el-table-column>
            <el-table-column prop="brand.name" label="品牌"></el-table-column>
            <el-table-column prop="name" label="名称"></el-table-column>
            <el-table-column prop="bar_code" label="条码"></el-table-column>
            <el-table-column prop="sell_price" label="销售价"></el-table-column>
            <el-table-column prop="stock" label="库存" :formatter="formatStock"></el-table-column>
            <el-table-column prop="pre_sell_at" label="预售时间"></el-table-column>
            <el-table-column prop="warning" label="预警数量" :formatter="formatWarning"></el-table-column>
            <el-table-column prop="status" label="状态" :formatter="formatStatusZh"></el-table-column>
            <el-table-column label="操作">
                <template scope="scope">
                    <el-dropdown>
                        <el-button type="primary">
                            操作<i class="el-icon-caret-bottom el-icon--right"></i>
                        </el-button>
                        <el-dropdown-menu slot="dropdown" class="z-menu">
                            <el-dropdown-item>
                                <el-button size="small"
                                           @click="editRoleDialog = true;handleEdit(scope.$index, scope.row)">编辑
                                </el-button>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <el-button size="small" type="primary"
                                           @click="inList(scope.row)">入库
                                </el-button>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <el-button size="small" type="info"
                                           @click="outList(scope.row)">出库
                                </el-button>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <el-button size="small" type="warning"
                                           @click="warningList(scope.row)">临期
                                </el-button>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <el-button size="small" type="primary"
                                           @click="handleCheck(scope.row)">添加盘点
                                </el-button>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <el-button size="small" type="danger"
                                           @click="handleDelete(scope.$index, scope.row)">删除
                                </el-button>
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
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
                        <img v-if="role.img" :src="$rootPath+role.img" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item label="轮播图片">
                    <el-upload
                            :action="$apiPath+'/upload'"
                            list-type="picture-card"
                            :headers="headers"
                            :with-credentials="true"
                            :on-success="handlePicList"
                            :before-upload="beforeAvatarUpload"
                            :on-preview="handlePictureCardPreview"
                            :on-remove="handleRemove">
                        <i class="el-icon-plus"></i>
                    </el-upload>
                    <el-dialog v-model="dialogVisible" size="tiny">
                        <img width="100%" :src="dialogImageUrl" alt="">
                    </el-dialog>
                </el-form-item>
                <el-form-item label="分类" prop="cate_id">
                    <el-select v-model="role.cate_id" placeholder="请选择分类">
                        <el-option
                                v-for="item in allCategories"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="品牌" prop="brand_id">
                    <el-select v-model="role.brand_id" placeholder="请选择分类">
                        <el-option
                                v-for="item in allBrands"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="名称" prop="name">
                    <el-input v-model="role.name" placeholder="请输入名称"></el-input>
                </el-form-item>
                <el-form-item label="条码" prop="bar_code">
                    <el-input v-model="role.bar_code" placeholder="请输入条码"></el-input>
                </el-form-item>
                <el-form-item label="保质期(月)" prop="shelf_life">
                    <el-input v-model="role.shelf_life" placeholder="请输入保质期" type="number"></el-input>
                </el-form-item>
                <el-form-item label="销售价" prop="sell_price">
                    <el-input v-model="role.sell_price" placeholder="请输入销售价"></el-input>
                </el-form-item>
                <el-form-item label="折扣率" prop="discount_rate">
                    <el-input v-model="role.discount_rate" type="number" placeholder="请输入折扣率"></el-input>
                </el-form-item>
                <el-form-item label="状态">
                    <el-switch
                            v-model="role.status"
                            on-color="#13ce66"
                            off-color="#ff4949">
                    </el-switch>
                </el-form-item>
                <el-form-item label="简介" prop="intro">
                    <el-input v-model="role.intro" type="textarea" placeholder="请输入简介" :rows="5"></el-input>
                </el-form-item>
                <el-form-item label="预售时间">
                    <el-date-picker
                            v-model="role.pre_sell_at"
                            type="date"
                            placeholder="选择日期"
                            :picker-options="pickerOptions">
                    </el-date-picker>
                </el-form-item>
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
                            :action="this.$apiPath+'/upload'"
                            :show-file-list="false"
                            :on-success="handleAvatarSuccess"
                            :before-upload="beforeAvatarUpload"
                            :headers="headers"
                            :with-credentials="true">
                        <img v-if="role.img" :src="$rootPath+role.img" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item label="轮播图片">
                    <el-upload
                            :action="this.$apiPath+'/upload'"
                            list-type="picture-card"
                            :file-list="picList"
                            :headers="headers"
                            :with-credentials="true"
                            :on-success="handlePicList"
                            :before-upload="beforeAvatarUpload"
                            :on-preview="handlePictureCardPreview"
                            :on-remove="handlePicListRemove">
                        <i class="el-icon-plus"></i>
                    </el-upload>
                    <el-dialog v-model="dialogVisible" size="tiny">
                        <img width="100%" :src="dialogImageUrl" alt="">
                    </el-dialog>
                </el-form-item>
                <el-form-item label="分类" prop="cate_id">
                    <el-select v-model="role.cate_id" placeholder="请选择分类">
                        <el-option
                                v-for="item in allCategories"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="品牌" prop="brand_id">
                    <el-select v-model="role.brand_id" placeholder="请选择分类">
                        <el-option
                                v-for="item in allBrands"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="名称" prop="name">
                    <el-input v-model="role.name" placeholder="请输入名称"></el-input>
                </el-form-item>
                <el-form-item label="条码" prop="bar_code">
                    <el-input v-model="role.bar_code" placeholder="请输入条码"></el-input>
                </el-form-item>
                <el-form-item label="保质期(月)" prop="shelf_life">
                    <el-input v-model="role.shelf_life" placeholder="请输入保质期"></el-input>
                </el-form-item>
                <el-form-item label="销售价" prop="sell_price">
                    <el-input v-model="role.sell_price" placeholder="请输入销售价"></el-input>
                </el-form-item>
                <el-form-item label="折扣率" prop="discount_rate">
                    <el-input v-model="role.discount_rate" type="number" placeholder="请输入折扣率"></el-input>
                </el-form-item>
                <el-form-item label="状态">
                    <el-switch
                            v-model="role.status"
                            on-color="#13ce66"
                            off-color="#ff4949">
                    </el-switch>
                </el-form-item>
                <el-form-item label="简介" prop="intro">
                    <el-input v-model="role.intro" type="textarea" placeholder="请输入简介" :rows="5"></el-input>
                </el-form-item>
                <el-form-item label="预售时间">
                    <el-date-picker
                            v-model="role.pre_sell_at"
                            type="date"
                            placeholder="选择日期"
                            :picker-options="pickerOptions">
                    </el-date-picker>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="editRoleDialog = false">取 消</el-button>
                <el-button type="primary" @click="putEdit('editRole')">确 定</el-button>
            </div>
        </el-dialog>
        <!--盘点-->
        <el-dialog title="添加盘点信息" :visible.sync="addCheckDialog">
            <el-form :model="role" :rules="checkRules" ref="check">
                <el-form-item label="实际库存数量" prop="checked_num">
                    <el-input v-model="role.checked_num" placeholder="请输入库存数量"></el-input>
                </el-form-item>
                <el-form-item label="盘点人" prop="maker">
                    <el-input v-model="role.maker" placeholder="请输入盘点人名称"></el-input>
                </el-form-item>
                <el-form-item label="备注">
                    <el-input v-model="role.remark" type="textarea" placeholder="请输入备注" :rows="5"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="addCheckDialog = false">取 消</el-button>
                <el-button type="primary" @click="addCheck('check')">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        data() {
            let checkNum = (rule, value, callback) => {
                if (value < 0) {
                    callback(new Error('请输入合法的数量'));
                } else {
                    callback();
                }
            };
            return {
                url: '/goods',
                tableData: [],
                addRoleDialog: false,
                editRoleDialog: false,
                rightDialog: false,
                addCheckDialog: false,
                role: {},
                allModules: [],
                rules: {
                    name: [
                        {required: true, message: '请输入名称', trigger: 'blur'}
                    ],
                    cate_id: [
                        {required: true, message: '请选择分类'}
                    ],
                    brand_id: [
                        {required: true, message: '请选择品牌'}
                    ],
                    img: [
                        {required: true, message: '请上传封面图片', trigger: 'blur'}
                    ],
                    bar_code: [
                        {required: true, message: '请输入条码', trigger: 'blur'}
                    ],
                    shelf_life: [
                        {required: true,message: '请输入保质期,单位:月'},
                        {validator: checkNum}
                    ],
                    sell_price: [
                        {required: true, message: '请输入零售价', trigger: 'blur'},
                        {validator: checkNum}
                    ],
                    discount_rate: [
                        {min: 1, max: 100, message: '优惠价占比必须在1~100之间', trigger: 'blur'},
                        {validator: checkNum}
                    ],
                    intro: [
                        {required: true, message: '请输入简介', trigger: 'blur'}
                    ],
                },
                checkRules: {
                    checked_num: [
                        {validator: checkNum, required: true, message: '请输入盘点数量', trigger: 'blur'}
                    ],
                    maker: [
                        {required: true, message: '请入盘点人'}
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
                    {id: 'all', name: '全部状态'},
                    {id: 1, name: '启用'},
                    {id: 0, name: '禁用'}
                ],
                pickerOptions: {
                    disabledDate(time) {
                        return time.getTime() < Date.now() - 8.64e7;
                    }
                },
                headers: {
                    ct: ''
                },
                dialogImageUrl: '',
                dialogVisible: false,
                picList: [],
                paginate: {}
            }
        },
        mounted(){
            this.getData();
            this.getOptions();
            this.headers.ct = JSON.parse(localStorage.getItem('user_info')).user.uuid;
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
                self.$axios.get(self.$apiPath + '/brand').then((res) => {
                    self.allBrands = res.data.data.list;
                    self.brands = self.brands.concat(self.allBrands);
                });
                self.$axios.get(self.$apiPath + '/category').then((res) => {
                    self.allCategories = res.data.data.list;
                    self.categories = self.categories.concat(self.allCategories);
                });
            },
            formatter(row, column) {
                return row.created_at;
            },
            formatStatusZh(row, column) {
                return row.status ? '启用' : '禁用';
            },
            formatStock(row, column) {
                return row.stock + '(' + row.size + ')';
            },
            formatWarning(row, column) {
                let cnt = 0;
                for (let i = 0; i < row.warning.length; i++) {
                    cnt += parseInt(row.warning[i].surplus)
                }
                return cnt;
            },
            handleAdd(){
                this.role = {img: '', pre_sell_at: '', status: 1, pic_list: [], cate_id: '', brand_id: ''};
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
                self.$confirm('请确定删除该商品么?', '提示', {
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
                self.$axios.put(self.$apiPath + '/module/' + self.role.id, this.$refs.tree.getCheckedKeys()).then((res) => {
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
            beforeAvatarUpload(file) {
                const isLt2M = file.size / 1024 / 1024 < 2;
                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isLt2M;
            },
            handlePicList(res, file){
                this.role.pic_list.push(res.data.uri);
                this.picList.push({response: {data: {uri: res.data.uri}}, url: this.$rootPath + res.data.uri})
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
            outList(row){
                this.$router.push('out/' + row.id)
            },
            warningList(row){
                this.$router.push('warning/' + row.id)
            },
            handleCheck(row){
                this.role = {goods_id: row.id};
                this.addCheckDialog = true;
            },
            addCheck(rule)
            {
                let self = this;
                self.$refs[rule].validate((valid) => {
                    if (valid) {
                        self.$emit('changeLoading');
                        self.$axios.post(self.$apiPath + '/inventory', self.role).then((res) => {
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
                            self.addCheckDialog = false;
                        });
                    } else {
                        return false;
                    }
                });
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