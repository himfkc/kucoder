<template>
   <div class="app-container">
      <el-row :gutter="10" class="mb8">
         <el-col :span="1.5">
            <el-tooltip effect="dark" content="刷新" placement="top">
               <el-button size="small" type="info" circle plain @click="refresh">
                  <icon-ep-refresh class="w-4" />
               </el-button>
            </el-tooltip>
         </el-col>
         <el-col :span="1.5">
            <el-button size="small" type="primary" @click="handleAdd" v-auth:add>
               <icon-ep-plus class="icon" />新增</el-button>
         </el-col>
         <el-col :span="1.5">
            <el-button type="danger" :disabled="multiple || queryParams.recycle == 1" @click="handleDelete"
               v-auth:delete size="small"><icon-ep-delete class="icon" />删除</el-button>
         </el-col>
         <!--        <el-col :span="1.5">
          <el-button size="small" type="info" @click="toggleExpandAll">
            <icon-ep-sort class="icon" />展开/折叠</el-button>
        </el-col>-->
         <el-col :span="1.5">
            <el-button size="small" type="info" @click="exportMenu" v-auth:exportPluginMenu>
               <icon-ep-download class="icon" />导出插件菜单</el-button>
         </el-col>
         <el-col :span="1.5">
            <el-button size="small" type="warning" @click="handleRecycle" v-auth:trueDel>
               <icon-ep-delete class="icon" />回收站</el-button>
         </el-col>
         <el-col :span="1.5">
            <el-button v-if="queryParams.recycle == 1" type="info" size="small" @click="refresh"><icon-ep-refresh
                  class="icon" />退出回收站</el-button>
         </el-col>
         <el-col :span="1.5">
            <el-button v-if="queryParams.recycle == 1" type="danger" size="small" @click="handleDeleteTrue"
               v-auth:trueDel><icon-ep-refresh class="icon" />彻底删除</el-button>
         </el-col>
         <!-- <el-col :span="1.5">
            <el-button size="small" type="info" @click="toggleExpandAll">
               <icon-ep-sort class="icon" />展开/折叠</el-button>
         </el-col> -->
         <right-toolbar v-model:showSearch="showSearch" @queryTable="getList"></right-toolbar>
      </el-row>

      <el-table :border="true" v-loading="loading" element-loading-text="数据加载中，请稍候..." :data="menuList" row-key="id"
         :tree-props="{ children: 'children', hasChildren: 'hasChildren' }" style="width:100%"
         @selection-change="handleSelectionChange"  >
         <el-table-column type="selection" width="45" align="center" />
         <el-table-column prop="title" label="菜单名称" :show-overflow-tooltip="true" width="150"></el-table-column>
         <el-table-column prop="icon" label="图标" align="center" width="65">
            <template #default="scope">
               <div class="x c">
                  <!-- <svg-icon :icon-class="scope.row.icon" /> -->
                  <kc-icon :icon="scope.row.icon"></kc-icon>
               </div>
            </template>
         </el-table-column>
         <el-table-column prop="sort" label="排序" align="center" width="55"></el-table-column>
         <el-table-column prop="plugin" label="插件标识" align="center" width="120"></el-table-column>
         <el-table-column prop="name" label="路由name" align="center" :show-overflow-tooltip="true"
            width="180"></el-table-column>
         <el-table-column prop="path" label="路由路径" align="center" :show-overflow-tooltip="true"
            width="150"></el-table-column>
         <el-table-column prop="component" label="路由view" align="center" :show-overflow-tooltip="true"
            width="150"></el-table-column>
         <el-table-column prop="type" label="菜单类型" align="center" :show-overflow-tooltip="true" width="80">
            <template #default="scope">
               <dict-tag :options="enumFieldData.type" :value="scope.row.type" />
            </template>
         </el-table-column>
         <el-table-column prop="keepalive" label="缓存" align="center" :show-overflow-tooltip="true" width="80">
            <template #default="scope">
               <el-switch v-model="scope.row.keepalive" :active-value="1" :inactive-value="0" @change="
                  (val) => {
                     handleChange(val, scope.row, 'keepalive');
                  }
               " />
            </template>
         </el-table-column>
         <el-table-column prop="show" label="显示" align="center" width="80">
            <template #default="scope">
               <el-switch v-model="scope.row.show" :active-value="1" :inactive-value="0" @change="
                  (val) => {
                     handleChange(val, scope.row, 'show');
                  }
               " />
            </template>
         </el-table-column>
         <!-- <el-table-column label="创建时间" align="center" width="160" prop="create_time"></el-table-column> -->
         <el-table-column label="操作" align="center" class-name="" width="200">
            <template #default="scope">
               <!-- 未删除 -->
               <div v-if="!scope.row.delete_time" class="x">
                  <el-button link size="small" type="primary" @click="handleAdd(scope.row)" v-auth:add>
                     <icon-ep-plus class="icon" />新增
                  </el-button>
                  <el-button link size="small" type="success" @click="handleUpdate(scope.row)" v-auth:edit>
                     <icon-ep-edit class="icon" />修改
                  </el-button>
                  <el-button link size="small" type="danger" @click="handleDelete(scope.row)" v-auth:delete>
                     <icon-ep-delete class="icon" />删除
                  </el-button>
               </div>
               <!-- 回收站 -->
               <div class="x" v-else>
                  <el-button link size="small" type="info" @click="handleRestore(scope.row)" v-auth:trueDel>
                     <icon-ep-refreshLeft class="icon" />恢复
                  </el-button>
                  <el-button link size="small" type="danger" @click="handleDeleteTrue(scope.row)" v-auth:trueDel>
                     <icon-ep-delete class="icon" />彻底删除
                  </el-button>
               </div>
            </template>
         </el-table-column>
      </el-table>

      <!-- 添加或修改菜单对话框 -->
      <el-dialog :title="title" v-model="open" width="850px" append-to-body draggable overflow>
         <el-form ref="menuRef" :model="form" :rules="rules" label-width="120px">
            <el-row>
               <el-col :span="24">
                  <el-form-item label="上级菜单">
                     <el-tree-select v-model="form.pid" :data="menuList"
                        :props="{ value: 'id', label: 'title', children: 'children' }" value-key="id"
                        placeholder="选择上级菜单" check-strictly class="elTreeSelect" />
                  </el-form-item>
               </el-col>
               <el-col :span="24">
                  <el-form-item label="菜单类型" prop="type">
                     <el-radio-group v-model="form.type" @change="handleChangeMenuType">
                        <!-- <el-radio value="M">目录</el-radio>
                        <el-radio value="C">菜单</el-radio>
                        <el-radio value="F">按钮</el-radio> -->
                        <el-radio value="dir">目录(控制器目录)</el-radio>
                        <el-radio value="menu">菜单(控制器)</el-radio>
                        <el-radio value="button">按钮(方法)</el-radio>
                        <el-radio value="link">链接</el-radio>
                     </el-radio-group>
                  </el-form-item>
               </el-col>
               <el-col :span="12">
                  <el-form-item label="所属插件" prop="plugin">
                     <el-input v-model="form.plugin" placeholder="请输入菜单所属的插件标识" />
                  </el-form-item>
               </el-col>
               <el-col :span="12">
                  <el-form-item label="菜单名称" prop="title">
                     <el-input v-model="form.title" placeholder="请输入菜单中文名称" />
                  </el-form-item>
               </el-col>

               <el-col :span="12" v-if="form.type !== 'link'">
                  <el-form-item prop="path">
                     <template #label>
                        <span>
                           {{ form.type == 'dir' ? '目录' : form.type == 'menu' ? '控制器' : '方法' }}名称
                           <el-tooltip content="" placement="top">
                              <template #content>
                                 [控制器目录/] 控制器类名 [/方法名] 未开启路由与控制器真实路径一致 开启路由则与路由路径匹配
                                 <br />
                                 <!-- 如：目录`system`，菜单`system/menu`，按钮`system/menu/action` -->
                                 比如路径为：/app/ 插件kucoder/ [应用admin]/
                                 目录system/ 控制器menu/
                                 方法action，则目录名称为system，菜单控制器名称为system/menu，按钮方法名称为system/menu/action
                                 <br />
                                 如无控制器目录，比如/app/ 插件kucoder/ [应用admin]/
                                 控制器menu/ 方法action，则目录名称为空，菜单控制器名称为menu，按钮方法名称为menu/action
                              </template>
                              <el-icon><icon-ep-question-filled /></el-icon>
                           </el-tooltip>
                        </span>
                     </template>
                     <el-input v-model="form.path"
                        :placeholder="`${form.type == 'dir' ? '一级菜单目录可为空 子菜单目录不能为空' : form.type == 'menu' ? '[目录/]控制器(相对路径 不含插件及应用名)' : '[目录/]控制器/方法(相对路径 不含插件及应用名)'}`" />
                  </el-form-item>
               </el-col>
               <el-col :span="12" v-if="form.type == 'menu'">
                  <el-form-item prop="component">
                     <template #label>
                        <span>
                           vue文件名
                           <el-tooltip
                              content="vue的view文件 在`vue-kc-admin/views`目录下 带目录的vue文件要加上目录 比如views /kucoder /system目录 /menu /index.vue文件 则填写system/menu"
                              placement="top">
                              <el-icon><icon-ep-question-filled /></el-icon>
                           </el-tooltip>
                        </span>
                     </template>
                     <el-input v-model="form.component" placeholder="不填则默认为与控制器一致 建议一致" maxlength="255" />
                  </el-form-item>
               </el-col>
               <el-col :span="12" v-if="form.type == 'menu'">
                  <el-form-item>
                     <el-input v-model="form.query" placeholder="请输入路由参数" maxlength="255" />
                     <template #label>
                        <span>
                           路由参数
                           <el-tooltip content="访问路由的默认传递参数，如：`id=1&title=hello`" placement="top">
                              <el-icon><icon-ep-question-filled /></el-icon>
                           </el-tooltip>
                        </span>
                     </template>
                  </el-form-item>
               </el-col>
               <el-col :span="12" v-if="form.type == 'menu'">
                  <el-form-item>
                     <template #label>
                        <span>
                           <el-tooltip content="选择是否会被`keep-alive`缓存 默认菜单控制器缓存" placement="top">
                              <el-icon><icon-ep-question-filled /></el-icon>
                           </el-tooltip>
                           是否缓存
                        </span>
                     </template>
                     <el-radio-group v-model="form.keepalive">
                        <el-radio :value="0">不缓存</el-radio>
                        <el-radio :value="1">缓存</el-radio>
                     </el-radio-group>
                  </el-form-item>
               </el-col>
               <el-col :span="12" v-if="form.type != 'button'">
                  <el-form-item label="菜单图标" prop="icon">
                     <el-popover placement="bottom-start" :width="540" trigger="click">
                        <template #reference>
                           <el-input v-model="form.icon" placeholder="点击选择图标" @blur="showSelectIcon" readonly>
                              <template #prefix>
                                 <!-- <svg-icon v-if="form.icon" :icon-class="form.icon" class="el-input__icon"
                                    style="height: 32px; width: 16px" /> -->
                                 <kc-icon v-if="form.icon" :icon="form.icon" :icon-class="form.icon"
                                    class="el-input__icon" style="height: 32px; width: 16px"></kc-icon>
                                 <el-icon v-else class="h-6 w-4"><icon-ep-search /></el-icon>
                              </template>
                           </el-input>
                        </template>
                        <icon-select ref="iconSelectRef" @selected="selected" :active-icon="form.icon" />
                     </el-popover>
                  </el-form-item>
               </el-col>
               <el-col :span="12">
                  <el-form-item label="显示排序" prop="sort">
                     <el-input-number v-model="form.sort" controls-position="right" :min="0" />
                  </el-form-item>
               </el-col>
               <el-col :span="24" v-if="form.type === 'link'">
                  <el-form-item prop="link_url">
                     <template #label>
                        <span>
                           链接地址
                           <el-tooltip content="访问的外链url,需要以`http(s)://`开头" placement="top">
                              <el-icon><icon-ep-question-filled /></el-icon>
                           </el-tooltip>
                        </span>
                     </template>
                     <el-input v-model="form.link_url" placeholder="请输入外链地址 需要以`http(s)://`开头" />
                  </el-form-item>
               </el-col>
               <el-col :span="12" v-if="form.type !== 'button'">
                  <el-form-item>
                     <template #label>
                        <span>
                           <el-tooltip content="选择隐藏则路由将不会出现在侧边栏，但仍然可以访问" placement="top">
                              <el-icon><icon-ep-question-filled /></el-icon>
                           </el-tooltip>
                           显示状态
                        </span>
                     </template>
                     <el-radio-group v-model="form.show">
                        <el-radio v-for="dict in enumFieldData.show" :key="dict.value" :value="dict.value">{{ dict.label
                           }}</el-radio>
                     </el-radio-group>
                  </el-form-item>
               </el-col>
               <el-col :span="12" v-if="form.type === 'menu' && formOperation === 'add'">
                  <el-form-item>
                     <template #label>
                        <span>
                           <el-tooltip content="自动添加菜单控制器的增删改查子菜单按钮" placement="top">
                              <el-icon><icon-ep-question-filled /></el-icon>
                           </el-tooltip>
                           添加菜单按钮
                        </span>
                     </template>
                     <el-radio-group v-model="form.autoMenuBtns">
                        <el-radio :value="true">添加</el-radio>
                        <el-radio :value="false">不添加</el-radio>
                     </el-radio-group>
                  </el-form-item>
               </el-col>
               <!-- <el-col :span="12">
                  <el-form-item>
                     <template #label>
                        <span>
                           菜单状态
                           <el-tooltip content="选择停用则路由将不会出现在侧边栏，也不能被访问" placement="top">
                              <el-icon><icon-ep-question-filled /></el-icon>
                           </el-tooltip>
                        </span>
                     </template>
                     <el-radio-group v-model="form.status">
                        <el-radio v-for="dict in enumFieldData.status" :key="dict.value" :value="dict.value">{{
                           dict.label
                        }}</el-radio>
                     </el-radio-group>
                  </el-form-item>
               </el-col> -->
            </el-row>
         </el-form>
         <template #footer>
            <div class="dialog-footer">
               <el-button type="primary" @click="submitForm">确 定</el-button>
               <el-button @click="cancel">取 消</el-button>
            </div>
         </template>
      </el-dialog>
   </div>
