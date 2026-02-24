<template>
    <div class="dashboard-container">
        <!-- 顶部统计卡片 -->
        <el-row :gutter="20" class="stats-row">
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <el-card class="stat-card" shadow="hover">
                    <div class="stat-content bold">
                        此页面仅为系统功能演示 非真实数据
                    </div>
                </el-card>
            </el-col>
        </el-row>

        <!-- 顶部统计卡片 -->
        <el-row :gutter="20" class="stats-row">
            <el-col :xs="12" :sm="12" :md="6" :lg="6" :xl="6" v-for="item in statsData" :key="item.title">
                <el-card class="stat-card" shadow="hover">
                    <div class="stat-content">
                        <div class="stat-icon" :style="{ background: item.color }">
                            <el-icon :size="28">
                                <component :is="item.icon" />
                            </el-icon>
                        </div>
                        <div class="stat-info">
                            <div class="stat-title">{{ item.title }}</div>
                            <div class="stat-value">{{ item.value }}</div>
                            <div class="stat-trend" :class="item.trend >= 0 ? 'up' : 'down'">
                                <el-icon>
                                    <component :is="item.trend >= 0 ? 'CaretTop' : 'CaretBottom'" />
                                </el-icon>
                                <span>{{ Math.abs(item.trend) }}%</span>
                                <span class="trend-label">较昨日</span>
                            </div>
                        </div>
                    </div>
                </el-card>
            </el-col>
        </el-row>

        <!-- 图表区域 -->
        <el-row :gutter="20" class="charts-row">
            <!-- 访问趋势图 -->
            <el-col :xs="24" :sm="24" :md="16" :lg="16" :xl="16">
                <el-card class="chart-card" shadow="hover">
                    <template #header>
                        <div class="card-header">
                            <span>访问趋势</span>
                            <el-radio-group v-model="visitRange" size="small" @change="handleVisitRangeChange">
                                <el-radio-button label="week">近7天</el-radio-button>
                                <el-radio-button label="month">近30天</el-radio-button>
                            </el-radio-group>
                        </div>
                    </template>
                    <div class="chart-container">
                        <v-chart class="chart" :option="visitTrendOption" autoresize />
                    </div>
                </el-card>
            </el-col>

            <!-- 用户来源分布 -->
            <el-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                <el-card class="chart-card" shadow="hover">
                    <template #header>
                        <span>用户来源分布</span>
                    </template>
                    <div class="chart-container">
                        <v-chart class="chart" :option="userSourceOption" autoresize />
                    </div>
                </el-card>
            </el-col>
        </el-row>

        <el-row :gutter="20" class="charts-row">
            <!-- 订单统计 -->
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                <el-card class="chart-card" shadow="hover">
                    <template #header>
                        <span>订单统计</span>
                    </template>
                    <div class="chart-container">
                        <v-chart class="chart" :option="orderOption" autoresize />
                    </div>
                </el-card>
            </el-col>

            <!-- 系统状态 -->
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                <el-card class="chart-card" shadow="hover">
                    <template #header>
                        <span>系统状态</span>
                    </template>
                    <div class="system-status">
                        <div v-for="item in systemStatus" :key="item.name" class="status-item">
                            <div class="status-label">{{ item.name }}</div>
                            <div class="status-bar">
                                <div class="status-progress"
                                    :style="{ width: item.percent + '%', background: item.color }">
                                </div>
                            </div>
                            <div class="status-value">{{ item.percent }}%</div>
                        </div>
                    </div>
                </el-card>
            </el-col>
        </el-row>

        <!-- 快捷操作和最新动态 -->
        <el-row :gutter="20" class="info-row">
            <!-- 快捷操作 -->
            <el-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                <el-card class="info-card" shadow="hover">
                    <template #header>
                        <span>快捷操作</span>
                    </template>
                    <div class="quick-actions">
                        <div v-for="action in quickActions" :key="action.name" class="action-item"
                            @click="handleQuickAction(action)">
                            <div class="action-icon" :style="{ color: action.color }">
                                <el-icon :size="24">
                                    <component :is="action.icon" />
                                </el-icon>
                            </div>
                            <div class="action-name">{{ action.name }}</div>
                        </div>
                    </div>
                </el-card>
            </el-col>

            <!-- 最新动态 -->
            <el-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                <el-card class="info-card" shadow="hover">
                    <template #header>
                        <span>最新动态</span>
                    </template>
                    <div class="activities-list">
                        <div v-for="(activity, index) in recentActivities" :key="index" class="activity-item">
                            <div class="activity-icon" :style="{ background: activity.color }">
                                <el-icon>
                                    <component :is="activity.icon" />
                                </el-icon>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">{{ activity.title }}</div>
                                <div class="activity-time">{{ activity.time }}</div>
                            </div>
                        </div>
                    </div>
                </el-card>
            </el-col>

            <!-- 待办事项 -->
            <el-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                <el-card class="info-card" shadow="hover">
                    <template #header>
                        <div class="card-header">
                            <span>待办事项</span>
                            <el-tag type="info" size="small">{{ todoList.length }}</el-tag>
                        </div>
                    </template>
                    <div class="todo-list">
                        <div v-for="(todo, index) in todoList" :key="index" class="todo-item">
                            <el-checkbox v-model="todo.completed" @change="handleTodoChange(todo)">
                                <span :class="{ 'completed': todo.completed }">{{ todo.title }}</span>
                            </el-checkbox>
                            <el-tag :type="todo.priority" size="small">{{ todo.priorityText }}</el-tag>
                        </div>
                    </div>
                </el-card>
            </el-col>
        </el-row>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import VChart from 'vue-echarts'
