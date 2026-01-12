import { d as l, j as e } from "./compiler-runtime-DyHbwOGE.js";
import { q as d, l as c } from "./index-DLuvQKrb.js";
import { F as p, H as u } from "./app-mGJhb3YO.js";
import { I as o } from "./input-error-Bu6cIHH2.js";
import { T as x } from "./text-link-DMLf__ta.js";
import { B as f } from "./app-logo-icon-CT5L2AiP.js";
import { I as i } from "./input-CNccwKGQ.js";
import { L as m } from "./label-Cxe3Hu_U.js";
import { S as h } from "./spinner-D_0Rae4j.js";
import { A as j } from "./auth-layout-Q2uAfpi0.js";
const r = (s) => ({
  url: r.url(s),
  method: "post"
});
r.definition = {
  methods: ["post"],
  url: "/register"
};
r.url = (s) => r.definition.url + d(s);
r.post = (s) => ({
  url: r.url(s),
  method: "post"
});
const n = (s) => ({
  action: r.url(s),
  method: "post"
});
n.post = (s) => ({
  action: r.url(s),
  method: "post"
});
r.form = n;
Object.assign(r, r);
function E() {
  const s = l.c(2);
  let a;
  s[0] === Symbol.for("react.memo_cache_sentinel") ? (a = /* @__PURE__ */ e.jsx(u, { title: "Register" }), s[0] = a) : a = s[0];
  let t;
  return s[1] === Symbol.for("react.memo_cache_sentinel") ? (t = /* @__PURE__ */ e.jsxs(j, { title: "Create an account", description: "Enter your details below to create your account", children: [
    a,
    /* @__PURE__ */ e.jsx(p, { ...r.form(), resetOnSuccess: ["password", "password_confirmation"], disableWhileProcessing: !0, className: "flex flex-col gap-6", children: g })
  ] }), s[1] = t) : t = s[1], t;
}
function g(s) {
  const {
    processing: a,
    errors: t
  } = s;
  return /* @__PURE__ */ e.jsxs(e.Fragment, { children: [
    /* @__PURE__ */ e.jsxs("div", { className: "grid gap-6", children: [
      /* @__PURE__ */ e.jsxs("div", { className: "grid gap-2", children: [
        /* @__PURE__ */ e.jsx(m, { htmlFor: "name", children: "Name" }),
        /* @__PURE__ */ e.jsx(i, { id: "name", type: "text", required: !0, autoFocus: !0, tabIndex: 1, autoComplete: "name", name: "name", placeholder: "Full name" }),
        /* @__PURE__ */ e.jsx(o, { message: t.name, className: "mt-2" })
      ] }),
      /* @__PURE__ */ e.jsxs("div", { className: "grid gap-2", children: [
        /* @__PURE__ */ e.jsx(m, { htmlFor: "email", children: "Email address" }),
        /* @__PURE__ */ e.jsx(i, { id: "email", type: "email", required: !0, tabIndex: 2, autoComplete: "email", name: "email", placeholder: "email@example.com" }),
        /* @__PURE__ */ e.jsx(o, { message: t.email })
      ] }),
      /* @__PURE__ */ e.jsxs("div", { className: "grid gap-2", children: [
        /* @__PURE__ */ e.jsx(m, { htmlFor: "password", children: "Password" }),
        /* @__PURE__ */ e.jsx(i, { id: "password", type: "password", required: !0, tabIndex: 3, autoComplete: "new-password", name: "password", placeholder: "Password" }),
        /* @__PURE__ */ e.jsx(o, { message: t.password })
      ] }),
      /* @__PURE__ */ e.jsxs("div", { className: "grid gap-2", children: [
        /* @__PURE__ */ e.jsx(m, { htmlFor: "password_confirmation", children: "Confirm password" }),
        /* @__PURE__ */ e.jsx(i, { id: "password_confirmation", type: "password", required: !0, tabIndex: 4, autoComplete: "new-password", name: "password_confirmation", placeholder: "Confirm password" }),
        /* @__PURE__ */ e.jsx(o, { message: t.password_confirmation })
      ] }),
      /* @__PURE__ */ e.jsxs(f, { type: "submit", className: "mt-2 w-full", tabIndex: 5, "data-test": "register-user-button", children: [
        a && /* @__PURE__ */ e.jsx(h, {}),
        "Create account"
      ] })
    ] }),
    /* @__PURE__ */ e.jsxs("div", { className: "text-center text-sm text-muted-foreground", children: [
      "Already have an account?",
      " ",
      /* @__PURE__ */ e.jsx(x, { href: c(), tabIndex: 6, children: "Log in" })
    ] })
  ] });
}
export {
  E as default
};
