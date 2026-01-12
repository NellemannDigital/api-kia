import { d as h, j as s } from "./compiler-runtime-DyHbwOGE.js";
import { A as p } from "./app-logo-icon-CT5L2AiP.js";
import { h as u } from "./index-DLuvQKrb.js";
import { L as j } from "./app-mGJhb3YO.js";
function N(x) {
  const e = h.c(17), {
    children: i,
    title: t,
    description: c
  } = x;
  let l;
  e[0] === Symbol.for("react.memo_cache_sentinel") ? (l = u(), e[0] = l) : l = e[0];
  let r;
  e[1] === Symbol.for("react.memo_cache_sentinel") ? (r = /* @__PURE__ */ s.jsx("div", { className: "mb-1 flex h-9 w-9 items-center justify-center rounded-md", children: /* @__PURE__ */ s.jsx(p, { className: "size-9 fill-current text-[var(--foreground)] dark:text-white" }) }), e[1] = r) : r = e[1];
  let n;
  e[2] !== t ? (n = /* @__PURE__ */ s.jsxs(j, { href: l, className: "flex flex-col items-center gap-2 font-medium", children: [
    r,
    /* @__PURE__ */ s.jsx("span", { className: "sr-only", children: t })
  ] }), e[2] = t, e[3] = n) : n = e[3];
  let m;
  e[4] !== t ? (m = /* @__PURE__ */ s.jsx("h1", { className: "text-xl font-medium", children: t }), e[4] = t, e[5] = m) : m = e[5];
  let a;
  e[6] !== c ? (a = /* @__PURE__ */ s.jsx("p", { className: "text-center text-sm text-muted-foreground", children: c }), e[6] = c, e[7] = a) : a = e[7];
  let o;
  e[8] !== m || e[9] !== a ? (o = /* @__PURE__ */ s.jsxs("div", { className: "space-y-2 text-center", children: [
    m,
    a
  ] }), e[8] = m, e[9] = a, e[10] = o) : o = e[10];
  let f;
  e[11] !== n || e[12] !== o ? (f = /* @__PURE__ */ s.jsxs("div", { className: "flex flex-col items-center gap-4", children: [
    n,
    o
  ] }), e[11] = n, e[12] = o, e[13] = f) : f = e[13];
  let d;
  return e[14] !== i || e[15] !== f ? (d = /* @__PURE__ */ s.jsx("div", { className: "flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10", children: /* @__PURE__ */ s.jsx("div", { className: "w-full max-w-sm", children: /* @__PURE__ */ s.jsxs("div", { className: "flex flex-col gap-8", children: [
    f,
    i
  ] }) }) }), e[14] = i, e[15] = f, e[16] = d) : d = e[16], d;
}
function A(x) {
  const e = h.c(10);
  let i, t, c, l;
  e[0] !== x ? ({
    children: i,
    title: l,
    description: t,
    ...c
  } = x, e[0] = x, e[1] = i, e[2] = t, e[3] = c, e[4] = l) : (i = e[1], t = e[2], c = e[3], l = e[4]);
  let r;
  return e[5] !== i || e[6] !== t || e[7] !== c || e[8] !== l ? (r = /* @__PURE__ */ s.jsx(N, { title: l, description: t, ...c, children: i }), e[5] = i, e[6] = t, e[7] = c, e[8] = l, e[9] = r) : r = e[9], r;
}
export {
  A
};