</template>

<script setup>
import {
   addMenu,
   delMenu,
   listMenu,
   updateMenu,
   change,
   exportPluginMenu,
   trueDel,
} from "@/api/kucoder/system/menu";
import IconSelect from "@/components/IconSelect";
import { handleEnumField, clone, join_path, kcPrompt, kcMsg, kcAlert } from "@/utils/kucoder";
import usePermissionStore from "@/store/modules/permission";
// import { ElLoading } from "element-plus";

defineOptions({
   name: "kucoder_system_menu",
});

const { proxy } = getCurrentInstance();
// const { sys_show_hide, sys_normal_disable } = proxy.useDict("sys_show_hide", "sys_normal_disable")
// const userStore = useUserStore()
const permissionStore = usePermissionStore();
// const loadingInstance = ElLoading.service({})

const menuList = ref([]);
const open = ref(false);
const loading = ref(true);
const showSearch = ref(true);
const title = ref("");
const isExpandAll = ref(false);
const refreshTable = ref(true);
const iconSelectRef = ref(null);
const ids = ref([])
const single = ref(true)
const multiple = ref(true)

const enumFieldData = ref({});
const formOperation = ref("");
// const refresh = ref(false)

const data = reactive({
   form: {},
   queryParams: {
      title: undefined,
      visible: undefined,
      recycle: 0, // 是否查询回收站数据
   },
   rules: {
      plugin: [{ required: true, message: "插件标识不能为空", trigger: "blur" }],
      title: [{ required: true, message: "菜单名称不能为空", trigger: "blur" }],
      // path: [{ required: true, message: "路由地址不能为空", trigger: "blur" }],
   },
});

