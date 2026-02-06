"use client";

import Link from "next/link";
import { getBackendUrl } from "@/lib/api";

export default function LoginPage() {
  const backendUrl = getBackendUrl();
  const loginUrl = backendUrl ? `${backendUrl}/login` : "#";

  return (
    <main className="min-h-screen flex flex-col items-center justify-center p-6">
      <h1 className="text-2xl font-semibold mb-4">Login</h1>
      <p className="text-gray-600 dark:text-gray-400 mb-6 text-center max-w-sm">
        Sign in via the Laravel backend.
      </p>
      <a
        href={loginUrl}
        className="rounded-lg bg-black dark:bg-white text-white dark:text-black px-4 py-2 font-medium hover:opacity-90"
      >
        Open Laravel login
      </a>
      <Link href="/" className="mt-4 text-sm text-gray-500 hover:underline">
        ← Back to home
      </Link>
    </main>
  );
}
