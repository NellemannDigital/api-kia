import { d as C, r as y, j as t } from "./compiler-runtime-DyHbwOGE.js";
import { I as j } from "./input-error-Bu6cIHH2.js";
import { B as S } from "./app-logo-icon-CT5L2AiP.js";
import { I as N } from "./input-CNccwKGQ.js";
import { I as F, K as O, O as _, a as w, b as A } from "./use-two-factor-auth-D007eWR-.js";
import { A as R } from "./auth-layout-Q2uAfpi0.js";
import { s as P } from "./index-D3PXbJSX.js";
import { F as k, H } from "./app-mGJhb3YO.js";
function D() {
  const e = C.c(18), [o, v] = y.useState(!1), [m, h] = y.useState("");
  let f;
  e: {
    if (o) {
      let n;
      e[0] === Symbol.for("react.memo_cache_sentinel") ? (n = {
        title: "Recovery Code",
        description: "Please confirm access to your account by entering one of your emergency recovery codes.",
        toggleText: "login using an authentication code"
      }, e[0] = n) : n = e[0], f = n;
      break e;
    }
    let s;
    e[1] === Symbol.for("react.memo_cache_sentinel") ? (s = {
      title: "Authentication Code",
      description: "Enter the authentication code provided by your authenticator application.",
      toggleText: "login using a recovery code"
    }, e[1] = s) : s = e[1], f = s;
  }
  const r = f;
  let a;
  e[2] !== o ? (a = (s) => {
    v(!o), s(), h("");
  }, e[2] = o, e[3] = a) : a = e[3];
  const p = a, b = r.title, T = r.description;
  let l;
  e[4] === Symbol.for("react.memo_cache_sentinel") ? (l = /* @__PURE__ */ t.jsx(H, { title: "Two-Factor Authentication" }), e[4] = l) : l = e[4];
  let u;
  e[5] === Symbol.for("react.memo_cache_sentinel") ? (u = P.form(), e[5] = u) : u = e[5];
  const x = !o;
  let c;
  e[6] !== r.toggleText || e[7] !== m || e[8] !== o || e[9] !== p ? (c = (s) => {
    const {
      errors: n,
      processing: g,
      clearErrors: E
    } = s;
    return /* @__PURE__ */ t.jsxs(t.Fragment, { children: [
      o ? /* @__PURE__ */ t.jsxs(t.Fragment, { children: [
        /* @__PURE__ */ t.jsx(N, { name: "recovery_code", type: "text", placeholder: "Enter recovery code", autoFocus: o, required: !0 }),
        /* @__PURE__ */ t.jsx(j, { message: n.recovery_code })
      ] }) : /* @__PURE__ */ t.jsxs("div", { className: "flex flex-col items-center justify-center space-y-3 text-center", children: [
        /* @__PURE__ */ t.jsx("div", { className: "flex w-full items-center justify-center", children: /* @__PURE__ */ t.jsx(F, { name: "code", maxLength: _, value: m, onChange: (I) => h(I), disabled: g, pattern: O, children: /* @__PURE__ */ t.jsx(w, { children: Array.from({
          length: _
        }, L) }) }) }),
        /* @__PURE__ */ t.jsx(j, { message: n.code })
      ] }),
      /* @__PURE__ */ t.jsx(S, { type: "submit", className: "w-full", disabled: g, children: "Continue" }),
      /* @__PURE__ */ t.jsxs("div", { className: "text-center text-sm text-muted-foreground", children: [
        /* @__PURE__ */ t.jsx("span", { children: "or you can " }),
        /* @__PURE__ */ t.jsx("button", { type: "button", className: "cursor-pointer text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500", onClick: () => p(E), children: r.toggleText })
      ] })
    ] });
  }, e[6] = r.toggleText, e[7] = m, e[8] = o, e[9] = p, e[10] = c) : c = e[10];
  let i;
  e[11] !== x || e[12] !== c ? (i = /* @__PURE__ */ t.jsx("div", { className: "space-y-6", children: /* @__PURE__ */ t.jsx(k, { ...u, className: "space-y-4", resetOnError: !0, resetOnSuccess: x, children: c }) }), e[11] = x, e[12] = c, e[13] = i) : i = e[13];
  let d;
  return e[14] !== r.description || e[15] !== r.title || e[16] !== i ? (d = /* @__PURE__ */ t.jsxs(R, { title: b, description: T, children: [
    l,
    i
  ] }), e[14] = r.description, e[15] = r.title, e[16] = i, e[17] = d) : d = e[17], d;
}
function L(e, o) {
  return /* @__PURE__ */ t.jsx(A, { index: o }, o);
}
export {
  D as default
};