const { queryParams, form, rules } = toRefs(data);

function exportMenu() {
   kcPrompt('要导出的插件标识').then(res => {
      console.log("要导出的插件标识", res);
      if (res.action === 'confirm') {
         if (!res.value) {
            kcMsg('请输入要导出的插件标识', 'error')
            return;
         }
         const pluginName = res.value
         exportPluginMenu({ pluginName })
            .then(res => {
               kcAlert(`导出成功，请在plugin/${pluginName}/config/menu.php中查看`, '导出成功', { type: 'success' })
            });
      }
   });
}

/** 查询菜单列表 */
function getList() {
   loading.value = true;
   // const loadingInstance = ElLoading.service({});
   listMenu(queryParams.value)
      .then(({ data, msg, code }) => {
         console.log("菜单数据列表", data);
         menuList.value = proxy.handleTree(data.menus, "id", "pid");
         enumFieldData.value = handleEnumField(data.enumFieldData);
         console.log("enumFieldData.value", enumFieldData.value);
         nextTick(() => {
            loading.value = false
            // loadingInstance.close();
         })
      })
      .catch(err => {
         console.warn(err)
         loading.value = false
      })
}

function handleChangeMenuType(val) {
   if (val === 'menu') {
      form.value.keepalive = 1
   }
}

