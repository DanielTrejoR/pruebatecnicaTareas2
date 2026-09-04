<script setup lang="ts">
import { onMounted, ref } from 'vue';
import {
    completeTask,
    createTask,
    createUser,
    deleteTask,
    getTasks,
    getUsers,
} from '../../services/api';
import type { Task, User } from '../../types/task';

const users = ref<User[]>([]);
const tasks = ref<Task[]>([]);
const selectedUser = ref<User | null>(null);

const loadingUsers = ref(false);
const loadingTasks = ref(false);
const error = ref('');

const title = ref('');
const description = ref('');

const filter = ref<'all' | 'pending' | 'completed'>('all');
const sort = ref<'date' | 'title'>('date');


const newUserName = ref('');
const newUserEmail = ref('');
const newUserPassword = ref('');
const creatingUser = ref(false);
const userError = ref('');

//submit user
async function submitUser(): Promise<void> {
    userError.value = '';

    if (
        !newUserName.value ||
        !newUserEmail.value ||
        !newUserPassword.value
    ) {
        userError.value = 'Todos los campos son obligatorios.';
        return;
    }

    creatingUser.value = true;

    try {
        const response = await createUser(
            newUserName.value,
            newUserEmail.value,
            newUserPassword.value,
        );

        users.value.push(response.data);

        newUserName.value = '';
        newUserEmail.value = '';
        newUserPassword.value = '';
    } catch (err: unknown) {
        const error = err as {
            message?: string;
            errors?: Record<string, string[]>;
        };

        userError.value =
            error.errors?.email?.[0] ??
            error.errors?.name?.[0] ??
            error.errors?.password?.[0] ??
            error.message ??
            'No se pudo crear el usuario.';
    } finally {
        creatingUser.value = false;
    }
}

async function loadUsers(): Promise<void> {
    loadingUsers.value = true;
    error.value = '';

    try {
        const response = await getUsers();

        users.value = response.data;

        if (users.value.length > 0) {
            await selectUser(users.value[0]);
        }
    } catch {
        error.value = 'No se pudieron cargar los usuarios.';
    } finally {
        loadingUsers.value = false;
    }
}

async function selectUser(user: User) {
    selectedUser.value = user;
    await loadTasks();
}

async function loadTasks() {
    if (!selectedUser.value) {
        return;
    }

    loadingTasks.value = true;
    error.value = '';

    try {
        const response = await getTasks(
            selectedUser.value.id,
            {
                completed:
                    filter.value === 'all'
                        ? undefined
                        : filter.value === 'completed',
                sort: sort.value,
            },
        );

        tasks.value = response.data;
    } catch {
        error.value = 'No se pudieron cargar las tareas.';
    } finally {
        loadingTasks.value = false;
    }
}

async function submitTask() {
    if (!selectedUser.value || !title.value.trim()) {
        return;
    }

    try {
        await createTask(
            selectedUser.value.id,
            title.value,
            description.value,
        );

        title.value = '';
        description.value = '';

        await loadTasks();
    } catch {
        error.value = 'No se pudo crear la tarea.';
    }
}

async function complete(task: Task) {
    try {
        const response = await completeTask(task.id);

        const index = tasks.value.findIndex(
            (item) => item.id === task.id,
        );

        if (index === -1) {
            return;
        }

        if (filter.value === 'pending') {
            tasks.value.splice(index, 1);
            return;
        }

        tasks.value[index] = response.data;
    } catch {
        error.value = 'No se pudo completar la tarea.';
    }
}

async function remove(task: Task) {
    try {
        await deleteTask(task.id);

        tasks.value = tasks.value.filter(
            (item) => item.id !== task.id,
        );
    } catch {
        error.value = 'No se pudo eliminar la tarea.';
    }
}

async function changeFilter(
    value: 'all' | 'pending' | 'completed',
) {
    filter.value = value;
    await loadTasks();
}

async function changeSort(
    value: 'date' | 'title',
) {
    sort.value = value;
    await loadTasks();
}

function logout(): void {
    sessionStorage.removeItem('api_token');
    window.location.reload();
}

onMounted(loadUsers);
</script>

