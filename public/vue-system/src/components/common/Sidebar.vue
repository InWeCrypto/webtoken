<template>
    <div class="sidebar">
        <el-menu :default-active="onRoutes" class="el-menu-vertical-demo" unique-opened router>
            <template v-for="item in items">
                <template v-if="item.subs">
                    <el-submenu :index="item.title">
                        <template slot="title"><i :class="item.icon"></i>{{ item.title }}</template>
                        <el-menu-item v-for="(subItem,i) in item.subs" :key="i" :index="subItem.index">{{ subItem.title }}
                        </el-menu-item>
                    </el-submenu>
                </template>
                <template v-else>
                    <el-menu-item :index="item.index">
                        <i :class="item.icon"></i>{{ item.title }}
                    </el-menu-item>
                </template>
            </template>
        </el-menu>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                items: []
            }
        },
        created: function () {
            this.getModules();
        },
        computed: {
            onRoutes(){
                return this.$route.path.replace('/', '');
            }
        },
        methods: {
            getModules() {
                let modules = JSON.parse(localStorage.getItem('user_info')).modulesTree;
                let modulesCnt = modules.length;
                for (let i = 0; i < modulesCnt; i++) {
                    let item = modules[i];
                    let temp = {icon: item.icon_class,index:item.uri||'', title: item.name};
                    let subsContent = [];
                    let children = modules[i]['child'];
                    if (children && children.length) {
                        let childCnt = children.length;
                        for (let j = 0; j < childCnt; j++) {
                            subsContent.push({
                                index: children[j].uri,
                                title: children[j].name
                            })
                        }
                    }
                    if (subsContent.length) {
                        temp.subs = subsContent;
                    }
                    this.items.push(temp);
                }

            },
        },
    }
</script>

<style scoped>
    .sidebar {
        display: block;
        position: absolute;
        width: 12%;
        left: 0;
        top: 70px;
        bottom: 0;
        background: #2E363F;
    }

    .sidebar > ul {
        height: 100%;
    }
</style>
