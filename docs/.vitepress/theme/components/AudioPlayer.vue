<script setup>
import { ref } from 'vue'

const props = defineProps({
    src: { type: String, required: true },
    caption: { type: String, default: '' }
})

const isPlaying = ref(false)
const audio = ref(null)

function togglePlay() {
    if (!audio.value) return

    if (isPlaying.value) {
        audio.value.pause()
    } else {
        audio.value.play()
    }
    isPlaying.value = !isPlaying.value
}
</script>

<template>
    <div class="flex items-center gap-3 mb-6 text-[0.95rem] text">
        <audio ref="audio" :src="src" @ended="isPlaying = false" preload="metadata"></audio>

        <button
            @click="togglePlay"
            class="text-xl leading-none hover:scale-110 transition-transform duration-150"
        >
            <span v-if="!isPlaying">▶</span>
            <span v-else>■</span>
        </button>

        <p v-if="caption" class="italic m-0">
            {{ caption }}
        </p>
    </div>
</template>

<style scoped>
.text {
    color: #1e2939
}
.dark .text {
    color: #dfdfd6
}
</style>
