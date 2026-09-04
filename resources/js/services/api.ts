import type {
    ApiResponse,
    LoginResponse,
    Task,
    User,
} from '@/types/task';

interface ApiError {
    message: string;
    errors?: Record<string, string[]>;
    status: number;
}

const API_URL = '/api';

function getToken(): string | null {
    return sessionStorage.getItem('api_token');
}

async function request<T>(
    endpoint: string,
    options: RequestInit = {},
): Promise<T> {
    const token = getToken();

    const response = await fetch(`${API_URL}${endpoint}`, {
        ...options,
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            ...(token
                ? {
                      Authorization: `Bearer ${token}`,
                  }
                : {}),
            ...options.headers,
        },
    });

    if (!response.ok) {
        const data = await response.json().catch(() => null);

        const error: ApiError = {
            message:
                data?.message ?? 'Ocurrió un error inesperado.',
            errors: data?.errors,
            status: response.status,
        };

        throw error;
    }

    return response.json();
}

export async function login(
    email: string,
    password: string,
): Promise<LoginResponse> {
    return request<LoginResponse>('/login', {
        method: 'POST',
        body: JSON.stringify({
            email,
            password,
        }),
    });
}

export async function getUsers(): Promise<ApiResponse<User[]>> {
    return request<ApiResponse<User[]>>('/users');
}

export async function getTasks(
    userId: number,
    params: {
        completed?: boolean;
        sort?: 'title' | 'date';
    } = {},
): Promise<ApiResponse<Task[]>> {
    const query = new URLSearchParams();

    if (params.completed !== undefined) {
        query.set('completed', String(params.completed));
    }

    if (params.sort) {
        query.set('sort', params.sort);
    }

    const queryString = query.toString();

    return request<ApiResponse<Task[]>>(
        `/users/${userId}/tasks${queryString ? `?${queryString}` : ''}`,
    );
}

export async function createTask(
    userId: number,
    title: string,
    description: string,
): Promise<ApiResponse<Task>> {
    return request<ApiResponse<Task>>(`/users/${userId}/tasks`, {
        method: 'POST',
        body: JSON.stringify({
            title,
            description,
        }),
    });
}

export async function completeTask(
    taskId: number,
): Promise<ApiResponse<Task>> {
    return request<ApiResponse<Task>>(
        `/tasks/${taskId}/complete`,
        {
            method: 'PATCH',
        },
    );
}

export async function deleteTask(
    taskId: number,
): Promise<{ message: string }> {
    return request<{ message: string }>(
        `/tasks/${taskId}`,
        {
            method: 'DELETE',
        },
    );
}

export async function createUser(
    name: string,
    email: string,
    password: string,
): Promise<ApiResponse<User>> {
    return request<ApiResponse<User>>('/users', {
        method: 'POST',
        body: JSON.stringify({
            name,
            email,
            password,
        }),
    });
}
