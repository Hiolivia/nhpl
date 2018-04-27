window.Modernizr = function(e, t, n) {
    function r(e) {
        v.cssText = e
    }
    function o(e, t) {
        return typeof e === t
    }
    function i(e, t) {
        return !! ~ ("" + e).indexOf(t)
    }
    function a(e, t) {
        for (var r in e) {
            var o = e[r];
            if (!i(o, "-") && v[o] !== n) return "pfx" == t ? o: !0
        }
        return ! 1
    }
    function c(e, t, r) {
        for (var i in e) {
            var a = t[e[i]];
            if (a !== n) return r === !1 ? e[i] : o(a, "function") ? a.bind(r || t) : a
        }
        return ! 1
    }
    function l(e, t, n) {
        var r = e.charAt(0).toUpperCase() + e.slice(1),
        i = (e + " " + E.join(r + " ") + r).split(" ");
        return o(t, "string") || o(t, "undefined") ? a(i, t) : (i = (e + " " + j.join(r + " ") + r).split(" "), c(i, t, n))
    }
    var s, u, f, p = "2.8.3",
    d = {},
    m = !0,
    h = t.documentElement,
    y = "modernizr",
    g = t.createElement(y),
    v = g.style,
    b = ({}.toString, "Webkit Moz O ms"),
    E = b.split(" "),
    j = b.toLowerCase().split(" "),
    S = {},
    C = [],
    x = C.slice,
    w = {}.hasOwnProperty;
    f = o(w, "undefined") || o(w.call, "undefined") ?
    function(e, t) {
        return t in e && o(e.constructor.prototype[t], "undefined")
    }: function(e, t) {
        return w.call(e, t)
    },
    Function.prototype.bind || (Function.prototype.bind = function(e) {
        var t = this;
        if ("function" != typeof t) throw new TypeError;
        var n = x.call(arguments, 1),
        r = function() {
            if (this instanceof r) {
                var o = function() {};
                o.prototype = t.prototype;
                var i = new o,
                a = t.apply(i, n.concat(x.call(arguments)));
                return Object(a) === a ? a: i
            }
            return t.apply(e, n.concat(x.call(arguments)))
        };
        return r
    }),
    S.cssanimations = function() {
        return l("animationName")
    };
    for (var N in S) f(S, N) && (u = N.toLowerCase(), d[u] = S[N](), C.push((d[u] ? "": "no-") + u));
    return d.addTest = function(e, t) {
        if ("object" == typeof e) for (var r in e) f(e, r) && d.addTest(r, e[r]);
        else {
            if (e = e.toLowerCase(), d[e] !== n) return d;
            t = "function" == typeof t ? t() : t,
            "undefined" != typeof m && m && (h.className += " " + (t ? "": "no-") + e),
            d[e] = t
        }
        return d
    },
    r(""),
    g = s = null,
    function(e, t) {
        function n(e, t) {
            var n = e.createElement("p"),
            r = e.getElementsByTagName("head")[0] || e.documentElement;
            return n.innerHTML = "x<style>" + t + "</style>",
            r.insertBefore(n.lastChild, r.firstChild)
        }
        function r() {
            var e = v.elements;
            return "string" == typeof e ? e.split(" ") : e
        }
        function o(e) {
            var t = g[e[h]];
            return t || (t = {},
            y++, e[h] = y, g[y] = t),
            t
        }
        function i(e, n, r) {
            if (n || (n = t), u) return n.createElement(e);
            r || (r = o(n));
            var i;
            return i = r.cache[e] ? r.cache[e].cloneNode() : m.test(e) ? (r.cache[e] = r.createElem(e)).cloneNode() : r.createElem(e),
            !i.canHaveChildren || d.test(e) || i.tagUrn ? i: r.frag.appendChild(i)
        }
        function a(e, n) {
            if (e || (e = t), u) return e.createDocumentFragment();
            n = n || o(e);
            for (var i = n.frag.cloneNode(), a = 0, c = r(), l = c.length; l > a; a++) i.createElement(c[a]);
            return i
        }
        function c(e, t) {
            t.cache || (t.cache = {},
            t.createElem = e.createElement, t.createFrag = e.createDocumentFragment, t.frag = t.createFrag()),
            e.createElement = function(n) {
                return v.shivMethods ? i(n, e, t) : t.createElem(n)
            },
            e.createDocumentFragment = Function("h,f", "return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&(" + r().join().replace(/[\w\-]+/g,
            function(e) {
                return t.createElem(e),
                t.frag.createElement(e),
                'c("' + e + '")'
            }) + ");return n}")(v, t.frag)
        }
        function l(e) {
            e || (e = t);
            var r = o(e);
            return v.shivCSS && !s && !r.hasCSS && (r.hasCSS = !!n(e, "article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}template{display:none}")),
            u || c(e, r),
            e
        }
        var s, u, f = "3.7.0",
        p = e.html5 || {},
        d = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,
        m = /^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,
        h = "_html5shiv",
        y = 0,
        g = {}; !
        function() {
            try {
                var e = t.createElement("a");
                e.innerHTML = "<xyz></xyz>",
                s = "hidden" in e,
                u = 1 == e.childNodes.length ||
                function() {
                    t.createElement("a");
                    var e = t.createDocumentFragment();
                    return "undefined" == typeof e.cloneNode || "undefined" == typeof e.createDocumentFragment || "undefined" == typeof e.createElement
                } ()
            } catch(n) {
                s = !0,
                u = !0
            }
        } ();
        var v = {
            elements: p.elements || "abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output progress section summary template time video",
            version: f,
            shivCSS: p.shivCSS !== !1,
            supportsUnknownElements: u,
            shivMethods: p.shivMethods !== !1,
            type: "default",
            shivDocument: l,
            createElement: i,
            createDocumentFragment: a
        };
        e.html5 = v,
        l(t)
    } (this, t),
    d._version = p,
    d._domPrefixes = j,
    d._cssomPrefixes = E,
    d.testProp = function(e) {
        return a([e])
    },
    d.testAllProps = l,
    d.prefixed = function(e, t, n) {
        return t ? l(e, t, n) : l(e, "pfx")
    },
    h.className = h.className.replace(/(^|\s)no-js(\s|$)/, "$1$2") + (m ? " js " + C.join(" ") : ""),
    d
} (this, this.document),
function(e, t, n) {
    function r(e) {
        return "[object Function]" == y.call(e)
    }
    function o(e) {
        return "string" == typeof e
    }
    function i() {}
    function a(e) {
        return ! e || "loaded" == e || "complete" == e || "uninitialized" == e
    }
    function c() {
        var e = g.shift();
        v = 1,
        e ? e.t ? m(function() { ("c" == e.t ? p.injectCss: p.injectJs)(e.s, 0, e.a, e.x, e.e, 1)
        },
        0) : (e(), c()) : v = 0
    }
    function l(e, n, r, o, i, l, s) {
        function u(t) {
            if (!d && a(f.readyState) && (b.r = d = 1, !v && c(), f.onload = f.onreadystatechange = null, t)) {
                "img" != e && m(function() {
                    j.removeChild(f)
                },
                50);
                for (var r in N[n]) N[n].hasOwnProperty(r) && N[n][r].onload()
            }
        }
        var s = s || p.errorTimeout,
        f = t.createElement(e),
        d = 0,
        y = 0,
        b = {
            t: r,
            s: n,
            e: i,
            a: l,
            x: s
        };
        1 === N[n] && (y = 1, N[n] = []),
        "object" == e ? f.data = n: (f.src = n, f.type = e),
        f.width = f.height = "0",
        f.onerror = f.onload = f.onreadystatechange = function() {
            u.call(this, y)
        },
        g.splice(o, 0, b),
        "img" != e && (y || 2 === N[n] ? (j.insertBefore(f, E ? null: h), m(u, s)) : N[n].push(f))
    }
    function s(e, t, n, r, i) {
        return v = 0,
        t = t || "j",
        o(e) ? l("c" == t ? C: S, e, t, this.i++, n, r, i) : (g.splice(this.i++, 0, e), 1 == g.length && c()),
        this
    }
    function u() {
        var e = p;
        return e.loader = {
            load: s,
            i: 0
        },
        e
    }
    var f, p, d = t.documentElement,
    m = e.setTimeout,
    h = t.getElementsByTagName("script")[0],
    y = {}.toString,
    g = [],
    v = 0,
    b = "MozAppearance" in d.style,
    E = b && !!t.createRange().compareNode,
    j = E ? d: h.parentNode,
    d = e.opera && "[object Opera]" == y.call(e.opera),
    d = !!t.attachEvent && !d,
    S = b ? "object": d ? "script": "img",
    C = d ? "script": S,
    x = Array.isArray ||
    function(e) {
        return "[object Array]" == y.call(e)
    },
    w = [],
    N = {},
    F = {
        timeout: function(e, t) {
            return t.length && (e.timeout = t[0]),
            e
        }
    };
    p = function(e) {
        function t(e) {
            var t, n, r, e = e.split("!"),
            o = w.length,
            i = e.pop(),
            a = e.length,
            i = {
                url: i,
                origUrl: i,
                prefixes: e
            };
            for (n = 0; a > n; n++) r = e[n].split("="),
            (t = F[r.shift()]) && (i = t(i, r));
            for (n = 0; o > n; n++) i = w[n](i);
            return i
        }
        function a(e, o, i, a, c) {
            var l = t(e),
            s = l.autoCallback;
            l.url.split(".").pop().split("?").shift(),
            l.bypass || (o && (o = r(o) ? o: o[e] || o[a] || o[e.split("/").pop().split("?")[0]]), l.instead ? l.instead(e, o, i, a, c) : (N[l.url] ? l.noexec = !0 : N[l.url] = 1, i.load(l.url, l.forceCSS || !l.forceJS && "css" == l.url.split(".").pop().split("?").shift() ? "c": n, l.noexec, l.attrs, l.timeout), (r(o) || r(s)) && i.load(function() {
                u(),
                o && o(l.origUrl, c, a),
                s && s(l.origUrl, c, a),
                N[l.url] = 2
            })))
        }
        function c(e, t) {
            function n(e, n) {
                if (e) {
                    if (o(e)) n || (f = function() {
                        var e = [].slice.call(arguments);
                        p.apply(this, e),
                        d()
                    }),
                    a(e, f, t, 0, s);
                    else if (Object(e) === e) for (l in c = function() {
                        var t, n = 0;
                        for (t in e) e.hasOwnProperty(t) && n++;
                        return n
                    } (), e) e.hasOwnProperty(l) && (!n && !--c && (r(f) ? f = function() {
                        var e = [].slice.call(arguments);
                        p.apply(this, e),
                        d()
                    }: f[l] = function(e) {
                        return function() {
                            var t = [].slice.call(arguments);
                            e && e.apply(this, t),
                            d()
                        }
                    } (p[l])), a(e[l], f, t, l, s))
                } else ! n && d()
            }
            var c, l, s = !!e.test,
            u = e.load || e.both,
            f = e.callback || i,
            p = f,
            d = e.complete || i;
            n(s ? e.yep: e.nope, !!u),
            u && n(u)
        }
        var l, s, f = this.yepnope.loader;
        if (o(e)) a(e, 0, f, 0);
        else if (x(e)) for (l = 0; l < e.length; l++) s = e[l],
        o(s) ? a(s, 0, f, 0) : x(s) ? p(s) : Object(s) === s && c(s, f);
        else Object(e) === e && c(e, f)
    },
    p.addPrefix = function(e, t) {
        F[e] = t
    },
    p.addFilter = function(e) {
        w.push(e)
    },
    p.errorTimeout = 1e4,
    null == t.readyState && t.addEventListener && (t.readyState = "loading", t.addEventListener("DOMContentLoaded", f = function() {
        t.removeEventListener("DOMContentLoaded", f, 0),
        t.readyState = "complete"
    },
    0)),
    e.yepnope = u(),
    e.yepnope.executeStack = c,
    e.yepnope.injectJs = function(e, n, r, o, l, s) {
        var u, f, d = t.createElement("script"),
        o = o || p.errorTimeout;
        d.src = e;
        for (f in r) d.setAttribute(f, r[f]);
        n = s ? c: n || i,
        d.onreadystatechange = d.onload = function() { ! u && a(d.readyState) && (u = 1, n(), d.onload = d.onreadystatechange = null)
        },
        m(function() {
            u || (u = 1, n(1))
        },
        o),
        l ? d.onload() : h.parentNode.insertBefore(d, h)
    },
    e.yepnope.injectCss = function(e, n, r, o, a, l) {
        var s, o = t.createElement("link"),
        n = l ? c: n || i;
        o.href = e,
        o.rel = "stylesheet",
        o.type = "text/css";
        for (s in r) o.setAttribute(s, r[s]);
        a || (h.parentNode.insertBefore(o, h), m(n, 0))
    }
} (this, document),
Modernizr.load = function() {
    yepnope.apply(window, [].slice.call(arguments, 0))
};