import { d as j, j as e } from "./compiler-runtime-DyHbwOGE.js";
import { u as h } from "./index-CejI3Ej_.js";
import { F as _, H as g } from "./app-mGJhb3YO.js";
import { I as c } from "./input-error-Bu6cIHH2.js";
import { B as b } from "./app-logo-icon-CT5L2AiP.js";
import { I as f } from "./input-CNccwKGQ.js";
import { L as w } from "./label-Cxe3Hu_U.js";
import { S as y } from "./spinner-D_0Rae4j.js";
import { A as N } from "./auth-layout-Q2uAfpi0.js";
function L(x) {
  const s = j.c(11), {
    token: d,
    email: a
  } = x;
  let t;
  s[0] === Symbol.for("react.memo_cache_sentinel") ? (t = /* @__PURE__ */ e.jsx(g, { title: "Reset password" }), s[0] = t) : t = s[0];
  let l;
  s[1] === Symbol.for("react.memo_cache_sentinel") ? (l = h.form(), s[1] = l) : l = s[1];
  let r;
  s[2] !== a || s[3] !== d ? (r = (n) => ({
    ...n,
    token: d,
    email: a
  }), s[2] = a, s[3] = d, s[4] = r) : r = s[4];
  let m;
  s[5] === Symbol.for("react.memo_cache_sentinel") ? (m = ["password", "password_confirmation"], s[5] = m) : m = s[5];
  let o;
  s[6] !== a ? (o = (n) => {
    const {
      processing: u,
      errors: p
    } = n;
    return /* @__PURE__ */ e.jsxs("div", { className: "grid gap-6", children: [
      /* @__PURE__ */ e.jsxs("div", { className: "grid gap-2", children: [
        /* @__PURE__ */ e.jsx(w, { htmlFor: "email", children: "Email" }),
        /* @__PURE__ */ e.jsx(f, { id: "email", type: "email", name: "email", autoComplete: "email", value: a, className: "mt-1 block w-full", readOnly: !0 }),
        /* @__PURE__ */ e.jsx(c, { message: p.email, className: "mt-2" })
      ] }),
      /* @__PURE__ */ e.jsxs("div", { className: "grid gap-2", children: [
        /* @__PURE__ */ e.jsx(w, { htmlFor: "password", children: "Password" }),
        /* @__PURE__ */ e.jsx(f, { id: "password", type: "password", name: "password", autoComplete: "new-password", className: "mt-1 block w-full", autoFocus: !0, placeholder: "Password" }),
        /* @__PURE__ */ e.jsx(c, { message: p.password })
      ] }),
      /* @__PURE__ */ e.jsxs("div", { className: "grid gap-2", children: [
        /* @__PURE__ */ e.jsx(w, { htmlFor: "password_confirmation", children: "Confirm password" }),
        /* @__PURE__ */ e.jsx(f, { id: "password_confirmation", type: "password", name: "password_confirmation", autoComplete: "new-password", className: "mt-1 block w-full", placeholder: "Confirm password" }),
        /* @__PURE__ */ e.jsx(c, { message: p.password_confirmation, className: "mt-2" })
      ] }),
      /* @__PURE__ */ e.jsxs(b, { type: "submit", className: "mt-4 w-full", disabled: u, "data-test": "reset-password-button", children: [
        u && /* @__PURE__ */ e.jsx(y, {}),
        "Reset password"
      ] })
    ] });
  }, s[6] = a, s[7] = o) : o = s[7];
  let i;
  return s[8] !== r || s[9] !== o ? (i = /* @__PURE__ */ e.jsxs(N, { title: "Reset password", description: "Please enter your new password below", children: [
    t,
    /* @__PURE__ */ e.jsx(_, { ...l, transform: r, resetOnSuccess: m, children: o })
  ] }), s[8] = r, s[9] = o, s[10] = i) : i = s[10], i;
}
export {
  L as default
};