/** 多选框选中数据 */
function handleSelectionChange(selection) {
   ids.value = selection.map(item => item.id)
   single.value = selection.length != 1
   multiple.value = !selection.length
}

function handleChange(val, row, field) {
   if (field === 'keepalive' && Number(val) === 1) {
      if (row.type !== 'menu') {
         let msg = ''
         switch (row.type) {
            case 'dir':
               msg = '目录不能开启缓存';
               break;
            case 'button':
               msg = '按钮不能开启缓存'
               break;
            case 'link':
               msg = '链接不能开启缓存'
               break;
         }
         proxy.$modal.msgWarning(msg)
         row.keepalive = Number(!row.keepalive)
         return false;
      }
   }
   if (queryParams.value.recycle == 1) {
      proxy.$modal.msgWarning("回收站数据不能修改状态")
      row.show = Number(!row.show)
      return false;
   }
   change({ id: row.id, [field]: val })
      .then(() => {
         proxy.$modal.msgSuccess("更新成功")
      })
      .then(() => {
         refreshRoute(); // 刷新路由
      })
      .catch(err => {
         proxy.$modal.msgWarning(err)
      })
}
// 刷新
function refresh() {
   queryParams.value.pageNum = 1
   queryParams.value.recycle = 0 // 重置回收站查询
   getList()
}
/** 回收站按钮操作 */
function handleRecycle() {
   queryParams.value.recycle = 1; // 设置查询回收站数据
   getList(); // 重新查询菜单列表
}
/** 回收站恢复按钮操作 */
function handleRestore(row) {
   const ids = row.id
   proxy.$modal.confirm('是否确认恢复角色编号为"' + ids + '"的数据项?').then(function () {
      return updateMenu({ id: ids, delete_restore: 1 })
   }).then(() => {
      getList()
      proxy.$modal.msgSuccess("恢复成功")
   }).catch(() => { })
}
/**回收站彻底删除按钮操作 */
function handleDeleteTrue(row = {}) {
   const deleteIds = row.id || ids.value
   proxy.$modal.confirm('是否确认彻底删除角色编号为"' + deleteIds + '"的数据项?').then(function () {
      return trueDel({ id: deleteIds })
   }).then(() => {
      getList()
      proxy.$modal.msgSuccess("彻底删除成功")
   }).catch(() => { })
}