import { use } from 'echarts/core'
import { CanvasRenderer } from 'echarts/renderers'
import { LineChart, PieChart, BarChart } from 'echarts/charts'
import { TitleComponent, TooltipComponent, LegendComponent, GridComponent } from 'echarts/components'

defineOptions({
    name: 'Index'
})

// 注册 ECharts 组件Index
use([
    CanvasRenderer,
    LineChart,
    PieChart,
    BarChart,
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent
])

import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import {
    User,
    ShoppingBag,
    DocumentCopy,
    TrendCharts,
    Setting,
    Plus,
    Edit,
    Upload,
    Download,
    Calendar,
    Bell,
    CaretTop,
    CaretBottom
} from '@element-plus/icons-vue'

const router = useRouter()

// 统计数据（假数据）
const statsData = ref([
    { title: '总用户数', value: '12,580', trend: 12.5, color: '#409EFF', icon: 'User' },
    { title: '订单数量', value: '3,256', trend: 8.2, color: '#67C23A', icon: 'ShoppingBag' },
    { title: '访问量', value: '89,432', trend: -3.2, color: '#E6A23C', icon: 'TrendCharts' },
    { title: '收入总额', value: '¥256,800', trend: 15.8, color: '#F56C6C', icon: 'DocumentCopy' }
])

// 访问趋势范围
const visitRange = ref('week')

// 访问趋势数据（假数据）
const weekVisitData = {
    dates: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
    values: [1200, 1320, 1100, 1450, 1680, 1920, 1780]
}
const monthVisitData = {
    dates: ['1日', '5日', '10日', '15日', '20日', '25日', '30日'],
    values: [3200, 2800, 3500, 4200, 3800, 4500, 5200]
}

// 访问趋势图表配置
const visitTrendOption = computed(() => ({
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'shadow'
        }
    },
    legend: {
        data: ['访问量']
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: {
        type: 'category',
        data: visitRange.value === 'week' ? weekVisitData.dates : monthVisitData.dates,
        boundaryGap: false
    },
    yAxis: {
        type: 'value'
    },
    series: [
        {
            name: '访问量',
            type: 'line',
            data: visitRange.value === 'week' ? weekVisitData.values : monthVisitData.values,
            smooth: true,
            lineStyle: {
                color: '#409EFF'
            },
            areaStyle: {
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [
                        { offset: 0, color: 'rgba(64, 158, 255, 0.3)' },
                        { offset: 1, color: 'rgba(64, 158, 255, 0.05)' }
                    ]
                }
            }
        }
    ]
}))

