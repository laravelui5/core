<script setup>
function openOptin() {
    window.dispatchEvent(new CustomEvent('open-opt-in'))
}
</script>
<template>
  <div>
    <a
        @click.prevent="openOptin"
        class="text-brand font-medium hover:no-underline cursor-pointer"
    >
      <slot />
    </a>
  </div>
</template>
