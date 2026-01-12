function tt(c) {
  return c && c.__esModule && Object.prototype.hasOwnProperty.call(c, "default") ? c.default : c;
}
var Re = { exports: {} }, R = {};
var Fe;
function it() {
  if (Fe) return R;
  Fe = 1;
  var c = Symbol.for("react.transitional.element"), a = Symbol.for("react.portal"), m = Symbol.for("react.fragment"), v = Symbol.for("react.strict_mode"), T = Symbol.for("react.profiler"), E = Symbol.for("react.consumer"), M = Symbol.for("react.context"), j = Symbol.for("react.forward_ref"), f = Symbol.for("react.suspense"), u = Symbol.for("react.memo"), h = Symbol.for("react.lazy"), s = Symbol.for("react.activity"), n = Symbol.iterator;
  function d(t) {
    return t === null || typeof t != "object" ? null : (t = n && t[n] || t["@@iterator"], typeof t == "function" ? t : null);
  }
  var b = {
    isMounted: function() {
      return !1;
    },
    enqueueForceUpdate: function() {
    },
    enqueueReplaceState: function() {
    },
    enqueueSetState: function() {
    }
  }, L = Object.assign, $ = {};
  function I(t, o, p) {
    this.props = t, this.context = o, this.refs = $, this.updater = p || b;
  }
  I.prototype.isReactComponent = {}, I.prototype.setState = function(t, o) {
    if (typeof t != "object" && typeof t != "function" && t != null)
      throw Error(
        "takes an object of state variables to update or a function which returns an object of state variables."
      );
    this.updater.enqueueSetState(this, t, o, "setState");
  }, I.prototype.forceUpdate = function(t) {
    this.updater.enqueueForceUpdate(this, t, "forceUpdate");
  };
  function U() {
  }
  U.prototype = I.prototype;
  function z(t, o, p) {
    this.props = t, this.context = o, this.refs = $, this.updater = p || b;
  }
  var Q = z.prototype = new U();
  Q.constructor = z, L(Q, I.prototype), Q.isPureReactComponent = !0;
  var V = Array.isArray;
  function ue() {
  }
  var P = { H: null, A: null, T: null, S: null }, ce = Object.prototype.hasOwnProperty;
  function W(t, o, p) {
    var y = p.ref;
    return {
      $$typeof: c,
      type: t,
      key: o,
      ref: y !== void 0 ? y : null,
      props: p
    };
  }
  function te(t, o) {
    return W(t.type, o, t.props);
  }
  function se(t) {
    return typeof t == "object" && t !== null && t.$$typeof === c;
  }
  function N(t) {
    var o = { "=": "=0", ":": "=2" };
    return "$" + t.replace(/[=:]/g, function(p) {
      return o[p];
    });
  }
  var re = /\/+/g;
  function J(t, o) {
    return typeof t == "object" && t !== null && t.key != null ? N("" + t.key) : o.toString(36);
  }
  function K(t) {
    switch (t.status) {
      case "fulfilled":
        return t.value;
      case "rejected":
        throw t.reason;
      default:
        switch (typeof t.status == "string" ? t.then(ue, ue) : (t.status = "pending", t.then(
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
  function G(t, o, p, y, w) {
    var k = typeof t;
    (k === "undefined" || k === "boolean") && (t = null);
    var _ = !1;
    if (t === null) _ = !0;
    else
      switch (k) {
        case "bigint":
        case "string":
        case "number":
          _ = !0;
          break;
        case "object":
          switch (t.$$typeof) {
            case c:
            case a:
              _ = !0;
              break;
            case h:
              return _ = t._init, G(
                _(t._payload),
                o,
                p,
                y,
                w
              );
          }
      }
    if (_)
      return w = w(t), _ = y === "" ? "." + J(t, 0) : y, V(w) ? (p = "", _ != null && (p = _.replace(re, "$&/") + "/"), G(w, o, p, "", function(ee) {
        return ee;
      })) : w != null && (se(w) && (w = te(
        w,
        p + (w.key == null || t && t.key === w.key ? "" : ("" + w.key).replace(
          re,
          "$&/"
        ) + "/") + _
      )), o.push(w)), 1;
    _ = 0;
    var H = y === "" ? "." : y + ":";
    if (V(t))
      for (var Y = 0; Y < t.length; Y++)
        y = t[Y], k = H + J(y, Y), _ += G(
          y,
          o,
          p,
          k,
          w
        );
    else if (Y = d(t), typeof Y == "function")
      for (t = Y.call(t), Y = 0; !(y = t.next()).done; )
        y = y.value, k = H + J(y, Y++), _ += G(
          y,
          o,
          p,
          k,
          w
        );
    else if (k === "object") {
      if (typeof t.then == "function")
        return G(
          K(t),
          o,
          p,
          y,
          w
        );
      throw o = String(t), Error(
        "Objects are not valid as a React child (found: " + (o === "[object Object]" ? "object with keys {" + Object.keys(t).join(", ") + "}" : o) + "). If you meant to render a collection of children, use an array instead."
      );
    }
    return _;
  }
  function X(t, o, p) {
    if (t == null) return t;
    var y = [], w = 0;
    return G(t, y, "", "", function(k) {
      return o.call(p, k, w++);
    }), y;
  }
  function ne(t) {
    if (t._status === -1) {
      var o = t._result;
      o = o(), o.then(
        function(p) {
          (t._status === 0 || t._status === -1) && (t._status = 1, t._result = p);
        },
        function(p) {
          (t._status === 0 || t._status === -1) && (t._status = 2, t._result = p);
        }
      ), t._status === -1 && (t._status = 0, t._result = o);
    }
    if (t._status === 1) return t._result.default;
    throw t._result;
  }
  var Z = typeof reportError == "function" ? reportError : function(t) {
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
    map: X,
    forEach: function(t, o, p) {
      X(
        t,
        function() {
          o.apply(this, arguments);
        },
        p
      );
    },
    count: function(t) {
      var o = 0;
      return X(t, function() {
        o++;
      }), o;
    },
    toArray: function(t) {
      return X(t, function(o) {
        return o;
      }) || [];
    },
    only: function(t) {
      if (!se(t))
        throw Error(
          "React.Children.only expected to receive a single React element child."
        );
      return t;
    }
  };
  return R.Activity = s, R.Children = ae, R.Component = I, R.Fragment = m, R.Profiler = T, R.PureComponent = z, R.StrictMode = v, R.Suspense = f, R.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = P, R.__COMPILER_RUNTIME = {
    __proto__: null,
    c: function(t) {
      return P.H.useMemoCache(t);
    }
  }, R.cache = function(t) {
    return function() {
      return t.apply(null, arguments);
    };
  }, R.cacheSignal = function() {
    return null;
  }, R.cloneElement = function(t, o, p) {
    if (t == null)
      throw Error(
        "The argument must be a React element, but you passed " + t + "."
      );
    var y = L({}, t.props), w = t.key;
    if (o != null)
      for (k in o.key !== void 0 && (w = "" + o.key), o)
        !ce.call(o, k) || k === "key" || k === "__self" || k === "__source" || k === "ref" && o.ref === void 0 || (y[k] = o[k]);
    var k = arguments.length - 2;
    if (k === 1) y.children = p;
    else if (1 < k) {
      for (var _ = Array(k), H = 0; H < k; H++)
        _[H] = arguments[H + 2];
      y.children = _;
    }
    return W(t.type, w, y);
  }, R.createContext = function(t) {
    return t = {
      $$typeof: M,
      _currentValue: t,
      _currentValue2: t,
      _threadCount: 0,
      Provider: null,
      Consumer: null
    }, t.Provider = t, t.Consumer = {
      $$typeof: E,
      _context: t
    }, t;
  }, R.createElement = function(t, o, p) {
    var y, w = {}, k = null;
    if (o != null)
      for (y in o.key !== void 0 && (k = "" + o.key), o)
        ce.call(o, y) && y !== "key" && y !== "__self" && y !== "__source" && (w[y] = o[y]);
    var _ = arguments.length - 2;
    if (_ === 1) w.children = p;
    else if (1 < _) {
      for (var H = Array(_), Y = 0; Y < _; Y++)
        H[Y] = arguments[Y + 2];
      w.children = H;
    }
    if (t && t.defaultProps)
      for (y in _ = t.defaultProps, _)
        w[y] === void 0 && (w[y] = _[y]);
    return W(t, k, w);
  }, R.createRef = function() {
    return { current: null };
  }, R.forwardRef = function(t) {
    return { $$typeof: j, render: t };
  }, R.isValidElement = se, R.lazy = function(t) {
    return {
      $$typeof: h,
      _payload: { _status: -1, _result: t },
      _init: ne
    };
  }, R.memo = function(t, o) {
    return {
      $$typeof: u,
      type: t,
      compare: o === void 0 ? null : o
    };
  }, R.startTransition = function(t) {
    var o = P.T, p = {};
    P.T = p;
    try {
      var y = t(), w = P.S;
      w !== null && w(p, y), typeof y == "object" && y !== null && typeof y.then == "function" && y.then(ue, Z);
    } catch (k) {
      Z(k);
    } finally {
      o !== null && p.types !== null && (o.types = p.types), P.T = o;
    }
  }, R.unstable_useCacheRefresh = function() {
    return P.H.useCacheRefresh();
  }, R.use = function(t) {
    return P.H.use(t);
  }, R.useActionState = function(t, o, p) {
    return P.H.useActionState(t, o, p);
  }, R.useCallback = function(t, o) {
    return P.H.useCallback(t, o);
  }, R.useContext = function(t) {
    return P.H.useContext(t);
  }, R.useDebugValue = function() {
  }, R.useDeferredValue = function(t, o) {
    return P.H.useDeferredValue(t, o);
  }, R.useEffect = function(t, o) {
    return P.H.useEffect(t, o);
  }, R.useEffectEvent = function(t) {
    return P.H.useEffectEvent(t);
  }, R.useId = function() {
    return P.H.useId();
  }, R.useImperativeHandle = function(t, o, p) {
    return P.H.useImperativeHandle(t, o, p);
  }, R.useInsertionEffect = function(t, o) {
    return P.H.useInsertionEffect(t, o);
  }, R.useLayoutEffect = function(t, o) {
    return P.H.useLayoutEffect(t, o);
  }, R.useMemo = function(t, o) {
    return P.H.useMemo(t, o);
  }, R.useOptimistic = function(t, o) {
    return P.H.useOptimistic(t, o);
  }, R.useReducer = function(t, o, p) {
    return P.H.useReducer(t, o, p);
  }, R.useRef = function(t) {
    return P.H.useRef(t);
  }, R.useState = function(t) {
    return P.H.useState(t);
  }, R.useSyncExternalStore = function(t, o, p) {
    return P.H.useSyncExternalStore(
      t,
      o,
      p
    );
  }, R.useTransition = function() {
    return P.H.useTransition();
  }, R.version = "19.2.3", R;
}
var me = { exports: {} };
me.exports;
var Be;
function ct() {
  return Be || (Be = 1, (function(c, a) {
    process.env.NODE_ENV !== "production" && (function() {
      function m(e, r) {
        Object.defineProperty(E.prototype, e, {
          get: function() {
            console.warn(
              "%s(...) is deprecated in plain JavaScript React classes. %s",
              r[0],
              r[1]
            );
          }
        });
      }
      function v(e) {
        return e === null || typeof e != "object" ? null : (e = Ne && e[Ne] || e["@@iterator"], typeof e == "function" ? e : null);
      }
      function T(e, r) {
        e = (e = e.constructor) && (e.displayName || e.name) || "ReactClass";
        var i = e + "." + r;
        De[i] || (console.error(
          "Can't call %s on a component that is not yet mounted. This is a no-op, but it might indicate a bug in your application. Instead, assign to `this.state` directly or define a `state = {};` class property with the desired state in the %s component.",
          r,
          e
        ), De[i] = !0);
      }
      function E(e, r, i) {
        this.props = e, this.context = r, this.refs = Ce, this.updater = i || Me;
      }
      function M() {
      }
      function j(e, r, i) {
        this.props = e, this.context = r, this.refs = Ce, this.updater = i || Me;
      }
      function f() {
      }
      function u(e) {
        return "" + e;
      }
      function h(e) {
        try {
          u(e);
          var r = !1;
        } catch {
          r = !0;
        }
        if (r) {
          r = console;
          var i = r.error, l = typeof Symbol == "function" && Symbol.toStringTag && e[Symbol.toStringTag] || e.constructor.name || "Object";
          return i.call(
            r,
            "The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",
            l
          ), u(e);
        }
      }
      function s(e) {
        if (e == null) return null;
        if (typeof e == "function")
          return e.$$typeof === nt ? null : e.displayName || e.name || null;
        if (typeof e == "string") return e;
        switch (e) {
          case t:
            return "Fragment";
          case p:
            return "Profiler";
          case o:
            return "StrictMode";
          case _:
            return "Suspense";
          case H:
            return "SuspenseList";
          case je:
            return "Activity";
        }
        if (typeof e == "object")
          switch (typeof e.tag == "number" && console.error(
            "Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."
          ), e.$$typeof) {
            case ae:
              return "Portal";
            case w:
              return e.displayName || "Context";
            case y:
              return (e._context.displayName || "Context") + ".Consumer";
            case k:
              var r = e.render;
              return e = e.displayName, e || (e = r.displayName || r.name || "", e = e !== "" ? "ForwardRef(" + e + ")" : "ForwardRef"), e;
            case Y:
              return r = e.displayName || null, r !== null ? r : s(e.type) || "Memo";
            case ee:
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
        if (typeof e == "object" && e !== null && e.$$typeof === ee)
          return "<...>";
        try {
          var r = s(e);
          return r ? "<" + r + ">" : "<...>";
        } catch {
          return "<...>";
        }
      }
      function d() {
        var e = S.A;
        return e === null ? null : e.getOwner();
      }
      function b() {
        return Error("react-stack-top-frame");
      }
      function L(e) {
        if (_e.call(e, "key")) {
          var r = Object.getOwnPropertyDescriptor(e, "key").get;
          if (r && r.isReactWarning) return !1;
        }
        return e.key !== void 0;
      }
      function $(e, r) {
        function i() {
          Ye || (Ye = !0, console.error(
            "%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://react.dev/link/special-props)",
            r
          ));
        }
        i.isReactWarning = !0, Object.defineProperty(e, "key", {
          get: i,
          configurable: !0
        });
      }
      function I() {
        var e = s(this.type);
        return He[e] || (He[e] = !0, console.error(
          "Accessing element.ref was removed in React 19. ref is now a regular prop. It will be removed from the JSX Element type in a future release."
        )), e = this.props.ref, e !== void 0 ? e : null;
      }
      function U(e, r, i, l, g, A) {
        var O = i.ref;
        return e = {
          $$typeof: Z,
          type: e,
          key: r,
          props: i,
          _owner: l
        }, (O !== void 0 ? O : null) !== null ? Object.defineProperty(e, "ref", {
          enumerable: !1,
          get: I
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
          value: g
        }), Object.defineProperty(e, "_debugTask", {
          configurable: !1,
          enumerable: !1,
          writable: !0,
          value: A
        }), Object.freeze && (Object.freeze(e.props), Object.freeze(e)), e;
      }
      function z(e, r) {
        return r = U(
          e.type,
          r,
          e.props,
          e._owner,
          e._debugStack,
          e._debugTask
        ), e._store && (r._store.validated = e._store.validated), r;
      }
      function Q(e) {
        V(e) ? e._store && (e._store.validated = 1) : typeof e == "object" && e !== null && e.$$typeof === ee && (e._payload.status === "fulfilled" ? V(e._payload.value) && e._payload.value._store && (e._payload.value._store.validated = 1) : e._store && (e._store.validated = 1));
      }
      function V(e) {
        return typeof e == "object" && e !== null && e.$$typeof === Z;
      }
      function ue(e) {
        var r = { "=": "=0", ":": "=2" };
        return "$" + e.replace(/[=:]/g, function(i) {
          return r[i];
        });
      }
      function P(e, r) {
        return typeof e == "object" && e !== null && e.key != null ? (h(e.key), ue("" + e.key)) : r.toString(36);
      }
      function ce(e) {
        switch (e.status) {
          case "fulfilled":
            return e.value;
          case "rejected":
            throw e.reason;
          default:
            switch (typeof e.status == "string" ? e.then(f, f) : (e.status = "pending", e.then(
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
      function W(e, r, i, l, g) {
        var A = typeof e;
        (A === "undefined" || A === "boolean") && (e = null);
        var O = !1;
        if (e === null) O = !0;
        else
          switch (A) {
            case "bigint":
            case "string":
            case "number":
              O = !0;
              break;
            case "object":
              switch (e.$$typeof) {
                case Z:
                case ae:
                  O = !0;
                  break;
                case ee:
                  return O = e._init, W(
                    O(e._payload),
                    r,
                    i,
                    l,
                    g
                  );
              }
          }
        if (O) {
          O = e, g = g(O);
          var D = l === "" ? "." + P(O, 0) : l;
          return $e(g) ? (i = "", D != null && (i = D.replace(qe, "$&/") + "/"), W(g, r, i, "", function(oe) {
            return oe;
          })) : g != null && (V(g) && (g.key != null && (O && O.key === g.key || h(g.key)), i = z(
            g,
            i + (g.key == null || O && O.key === g.key ? "" : ("" + g.key).replace(
              qe,
              "$&/"
            ) + "/") + D
          ), l !== "" && O != null && V(O) && O.key == null && O._store && !O._store.validated && (i._store.validated = 2), g = i), r.push(g)), 1;
        }
        if (O = 0, D = l === "" ? "." : l + ":", $e(e))
          for (var C = 0; C < e.length; C++)
            l = e[C], A = D + P(l, C), O += W(
              l,
              r,
              i,
              A,
              g
            );
        else if (C = v(e), typeof C == "function")
          for (C === e.entries && (xe || console.warn(
            "Using Maps as children is not supported. Use an array of keyed ReactElements instead."
          ), xe = !0), e = C.call(e), C = 0; !(l = e.next()).done; )
            l = l.value, A = D + P(l, C++), O += W(
              l,
              r,
              i,
              A,
              g
            );
        else if (A === "object") {
          if (typeof e.then == "function")
            return W(
              ce(e),
              r,
              i,
              l,
              g
            );
          throw r = String(e), Error(
            "Objects are not valid as a React child (found: " + (r === "[object Object]" ? "object with keys {" + Object.keys(e).join(", ") + "}" : r) + "). If you meant to render a collection of children, use an array instead."
          );
        }
        return O;
      }
      function te(e, r, i) {
        if (e == null) return e;
        var l = [], g = 0;
        return W(e, l, "", "", function(A) {
          return r.call(i, A, g++);
        }), l;
      }
      function se(e) {
        if (e._status === -1) {
          var r = e._ioInfo;
          r != null && (r.start = r.end = performance.now()), r = e._result;
          var i = r();
          if (i.then(
            function(g) {
              if (e._status === 0 || e._status === -1) {
                e._status = 1, e._result = g;
                var A = e._ioInfo;
                A != null && (A.end = performance.now()), i.status === void 0 && (i.status = "fulfilled", i.value = g);
              }
            },
            function(g) {
              if (e._status === 0 || e._status === -1) {
                e._status = 2, e._result = g;
                var A = e._ioInfo;
                A != null && (A.end = performance.now()), i.status === void 0 && (i.status = "rejected", i.reason = g);
              }
            }
          ), r = e._ioInfo, r != null) {
            r.value = i;
            var l = i.displayName;
            typeof l == "string" && (r.name = l);
          }
          e._status === -1 && (e._status = 0, e._result = i);
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
        var e = S.H;
        return e === null && console.error(
          `Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`
        ), e;
      }
      function re() {
        S.asyncTransitions--;
      }
      function J(e) {
        if (he === null)
          try {
            var r = ("require" + Math.random()).slice(0, 7);
            he = (c && c[r]).call(
              c,
              "timers"
            ).setImmediate;
          } catch {
            he = function(l) {
              We === !1 && (We = !0, typeof MessageChannel > "u" && console.error(
                "This browser does not have a MessageChannel implementation, so enqueuing tasks via await act(async () => ...) will fail. Please file an issue at https://github.com/facebook/react/issues if you encounter this warning."
              ));
              var g = new MessageChannel();
              g.port1.onmessage = l, g.port2.postMessage(void 0);
            };
          }
        return he(e);
      }
      function K(e) {
        return 1 < e.length && typeof AggregateError == "function" ? new AggregateError(e) : e[0];
      }
      function G(e, r) {
        r !== ve - 1 && console.error(
          "You seem to have overlapping act() calls, this is not supported. Be sure to await previous act() calls before making a new one. "
        ), ve = r;
      }
      function X(e, r, i) {
        var l = S.actQueue;
        if (l !== null)
          if (l.length !== 0)
            try {
              ne(l), J(function() {
                return X(e, r, i);
              });
              return;
            } catch (g) {
              S.thrownErrors.push(g);
            }
          else S.actQueue = null;
        0 < S.thrownErrors.length ? (l = K(S.thrownErrors), S.thrownErrors.length = 0, i(l)) : r(e);
      }
      function ne(e) {
        if (!ke) {
          ke = !0;
          var r = 0;
          try {
            for (; r < e.length; r++) {
              var i = e[r];
              do {
                S.didUsePromise = !1;
                var l = i(!1);
                if (l !== null) {
                  if (S.didUsePromise) {
                    e[r] = i, e.splice(0, r);
                    return;
                  }
                  i = l;
                } else break;
              } while (!0);
            }
            e.length = 0;
          } catch (g) {
            e.splice(0, r + 1), S.thrownErrors.push(g);
          } finally {
            ke = !1;
          }
        }
      }
      typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u" && typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart == "function" && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());
      var Z = Symbol.for("react.transitional.element"), ae = Symbol.for("react.portal"), t = Symbol.for("react.fragment"), o = Symbol.for("react.strict_mode"), p = Symbol.for("react.profiler"), y = Symbol.for("react.consumer"), w = Symbol.for("react.context"), k = Symbol.for("react.forward_ref"), _ = Symbol.for("react.suspense"), H = Symbol.for("react.suspense_list"), Y = Symbol.for("react.memo"), ee = Symbol.for("react.lazy"), je = Symbol.for("react.activity"), Ne = Symbol.iterator, De = {}, Me = {
        isMounted: function() {
          return !1;
        },
        enqueueForceUpdate: function(e) {
          T(e, "forceUpdate");
        },
        enqueueReplaceState: function(e) {
          T(e, "replaceState");
        },
        enqueueSetState: function(e) {
          T(e, "setState");
        }
      }, Le = Object.assign, Ce = {};
      Object.freeze(Ce), E.prototype.isReactComponent = {}, E.prototype.setState = function(e, r) {
        if (typeof e != "object" && typeof e != "function" && e != null)
          throw Error(
            "takes an object of state variables to update or a function which returns an object of state variables."
          );
        this.updater.enqueueSetState(this, e, r, "setState");
      }, E.prototype.forceUpdate = function(e) {
        this.updater.enqueueForceUpdate(this, e, "forceUpdate");
      };
      var B = {
        isMounted: [
          "isMounted",
          "Instead, make sure to clean up subscriptions and pending requests in componentWillUnmount to prevent memory leaks."
        ],
        replaceState: [
          "replaceState",
          "Refactor your code to use setState instead (see https://github.com/facebook/react/issues/3236)."
        ]
      };
      for (le in B)
        B.hasOwnProperty(le) && m(le, B[le]);
      M.prototype = E.prototype, B = j.prototype = new M(), B.constructor = j, Le(B, E.prototype), B.isPureReactComponent = !0;
      var $e = Array.isArray, nt = Symbol.for("react.client.reference"), S = {
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
      }, _e = Object.prototype.hasOwnProperty, Ie = console.createTask ? console.createTask : function() {
        return null;
      };
      B = {
        react_stack_bottom_frame: function(e) {
          return e();
        }
      };
      var Ye, Ue, He = {}, ot = B.react_stack_bottom_frame.bind(
        B,
        b
      )(), ut = Ie(n(b)), xe = !1, qe = /\/+/g, ze = typeof reportError == "function" ? reportError : function(e) {
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
      }, We = !1, he = null, ve = 0, Ee = !1, ke = !1, Ge = typeof queueMicrotask == "function" ? function(e) {
        queueMicrotask(function() {
          return queueMicrotask(e);
        });
      } : J;
      B = Object.freeze({
        __proto__: null,
        c: function(e) {
          return N().useMemoCache(e);
        }
      });
      var le = {
        map: te,
        forEach: function(e, r, i) {
          te(
            e,
            function() {
              r.apply(this, arguments);
            },
            i
          );
        },
        count: function(e) {
          var r = 0;
          return te(e, function() {
            r++;
          }), r;
        },
        toArray: function(e) {
          return te(e, function(r) {
            return r;
          }) || [];
        },
        only: function(e) {
          if (!V(e))
            throw Error(
              "React.Children.only expected to receive a single React element child."
            );
          return e;
        }
      };
      a.Activity = je, a.Children = le, a.Component = E, a.Fragment = t, a.Profiler = p, a.PureComponent = j, a.StrictMode = o, a.Suspense = _, a.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = S, a.__COMPILER_RUNTIME = B, a.act = function(e) {
        var r = S.actQueue, i = ve;
        ve++;
        var l = S.actQueue = r !== null ? r : [], g = !1;
        try {
          var A = e();
        } catch (C) {
          S.thrownErrors.push(C);
        }
        if (0 < S.thrownErrors.length)
          throw G(r, i), e = K(S.thrownErrors), S.thrownErrors.length = 0, e;
        if (A !== null && typeof A == "object" && typeof A.then == "function") {
          var O = A;
          return Ge(function() {
            g || Ee || (Ee = !0, console.error(
              "You called act(async () => ...) without await. This could lead to unexpected testing behaviour, interleaving multiple act calls and mixing their scopes. You should - await act(async () => ...);"
            ));
          }), {
            then: function(C, oe) {
              g = !0, O.then(
                function(fe) {
                  if (G(r, i), i === 0) {
                    try {
                      ne(l), J(function() {
                        return X(
                          fe,
                          C,
                          oe
                        );
                      });
                    } catch (at) {
                      S.thrownErrors.push(at);
                    }
                    if (0 < S.thrownErrors.length) {
                      var st = K(
                        S.thrownErrors
                      );
                      S.thrownErrors.length = 0, oe(st);
                    }
                  } else C(fe);
                },
                function(fe) {
                  G(r, i), 0 < S.thrownErrors.length && (fe = K(
                    S.thrownErrors
                  ), S.thrownErrors.length = 0), oe(fe);
                }
              );
            }
          };
        }
        var D = A;
        if (G(r, i), i === 0 && (ne(l), l.length !== 0 && Ge(function() {
          g || Ee || (Ee = !0, console.error(
            "A component suspended inside an `act` scope, but the `act` call was not awaited. When testing React components that depend on asynchronous data, you must await the result:\n\nawait act(() => ...)"
          ));
        }), S.actQueue = null), 0 < S.thrownErrors.length)
          throw e = K(S.thrownErrors), S.thrownErrors.length = 0, e;
        return {
          then: function(C, oe) {
            g = !0, i === 0 ? (S.actQueue = l, J(function() {
              return X(
                D,
                C,
                oe
              );
            })) : C(D);
          }
        };
      }, a.cache = function(e) {
        return function() {
          return e.apply(null, arguments);
        };
      }, a.cacheSignal = function() {
        return null;
      }, a.captureOwnerStack = function() {
        var e = S.getCurrentStack;
        return e === null ? null : e();
      }, a.cloneElement = function(e, r, i) {
        if (e == null)
          throw Error(
            "The argument must be a React element, but you passed " + e + "."
          );
        var l = Le({}, e.props), g = e.key, A = e._owner;
        if (r != null) {
          var O;
          e: {
            if (_e.call(r, "ref") && (O = Object.getOwnPropertyDescriptor(
              r,
              "ref"
            ).get) && O.isReactWarning) {
              O = !1;
              break e;
            }
            O = r.ref !== void 0;
          }
          O && (A = d()), L(r) && (h(r.key), g = "" + r.key);
          for (D in r)
            !_e.call(r, D) || D === "key" || D === "__self" || D === "__source" || D === "ref" && r.ref === void 0 || (l[D] = r[D]);
        }
        var D = arguments.length - 2;
        if (D === 1) l.children = i;
        else if (1 < D) {
          O = Array(D);
          for (var C = 0; C < D; C++)
            O[C] = arguments[C + 2];
          l.children = O;
        }
        for (l = U(
          e.type,
          g,
          l,
          A,
          e._debugStack,
          e._debugTask
        ), g = 2; g < arguments.length; g++)
          Q(arguments[g]);
        return l;
      }, a.createContext = function(e) {
        return e = {
          $$typeof: w,
          _currentValue: e,
          _currentValue2: e,
          _threadCount: 0,
          Provider: null,
          Consumer: null
        }, e.Provider = e, e.Consumer = {
          $$typeof: y,
          _context: e
        }, e._currentRenderer = null, e._currentRenderer2 = null, e;
      }, a.createElement = function(e, r, i) {
        for (var l = 2; l < arguments.length; l++)
          Q(arguments[l]);
        l = {};
        var g = null;
        if (r != null)
          for (C in Ue || !("__self" in r) || "key" in r || (Ue = !0, console.warn(
            "Your app (or one of its dependencies) is using an outdated JSX transform. Update to the modern JSX transform for faster performance: https://react.dev/link/new-jsx-transform"
          )), L(r) && (h(r.key), g = "" + r.key), r)
            _e.call(r, C) && C !== "key" && C !== "__self" && C !== "__source" && (l[C] = r[C]);
        var A = arguments.length - 2;
        if (A === 1) l.children = i;
        else if (1 < A) {
          for (var O = Array(A), D = 0; D < A; D++)
            O[D] = arguments[D + 2];
          Object.freeze && Object.freeze(O), l.children = O;
        }
        if (e && e.defaultProps)
          for (C in A = e.defaultProps, A)
            l[C] === void 0 && (l[C] = A[C]);
        g && $(
          l,
          typeof e == "function" ? e.displayName || e.name || "Unknown" : e
        );
        var C = 1e4 > S.recentlyCreatedOwnerStacks++;
        return U(
          e,
          g,
          l,
          d(),
          C ? Error("react-stack-top-frame") : ot,
          C ? Ie(n(e)) : ut
        );
      }, a.createRef = function() {
        var e = { current: null };
        return Object.seal(e), e;
      }, a.forwardRef = function(e) {
        e != null && e.$$typeof === Y ? console.error(
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
        var r = { $$typeof: k, render: e }, i;
        return Object.defineProperty(r, "displayName", {
          enumerable: !1,
          configurable: !0,
          get: function() {
            return i;
          },
          set: function(l) {
            i = l, e.name || e.displayName || (Object.defineProperty(e, "name", { value: l }), e.displayName = l);
          }
        }), r;
      }, a.isValidElement = V, a.lazy = function(e) {
        e = { _status: -1, _result: e };
        var r = {
          $$typeof: ee,
          _payload: e,
          _init: se
        }, i = {
          name: "lazy",
          start: -1,
          end: -1,
          value: null,
          owner: null,
          debugStack: Error("react-stack-top-frame"),
          debugTask: console.createTask ? console.createTask("lazy()") : null
        };
        return e._ioInfo = i, r._debugInfo = [{ awaited: i }], r;
      }, a.memo = function(e, r) {
        e == null && console.error(
          "memo: The first argument must be a component. Instead received: %s",
          e === null ? "null" : typeof e
        ), r = {
          $$typeof: Y,
          type: e,
          compare: r === void 0 ? null : r
        };
        var i;
        return Object.defineProperty(r, "displayName", {
          enumerable: !1,
          configurable: !0,
          get: function() {
            return i;
          },
          set: function(l) {
            i = l, e.name || e.displayName || (Object.defineProperty(e, "name", { value: l }), e.displayName = l);
          }
        }), r;
      }, a.startTransition = function(e) {
        var r = S.T, i = {};
        i._updatedFibers = /* @__PURE__ */ new Set(), S.T = i;
        try {
          var l = e(), g = S.S;
          g !== null && g(i, l), typeof l == "object" && l !== null && typeof l.then == "function" && (S.asyncTransitions++, l.then(re, re), l.then(f, ze));
        } catch (A) {
          ze(A);
        } finally {
          r === null && i._updatedFibers && (e = i._updatedFibers.size, i._updatedFibers.clear(), 10 < e && console.warn(
            "Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."
          )), r !== null && i.types !== null && (r.types !== null && r.types !== i.types && console.error(
            "We expected inner Transitions to have transferred the outer types set and that you cannot add to the outer Transition while inside the inner.This is a bug in React."
          ), r.types = i.types), S.T = r;
        }
      }, a.unstable_useCacheRefresh = function() {
        return N().useCacheRefresh();
      }, a.use = function(e) {
        return N().use(e);
      }, a.useActionState = function(e, r, i) {
        return N().useActionState(
          e,
          r,
          i
        );
      }, a.useCallback = function(e, r) {
        return N().useCallback(e, r);
      }, a.useContext = function(e) {
        var r = N();
        return e.$$typeof === y && console.error(
          "Calling useContext(Context.Consumer) is not supported and will cause bugs. Did you mean to call useContext(Context) instead?"
        ), r.useContext(e);
      }, a.useDebugValue = function(e, r) {
        return N().useDebugValue(e, r);
      }, a.useDeferredValue = function(e, r) {
        return N().useDeferredValue(e, r);
      }, a.useEffect = function(e, r) {
        return e == null && console.warn(
          "React Hook useEffect requires an effect callback. Did you forget to pass a callback to the hook?"
        ), N().useEffect(e, r);
      }, a.useEffectEvent = function(e) {
        return N().useEffectEvent(e);
      }, a.useId = function() {
        return N().useId();
      }, a.useImperativeHandle = function(e, r, i) {
        return N().useImperativeHandle(e, r, i);
      }, a.useInsertionEffect = function(e, r) {
        return e == null && console.warn(
          "React Hook useInsertionEffect requires an effect callback. Did you forget to pass a callback to the hook?"
        ), N().useInsertionEffect(e, r);
      }, a.useLayoutEffect = function(e, r) {
        return e == null && console.warn(
          "React Hook useLayoutEffect requires an effect callback. Did you forget to pass a callback to the hook?"
        ), N().useLayoutEffect(e, r);
      }, a.useMemo = function(e, r) {
        return N().useMemo(e, r);
      }, a.useOptimistic = function(e, r) {
        return N().useOptimistic(e, r);
      }, a.useReducer = function(e, r, i) {
        return N().useReducer(e, r, i);
      }, a.useRef = function(e) {
        return N().useRef(e);
      }, a.useState = function(e) {
        return N().useState(e);
      }, a.useSyncExternalStore = function(e, r, i) {
        return N().useSyncExternalStore(
          e,
          r,
          i
        );
      }, a.useTransition = function() {
        return N().useTransition();
      }, a.version = "19.2.3", typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u" && typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop == "function" && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error());
    })();
  })(me, me.exports)), me.exports;
}
var Ve;
function Ae() {
  return Ve || (Ve = 1, process.env.NODE_ENV === "production" ? Re.exports = it() : Re.exports = ct()), Re.exports;
}
var Se = Ae();
const ft = /* @__PURE__ */ tt(Se);
var Te = { exports: {} }, x = {};
var Ke;
function lt() {
  if (Ke) return x;
  Ke = 1;
  var c = Ae();
  function a(f) {
    var u = "https://react.dev/errors/" + f;
    if (1 < arguments.length) {
      u += "?args[]=" + encodeURIComponent(arguments[1]);
      for (var h = 2; h < arguments.length; h++)
        u += "&args[]=" + encodeURIComponent(arguments[h]);
    }
    return "Minified React error #" + f + "; visit " + u + " for the full message or use the non-minified dev environment for full errors and additional helpful warnings.";
  }
  function m() {
  }
  var v = {
    d: {
      f: m,
      r: function() {
        throw Error(a(522));
      },
      D: m,
      C: m,
      L: m,
      m,
      X: m,
      S: m,
      M: m
    },
    p: 0,
    findDOMNode: null
  }, T = Symbol.for("react.portal");
  function E(f, u, h) {
    var s = 3 < arguments.length && arguments[3] !== void 0 ? arguments[3] : null;
    return {
      $$typeof: T,
      key: s == null ? null : "" + s,
      children: f,
      containerInfo: u,
      implementation: h
    };
  }
  var M = c.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;
  function j(f, u) {
    if (f === "font") return "";
    if (typeof u == "string")
      return u === "use-credentials" ? u : "";
  }
  return x.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = v, x.createPortal = function(f, u) {
    var h = 2 < arguments.length && arguments[2] !== void 0 ? arguments[2] : null;
    if (!u || u.nodeType !== 1 && u.nodeType !== 9 && u.nodeType !== 11)
      throw Error(a(299));
    return E(f, u, null, h);
  }, x.flushSync = function(f) {
    var u = M.T, h = v.p;
    try {
      if (M.T = null, v.p = 2, f) return f();
    } finally {
      M.T = u, v.p = h, v.d.f();
    }
  }, x.preconnect = function(f, u) {
    typeof f == "string" && (u ? (u = u.crossOrigin, u = typeof u == "string" ? u === "use-credentials" ? u : "" : void 0) : u = null, v.d.C(f, u));
  }, x.prefetchDNS = function(f) {
    typeof f == "string" && v.d.D(f);
  }, x.preinit = function(f, u) {
    if (typeof f == "string" && u && typeof u.as == "string") {
      var h = u.as, s = j(h, u.crossOrigin), n = typeof u.integrity == "string" ? u.integrity : void 0, d = typeof u.fetchPriority == "string" ? u.fetchPriority : void 0;
      h === "style" ? v.d.S(
        f,
        typeof u.precedence == "string" ? u.precedence : void 0,
        {
          crossOrigin: s,
          integrity: n,
          fetchPriority: d
        }
      ) : h === "script" && v.d.X(f, {
        crossOrigin: s,
        integrity: n,
        fetchPriority: d,
        nonce: typeof u.nonce == "string" ? u.nonce : void 0
      });
    }
  }, x.preinitModule = function(f, u) {
    if (typeof f == "string")
      if (typeof u == "object" && u !== null) {
        if (u.as == null || u.as === "script") {
          var h = j(
            u.as,
            u.crossOrigin
          );
          v.d.M(f, {
            crossOrigin: h,
            integrity: typeof u.integrity == "string" ? u.integrity : void 0,
            nonce: typeof u.nonce == "string" ? u.nonce : void 0
          });
        }
      } else u == null && v.d.M(f);
  }, x.preload = function(f, u) {
    if (typeof f == "string" && typeof u == "object" && u !== null && typeof u.as == "string") {
      var h = u.as, s = j(h, u.crossOrigin);
      v.d.L(f, h, {
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
  }, x.preloadModule = function(f, u) {
    if (typeof f == "string")
      if (u) {
        var h = j(u.as, u.crossOrigin);
        v.d.m(f, {
          as: typeof u.as == "string" && u.as !== "script" ? u.as : void 0,
          crossOrigin: h,
          integrity: typeof u.integrity == "string" ? u.integrity : void 0
        });
      } else v.d.m(f);
  }, x.requestFormReset = function(f) {
    v.d.r(f);
  }, x.unstable_batchedUpdates = function(f, u) {
    return f(u);
  }, x.useFormState = function(f, u, h) {
    return M.H.useFormState(f, u, h);
  }, x.useFormStatus = function() {
    return M.H.useHostTransitionStatus();
  }, x.version = "19.2.3", x;
}
var q = {};
var Xe;
function dt() {
  return Xe || (Xe = 1, process.env.NODE_ENV !== "production" && (function() {
    function c() {
    }
    function a(s) {
      return "" + s;
    }
    function m(s, n, d) {
      var b = 3 < arguments.length && arguments[3] !== void 0 ? arguments[3] : null;
      try {
        a(b);
        var L = !1;
      } catch {
        L = !0;
      }
      return L && (console.error(
        "The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",
        typeof Symbol == "function" && Symbol.toStringTag && b[Symbol.toStringTag] || b.constructor.name || "Object"
      ), a(b)), {
        $$typeof: u,
        key: b == null ? null : "" + b,
        children: s,
        containerInfo: n,
        implementation: d
      };
    }
    function v(s, n) {
      if (s === "font") return "";
      if (typeof n == "string")
        return n === "use-credentials" ? n : "";
    }
    function T(s) {
      return s === null ? "`null`" : s === void 0 ? "`undefined`" : s === "" ? "an empty string" : 'something with type "' + typeof s + '"';
    }
    function E(s) {
      return s === null ? "`null`" : s === void 0 ? "`undefined`" : s === "" ? "an empty string" : typeof s == "string" ? JSON.stringify(s) : typeof s == "number" ? "`" + s + "`" : 'something with type "' + typeof s + '"';
    }
    function M() {
      var s = h.H;
      return s === null && console.error(
        `Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`
      ), s;
    }
    typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u" && typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart == "function" && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());
    var j = Ae(), f = {
      d: {
        f: c,
        r: function() {
          throw Error(
            "Invalid form element. requestFormReset must be passed a form that was rendered by React."
          );
        },
        D: c,
        C: c,
        L: c,
        m: c,
        X: c,
        S: c,
        M: c
      },
      p: 0,
      findDOMNode: null
    }, u = Symbol.for("react.portal"), h = j.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;
    typeof Map == "function" && Map.prototype != null && typeof Map.prototype.forEach == "function" && typeof Set == "function" && Set.prototype != null && typeof Set.prototype.clear == "function" && typeof Set.prototype.forEach == "function" || console.error(
      "React depends on Map and Set built-in types. Make sure that you load a polyfill in older browsers. https://reactjs.org/link/react-polyfills"
    ), q.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE = f, q.createPortal = function(s, n) {
      var d = 2 < arguments.length && arguments[2] !== void 0 ? arguments[2] : null;
      if (!n || n.nodeType !== 1 && n.nodeType !== 9 && n.nodeType !== 11)
        throw Error("Target container is not a DOM element.");
      return m(s, n, null, d);
    }, q.flushSync = function(s) {
      var n = h.T, d = f.p;
      try {
        if (h.T = null, f.p = 2, s)
          return s();
      } finally {
        h.T = n, f.p = d, f.d.f() && console.error(
          "flushSync was called from inside a lifecycle method. React cannot flush when React is already rendering. Consider moving this call to a scheduler task or micro task."
        );
      }
    }, q.preconnect = function(s, n) {
      typeof s == "string" && s ? n != null && typeof n != "object" ? console.error(
        "ReactDOM.preconnect(): Expected the `options` argument (second) to be an object but encountered %s instead. The only supported option at this time is `crossOrigin` which accepts a string.",
        E(n)
      ) : n != null && typeof n.crossOrigin != "string" && console.error(
        "ReactDOM.preconnect(): Expected the `crossOrigin` option (second argument) to be a string but encountered %s instead. Try removing this option or passing a string value instead.",
        T(n.crossOrigin)
      ) : console.error(
        "ReactDOM.preconnect(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",
        T(s)
      ), typeof s == "string" && (n ? (n = n.crossOrigin, n = typeof n == "string" ? n === "use-credentials" ? n : "" : void 0) : n = null, f.d.C(s, n));
    }, q.prefetchDNS = function(s) {
      if (typeof s != "string" || !s)
        console.error(
          "ReactDOM.prefetchDNS(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",
          T(s)
        );
      else if (1 < arguments.length) {
        var n = arguments[1];
        typeof n == "object" && n.hasOwnProperty("crossOrigin") ? console.error(
          "ReactDOM.prefetchDNS(): Expected only one argument, `href`, but encountered %s as a second argument instead. This argument is reserved for future options and is currently disallowed. It looks like the you are attempting to set a crossOrigin property for this DNS lookup hint. Browsers do not perform DNS queries using CORS and setting this attribute on the resource hint has no effect. Try calling ReactDOM.prefetchDNS() with just a single string argument, `href`.",
          E(n)
        ) : console.error(
          "ReactDOM.prefetchDNS(): Expected only one argument, `href`, but encountered %s as a second argument instead. This argument is reserved for future options and is currently disallowed. Try calling ReactDOM.prefetchDNS() with just a single string argument, `href`.",
          E(n)
        );
      }
      typeof s == "string" && f.d.D(s);
    }, q.preinit = function(s, n) {
      if (typeof s == "string" && s ? n == null || typeof n != "object" ? console.error(
        "ReactDOM.preinit(): Expected the `options` argument (second) to be an object with an `as` property describing the type of resource to be preinitialized but encountered %s instead.",
        E(n)
      ) : n.as !== "style" && n.as !== "script" && console.error(
        'ReactDOM.preinit(): Expected the `as` property in the `options` argument (second) to contain a valid value describing the type of resource to be preinitialized but encountered %s instead. Valid values for `as` are "style" and "script".',
        E(n.as)
      ) : console.error(
        "ReactDOM.preinit(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",
        T(s)
      ), typeof s == "string" && n && typeof n.as == "string") {
        var d = n.as, b = v(d, n.crossOrigin), L = typeof n.integrity == "string" ? n.integrity : void 0, $ = typeof n.fetchPriority == "string" ? n.fetchPriority : void 0;
        d === "style" ? f.d.S(
          s,
          typeof n.precedence == "string" ? n.precedence : void 0,
          {
            crossOrigin: b,
            integrity: L,
            fetchPriority: $
          }
        ) : d === "script" && f.d.X(s, {
          crossOrigin: b,
          integrity: L,
          fetchPriority: $,
          nonce: typeof n.nonce == "string" ? n.nonce : void 0
        });
      }
    }, q.preinitModule = function(s, n) {
      var d = "";
      if (typeof s == "string" && s || (d += " The `href` argument encountered was " + T(s) + "."), n !== void 0 && typeof n != "object" ? d += " The `options` argument encountered was " + T(n) + "." : n && "as" in n && n.as !== "script" && (d += " The `as` option encountered was " + E(n.as) + "."), d)
        console.error(
          "ReactDOM.preinitModule(): Expected up to two arguments, a non-empty `href` string and, optionally, an `options` object with a valid `as` property.%s",
          d
        );
      else
        switch (d = n && typeof n.as == "string" ? n.as : "script", d) {
          case "script":
            break;
          default:
            d = E(d), console.error(
              'ReactDOM.preinitModule(): Currently the only supported "as" type for this function is "script" but received "%s" instead. This warning was generated for `href` "%s". In the future other module types will be supported, aligning with the import-attributes proposal. Learn more here: (https://github.com/tc39/proposal-import-attributes)',
              d,
              s
            );
        }
      typeof s == "string" && (typeof n == "object" && n !== null ? (n.as == null || n.as === "script") && (d = v(
        n.as,
        n.crossOrigin
      ), f.d.M(s, {
        crossOrigin: d,
        integrity: typeof n.integrity == "string" ? n.integrity : void 0,
        nonce: typeof n.nonce == "string" ? n.nonce : void 0
      })) : n == null && f.d.M(s));
    }, q.preload = function(s, n) {
      var d = "";
      if (typeof s == "string" && s || (d += " The `href` argument encountered was " + T(s) + "."), n == null || typeof n != "object" ? d += " The `options` argument encountered was " + T(n) + "." : typeof n.as == "string" && n.as || (d += " The `as` option encountered was " + T(n.as) + "."), d && console.error(
        'ReactDOM.preload(): Expected two arguments, a non-empty `href` string and an `options` object with an `as` property valid for a `<link rel="preload" as="..." />` tag.%s',
        d
      ), typeof s == "string" && typeof n == "object" && n !== null && typeof n.as == "string") {
        d = n.as;
        var b = v(
          d,
          n.crossOrigin
        );
        f.d.L(s, d, {
          crossOrigin: b,
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
    }, q.preloadModule = function(s, n) {
      var d = "";
      typeof s == "string" && s || (d += " The `href` argument encountered was " + T(s) + "."), n !== void 0 && typeof n != "object" ? d += " The `options` argument encountered was " + T(n) + "." : n && "as" in n && typeof n.as != "string" && (d += " The `as` option encountered was " + T(n.as) + "."), d && console.error(
        'ReactDOM.preloadModule(): Expected two arguments, a non-empty `href` string and, optionally, an `options` object with an `as` property valid for a `<link rel="modulepreload" as="..." />` tag.%s',
        d
      ), typeof s == "string" && (n ? (d = v(
        n.as,
        n.crossOrigin
      ), f.d.m(s, {
        as: typeof n.as == "string" && n.as !== "script" ? n.as : void 0,
        crossOrigin: d,
        integrity: typeof n.integrity == "string" ? n.integrity : void 0
      })) : f.d.m(s));
    }, q.requestFormReset = function(s) {
      f.d.r(s);
    }, q.unstable_batchedUpdates = function(s, n) {
      return s(n);
    }, q.useFormState = function(s, n, d) {
      return M().useFormState(s, n, d);
    }, q.useFormStatus = function() {
      return M().useHostTransitionStatus();
    }, q.version = "19.2.3", typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ < "u" && typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop == "function" && __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error());
  })()), q;
}
var Qe;
function pt() {
  if (Qe) return Te.exports;
  Qe = 1;
  function c() {
    if (!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ > "u" || typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE != "function")) {
      if (process.env.NODE_ENV !== "production")
        throw new Error("^_^");
      try {
        __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(c);
      } catch (a) {
        console.error(a);
      }
    }
  }
  return process.env.NODE_ENV === "production" ? (c(), Te.exports = lt()) : Te.exports = dt(), Te.exports;
}
var yt = pt();
const gt = /* @__PURE__ */ tt(yt);
var mt = Object.defineProperty, _t = (c, a, m) => a in c ? mt(c, a, { enumerable: !0, configurable: !0, writable: !0, value: m }) : c[a] = m, Oe = (c, a, m) => _t(c, typeof a != "symbol" ? a + "" : a, m);
const ht = {
  stringify: (c) => c ? "true" : "false",
  parse: (c) => /^[ty1-9]/i.test(c)
}, vt = {
  stringify: (c) => c.name,
  parse: (c, a, m) => {
    const v = (() => {
      if (typeof window < "u" && c in window)
        return window[c];
      if (typeof global < "u" && c in global)
        return global[c];
    })();
    return typeof v == "function" ? v.bind(m) : void 0;
  }
}, Et = {
  stringify: (c) => JSON.stringify(c),
  parse: (c) => JSON.parse(c)
};
function Rt(c) {
  return c.replace(
    /([a-z0-9])([A-Z])/g,
    (a, m, v) => `${m}-${v.toLowerCase()}`
  );
}
function rt(c) {
  return c.replace(/[-:]([a-z])/g, (a, m) => `${m.toUpperCase()}`);
}
const Tt = {
  stringify: (c) => c.name,
  parse: (c, a, m) => {
    const v = (() => {
      const T = rt(a);
      if (typeof m < "u" && T in m.container)
        return m.container[T];
    })();
    return typeof v == "function" ? v.bind(m) : void 0;
  }
}, Ot = {
  stringify: (c) => `${c}`,
  parse: (c) => parseFloat(c)
}, bt = {
  stringify: (c) => c,
  parse: (c) => c
}, Pe = {
  string: bt,
  number: Ot,
  boolean: ht,
  function: vt,
  method: Tt,
  json: Et
}, de = Symbol.for("r2wc.render"), be = Symbol.for("r2wc.connected"), ie = Symbol.for("r2wc.context"), F = Symbol.for("r2wc.props");
function wt(c, a, m) {
  var v, T, E;
  a.props || (a.props = c.propTypes ? Object.keys(c.propTypes) : []), a.events || (a.events = []);
  const M = Array.isArray(a.props) ? a.props.slice() : Object.keys(a.props), j = Array.isArray(a.events) ? a.events.slice() : Object.keys(a.events), f = {}, u = {}, h = {}, s = {};
  for (const d of M) {
    f[d] = Array.isArray(a.props) ? "string" : a.props[d];
    const b = Rt(d);
    h[d] = b, s[b] = d;
  }
  for (const d of j)
    u[d] = Array.isArray(a.events) ? {} : a.events[d];
  class n extends HTMLElement {
    constructor() {
      super(), Oe(this, E, !0), Oe(this, T), Oe(this, v, {}), Oe(this, "container"), a.shadow ? this.container = this.attachShadow({
        mode: a.shadow
      }) : this.container = this, this[F].container = this.container;
      for (const b of M) {
        const L = h[b], $ = this.getAttribute(L), I = f[b], U = I ? Pe[I] : null;
        if (I === "method") {
          const z = rt(L);
          Object.defineProperty(this[F].container, z, {
            enumerable: !0,
            configurable: !0,
            get() {
              return this[F][z];
            },
            set(Q) {
              this[F][z] = Q, this[de]();
            }
          }), this[F][b] = U.parse($, L, this);
        }
        U != null && U.parse && $ && (this[F][b] = U.parse($, L, this));
      }
      for (const b of j)
        this[F][b] = (L) => {
          const $ = b.replace(/^on/, "").toLowerCase();
          this.dispatchEvent(
            new CustomEvent($, { detail: L, ...u[b] })
          );
        };
    }
    static get observedAttributes() {
      return Object.keys(s);
    }
    connectedCallback() {
      this[be] = !0, this[de]();
    }
    disconnectedCallback() {
      this[be] = !1, this[ie] && m.unmount(this[ie]), delete this[ie];
    }
    attributeChangedCallback(b, L, $) {
      const I = s[b], U = f[I], z = U ? Pe[U] : null;
      I in f && z != null && z.parse && $ && (this[F][I] = z.parse($, b, this), this[de]());
    }
    [(E = be, T = ie, v = F, de)]() {
      this[be] && (this[ie] ? m.update(this[ie], this[F]) : this[ie] = m.mount(
        this.container,
        c,
        this[F]
      ));
    }
  }
  for (const d of M) {
    const b = h[d], L = f[d];
    Object.defineProperty(n.prototype, d, {
      enumerable: !0,
      configurable: !0,
      get() {
        return this[F][d];
      },
      set($) {
        this[F][d] = $;
        const I = L ? Pe[L] : null;
        if (I != null && I.stringify) {
          const U = I.stringify($, b, this);
          this.getAttribute(b) !== U && this.setAttribute(b, U);
        } else
          this[de]();
      }
    });
  }
  return n;
}
function St(c, a, m, v = {}) {
  function T(j, f, u) {
    const h = a.createElement(f, u);
    if ("createRoot" in m) {
      const s = m.createRoot(j);
      return s.render(h), {
        container: j,
        root: s,
        ReactComponent: f
      };
    }
    if ("render" in m)
      return m.render(h, j), {
        container: j,
        ReactComponent: f
      };
    throw new Error("Invalid ReactDOM instance provided.");
  }
  function E({ container: j, root: f, ReactComponent: u }, h) {
    const s = a.createElement(u, h);
    if (f) {
      f.render(s);
      return;
    }
    if ("render" in m) {
      m.render(s, j);
      return;
    }
  }
  function M({ container: j, root: f }) {
    if (f) {
      f.unmount();
      return;
    }
    if ("unmountComponentAtNode" in m) {
      m.unmountComponentAtNode(j);
      return;
    }
  }
  return wt(c, v, { mount: T, unmount: M, update: E });
}
var we = { exports: {} }, pe = {};
var Je;
function At() {
  if (Je) return pe;
  Je = 1;
  var c = Symbol.for("react.transitional.element"), a = Symbol.for("react.fragment");
  function m(v, T, E) {
    var M = null;
    if (E !== void 0 && (M = "" + E), T.key !== void 0 && (M = "" + T.key), "key" in T) {
      E = {};
      for (var j in T)
        j !== "key" && (E[j] = T[j]);
    } else E = T;
    return T = E.ref, {
      $$typeof: c,
      type: v,
      key: M,
      ref: T !== void 0 ? T : null,
      props: E
    };
  }
  return pe.Fragment = a, pe.jsx = m, pe.jsxs = m, pe;
}
var ye = {};
var Ze;
function Ct() {
  return Ze || (Ze = 1, process.env.NODE_ENV !== "production" && (function() {
    function c(t) {
      if (t == null) return null;
      if (typeof t == "function")
        return t.$$typeof === se ? null : t.displayName || t.name || null;
      if (typeof t == "string") return t;
      switch (t) {
        case $:
          return "Fragment";
        case U:
          return "Profiler";
        case I:
          return "StrictMode";
        case ue:
          return "Suspense";
        case P:
          return "SuspenseList";
        case te:
          return "Activity";
      }
      if (typeof t == "object")
        switch (typeof t.tag == "number" && console.error(
          "Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."
        ), t.$$typeof) {
          case L:
            return "Portal";
          case Q:
            return t.displayName || "Context";
          case z:
            return (t._context.displayName || "Context") + ".Consumer";
          case V:
            var o = t.render;
            return t = t.displayName, t || (t = o.displayName || o.name || "", t = t !== "" ? "ForwardRef(" + t + ")" : "ForwardRef"), t;
          case ce:
            return o = t.displayName || null, o !== null ? o : c(t.type) || "Memo";
          case W:
            o = t._payload, t = t._init;
            try {
              return c(t(o));
            } catch {
            }
        }
      return null;
    }
    function a(t) {
      return "" + t;
    }
    function m(t) {
      try {
        a(t);
        var o = !1;
      } catch {
        o = !0;
      }
      if (o) {
        o = console;
        var p = o.error, y = typeof Symbol == "function" && Symbol.toStringTag && t[Symbol.toStringTag] || t.constructor.name || "Object";
        return p.call(
          o,
          "The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",
          y
        ), a(t);
      }
    }
    function v(t) {
      if (t === $) return "<>";
      if (typeof t == "object" && t !== null && t.$$typeof === W)
        return "<...>";
      try {
        var o = c(t);
        return o ? "<" + o + ">" : "<...>";
      } catch {
        return "<...>";
      }
    }
    function T() {
      var t = N.A;
      return t === null ? null : t.getOwner();
    }
    function E() {
      return Error("react-stack-top-frame");
    }
    function M(t) {
      if (re.call(t, "key")) {
        var o = Object.getOwnPropertyDescriptor(t, "key").get;
        if (o && o.isReactWarning) return !1;
      }
      return t.key !== void 0;
    }
    function j(t, o) {
      function p() {
        G || (G = !0, console.error(
          "%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://react.dev/link/special-props)",
          o
        ));
      }
      p.isReactWarning = !0, Object.defineProperty(t, "key", {
        get: p,
        configurable: !0
      });
    }
    function f() {
      var t = c(this.type);
      return X[t] || (X[t] = !0, console.error(
        "Accessing element.ref was removed in React 19. ref is now a regular prop. It will be removed from the JSX Element type in a future release."
      )), t = this.props.ref, t !== void 0 ? t : null;
    }
    function u(t, o, p, y, w, k) {
      var _ = p.ref;
      return t = {
        $$typeof: b,
        type: t,
        key: o,
        props: p,
        _owner: y
      }, (_ !== void 0 ? _ : null) !== null ? Object.defineProperty(t, "ref", {
        enumerable: !1,
        get: f
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
        value: w
      }), Object.defineProperty(t, "_debugTask", {
        configurable: !1,
        enumerable: !1,
        writable: !0,
        value: k
      }), Object.freeze && (Object.freeze(t.props), Object.freeze(t)), t;
    }
    function h(t, o, p, y, w, k) {
      var _ = o.children;
      if (_ !== void 0)
        if (y)
          if (J(_)) {
            for (y = 0; y < _.length; y++)
              s(_[y]);
            Object.freeze && Object.freeze(_);
          } else
            console.error(
              "React.jsx: Static children should always be an array. You are likely explicitly calling React.jsxs or React.jsxDEV. Use the Babel transform instead."
            );
        else s(_);
      if (re.call(o, "key")) {
        _ = c(t);
        var H = Object.keys(o).filter(function(ee) {
          return ee !== "key";
        });
        y = 0 < H.length ? "{key: someKey, " + H.join(": ..., ") + ": ...}" : "{key: someKey}", ae[_ + y] || (H = 0 < H.length ? "{" + H.join(": ..., ") + ": ...}" : "{}", console.error(
          `A props object containing a "key" prop is being spread into JSX:
  let props = %s;
  <%s {...props} />
React keys must be passed directly to JSX without using spread:
  let props = %s;
  <%s key={someKey} {...props} />`,
          y,
          _,
          H,
          _
        ), ae[_ + y] = !0);
      }
      if (_ = null, p !== void 0 && (m(p), _ = "" + p), M(o) && (m(o.key), _ = "" + o.key), "key" in o) {
        p = {};
        for (var Y in o)
          Y !== "key" && (p[Y] = o[Y]);
      } else p = o;
      return _ && j(
        p,
        typeof t == "function" ? t.displayName || t.name || "Unknown" : t
      ), u(
        t,
        _,
        p,
        T(),
        w,
        k
      );
    }
    function s(t) {
      n(t) ? t._store && (t._store.validated = 1) : typeof t == "object" && t !== null && t.$$typeof === W && (t._payload.status === "fulfilled" ? n(t._payload.value) && t._payload.value._store && (t._payload.value._store.validated = 1) : t._store && (t._store.validated = 1));
    }
    function n(t) {
      return typeof t == "object" && t !== null && t.$$typeof === b;
    }
    var d = Ae(), b = Symbol.for("react.transitional.element"), L = Symbol.for("react.portal"), $ = Symbol.for("react.fragment"), I = Symbol.for("react.strict_mode"), U = Symbol.for("react.profiler"), z = Symbol.for("react.consumer"), Q = Symbol.for("react.context"), V = Symbol.for("react.forward_ref"), ue = Symbol.for("react.suspense"), P = Symbol.for("react.suspense_list"), ce = Symbol.for("react.memo"), W = Symbol.for("react.lazy"), te = Symbol.for("react.activity"), se = Symbol.for("react.client.reference"), N = d.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE, re = Object.prototype.hasOwnProperty, J = Array.isArray, K = console.createTask ? console.createTask : function() {
      return null;
    };
    d = {
      react_stack_bottom_frame: function(t) {
        return t();
      }
    };
    var G, X = {}, ne = d.react_stack_bottom_frame.bind(
      d,
      E
    )(), Z = K(v(E)), ae = {};
    ye.Fragment = $, ye.jsx = function(t, o, p) {
      var y = 1e4 > N.recentlyCreatedOwnerStacks++;
      return h(
        t,
        o,
        p,
        !1,
        y ? Error("react-stack-top-frame") : ne,
        y ? K(v(t)) : Z
      );
    }, ye.jsxs = function(t, o, p) {
      var y = 1e4 > N.recentlyCreatedOwnerStacks++;
      return h(
        t,
        o,
        p,
        !0,
        y ? Error("react-stack-top-frame") : ne,
        y ? K(v(t)) : Z
      );
    };
  })()), ye;
}
var et;
function kt() {
  return et || (et = 1, process.env.NODE_ENV === "production" ? we.exports = At() : we.exports = Ct()), we.exports;
}
var ge = kt();
const Pt = ({ dealerId: c }) => {
  const [a, m] = Se.useState(""), [v, T] = Se.useState([]);
  return Se.useEffect(() => {
    fetch(`/api/widget-data?dealer=${c || ""}`).then((E) => E.json()).then((E) => {
      m(E.title), T(E.cars);
    });
  }, [c]), /* @__PURE__ */ ge.jsxs("div", { className: "bg-white shadow p-4 border rounded-lg max-w-sm", children: [
    /* @__PURE__ */ ge.jsx("h2", { className: "mb-4 text-red-800 text-xl", children: a }),
    /* @__PURE__ */ ge.jsx("ul", { children: v.map((E) => /* @__PURE__ */ ge.jsx("li", { className: "flex justify-between mb-2", children: /* @__PURE__ */ ge.jsxs("span", { children: [
      E.name,
      " – ",
      E.price,
      " DKK"
    ] }) }, E.name)) })
  ] });
}, jt = St(Pt, ft, gt);
customElements.define("my-widget", jt);