/** 取消按钮 */
function cancel() {
   open.value = false;
   if (formOperation.value === "add") {
      reset();
   }
}

/** 表单重置 */
function reset() {
   form.value = {
      id: undefined,
      pid: 0,
      title: undefined,
      icon: undefined,
      type: "dir",
      sort: 999,
      // isFrame: "1",
      link_url: "",
      keepalive: 0,
      show: 1,
      // 路由路径
      path: "",
      // 路由组件
      component: "",
      // 路由参数
      query: "",
      // status: 1，
      // 菜单自动添加子按钮方法
      autoMenuBtns: true,
   };
   proxy.resetForm("menuRef");
}

/** 展示下拉图标 */
function showSelectIcon() {
   iconSelectRef.value.reset();
}

/** 选择图标 */
function selected(name) {
   form.value.icon = name;
}

/** 搜索按钮操作 */
function handleQuery() {
   getList();
}

/** 重置按钮操作 */
function resetQuery() {
   proxy.resetForm("queryRef");
   handleQuery();
}

function refreshRoute() {
   permissionStore.routes = [];
   permissionStore.generateRoutes().then(({ data, msg, code }) => {
      console.log("更新menu后的新路由", data);
   });
}

/** 新增按钮操作 */
function handleAdd(row) {
   reset();
   // getTreeselect()
   if (row != null && row.id) {
      form.value.pid = row.id;
      form.value.plugin = row.plugin; // 继承上级菜单的插件标识
      if (row.path) {
         form.value.path = row.path + "/"; // 继承上级菜单的路径
      }
      if (row.type === "dir") {
         form.value.type = "menu"; // 如果是目录类型，则子菜单默认type为menu
         form.value.keepalive = 1; //菜单控制器默认开启缓存
      } else if (row.type === "menu") {
         form.value.type = "button"; // 如果是menu类型，则子菜单默认type为button
      }
   } else {
      form.value.pid = 0;
   }
   formOperation.value = "add";
   open.value = true;
   title.value = "添加菜单";
}

