import { d as c, j as t } from "./compiler-runtime-DyHbwOGE.js";
import { l as d } from "./index-DLuvQKrb.js";
import { e as f } from "./index-CejI3Ej_.js";
import { F as p, H as x } from "./app-mGJhb3YO.js";
import { I as u } from "./input-error-Bu6cIHH2.js";
import { T as h } from "./text-link-DMLf__ta.js";
import { B as j } from "./app-logo-icon-CT5L2AiP.js";
import { I as g } from "./input-CNccwKGQ.js";
import { L as _ } from "./label-Cxe3Hu_U.js";
import { A as y } from "./auth-layout-Q2uAfpi0.js";
import { L as b } from "./loader-circle-BNlzAMyy.js";
function H(n) {
  const e = c.c(8), {
    status: s
  } = n;
  let o;
  e[0] === Symbol.for("react.memo_cache_sentinel") ? (o = /* @__PURE__ */ t.jsx(x, { title: "Forgot password" }), e[0] = o) : o = e[0];
  let r;
  e[1] !== s ? (r = s && /* @__PURE__ */ t.jsx("div", { className: "mb-4 text-center text-sm font-medium text-green-600", children: s }), e[1] = s, e[2] = r) : r = e[2];
  let a;
  e[3] === Symbol.for("react.memo_cache_sentinel") ? (a = /* @__PURE__ */ t.jsx(p, { ...f.form(), children: w }), e[3] = a) : a = e[3];
  let i;
  e[4] === Symbol.for("react.memo_cache_sentinel") ? (i = /* @__PURE__ */ t.jsx("span", { children: "Or, return to" }), e[4] = i) : i = e[4];
  let m;
  e[5] === Symbol.for("react.memo_cache_sentinel") ? (m = /* @__PURE__ */ t.jsxs("div", { className: "space-y-6", children: [
    a,
    /* @__PURE__ */ t.jsxs("div", { className: "space-x-1 text-center text-sm text-muted-foreground", children: [
      i,
      /* @__PURE__ */ t.jsx(h, { href: d(), children: "log in" })
    ] })
  ] }), e[5] = m) : m = e[5];
  let l;
  return e[6] !== r ? (l = /* @__PURE__ */ t.jsxs(y, { title: "Forgot password", description: "Enter your email to receive a password reset link", children: [
    o,
    r,
    m
  ] }), e[6] = r, e[7] = l) : l = e[7], l;
}
function w(n) {
  const {
    processing: e,
    errors: s
  } = n;
  return /* @__PURE__ */ t.jsxs(t.Fragment, { children: [
    /* @__PURE__ */ t.jsxs("div", { className: "grid gap-2", children: [
      /* @__PURE__ */ t.jsx(_, { htmlFor: "email", children: "Email address" }),
      /* @__PURE__ */ t.jsx(g, { id: "email", type: "email", name: "email", autoComplete: "off", autoFocus: !0, placeholder: "email@example.com" }),
      /* @__PURE__ */ t.jsx(u, { message: s.email })
    ] }),
    /* @__PURE__ */ t.jsx("div", { className: "my-6 flex items-center justify-start", children: /* @__PURE__ */ t.jsxs(j, { className: "w-full", disabled: e, "data-test": "email-password-reset-link-button", children: [
      e && /* @__PURE__ */ t.jsx(b, { className: "h-4 w-4 animate-spin" }),
      "Email password reset link"
    ] }) })
  ] });
}
export {
  H as default
};
