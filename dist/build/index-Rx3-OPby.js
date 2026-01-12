import { r as p, j as c } from "./compiler-runtime-DyHbwOGE.js";
import { r as f } from "./index-GQej7NOz.js";
import { d as u } from "./app-logo-icon-CT5L2AiP.js";
var d = [
  "a",
  "button",
  "div",
  "form",
  "h2",
  "h3",
  "img",
  "input",
  "label",
  "li",
  "nav",
  "ol",
  "p",
  "select",
  "span",
  "svg",
  "ul"
], h = d.reduce((t, r) => {
  const o = u(`Primitive.${r}`), i = p.forwardRef((s, e) => {
    const { asChild: a, ...m } = s, n = a ? o : r;
    return typeof window < "u" && (window[Symbol.for("radix-ui")] = !0), /* @__PURE__ */ c.jsx(n, { ...m, ref: e });
  });
  return i.displayName = `Primitive.${r}`, { ...t, [r]: i };
}, {});
function E(t, r) {
  t && f.flushSync(() => t.dispatchEvent(r));
}
export {
  h as P,
  E as d
};
