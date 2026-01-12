import { d as t, j as s } from "./compiler-runtime-DyHbwOGE.js";
import { I as a } from "./input-error-Bu6cIHH2.js";
import { B as i } from "./app-logo-icon-CT5L2AiP.js";
import { I as m } from "./input-CNccwKGQ.js";
import { L as n } from "./label-Cxe3Hu_U.js";
import { S as p } from "./spinner-D_0Rae4j.js";
import { A as c } from "./auth-layout-Q2uAfpi0.js";
import { s as d } from "./index-nuLNGCIN.js";
import { F as l, H as f } from "./app-mGJhb3YO.js";
function S() {
  const r = t.c(2);
  let o;
  r[0] === Symbol.for("react.memo_cache_sentinel") ? (o = /* @__PURE__ */ s.jsx(f, { title: "Confirm password" }), r[0] = o) : o = r[0];
  let e;
  return r[1] === Symbol.for("react.memo_cache_sentinel") ? (e = /* @__PURE__ */ s.jsxs(c, { title: "Confirm your password", description: "This is a secure area of the application. Please confirm your password before continuing.", children: [
    o,
    /* @__PURE__ */ s.jsx(l, { ...d.form(), resetOnSuccess: ["password"], children: u })
  ] }), r[1] = e) : e = r[1], e;
}
function u(r) {
  const {
    processing: o,
    errors: e
  } = r;
  return /* @__PURE__ */ s.jsxs("div", { className: "space-y-6", children: [
    /* @__PURE__ */ s.jsxs("div", { className: "grid gap-2", children: [
      /* @__PURE__ */ s.jsx(n, { htmlFor: "password", children: "Password" }),
      /* @__PURE__ */ s.jsx(m, { id: "password", type: "password", name: "password", placeholder: "Password", autoComplete: "current-password", autoFocus: !0 }),
      /* @__PURE__ */ s.jsx(a, { message: e.password })
    ] }),
    /* @__PURE__ */ s.jsx("div", { className: "flex items-center", children: /* @__PURE__ */ s.jsxs(i, { className: "w-full", disabled: o, "data-test": "confirm-password-button", children: [
      o && /* @__PURE__ */ s.jsx(p, {}),
      "Confirm password"
    ] }) })
  ] });
}
export {
  S as default
};
