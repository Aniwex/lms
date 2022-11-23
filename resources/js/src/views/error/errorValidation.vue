<template>
  <div>
    <span style="color: red" class="db__tc" v-if="errors">
      <span v-for="(err, index) in error" :key="index">{{ err }}<br /></span>
    </span>
  </div>
</template>

<script>
export default {
  props: ["errors", "error"],
};
</script>

<style>
</style>