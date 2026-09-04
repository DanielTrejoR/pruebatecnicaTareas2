export interface User {
    id: number;
    name: string;
    email: string;
}

export interface Task {
    id: number;
    user_id: number;
    title: string;
    description: string;
    completed: boolean;
    created_at: string;
    updated_at: string;
}

export interface ApiResponse<T> {
    data: T;
    message?: string;
}

export interface LoginResponse {
    message: string;
    token: string;
}