// 用户来源数据（假数据）
const userSourceData = ref([
    { name: '直接访问', value: 35 },
    { name: '搜索引擎', value: 28 },
    { name: '社交媒体', value: 20 },
    { name: '外部链接', value: 12 },
    { name: '其他', value: 5 }
])

// 用户来源分布图表配置
const userSourceOption = ref({
    tooltip: {
        trigger: 'item',
        formatter: '{b}: {c}%'
    },
    legend: {
        bottom: '0%',
        left: 'center'
    },
    series: [
        {
            type: 'pie',
            radius: ['40%', '70%'],
            avoidLabelOverlap: false,
            itemStyle: {
                borderRadius: 10,
                borderColor: '#fff',
                borderWidth: 2
            },
            label: {
                show: false,
                position: 'center'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: 20,
                    fontWeight: 'bold'
                }
            },
            labelLine: {
                show: false
            },
            data: userSourceData.value
        }
    ]
})

// 订单数据（假数据）
const orderData = ref({
    months: ['1月', '2月', '3月', '4月', '5月', '6月'],
    values: [120, 132, 145, 158, 142, 168]
})

// 订单统计图表配置
const orderOption = ref({
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'shadow'
        }
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: {
        type: 'category',
        data: orderData.value.months
    },
    yAxis: {
        type: 'value'
    },
    series: [
        {
            name: '订单数',
            type: 'bar',
            data: orderData.value.values,
            itemStyle: {
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [
                        { offset: 0, color: '#67C23A' },
                        { offset: 1, color: '#95D475' }
                    ]
                },
                borderRadius: [4, 4, 0, 0]
            },
            barWidth: '40%'
        }
    ]
})

// 系统状态（假数据）
const systemStatus = ref([
    { name: 'CPU使用率', percent: 45, color: '#409EFF' },
    { name: '内存使用率', percent: 62, color: '#67C23A' },
    { name: '磁盘使用率', percent: 38, color: '#E6A23C' },
    { name: '网络负载', percent: 28, color: '#909399' }
])

// 快捷操作
const quickActions = ref([
    { name: '新增用户', icon: 'Plus', color: '#409EFF', route: '/member/list' },
    { name: '发布内容', icon: 'Edit', color: '#67C23A', route: '/content/list' },
    { name: '系统设置', icon: 'Setting', color: '#E6A23C', route: '/system/config' },
    { name: '数据导出', icon: 'Download', color: '#F56C6C', route: '/data/export' },
    { name: '文件上传', icon: 'Upload', color: '#909399', route: '/file/upload' },
    { name: '查看日志', icon: 'Calendar', color: '#606266', route: '/system/log' }
])

// 最新动态（假数据）
const recentActivities = ref([
    { title: '用户 张三 完成了订单支付', time: '5分钟前', icon: 'Bell', color: '#409EFF' },
    { title: '系统自动备份完成', time: '30分钟前', icon: 'Calendar', color: '#67C23A' },
    { title: '新版本 v2.1.0 发布', time: '2小时前', icon: 'DocumentCopy', color: '#E6A23C' },
    { title: '检测到 3 个待审核评论', time: '3小时前', icon: 'Bell', color: '#F56C6C' },
    { title: '系统自动更新完成', time: '5小时前', icon: 'Setting', color: '#909399' }
])

// 待办事项（假数据）
const todoList = ref([
    { title: '审核用户注册申请', completed: false, priority: 'danger', priorityText: '紧急' },
    { title: '回复客户反馈邮件', completed: false, priority: 'warning', priorityText: '重要' },
    { title: '更新系统文档', completed: true, priority: 'info', priorityText: '普通' },
    { title: '优化数据库查询', completed: false, priority: 'info', priorityText: '普通' },
    { title: '检查安全补丁', completed: false, priority: 'warning', priorityText: '重要' }
])

// 访问范围切换
const handleVisitRangeChange = (val) => {
    // TODO: 调用后端API获取真实数据
    // getVisitTrend({ range: val })
}

