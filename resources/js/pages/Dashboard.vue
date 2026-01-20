<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import announcements from '@/routes/announcements';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
        <div class="grid grid-flow-col gap-4 grid-rows-3 grid-cols-3 h-full">
            <div class="row-span-3 col-span-1 widget-container relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <announcements-list></announcements-list>
            </div>
            <div class="row-span-1 col-span-2 widget-container relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
            <div class="col-span-2 row-span-2 widget-container relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
        </div>

    </AppLayout>
</template>

<style>
    .widget-container {
        background: #e7e7e7;
        height: 100%;
        width: 100%;
    }
    
    .scroll-container {
        max-height: 88%;/*72%;*/
        overflow: scroll;
        position: absolute;
        min-width: 93%;
    }
    .scroll-container::-webkit-scrollbar {
        display: none; /* For Chrome, Safari, and Edge */
    }
    .grid {
        height: 88vh;
    }
</style>>