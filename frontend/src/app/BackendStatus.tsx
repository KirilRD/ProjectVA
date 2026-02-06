"use client";

import { useEffect, useState } from "react";
import { fetchApiStatus, getApiUrl, type ApiStatus } from "@/lib/api";

export function BackendStatus() {
  const [data, setData] = useState<ApiStatus | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let cancelled = false;
    fetchApiStatus()
      .then((res) => {
        if (!cancelled) setData(res);
      })
      .catch((e) => {
        if (!cancelled) setError(e instanceof Error ? e.message : "Request failed");
      })
      .finally(() => {
        if (!cancelled) setLoading(false);
      });
    return () => {
      cancelled = true;
    };
  }, []);

  const baseUrl = getApiUrl("").replace(/\/api\/?$/, "");

  if (loading) {
    return (
      <div className="rounded-lg border border-black/[.08] dark:border-white/[.145] p-4 text-sm text-gray-500">
        Checking backend…
      </div>
    );
  }

  if (error) {
    return (
      <div className="rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 p-4 text-sm">
        <p className="font-medium text-amber-800 dark:text-amber-200">Backend unreachable</p>
        <p className="mt-1 text-amber-700 dark:text-amber-300">{error}</p>
        <p className="mt-2 text-xs text-amber-600 dark:text-amber-400">
          Ensure the Laravel backend is running at{" "}
          <code className="rounded bg-amber-100 dark:bg-amber-900/50 px-1">{baseUrl || "NEXT_PUBLIC_API_URL"}</code> and
          CORS allows this origin.
        </p>
        <a
          href={baseUrl ? `${baseUrl}/login` : "#"}
          className="mt-2 inline-block text-xs font-medium text-amber-700 dark:text-amber-300 underline hover:no-underline"
        >
          Open Laravel login →
        </a>
      </div>
    );
  }

  return (
    <div className="rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/30 p-4 text-sm">
      <p className="font-medium text-green-800 dark:text-green-200">Backend connected</p>
      {data && (
        <p className="mt-1 text-green-700 dark:text-green-300">
          {data.backend} · {data.status} · {data.timestamp}
        </p>
      )}
      <a
        href={baseUrl ? `${baseUrl}/login` : "#"}
        className="mt-2 inline-block text-xs font-medium text-green-700 dark:text-green-300 underline hover:no-underline"
      >
        Laravel login →
      </a>
    </div>
  );
}
