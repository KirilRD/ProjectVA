"use client";

import { useState, type ChangeEvent, type FormEvent, type CSSProperties } from "react";
import { createTool, ToolCreateInput, RECOMMENDED_ROLES } from "@/lib/api";

export default function AddToolForm({ onSuccess }: { onSuccess: () => void }) {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const [formData, setFormData] = useState<ToolCreateInput>({
    name: "",
    link: "",
    description: "",
    how_to_use: "",
    examples: [],
    roles: [],
    recommended_role: null,
  });

  const [exampleInput, setExampleInput] = useState("");

  const handleChange = (e: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleRoleToggle = (role: string) => {
    const currentRoles = formData.roles;
    if (currentRoles.includes(role)) {
      setFormData({ ...formData, roles: currentRoles.filter((r: string) => r !== role) });
    } else {
      setFormData({ ...formData, roles: [...currentRoles, role] });
    }
  };

  const addExample = () => {
    if (exampleInput.trim()) {
      setFormData({
        ...formData,
        examples: [...(formData.examples || []), exampleInput.trim()],
      });
      setExampleInput("");
    }
  };

  const removeExample = (index: number) => {
    setFormData({
      ...formData,
      examples: (formData.examples || []).filter((_: string, i: number) => i !== index),
    });
  };

  const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      await createTool(formData);
      setFormData({
        name: "",
        link: "",
        description: "",
        how_to_use: "",
        examples: [],
        roles: [],
        recommended_role: null,
      });
      onSuccess();
    } catch (err) {
      setError((err as Error).message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ background: "#fff", borderRadius: 8, padding: 24, boxShadow: "0 2px 8px rgba(0,0,0,0.1)", marginBottom: 24 }}>
      <h2 style={{ margin: "0 0 16px" }}>➕ Добави нов Tool</h2>
      {error && <div style={{ color: "red", marginBottom: 12 }}>{error}</div>}
      <form onSubmit={handleSubmit}>
        {/* Name */}
        <label style={labelStyle}>Име на Tool</label>
        <input
          style={inputStyle}
          name="name"
          value={formData.name}
          onChange={handleChange}
          placeholder="Например: ChatGPT"
          required
        />

        {/* Link */}
        <label style={labelStyle}>Линк (URL)</label>
        <input
          style={inputStyle}
          name="link"
          value={formData.link}
          onChange={handleChange}
          placeholder="https://example.com"
          required
        />

        {/* Description */}
        <label style={labelStyle}>Описание</label>
        <textarea
          style={{ ...inputStyle, minHeight: 80 }}
          name="description"
          value={formData.description}
          onChange={handleChange}
          placeholder="Кратко описание на tool-а"
          required
        />

        {/* How to use */}
        <label style={labelStyle}>Как се използва</label>
        <textarea
          style={{ ...inputStyle, minHeight: 80 }}
          name="how_to_use"
          value={formData.how_to_use}
          onChange={handleChange}
          placeholder="Описание на начина заползване"
          required
        />

        {/* Examples */}
        <label style={labelStyle}>Реални примери (опционално)</label>
        <div style={{ display: "flex", gap: 8 }}>
          <input
            style={{ ...inputStyle, flex: 1 }}
            value={exampleInput}
            onChange={(e: ChangeEvent<HTMLInputElement>) => setExampleInput(e.target.value)}
            placeholder="Напиши пример и натисни +"
          />
          <button type="button" onClick={addExample} style={addBtnStyle}>+</button>
        </div>
        {(formData.examples || []).map((ex: string, i: number) => (
          <div key={i} style={tagStyle}>
            {ex}
            <button type="button" onClick={() => removeExample(i)} style={removeTagBtnStyle}>✕</button>
          </div>
        ))}

        {/* Roles */}
        <label style={labelStyle}>Препоръчителни роли</label>
        <div style={{ display: "flex", gap: 8, flexWrap: "wrap" }}>
          {["owner", "backend", "frontend", "devops", "design", "qa"].map((role) => (
            <button
              key={role}
              type="button"
              onClick={() => handleRoleToggle(role)}
              style={{
                ...roleBtnStyle,
                background: formData.roles.includes(role) ? "#1d4ed8" : "#e5e7eb",
                color: formData.roles.includes(role) ? "#fff" : "#374151",
              }}
            >
              {role}
            </button>
          ))}
        </div>

        {/* Recommended for (best for which role) */}
        <label style={labelStyle}>Recommended for (Best for)</label>
        <select
          style={inputStyle}
          value={formData.recommended_role ?? ""}
          onChange={(e: ChangeEvent<HTMLSelectElement>) =>
            setFormData({ ...formData, recommended_role: e.target.value || null })
          }
        >
          <option value="">— Select role —</option>
          {RECOMMENDED_ROLES.map((role) => (
            <option key={role} value={role}>
              {role}
            </option>
          ))}
        </select>

        {/* Submit */}
        <button type="submit" disabled={loading} style={submitBtnStyle}>
          {loading ? "Запазва..." : "Запази Tool"}
        </button>
      </form>
    </div>
  );
}

// ---- Styles ----
const labelStyle: CSSProperties = {
  display: "block",
  fontSize: 14,
  fontWeight: 600,
  color: "#374151",
  marginTop: 16,
  marginBottom: 4,
};

const inputStyle: CSSProperties = {
  width: "100%",
  padding: "8px 12px",
  border: "1px solid #d1d5db",
  borderRadius: 6,
  fontSize: 14,
  boxSizing: "border-box",
};

const addBtnStyle: CSSProperties = {
  padding: "8px 14px",
  background: "#1d4ed8",
  color: "#fff",
  border: "none",
  borderRadius: 6,
  cursor: "pointer",
  fontSize: 18,
};

const tagStyle: CSSProperties = {
  display: "inline-flex",
  alignItems: "center",
  gap: 6,
  background: "#e0e7ff",
  padding: "4px 10px",
  borderRadius: 16,
  fontSize: 13,
  marginTop: 6,
  marginRight: 4,
};

const removeTagBtnStyle: CSSProperties = {
  background: "none",
  border: "none",
  cursor: "pointer",
  color: "#6366f1",
  fontSize: 12,
};

const roleBtnStyle: CSSProperties = {
  padding: "4px 12px",
  border: "none",
  borderRadius: 16,
  cursor: "pointer",
  fontSize: 13,
  fontWeight: 500,
};

const submitBtnStyle: CSSProperties = {
  width: "100%",
  marginTop: 24,
  padding: "10px",
  background: "#1d4ed8",
  color: "#fff",
  border: "none",
  borderRadius: 6,
  fontSize: 16,
  cursor: "pointer",
};