<script setup lang="ts">
import { ref } from 'vue';
import Login from './pages/Auth/Login.vue';
import TasksIndex from './pages/Tasks/Index.vue';

const authenticated = ref(
    sessionStorage.getItem('api_token') !== null,
);

function handleAuthenticated(): void {
    authenticated.value = true;
}
</script>

<template>
    <Login
        v-if="!authenticated"
        @authenticated="handleAuthenticated"
    />

    <TasksIndex v-else />
</template>
