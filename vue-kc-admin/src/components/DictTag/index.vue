<template>
  <div v-for="(item, index) in options">
    <el-tag v-if="value == item.value" :key="item.value" :type="item.type" :effect="effect">
      {{ item.label }}
    </el-tag>
  </div>
</template>

<script setup>
const props = defineProps({
  // 数据
  options: {
    type: Array,
    default: null,
  },
  // 当前的值
  value: [Number, String, Array],
  // effect
  effect: {
    type: String,
    default: 'light'
  }
})
</script>

<style lang="scss" scoped></style>