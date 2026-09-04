<script setup lang="ts">
import { ref } from 'vue';
import { login } from '@/services/api';

const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);

const emit = defineEmits<{
    authenticated: [];
}>();

async function handleLogin(): Promise<void> {
    error.value = '';
    loading.value = true;

    try {
        const response = await login(email.value, password.value);

        sessionStorage.setItem('api_token', response.token);

        emit('authenticated');
    } catch (err: any) {
        error.value =
            err?.errors?.email?.[0] ??
            err?.message ??
            'No se pudo iniciar sesión.';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <main class="login-container">
        <section class="login-card">
            <h1>Gestor de tareas</h1>
            <p>Inicia sesión para continuar.</p>

            <div v-if="error" class="error">
                {{ error }}
            </div>

            <form @submit.prevent="handleLogin">
                <label for="email">Correo electrónico</label>

                <input
                    id="email"
                    v-model="email"
                    type="email"
                    placeholder="demo@example.com"
                    required
                />

                <label for="password">Contraseña</label>

                <input
                    id="password"
                    v-model="password"
                    type="password"
                    placeholder="password123"
                    required
                />

                <button type="submit" :disabled="loading">
                    {{ loading ? 'Iniciando sesión...' : 'Iniciar sesión' }}
                </button>
            </form>
        </section>
    </main>
</template>

<style scoped>
.login-container {
    min-height: 100vh;
    display: grid;
    place-items: center;
    background: #f3f4f6;
}

.login-card {
    width: min(400px, 90%);
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgb(0 0 0 / 10%);
}

h1 {
    margin-bottom: 0.5rem;
}

p {
    margin-bottom: 1.5rem;
    color: #6b7280;
}

form {
    display: grid;
    gap: 0.75rem;
}

input {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
}

button {
    margin-top: 0.5rem;
    padding: 0.75rem;
    border: 0;
    border-radius: 6px;
    cursor: pointer;
}

button:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.error {
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: #fee2e2;
    color: #b91c1c;
    border-radius: 6px;
}
</style>
