/**
 * API client for the Laravel backend.
 * Base URL is set via NEXT_PUBLIC_API_URL (e.g. http://localhost:8201/api).
 */

const getBaseUrl = (): string => {
  if (typeof window !== "undefined") {
    return process.env.NEXT_PUBLIC_API_URL ?? "";
  }
  return process.env.NEXT_PUBLIC_API_URL ?? "";
};

export function getApiUrl(path: string): string {
  const base = getBaseUrl().replace(/\/$/, "");
  const p = path.startsWith("/") ? path : `/${path}`;
  return `${base}${p}`;
}

/** Backend base URL (e.g. http://localhost:8201) for login, dashboard, etc. */
export function getBackendUrl(): string {
  const base = getBaseUrl().replace(/\/$/, "");
  return base.replace(/\/api\/?$/, "") || "";
}

export type ApiStatus = {
  status: string;
  backend: string;
  timestamp: string;
};

export async function fetchApiStatus(): Promise<ApiStatus> {
  const url = getApiUrl("/status");
  const res = await fetch(url, { cache: "no-store" });
  if (!res.ok) throw new Error(`Backend returned ${res.status}`);
  return res.json() as Promise<ApiStatus>;
}
// Recommended role options (must match backend validation: Backend, Frontend, QA, Design, PM)
export const RECOMMENDED_ROLES = ["Backend", "Frontend", "QA", "Design", "PM"] as const;

// ---- Tool types ----
export type Tool = {
  id: number;
  name: string;
  link: string;
  description: string;
  how_to_use: string;
  examples: string[] | null;
  roles: string[];
  recommended_role: string | null;
  created_at: string;
  updated_at: string;
};

export type ToolCreateInput = Omit<Tool, "id" | "created_at" | "updated_at">;

// Map user.role (backend) to tool recommended_role for "Recommended for your role"
export function userRoleToRecommendedRole(userRole: string | null | undefined): string | null {
  if (!userRole) return null;
  const map: Record<string, string> = {
    backend: "Backend",
    frontend: "Frontend",
    qa: "QA",
    designer: "Design",
    project_manager: "PM",
  };
  return map[userRole] ?? null;
}

export type CurrentUser = {
  id: number;
  name: string;
  email: string;
  role: string | null;
};

export async function fetchCurrentUser(): Promise<CurrentUser | null> {
  const res = await fetch(getApiUrl("/user"), { credentials: "include", cache: "no-store" });
  if (res.status === 401) return null;
  if (!res.ok) throw new Error(`Failed to fetch user: ${res.status}`);
  return res.json();
}

// ---- Tool API calls ----
export async function fetchTools(): Promise<Tool[]> {
  const res = await fetch(getApiUrl("/tools"), { cache: "no-store" });
  if (!res.ok) throw new Error(`Failed to fetch tools: ${res.status}`);
  return res.json();
}

export async function createTool(data: ToolCreateInput): Promise<Tool> {
  const res = await fetch(getApiUrl("/tools"), {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
    credentials: "include",
  });
  if (!res.ok) throw new Error(`Failed to create tool: ${res.status}`);
  return res.json();
}