// 快捷操作点击
const handleQuickAction = (action) => {
    router.push(action.route)
}

// 待办事项状态改变
const handleTodoChange = (todo) => {
    // TODO: 调用后端API更新待办状态
    console.log('Todo changed:', todo)
    ElMessage.success(todo.completed ? '已完成' : '已标记为未完成')
}

// 初始化数据（预留后端API调用）
const initData = async () => {
    try {
        // TODO: 取消注释以调用真实API
        // const stats = await getDashboardStats()
        // const visit = await getVisitTrend({ range: visitRange.value })
        // const growth = await getUserGrowth()
        // const order = await getOrderData()
        // const status = await getSystemStatus()
        // const actions = await getQuickActions()
        // const activities = await getRecentActivities()
        // const todos = await getTodoList()

        // statsData.value = stats
        // userSourceData.value = growth
        // orderData.value = order
        // systemStatus.value = status
        // quickActions.value = actions
        // recentActivities.value = activities
        // todoList.value = todos
    } catch (error) {
        console.error('Failed to load dashboard data:', error)
    }
}

// 初始化
initData()
</script>

<style lang="scss" scoped>
.dashboard-container {
    padding: 20px;
    background: #f5f7fa;
    min-height: 100%;
}

.stats-row {
    margin-bottom: 20px;
}

.stat-card {
    border-radius: 8px;
    transition: all 0.3s;

    &:hover {
        transform: translateY(-4px);
    }
}

.stat-content {
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.stat-info {
    flex: 1;
}

.stat-title {
    font-size: 14px;
    color: #909399;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #303133;
    margin-bottom: 8px;
}

.stat-trend {
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;

    &.up {
        color: #67C23A;
    }

    &.down {
        color: #F56C6C;
    }

    .trend-label {
        color: #909399;
    }
}

.charts-row {
    margin-bottom: 20px;
}

.chart-card {
    border-radius: 8px;

    :deep(.el-card__header) {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    :deep(.el-card__body) {
        padding: 20px;
    }
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
}

.chart-container {
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;

    .chart {
        width: 100%;
        height: 100%;
    }
}

.system-status {
    padding: 20px 0;
}

.status-item {
    display: flex;
    align-items: center;
    margin-bottom: 24px;

    &:last-child {
        margin-bottom: 0;
    }
}

.status-label {
    width: 100px;
    font-size: 14px;
    color: #606266;
}

.status-bar {
    flex: 1;
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
    margin: 0 16px;
}

.status-progress {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.status-value {
    width: 50px;
    text-align: right;
    font-size: 14px;
    font-weight: 600;
    color: #303133;
}

.info-row {
    margin-bottom: 20px;
}

.info-card {
    border-radius: 8px;
    height: 100%;

    :deep(.el-card__body) {
        padding: 20px;
    }
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px 8px;
    border-radius: 8px;
    background: #f5f7fa;
    cursor: pointer;
    transition: all 0.3s;

    &:hover {
        background: #ecf5ff;
        transform: translateY(-2px);
    }
}

.action-icon {
    margin-bottom: 8px;
}

.action-name {
    font-size: 13px;
    color: #606266;
}

.activities-list {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;

    &:last-child {
        border-bottom: none;
    }
}

.activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    font-size: 16px;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-size: 14px;
    color: #303133;
    margin-bottom: 4px;
}

.activity-time {
    font-size: 12px;
    color: #909399;
}

.todo-list {
    max-height: 300px;
    overflow-y: auto;
}

.todo-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;

    &:last-child {
        border-bottom: none;
    }

    :deep(.el-checkbox__label) {
        span.completed {
            text-decoration: line-through;
            color: #c0c4cc;
        }
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 10px;
    }

    .stats-row,
    .charts-row,
    .info-row {
        margin-bottom: 10px;
    }

    .stat-content {
        flex-direction: column;
        text-align: center;
    }

    .stat-value {
        font-size: 20px;
    }

    .stat-trend {
        justify-content: center;
    }

    .chart-container {
        height: 250px;
    }

    .quick-actions {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
