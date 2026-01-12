import { d as m, r as b, j as s } from "./compiler-runtime-DyHbwOGE.js";
import { A as f } from "./app-layout-BgMxCcMd.js";
import { d as u } from "./index-DLuvQKrb.js";
import { H as h } from "./app-mGJhb3YO.js";
function n(r) {
  const e = m.c(9), {
    className: a
  } = r, l = b.useId();
  let o;
  e[0] === Symbol.for("react.memo_cache_sentinel") ? (o = /* @__PURE__ */ s.jsx("path", { d: "M-3 13 15-5M-5 5l18-18M-1 21 17 3" }), e[0] = o) : o = e[0];
  let t;
  e[1] !== l ? (t = /* @__PURE__ */ s.jsx("defs", { children: /* @__PURE__ */ s.jsx("pattern", { id: l, x: "0", y: "0", width: "10", height: "10", patternUnits: "userSpaceOnUse", children: o }) }), e[1] = l, e[2] = t) : t = e[2];
  const c = `url(#${l})`;
  let d;
  e[3] !== c ? (d = /* @__PURE__ */ s.jsx("rect", { stroke: "none", fill: c, width: "100%", height: "100%" }), e[3] = c, e[4] = d) : d = e[4];
  let i;
  return e[5] !== a || e[6] !== t || e[7] !== d ? (i = /* @__PURE__ */ s.jsxs("svg", { className: a, fill: "none", children: [
    t,
    d
  ] }), e[5] = a, e[6] = t, e[7] = d, e[8] = i) : i = e[8], i;
}
const x = [{
  title: "Dashboard",
  href: u().url
}];
function _() {
  const r = m.c(5);
  let e;
  r[0] === Symbol.for("react.memo_cache_sentinel") ? (e = /* @__PURE__ */ s.jsx(h, { title: "Dashboard" }), r[0] = e) : e = r[0];
  let a;
  r[1] === Symbol.for("react.memo_cache_sentinel") ? (a = /* @__PURE__ */ s.jsx("div", { className: "relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border", children: /* @__PURE__ */ s.jsx(n, { className: "absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" }) }), r[1] = a) : a = r[1];
  let l;
  r[2] === Symbol.for("react.memo_cache_sentinel") ? (l = /* @__PURE__ */ s.jsx("div", { className: "relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border", children: /* @__PURE__ */ s.jsx(n, { className: "absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" }) }), r[2] = l) : l = r[2];
  let o;
  r[3] === Symbol.for("react.memo_cache_sentinel") ? (o = /* @__PURE__ */ s.jsxs("div", { className: "grid auto-rows-min gap-4 md:grid-cols-3", children: [
    a,
    l,
    /* @__PURE__ */ s.jsx("div", { className: "relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border", children: /* @__PURE__ */ s.jsx(n, { className: "absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" }) })
  ] }), r[3] = o) : o = r[3];
  let t;
  return r[4] === Symbol.for("react.memo_cache_sentinel") ? (t = /* @__PURE__ */ s.jsxs(f, { breadcrumbs: x, children: [
    e,
    /* @__PURE__ */ s.jsxs("div", { className: "flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4", children: [
      o,
      /* @__PURE__ */ s.jsx("div", { className: "relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border", children: /* @__PURE__ */ s.jsx(n, { className: "absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" }) })
    ] })
  ] }), r[4] = t) : t = r[4], t;
}
export {
  _ as default
};