<template>
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="mx-auto max-w-7xl">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">
                    Gestor de tareas
                </h1>

                <p class="mt-1 text-gray-600">
                    Administra las tareas de tus usuarios.
                </p>

                <button
                    class="logout-button"
                    type="button"
                    @click="logout"
                >
                    Cerrar sesión
            </button>
            </div>
            <article class="card create-card">
                <div class="card-header">
                    <div>
                        <span class="icon-badge">＋</span>
                        <div>
                            <h2>Nuevo usuario</h2>
                            <p>Agrega un usuario al sistema.</p>
                        </div>
                    </div>
                </div>

                <div v-if="userError" class="alert">
                    {{ userError }}
                </div>

                <form
                    class="user-form"
                    @submit.prevent="submitUser"
                >
                    <label>
                        <span>Nombre</span>

                        <input
                            v-model="newUserName"
                            type="text"
                            placeholder="Ej. Juan Pérez"
                            required
                        />
                    </label>

                    <label>
                        <span>Correo electrónico</span>

                        <input
                            v-model="newUserEmail"
                            type="email"
                            placeholder="juan@example.com"
                            required
                        />
                    </label>

                    <label>
                        <span>Contraseña</span>

                        <input
                            v-model="newUserPassword"
                            type="password"
                            placeholder="Mínimo 8 caracteres"
                            required
                        />
                    </label>

                    <button
                        class="primary-button"
                        type="submit"
                        :disabled="creatingUser"
                    >
                        {{
                            creatingUser
                                ? 'Creando usuario...'
                                : 'Crear usuario'
                        }}
                    </button>
                </form>
            </article>
            <div
                v-if="error"
                class="mb-6 rounded-lg bg-red-100 p-4 text-red-700"
            >
                {{ error }}
            </div>

            <div class="grid gap-6 md:grid-cols-3 mt-3">
                <!-- Usuarios -->
                <section class="rounded-xl bg-white p-5 shadow">
                    <h2 class="mb-4 text-lg font-semibold">
                        Usuarios
                    </h2>

                    <div v-if="loadingUsers">
                        Cargando usuarios...
                    </div>

                    <div v-else class="space-y-2">
                        <button
                            v-for="user in users"
                            :key="user.id"
                            type="button"
                            class="w-full rounded-lg border p-3 text-left transition"
                            :class="
                                selectedUser?.id === user.id
                                    ? 'border-gray-900 bg-gray-900 text-white'
                                    : 'hover:bg-gray-50'
                            "
                            @click="selectUser(user)"
                        >
                            <div class="font-medium">
                                {{ user.name }}
                            </div>

                            <div
                                class="text-sm opacity-70"
                            >
                                {{ user.email }}
                            </div>
                        </button>
                    </div>
                </section>

                <!-- Tareas -->
                <section
                    class="rounded-xl bg-white p-5 shadow md:col-span-2"
                >
                    <div
                        class="mb-5 flex flex-wrap items-center justify-between gap-3"
                    >
                        <div>
                            <h2 class="text-lg font-semibold">
                                Tareas
                            </h2>

                            <p
                                v-if="selectedUser"
                                class="text-sm text-gray-500"
                            >
                                {{ selectedUser.name }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <select
                                class="rounded-lg border px-3 py-2 text-sm"
                                :value="filter"
                                @change="
                                    changeFilter(
                                        (
                                            $event.target as HTMLSelectElement
                                        ).value as
                                            | 'all'
                                            | 'pending'
                                            | 'completed',
                                    )
                                "
                            >
                                <option value="all">
                                    Todas
                                </option>

                                <option value="pending">
                                    Pendientes
                                </option>

                                <option value="completed">
                                    Completadas
                                </option>
                            </select>

                            <select
                                class="rounded-lg border px-3 py-2 text-sm"
                                :value="sort"
                                @change="
                                    changeSort(
                                        (
                                            $event.target as HTMLSelectElement
                                        ).value as
                                            | 'date'
                                            | 'title',
                                    )
                                "
                            >
                                <option value="date">
                                    Por fecha
                                </option>

                                <option value="title">
                                    Por título
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Nueva tarea -->
                    <form
                        class="mb-6 rounded-lg bg-gray-50 p-4"
                        @submit.prevent="submitTask"
                    >
                        <h3 class="mb-3 font-medium">
                            Nueva tarea
                        </h3>

                        <input
                            v-model="title"
                            type="text"
                            placeholder="Título"
                            class="mb-2 w-full rounded-lg border px-3 py-2"
                            required
                        />

                        <textarea
                            v-model="description"
                            placeholder="Descripción"
                            rows="3"
                            class="mb-3 w-full rounded-lg border px-3 py-2"
                            required
                        />

                        <button
                            type="submit"
                            :disabled="!selectedUser"
                            class="rounded-lg bg-gray-900 px-4 py-2 text-white disabled:opacity-50"
                        >
                            Crear tarea
                        </button>
                    </form>

                    <!-- Loading -->
                    <div
                        v-if="loadingTasks"
                        class="py-8 text-center text-gray-500"
                    >
                        Cargando tareas...
                    </div>

                    <!-- Empty -->
                    <div
                        v-else-if="tasks.length === 0"
                        class="py-8 text-center text-gray-500"
                    >
                        No hay tareas para mostrar.
                    </div>

                    <!-- Tasks -->
                    <div v-else class="space-y-3">
                        <article
                            v-for="task in tasks"
                            :key="task.id"
                            class="flex items-start justify-between gap-4 rounded-lg border p-4"
                        >
                            <div>
                                <h3
                                    class="font-semibold"
                                    :class="{
                                        'line-through text-gray-400':
                                            task.completed,
                                    }"
                                >
                                    {{ task.title }}
                                </h3>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ task.description }}
                                </p>

                                <span
                                    class="mt-2 inline-block rounded-full px-2 py-1 text-xs"
                                    :class="
                                        task.completed
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-yellow-100 text-yellow-700'
                                    "
                                >
                                    {{
                                        task.completed
                                            ? 'Completada'
                                            : 'Pendiente'
                                    }}
                                </span>
                            </div>

                            <div class="flex shrink-0 gap-2">
                                <button
                                    v-if="!task.completed"
                                    type="button"
                                    class="rounded-lg bg-green-600 px-3 py-2 text-sm text-white"
                                    @click="complete(task)"
                                >
                                    Completar
                                </button>

                                <button
                                    type="button"
                                    class="rounded-lg bg-red-600 px-3 py-2 text-sm text-white"
                                    @click="remove(task)"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
