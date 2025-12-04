<script setup>
import { data as posts } from '../posts.data.js'
console.log(JSON.stringify(posts))
</script>

<template>
    <div class="mt-4 sm:mt-6 lg:mt-8 space-y-2">
        <div
            v-for="{url, title, teaser, fdate} of posts"
            class="pb-2 -ml-2 lg:-ml-4"
        >
            <a
                :href="url"
                class="block no-underline hover:bg-gray-50 p-2 lg:p-4 rounded-md"
            >
                <span class="block no-underline text-sm text-stone-500 mt-1">{{ fdate }}</span>
                {{ title }}
            </a>
        </div>
    </div>
</template>
