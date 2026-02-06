"use client";

import React, { useEffect, useState } from "react";
import Link from "next/link";
import {
  fetchTools,
  fetchCurrentUser,
  Tool,
  userRoleToRecommendedRole,
  type CurrentUser,
} from "@/lib/api";
import AddToolForm from "./AddToolForm";

export default function ToolsPage() {
  const [tools, setTools] = useState<Tool[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [currentUser, setCurrentUser] = useState<CurrentUser | null>(null);

  const loadTools = async () => {
    try {
      const data = await fetchTools();
      setTools(data);
    } catch (err) {
      setError((err as Error).message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadTools();
  }, []);

  useEffect(() => {
    fetchCurrentUser()
      .then(setCurrentUser)
      .catch(() => setCurrentUser(null));
  }, []);

  const recommendedRole = userRoleToRecommendedRole(currentUser?.role ?? null);
  const recommendedTools =
    recommendedRole != null
      ? tools.filter((t) => t.recommended_role === recommendedRole)
      : [];

  return (
    <div style={{ maxWidth: 800, margin: "0 auto", padding: 24, fontFamily: "sans-serif" }}>
      <div style={{ marginBottom: 24, display: "flex", justifyContent: "space-between", alignItems: "center" }}>
        <h1 style={{ margin: 0, textAlign: "center", flex: 1 }}>AI Tools Platform</h1>
        <Link href="/" style={{ color: "#1d4ed8", fontSize: 14, textDecoration: "none" }}>
          ← Home
        </Link>
      </div>

      <AddToolForm onSuccess={loadTools} />

      {/* Recommended tools for your role (when logged in) */}
      {currentUser && recommendedRole && recommendedTools.length > 0 && (
        <div
          style={{
            background: "#eef2ff",
            borderRadius: 8,
            padding: 16,
            marginBottom: 24,
            border: "1px solid #c7d2fe",
          }}
        >
          <h3 style={{ margin: "0 0 12px", fontSize: 14, fontWeight: 600, color: "#3730a3" }}>
            Recommended tools for your role
          </h3>
          <div style={{ display: "flex", flexWrap: "wrap", gap: 8 }}>
            {recommendedTools.map((tool) => (
              <span
                key={tool.id}
                style={{
                  display: "inline-block",
                  background: "#fff",
                  padding: "6px 12px",
                  borderRadius: 6,
                  fontSize: 13,
                  fontWeight: 500,
                  color: "#4338ca",
                  border: "1px solid #a5b4fc",
                }}
              >
                {tool.name}
              </span>
            ))}
          </div>
        </div>
      )}

      <h2 style={{ margin: "32px 0 16px" }}>All Tools</h2>

      {loading && <p>Loading…</p>}
      {error && <p style={{ color: "red" }}>{error}</p>}

      {!loading && tools.length === 0 && (
        <p style={{ color: "#6b7280" }}>No tools yet. Add the first one!</p>
      )}

      {tools.map((tool) => (
        <div
          key={tool.id}
          style={{
            background: "#fff",
            borderRadius: 8,
            padding: 20,
            marginBottom: 16,
            boxShadow: "0 2px 8px rgba(0,0,0,0.08)",
          }}
        >
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
            <h3 style={{ margin: 0 }}>{tool.name}</h3>
            <a href={tool.link} target="_blank" rel="noopener noreferrer" style={{ color: "#1d4ed8", fontSize: 14 }}>
              Open Link
            </a>
          </div>

          {tool.recommended_role && (
            <div style={{ marginTop: 8 }}>
              <span
                style={{
                  background: "#e0e7ff",
                  color: "#4338ca",
                  padding: "2px 10px",
                  borderRadius: 12,
                  fontSize: 12,
                  fontWeight: 600,
                }}
              >
                Best for: {tool.recommended_role}
              </span>
            </div>
          )}

          <p style={{ color: "#4b5563", margin: "8px 0" }}>{tool.description}</p>

          <div style={{ marginTop: 8 }}>
            <strong style={{ fontSize: 13 }}>How to use:</strong>
            <p style={{ fontSize: 13, color: "#374151", margin: "4px 0 0" }}>{tool.how_to_use}</p>
          </div>

          {tool.examples && tool.examples.length > 0 && (
            <div style={{ marginTop: 8 }}>
              <strong style={{ fontSize: 13 }}>Examples:</strong>
              <ul style={{ margin: "4px 0 0", paddingLeft: 20, fontSize: 13, color: "#374151" }}>
                {tool.examples.map((ex: string, i: number) => (
                  <li key={i}>{ex}</li>
                ))}
              </ul>
            </div>
          )}

          <div style={{ marginTop: 12, display: "flex", gap: 8, flexWrap: "wrap" }}>
            {tool.roles?.map((role: string) => (
              <span
                key={role}
                style={{
                  background: "#e0e7ff",
                  color: "#4338ca",
                  padding: "2px 10px",
                  borderRadius: 12,
                  fontSize: 12,
                  fontWeight: 600,
                }}
              >
                {role}
              </span>
            ))}
          </div>
        </div>
      ))}
    </div>
  );
}
