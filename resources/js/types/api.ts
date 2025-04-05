export interface ApiResponse<T> {
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: {
        active: boolean;
        label: string;
        url: string;
    }[];
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: string;
    to: number;
    total: number;
    data: T;
}

export interface ActionsResponse<T> {
    message: string;
    status: boolean;
    type: string;
    data: T;
    errors?: {
        [key: string]: string;
    };
}
