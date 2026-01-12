import { d as m, j as t } from "./compiler-runtime-DyHbwOGE.js";
import { T as l } from "./text-link-DMLf__ta.js";
import { B as c } from "./app-logo-icon-CT5L2AiP.js";
import { S as f } from "./spinner-D_0Rae4j.js";
import { A as d } from "./auth-layout-Q2uAfpi0.js";
import { b as u } from "./index-DLuvQKrb.js";
import { s as x } from "./index-D0EuCgl9.js";
import { F as p, H as h } from "./app-mGJhb3YO.js";
function A(n) {
  const e = m.c(6), {
    status: a
  } = n;
  let s;
  e[0] === Symbol.for("react.memo_cache_sentinel") ? (s = /* @__PURE__ */ t.jsx(h, { title: "Email verification" }), e[0] = s) : s = e[0];
  let i;
  e[1] !== a ? (i = a === "verification-link-sent" && /* @__PURE__ */ t.jsx("div", { className: "mb-4 text-center text-sm font-medium text-green-600", children: "A new verification link has been sent to the email address you provided during registration." }), e[1] = a, e[2] = i) : i = e[2];
  let r;
  e[3] === Symbol.for("react.memo_cache_sentinel") ? (r = /* @__PURE__ */ t.jsx(p, { ...x.form(), className: "space-y-6 text-center", children: y }), e[3] = r) : r = e[3];
  let o;
  return e[4] !== i ? (o = /* @__PURE__ */ t.jsxs(d, { title: "Verify email", description: "Please verify your email address by clicking on the link we just emailed to you.", children: [
    s,
    i,
    r
  ] }), e[4] = i, e[5] = o) : o = e[5], o;
}
function y(n) {
  const {
    processing: e
  } = n;
  return /* @__PURE__ */ t.jsxs(t.Fragment, { children: [
    /* @__PURE__ */ t.jsxs(c, { disabled: e, variant: "secondary", children: [
      e && /* @__PURE__ */ t.jsx(f, {}),
      "Resend verification email"
    ] }),
    /* @__PURE__ */ t.jsx(l, { href: u(), className: "mx-auto block text-sm", children: "Log out" })
  ] });
}
export {
  A as default
};