<style scoped>
.page {
    min-height: 100vh;
    padding: 40px;
    background: #f5f7fb;
    color: #172033;
}

.page-header {
    max-width: 1200px;
    margin: 0 auto 32px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 24px;
}

.eyebrow {
    margin: 0 0 6px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #64748b;
}

.page-header h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
}

.subtitle {
    margin: 8px 0 0;
    color: #64748b;
}

.logout-button {
    padding: 10px 16px;
    border: 1px solid #dbe1ea;
    border-radius: 10px;
    background: white;
    color: #334155;
    cursor: pointer;
}

.logout-button:hover {
    background: #f8fafc;
}

.top-grid {
    max-width: 1200px;
    margin: 0 auto 24px;
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 24px;
}

.card {
    background: white;
    border: 1px solid #e7ebf0;
    border-radius: 18px;
    box-shadow: 0 8px 30px rgb(15 23 42 / 5%);
}

.create-card,
.users-card,
.tasks-card {
    padding: 24px;
}

.card-header {
    margin-bottom: 22px;
}

.card-header > div {
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-header h2 {
    margin: 0;
    font-size: 1.1rem;
}

.card-header p {
    margin: 4px 0 0;
    color: #64748b;
    font-size: 0.9rem;
}

.icon-badge {
    width: 40px;
    height: 40px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    background: #eef2ff;
    color: #4f46e5;
    font-size: 1.2rem;
    font-weight: 700;
}

.user-form {
    display: grid;
    gap: 16px;
}

.user-form label {
    display: grid;
    gap: 7px;
}

.user-form label span {
    font-size: 0.85rem;
    font-weight: 600;
    color: #334155;
}

.user-form input {
    width: 100%;
    box-sizing: border-box;
    padding: 12px 14px;
    border: 1px solid #dbe1ea;
    border-radius: 10px;
    outline: none;
    transition:
        border-color 0.2s,
        box-shadow 0.2s;
}

.user-form input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgb(99 102 241 / 10%);
}

.primary-button {
    margin-top: 4px;
    padding: 12px 16px;
    border: 0;
    border-radius: 10px;
    background: #4f46e5;
    color: white;
    font-weight: 600;
    cursor: pointer;
}

.primary-button:hover {
    background: #4338ca;
}

.primary-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.alert {
    margin-bottom: 16px;
    padding: 12px 14px;
    border-radius: 10px;
    background: #fef2f2;
    color: #b91c1c;
    font-size: 0.9rem;
}

.users-list {
    display: grid;
    gap: 10px;
    max-height: 390px;
    overflow-y: auto;
}

.user-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border: 1px solid #e7ebf0;
    border-radius: 12px;
    background: white;
    text-align: left;
    cursor: pointer;
    transition:
        transform 0.15s,
        border-color 0.15s,
        background 0.15s;
}

.user-item:hover {
    transform: translateY(-1px);
    background: #f8fafc;
}

.user-item.selected {
    border-color: #6366f1;
    background: #eef2ff;
}

.avatar {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #e2e8f0;
    color: #334155;
    font-weight: 700;
}

.user-info {
    min-width: 0;
    display: grid;
    gap: 3px;
}

.user-info strong {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-info span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #64748b;
    font-size: 0.85rem;
}

.arrow {
    margin-left: auto;
    color: #94a3b8;
    font-size: 1.5rem;
}

.empty-state {
    padding: 40px 20px;
    text-align: center;
    color: #64748b;
}

.tasks-card {
    max-width: 1152px;
    margin: 0 auto;
}

.tasks-empty {
    min-height: 260px;
    display: grid;
    place-content: center;
    justify-items: center;
    text-align: center;
}

.empty-icon {
    width: 60px;
    height: 60px;
    display: grid;
    place-items: center;
    margin-bottom: 16px;
    border-radius: 50%;
    background: #eef2ff;
    color: #4f46e5;
    font-size: 1.5rem;
}

.tasks-empty h3 {
    margin: 0;
}

.tasks-empty p {
    max-width: 420px;
    margin: 8px 0 0;
    color: #64748b;
}

@media (max-width: 900px) {
    .page {
        padding: 20px;
    }

    .page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .top-grid {
        grid-template-columns: 1fr;
    }
}
</style>
