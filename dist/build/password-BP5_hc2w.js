import { d as N, r as x, j as r } from "./compiler-runtime-DyHbwOGE.js";
import { q as _ } from "./index-DLuvQKrb.js";
import { I as p } from "./input-error-Bu6cIHH2.js";
import { A as v } from "./app-layout-BgMxCcMd.js";
import { a as F, S as Q, H as q } from "./layout-DfptgqeP.js";
import { F as C, H as E } from "./app-mGJhb3YO.js";
import { B as P } from "./app-logo-icon-CT5L2AiP.js";
import { I as f } from "./input-CNccwKGQ.js";
import { L as w } from "./label-Cxe3Hu_U.js";
import { z as I } from "./transition-DYhr-csv.js";
const s = (e) => ({
  url: s.url(e),
  method: "get"
});
s.definition = {
  methods: ["get", "head"],
  url: "/settings/password"
};
s.url = (e) => s.definition.url + _(e);
s.get = (e) => ({
  url: s.url(e),
  method: "get"
});
s.head = (e) => ({
  url: s.url(e),
  method: "head"
});
const h = (e) => ({
  action: s.url(e),
  method: "get"
});
h.get = (e) => ({
  action: s.url(e),
  method: "get"
});
h.head = (e) => ({
  action: s.url({
    [e?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "HEAD",
      ...e?.query ?? e?.mergeQuery ?? {}
    }
  }),
  method: "get"
});
s.form = h;
const t = (e) => ({
  url: t.url(e),
  method: "put"
});
t.definition = {
  methods: ["put"],
  url: "/settings/password"
};
t.url = (e) => t.definition.url + _(e);
t.put = (e) => ({
  url: t.url(e),
  method: "put"
});
const j = (e) => ({
  action: t.url({
    [e?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "PUT",
      ...e?.query ?? e?.mergeQuery ?? {}
    }
  }),
  method: "post"
});
j.put = (e) => ({
  action: t.url({
    [e?.mergeQuery ? "mergeQuery" : "query"]: {
      _method: "PUT",
      ...e?.query ?? e?.mergeQuery ?? {}
    }
  }),
  method: "post"
});
t.form = j;
const H = {
  update: t
}, L = [{
  title: "Password settings",
  href: F().url
}];
function G() {
  const e = N.c(7), g = x.useRef(null), y = x.useRef(null);
  let o;
  e[0] === Symbol.for("react.memo_cache_sentinel") ? (o = /* @__PURE__ */ r.jsx(E, { title: "Password settings" }), e[0] = o) : o = e[0];
  let a;
  e[1] === Symbol.for("react.memo_cache_sentinel") ? (a = /* @__PURE__ */ r.jsx(q, { title: "Update password", description: "Ensure your account is using a long, random password to stay secure" }), e[1] = a) : a = e[1];
  let n, l, d;
  e[2] === Symbol.for("react.memo_cache_sentinel") ? (n = H.update.form(), l = {
    preserveScroll: !0
  }, d = ["password", "password_confirmation", "current_password"], e[2] = n, e[3] = l, e[4] = d) : (n = e[2], l = e[3], d = e[4]);
  let m;
  e[5] === Symbol.for("react.memo_cache_sentinel") ? (m = (u) => {
    u.password && g.current?.focus(), u.current_password && y.current?.focus();
  }, e[5] = m) : m = e[5];
  let i;
  return e[6] === Symbol.for("react.memo_cache_sentinel") ? (i = /* @__PURE__ */ r.jsxs(v, { breadcrumbs: L, children: [
    o,
    /* @__PURE__ */ r.jsx(Q, { children: /* @__PURE__ */ r.jsxs("div", { className: "space-y-6", children: [
      a,
      /* @__PURE__ */ r.jsx(C, { ...n, options: l, resetOnError: d, resetOnSuccess: !0, onError: m, className: "space-y-6", children: (u) => {
        const {
          errors: c,
          processing: b,
          recentlySuccessful: S
        } = u;
        return /* @__PURE__ */ r.jsxs(r.Fragment, { children: [
          /* @__PURE__ */ r.jsxs("div", { className: "grid gap-2", children: [
            /* @__PURE__ */ r.jsx(w, { htmlFor: "current_password", children: "Current password" }),
            /* @__PURE__ */ r.jsx(f, { id: "current_password", ref: y, name: "current_password", type: "password", className: "mt-1 block w-full", autoComplete: "current-password", placeholder: "Current password" }),
            /* @__PURE__ */ r.jsx(p, { message: c.current_password })
          ] }),
          /* @__PURE__ */ r.jsxs("div", { className: "grid gap-2", children: [
            /* @__PURE__ */ r.jsx(w, { htmlFor: "password", children: "New password" }),
            /* @__PURE__ */ r.jsx(f, { id: "password", ref: g, name: "password", type: "password", className: "mt-1 block w-full", autoComplete: "new-password", placeholder: "New password" }),
            /* @__PURE__ */ r.jsx(p, { message: c.password })
          ] }),
          /* @__PURE__ */ r.jsxs("div", { className: "grid gap-2", children: [
            /* @__PURE__ */ r.jsx(w, { htmlFor: "password_confirmation", children: "Confirm password" }),
            /* @__PURE__ */ r.jsx(f, { id: "password_confirmation", name: "password_confirmation", type: "password", className: "mt-1 block w-full", autoComplete: "new-password", placeholder: "Confirm password" }),
            /* @__PURE__ */ r.jsx(p, { message: c.password_confirmation })
          ] }),
          /* @__PURE__ */ r.jsxs("div", { className: "flex items-center gap-4", children: [
            /* @__PURE__ */ r.jsx(P, { disabled: b, "data-test": "update-password-button", children: "Save password" }),
            /* @__PURE__ */ r.jsx(I, { show: S, enter: "transition ease-in-out", enterFrom: "opacity-0", leave: "transition ease-in-out", leaveTo: "opacity-0", children: /* @__PURE__ */ r.jsx("p", { className: "text-sm text-neutral-600", children: "Saved" }) })
          ] })
        ] });
      } })
    ] }) })
  ] }), e[6] = i) : i = e[6], i;
}
export {
  G as default
};
