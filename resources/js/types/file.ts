export interface FileInterface {
    id: number;
    parent_id: number | null;
    is_folder: 'Y' | 'N';
    uuid: string;
    path: string;
    name: string;
    ext: string;
    mime_type: string;
    type: string;
    size: number;
    created_at: string | null;
    updated_at: string | null;
}