/** 修改按钮操作 */
async function handleUpdate(row) {
   console.log("修改菜单row:", row);
   formOperation.value = "edit";
   form.value = clone(row);
   open.value = true;
   title.value = "修改菜单";
}

/** 展开/折叠操作 */
/* function toggleExpandAll() {
   console.log("展开/折叠操作");
   refreshTable.value = false;
   isExpandAll.value = !isExpandAll.value;
   nextTick(() => {
      refreshTable.value = true;
   });
} */

/** 提交按钮 */
function submitForm() {
   proxy.$refs["menuRef"].validate((valid) => {
      if (form.value.path.startsWith("/")) {
         form.value.path = form.value.path.substring(1); // 去掉开头的斜杠
      }
      if (form.value.type === "menu") {
         // 如果是menu菜单类型且没有填写view组件，则默认 插件/路径
         if (!form.value.component) {
            // form.value.component = join_path(form.value.plugin, form.value.path);
            form.value.component = form.value.path
         }
         // 菜单必须要有目录
         if (!form.value.pid) {
            proxy.$modal.msgError("菜单控制器的上次菜单必须是目录");
            return;
         }
      }
      // 菜单与按钮的路径不能为空
      if (form.value.type === "menu" || form.value.type === "button") {
         if (!form.value.path) {
            proxy.$modal.msgError("菜单或按钮的名称不能为空");
            return;
         }
      }
      // 链接类型需要填写外链地址
      if (form.value.type === "link") {
         if (!form.value.link_url) {
            proxy.$modal.msgError("链接类型需要填写外链地址");
            return;
         }
         // 链接要以http或https开头
         if (
            !form.value.link_url.startsWith("http://") &&
            !form.value.link_url.startsWith("https://")
         ) {
            proxy.$modal.msgError("外链地址需要以`http(s)://`开头");
            return;
         }
      }
      //按钮不显示在侧边栏
      if (form.value.type === "button") {
         form.value.show = 0;
      }
      // 子目录的路径path不能为空
      if (form.value.pid && !form.value.path && form.value.type === "dir") {
         proxy.$modal.msgError("子目录的路径不能为空");
         return;
      }
      // 一级目录的路径为空时默认为插件名
      /* if (form.value.pid === 0 && !form.value.path && form.value.type === "dir") {
         form.value.path = form.value.plugin
      } */
      if (valid) {
         if (form.value.id != undefined) {
            updateMenu(form.value)
               .then(() => {
                  proxy.$modal.msgSuccess("修改成功");
                  open.value = false;
                  getList();
                  // refreshRoute() // 刷新路由
               })
               .then(() => {
                  refreshRoute(); // 刷新路由
               });
         } else {
            addMenu(form.value)
               .then(() => {
                  proxy.$modal.msgSuccess("新增成功");
                  open.value = false;
                  getList();
                  // refreshRoute() // 刷新路由
               })
               .then(() => {
                  refreshRoute(); // 刷新路由
               });
         }
      }
   });
}

/** 删除按钮操作 */
function handleDelete(row) {
   const deleteIds = row.id || ids.value
   proxy.$modal
      .confirm('是否确认删除编号为"' + deleteIds + '"的数据项？')
      .then(function () {
         return delMenu({ id: deleteIds });
      })
      .then(() => {
         getList();
         proxy.$modal.msgSuccess("删除成功");
      })
      .then(() => {
         refreshRoute(); // 刷新路由
      })
      .catch(() => { });
}

getList();
</script>

<style lang="scss" scoped>
.elTreeSelect {
   :deep(.el-tree-node) {
      font-weight: bold;
      margin-bottom: 0.2em;
   }
}
</style>
