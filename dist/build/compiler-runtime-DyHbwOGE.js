function ut(_, c) {
  for (var C = 0; C < c.length; C++) {
    const h = c[C];
    if (typeof h != "string" && !Array.isArray(h)) {
      for (const m in h)
        if (m !== "default" && !(m in _)) {
          const A = Object.getOwnPropertyDescriptor(h, m);
          A && Object.defineProperty(_, m, A.get ? A : {
            enumerable: !0,
            get: () => h[m]
          });
        }
    }
  }
  return Object.freeze(Object.defineProperty(_, Symbol.toStringTag, { value: "Module" }));
}
var vt = typeof globalThis < "u" ? globalThis : typeof window < "u" ? window : typeof global < "u" ? global : typeof self < "u" ? self : {};
function at(_) {
  return _ && _.__esModule && Object.prototype.hasOwnProperty.call(_, "default") ? _.default : _;
}
function ht(_) {
  if (Object.prototype.hasOwnProperty.call(_, "__esModule")) return _;
  var c = _.default;
  if (typeof c == "function") {
    var C = function h() {
      var m = !1;
      try {
        m = this instanceof h;
      } catch {
      }
      return m ? Reflect.construct(c, arguments, this.constructor) : c.apply(this, arguments);
    };
    C.prototype = c.prototype;
  } else C = {};
  return Object.defineProperty(C, "__esModule", { value: !0 }), Object.keys(_).forEach(function(h) {
    var m = Object.getOwnPropertyDescriptor(_, h);
    Object.defineProperty(C, h, m.get ? m : {
      enumerable: !0,
      get: function() {
        return _[h];
      }
    });
  }), C;
}
var ve = { exports: {} }, le = {};
var xe;
function st() {
  if (xe) return le;
  xe = 1;
  var _ = Symbol.for("react.transitional.element"), c = Symbol.for("react.fragment");
  function C(h, m, A) {
    var M = null;
    if (A !== void 0 && (M = "" + A), m.key !== void 0 && (M = "" + m.key), "key" in m) {
      A = {};
      for (var I in m)
        I !== "key" && (A[I] = m[I]);
    } else A = m;
    return m = A.ref, {
      $$typeof: _,
      type: h,
      key: M,
      ref: m !== void 0 ? m : null,
      props: A
    };
  }
  return le.Fragment = c, le.jsx = C, le.jsxs = C, le;
}
var de = {}, he = { exports: {} }, v = {};
var qe;
function it() {
  if (qe) return v;
  qe = 1;
  var _ = Symbol.for("react.transitional.element"), c = Symbol.for("react.portal"), C = Symbol.for("react.fragment"), h = Symbol.for("react.strict_mode"), m = Symbol.for("react.profiler"), A = Symbol.for("react.consumer"), M = Symbol.for("react.context"), I = Symbol.for("react.forward_ref"), d = Symbol.for("react.suspense"), u = Symbol.for("react.memo"), O = Symbol.for("react.lazy"), s = Symbol.for("react.activity"), n = Symbol.iterator;
  function y(t) {
    return t === null || typeof t != "object" ? null : (t = n && t[n] || t["@@iterator"], typeof t == "function" ? t : null);
  }
  var D = {
    isMounted: function() {
      return !1;
    },
    enqueueForceUpdate: function() {
    },
    enqueueReplaceState: function() {
    },
    enqueueSetState: function() {
    }
  }, x = Object.assign, q = {};
  function V(t, o, f) {
    this.props = t, this.context = o, this.refs = q, this.updater = f || D;
  }
  V.prototype.isReactComponent = {}, V.prototype.setState = function(t, o) {
    if (typeof t != "object" && typeof t != "function" && t != null)
      throw Error(
        "takes an object of state variables to update or a function which returns an object of state variables."
      );
    this.updater.enqueueSetState(this, t, o, "setState");
  }, V.prototype.forceUpdate = function(t) {
    this.updater.enqueueForceUpdate(this, t, "forceUpdate");
  };
  function Q() {
  }
  Q.prototype = V.prototype;
  function ne(t, o, f) {
    this.props = t, this.context = o, this.refs = q, this.updater = f || D;
  }
  var Z = ne.prototype = new Q();
  Z.constructor = ne, x(Z, V.prototype), Z.isPureReactComponent = !0;
  var z = Array.isArray;
  function oe() {
  }
  var k = { H: null, A: null, T: null, S: null }, se = Object.prototype.hasOwnProperty;
  function U(t, o, f) {
    var l = f.ref;
    return {
      $$typeof: _,
      type: t,
      key: o,
      ref: l !== void 0 ? l : null,
      props: f
    };
  }
  function J(t, o) {
    return U(t.type, o, t.props);
  }
  function ue(t) {
    return typeof t == "object" && t !== null && t.$$typeof === _;
  }
  function N(t) {
    var o = { "=": "=0", ":": "=2" };
    return "$" + t.replace(/[=:]/g, function(f) {
      return o[f];
    });
  }
  var ee = /\/+/g;
  function B(t, o) {
    return typeof t == "object" && t !== null && t.key != null ? N("" + t.key) : o.toString(36);
  }
  function G(t) {
    switch (t.status) {
      case "fulfilled":
        return t.value;
      case "rejected":
        throw t.reason;
      default:
        switch (typeof t.status == "string" ? t.then(oe, oe) : (t.status = "pending", t.then(
          function(o) {
            t.status === "pending" && (t.status = "fulfilled", t.value = o);
          },
          function(o) {
            t.status === "pending" && (t.status = "rejected", t.reason = o);
          }
        )), t.status) {
          case "fulfilled":
            return t.value;
          case "rejected":
            throw t.reason;
        }
    }
    throw t;
  }
  function H(t, o, f, l, R) {
    var S = typeof t;
    (S === "undefined" || S === "boolean") && (t = null);
    var g = !1;
    if (t === null) g = !0;
    else
      switch (S) {
        case "bigint":
        case "string":
        case "number":
          g = !0;
          break;
        case "object":
          switch (t.$$typeof) {
            case _:
            case c:
              g = !0;
              break;
            case O:
              return g = t._init, H(
                g(t._payload),
                o,
                f,
                l,
                R
              );
          }
      }
    if (g)
      return R = R(t), g = l === "" ? "." + B(t, 0) : l, z(R) ? (f = "", g != null && (f = g.replace(ee, "$&/") + "/"), H(R, o, f, "", function(X) {
        return X;
      })) : R != null && (ue(R) && (R = J(
        R,
        f + (R.key == null || t && t.key === R.key ? "" : ("" + R.key).replace(
          ee,
          "$&/"
        ) + "/") + g
      )), o.push(R)), 1;
    g = 0;
    var L = l === "" ? "." : l + ":";
    if (z(t))
      for (var j = 0; j < t.length; j++)
        l = t[j], S = L + B(l, j), g += H(
          l,
          o,
          f,
          S,
          R
        );
    else if (j = y(t), typeof j == "function")
      for (t = j.call(t), j = 0; !(l = t.next()).done; )
        l = l.value, S = L + B(l, j++), g += H(
          l,
          o,
          f,
          S,
          R
        );
    else if (S === "object") {
      if (typeof t.then == "function")
        return H(
          G(t),
          o,
          f,
          l,
          R
        );
      throw o = String(t), Error(
        "Objects are not valid as a React child (found: " + (o === "[object Object]" ? "object with keys {" + Object.keys(t).join(", ") + "}" : o) + "). If you meant to render a collection of children, use an array instead."
      );
    }
    return g;
  }
  function F(t, o, f) {
    if (t == null) return t;
    var l = [], R = 0;
    return H(t, l, "", "", function(S) {
      return o.call(f, S, R++);
    }), l;
  }
  function te(t) {
    if (t._status === -1) {
      var o = t._result;
      o = o(), o.then(
        function(f) {
          (t._status === 0 || t._status === -1) && (t._status = 1, t._result = f);
        },
        function(f) {
          (t._status === 0 || t._status === -1) && (t._status = 2, t._result = f);
        }
      ), t._status === -1 && (t._status = 0, t._result = o);
    }
    if (t._status === 1) return t._result.default;
    throw t._result;
  }
  var K = typeof reportError == "function" ? reportError : function(t) {
    if (typeof window == "object" && typeof window.ErrorEvent == "function") {
      var o = new window.ErrorEvent("error", {
        bubbles: !0,
        cancelable: !0,
        message: typeof t == "object" && t !== null && typeof t.message == "string" ? String(t.message) : String(t),
        error: t
      });
      if (!window.dispatchEvent(o)) return;
    } else if (typeof process == "object" && typeof process.emit == "function") {
      process.emit("uncaughtException", t);
      return;
    }
    console.error(t);
  }, ae = {
    map: F,
    forEach: function(t, o, f) {
      F(
        t,
        function() {
          o.apply(this, arguments);
        },
        f
      );
    },
    count: function(t) {
      var o = 0;
      return F(t, function() {
        o++;
      }), o;
    },
    toArray: function(t) {
      return F(t, function(o) {
        return o;
      }) || [];
    },
    only: function(t) {
      if (!ue(t))
        throw Error(
          "React.Children.only expected to receive a single React element child."
        );
      return t;
    }
  };
  return v.Activity = s, v.Children = ae, v.Component = V, v.Fragment = C, v.Profiler = m, v.PureComponent = ne, v.StrictMode = h, v.Suspense = d, v.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = k, v.__COMPILER_RUNTIME = {
    __proto__: null,
    c: function(t) {
      return k.H.useMemoCache(t);
    }
  }, v.cache = function(t) {
    return function() {
      return t.apply(null, arguments);
    };
  }, v.cacheSignal = function() {
    return null;
  }, v.cloneElement = function(t, o, f) {
    if (t == null)
      throw Error(
        "The argument must be a React element, but you passed " + t + "."
      );
    var l = x({}, t.props), R = t.key;
    if (o != null)
      for (S in o.key !== void 0 && (R = "" + o.key), o)
        !se.call(o, S) || S === "key" || S === "__self" || S === "__source" || S === "ref" && o.ref === void 0 || (l[S] = o[S]);
    var S = arguments.length - 2;
    if (S === 1) l.children = f;
    else if (1 < S) {
      for (var g = Array(S), L = 0; L < S; L++)
        g[L] = arguments[L + 2];
      l.children = g;
    }
    return U(t.type, R, l);
  }, v.createContext = function(t) {
    return t = {
      $$typeof: M,
      _currentValue: t,
      _currentValue2: t,
      _threadCount: 0,
      Provider: null,
      Consumer: null
    }, t.Provider = t, t.Consumer = {
      $$typeof: A,
      _context: t
    }, t;
  }, v.createElement = function(t, o, f) {
    var l, R = {}, S = null;
    if (o != null)
      for (l in o.key !== void 0 && (S = "" + o.key), o)
        se.call(o, l) && l !== "key" && l !== "__self" && l !== "__source" && (R[l] = o[l]);
    var g = arguments.length - 2;
    if (g === 1) R.children = f;
    else if (1 < g) {
      for (var L = Array(g), j = 0; j < g; j++)
        L[j] = arguments[j + 2];
      R.children = L;
    }
    if (t && t.defaultProps)
      for (l in g = t.defaultProps, g)
        R[l] === void 0 && (R[l] = g[l]);
    return U(t, S, R);
  }, v.createRef = function() {
    return { current: null };
  }, v.forwardRef = function(t) {
    return { $$typeof: I, render: t };
  }, v.isValidElement = ue, v.lazy = function(t) {
    return {
      $$typeof: O,
      _payload: { _status: -1, _result: t },
      _init: te
    };
  }, v.memo = function(t, o) {
    return {
      $$typeof: u,
      type: t,
      compare: o === void 0 ? null : o
    };
  }, v.startTransition = function(t) {
    var o = k.T, f = {};
    k.T = f;
    try {
      var l = t(), R = k.S;
      R !== null && R(f, l), typeof l == "object" && l !== null && typeof l.then == "function" && l.then(oe, K);
    } catch (S) {
      K(S);
    } finally {
      o !== null && f.types !== null && (o.types = f.types), k.T = o;
    }
  }, v.unstable_useCacheRefresh = function() {
    return k.H.useCacheRefresh();
  }, v.use = function(t) {
    return k.H.use(t);
  }, v.useActionState = function(t, o, f) {
    return k.H.useActionState(t, o, f);
  }, v.useCallback = function(t, o) {
    return k.H.useCallback(t, o);
  }, v.useContext = function(t) {
    return k.H.useContext(t);
  }, v.useDebugValue = function() {
  }, v.useDeferredValue = function(t, o) {
    return k.H.useDeferredValue(t, o);
  }, v.useEffect = function(t, o) {
    return k.H.useEffect(t, o);
  }, v.useEffectEvent = function(t) {
    return k.H.useEffectEvent(t);
  }, v.useId = function() {
    return k.H.useId();
  }, v.useImperativeHandle = function(t, o, f) {
    return k.H.useImperativeHandle(t, o, f);
  }, v.useInsertionEffect = function(t, o) {
    return k.H.useInsertionEffect(t, o);
  }, v.useLayoutEffect = function(t, o) {
    return k.H.useLayoutEffect(t, o);
  }, v.useMemo = function(t, o) {
    return k.H.useMemo(t, o);
  }, v.useOptimistic = function(t, o) {
    return k.H.useOptimistic(t, o);
  }, v.useReducer = function(t, o, f) {
    return k.H.useReducer(t, o, f);
  }, v.useRef = function(t) {
    return k.H.useRef(t);
  }, v.useState = function(t) {
    return k.H.useState(t);
  }, v.useSyncExternalStore = function(t, o, f) {
    return k.H.useSyncExternalStore(
      t,
      o,
      f
    );
  }, v.useTransition = function() {
    return k.H.useTransition();
  }, v.version = "19.2.3", v;
}
var pe = { exports: {} };
pe.exports;
var We;
function ct() {
  return We || (We = 1, (function(_, c) {
    process.env.NODE_ENV !== "production" && (function() {
      function C(e, r) {
        Object.defineProperty(A.prototype, e, {
          get: function() {
            console.warn(
              "%s(...) is deprecated in plain JavaScript React classes. %s",
              r[0],
              r[1]
            );
          }
        });
      }
      function h(e) {
        return e === null || typeof e != "object" ? null : (e = Ae && e[Ae] || e["@@iterator"], typeof e == "function" ? e : null);
      }
      function m(e, r) {
        e = (e = e.constructor) && (e.displayName || e.name) || "ReactClass";
        var a = e + "." + r;
        Ce[a] || (console.error(
          "Can't call %s on a component that is not yet mounted. This is a no-op, but it might indicate a bug in your application. Instead, assign to `this.state` directly or define a `state = {};` class property with the desired state in the %s component.",
          r,
          e
        ), Ce[a] = !0);
      }
      function A(e, r, a) {
        this.props = e, this.context = r, this.refs = Oe, this.updater = a || ke;
      }
      function M() {
      }
      function I(e, r, a) {
        this.props = e, this.context = r, this.refs = Oe, this.updater = a || ke;
      }
      function d() {
      }
      function u(e) {
        return "" + e;
      }
      function O(e) {
        try {
          u(e);
          var r = !1;
        } catch {
          r = !0;
        }
        if (r) {
          r = console;
          var a = r.error, i = typeof Symbol == "function" && Symbol.toStringTag && e[Symbol.toStringTag] || e.constructor.name || "Object";
          return a.call(
            r,
            "The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",
            i
          ), u(e);
        }
      }
      function s(e) {
        if (e == null) return null;
        if (typeof e == "function")
          return e.$$typeof === et ? null : e.displayName || e.name || null;
        if (typeof e == "string") return e;
        switch (e) {
          case t:
            return "Fragment";
          case f:
            return "Profiler";
          case o:
            return "StrictMode";
          case g:
            return "Suspense";
          case L:
            return "SuspenseList";
          case Se:
            return "Activity";
        }
        if (typeof e == "object")
          switch (typeof e.tag == "number" && console.error(
            "Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."
          ), e.$$typeof) {
            case ae:
              return "Portal";
            case R:
              return e.displayName || "Context";
            case l:
              return (e._context.displayName || "Context") + ".Consumer";
            case S:
              var r = e.render;
              return e = e.displayName, e || (e = r.displayName || r.name || "", e = e !== "" ? "ForwardRef(" + e + ")" : "ForwardRef"), e;
            case j:
              return r = e.displayName || null, r !== null ? r : s(e.type) || "Memo";
            case X:
              r = e._payload, e = e._init;
              try {
                return s(e(r));
              } catch {
              }
          }
        return null;
      }
      function n(e) {
        if (e === t) return "<>";
        if (typeof e == "object" && e !== null && e.$$typeof === X)
          return "<...>";
        try {
          var r = s(e);
          return r ? "<" + r + ">" : "<...>";
        } catch {
          return "<...>";
        }
      }
      function y() {
        var e = T.A;
        return e === null ? null : e.getOwner();
      }
      function D() {
        return Error("react-stack-top-frame");
      }
      function x(e) {
        if (ye.call(e, "key")) {
          var r = Object.getOwnPropertyDescriptor(e, "key").get;
          if (r && r.isReactWarning) return !1;
        }
        return e.key !== void 0;
      }
      function q(e, r) {
        function a() {
          De || (De = !0, console.error(
            "%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://react.dev/link/special-props)",
            r
          ));
        }
        a.isReactWarning = !0, Object.defineProperty(e, "key", {
          get: a,
          configurable: !0
        });
      }
      function V() {
        var e = s(this.type);
        return Le[e] || (Le[e] = !0, console.error(
          "Accessing element.ref was removed in React 19. ref is now a regular prop. It will be removed from the JSX Element type in a future release."
        )), e = this.props.ref, e !== void 0 ? e : null;
      }
      function Q(e, r, a, i, p, b) {
        var E = a.ref;
        return e = {
          $$typeof: K,
          type: e,
          key: r,
          props: a,
          _owner: i
        }, (E !== void 0 ? E : null) !== null ? Object.defineProperty(e, "ref", {
          enumerable: !1,
          get: V
        }) : Object.defineProperty(e, "ref", { enumerable: !1, value: null }), e._store = {}, Object.defineProperty(e._store, "validated", {
          configurable: !1,
          enumerable: !1,
          writable: !0,
          value: 0
        }), Object.defineProperty(e, "_debugInfo", {
          configurable: !1,
          enumerable: !1,
          writable: !0,
          value: null
        }), Object.defineProperty(e, "_debugStack", {
          configurable: !1,
          enumerable: !1,
          writable: !0,
          value: p
        }), Object.defineProperty(e, "_debugTask", {
          configurable: !1,
          enumerable: !1,
          writable: !0,
          value: b
        }), Object.freeze && (Object.freeze(e.props), Object.freeze(e)), e;
      }
      function ne(e, r) {
        return r = Q(
          e.type,
          r,
          e.props,
          e._owner,
          e._debugStack,
          e._debugTask
        ), e._store && (r._store.validated = e._store.validated), r;
      }
      function Z(e) {
        z(e) ? e._store && (e._store.validated = 1) : typeof e == "object" && e !== null && e.$$typeof === X && (e._payload.status === "fulfilled" ? z(e._payload.value) && e._payload.value._store && (e._payload.value._store.validated = 1) : e._store && (e._store.validated = 1));
      }
      function z(e) {
        return typeof e == "object" && e !== null && e.$$typeof === K;
      }
      function oe(e) {
        var r = { "=": "=0", ":": "=2" };
        return "$" + e.replace(/[=:]/g, function(a) {
          return r[a];
        });
      }
      function k(e, r) {
        return typeof e == "object" && e !== null && e.key != null ? (O(e.key), oe("" + e.key)) : r.toString(36);
      }
      function se(e) {
        switch (e.status) {
          case "fulfilled":
            return e.value;
          case "rejected":
            throw e.reason;
          default:
            switch (typeof e.status == "string" ? e.then(d, d) : (e.status = "pending", e.then(
              function(r) {
                e.status === "pending" && (e.status = "fulfilled", e.value = r);
              },
              function(r) {
                e.status === "pending" && (e.status = "rejected", e.reason = r);
              }
            )), e.status) {
              case "fulfilled":
                return e.value;
              case "rejected":
                throw e.reason;
            }
        }
        throw e;
      }
      function U(e, r, a, i, p) {
        var b = typeof e;
        (b === "undefined" || b === "boolean") && (e = null);
        var E = !1;
        if (e === null) E = !0;
        else
          switch (b) {
            case "bigint":
            case "string":
            case "number":
              E = !0;
              break;
            case "object":
              switch (e.$$typeof) {
                case K:
                case ae:
                  E = !0;
                  break;
                case X:
                  return E = e._init, U(
                    E(e._payload),
                    r,
                    a,
                    i,
                    p
                  );
              }
          }
        if (E) {
          E = e, p = p(E);
          var P = i === "" ? "." + k(E, 0) : i;
          return Pe(p) ? (a = "", P != null && (a = P.replace(Ye, "$&/") + "/"), U(p, r, a, "", function(re) {
            return re;
          })) : p != null && (z(p) && (p.key != null && (E && E.key === p.key || O(p.key)), a = ne(
            p,
            a + (p.key == null || E && E.key === p.key ? "" : ("" + p.key).replace(
              Ye,
              "$&/"
            ) + "/") + P
          ), i !== "" && E != null && z(E) && E.key == null && E._store && !E._store.validated && (a._store.validated = 2), p = a), r.push(p)), 1;
        }
        if (E = 0, P = i === "" ? "." : i + ":", Pe(e))
          for (var w = 0; w < e.length; w++)
            i = e[w], b = P + k(i, w), E += U(
              i,
              r,
              a,
              b,
              p
            );
        else if (w = h(e), typeof w == "function")
          for (w === e.entries && (Ie || console.warn(
            "Using Maps as children is not supported. Use an array of keyed ReactElements instead."
          ), Ie = !0), e = w.call(e), w = 0; !(i = e.next()).done; )
            i = i.value, b = P + k(i, w++), E += U(
              i,
              r,
              a,
              b,
              p
            );
        else if (b === "object") {
          if (typeof e.then == "function")
            return U(
              se(e),
              r,
              a,
              i,
              p
            );
          throw r = String(e), Error(
            "Objects are not valid as a React child (found: " + (r === "[object Object]" ? "object with keys {" + Object.keys(e).join(", ") + "}" : r) + "). If you meant to render a collection of children, use an array instead."
          );
        }
        return E;
      }
      function J(e, r, a) {
        if (e == null) return e;
        var i = [], p = 0;
        return U(e, i, "", "", function(b) {
          return r.call(a, b, p++);
        }), i;
      }
      function ue(e) {
        if (e._status === -1) {
          var r = e._ioInfo;
          r != null && (r.start = r.end = performance.now()), r = e._result;
          var a = r();
          if (a.then(
            function(p) {
              if (e._status === 0 || e._status === -1) {
                e._status = 1, e._result = p;
                var b = e._ioInfo;
                b != null && (b.end = performance.now()), a.status === void 0 && (a.status = "fulfilled", a.value = p);
              }
            },
            function(p) {
              if (e._status === 0 || e._status === -1) {
                e._status = 2, e._result = p;
                var b = e._ioInfo;
                b != null && (b.end = performance.now()), a.status === void 0 && (a.status = "rejected", a.reason = p);
              }
            }
          ), r = e._ioInfo, r != null) {
            r.value = a;
            var i = a.displayName;
            typeof i == "string" && (r.name = i);
          }
          e._status === -1 && (e._status = 0, e._result = a);
        }
        if (e._status === 1)
          return r = e._result, r === void 0 && console.error(
            `lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))

Did you accidentally put curly braces around the import?`,
            r
          ), "default" in r || console.error(
            `lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))`,
            r
          ), r.default;
        throw e._result;
      }
      function N() {
        var e = T.H;
        return e === null && console.error(
          `Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`
        ), e;
      }
      function ee() {
        T.asyncTransitions--;
      }
      function B(e) {
        if (_e === null)
          try {
            var r = ("require" + Math.random()).slice(0, 7);
            _e = (_ && _[r]).call(
              _,
              "timers"
            ).setImmediate;
          } catch {
            _e = function(i) {
              Ue === !1 && (Ue = !0, typeof MessageChannel > "u" && console.error(
                "This browser does not have a MessageChannel implementation, so enqueuing tasks via await act(async () => ...) will fail. Please file an issue at https://github.com/facebook/react/issues if you encounter this warning."
              ));
              var p = new MessageChannel();
              p.port1.onmessage = i, p.port2.postMessage(void 0);
            };
          }
        return _e(e);
      }
      function G(e) {
        return 1 < e.length && typeof AggregateError == "function" ? new AggregateError(e) : e[0];
      }
      function H(e, r) {
        r !== ge - 1 && console.error(
          "You seem to have overlapping act() calls, this is not supported. Be sure to await previous act() calls before making a new one. "
        ), ge = r;
      }
      function F(e, r, a) {
        var i = T.actQueue;
        if (i !== null)
          if (i.length !== 0)
            try {
              te(i), B(function() {
                return F(e, r, a);
              });
              return;
            } catch (p) {
              T.thrownErrors.push(p);
            }
          else T.actQueue = null;
        0 < T.thrownErrors.length ? (i = G(T.thrownErrors), T.thrownErrors.length = 0, a(i)) : r(e);
      }
      function te(e) {
        if (!Te) {
          Te = !0;
          var r = 0;
          try {
            for (; r < e.length; r++) {
              var a = e[r];
              do {
                T.didUsePromise = !1;
                var i = a(!1);
                if (i !== null) {
                  if (T.didUsePromise) {
                    e[r] = a, e.splice(0, r);
                    return;
                  }
                  a = i;
                } else break;
              } while (!0);
            }
            e.length = 0;
          } catch (p) {
            e.splice(0, r + 1), T.thrownErrors.push(p);
          } finally {
            Te = !1;
          }
        }
      }
      typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u" && typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart == "function" && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());
      var K = Symbol.for("react.transitional.element"), ae = Symbol.for("react.portal"), t = Symbol.for("react.fragment"), o = Symbol.for("react.strict_mode"), f = Symbol.for("react.profiler"), l = Symbol.for("react.consumer"), R = Symbol.for("react.context"), S = Symbol.for("react.forward_ref"), g = Symbol.for("react.suspense"), L = Symbol.for("react.suspense_list"), j = Symbol.for("react.memo"), X = Symbol.for("react.lazy"), Se = Symbol.for("react.activity"), Ae = Symbol.iterator, Ce = {}, ke = {
        isMounted: function() {
          return !1;
        },
        enqueueForceUpdate: function(e) {
          m(e, "forceUpdate");
        },
        enqueueReplaceState: function(e) {
          m(e, "replaceState");
        },
        enqueueSetState: function(e) {
          m(e, "setState");
        }
      }, Ne = Object.assign, Oe = {};
      Object.freeze(Oe), A.prototype.isReactComponent = {}, A.prototype.setState = function(e, r) {
        if (typeof e != "object" && typeof e != "function" && e != null)
          throw Error(
            "takes an object of state variables to update or a function which returns an object of state variables."
          );
        this.updater.enqueueSetState(this, e, r, "setState");
      }, A.prototype.forceUpdate = function(e) {
        this.updater.enqueueForceUpdate(this, e, "forceUpdate");
      };
      var W = {
        isMounted: [
          "isMounted",
          "Instead, make sure to clean up subscriptions and pending requests in componentWillUnmount to prevent memory leaks."
        ],
        replaceState: [
          "replaceState",
          "Refactor your code to use setState instead (see https://github.com/facebook/react/issues/3236)."
        ]
      };
      for (fe in W)
        W.hasOwnProperty(fe) && C(fe, W[fe]);
      M.prototype = A.prototype, W = I.prototype = new M(), W.constructor = I, Ne(W, A.prototype), W.isPureReactComponent = !0;
      var Pe = Array.isArray, et = Symbol.for("react.client.reference"), T = {
        H: null,
        A: null,
        T: null,
        S: null,
        actQueue: null,
        asyncTransitions: 0,
        isBatchingLegacy: !1,
        didScheduleLegacyUpdate: !1,
        didUsePromise: !1,
        thrownErrors: [],
        getCurrentStack: null,
        recentlyCreatedOwnerStacks: 0
      }, ye = Object.prototype.hasOwnProperty, je = console.createTask ? console.createTask : function() {
        return null;
      };
      W = {
        react_stack_bottom_frame: function(e) {
          return e();
        }
      };
      var De, Me, Le = {}, tt = W.react_stack_bottom_frame.bind(
        W,
        D
      )(), rt = je(n(D)), Ie = !1, Ye = /\/+/g, $e = typeof reportError == "function" ? reportError : function(e) {
        if (typeof window == "object" && typeof window.ErrorEvent == "function") {
          var r = new window.ErrorEvent("error", {
            bubbles: !0,
            cancelable: !0,
            message: typeof e == "object" && e !== null && typeof e.message == "string" ? String(e.message) : String(e),
            error: e
          });
          if (!window.dispatchEvent(r)) return;
        } else if (typeof process == "object" && typeof process.emit == "function") {
          process.emit("uncaughtException", e);
          return;
        }
        console.error(e);
      }, Ue = !1, _e = null, ge = 0, me = !1, Te = !1, He = typeof queueMicrotask == "function" ? function(e) {
        queueMicrotask(function() {
          return queueMicrotask(e);
        });
      } : B;
      W = Object.freeze({
        __proto__: null,
        c: function(e) {
          return N().useMemoCache(e);
        }
      });
      var fe = {
        map: J,
        forEach: function(e, r, a) {
          J(
            e,
            function() {
              r.apply(this, arguments);
            },
            a
          );
        },
        count: function(e) {
          var r = 0;
          return J(e, function() {
            r++;
          }), r;
        },
        toArray: function(e) {
          return J(e, function(r) {
            return r;
          }) || [];
        },
        only: function(e) {
          if (!z(e))
            throw Error(
              "React.Children.only expected to receive a single React element child."
            );
          return e;
        }
      };
      c.Activity = Se, c.Children = fe, c.Component = A, c.Fragment = t, c.Profiler = f, c.PureComponent = I, c.StrictMode = o, c.Suspense = g, c.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = T, c.__COMPILER_RUNTIME = W, c.act = function(e) {
        var r = T.actQueue, a = ge;
        ge++;
        var i = T.actQueue = r !== null ? r : [], p = !1;
        try {
          var b = e();
        } catch (w) {
          T.thrownErrors.push(w);
        }
        if (0 < T.thrownErrors.length)
          throw H(r, a), e = G(T.thrownErrors), T.thrownErrors.length = 0, e;
        if (b !== null && typeof b == "object" && typeof b.then == "function") {
          var E = b;
          return He(function() {
            p || me || (me = !0, console.error(
              "You called act(async () => ...) without await. This could lead to unexpected testing behaviour, interleaving multiple act calls and mixing their scopes. You should - await act(async () => ...);"
            ));
          }), {
            then: function(w, re) {
              p = !0, E.then(
                function(ie) {
                  if (H(r, a), a === 0) {
                    try {
                      te(i), B(function() {
                        return F(
                          ie,
                          w,
                          re
                        );
                      });
                    } catch (ot) {
                      T.thrownErrors.push(ot);
                    }
                    if (0 < T.thrownErrors.length) {
                      var nt = G(
                        T.thrownErrors
                      );
                      T.thrownErrors.length = 0, re(nt);
                    }
                  } else w(ie);
                },
                function(ie) {
                  H(r, a), 0 < T.thrownErrors.length && (ie = G(
                    T.thrownErrors
                  ), T.thrownErrors.length = 0), re(ie);
                }
              );
            }
          };
        }
        var P = b;
        if (H(r, a), a === 0 && (te(i), i.length !== 0 && He(function() {
          p || me || (me = !0, console.error(
            "A component suspended inside an `act` scope, but the `act` call was not awaited. When testing React components that depend on asynchronous data, you must await the result:\n\nawait act(() => ...)"
          ));
        }), T.actQueue = null), 0 < T.thrownErrors.length)
          throw e = G(T.thrownErrors), T.thrownErrors.length = 0, e;
        return {
          then: function(w, re) {
            p = !0, a === 0 ? (T.actQueue = i, B(function() {
              return F(
                P,
                w,
                re
              );
            })) : w(P);
          }
        };
      }, c.cache = function(e) {
        return function() {
          return e.apply(null, arguments);
        };
      }, c.cacheSignal = function() {
        return null;
      }, c.captureOwnerStack = function() {
        var e = T.getCurrentStack;
        return e === null ? null : e();
      }, c.cloneElement = function(e, r, a) {
        if (e == null)
          throw Error(
            "The argument must be a React element, but you passed " + e + "."
          );
        var i = Ne({}, e.props), p = e.key, b = e._owner;
        if (r != null) {
          var E;
          e: {
            if (ye.call(r, "ref") && (E = Object.getOwnPropertyDescriptor(
              r,
              "ref"
            ).get) && E.isReactWarning) {
              E = !1;
              break e;
            }
            E = r.ref !== void 0;
          }
          E && (b = y()), x(r) && (O(r.key), p = "" + r.key);
          for (P in r)
            !ye.call(r, P) || P === "key" || P === "__self" || P === "__source" || P === "ref" && r.ref === void 0 || (i[P] = r[P]);
        }
        var P = arguments.length - 2;
        if (P === 1) i.children = a;
        else if (1 < P) {
          E = Array(P);
          for (var w = 0; w < P; w++)
            E[w] = arguments[w + 2];
          i.children = E;
        }
        for (i = Q(
          e.type,
          p,
          i,
          b,
          e._debugStack,
          e._debugTask
        ), p = 2; p < arguments.length; p++)
          Z(arguments[p]);
        return i;
      }, c.createContext = function(e) {
        return e = {
          $$typeof: R,
          _currentValue: e,
          _currentValue2: e,
          _threadCount: 0,
          Provider: null,
          Consumer: null
        }, e.Provider = e, e.Consumer = {
          $$typeof: l,
          _context: e
        }, e._currentRenderer = null, e._currentRenderer2 = null, e;
      }, c.createElement = function(e, r, a) {
        for (var i = 2; i < arguments.length; i++)
          Z(arguments[i]);
        i = {};
        var p = null;
        if (r != null)
          for (w in Me || !("__self" in r) || "key" in r || (Me = !0, console.warn(
            "Your app (or one of its dependencies) is using an outdated JSX transform. Update to the modern JSX transform for faster performance: https://react.dev/link/new-jsx-transform"
          )), x(r) && (O(r.key), p = "" + r.key), r)
            ye.call(r, w) && w !== "key" && w !== "__self" && w !== "__source" && (i[w] = r[w]);
        var b = arguments.length - 2;
        if (b === 1) i.children = a;
        else if (1 < b) {
          for (var E = Array(b), P = 0; P < b; P++)
            E[P] = arguments[P + 2];
          Object.freeze && Object.freeze(E), i.children = E;
        }
        if (e && e.defaultProps)
          for (w in b = e.defaultProps, b)
            i[w] === void 0 && (i[w] = b[w]);
        p && q(
          i,
          typeof e == "function" ? e.displayName || e.name || "Unknown" : e
        );
        var w = 1e4 > T.recentlyCreatedOwnerStacks++;
        return Q(
          e,
          p,
          i,
          y(),
          w ? Error("react-stack-top-frame") : tt,
          w ? je(n(e)) : rt
        );
      }, c.createRef = function() {
        var e = { current: null };
        return Object.seal(e), e;
      }, c.forwardRef = function(e) {
        e != null && e.$$typeof === j ? console.error(
          "forwardRef requires a render function but received a `memo` component. Instead of forwardRef(memo(...)), use memo(forwardRef(...))."
        ) : typeof e != "function" ? console.error(
          "forwardRef requires a render function but was given %s.",
          e === null ? "null" : typeof e
        ) : e.length !== 0 && e.length !== 2 && console.error(
          "forwardRef render functions accept exactly two parameters: props and ref. %s",
          e.length === 1 ? "Did you forget to use the ref parameter?" : "Any additional parameter will be undefined."
        ), e != null && e.defaultProps != null && console.error(
          "forwardRef render functions do not support defaultProps. Did you accidentally pass a React component?"
        );
        var r = { $$typeof: S, render: e }, a;
        return Object.defineProperty(r, "displayName", {
          enumerable: !1,
          configurable: !0,
          get: function() {
            return a;
          },
          set: function(i) {
            a = i, e.name || e.displayName || (Object.defineProperty(e, "name", { value: i }), e.displayName = i);
          }
        }), r;
      }, c.isValidElement = z, c.lazy = function(e) {
        e = { _status: -1, _result: e };
        var r = {
          $$typeof: X,
          _payload: e,
          _init: ue
        }, a = {
          name: "lazy",
          start: -1,
          end: -1,
          value: null,
          owner: null,
          debugStack: Error("react-stack-top-frame"),
          debugTask: console.createTask ? console.createTask("lazy()") : null
        };
        return e._ioInfo = a, r._debugInfo = [{ awaited: a }], r;
      }, c.memo = function(e, r) {
        e == null && console.error(
          "memo: The first argument must be a component. Instead received: %s",
          e === null ? "null" : typeof e
        ), r = {
          $$typeof: j,
          type: e,
          compare: r === void 0 ? null : r
        };
        var a;
        return Object.defineProperty(r, "displayName", {
          enumerable: !1,
          configurable: !0,
          get: function() {
            return a;
          },
          set: function(i) {
            a = i, e.name || e.displayName || (Object.defineProperty(e, "name", { value: i }), e.displayName = i);
          }
        }), r;
      }, c.startTransition = function(e) {
        var r = T.T, a = {};
        a._updatedFibers = /* @__PURE__ */ new Set(), T.T = a;
        try {
          var i = e(), p = T.S;
          p !== null && p(a, i), typeof i == "object" && i !== null && typeof i.then == "function" && (T.asyncTransitions++, i.then(ee, ee), i.then(d, $e));
        } catch (b) {
          $e(b);
        } finally {
          r === null && a._updatedFibers && (e = a._updatedFibers.size, a._updatedFibers.clear(), 10 < e && console.warn(
            "Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."
          )), r !== null && a.types !== null && (r.types !== null && r.types !== a.types && console.error(
            "We expected inner Transitions to have transferred the outer types set and that you cannot add to the outer Transition while inside the inner.This is a bug in React."
          ), r.types = a.types), T.T = r;
        }
      }, c.unstable_useCacheRefresh = function() {
        return N().useCacheRefresh();
      }, c.use = function(e) {
        return N().use(e);
      }, c.useActionState = function(e, r, a) {
        return N().useActionState(
          e,
          r,
          a
        );
      }, c.useCallback = function(e, r) {
        return N().useCallback(e, r);
      }, c.useContext = function(e) {
        var r = N();
        return e.$$typeof === l && console.error(
          "Calling useContext(Context.Consumer) is not supported and will cause bugs. Did you mean to call useContext(Context) instead?"
        ), r.useContext(e);
      }, c.useDebugValue = function(e, r) {
        return N().useDebugValue(e, r);
      }, c.useDeferredValue = function(e, r) {
        return N().useDeferredValue(e, r);
      }, c.useEffect = function(e, r) {
        return e == null && console.warn(
          "React Hook useEffect requires an effect callback. Did you forget to pass a callback to the hook?"
        ), N().useEffect(e, r);
      }, c.useEffectEvent = function(e) {
        return N().useEffectEvent(e);
      }, c.useId = function() {
        return N().useId();
      }, c.useImperativeHandle = function(e, r, a) {
        return N().useImperativeHandle(e, r, a);
      }, c.useInsertionEffect = function(e, r) {
        return e == null && console.warn(
          "React Hook useInsertionEffect requires an effect callback. Did you forget to pass a callback to the hook?"
        ), N().useInsertionEffect(e, r);
      }, c.useLayoutEffect = function(e, r) {
        return e == null && console.warn(
          "React Hook useLayoutEffect requires an effect callback. Did you forget to pass a callback to the hook?"
        ), N().useLayoutEffect(e, r);
      }, c.useMemo = function(e, r) {
        return N().useMemo(e, r);
      }, c.useOptimistic = function(e, r) {
        return N().useOptimistic(e, r);
      }, c.useReducer = function(e, r, a) {
        return N().useReducer(e, r, a);
      }, c.useRef = function(e) {
        return N().useRef(e);
      }, c.useState = function(e) {
        return N().useState(e);
      }, c.useSyncExternalStore = function(e, r, a) {
        return N().useSyncExternalStore(
          e,
          r,
          a
        );
      }, c.useTransition = function() {
        return N().useTransition();
      }, c.version = "19.2.3", typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u" && typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop == "function" && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error());
    })();
  })(pe, pe.exports)), pe.exports;
}
var ze;
function ce() {
  return ze || (ze = 1, process.env.NODE_ENV === "production" ? he.exports = it() : he.exports = ct()), he.exports;
}
var Ge;
function ft() {
  return Ge || (Ge = 1, process.env.NODE_ENV !== "production" && (function() {
    function _(t) {
      if (t == null) return null;
      if (typeof t == "function")
        return t.$$typeof === ue ? null : t.displayName || t.name || null;
      if (typeof t == "string") return t;
      switch (t) {
        case q:
          return "Fragment";
        case Q:
          return "Profiler";
        case V:
          return "StrictMode";
        case oe:
          return "Suspense";
        case k:
          return "SuspenseList";
        case J:
          return "Activity";
      }
      if (typeof t == "object")
        switch (typeof t.tag == "number" && console.error(
          "Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."
        ), t.$$typeof) {
          case x:
            return "Portal";
          case Z:
            return t.displayName || "Context";
          case ne:
            return (t._context.displayName || "Context") + ".Consumer";
          case z:
            var o = t.render;
            return t = t.displayName, t || (t = o.displayName || o.name || "", t = t !== "" ? "ForwardRef(" + t + ")" : "ForwardRef"), t;
          case se:
            return o = t.displayName || null, o !== null ? o : _(t.type) || "Memo";
          case U:
            o = t._payload, t = t._init;
            try {
              return _(t(o));
            } catch {
            }
        }
      return null;
    }
    function c(t) {
      return "" + t;
    }
    function C(t) {
      try {
        c(t);
        var o = !1;
      } catch {
        o = !0;
      }
      if (o) {
        o = console;
        var f = o.error, l = typeof Symbol == "function" && Symbol.toStringTag && t[Symbol.toStringTag] || t.constructor.name || "Object";
        return f.call(
          o,
          "The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",
          l
        ), c(t);
      }
    }
    function h(t) {
      if (t === q) return "<>";
      if (typeof t == "object" && t !== null && t.$$typeof === U)
        return "<...>";
      try {
        var o = _(t);
        return o ? "<" + o + ">" : "<...>";
      } catch {
        return "<...>";
      }
    }
    function m() {
      var t = N.A;
      return t === null ? null : t.getOwner();
    }
    function A() {
      return Error("react-stack-top-frame");
    }
    function M(t) {
      if (ee.call(t, "key")) {
        var o = Object.getOwnPropertyDescriptor(t, "key").get;
        if (o && o.isReactWarning) return !1;
      }
      return t.key !== void 0;
    }
    function I(t, o) {
      function f() {
        H || (H = !0, console.error(
          "%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://react.dev/link/special-props)",
          o
        ));
      }
      f.isReactWarning = !0, Object.defineProperty(t, "key", {
        get: f,
        configurable: !0
      });
    }
    function d() {
      var t = _(this.type);
      return F[t] || (F[t] = !0, console.error(
        "Accessing element.ref was removed in React 19. ref is now a regular prop. It will be removed from the JSX Element type in a future release."
      )), t = this.props.ref, t !== void 0 ? t : null;
    }
    function u(t, o, f, l, R, S) {
      var g = f.ref;
      return t = {
        $$typeof: D,
        type: t,
        key: o,
        props: f,
        _owner: l
      }, (g !== void 0 ? g : null) !== null ? Object.defineProperty(t, "ref", {
        enumerable: !1,
        get: d
      }) : Object.defineProperty(t, "ref", { enumerable: !1, value: null }), t._store = {}, Object.defineProperty(t._store, "validated", {
        configurable: !1,
        enumerable: !1,
        writable: !0,
        value: 0
      }), Object.defineProperty(t, "_debugInfo", {
        configurable: !1,
        enumerable: !1,
        writable: !0,
        value: null
      }), Object.defineProperty(t, "_debugStack", {
        configurable: !1,
        enumerable: !1,
        writable: !0,
        value: R
      }), Object.defineProperty(t, "_debugTask", {
        configurable: !1,
        enumerable: !1,
        writable: !0,
        value: S
      }), Object.freeze && (Object.freeze(t.props), Object.freeze(t)), t;
    }
    function O(t, o, f, l, R, S) {
      var g = o.children;
      if (g !== void 0)
        if (l)
          if (B(g)) {
            for (l = 0; l < g.length; l++)
              s(g[l]);
            Object.freeze && Object.freeze(g);
          } else
            console.error(
              "React.jsx: Static children should always be an array. You are likely explicitly calling React.jsxs or React.jsxDEV. Use the Babel transform instead."
            );
        else s(g);
      if (ee.call(o, "key")) {
        g = _(t);
        var L = Object.keys(o).filter(function(X) {
          return X !== "key";
        });
        l = 0 < L.length ? "{key: someKey, " + L.join(": ..., ") + ": ...}" : "{key: someKey}", ae[g + l] || (L = 0 < L.length ? "{" + L.join(": ..., ") + ": ...}" : "{}", console.error(
          `A props object containing a "key" prop is being spread into JSX:
  let props = %s;
  <%s {...props} />
React keys must be passed directly to JSX without using spread:
  let props = %s;
  <%s key={someKey} {...props} />`,
          l,
          g,
          L,
          g
        ), ae[g + l] = !0);
      }
      if (g = null, f !== void 0 && (C(f), g = "" + f), M(o) && (C(o.key), g = "" + o.key), "key" in o) {
        f = {};
        for (var j in o)
          j !== "key" && (f[j] = o[j]);
      } else f = o;
      return g && I(
        f,
        typeof t == "function" ? t.displayName || t.name || "Unknown" : t
      ), u(
        t,
        g,
        f,
        m(),
        R,
        S
      );
    }
    function s(t) {
      n(t) ? t._store && (t._store.validated = 1) : typeof t == "object" && t !== null && t.$$typeof === U && (t._payload.status === "fulfilled" ? n(t._payload.value) && t._payload.value._store && (t._payload.value._store.validated = 1) : t._store && (t._store.validated = 1));
    }
    function n(t) {
      return typeof t == "object" && t !== null && t.$$typeof === D;
    }
    var y = ce(), D = Symbol.for("react.transitional.element"), x = Symbol.for("react.portal"), q = Symbol.for("react.fragment"), V = Symbol.for("react.strict_mode"), Q = Symbol.for("react.profiler"), ne = Symbol.for("react.consumer"), Z = Symbol.for("react.context"), z = Symbol.for("react.forward_ref"), oe = Symbol.for("react.suspense"), k = Symbol.for("react.suspense_list"), se = Symbol.for("react.memo"), U = Symbol.for("react.lazy"), J = Symbol.for("react.activity"), ue = Symbol.for("react.client.reference"), N = y.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE, ee = Object.prototype.hasOwnProperty, B = Array.isArray, G = console.createTask ? console.createTask : function() {
      return null;
    };
    y = {
      react_stack_bottom_frame: function(t) {
        return t();
      }
    };
    var H, F = {}, te = y.react_stack_bottom_frame.bind(
      y,
      A
    )(), K = G(h(A)), ae = {};
    de.Fragment = q, de.jsx = function(t, o, f) {
      var l = 1e4 > N.recentlyCreatedOwnerStacks++;
      return O(
        t,
        o,
        f,
        !1,
        l ? Error("react-stack-top-frame") : te,
        l ? G(h(t)) : K
      );
    }, de.jsxs = function(t, o, f) {
      var l = 1e4 > N.recentlyCreatedOwnerStacks++;
      return O(
        t,
        o,
        f,
        !0,
        l ? Error("react-stack-top-frame") : te,
        l ? G(h(t)) : K
      );
    };
  })()), de;
}
var Fe;
function lt() {
  return Fe || (Fe = 1, process.env.NODE_ENV === "production" ? ve.exports = st() : ve.exports = ft()), ve.exports;
}
var Et = lt(), Je = ce();
const dt = /* @__PURE__ */ at(Je), Rt = /* @__PURE__ */ ut({
  __proto__: null,
  default: dt
}, [Je]);
var Ee = { exports: {} }, Y = {};
var Ve;
function pt() {
  if (Ve) return Y;
  Ve = 1;
  var _ = ce();
  function c(d) {
    var u = "https://react.dev/errors/" + d;
    if (1 < arguments.length) {
      u += "?args[]=" + encodeURIComponent(arguments[1]);
      for (var O = 2; O < arguments.length; O++)
        u += "&args[]=" + encodeURIComponent(arguments[O]);
    }
    return "Minified React error #" + d + "; visit " + u + " for the full message or use the non-minified dev environment for full errors and additional helpful warnings.";
  }
  function C() {
  }
  var h = {
    d: {
      f: C,
      r: function() {
        throw Error(c(522));
      },
      D: C,
      C,
      L: C,
      m: C,
      X: C,
      S: C,
      M: C
    },
    p: 0,
    findDOMNode: null
  }, m = Symbol.for("react.portal");
  function A(d, u, O) {
    var s = 3 < arguments.length && arguments[3] !== void 0 ? arguments[3] : null;
    return {
      $$typeof: m,
      key: s == null ? null : "" + s,
      children: d,
      containerInfo: u,
      implementation: O
    };
  }
  var M = _.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;
  function I(d, u) {
    if (d === "font") return "";
    if (typeof u == "string")
      return u === "use-credentials" ? u : "";
  }
  return Y.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = h, Y.createPortal = function(d, u) {
    var O = 2 < arguments.length && arguments[2] !== void 0 ? arguments[2] : null;
    if (!u || u.nodeType !== 1 && u.nodeType !== 9 && u.nodeType !== 11)
      throw Error(c(299));
    return A(d, u, null, O);
  }, Y.flushSync = function(d) {
    var u = M.T, O = h.p;
    try {
      if (M.T = null, h.p = 2, d) return d();
    } finally {
      M.T = u, h.p = O, h.d.f();
    }
  }, Y.preconnect = function(d, u) {
    typeof d == "string" && (u ? (u = u.crossOrigin, u = typeof u == "string" ? u === "use-credentials" ? u : "" : void 0) : u = null, h.d.C(d, u));
  }, Y.prefetchDNS = function(d) {
    typeof d == "string" && h.d.D(d);
  }, Y.preinit = function(d, u) {
    if (typeof d == "string" && u && typeof u.as == "string") {
      var O = u.as, s = I(O, u.crossOrigin), n = typeof u.integrity == "string" ? u.integrity : void 0, y = typeof u.fetchPriority == "string" ? u.fetchPriority : void 0;
      O === "style" ? h.d.S(
        d,
        typeof u.precedence == "string" ? u.precedence : void 0,
        {
          crossOrigin: s,
          integrity: n,
          fetchPriority: y
        }
      ) : O === "script" && h.d.X(d, {
        crossOrigin: s,
        integrity: n,
        fetchPriority: y,
        nonce: typeof u.nonce == "string" ? u.nonce : void 0
      });
    }
  }, Y.preinitModule = function(d, u) {
    if (typeof d == "string")
      if (typeof u == "object" && u !== null) {
        if (u.as == null || u.as === "script") {
          var O = I(
            u.as,
            u.crossOrigin
          );
          h.d.M(d, {
            crossOrigin: O,
            integrity: typeof u.integrity == "string" ? u.integrity : void 0,
            nonce: typeof u.nonce == "string" ? u.nonce : void 0
          });
        }
      } else u == null && h.d.M(d);
  }, Y.preload = function(d, u) {
    if (typeof d == "string" && typeof u == "object" && u !== null && typeof u.as == "string") {
      var O = u.as, s = I(O, u.crossOrigin);
      h.d.L(d, O, {
        crossOrigin: s,
        integrity: typeof u.integrity == "string" ? u.integrity : void 0,
        nonce: typeof u.nonce == "string" ? u.nonce : void 0,
        type: typeof u.type == "string" ? u.type : void 0,
        fetchPriority: typeof u.fetchPriority == "string" ? u.fetchPriority : void 0,
        referrerPolicy: typeof u.referrerPolicy == "string" ? u.referrerPolicy : void 0,
        imageSrcSet: typeof u.imageSrcSet == "string" ? u.imageSrcSet : void 0,
        imageSizes: typeof u.imageSizes == "string" ? u.imageSizes : void 0,
        media: typeof u.media == "string" ? u.media : void 0
      });
    }
  }, Y.preloadModule = function(d, u) {
    if (typeof d == "string")
      if (u) {
        var O = I(u.as, u.crossOrigin);
        h.d.m(d, {
          as: typeof u.as == "string" && u.as !== "script" ? u.as : void 0,
          crossOrigin: O,
          integrity: typeof u.integrity == "string" ? u.integrity : void 0
        });
      } else h.d.m(d);
  }, Y.requestFormReset = function(d) {
    h.d.r(d);
  }, Y.unstable_batchedUpdates = function(d, u) {
    return d(u);
  }, Y.useFormState = function(d, u, O) {
    return M.H.useFormState(d, u, O);
  }, Y.useFormStatus = function() {
    return M.H.useHostTransitionStatus();
  }, Y.version = "19.2.3", Y;
}
var $ = {};
var Be;
function yt() {
  return Be || (Be = 1, process.env.NODE_ENV !== "production" && (function() {
    function _() {
    }
    function c(s) {
      return "" + s;
    }
    function C(s, n, y) {
      var D = 3 < arguments.length && arguments[3] !== void 0 ? arguments[3] : null;
      try {
        c(D);
        var x = !1;
      } catch {
        x = !0;
      }
      return x && (console.error(
        "The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",
        typeof Symbol == "function" && Symbol.toStringTag && D[Symbol.toStringTag] || D.constructor.name || "Object"
      ), c(D)), {
        $$typeof: u,
        key: D == null ? null : "" + D,
        children: s,
        containerInfo: n,
        implementation: y
      };
    }
    function h(s, n) {
      if (s === "font") return "";
      if (typeof n == "string")
        return n === "use-credentials" ? n : "";
    }
    function m(s) {
      return s === null ? "`null`" : s === void 0 ? "`undefined`" : s === "" ? "an empty string" : 'something with type "' + typeof s + '"';
    }
    function A(s) {
      return s === null ? "`null`" : s === void 0 ? "`undefined`" : s === "" ? "an empty string" : typeof s == "string" ? JSON.stringify(s) : typeof s == "number" ? "`" + s + "`" : 'something with type "' + typeof s + '"';
    }
    function M() {
      var s = O.H;
      return s === null && console.error(
        `Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`
      ), s;
    }
    typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u" && typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart == "function" && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());
    var I = ce(), d = {
      d: {
        f: _,
        r: function() {
          throw Error(
            "Invalid form element. requestFormReset must be passed a form that was rendered by React."
          );
        },
        D: _,
        C: _,
        L: _,
        m: _,
        X: _,
        S: _,
        M: _
      },
      p: 0,
      findDOMNode: null
    }, u = Symbol.for("react.portal"), O = I.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;
    typeof Map == "function" && Map.prototype != null && typeof Map.prototype.forEach == "function" && typeof Set == "function" && Set.prototype != null && typeof Set.prototype.clear == "function" && typeof Set.prototype.forEach == "function" || console.error(
      "React depends on Map and Set built-in types. Make sure that you load a polyfill in older browsers. https://reactjs.org/link/react-polyfills"
    ), $.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = d, $.createPortal = function(s, n) {
      var y = 2 < arguments.length && arguments[2] !== void 0 ? arguments[2] : null;
      if (!n || n.nodeType !== 1 && n.nodeType !== 9 && n.nodeType !== 11)
        throw Error("Target container is not a DOM element.");
      return C(s, n, null, y);
    }, $.flushSync = function(s) {
      var n = O.T, y = d.p;
      try {
        if (O.T = null, d.p = 2, s)
          return s();
      } finally {
        O.T = n, d.p = y, d.d.f() && console.error(
          "flushSync was called from inside a lifecycle method. React cannot flush when React is already rendering. Consider moving this call to a scheduler task or micro task."
        );
      }
    }, $.preconnect = function(s, n) {
      typeof s == "string" && s ? n != null && typeof n != "object" ? console.error(
        "ReactDOM.preconnect(): Expected the `options` argument (second) to be an object but encountered %s instead. The only supported option at this time is `crossOrigin` which accepts a string.",
        A(n)
      ) : n != null && typeof n.crossOrigin != "string" && console.error(
        "ReactDOM.preconnect(): Expected the `crossOrigin` option (second argument) to be a string but encountered %s instead. Try removing this option or passing a string value instead.",
        m(n.crossOrigin)
      ) : console.error(
        "ReactDOM.preconnect(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",
        m(s)
      ), typeof s == "string" && (n ? (n = n.crossOrigin, n = typeof n == "string" ? n === "use-credentials" ? n : "" : void 0) : n = null, d.d.C(s, n));
    }, $.prefetchDNS = function(s) {
      if (typeof s != "string" || !s)
        console.error(
          "ReactDOM.prefetchDNS(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",
          m(s)
        );
      else if (1 < arguments.length) {
        var n = arguments[1];
        typeof n == "object" && n.hasOwnProperty("crossOrigin") ? console.error(
          "ReactDOM.prefetchDNS(): Expected only one argument, `href`, but encountered %s as a second argument instead. This argument is reserved for future options and is currently disallowed. It looks like the you are attempting to set a crossOrigin property for this DNS lookup hint. Browsers do not perform DNS queries using CORS and setting this attribute on the resource hint has no effect. Try calling ReactDOM.prefetchDNS() with just a single string argument, `href`.",
          A(n)
        ) : console.error(
          "ReactDOM.prefetchDNS(): Expected only one argument, `href`, but encountered %s as a second argument instead. This argument is reserved for future options and is currently disallowed. Try calling ReactDOM.prefetchDNS() with just a single string argument, `href`.",
          A(n)
        );
      }
      typeof s == "string" && d.d.D(s);
    }, $.preinit = function(s, n) {
      if (typeof s == "string" && s ? n == null || typeof n != "object" ? console.error(
        "ReactDOM.preinit(): Expected the `options` argument (second) to be an object with an `as` property describing the type of resource to be preinitialized but encountered %s instead.",
        A(n)
      ) : n.as !== "style" && n.as !== "script" && console.error(
        'ReactDOM.preinit(): Expected the `as` property in the `options` argument (second) to contain a valid value describing the type of resource to be preinitialized but encountered %s instead. Valid values for `as` are "style" and "script".',
        A(n.as)
      ) : console.error(
        "ReactDOM.preinit(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",
        m(s)
      ), typeof s == "string" && n && typeof n.as == "string") {
        var y = n.as, D = h(y, n.crossOrigin), x = typeof n.integrity == "string" ? n.integrity : void 0, q = typeof n.fetchPriority == "string" ? n.fetchPriority : void 0;
        y === "style" ? d.d.S(
          s,
          typeof n.precedence == "string" ? n.precedence : void 0,
          {
            crossOrigin: D,
            integrity: x,
            fetchPriority: q
          }
        ) : y === "script" && d.d.X(s, {
          crossOrigin: D,
          integrity: x,
          fetchPriority: q,
          nonce: typeof n.nonce == "string" ? n.nonce : void 0
        });
      }
    }, $.preinitModule = function(s, n) {
      var y = "";
      if (typeof s == "string" && s || (y += " The `href` argument encountered was " + m(s) + "."), n !== void 0 && typeof n != "object" ? y += " The `options` argument encountered was " + m(n) + "." : n && "as" in n && n.as !== "script" && (y += " The `as` option encountered was " + A(n.as) + "."), y)
        console.error(
          "ReactDOM.preinitModule(): Expected up to two arguments, a non-empty `href` string and, optionally, an `options` object with a valid `as` property.%s",
          y
        );
      else
        switch (y = n && typeof n.as == "string" ? n.as : "script", y) {
          case "script":
            break;
          default:
            y = A(y), console.error(
              'ReactDOM.preinitModule(): Currently the only supported "as" type for this function is "script" but received "%s" instead. This warning was generated for `href` "%s". In the future other module types will be supported, aligning with the import-attributes proposal. Learn more here: (https://github.com/tc39/proposal-import-attributes)',
              y,
              s
            );
        }
      typeof s == "string" && (typeof n == "object" && n !== null ? (n.as == null || n.as === "script") && (y = h(
        n.as,
        n.crossOrigin
      ), d.d.M(s, {
        crossOrigin: y,
        integrity: typeof n.integrity == "string" ? n.integrity : void 0,
        nonce: typeof n.nonce == "string" ? n.nonce : void 0
      })) : n == null && d.d.M(s));
    }, $.preload = function(s, n) {
      var y = "";
      if (typeof s == "string" && s || (y += " The `href` argument encountered was " + m(s) + "."), n == null || typeof n != "object" ? y += " The `options` argument encountered was " + m(n) + "." : typeof n.as == "string" && n.as || (y += " The `as` option encountered was " + m(n.as) + "."), y && console.error(
        'ReactDOM.preload(): Expected two arguments, a non-empty `href` string and an `options` object with an `as` property valid for a `<link rel="preload" as="..." />` tag.%s',
        y
      ), typeof s == "string" && typeof n == "object" && n !== null && typeof n.as == "string") {
        y = n.as;
        var D = h(
          y,
          n.crossOrigin
        );
        d.d.L(s, y, {
          crossOrigin: D,
          integrity: typeof n.integrity == "string" ? n.integrity : void 0,
          nonce: typeof n.nonce == "string" ? n.nonce : void 0,
          type: typeof n.type == "string" ? n.type : void 0,
          fetchPriority: typeof n.fetchPriority == "string" ? n.fetchPriority : void 0,
          referrerPolicy: typeof n.referrerPolicy == "string" ? n.referrerPolicy : void 0,
          imageSrcSet: typeof n.imageSrcSet == "string" ? n.imageSrcSet : void 0,
          imageSizes: typeof n.imageSizes == "string" ? n.imageSizes : void 0,
          media: typeof n.media == "string" ? n.media : void 0
        });
      }
    }, $.preloadModule = function(s, n) {
      var y = "";
      typeof s == "string" && s || (y += " The `href` argument encountered was " + m(s) + "."), n !== void 0 && typeof n != "object" ? y += " The `options` argument encountered was " + m(n) + "." : n && "as" in n && typeof n.as != "string" && (y += " The `as` option encountered was " + m(n.as) + "."), y && console.error(
        'ReactDOM.preloadModule(): Expected two arguments, a non-empty `href` string and, optionally, an `options` object with an `as` property valid for a `<link rel="modulepreload" as="..." />` tag.%s',
        y
      ), typeof s == "string" && (n ? (y = h(
        n.as,
        n.crossOrigin
      ), d.d.m(s, {
        as: typeof n.as == "string" && n.as !== "script" ? n.as : void 0,
        crossOrigin: y,
        integrity: typeof n.integrity == "string" ? n.integrity : void 0
      })) : d.d.m(s));
    }, $.requestFormReset = function(s) {
      d.d.r(s);
    }, $.unstable_batchedUpdates = function(s, n) {
      return s(n);
    }, $.useFormState = function(s, n, y) {
      return M().useFormState(s, n, y);
    }, $.useFormStatus = function() {
      return M().useHostTransitionStatus();
    }, $.version = "19.2.3", typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u" && typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop == "function" && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error());
  })()), $;
}
var Ke;
function Ot() {
  if (Ke) return Ee.exports;
  Ke = 1;
  function _() {
    if (!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ > "u" || typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE != "function")) {
      if (process.env.NODE_ENV !== "production")
        throw new Error("^_^");
      try {
        __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(_);
      } catch (c) {
        console.error(c);
      }
    }
  }
  return process.env.NODE_ENV === "production" ? (_(), Ee.exports = pt()) : Ee.exports = yt(), Ee.exports;
}
var Re = { exports: {} }, be = {};
var Xe;
function _t() {
  if (Xe) return be;
  Xe = 1;
  var _ = ce().__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;
  return be.c = function(c) {
    return _.H.useMemoCache(c);
  }, be;
}
var we = {};
var Qe;
function gt() {
  return Qe || (Qe = 1, process.env.NODE_ENV !== "production" && (function() {
    var _ = ce().__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;
    we.c = function(c) {
      var C = _.H;
      return C === null && console.error(
        `Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`
      ), C.useMemoCache(c);
    };
  })()), we;
}
var Ze;
function mt() {
  return Ze || (Ze = 1, process.env.NODE_ENV === "production" ? Re.exports = _t() : Re.exports = gt()), Re.exports;
}
var Tt = mt();
export {
  dt as U,
  ce as a,
  Ot as b,
  vt as c,
  Tt as d,
  at as e,
  ht as g,
  Et as j,
  Je as r,
  Rt as t
};
