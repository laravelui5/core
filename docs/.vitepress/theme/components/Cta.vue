<script setup>
function openOptIn() {
    window.dispatchEvent(new CustomEvent('open-opt-in'))
}
</script>
<template>
  <a
      class="ml-4 cursor-pointer"
      @click="openOptIn"
  >Join the Journey ⇢</a>
</template>
