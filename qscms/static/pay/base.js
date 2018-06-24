
var Zepto = function () {
    function t(t) {
        return null == t ? String(t) : X[B.call(t)] || "object"
    }

    function n(n) {
        return "function" == t(n)
    }

    function e(t) {
        return null != t && t == t.window
    }

    function i(t) {
        return null != t && t.nodeType == t.DOCUMENT_NODE
    }

    function r(n) {
        return "object" == t(n)
    }

    function o(t) {
        return r(t) && !e(t) && Object.getPrototypeOf(t) == Object.prototype
    }

    function a(t) {
        return "number" == typeof t.length
    }

    function s(t) {
        return P.call(t, function (t) {
            return null != t
        })
    }

    function u(t) {
        return t.length > 0 ? j.fn.concat.apply([], t) : t
    }

    function c(t) {
        return t.replace(/::/g, "/").replace(/([A-Z]+)([A-Z][a-z])/g, "$1_$2").replace(/([a-z\d])([A-Z])/g, "$1_$2").replace(/_/g, "-").toLowerCase()
    }

    function l(t) {
        return t in Z ? Z[t] : Z[t] = new RegExp("(^|\\s)" + t + "(\\s|$)")
    }

    function f(t, n) {
        return "number" != typeof n || L[c(t)] ? n : n + "px"
    }

    function h(t) {
        var n, e;
        return k[t] || (n = A.createElement(t), A.body.appendChild(n), e = getComputedStyle(n, "").getPropertyValue("display"), n.parentNode.removeChild(n), "none" == e && (e = "block"), k[t] = e), k[t]
    }

    function p(t) {
        return "children" in t ? S.call(t.children) : j.map(t.childNodes, function (t) {
            return 1 == t.nodeType ? t : void 0
        })
    }

    function d(t, n, e) {
        for (E in n) e && (o(n[E]) || G(n[E])) ? (o(n[E]) && !o(t[E]) && (t[E] = {}), G(n[E]) && !G(t[E]) && (t[E] = []), d(t[E], n[E], e)) : n[E] !== w && (t[E] = n[E])
    }

    function m(t, n) {
        return null == n ? j(t) : j(t).filter(n)
    }

    function v(t, e, i, r) {
        return n(e) ? e.call(t, i, r) : e
    }

    function g(t, n, e) {
        null == e ? t.removeAttribute(n) : t.setAttribute(n, e)
    }

    function y(t, n) {
        var e = t.className || "",
            i = e && e.baseVal !== w;
        return n === w ? i ? e.baseVal : e : void(i ? e.baseVal = n : t.className = n)
    }

    function x(t) {
        try {
            return t ? "true" == t || ("false" == t ? !1 : "null" == t ? null : +t + "" == t ? +t : /^[\[\{]/.test(t) ? j.parseJSON(t) : t) : t
        } catch (n) {
            return t
        }
    }

    function b(t, n) {
        n(t);
        for (var e = 0, i = t.childNodes.length; i > e; e++) b(t.childNodes[e], n)
    }
    var w, E, j, T, C, N, O = [],
        S = O.slice,
        P = O.filter,
        A = window.document,
        k = {},
        Z = {},
        L = {
            "column-count": 1,
            columns: 1,
            "font-weight": 1,
            "line-height": 1,
            opacity: 1,
            "z-index": 1,
            zoom: 1
        },
        $ = /^\s*<(\w+|!)[^>]*>/,
        D = /^<(\w+)\s*\/?>(?:<\/\1>|)$/,
        F = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,
        z = /^(?:body|html)$/i,
        q = /([A-Z])/g,
        M = ["val", "css", "html", "text", "data", "width", "height", "offset"],
        R = ["after", "prepend", "before", "append"],
        _ = A.createElement("table"),
        W = A.createElement("tr"),
        H = {
            tr: A.createElement("tbody"),
            tbody: _,
            thead: _,
            tfoot: _,
            td: W,
            th: W,
            "*": A.createElement("div")
        },
        I = /complete|loaded|interactive/,
        V = /^[\w-]*$/,
        X = {},
        B = X.toString,
        U = {},
        J = A.createElement("div"),
        Y = {
            tabindex: "tabIndex",
            readonly: "readOnly",
            "for": "htmlFor",
            "class": "className",
            maxlength: "maxLength",
            cellspacing: "cellSpacing",
            cellpadding: "cellPadding",
            rowspan: "rowSpan",
            colspan: "colSpan",
            usemap: "useMap",
            frameborder: "frameBorder",
            contenteditable: "contentEditable"
        },
        G = Array.isArray || function (t) {
            return t instanceof Array
        };
    return U.matches = function (t, n) {
        if (!n || !t || 1 !== t.nodeType) return !1;
        var e = t.webkitMatchesSelector || t.mozMatchesSelector || t.oMatchesSelector || t.matchesSelector;
        if (e) return e.call(t, n);
        var i, r = t.parentNode,
            o = !r;
        return o && (r = J).appendChild(t), i = ~U.qsa(r, n).indexOf(t), o && J.removeChild(t), i
    }, C = function (t) {
        return t.replace(/-+(.)?/g, function (t, n) {
            return n ? n.toUpperCase() : ""
        })
    }, N = function (t) {
        return P.call(t, function (n, e) {
            return t.indexOf(n) == e
        })
    }, U.fragment = function (t, n, e) {
        var i, r, a;
        return D.test(t) && (i = j(A.createElement(RegExp.$1))), i || (t.replace && (t = t.replace(F, "<$1></$2>")), n === w && (n = $.test(t) && RegExp.$1), n in H || (n = "*"), a = H[n], a.innerHTML = "" + t, i = j.each(S.call(a.childNodes), function () {
            a.removeChild(this)
        })), o(e) && (r = j(i), j.each(e, function (t, n) {
            M.indexOf(t) > -1 ? r[t](n) : r.attr(t, n)
        })), i
    }, U.Z = function (t, n) {
        return t = t || [], t.__proto__ = j.fn, t.selector = n || "", t
    }, U.isZ = function (t) {
        return t instanceof U.Z
    }, U.init = function (t, e) {
        var i;
        if (!t) return U.Z();
        if ("string" == typeof t)
            if (t = t.trim(), "<" == t[0] && $.test(t)) i = U.fragment(t, RegExp.$1, e), t = null;
            else {
                if (e !== w) return j(e).find(t);
                i = U.qsa(A, t)
            } else {
            if (n(t)) return j(A).ready(t);
            if (U.isZ(t)) return t;
            if (G(t)) i = s(t);
            else if (r(t)) i = [t], t = null;
            else if ($.test(t)) i = U.fragment(t.trim(), RegExp.$1, e), t = null;
            else {
                if (e !== w) return j(e).find(t);
                i = U.qsa(A, t)
            }
        }
        return U.Z(i, t)
    }, j = function (t, n) {
        return U.init(t, n)
    }, j.extend = function (t) {
        var n, e = S.call(arguments, 1);
        return "boolean" == typeof t && (n = t, t = e.shift()), e.forEach(function (e) {
            d(t, e, n)
        }), t
    }, U.qsa = function (t, n) {
        var e, r = "#" == n[0],
            o = !r && "." == n[0],
            a = r || o ? n.slice(1) : n,
            s = V.test(a);
        return i(t) && s && r ? (e = t.getElementById(a)) ? [e] : [] : 1 !== t.nodeType && 9 !== t.nodeType ? [] : S.call(s && !r ? o ? t.getElementsByClassName(a) : t.getElementsByTagName(n) : t.querySelectorAll(n))
    }, j.contains = A.documentElement.contains ? function (t, n) {
        return t !== n && t.contains(n)
    } : function (t, n) {
        for (; n && (n = n.parentNode);)
            if (n === t) return !0;
        return !1
    }, j.type = t, j.isFunction = n, j.isWindow = e, j.isArray = G, j.isPlainObject = o, j.isEmptyObject = function (t) {
        var n;
        for (n in t) return !1;
        return !0
    }, j.inArray = function (t, n, e) {
        return O.indexOf.call(n, t, e)
    }, j.camelCase = C, j.trim = function (t) {
        return null == t ? "" : String.prototype.trim.call(t)
    }, j.uuid = 0, j.support = {}, j.expr = {}, j.map = function (t, n) {
        var e, i, r, o = [];
        if (a(t))
            for (i = 0; i < t.length; i++) e = n(t[i], i), null != e && o.push(e);
        else
            for (r in t) e = n(t[r], r), null != e && o.push(e);
        return u(o)
    }, j.each = function (t, n) {
        var e, i;
        if (a(t)) {
            for (e = 0; e < t.length; e++)
                if (n.call(t[e], e, t[e]) === !1) return t
        } else
            for (i in t)
                if (n.call(t[i], i, t[i]) === !1) return t; return t
    }, j.grep = function (t, n) {
        return P.call(t, n)
    }, window.JSON && (j.parseJSON = JSON.parse), j.each("Boolean Number String Function Array Date RegExp Object Error".split(" "), function (t, n) {
        X["[object " + n + "]"] = n.toLowerCase()
    }), j.fn = {
        forEach: O.forEach,
        reduce: O.reduce,
        push: O.push,
        sort: O.sort,
        indexOf: O.indexOf,
        concat: O.concat,
        map: function (t) {
            return j(j.map(this, function (n, e) {
                return t.call(n, e, n)
            }))
        }, slice: function () {
            return j(S.apply(this, arguments))
        }, ready: function (t) {
            return I.test(A.readyState) && A.body ? t(j) : A.addEventListener("DOMContentLoaded", function () {
                t(j)
            }, !1), this
        }, get: function (t) {
            return t === w ? S.call(this) : this[t >= 0 ? t : t + this.length]
        }, toArray: function () {
            return this.get()
        }, size: function () {
            return this.length
        }, remove: function () {
            return this.each(function () {
                null != this.parentNode && this.parentNode.removeChild(this)
            })
        }, each: function (t) {
            return O.every.call(this, function (n, e) {
                return t.call(n, e, n) !== !1
            }), this
        }, filter: function (t) {
            return n(t) ? this.not(this.not(t)) : j(P.call(this, function (n) {
                return U.matches(n, t)
            }))
        }, add: function (t, n) {
            return j(N(this.concat(j(t, n))))
        }, is: function (t) {
            return this.length > 0 && U.matches(this[0], t)
        }, not: function (t) {
            var e = [];
            if (n(t) && t.call !== w) this.each(function (n) {
                t.call(this, n) || e.push(this)
            });
            else {
                var i = "string" == typeof t ? this.filter(t) : a(t) && n(t.item) ? S.call(t) : j(t);
                this.forEach(function (t) {
                    i.indexOf(t) < 0 && e.push(t)
                })
            }
            return j(e)
        }, has: function (t) {
            return this.filter(function () {
                return r(t) ? j.contains(this, t) : j(this).find(t).size()
            })
        }, eq: function (t) {
            return -1 === t ? this.slice(t) : this.slice(t, +t + 1)
        }, first: function () {
            var t = this[0];
            return t && !r(t) ? t : j(t)
        }, last: function () {
            var t = this[this.length - 1];
            return t && !r(t) ? t : j(t)
        }, find: function (t) {
            var n, e = this;
            return n = t ? "object" == typeof t ? j(t).filter(function () {
                var t = this;
                return O.some.call(e, function (n) {
                    return j.contains(n, t)
                })
            }) : 1 == this.length ? j(U.qsa(this[0], t)) : this.map(function () {
                return U.qsa(this, t)
            }) : j()
        }, closest: function (t, n) {
            var e = this[0],
                r = !1;
            for ("object" == typeof t && (r = j(t)); e && !(r ? r.indexOf(e) >= 0 : U.matches(e, t));) e = e !== n && !i(e) && e.parentNode;
            return j(e)
        }, parents: function (t) {
            for (var n = [], e = this; e.length > 0;) e = j.map(e, function (t) {
                return (t = t.parentNode) && !i(t) && n.indexOf(t) < 0 ? (n.push(t), t) : void 0
            });
            return m(n, t)
        }, parent: function (t) {
            return m(N(this.pluck("parentNode")), t)
        }, children: function (t) {
            return m(this.map(function () {
                return p(this)
            }), t)
        }, contents: function () {
            return this.map(function () {
                return S.call(this.childNodes)
            })
        }, siblings: function (t) {
            return m(this.map(function (t, n) {
                return P.call(p(n.parentNode), function (t) {
                    return t !== n
                })
            }), t)
        }, empty: function () {
            return this.each(function () {
                this.innerHTML = ""
            })
        }, pluck: function (t) {
            return j.map(this, function (n) {
                return n[t]
            })
        }, show: function () {
            return this.each(function () {
                "none" == this.style.display && (this.style.display = ""), "none" == getComputedStyle(this, "").getPropertyValue("display") && (this.style.display = h(this.nodeName))
            })
        }, replaceWith: function (t) {
            return this.before(t).remove()
        }, wrap: function (t) {
            var e = n(t);
            if (this[0] && !e) var i = j(t).get(0),
                r = i.parentNode || this.length > 1;
            return this.each(function (n) {
                j(this).wrapAll(e ? t.call(this, n) : r ? i.cloneNode(!0) : i)
            })
        }, wrapAll: function (t) {
            if (this[0]) {
                j(this[0]).before(t = j(t));
                for (var n;
                    (n = t.children()).length;) t = n.first();
                j(t).append(this)
            }
            return this
        }, wrapInner: function (t) {
            var e = n(t);
            return this.each(function (n) {
                var i = j(this),
                    r = i.contents(),
                    o = e ? t.call(this, n) : t;
                r.length ? r.wrapAll(o) : i.append(o)
            })
        }, unwrap: function () {
            return this.parent().each(function () {
                j(this).replaceWith(j(this).children())
            }), this
        }, clone: function () {
            return this.map(function () {
                return this.cloneNode(!0)
            })
        }, hide: function () {
            return this.css("display", "none")
        }, toggle: function (t) {
            return this.each(function () {
                var n = j(this);
                (t === w ? "none" == n.css("display") : t) ? n.show(): n.hide()
            })
        }, prev: function (t) {
            return j(this.pluck("previousElementSibling")).filter(t || "*")
        }, next: function (t) {
            return j(this.pluck("nextElementSibling")).filter(t || "*")
        }, html: function (t) {
            return 0 in arguments ? this.each(function (n) {
                var e = this.innerHTML;
                j(this).empty().append(v(this, t, n, e))
            }) : 0 in this ? this[0].innerHTML : null
        }, text: function (t) {
            return 0 in arguments ? this.each(function (n) {
                var e = v(this, t, n, this.textContent);
                this.textContent = null == e ? "" : "" + e
            }) : 0 in this ? this[0].textContent : null
        }, attr: function (t, n) {
            var e;
            return "string" != typeof t || 1 in arguments ? this.each(function (e) {
                if (1 === this.nodeType)
                    if (r(t))
                        for (E in t) g(this, E, t[E]);
                    else g(this, t, v(this, n, e, this.getAttribute(t)))
            }) : this.length && 1 === this[0].nodeType ? !(e = this[0].getAttribute(t)) && t in this[0] ? this[0][t] : e : w
        }, removeAttr: function (t) {
            return this.each(function () {
                1 === this.nodeType && t.split(" ").forEach(function (t) {
                    g(this, t)
                }, this)
            })
        }, prop: function (t, n) {
            return t = Y[t] || t, 1 in arguments ? this.each(function (e) {
                this[t] = v(this, n, e, this[t])
            }) : this[0] && this[0][t]
        }, data: function (t, n) {
            var e = "data-" + t.replace(q, "-$1").toLowerCase(),
                i = 1 in arguments ? this.attr(e, n) : this.attr(e);
            return null !== i ? x(i) : w
        }, val: function (t) {
            return 0 in arguments ? this.each(function (n) {
                this.value = v(this, t, n, this.value)
            }) : this[0] && (this[0].multiple ? j(this[0]).find("option").filter(function () {
                return this.selected
            }).pluck("value") : this[0].value)
        }, offset: function (t) {
            if (t) return this.each(function (n) {
                var e = j(this),
                    i = v(this, t, n, e.offset()),
                    r = e.offsetParent().offset(),
                    o = {
                        top: i.top - r.top,
                        left: i.left - r.left
                    };
                "static" == e.css("position") && (o.position = "relative"), e.css(o)
            });
            if (!this.length) return null;
            var n = this[0].getBoundingClientRect();
            return {
                left: n.left + window.pageXOffset,
                top: n.top + window.pageYOffset,
                width: Math.round(n.width),
                height: Math.round(n.height)
            }
        }, css: function (n, e) {
            if (arguments.length < 2) {
                var i, r = this[0];
                if (!r) return;
                if (i = getComputedStyle(r, ""), "string" == typeof n) return r.style[C(n)] || i.getPropertyValue(n);
                if (G(n)) {
                    var o = {};
                    return j.each(n, function (t, n) {
                        o[n] = r.style[C(n)] || i.getPropertyValue(n)
                    }), o
                }
            }
            var a = "";
            if ("string" == t(n)) e || 0 === e ? a = c(n) + ":" + f(n, e) : this.each(function () {
                this.style.removeProperty(c(n))
            });
            else
                for (E in n) n[E] || 0 === n[E] ? a += c(E) + ":" + f(E, n[E]) + ";" : this.each(function () {
                    this.style.removeProperty(c(E))
                });
            return this.each(function () {
                this.style.cssText += ";" + a
            })
        }, index: function (t) {
            return t ? this.indexOf(j(t)[0]) : this.parent().children().indexOf(this[0])
        }, hasClass: function (t) {
            return t ? O.some.call(this, function (t) {
                return this.test(y(t))
            }, l(t)) : !1
        }, addClass: function (t) {
            return t ? this.each(function (n) {
                if ("className" in this) {
                    T = [];
                    var e = y(this),
                        i = v(this, t, n, e);
                    i.split(/\s+/g).forEach(function (t) {
                        j(this).hasClass(t) || T.push(t)
                    }, this), T.length && y(this, e + (e ? " " : "") + T.join(" "))
                }
            }) : this
        }, removeClass: function (t) {
            return this.each(function (n) {
                if ("className" in this) {
                    if (t === w) return y(this, "");
                    T = y(this), v(this, t, n, T).split(/\s+/g).forEach(function (t) {
                        T = T.replace(l(t), " ")
                    }), y(this, T.trim())
                }
            })
        }, toggleClass: function (t, n) {
            return t ? this.each(function (e) {
                var i = j(this),
                    r = v(this, t, e, y(this));
                r.split(/\s+/g).forEach(function (t) {
                    (n === w ? !i.hasClass(t) : n) ? i.addClass(t): i.removeClass(t)
                })
            }) : this
        }, scrollTop: function (t) {
            if (this.length) {
                var n = "scrollTop" in this[0];
                return t === w ? n ? this[0].scrollTop : this[0].pageYOffset : this.each(n ? function () {
                    this.scrollTop = t
                } : function () {
                    this.scrollTo(this.scrollX, t)
                })
            }
        }, scrollLeft: function (t) {
            if (this.length) {
                var n = "scrollLeft" in this[0];
                return t === w ? n ? this[0].scrollLeft : this[0].pageXOffset : this.each(n ? function () {
                    this.scrollLeft = t
                } : function () {
                    this.scrollTo(t, this.scrollY)
                })
            }
        }, position: function () {
            if (this.length) {
                var t = this[0],
                    n = this.offsetParent(),
                    e = this.offset(),
                    i = z.test(n[0].nodeName) ? {
                        top: 0,
                        left: 0
                    } : n.offset();
                return e.top -= parseFloat(j(t).css("margin-top")) || 0, e.left -= parseFloat(j(t).css("margin-left")) || 0, i.top += parseFloat(j(n[0]).css("border-top-width")) || 0, i.left += parseFloat(j(n[0]).css("border-left-width")) || 0, {
                    top: e.top - i.top,
                    left: e.left - i.left
                }
            }
        }, offsetParent: function () {
            return this.map(function () {
                for (var t = this.offsetParent || A.body; t && !z.test(t.nodeName) && "static" == j(t).css("position");) t = t.offsetParent;
                return t
            })
        }
    }, j.fn.detach = j.fn.remove, ["width", "height"].forEach(function (t) {
        var n = t.replace(/./, function (t) {
            return t[0].toUpperCase()
        });
        j.fn[t] = function (r) {
            var o, a = this[0];
            return r === w ? e(a) ? a["inner" + n] : i(a) ? a.documentElement["scroll" + n] : (o = this.offset()) && o[t] : this.each(function (n) {
                a = j(this), a.css(t, v(this, r, n, a[t]()))
            })
        }
    }), R.forEach(function (n, e) {
        var i = e % 2;
        j.fn[n] = function () {
            var n, r, o = j.map(arguments, function (e) {
                    return n = t(e), "object" == n || "array" == n || null == e ? e : U.fragment(e)
                }),
                a = this.length > 1;
            return o.length < 1 ? this : this.each(function (t, n) {
                r = i ? n : n.parentNode, n = 0 == e ? n.nextSibling : 1 == e ? n.firstChild : 2 == e ? n : null;
                var s = j.contains(A.documentElement, r);
                o.forEach(function (t) {
                    if (a) t = t.cloneNode(!0);
                    else if (!r) return j(t).remove();
                    r.insertBefore(t, n), s && b(t, function (t) {
                        null == t.nodeName || "SCRIPT" !== t.nodeName.toUpperCase() || t.type && "text/javascript" !== t.type || t.src || window.eval.call(window, t.innerHTML)
                    })
                })
            })
        }, j.fn[i ? n + "To" : "insert" + (e ? "Before" : "After")] = function (t) {
            return j(t)[n](this), this
        }
    }), U.Z.prototype = j.fn, U.uniq = N, U.deserializeValue = x, j.zepto = U, j
}();
window.Zepto = Zepto, void 0 === window.$ && (window.$ = Zepto), ! function (t) {
    function n(t) {
        return t._zid || (t._zid = h++)
    }

    function e(t, e, o, a) {
        if (e = i(e), e.ns) var s = r(e.ns);
        return (v[n(t)] || []).filter(function (t) {
            return t && (!e.e || t.e == e.e) && (!e.ns || s.test(t.ns)) && (!o || n(t.fn) === n(o)) && (!a || t.sel == a)
        })
    }

    function i(t) {
        var n = ("" + t).split(".");
        return {
            e: n[0],
            ns: n.slice(1).sort().join(" ")
        }
    }

    function r(t) {
        return new RegExp("(?:^| )" + t.replace(" ", " .* ?") + "(?: |$)")
    }

    function o(t, n) {
        return t.del && !y && t.e in x || !!n
    }

    function a(t) {
        return b[t] || y && x[t] || t
    }

    function s(e, r, s, u, l, h, p) {
        var d = n(e),
            m = v[d] || (v[d] = []);
        r.split(/\s/).forEach(function (n) {
            if ("ready" == n) return t(document).ready(s);
            var r = i(n);
            r.fn = s, r.sel = l, r.e in b && (s = function (n) {
                var e = n.relatedTarget;
                return !e || e !== this && !t.contains(this, e) ? r.fn.apply(this, arguments) : void 0
            }), r.del = h;
            var d = h || s;
            r.proxy = function (t) {
                if (t = c(t), !t.isImmediatePropagationStopped()) {
                    t.data = u;
                    var n = d.apply(e, t._args == f ? [t] : [t].concat(t._args));
                    return n === !1 && (t.preventDefault(), t.stopPropagation()), n
                }
            }, r.i = m.length, m.push(r), "addEventListener" in e && e.addEventListener(a(r.e), r.proxy, o(r, p))
        })
    }

    function u(t, i, r, s, u) {
        var c = n(t);
        (i || "").split(/\s/).forEach(function (n) {
            e(t, n, r, s).forEach(function (n) {
                delete v[c][n.i], "removeEventListener" in t && t.removeEventListener(a(n.e), n.proxy, o(n, u))
            })
        })
    }

    function c(n, e) {
        return !e && n.isDefaultPrevented || (e || (e = n), t.each(T, function (t, i) {
            var r = e[t];
            n[t] = function () {
                return this[i] = w, r && r.apply(e, arguments)
            }, n[i] = E
        }), (e.defaultPrevented !== f ? e.defaultPrevented : "returnValue" in e ? e.returnValue === !1 : e.getPreventDefault && e.getPreventDefault()) && (n.isDefaultPrevented = w)), n
    }

    function l(t) {
        var n, e = {
            originalEvent: t
        };
        for (n in t) j.test(n) || t[n] === f || (e[n] = t[n]);
        return c(e, t)
    }
    var f, h = 1,
        p = Array.prototype.slice,
        d = t.isFunction,
        m = function (t) {
            return "string" == typeof t
        },
        v = {},
        g = {},
        y = "onfocusin" in window,
        x = {
            focus: "focusin",
            blur: "focusout"
        },
        b = {
            mouseenter: "mouseover",
            mouseleave: "mouseout"
        };
    g.click = g.mousedown = g.mouseup = g.mousemove = "MouseEvents", t.event = {
        add: s,
        remove: u
    }, t.proxy = function (e, i) {
        var r = 2 in arguments && p.call(arguments, 2);
        if (d(e)) {
            var o = function () {
                return e.apply(i, r ? r.concat(p.call(arguments)) : arguments)
            };
            return o._zid = n(e), o
        }
        if (m(i)) return r ? (r.unshift(e[i], e), t.proxy.apply(null, r)) : t.proxy(e[i], e);
        throw new TypeError("expected function")
    }, t.fn.bind = function (t, n, e) {
        return this.on(t, n, e)
    }, t.fn.unbind = function (t, n) {
        return this.off(t, n)
    }, t.fn.one = function (t, n, e, i) {
        return this.on(t, n, e, i, 1)
    };
    var w = function () {
            return !0
        },
        E = function () {
            return !1
        },
        j = /^([A-Z]|returnValue$|layer[XY]$)/,
        T = {
            preventDefault: "isDefaultPrevented",
            stopImmediatePropagation: "isImmediatePropagationStopped",
            stopPropagation: "isPropagationStopped"
        };
    t.fn.delegate = function (t, n, e) {
        return this.on(n, t, e)
    }, t.fn.undelegate = function (t, n, e) {
        return this.off(n, t, e)
    }, t.fn.live = function (n, e) {
        return t(document.body).delegate(this.selector, n, e), this
    }, t.fn.die = function (n, e) {
        return t(document.body).undelegate(this.selector, n, e), this
    }, t.fn.on = function (n, e, i, r, o) {
        var a, c, h = this;
        return n && !m(n) ? (t.each(n, function (t, n) {
            h.on(t, e, i, n, o)
        }), h) : (m(e) || d(r) || r === !1 || (r = i, i = e, e = f), (d(i) || i === !1) && (r = i, i = f), r === !1 && (r = E), h.each(function (f, h) {
            o && (a = function (t) {
                return u(h, t.type, r), r.apply(this, arguments)
            }), e && (c = function (n) {
                var i, o = t(n.target).closest(e, h).get(0);
                return o && o !== h ? (i = t.extend(l(n), {
                    currentTarget: o,
                    liveFired: h
                }), (a || r).apply(o, [i].concat(p.call(arguments, 1)))) : void 0
            }), s(h, n, r, i, e, c || a)
        }))
    }, t.fn.off = function (n, e, i) {
        var r = this;
        return n && !m(n) ? (t.each(n, function (t, n) {
            r.off(t, e, n)
        }), r) : (m(e) || d(i) || i === !1 || (i = e, e = f), i === !1 && (i = E), r.each(function () {
            u(this, n, i, e)
        }))
    }, t.fn.trigger = function (n, e) {
        return n = m(n) || t.isPlainObject(n) ? t.Event(n) : c(n), n._args = e, this.each(function () {
            n.type in x && "function" == typeof this[n.type] ? this[n.type]() : "dispatchEvent" in this ? this.dispatchEvent(n) : t(this).triggerHandler(n, e)
        })
    }, t.fn.triggerHandler = function (n, i) {
        var r, o;
        return this.each(function (a, s) {
            r = l(m(n) ? t.Event(n) : n), r._args = i, r.target = s, t.each(e(s, n.type || n), function (t, n) {
                return o = n.proxy(r), r.isImmediatePropagationStopped() ? !1 : void 0
            })
        }), o
    }, "focusin focusout focus blur load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select keydown keypress keyup error".split(" ").forEach(function (n) {
        t.fn[n] = function (t) {
            return 0 in arguments ? this.bind(n, t) : this.trigger(n)
        }
    }), t.Event = function (t, n) {
        m(t) || (n = t, t = n.type);
        var e = document.createEvent(g[t] || "Events"),
            i = !0;
        if (n)
            for (var r in n) "bubbles" == r ? i = !!n[r] : e[r] = n[r];
        return e.initEvent(t, i, !0), c(e)
    }
}(Zepto), ! function (t) {
    function n(n, e, i) {
        var r = t.Event(e);
        return t(n).trigger(r, i), !r.isDefaultPrevented()
    }

    function e(t, e, i, r) {
        return t.global ? n(e || y, i, r) : void 0
    }

    function i(n) {
        n.global && 0 === t.active++ && e(n, null, "ajaxStart")
    }

    function r(n) {
        n.global && !--t.active && e(n, null, "ajaxStop")
    }

    function o(t, n) {
        var i = n.context;
        return n.beforeSend.call(i, t, n) === !1 || e(n, i, "ajaxBeforeSend", [t, n]) === !1 ? !1 : void e(n, i, "ajaxSend", [t, n])
    }

    function a(t, n, i, r) {
        var o = i.context,
            a = "success";
        i.success.call(o, t, a, n), r && r.resolveWith(o, [t, a, n]), e(i, o, "ajaxSuccess", [n, i, t]), u(a, n, i)
    }

    function s(t, n, i, r, o) {
        var a = r.context;
        r.error.call(a, i, n, t), o && o.rejectWith(a, [i, n, t]), e(r, a, "ajaxError", [i, r, t || n]), u(n, i, r)
    }

    function u(t, n, i) {
        var o = i.context;
        i.complete.call(o, n, t), e(i, o, "ajaxComplete", [n, i]), r(i)
    }

    function c() {}

    function l(t) {
        return t && (t = t.split(";", 2)[0]), t && (t == j ? "html" : t == E ? "json" : b.test(t) ? "script" : w.test(t) && "xml") || "text"
    }

    function f(t, n) {
        return "" == n ? t : (t + "&" + n).replace(/[&?]{1,2}/, "?")
    }

    function h(n) {
        n.processData && n.data && "string" != t.type(n.data) && (n.data = t.param(n.data, n.traditional)), !n.data || n.type && "GET" != n.type.toUpperCase() || (n.url = f(n.url, n.data), n.data = void 0)
    }

    function p(n, e, i, r) {
        return t.isFunction(e) && (r = i, i = e, e = void 0), t.isFunction(i) || (r = i, i = void 0), {
            url: n,
            data: e,
            success: i,
            dataType: r
        }
    }

    function d(n, e, i, r) {
        var o, a = t.isArray(e),
            s = t.isPlainObject(e);
        t.each(e, function (e, u) {
            o = t.type(u), r && (e = i ? r : r + "[" + (s || "object" == o || "array" == o ? e : "") + "]"), !r && a ? n.add(u.name, u.value) : "array" == o || !i && "object" == o ? d(n, u, i, e) : n.add(e, u)
        })
    }
    var m, v, g = 0,
        y = window.document,
        x = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
        b = /^(?:text|application)\/javascript/i,
        w = /^(?:text|application)\/xml/i,
        E = "application/json",
        j = "text/html",
        T = /^\s*$/,
        C = y.createElement("a");
    C.href = window.location.href, t.active = 0, t.ajaxJSONP = function (n, e) {
        if (!("type" in n)) return t.ajax(n);
        var i, r, u = n.jsonpCallback,
            c = (t.isFunction(u) ? u() : u) || "jsonp" + ++g,
            l = y.createElement("script"),
            f = window[c],
            h = function (n) {
                t(l).triggerHandler("error", n || "abort")
            },
            p = {
                abort: h
            };
        return e && e.promise(p), t(l).on("load error", function (o, u) {
            clearTimeout(r), t(l).off().remove(), "error" != o.type && i ? a(i[0], p, n, e) : s(null, u || "error", p, n, e), window[c] = f, i && t.isFunction(f) && f(i[0]), f = i = void 0
        }), o(p, n) === !1 ? (h("abort"), p) : (window[c] = function () {
            i = arguments
        }, l.src = n.url.replace(/\?(.+)=\?/, "?$1=" + c), y.head.appendChild(l), n.timeout > 0 && (r = setTimeout(function () {
            h("timeout")
        }, n.timeout)), p)
    }, t.ajaxSettings = {
        type: "GET",
        beforeSend: c,
        success: c,
        error: c,
        complete: c,
        context: null,
        global: !0,
        xhr: function () {
            return new window.XMLHttpRequest
        }, accepts: {
            script: "text/javascript, application/javascript, application/x-javascript",
            json: E,
            xml: "application/xml, text/xml",
            html: j,
            text: "text/plain"
        }, crossDomain: !1,
        timeout: 0,
        processData: !0,
        cache: !0
    }, t.ajax = function (n) {
        var e, r = t.extend({}, n || {}),
            u = t.Deferred && t.Deferred();
        for (m in t.ajaxSettings) void 0 === r[m] && (r[m] = t.ajaxSettings[m]);
        i(r), r.crossDomain || (e = y.createElement("a"), e.href = r.url, e.href = e.href, r.crossDomain = C.protocol + "//" + C.host != e.protocol + "//" + e.host), r.url || (r.url = window.location.toString()), h(r);
        var p = r.dataType,
            d = /\?.+=\?/.test(r.url);
        if (d && (p = "jsonp"), r.cache !== !1 && (n && n.cache === !0 || "script" != p && "jsonp" != p) || (r.url = f(r.url, "_=" + Date.now())), "jsonp" == p) return d || (r.url = f(r.url, r.jsonp ? r.jsonp + "=?" : r.jsonp === !1 ? "" : "callback=?")), t.ajaxJSONP(r, u);
        var g, x = r.accepts[p],
            b = {},
            w = function (t, n) {
                b[t.toLowerCase()] = [t, n]
            },
            E = /^([\w-]+:)\/\//.test(r.url) ? RegExp.$1 : window.location.protocol,
            j = r.xhr(),
            N = j.setRequestHeader;
        if (u && u.promise(j), r.crossDomain || w("X-Requested-With", "XMLHttpRequest"), w("Accept", x || "*/*"), (x = r.mimeType || x) && (x.indexOf(",") > -1 && (x = x.split(",", 2)[0]), j.overrideMimeType && j.overrideMimeType(x)), (r.contentType || r.contentType !== !1 && r.data && "GET" != r.type.toUpperCase()) && w("Content-Type", r.contentType || "application/x-www-form-urlencoded"), r.headers)
            for (v in r.headers) w(v, r.headers[v]);
        if (j.setRequestHeader = w, j.onreadystatechange = function () {
            if (4 == j.readyState) {
                j.onreadystatechange = c, clearTimeout(g);
                var n, e = !1;
                if (j.status >= 200 && j.status < 300 || 304 == j.status || 0 == j.status && "file:" == E) {
                    p = p || l(r.mimeType || j.getResponseHeader("content-type")), n = j.responseText;
                    try {
                        "script" == p ? (0, eval)(n) : "xml" == p ? n = j.responseXML : "json" == p && (n = T.test(n) ? null : t.parseJSON(n))
                    } catch (i) {
                        e = i
                    }
                    e ? s(e, "parsererror", j, r, u) : a(n, j, r, u)
                } else s(j.statusText || null, j.status ? "error" : "abort", j, r, u)
            }
        }, o(j, r) === !1) return j.abort(), s(null, "abort", j, r, u), j;
        if (r.xhrFields)
            for (v in r.xhrFields) j[v] = r.xhrFields[v];
        var O = "async" in r ? r.async : !0;
        j.open(r.type, r.url, O, r.username, r.password);
        for (v in b) N.apply(j, b[v]);
        return r.timeout > 0 && (g = setTimeout(function () {
            j.onreadystatechange = c, j.abort(), s(null, "timeout", j, r, u)
        }, r.timeout)), j.send(r.data ? r.data : null), j
    }, t.get = function () {
        return t.ajax(p.apply(null, arguments))
    }, t.post = function () {
        var n = p.apply(null, arguments);
        return n.type = "POST", t.ajax(n)
    }, t.getJSON = function () {
        var n = p.apply(null, arguments);
        return n.dataType = "json", t.ajax(n)
    }, t.fn.load = function (n, e, i) {
        if (!this.length) return this;
        var r, o = this,
            a = n.split(/\s/),
            s = p(n, e, i),
            u = s.success;
        return a.length > 1 && (s.url = a[0], r = a[1]), s.success = function (n) {
            o.html(r ? t("<div>").html(n.replace(x, "")).find(r) : n), u && u.apply(o, arguments)
        }, t.ajax(s), this
    };
    var N = encodeURIComponent;
    t.param = function (n, e) {
        var i = [];
        return i.add = function (n, e) {
            t.isFunction(e) && (e = e()), null == e && (e = ""), this.push(N(n) + "=" + N(e))
        }, d(i, n, e), i.join("&").replace(/%20/g, "+")
    }
}(Zepto), ! function (t) {
    t.fn.serializeArray = function () {
        var n, e, i = [],
            r = function (t) {
                return t.forEach ? t.forEach(r) : void i.push({
                    name: n,
                    value: t
                })
            };
        return this[0] && t.each(this[0].elements, function (i, o) {
            e = o.type, n = o.name, n && "fieldset" != o.nodeName.toLowerCase() && !o.disabled && "submit" != e && "reset" != e && "button" != e && "file" != e && ("radio" != e && "checkbox" != e || o.checked) && r(t(o).val())
        }), i
    }, t.fn.serialize = function () {
        var t = [];
        return this.serializeArray().forEach(function (n) {
            t.push(encodeURIComponent(n.name) + "=" + encodeURIComponent(n.value))
        }), t.join("&")
    }, t.fn.submit = function (n) {
        if (0 in arguments) this.bind("submit", n);
        else if (this.length) {
            var e = t.Event("submit");
            this.eq(0).trigger(e), e.isDefaultPrevented() || this.get(0).submit()
        }
        return this
    }
}(Zepto), ! function (t, n) {
    function e(t) {
        return t.replace(/([a-z])([A-Z])/, "$1-$2").toLowerCase()
    }

    function i(t) {
        return r ? r + t : t.toLowerCase()
    }
    var r, o, a, s, u, c, l, f, h, p, d = "",
        m = {
            Webkit: "webkit",
            Moz: "",
            O: "o"
        },
        v = window.document,
        g = v.createElement("div"),
        y = /^((translate|rotate|scale)(X|Y|Z|3d)?|matrix(3d)?|perspective|skew(X|Y)?)$/i,
        x = {};
    t.each(m, function (t, e) {
        return g.style[t + "TransitionProperty"] !== n ? (d = "-" + t.toLowerCase() + "-", r = e, !1) : void 0
    }), o = d + "transform", x[a = d + "transition-property"] = x[s = d + "transition-duration"] = x[c = d + "transition-delay"] = x[u = d + "transition-timing-function"] = x[l = d + "animation-name"] = x[f = d + "animation-duration"] = x[p = d + "animation-delay"] = x[h = d + "animation-timing-function"] = "", t.fx = {
        off: r === n && g.style.transitionProperty === n,
        speeds: {
            _default: 400,
            fast: 200,
            slow: 600
        },
        cssPrefix: d,
        transitionEnd: i("TransitionEnd"),
        animationEnd: i("AnimationEnd")
    }, t.fn.animate = function (e, i, r, o, a) {
        return t.isFunction(i) && (o = i, r = n, i = n), t.isFunction(r) && (o = r, r = n), t.isPlainObject(i) && (r = i.easing, o = i.complete, a = i.delay, i = i.duration), i && (i = ("number" == typeof i ? i : t.fx.speeds[i] || t.fx.speeds._default) / 1e3), a && (a = parseFloat(a) / 1e3), this.anim(e, i, r, o, a)
    }, t.fn.anim = function (i, r, d, m, v) {
        var g, b, w, E = {},
            j = "",
            T = this,
            C = t.fx.transitionEnd,
            N = !1;
        if (r === n && (r = t.fx.speeds._default / 1e3), v === n && (v = 0), t.fx.off && (r = 0), "string" == typeof i) E[l] = i, E[f] = r + "s", E[p] = v + "s", E[h] = d || "linear", C = t.fx.animationEnd;
        else {
            b = [];
            for (g in i) y.test(g) ? j += g + "(" + i[g] + ") " : (E[g] = i[g], b.push(e(g)));
            j && (E[o] = j, b.push(o)), r > 0 && "object" == typeof i && (E[a] = b.join(", "), E[s] = r + "s", E[c] = v + "s", E[u] = d || "linear")
        }
        return w = function (n) {
            if ("undefined" != typeof n) {
                if (n.target !== n.currentTarget) return;
                t(n.target).unbind(C, w)
            } else t(this).unbind(C, w);
            N = !0, t(this).css(x), m && m.call(this)
        }, r > 0 && (this.bind(C, w), setTimeout(function () {
            N || w.call(T)
        }, 1e3 * (r + v) + 25)), this.size() && this.get(0).clientLeft, this.css(E), 0 >= r && setTimeout(function () {
            T.each(function () {
                w.call(this)
            })
        }, 0), this
    }, g = null
}(Zepto), ! function (t, n) {
    function e(e, i, r, o, a) {
        "function" != typeof i || a || (a = i, i = n);
        var s = {
            opacity: r
        };
        return o && (s.scale = o, e.css(t.fx.cssPrefix + "transform-origin", "0 0")), e.animate(s, i, null, a)
    }

    function i(n, i, r, o) {
        return e(n, i, 0, r, function () {
            a.call(t(this)), o && o.call(this)
        })
    }
    var r = window.document,
        o = (r.documentElement, t.fn.show),
        a = t.fn.hide,
        s = t.fn.toggle;
    t.fn.show = function (t, i) {
        return o.call(this), t === n ? t = 0 : this.css("opacity", 0), e(this, t, 1, "1,1", i)
    }, t.fn.hide = function (t, e) {
        return t === n ? a.call(this) : i(this, t, "0,0", e)
    }, t.fn.toggle = function (e, i) {
        return e === n || "boolean" == typeof e ? s.call(this, e) : this.each(function () {
            var n = t(this);
            n["none" == n.css("display") ? "show" : "hide"](e, i)
        })
    }, t.fn.fadeTo = function (t, n, i) {
        return e(this, t, n, null, i)
    }, t.fn.fadeIn = function (t, n) {
        var e = this.css("opacity");
        return e > 0 ? this.css("opacity", 0) : e = 1, o.call(this).fadeTo(t, e, n)
    }, t.fn.fadeOut = function (t, n) {
        return i(this, t, null, n)
    }, t.fn.fadeToggle = function (n, e) {
        return this.each(function () {
            var i = t(this);
            i[0 == i.css("opacity") || "none" == i.css("display") ? "fadeIn" : "fadeOut"](n, e)
        })
    }
}(Zepto), ! function (t) {
    function n(n, i) {
        var s = n[a],
            u = s && r[s];
        if (void 0 === i) return u || e(n);
        if (u) {
            if (i in u) return u[i];
            var c = i;
            if (c in u) return u[c]
        }
        return o.call(t(n), i)
    }

    function e(n, e, o) {
        var s = n[a] || (n[a] = ++t.uuid),
            u = r[s] || (r[s] = i(n));
        return void 0 !== e && (u[e] = o), u
    }

    function i(n) {
        var e = {};
        return t.each(n.attributes || s, function (n, i) {
            0 == i.name.indexOf("data-") && (e[i.name.replace("data-", "")] = t.zepto.deserializeValue(i.value))
        }), e
    }
    var r = {},
        o = t.fn.data,
        a = (t.camelCase, t.expando = "Zepto" + +new Date),
        s = [];
    t.fn.data = function (i, r) {
        return void 0 === r ? t.isPlainObject(i) ? this.each(function (n, r) {
            t.each(i, function (t, n) {
                e(r, t, n)
            })
        }) : 0 in this ? n(this[0], i) : void 0 : this.each(function () {
            e(this, i, r)
        })
    }, t.fn.removeData = function (n) {
        return "string" == typeof n && (n = n.split(/\s+/)), this.each(function () {
            var e = this[a],
                i = e && r[e];
            i && t.each(n || i, function (t) {
                delete i[n ? this : t]
            })
        })
    }, ["remove", "empty"].forEach(function (n) {
        var e = t.fn[n];
        t.fn[n] = function () {
            var t = this.find("*");
            return "remove" === n && (t = t.add(this)), t.removeData(), e.call(this)
        }
    })
}(Zepto), ! function (t) {
    function n(n) {
        return n = t(n), !(!n.width() && !n.height()) && "none" !== n.css("display")
    }

    function e(t, n) {
        t = t.replace(/=#\]/g, '="#"]');
        var e, i, r = s.exec(t);
        if (r && r[2] in a && (e = a[r[2]], i = r[3], t = r[1], i)) {
            var o = Number(i);
            i = isNaN(o) ? i.replace(/^["']|["']$/g, "") : o
        }
        return n(t, e, i)
    }
    var i = t.zepto,
        r = i.qsa,
        o = i.matches,
        a = t.expr[":"] = {
            visible: function () {
                return n(this) ? this : void 0
            }, hidden: function () {
                return n(this) ? void 0 : this
            }, selected: function () {
                return this.selected ? this : void 0
            }, checked: function () {
                return this.checked ? this : void 0
            }, parent: function () {
                return this.parentNode
            }, first: function (t) {
                return 0 === t ? this : void 0
            }, last: function (t, n) {
                return t === n.length - 1 ? this : void 0
            }, eq: function (t, n, e) {
                return t === e ? this : void 0
            }, contains: function (n, e, i) {
                return t(this).text().indexOf(i) > -1 ? this : void 0
            }, has: function (t, n, e) {
                return i.qsa(this, e).length ? this : void 0
            }
        },
        s = new RegExp("(.*):(\\w+)(?:\\(([^)]+)\\))?$\\s*"),
        u = /^\s*>/,
        c = "Zepto" + +new Date;
    i.qsa = function (n, o) {
        return e(o, function (e, a, s) {
            try {
                var l;
                !e && a ? e = "*" : u.test(e) && (l = t(n).addClass(c), e = "." + c + " " + e);
                var f = r(n, e)
            } catch (h) {
                throw console.error("error performing selector: %o", o), h
            } finally {
                l && l.removeClass(c)
            }
            return a ? i.uniq(t.map(f, function (t, n) {
                return a.call(t, n, f, s)
            })) : f
        })
    }, i.matches = function (t, n) {
        return e(n, function (n, e, i) {
            return (!n || o(t, n)) && (!e || e.call(t, null, i) === t)
        })
    }
}(Zepto), ! function (t) {
    function n(e) {
        var i = [
                ["resolve", "done", t.Callbacks({
                    once: 1,
                    memory: 1
                }), "resolved"],
                ["reject", "fail", t.Callbacks({
                    once: 1,
                    memory: 1
                }), "rejected"],
                ["notify", "progress", t.Callbacks({
                    memory: 1
                })]
            ],
            r = "pending",
            o = {
                state: function () {
                    return r
                }, always: function () {
                    return a.done(arguments).fail(arguments), this
                }, then: function () {
                    var e = arguments;
                    return n(function (n) {
                        t.each(i, function (i, r) {
                            var s = t.isFunction(e[i]) && e[i];
                            a[r[1]](function () {
                                var e = s && s.apply(this, arguments);
                                if (e && t.isFunction(e.promise)) e.promise().done(n.resolve).fail(n.reject).progress(n.notify);
                                else {
                                    var i = this === o ? n.promise() : this,
                                        a = s ? [e] : arguments;
                                    n[r[0] + "With"](i, a)
                                }
                            })
                        }), e = null
                    }).promise()
                }, promise: function (n) {
                    return null != n ? t.extend(n, o) : o
                }
            },
            a = {};
        return t.each(i, function (t, n) {
            var e = n[2],
                s = n[3];
            o[n[1]] = e.add, s && e.add(function () {
                r = s
            }, i[1 ^ t][2].disable, i[2][2].lock), a[n[0]] = function () {
                return a[n[0] + "With"](this === a ? o : this, arguments), this
            }, a[n[0] + "With"] = e.fireWith
        }), o.promise(a), e && e.call(a, a), a
    }
    var e = Array.prototype.slice;
    t.when = function (i) {
        var r, o, a, s = e.call(arguments),
            u = s.length,
            c = 0,
            l = 1 !== u || i && t.isFunction(i.promise) ? u : 0,
            f = 1 === l ? i : n(),
            h = function (t, n, i) {
                return function (o) {
                    n[t] = this, i[t] = arguments.length > 1 ? e.call(arguments) : o, i === r ? f.notifyWith(n, i) : --l || f.resolveWith(n, i)
                }
            };
        if (u > 1)
            for (r = new Array(u), o = new Array(u), a = new Array(u); u > c; ++c) s[c] && t.isFunction(s[c].promise) ? s[c].promise().done(h(c, a, s)).fail(f.reject).progress(h(c, o, r)) : --l;
        return l || f.resolveWith(a, s), f.promise()
    }, t.Deferred = n
}(Zepto), ! function (t) {
    t.Callbacks = function (n) {
        n = t.extend({}, n);
        var e, i, r, o, a, s, u = [],
            c = !n.once && [],
            l = function (t) {
                for (e = n.memory && t, i = !0, s = o || 0, o = 0, a = u.length, r = !0; u && a > s; ++s)
                    if (u[s].apply(t[0], t[1]) === !1 && n.stopOnFalse) {
                        e = !1;
                        break
                    }
                r = !1, u && (c ? c.length && l(c.shift()) : e ? u.length = 0 : f.disable())
            },
            f = {
                add: function () {
                    if (u) {
                        var i = u.length,
                            s = function (e) {
                                t.each(e, function (t, e) {
                                    "function" == typeof e ? n.unique && f.has(e) || u.push(e) : e && e.length && "string" != typeof e && s(e);
                                })
                            };
                        s(arguments), r ? a = u.length : e && (o = i, l(e))
                    }
                    return this
                }, remove: function () {
                    return u && t.each(arguments, function (n, e) {
                        for (var i;
                            (i = t.inArray(e, u, i)) > -1;) u.splice(i, 1), r && (a >= i && --a, s >= i && --s)
                    }), this
                }, has: function (n) {
                    return !(!u || !(n ? t.inArray(n, u) > -1 : u.length))
                }, empty: function () {
                    return a = u.length = 0, this
                }, disable: function () {
                    return u = c = e = void 0, this
                }, disabled: function () {
                    return !u
                }, lock: function () {
                    return c = void 0, e || f.disable(), this
                }, locked: function () {
                    return !c
                }, fireWith: function (t, n) {
                    return !u || i && !c || (n = n || [], n = [t, n.slice ? n.slice() : n], r ? c.push(n) : l(n)), this
                }, fire: function () {
                    return f.fireWith(this, arguments)
                }, fired: function () {
                    return !!i
                }
            };
        return f
    }
}(Zepto);
$.smVersion = "0.6.2", + function (t) {
        "use strict";
        var e = {
            autoInit: !1,
            showPageLoadingIndicator: !0,
            router: !1,
            swipePanel: "left",
            swipePanelOnlyClose: !0
        };
        t.smConfig = t.extend(e, t.config)
    }(Zepto), + function (t) {
        "use strict";
        t.compareVersion = function (t, e) {
            var n = t.split("."),
                i = e.split(".");
            if (t === e) return 0;
            for (var o = 0; o < n.length; o++) {
                var a = parseInt(n[o]);
                if (!i[o]) return 1;
                var s = parseInt(i[o]);
                if (s > a) return -1;
                if (a > s) return 1
            }
            return -1
        }, t.getCurrentPage = function () {
            return t(".page-current")[0] || t(".page")[0] || document.body
        }
    }(Zepto),
    function (t) {
        "use strict";

        function e(t, e) {
            function n(t) {
                if (t.target === this)
                    for (e.call(this, t), i = 0; i < o.length; i++) a.off(o[i], n)
            }
            var i, o = t,
                a = this;
            if (e)
                for (i = 0; i < o.length; i++) a.on(o[i], n)
        }["width", "height"].forEach(function (e) {
            var n = e.replace(/./, function (t) {
                return t[0].toUpperCase()
            });
            t.fn["outer" + n] = function (t) {
                var n = this;
                if (n) {
                    var i = n[e](),
                        o = {
                            width: ["left", "right"],
                            height: ["top", "bottom"]
                        };
                    return o[e].forEach(function (e) {
                        t && (i += parseInt(n.css("margin-" + e), 10))
                    }), i
                }
                return null
            }
        }), t.support = function () {
            var t = {
                touch: !!("ontouchstart" in window || window.DocumentTouch && document instanceof window.DocumentTouch)
            };
            return t
        }(), t.touchEvents = {
            start: t.support.touch ? "touchstart" : "mousedown",
            move: t.support.touch ? "touchmove" : "mousemove",
            end: t.support.touch ? "touchend" : "mouseup"
        }, t.getTranslate = function (t, e) {
            var n, i, o, a;
            return "undefined" == typeof e && (e = "x"), o = window.getComputedStyle(t, null), window.WebKitCSSMatrix ? a = new WebKitCSSMatrix("none" === o.webkitTransform ? "" : o.webkitTransform) : (a = o.MozTransform || o.transform || o.getPropertyValue("transform").replace("translate(", "matrix(1, 0, 0, 1,"), n = a.toString().split(",")), "x" === e && (i = window.WebKitCSSMatrix ? a.m41 : 16 === n.length ? parseFloat(n[12]) : parseFloat(n[4])), "y" === e && (i = window.WebKitCSSMatrix ? a.m42 : 16 === n.length ? parseFloat(n[13]) : parseFloat(n[5])), i || 0
        }, t.requestAnimationFrame = function (t) {
            return requestAnimationFrame ? requestAnimationFrame(t) : webkitRequestAnimationFrame ? webkitRequestAnimationFrame(t) : mozRequestAnimationFrame ? mozRequestAnimationFrame(t) : setTimeout(t, 1e3 / 60)
        }, t.cancelAnimationFrame = function (t) {
            return cancelAnimationFrame ? cancelAnimationFrame(t) : webkitCancelAnimationFrame ? webkitCancelAnimationFrame(t) : mozCancelAnimationFrame ? mozCancelAnimationFrame(t) : clearTimeout(t)
        }, t.fn.dataset = function () {
            var e = {},
                n = this[0].dataset;
            for (var i in n) {
                var o = e[i] = n[i];
                "false" === o ? e[i] = !1 : "true" === o ? e[i] = !0 : parseFloat(o) === 1 * o && (e[i] = 1 * o)
            }
            return t.extend({}, e, this[0].__eleData)
        }, t.fn.animationEnd = function (t) {
            return e.call(this, ["webkitAnimationEnd", "animationend"], t), this
        }, t.fn.transitionEnd = function (t) {
            return e.call(this, ["webkitTransitionEnd", "transitionend"], t), this
        }, t.fn.transition = function (t) {
            "string" != typeof t && (t += "ms");
            for (var e = 0; e < this.length; e++) {
                var n = this[e].style;
                n.webkitTransitionDuration = n.MozTransitionDuration = n.transitionDuration = t
            }
            return this
        }, t.fn.transform = function (t) {
            for (var e = 0; e < this.length; e++) {
                var n = this[e].style;
                n.webkitTransform = n.MozTransform = n.transform = t
            }
            return this
        }, t.fn.prevAll = function (e) {
            var n = [],
                i = this[0];
            if (!i) return t([]);
            for (; i.previousElementSibling;) {
                var o = i.previousElementSibling;
                e ? t(o).is(e) && n.push(o) : n.push(o), i = o
            }
            return t(n)
        }, t.fn.nextAll = function (e) {
            var n = [],
                i = this[0];
            if (!i) return t([]);
            for (; i.nextElementSibling;) {
                var o = i.nextElementSibling;
                e ? t(o).is(e) && n.push(o) : n.push(o), i = o
            }
            return t(n)
        }, t.fn.show = function () {
            function t(t) {
                var n, i;
                return e[t] || (n = document.createElement(t), document.body.appendChild(n), i = getComputedStyle(n, "").getPropertyValue("display"), n.parentNode.removeChild(n), "none" === i && (i = "block"), e[t] = i), e[t]
            }
            var e = {};
            return this.each(function () {
                "none" === this.style.display && (this.style.display = ""), "none" === getComputedStyle(this, "").getPropertyValue("display"), this.style.display = t(this.nodeName)
            })
        }
    }(Zepto),
    function (t) {
        "use strict";
        var e = {},
            n = navigator.userAgent,
            i = n.match(/(Android);?[\s\/]+([\d.]+)?/),
            o = n.match(/(iPad).*OS\s([\d_]+)/),
            a = n.match(/(iPod)(.*OS\s([\d_]+))?/),
            s = !o && n.match(/(iPhone\sOS)\s([\d_]+)/);
        if (e.ios = e.android = e.iphone = e.ipad = e.androidChrome = !1, i && (e.os = "android", e.osVersion = i[2], e.android = !0, e.androidChrome = n.toLowerCase().indexOf("chrome") >= 0), (o || s || a) && (e.os = "ios", e.ios = !0), s && !a && (e.osVersion = s[2].replace(/_/g, "."), e.iphone = !0), o && (e.osVersion = o[2].replace(/_/g, "."), e.ipad = !0), a && (e.osVersion = a[3] ? a[3].replace(/_/g, ".") : null, e.iphone = !0), e.ios && e.osVersion && n.indexOf("Version/") >= 0 && "10" === e.osVersion.split(".")[0] && (e.osVersion = n.toLowerCase().split("version/")[1].split(" ")[0]), e.webView = (s || o || a) && n.match(/.*AppleWebKit(?!.*Safari)/i), e.os && "ios" === e.os) {
            var r = e.osVersion.split(".");
            e.minimalUi = !e.webView && (a || s) && (1 * r[0] === 7 ? 1 * r[1] >= 1 : 1 * r[0] > 7) && t('meta[name="viewport"]').length > 0 && t('meta[name="viewport"]').attr("content").indexOf("minimal-ui") >= 0
        }
        var l = t(window).width(),
            c = t(window).height();
        e.statusBar = !1, e.webView && l * c === screen.width * screen.height ? e.statusBar = !0 : e.statusBar = !1;
        var h = [];
        if (e.pixelRatio = window.devicePixelRatio || 1, h.push("pixel-ratio-" + Math.floor(e.pixelRatio)), e.pixelRatio >= 2 && h.push("retina"), e.os && (h.push(e.os, e.os + "-" + e.osVersion.split(".")[0], e.os + "-" + e.osVersion.replace(/\./g, "-")), "ios" === e.os))
            for (var p = parseInt(e.osVersion.split(".")[0], 10), d = p - 1; d >= 6; d--) h.push("ios-gt-" + d);
        e.statusBar ? h.push("with-statusbar-overlay") : t("html").removeClass("with-statusbar-overlay"), h.length > 0 && t("html").addClass(h.join(" ")), e.isWeixin = /MicroMessenger/i.test(n), t.device = e
    }(Zepto),
    function () {
        "use strict";

        function t(e, i) {
            function o(t, e) {
                return function () {
                    return t.apply(e, arguments)
                }
            }
            var a;
            if (i = i || {}, this.trackingClick = !1, this.trackingClickStart = 0, this.targetElement = null, this.touchStartX = 0, this.touchStartY = 0, this.lastTouchIdentifier = 0, this.touchBoundary = i.touchBoundary || 10, this.layer = e, this.tapDelay = i.tapDelay || 200, this.tapTimeout = i.tapTimeout || 700, !t.notNeeded(e)) {
                for (var s = ["onMouse", "onClick", "onTouchStart", "onTouchMove", "onTouchEnd", "onTouchCancel"], r = this, l = 0, c = s.length; c > l; l++) r[s[l]] = o(r[s[l]], r);
                n && (e.addEventListener("mouseover", this.onMouse, !0), e.addEventListener("mousedown", this.onMouse, !0), e.addEventListener("mouseup", this.onMouse, !0)), e.addEventListener("click", this.onClick, !0), e.addEventListener("touchstart", this.onTouchStart, !1), e.addEventListener("touchmove", this.onTouchMove, !1), e.addEventListener("touchend", this.onTouchEnd, !1), e.addEventListener("touchcancel", this.onTouchCancel, !1), Event.prototype.stopImmediatePropagation || (e.removeEventListener = function (t, n, i) {
                    var o = Node.prototype.removeEventListener;
                    "click" === t ? o.call(e, t, n.hijacked || n, i) : o.call(e, t, n, i)
                }, e.addEventListener = function (t, n, i) {
                    var o = Node.prototype.addEventListener;
                    "click" === t ? o.call(e, t, n.hijacked || (n.hijacked = function (t) {
                        t.propagationStopped || n(t)
                    }), i) : o.call(e, t, n, i)
                }), "function" == typeof e.onclick && (a = e.onclick, e.addEventListener("click", function (t) {
                    a(t)
                }, !1), e.onclick = null)
            }
        }
        var e = navigator.userAgent.indexOf("Windows Phone") >= 0,
            n = navigator.userAgent.indexOf("Android") > 0 && !e,
            i = /iP(ad|hone|od)/.test(navigator.userAgent) && !e,
            o = i && /OS 4_\d(_\d)?/.test(navigator.userAgent),
            a = i && /OS [6-7]_\d/.test(navigator.userAgent),
            s = navigator.userAgent.indexOf("BB10") > 0,
            r = !1;
        t.prototype.needsClick = function (t) {
            for (var e = t; e && "BODY" !== e.tagName.toUpperCase();) {
                if ("LABEL" === e.tagName.toUpperCase() && (r = !0, /\bneedsclick\b/.test(e.className))) return !0;
                e = e.parentNode
            }
            switch (t.nodeName.toLowerCase()) {
            case "button":
            case "select":
            case "textarea":
                if (t.disabled) return !0;
                break;
            case "input":
                if (i && "file" === t.type || t.disabled) return !0;
                break;
            case "label":
            case "iframe":
            case "video":
                return !0
            }
            return /\bneedsclick\b/.test(t.className)
        }, t.prototype.needsFocus = function (t) {
            switch (t.nodeName.toLowerCase()) {
            case "textarea":
                return !0;
            case "select":
                return !n;
            case "input":
                switch (t.type) {
                case "button":
                case "checkbox":
                case "file":
                case "image":
                case "radio":
                case "submit":
                    return !1
                }
                return !t.disabled && !t.readOnly;
            default:
                return /\bneedsfocus\b/.test(t.className)
            }
        }, t.prototype.sendClick = function (t, e) {
            var n, i;
            document.activeElement && document.activeElement !== t && document.activeElement.blur(), i = e.changedTouches[0], n = document.createEvent("MouseEvents"), n.initMouseEvent(this.determineEventType(t), !0, !0, window, 1, i.screenX, i.screenY, i.clientX, i.clientY, !1, !1, !1, !1, 0, null), n.forwardedTouchEvent = !0, t.dispatchEvent(n)
        }, t.prototype.determineEventType = function (t) {
            return n && "select" === t.tagName.toLowerCase() ? "mousedown" : "click"
        }, t.prototype.focus = function (t) {
            var e, n = ["date", "time", "month", "number", "email"];
            i && t.setSelectionRange && -1 === n.indexOf(t.type) ? (e = t.value.length, t.setSelectionRange(e, e)) : t.focus()
        }, t.prototype.updateScrollParent = function (t) {
            var e, n;
            if (e = t.fastClickScrollParent, !e || !e.contains(t)) {
                n = t;
                do {
                    if (n.scrollHeight > n.offsetHeight) {
                        e = n, t.fastClickScrollParent = n;
                        break
                    }
                    n = n.parentElement
                } while (n)
            }
            e && (e.fastClickLastScrollTop = e.scrollTop)
        }, t.prototype.getTargetElementFromEventTarget = function (t) {
            return t.nodeType === Node.TEXT_NODE ? t.parentNode : t
        }, t.prototype.onTouchStart = function (t) {
            var e, n, a;
            if (t.targetTouches.length > 1) return !0;
            if (e = this.getTargetElementFromEventTarget(t.target), n = t.targetTouches[0], i) {
                if (a = window.getSelection(), a.rangeCount && !a.isCollapsed) return !0;
                if (!o) {
                    if (n.identifier && n.identifier === this.lastTouchIdentifier) return t.preventDefault(), !1;
                    this.lastTouchIdentifier = n.identifier, this.updateScrollParent(e)
                }
            }
            return this.trackingClick = !0, this.trackingClickStart = t.timeStamp, this.targetElement = e, this.touchStartX = n.pageX, this.touchStartY = n.pageY, t.timeStamp - this.lastClickTime < this.tapDelay && t.preventDefault(), !0
        }, t.prototype.touchHasMoved = function (t) {
            var e = t.changedTouches[0],
                n = this.touchBoundary;
            return Math.abs(e.pageX - this.touchStartX) > n || Math.abs(e.pageY - this.touchStartY) > n
        }, t.prototype.onTouchMove = function (t) {
            return this.trackingClick ? ((this.targetElement !== this.getTargetElementFromEventTarget(t.target) || this.touchHasMoved(t)) && (this.trackingClick = !1, this.targetElement = null), !0) : !0
        }, t.prototype.findControl = function (t) {
            return void 0 !== t.control ? t.control : t.htmlFor ? document.getElementById(t.htmlFor) : t.querySelector("button, input:not([type=hidden]), keygen, meter, output, progress, select, textarea")
        }, t.prototype.onTouchEnd = function (t) {
            var e, s, r, l, c, h = this.targetElement;
            if (!this.trackingClick) return !0;
            if (t.timeStamp - this.lastClickTime < this.tapDelay) return this.cancelNextClick = !0, !0;
            if (t.timeStamp - this.trackingClickStart > this.tapTimeout) return !0;
            var p = ["date", "time", "month"];
            if (-1 !== p.indexOf(t.target.type)) return !1;
            if (this.cancelNextClick = !1, this.lastClickTime = t.timeStamp, s = this.trackingClickStart, this.trackingClick = !1, this.trackingClickStart = 0, a && (c = t.changedTouches[0], h = document.elementFromPoint(c.pageX - window.pageXOffset, c.pageY - window.pageYOffset) || h, h.fastClickScrollParent = this.targetElement.fastClickScrollParent), r = h.tagName.toLowerCase(), "label" === r) {
                if (e = this.findControl(h)) {
                    if (this.focus(h), n) return !1;
                    h = e
                }
            } else if (this.needsFocus(h)) return t.timeStamp - s > 100 || i && window.top !== window && "input" === r ? (this.targetElement = null, !1) : (this.focus(h), this.sendClick(h, t), i && "select" === r || (this.targetElement = null, t.preventDefault()), !1);
            return i && !o && (l = h.fastClickScrollParent, l && l.fastClickLastScrollTop !== l.scrollTop) ? !0 : (this.needsClick(h) || (t.preventDefault(), this.sendClick(h, t)), !1)
        }, t.prototype.onTouchCancel = function () {
            this.trackingClick = !1, this.targetElement = null
        }, t.prototype.onMouse = function (t) {
            return this.targetElement ? t.forwardedTouchEvent ? !0 : t.cancelable && (!this.needsClick(this.targetElement) || this.cancelNextClick) ? (t.stopImmediatePropagation ? t.stopImmediatePropagation() : t.propagationStopped = !0, t.stopPropagation(), r || t.preventDefault(), !1) : !0 : !0
        }, t.prototype.onClick = function (t) {
            var e;
            return this.trackingClick ? (this.targetElement = null, this.trackingClick = !1, !0) : "submit" === t.target.type && 0 === t.detail ? !0 : (e = this.onMouse(t), e || (this.targetElement = null), e)
        }, t.prototype.destroy = function () {
            var t = this.layer;
            n && (t.removeEventListener("mouseover", this.onMouse, !0), t.removeEventListener("mousedown", this.onMouse, !0), t.removeEventListener("mouseup", this.onMouse, !0)), t.removeEventListener("click", this.onClick, !0), t.removeEventListener("touchstart", this.onTouchStart, !1), t.removeEventListener("touchmove", this.onTouchMove, !1), t.removeEventListener("touchend", this.onTouchEnd, !1), t.removeEventListener("touchcancel", this.onTouchCancel, !1)
        }, t.notNeeded = function (t) {
            var e, i, o, a;
            if ("undefined" == typeof window.ontouchstart) return !0;
            if (i = +(/Chrome\/([0-9]+)/.exec(navigator.userAgent) || [, 0])[1]) {
                if (!n) return !0;
                if (e = document.querySelector("meta[name=viewport]")) {
                    if (-1 !== e.content.indexOf("user-scalable=no")) return !0;
                    if (i > 31 && document.documentElement.scrollWidth <= window.outerWidth) return !0
                }
            }
            if (s && (o = navigator.userAgent.match(/Version\/([0-9]*)\.([0-9]*)/), o[1] >= 10 && o[2] >= 3 && (e = document.querySelector("meta[name=viewport]")))) {
                if (-1 !== e.content.indexOf("user-scalable=no")) return !0;
                if (document.documentElement.scrollWidth <= window.outerWidth) return !0
            }
            return "none" === t.style.msTouchAction || "manipulation" === t.style.touchAction ? !0 : (a = +(/Firefox\/([0-9]+)/.exec(navigator.userAgent) || [, 0])[1], a >= 27 && (e = document.querySelector("meta[name=viewport]"), e && (-1 !== e.content.indexOf("user-scalable=no") || document.documentElement.scrollWidth <= window.outerWidth)) ? !0 : "none" === t.style.touchAction || "manipulation" === t.style.touchAction)
        }, t.attach = function (e, n) {
            return new t(e, n)
        }, window.FastClick = t
    }(), + function (t) {
        "use strict";

        function e(e) {
            var n, o = t(this),
                a = (o.attr("href"), o.dataset());
            o.hasClass("open-popup") && (n = a.popup ? a.popup : ".popup", t.popup(n)), o.hasClass("close-popup") && (n = a.popup ? a.popup : ".popup.modal-in", t.closeModal(n)), o.hasClass("modal-overlay") && (t(".modal.modal-in").length > 0 && i.modalCloseByOutside && t.closeModal(".modal.modal-in"), t(".actions-modal.modal-in").length > 0 && i.actionsCloseByOutside && t.closeModal(".actions-modal.modal-in")), o.hasClass("popup-overlay") && t(".popup.modal-in").length > 0 && i.popupCloseByOutside && t.closeModal(".popup.modal-in")
        }
        var n = document.createElement("div");
        t.modalStack = [], t.modalStackClearQueue = function () {
            t.modalStack.length && t.modalStack.shift()()
        }, t.modal = function (e) {
            e = e || {};
            var o = "",
                a = "";
            if (e.buttons && e.buttons.length > 0)
                for (var s = 0; s < e.buttons.length; s++) a += '<span class="modal-button' + (e.buttons[s].bold ? " modal-button-bold" : "") + '">' + e.buttons[s].text + "</span>";
            var r = e.extraClass || "",
                l = e.title ? '<div class="modal-title">' + e.title + "</div>" : "",
                c = e.text ? '<div class="modal-text">' + e.text + "</div>" : "",
                h = e.afterText ? e.afterText : "",
                p = e.buttons && 0 !== e.buttons.length ? "" : "modal-no-buttons",
                d = e.verticalButtons ? "modal-buttons-vertical" : "";
            o = '<div class="modal ' + r + " " + p + '"><div class="modal-inner">' + (l + c + h) + '</div><div class="modal-buttons ' + d + '">' + a + "</div></div>", n.innerHTML = o;
            var u = t(n).children();
            return t(i.modalContainer).append(u[0]), u.find(".modal-button").each(function (n, i) {
                t(i).on("click", function (i) {
                    e.buttons[n].close !== !1 && t.closeModal(u), e.buttons[n].onClick && e.buttons[n].onClick(u, i), e.onClick && e.onClick(u, n)
                })
            }), t.openModal(u), u[0]
        }, t.alert = function (e, n, o) {
            return "function" == typeof n && (o = arguments[1], n = void 0), t.modal({
                text: e || "",
                title: "undefined" == typeof n ? i.modalTitle : n,
                buttons: [{
                    text: i.modalButtonOk,
                    bold: !0,
                    onClick: o
                }]
            })
        }, t.confirm = function (e, n, o, a) {
            return "function" == typeof n && (a = arguments[2], o = arguments[1], n = void 0), t.modal({
                text: e || "",
                title: "undefined" == typeof n ? i.modalTitle : n,
                buttons: [{
                    text: i.modalButtonCancel,
                    onClick: a
                }, {
                    text: i.modalButtonOk,
                    bold: !0,
                    onClick: o
                }]
            })
        }, t.prompt = function (e, n, o, a) {
            return "function" == typeof n && (a = arguments[2], o = arguments[1], n = void 0), t.modal({
                text: e || "",
                title: "undefined" == typeof n ? i.modalTitle : n,
                afterText: '<input type="text" class="modal-text-input">',
                buttons: [{
                    text: i.modalButtonCancel
                }, {
                    text: i.modalButtonOk,
                    bold: !0
                }],
                onClick: function (e, n) {
                    0 === n && a && a(t(e).find(".modal-text-input").val()), 1 === n && o && o(t(e).find(".modal-text-input").val())
                }
            })
        }, t.modalLogin = function (e, n, o, a) {
            return "function" == typeof n && (a = arguments[2], o = arguments[1], n = void 0), t.modal({
                text: e || "",
                title: "undefined" == typeof n ? i.modalTitle : n,
                afterText: '<input type="text" name="modal-username" placeholder="' + i.modalUsernamePlaceholder + '" class="modal-text-input modal-text-input-double"><input type="password" name="modal-password" placeholder="' + i.modalPasswordPlaceholder + '" class="modal-text-input modal-text-input-double">',
                buttons: [{
                    text: i.modalButtonCancel
                }, {
                    text: i.modalButtonOk,
                    bold: !0
                }],
                onClick: function (e, n) {
                    var i = t(e).find('.modal-text-input[name="modal-username"]').val(),
                        s = t(e).find('.modal-text-input[name="modal-password"]').val();
                    0 === n && a && a(i, s), 1 === n && o && o(i, s)
                }
            })
        }, t.modalPassword = function (e, n, o, a) {
            return "function" == typeof n && (a = arguments[2], o = arguments[1], n = void 0), t.modal({
                text: e || "",
                title: "undefined" == typeof n ? i.modalTitle : n,
                afterText: '<input type="password" name="modal-password" placeholder="' + i.modalPasswordPlaceholder + '" class="modal-text-input">',
                buttons: [{
                    text: i.modalButtonCancel
                }, {
                    text: i.modalButtonOk,
                    bold: !0
                }],
                onClick: function (e, n) {
                    var i = t(e).find('.modal-text-input[name="modal-password"]').val();
                    0 === n && a && a(i), 1 === n && o && o(i)
                }
            })
        }, t.showPreloader = function (e) {
            return t.hidePreloader(), t.showPreloader.preloaderModal = t.modal({
                title: e || i.modalPreloaderTitle,
                text: '<div class="preloader"></div>'
            }), t.showPreloader.preloaderModal
        }, t.hidePreloader = function () {
            t.showPreloader.preloaderModal && t.closeModal(t.showPreloader.preloaderModal)
        }, t.showIndicator = function () {
            t(".preloader-indicator-modal")[0] || t(i.modalContainer).append('<div class="preloader-indicator-overlay"></div><div class="preloader-indicator-modal"><span class="preloader preloader-white"></span></div>')
        }, t.hideIndicator = function () {
            t(".preloader-indicator-overlay, .preloader-indicator-modal").remove()
        }, t.actions = function (e) {
            var o, a, s;
            e = e || [], e.length > 0 && !t.isArray(e[0]) && (e = [e]);
            for (var r, l = "", c = 0; c < e.length; c++)
                for (var h = 0; h < e[c].length; h++) {
                    0 === h && (l += '<div class="actions-modal-group">');
                    var p = e[c][h],
                        d = p.label ? "actions-modal-label" : "actions-modal-button";
                    p.bold && (d += " actions-modal-button-bold"), p.color && (d += " color-" + p.color), p.bg && (d += " bg-" + p.bg), p.disabled && (d += " disabled"), l += '<span class="' + d + '">' + p.text + "</span>", h === e[c].length - 1 && (l += "</div>")
                }
            r = '<div class="actions-modal">' + l + "</div>", n.innerHTML = r, o = t(n).children(), t(i.modalContainer).append(o[0]), a = ".actions-modal-group", s = ".actions-modal-button";
            var u = o.find(a);
            return u.each(function (n, i) {
                var a = n;
                t(i).children().each(function (n, i) {
                    var r, l = n,
                        c = e[a][l];
                    t(i).is(s) && (r = t(i)), r && r.on("click", function (e) {
                        c.close !== !1 && t.closeModal(o), c.onClick && c.onClick(o, e)
                    })
                })
            }), t.openModal(o), o[0]
        }, t.popup = function (e, n) {
            if ("undefined" == typeof n && (n = !0), "string" == typeof e && e.indexOf("<") >= 0) {
                var o = document.createElement("div");
                if (o.innerHTML = e.trim(), !(o.childNodes.length > 0)) return !1;
                e = o.childNodes[0], n && e.classList.add("remove-on-close"), t(i.modalContainer).append(e)
            }
            return e = t(e), 0 === e.length ? !1 : (e.show(), e.find(".content").scroller("refresh"), e.find("." + i.viewClass).length > 0 && t.sizeNavbars(e.find("." + i.viewClass)[0]), t.openModal(e), e[0])
        }, t.pickerModal = function (e, n) {
            if ("undefined" == typeof n && (n = !0), "string" == typeof e && e.indexOf("<") >= 0) {
                if (e = t(e), !(e.length > 0)) return !1;
                n && e.addClass("remove-on-close"), t(i.modalContainer).append(e[0])
            }
            return e = t(e), 0 === e.length ? !1 : (e.show(), t.openModal(e), e[0])
        }, t.loginScreen = function (e) {
            return e || (e = ".login-screen"), e = t(e), 0 === e.length ? !1 : (e.show(), e.find("." + i.viewClass).length > 0 && t.sizeNavbars(e.find("." + i.viewClass)[0]), t.openModal(e), e[0])
        }, t.toast = function (e, n, i) {
            var o = t('<div class="modal toast ' + (i || "") + '">' + e + "</div>").appendTo(document.body);
            t.openModal(o, function () {
                setTimeout(function () {
                    t.closeModal(o)
                }, n || 2e3)
            })
        }, t.openModal = function (e, n) {
            e = t(e);
            var o = e.hasClass("modal"),
                a = !e.hasClass("toast");
            if (t(".modal.modal-in:not(.modal-out)").length && i.modalStack && o && a) return void t.modalStack.push(function () {
                t.openModal(e, n)
            });
            var s = e.hasClass("popup"),
                r = e.hasClass("login-screen"),
                l = e.hasClass("picker-modal"),
                c = e.hasClass("toast");
            o && (e.show(), e.css({
                marginTop: -Math.round(e.outerHeight() / 2) + "px"
            })), c && e.css({
                marginLeft: -Math.round(e.outerWidth() / 2 / 1.185) + "px"
            });
            var h;
            r || l || c || (0 !== t(".modal-overlay").length || s || t(i.modalContainer).append('<div class="modal-overlay"></div>'), 0 === t(".popup-overlay").length && s && t(i.modalContainer).append('<div class="popup-overlay"></div>'), h = t(s ? ".popup-overlay" : ".modal-overlay"));
            e[0].clientLeft;
            return e.trigger("open"), l && t(i.modalContainer).addClass("with-picker-modal"), r || l || c || h.addClass("modal-overlay-visible"), e.removeClass("modal-out").addClass("modal-in").transitionEnd(function (t) {
                e.hasClass("modal-out") ? e.trigger("closed") : e.trigger("opened")
            }), "function" == typeof n && n.call(this), !0
        }, t.closeModal = function (e) {
            if (e = t(e || ".modal-in"), "undefined" == typeof e || 0 !== e.length) {
                var n = e.hasClass("modal"),
                    o = e.hasClass("popup"),
                    a = e.hasClass("toast"),
                    s = e.hasClass("login-screen"),
                    r = e.hasClass("picker-modal"),
                    l = e.hasClass("remove-on-close"),
                    c = t(o ? ".popup-overlay" : ".modal-overlay");
                return o ? e.length === t(".popup.modal-in").length && c.removeClass("modal-overlay-visible") : r || a || c.removeClass("modal-overlay-visible"), e.trigger("close"), r && (t(i.modalContainer).removeClass("with-picker-modal"), t(i.modalContainer).addClass("picker-modal-closing")), e.removeClass("modal-in").addClass("modal-out").transitionEnd(function (n) {
                    e.hasClass("modal-out") ? e.trigger("closed") : e.trigger("opened"), r && t(i.modalContainer).removeClass("picker-modal-closing"), o || s || r ? (e.removeClass("modal-out").hide(), l && e.length > 0 && e.remove()) : e.remove()
                }), n && i.modalStack && t.modalStackClearQueue(), !0
            }
        }, t(document).on("click", " .modal-overlay, .popup-overlay, .close-popup, .open-popup, .close-picker", e);
        var i = t.modal.prototype.defaults = {
            modalStack: !0,
            modalButtonOk: "确定",
            modalButtonCancel: "取消",
            modalPreloaderTitle: "加载中",
            modalContainer: document.body
        }
    }(Zepto), + function (t) {
        "use strict";
        var e = !1,
            n = function (n) {
                function i(t) {
                    t = new Date(t);
                    var e = t.getFullYear(),
                        n = t.getMonth(),
                        i = n + 1,
                        o = t.getDate(),
                        a = t.getDay();
                    return r.params.dateFormat.replace(/yyyy/g, e).replace(/yy/g, (e + "").substring(2)).replace(/mm/g, 10 > i ? "0" + i : i).replace(/m/g, i).replace(/MM/g, r.params.monthNames[n]).replace(/M/g, r.params.monthNamesShort[n]).replace(/dd/g, 10 > o ? "0" + o : o).replace(/d/g, o).replace(/DD/g, r.params.dayNames[a]).replace(/D/g, r.params.dayNamesShort[a])
                }

                function o(e) {
                    if (e.preventDefault(), t.device.isWeixin && t.device.android && r.params.inputReadOnly && (this.focus(), this.blur()), !r.opened && (r.open(), r.params.scrollToInput)) {
                        var n = r.input.parents(".content");
                        if (0 === n.length) return;
                        var i, o = parseInt(n.css("padding-top"), 10),
                            a = parseInt(n.css("padding-bottom"), 10),
                            s = n[0].offsetHeight - o - r.container.height(),
                            l = n[0].scrollHeight - o - r.container.height(),
                            c = r.input.offset().top - o + r.input[0].offsetHeight;
                        if (c > s) {
                            var h = n.scrollTop() + c - s;
                            h + s > l && (i = h + s - l + a, s === l && (i = r.container.height()), n.css({
                                "padding-bottom": i + "px"
                            })), n.scrollTop(h, 300)
                        }
                    }
                }

                function a(e) {
                    r.input && r.input.length > 0 ? e.target !== r.input[0] && 0 === t(e.target).parents(".picker-modal").length && r.close() : 0 === t(e.target).parents(".picker-modal").length && r.close()
                }

                function s() {
                    r.opened = !1, r.input && r.input.length > 0 && r.input.parents(".content").css({
                        "padding-bottom": ""
                    }), r.params.onClose && r.params.onClose(r), r.destroyCalendarEvents()
                }
                var r = this,
                    l = {
                        monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                        monthNamesShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                        dayNames: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
                        dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
                        firstDay: 1,
                        weekendDays: [0, 6],
                        multiple: !1,
                        dateFormat: "yyyy-mm-dd",
                        direction: "horizontal",
                        minDate: null,
                        maxDate: null,
                        touchMove: !0,
                        animate: !0,
                        closeOnSelect: !0,
                        monthPicker: !0,
                        monthPickerTemplate: '<div class="picker-calendar-month-picker"><a href="#" class="link icon-only picker-calendar-prev-month"><i class="icon icon-prev"></i></a><div class="current-month-value"></div><a href="#" class="link icon-only picker-calendar-next-month"><i class="icon icon-next"></i></a></div>',
                        yearPicker: !0,
                        yearPickerTemplate: '<div class="picker-calendar-year-picker"><a href="#" class="link icon-only picker-calendar-prev-year"><i class="icon icon-prev"></i></a><span class="current-year-value"></span><a href="#" class="link icon-only picker-calendar-next-year"><i class="icon icon-next"></i></a></div>',
                        weekHeader: !0,
                        scrollToInput: !0,
                        inputReadOnly: !0,
                        toolbar: !0,
                        toolbarCloseText: "Done",
                        toolbarTemplate: '<div class="toolbar"><div class="toolbar-inner">{{monthPicker}}{{yearPicker}}</div></div>'
                    };
                n = n || {};
                for (var c in l) "undefined" == typeof n[c] && (n[c] = l[c]);
                r.params = n, r.initialized = !1, r.inline = !!r.params.container, r.isH = "horizontal" === r.params.direction;
                var h = r.isH && e ? -1 : 1;
                return r.animating = !1, r.addValue = function (t) {
                    if (r.params.multiple) {
                        r.value || (r.value = []);
                        for (var e, n = 0; n < r.value.length; n++) new Date(t).getTime() === new Date(r.value[n]).getTime() && (e = n);
                        "undefined" == typeof e ? r.value.push(t) : r.value.splice(e, 1), r.updateValue()
                    } else r.value = [t], r.updateValue()
                }, r.setValue = function (t) {
                    r.value = t, r.updateValue()
                }, r.updateValue = function () {
                    r.wrapper.find(".picker-calendar-day-selected").removeClass("picker-calendar-day-selected");
                    var e, n;
                    for (e = 0; e < r.value.length; e++) {
                        var o = new Date(r.value[e]);
                        r.wrapper.find('.picker-calendar-day[data-date="' + o.getFullYear() + "-" + o.getMonth() + "-" + o.getDate() + '"]').addClass("picker-calendar-day-selected")
                    }
                    if (r.params.onChange && r.params.onChange(r, r.value, r.value.map(i)), r.input && r.input.length > 0) {
                        if (r.params.formatValue) n = r.params.formatValue(r, r.value);
                        else {
                            for (n = [], e = 0; e < r.value.length; e++) n.push(i(r.value[e]));
                            n = n.join(", ")
                        }
                        t(r.input).val(n), t(r.input).trigger("change")
                    }
                }, r.initCalendarEvents = function () {
                    function n(t) {
                        l || s || (s = !0, c = u = "touchstart" === t.type ? t.targetTouches[0].pageX : t.pageX, p = u = "touchstart" === t.type ? t.targetTouches[0].pageY : t.pageY, f = (new Date).getTime(), x = 0, b = !0, C = void 0, v = g = r.monthsTranslate)
                    }

                    function i(t) {
                        if (s) {
                            if (d = "touchmove" === t.type ? t.targetTouches[0].pageX : t.pageX, u = "touchmove" === t.type ? t.targetTouches[0].pageY : t.pageY, "undefined" == typeof C && (C = !!(C || Math.abs(u - p) > Math.abs(d - c))), r.isH && C) return void(s = !1);
                            if (t.preventDefault(), r.animating) return void(s = !1);
                            b = !1, l || (l = !0, y = r.wrapper[0].offsetWidth, w = r.wrapper[0].offsetHeight, r.wrapper.transition(0)), t.preventDefault(), T = r.isH ? d - c : u - p, x = T / (r.isH ? y : w), g = 100 * (r.monthsTranslate * h + x), r.wrapper.transform("translate3d(" + (r.isH ? g : 0) + "%, " + (r.isH ? 0 : g) + "%, 0)")
                        }
                    }

                    function o(t) {
                        return s && l ? (s = l = !1, m = (new Date).getTime(), 300 > m - f ? Math.abs(T) < 10 ? r.resetMonth() : T >= 10 ? e ? r.nextMonth() : r.prevMonth() : e ? r.prevMonth() : r.nextMonth() : -.5 >= x ? e ? r.prevMonth() : r.nextMonth() : x >= .5 ? e ? r.nextMonth() : r.prevMonth() : r.resetMonth(), void setTimeout(function () {
                            b = !0
                        }, 100)) : void(s = l = !1)
                    }

                    function a(e) {
                        if (b) {
                            var n = t(e.target).parents(".picker-calendar-day");
                            if (0 === n.length && t(e.target).hasClass("picker-calendar-day") && (n = t(e.target)), 0 !== n.length && (!n.hasClass("picker-calendar-day-selected") || r.params.multiple) && !n.hasClass("picker-calendar-day-disabled")) {
                                n.hasClass("picker-calendar-day-next") && r.nextMonth(), n.hasClass("picker-calendar-day-prev") && r.prevMonth();
                                var i = n.attr("data-year"),
                                    o = n.attr("data-month"),
                                    a = n.attr("data-day");
                                r.params.onDayClick && r.params.onDayClick(r, n[0], i, o, a), r.addValue(new Date(i, o, a).getTime()), r.params.closeOnSelect && r.close()
                            }
                        }
                    }
                    var s, l, c, p, d, u, f, m, v, g, y, w, x, T, C, b = !0;
                    r.container.find(".picker-calendar-prev-month").on("click", r.prevMonth), r.container.find(".picker-calendar-next-month").on("click", r.nextMonth), r.container.find(".picker-calendar-prev-year").on("click", r.prevYear), r.container.find(".picker-calendar-next-year").on("click", r.nextYear), r.wrapper.on("click", a), r.params.touchMove && (r.wrapper.on(t.touchEvents.start, n), r.wrapper.on(t.touchEvents.move, i), r.wrapper.on(t.touchEvents.end, o)), r.container[0].f7DestroyCalendarEvents = function () {
                        r.container.find(".picker-calendar-prev-month").off("click", r.prevMonth), r.container.find(".picker-calendar-next-month").off("click", r.nextMonth), r.container.find(".picker-calendar-prev-year").off("click", r.prevYear), r.container.find(".picker-calendar-next-year").off("click", r.nextYear), r.wrapper.off("click", a), r.params.touchMove && (r.wrapper.off(t.touchEvents.start, n), r.wrapper.off(t.touchEvents.move, i), r.wrapper.off(t.touchEvents.end, o))
                    }
                }, r.destroyCalendarEvents = function (t) {
                    "f7DestroyCalendarEvents" in r.container[0] && r.container[0].f7DestroyCalendarEvents()
                }, r.daysInMonth = function (t) {
                    var e = new Date(t);
                    return new Date(e.getFullYear(), e.getMonth() + 1, 0).getDate()
                }, r.monthHTML = function (t, e) {
                    t = new Date(t);
                    var n = t.getFullYear(),
                        i = t.getMonth();
                    t.getDate();
                    "next" === e && (t = 11 === i ? new Date(n + 1, 0) : new Date(n, i + 1, 1)), "prev" === e && (t = 0 === i ? new Date(n - 1, 11) : new Date(n, i - 1, 1)), "next" !== e && "prev" !== e || (i = t.getMonth(), n = t.getFullYear());
                    var o = r.daysInMonth(new Date(t.getFullYear(), t.getMonth()).getTime() - 864e6),
                        a = r.daysInMonth(t),
                        s = new Date(t.getFullYear(), t.getMonth()).getDay();
                    0 === s && (s = 7);
                    var l, c, h, p = [],
                        d = 6,
                        u = 7,
                        f = "",
                        m = 0 + (r.params.firstDay - 1),
                        v = (new Date).setHours(0, 0, 0, 0),
                        g = r.params.minDate ? new Date(r.params.minDate).getTime() : null,
                        y = r.params.maxDate ? new Date(r.params.maxDate).getTime() : null;
                    if (r.value && r.value.length)
                        for (c = 0; c < r.value.length; c++) p.push(new Date(r.value[c]).setHours(0, 0, 0, 0));
                    for (c = 1; d >= c; c++) {
                        var w = "";
                        for (h = 1; u >= h; h++) {
                            var x = h;
                            m++;
                            var T = m - s,
                                C = "";
                            0 > T ? (T = o + T + 1, C += " picker-calendar-day-prev", l = new Date(0 > i - 1 ? n - 1 : n, 0 > i - 1 ? 11 : i - 1, T).getTime()) : (T += 1, T > a ? (T -= a, C += " picker-calendar-day-next", l = new Date(i + 1 > 11 ? n + 1 : n, i + 1 > 11 ? 0 : i + 1, T).getTime()) : l = new Date(n, i, T).getTime()), l === v && (C += " picker-calendar-day-today"), p.indexOf(l) >= 0 && (C += " picker-calendar-day-selected"), r.params.weekendDays.indexOf(x - 1) >= 0 && (C += " picker-calendar-day-weekend"), (g && g > l || y && l > y) && (C += " picker-calendar-day-disabled"), l = new Date(l);
                            var b = l.getFullYear(),
                                k = l.getMonth();
                            w += '<div data-year="' + b + '" data-month="' + k + '" data-day="' + T + '" class="picker-calendar-day' + C + '" data-date="' + (b + "-" + k + "-" + T) + '"><span>' + T + "</span></div>"
                        }
                        f += '<div class="picker-calendar-row">' + w + "</div>"
                    }
                    return f = '<div class="picker-calendar-month" data-year="' + n + '" data-month="' + i + '">' + f + "</div>"
                }, r.animating = !1, r.updateCurrentMonthYear = function (t) {
                    "undefined" == typeof t ? (r.currentMonth = parseInt(r.months.eq(1).attr("data-month"), 10), r.currentYear = parseInt(r.months.eq(1).attr("data-year"), 10)) : (r.currentMonth = parseInt(r.months.eq("next" === t ? r.months.length - 1 : 0).attr("data-month"), 10), r.currentYear = parseInt(r.months.eq("next" === t ? r.months.length - 1 : 0).attr("data-year"), 10)), r.container.find(".current-month-value").text(r.params.monthNames[r.currentMonth]), r.container.find(".current-year-value").text(r.currentYear)
                }, r.onMonthChangeStart = function (t) {
                    r.updateCurrentMonthYear(t), r.months.removeClass("picker-calendar-month-current picker-calendar-month-prev picker-calendar-month-next");
                    var e = "next" === t ? r.months.length - 1 : 0;
                    r.months.eq(e).addClass("picker-calendar-month-current"), r.months.eq("next" === t ? e - 1 : e + 1).addClass("next" === t ? "picker-calendar-month-prev" : "picker-calendar-month-next"), r.params.onMonthYearChangeStart && r.params.onMonthYearChangeStart(r, r.currentYear, r.currentMonth)
                }, r.onMonthChangeEnd = function (t, e) {
                    r.animating = !1;
                    var n, i, o;
                    r.wrapper.find(".picker-calendar-month:not(.picker-calendar-month-prev):not(.picker-calendar-month-current):not(.picker-calendar-month-next)").remove(), "undefined" == typeof t && (t = "next", e = !0), e ? (r.wrapper.find(".picker-calendar-month-next, .picker-calendar-month-prev").remove(), i = r.monthHTML(new Date(r.currentYear, r.currentMonth), "prev"), n = r.monthHTML(new Date(r.currentYear, r.currentMonth), "next")) : o = r.monthHTML(new Date(r.currentYear, r.currentMonth), t), ("next" === t || e) && r.wrapper.append(o || n), ("prev" === t || e) && r.wrapper.prepend(o || i), r.months = r.wrapper.find(".picker-calendar-month"), r.setMonthsTranslate(r.monthsTranslate), r.params.onMonthAdd && r.params.onMonthAdd(r, "next" === t ? r.months.eq(r.months.length - 1)[0] : r.months.eq(0)[0]), r.params.onMonthYearChangeEnd && r.params.onMonthYearChangeEnd(r, r.currentYear, r.currentMonth)
                }, r.setMonthsTranslate = function (t) {
                    t = t || r.monthsTranslate || 0, "undefined" == typeof r.monthsTranslate && (r.monthsTranslate = t), r.months.removeClass("picker-calendar-month-current picker-calendar-month-prev picker-calendar-month-next");
                    var e = 100 * -(t + 1) * h,
                        n = 100 * -t * h,
                        i = 100 * -(t - 1) * h;
                    r.months.eq(0).transform("translate3d(" + (r.isH ? e : 0) + "%, " + (r.isH ? 0 : e) + "%, 0)").addClass("picker-calendar-month-prev"),
                        r.months.eq(1).transform("translate3d(" + (r.isH ? n : 0) + "%, " + (r.isH ? 0 : n) + "%, 0)").addClass("picker-calendar-month-current"), r.months.eq(2).transform("translate3d(" + (r.isH ? i : 0) + "%, " + (r.isH ? 0 : i) + "%, 0)").addClass("picker-calendar-month-next")
                }, r.nextMonth = function (e) {
                    "undefined" != typeof e && "object" != typeof e || (e = "", r.params.animate || (e = 0));
                    var n = parseInt(r.months.eq(r.months.length - 1).attr("data-month"), 10),
                        i = parseInt(r.months.eq(r.months.length - 1).attr("data-year"), 10),
                        o = new Date(i, n),
                        a = o.getTime(),
                        s = !r.animating;
                    if (r.params.maxDate && a > new Date(r.params.maxDate).getTime()) return r.resetMonth();
                    if (r.monthsTranslate--, n === r.currentMonth) {
                        var l = 100 * -r.monthsTranslate * h,
                            c = t(r.monthHTML(a, "next")).transform("translate3d(" + (r.isH ? l : 0) + "%, " + (r.isH ? 0 : l) + "%, 0)").addClass("picker-calendar-month-next");
                        r.wrapper.append(c[0]), r.months = r.wrapper.find(".picker-calendar-month"), r.params.onMonthAdd && r.params.onMonthAdd(r, r.months.eq(r.months.length - 1)[0])
                    }
                    r.animating = !0, r.onMonthChangeStart("next");
                    var p = 100 * r.monthsTranslate * h;
                    r.wrapper.transition(e).transform("translate3d(" + (r.isH ? p : 0) + "%, " + (r.isH ? 0 : p) + "%, 0)"), s && r.wrapper.transitionEnd(function () {
                        r.onMonthChangeEnd("next")
                    }), r.params.animate || r.onMonthChangeEnd("next")
                }, r.prevMonth = function (e) {
                    "undefined" != typeof e && "object" != typeof e || (e = "", r.params.animate || (e = 0));
                    var n = parseInt(r.months.eq(0).attr("data-month"), 10),
                        i = parseInt(r.months.eq(0).attr("data-year"), 10),
                        o = new Date(i, n + 1, -1),
                        a = o.getTime(),
                        s = !r.animating;
                    if (r.params.minDate && a < new Date(r.params.minDate).getTime()) return r.resetMonth();
                    if (r.monthsTranslate++, n === r.currentMonth) {
                        var l = 100 * -r.monthsTranslate * h,
                            c = t(r.monthHTML(a, "prev")).transform("translate3d(" + (r.isH ? l : 0) + "%, " + (r.isH ? 0 : l) + "%, 0)").addClass("picker-calendar-month-prev");
                        r.wrapper.prepend(c[0]), r.months = r.wrapper.find(".picker-calendar-month"), r.params.onMonthAdd && r.params.onMonthAdd(r, r.months.eq(0)[0])
                    }
                    r.animating = !0, r.onMonthChangeStart("prev");
                    var p = 100 * r.monthsTranslate * h;
                    r.wrapper.transition(e).transform("translate3d(" + (r.isH ? p : 0) + "%, " + (r.isH ? 0 : p) + "%, 0)"), s && r.wrapper.transitionEnd(function () {
                        r.onMonthChangeEnd("prev")
                    }), r.params.animate || r.onMonthChangeEnd("prev")
                }, r.resetMonth = function (t) {
                    "undefined" == typeof t && (t = "");
                    var e = 100 * r.monthsTranslate * h;
                    r.wrapper.transition(t).transform("translate3d(" + (r.isH ? e : 0) + "%, " + (r.isH ? 0 : e) + "%, 0)")
                }, r.setYearMonth = function (t, e, n) {
                    "undefined" == typeof t && (t = r.currentYear), "undefined" == typeof e && (e = r.currentMonth), "undefined" != typeof n && "object" != typeof n || (n = "", r.params.animate || (n = 0));
                    var i;
                    if (i = t < r.currentYear ? new Date(t, e + 1, -1).getTime() : new Date(t, e).getTime(), r.params.maxDate && i > new Date(r.params.maxDate).getTime()) return !1;
                    if (r.params.minDate && i < new Date(r.params.minDate).getTime()) return !1;
                    var o = new Date(r.currentYear, r.currentMonth).getTime(),
                        a = i > o ? "next" : "prev",
                        s = r.monthHTML(new Date(t, e));
                    r.monthsTranslate = r.monthsTranslate || 0;
                    var l, c, p = r.monthsTranslate,
                        d = !r.animating;
                    i > o ? (r.monthsTranslate--, r.animating || r.months.eq(r.months.length - 1).remove(), r.wrapper.append(s), r.months = r.wrapper.find(".picker-calendar-month"), l = 100 * -(p - 1) * h, r.months.eq(r.months.length - 1).transform("translate3d(" + (r.isH ? l : 0) + "%, " + (r.isH ? 0 : l) + "%, 0)").addClass("picker-calendar-month-next")) : (r.monthsTranslate++, r.animating || r.months.eq(0).remove(), r.wrapper.prepend(s), r.months = r.wrapper.find(".picker-calendar-month"), l = 100 * -(p + 1) * h, r.months.eq(0).transform("translate3d(" + (r.isH ? l : 0) + "%, " + (r.isH ? 0 : l) + "%, 0)").addClass("picker-calendar-month-prev")), r.params.onMonthAdd && r.params.onMonthAdd(r, "next" === a ? r.months.eq(r.months.length - 1)[0] : r.months.eq(0)[0]), r.animating = !0, r.onMonthChangeStart(a), c = 100 * r.monthsTranslate * h, r.wrapper.transition(n).transform("translate3d(" + (r.isH ? c : 0) + "%, " + (r.isH ? 0 : c) + "%, 0)"), d && r.wrapper.transitionEnd(function () {
                        r.onMonthChangeEnd(a, !0)
                    }), r.params.animate || r.onMonthChangeEnd(a)
                }, r.nextYear = function () {
                    r.setYearMonth(r.currentYear + 1)
                }, r.prevYear = function () {
                    r.setYearMonth(r.currentYear - 1)
                }, r.layout = function () {
                    var t, e = "",
                        n = "",
                        i = r.value && r.value.length ? r.value[0] : (new Date).setHours(0, 0, 0, 0),
                        o = r.monthHTML(i, "prev"),
                        a = r.monthHTML(i),
                        s = r.monthHTML(i, "next"),
                        l = '<div class="picker-calendar-months"><div class="picker-calendar-months-wrapper">' + (o + a + s) + "</div></div>",
                        c = "";
                    if (r.params.weekHeader) {
                        for (t = 0; 7 > t; t++) {
                            var h = t + r.params.firstDay > 6 ? t - 7 + r.params.firstDay : t + r.params.firstDay,
                                p = r.params.dayNamesShort[h];
                            c += '<div class="picker-calendar-week-day ' + (r.params.weekendDays.indexOf(h) >= 0 ? "picker-calendar-week-day-weekend" : "") + '"> ' + p + "</div>"
                        }
                        c = '<div class="picker-calendar-week-days">' + c + "</div>"
                    }
                    n = "picker-modal picker-calendar " + (r.params.cssClass || "");
                    var d = r.params.toolbar ? r.params.toolbarTemplate.replace(/{{closeText}}/g, r.params.toolbarCloseText) : "";
                    r.params.toolbar && (d = r.params.toolbarTemplate.replace(/{{closeText}}/g, r.params.toolbarCloseText).replace(/{{monthPicker}}/g, r.params.monthPicker ? r.params.monthPickerTemplate : "").replace(/{{yearPicker}}/g, r.params.yearPicker ? r.params.yearPickerTemplate : "")), e = '<div class="' + n + '">' + d + '<div class="picker-modal-inner">' + c + l + "</div></div>", r.pickerHTML = e
                }, r.params.input && (r.input = t(r.params.input), r.input.length > 0 && (r.params.inputReadOnly && r.input.prop("readOnly", !0), r.inline || r.input.on("click", o))), r.inline || t("html").on("click", a), r.opened = !1, r.open = function () {
                    var e = !1;
                    r.opened || (r.value || r.params.value && (r.value = r.params.value, e = !0), r.layout(), r.inline ? (r.container = t(r.pickerHTML), r.container.addClass("picker-modal-inline"), t(r.params.container).append(r.container)) : (r.container = t(t.pickerModal(r.pickerHTML)), t(r.container).on("close", function () {
                        s()
                    })), r.container[0].f7Calendar = r, r.wrapper = r.container.find(".picker-calendar-months-wrapper"), r.months = r.wrapper.find(".picker-calendar-month"), r.updateCurrentMonthYear(), r.monthsTranslate = 0, r.setMonthsTranslate(), r.initCalendarEvents(), e && r.updateValue()), r.opened = !0, r.initialized = !0, r.params.onMonthAdd && r.months.each(function () {
                        r.params.onMonthAdd(r, this)
                    }), r.params.onOpen && r.params.onOpen(r)
                }, r.close = function () {
                    r.opened && !r.inline && t.closeModal(r.container)
                }, r.destroy = function () {
                    r.close(), r.params.input && r.input.length > 0 && r.input.off("click", o), t("html").off("click", a)
                }, r.inline && r.open(), r
            };
        t.fn.calendar = function (e) {
            return this.each(function () {
                var i = t(this);
                if (i[0]) {
                    var o = {};
                    "INPUT" === i[0].tagName.toUpperCase() ? o.input = i : o.container = i, new n(t.extend(o, e))
                }
            })
        }, t.initCalendar = function (e) {
            var n = t(e ? e : document.body);
            n.find("[data-toggle='date']").each(function () {
                t(this).calendar()
            })
        }
    }(Zepto), + function (t) {
        "use strict";
        var e = function (e) {
            function n() {
                if (s.opened)
                    for (var t = 0; t < s.cols.length; t++) s.cols[t].divider || (s.cols[t].calcSize(), s.cols[t].setValue(s.cols[t].value, 0, !1))
            }

            function i(e) {
                if (e.preventDefault(), t.device.isWeixin && t.device.android && s.params.inputReadOnly && (this.focus(), this.blur()), !s.opened && (s.open(), s.params.scrollToInput)) {
                    var n = s.input.parents(".content");
                    if (0 === n.length) return;
                    var i, o = parseInt(n.css("padding-top"), 10),
                        a = parseInt(n.css("padding-bottom"), 10),
                        r = n[0].offsetHeight - o - s.container.height(),
                        l = n[0].scrollHeight - o - s.container.height(),
                        c = s.input.offset().top - o + s.input[0].offsetHeight;
                    if (c > r) {
                        var h = n.scrollTop() + c - r;
                        h + r > l && (i = h + r - l + a, r === l && (i = s.container.height()), n.css({
                            "padding-bottom": i + "px"
                        })), n.scrollTop(h, 300)
                    }
                }
            }

            function o(e) {
                s.opened && (s.input && s.input.length > 0 ? e.target !== s.input[0] && 0 === t(e.target).parents(".picker-modal").length && s.close() : 0 === t(e.target).parents(".picker-modal").length && s.close())
            }

            function a() {
                s.opened = !1, s.input && s.input.length > 0 && s.input.parents(".content").css({
                    "padding-bottom": ""
                }), s.params.onClose && s.params.onClose(s), s.container.find(".picker-items-col").each(function () {
                    s.destroyPickerCol(this)
                })
            }
            var s = this,
                r = {
                    updateValuesOnMomentum: !1,
                    updateValuesOnTouchmove: !0,
                    rotateEffect: !1,
                    momentumRatio: 7,
                    freeMode: !1,
                    scrollToInput: !0,
                    inputReadOnly: !0,
                    toolbar: !0,
                    toolbarCloseText: "确定",
                    toolbarTemplate: '<header class="bar bar-nav">                <button class="button button-link pull-right close-picker">确定</button>                <h1 class="title">请选择</h1>                </header>'
                };
            e = e || {};
            for (var l in r) "undefined" == typeof e[l] && (e[l] = r[l]);
            s.params = e, s.cols = [], s.initialized = !1, s.inline = !!s.params.container;
            var c = t.device.ios || navigator.userAgent.toLowerCase().indexOf("safari") >= 0 && navigator.userAgent.toLowerCase().indexOf("chrome") < 0 && !t.device.android;
            return s.setValue = function (t, e) {
                for (var n = 0, i = 0; i < s.cols.length; i++) s.cols[i] && !s.cols[i].divider && (s.cols[i].setValue(t[n], e), n++)
            }, s.updateValue = function () {
                for (var e = [], n = [], i = 0; i < s.cols.length; i++) s.cols[i].divider || (e.push(s.cols[i].value), n.push(s.cols[i].displayValue));
                e.indexOf(void 0) >= 0 || (s.value = e, s.displayValue = n, s.params.onChange && s.params.onChange(s, s.value, s.displayValue), s.input && s.input.length > 0 && (t(s.input).val(s.params.formatValue ? s.params.formatValue(s, s.value, s.displayValue) : s.value.join(" ")), t(s.input).trigger("change")))
            }, s.initPickerCol = function (e, n) {
                function i() {
                    y = t.requestAnimationFrame(function () {
                        d.updateItems(void 0, void 0, 0), i()
                    })
                }

                function o(e) {
                    x || w || (e.preventDefault(), w = !0, T = C = "touchstart" === e.type ? e.targetTouches[0].pageY : e.pageY, b = (new Date).getTime(), Y = !0, S = M = t.getTranslate(d.wrapper[0], "y"))
                }

                function a(e) {
                    if (w) {
                        e.preventDefault(), Y = !1, C = "touchmove" === e.type ? e.targetTouches[0].pageY : e.pageY, x || (t.cancelAnimationFrame(y), x = !0, S = M = t.getTranslate(d.wrapper[0], "y"), d.wrapper.transition(0)), e.preventDefault();
                        var n = C - T;
                        M = S + n, E = void 0, v > M && (M = v - Math.pow(v - M, .8), E = "min"), M > g && (M = g + Math.pow(M - g, .8), E = "max"), d.wrapper.transform("translate3d(0," + M + "px,0)"), d.updateItems(void 0, M, 0, s.params.updateValuesOnTouchmove), D = M - P || M, _ = (new Date).getTime(), P = M
                    }
                }

                function r(e) {
                    if (!w || !x) return void(w = x = !1);
                    w = x = !1, d.wrapper.transition(""), E && ("min" === E ? d.wrapper.transform("translate3d(0," + v + "px,0)") : d.wrapper.transform("translate3d(0," + g + "px,0)")), k = (new Date).getTime();
                    var n, o;
                    k - b > 300 ? o = M : (n = Math.abs(D / (k - _)), o = M + D * s.params.momentumRatio), o = Math.max(Math.min(o, g), v);
                    var a = -Math.floor((o - g) / f);
                    s.params.freeMode || (o = -a * f + g), d.wrapper.transform("translate3d(0," + parseInt(o, 10) + "px,0)"), d.updateItems(a, o, "", !0), s.params.updateValuesOnMomentum && (i(), d.wrapper.transitionEnd(function () {
                        t.cancelAnimationFrame(y)
                    })), setTimeout(function () {
                        Y = !0
                    }, 100)
                }

                function l(e) {
                    if (Y) {
                        t.cancelAnimationFrame(y);
                        var n = t(this).attr("data-picker-value");
                        d.setValue(n)
                    }
                }
                var h = t(e),
                    p = h.index(),
                    d = s.cols[p];
                if (!d.divider) {
                    d.container = h, d.wrapper = d.container.find(".picker-items-col-wrapper"), d.items = d.wrapper.find(".picker-item");
                    var u, f, m, v, g;
                    d.replaceValues = function (t, e) {
                        d.destroyEvents(), d.values = t, d.displayValues = e;
                        var n = s.columnHTML(d, !0);
                        d.wrapper.html(n), d.items = d.wrapper.find(".picker-item"), d.calcSize(), d.setValue(d.values[0], 0, !0), d.initEvents()
                    }, d.calcSize = function () {
                        s.params.rotateEffect && (d.container.removeClass("picker-items-col-absolute"), d.width || d.container.css({
                            width: ""
                        }));
                        var e, n;
                        e = 0, n = d.container[0].offsetHeight, u = d.wrapper[0].offsetHeight, f = d.items[0].offsetHeight, m = f * d.items.length, v = n / 2 - m + f / 2, g = n / 2 - f / 2, d.width && (e = d.width, parseInt(e, 10) === e && (e += "px"), d.container.css({
                            width: e
                        })), s.params.rotateEffect && (d.width || (d.items.each(function () {
                            var n = t(this);
                            n.css({
                                width: "auto"
                            }), e = Math.max(e, n[0].offsetWidth), n.css({
                                width: ""
                            })
                        }), d.container.css({
                            width: e + 2 + "px"
                        })), d.container.addClass("picker-items-col-absolute"))
                    }, d.calcSize(), d.wrapper.transform("translate3d(0," + g + "px,0)").transition(0);
                    var y;
                    d.setValue = function (e, n, o) {
                        "undefined" == typeof n && (n = "");
                        var a = d.wrapper.find('.picker-item[data-picker-value="' + e + '"]').index();
                        if ("undefined" != typeof a && -1 !== a) {
                            var r = -a * f + g;
                            d.wrapper.transition(n), d.wrapper.transform("translate3d(0," + r + "px,0)"), s.params.updateValuesOnMomentum && d.activeIndex && d.activeIndex !== a && (t.cancelAnimationFrame(y), d.wrapper.transitionEnd(function () {
                                t.cancelAnimationFrame(y)
                            }), i()), d.updateItems(a, r, n, o)
                        }
                    }, d.updateItems = function (e, n, i, o) {
                        "undefined" == typeof n && (n = t.getTranslate(d.wrapper[0], "y")), "undefined" == typeof e && (e = -Math.round((n - g) / f)), 0 > e && (e = 0), e >= d.items.length && (e = d.items.length - 1);
                        var a = d.activeIndex;
                        d.activeIndex = e, d.wrapper.find(".picker-selected").removeClass("picker-selected"), s.params.rotateEffect && d.items.transition(i);
                        var r = d.items.eq(e).addClass("picker-selected").transform("");
                        if ((o || "undefined" == typeof o) && (d.value = r.attr("data-picker-value"), d.displayValue = d.displayValues ? d.displayValues[e] : d.value, a !== e && (d.onChange && d.onChange(s, d.value, d.displayValue), s.updateValue())), s.params.rotateEffect) {
                            (n - (Math.floor((n - g) / f) * f + g)) / f;
                            d.items.each(function () {
                                var e = t(this),
                                    i = e.index() * f,
                                    o = g - n,
                                    a = i - o,
                                    s = a / f,
                                    r = Math.ceil(d.height / f / 2) + 1,
                                    l = -18 * s;
                                l > 180 && (l = 180), -180 > l && (l = -180), Math.abs(s) > r ? e.addClass("picker-item-far") : e.removeClass("picker-item-far"), e.transform("translate3d(0, " + (-n + g) + "px, " + (c ? -110 : 0) + "px) rotateX(" + l + "deg)")
                            })
                        }
                    }, n && d.updateItems(0, g, 0);
                    var w, x, T, C, b, k, S, E, M, P, D, _, Y = !0;
                    d.initEvents = function (e) {
                        var n = e ? "off" : "on";
                        d.container[n](t.touchEvents.start, o), d.container[n](t.touchEvents.move, a), d.container[n](t.touchEvents.end, r), d.items[n]("click", l)
                    }, d.destroyEvents = function () {
                        d.initEvents(!0)
                    }, d.container[0].f7DestroyPickerCol = function () {
                        d.destroyEvents()
                    }, d.initEvents()
                }
            }, s.destroyPickerCol = function (e) {
                e = t(e), "f7DestroyPickerCol" in e[0] && e[0].f7DestroyPickerCol()
            }, t(window).on("resize", n), s.columnHTML = function (t, e) {
                var n = "",
                    i = "";
                if (t.divider) i += '<div class="picker-items-col picker-items-col-divider ' + (t.textAlign ? "picker-items-col-" + t.textAlign : "") + " " + (t.cssClass || "") + '">' + t.content + "</div>";
                else {
                    for (var o = 0; o < t.values.length; o++) n += '<div class="picker-item" data-picker-value="' + t.values[o] + '">' + (t.displayValues ? t.displayValues[o] : t.values[o]) + "</div>";
                    i += '<div class="picker-items-col ' + (t.textAlign ? "picker-items-col-" + t.textAlign : "") + " " + (t.cssClass || "") + '"><div class="picker-items-col-wrapper">' + n + "</div></div>"
                }
                return e ? n : i
            }, s.layout = function () {
                var t, e = "",
                    n = "";
                s.cols = [];
                var i = "";
                for (t = 0; t < s.params.cols.length; t++) {
                    var o = s.params.cols[t];
                    i += s.columnHTML(s.params.cols[t]), s.cols.push(o)
                }
                n = "picker-modal picker-columns " + (s.params.cssClass || "") + (s.params.rotateEffect ? " picker-3d" : ""), e = '<div class="' + n + '">' + (s.params.toolbar ? s.params.toolbarTemplate.replace(/{{closeText}}/g, s.params.toolbarCloseText) : "") + '<div class="picker-modal-inner picker-items">' + i + '<div class="picker-center-highlight"></div></div></div>', s.pickerHTML = e
            }, s.params.input && (s.input = t(s.params.input), s.input.length > 0 && (s.params.inputReadOnly && s.input.prop("readOnly", !0), s.inline || s.input.on("click", i))), s.inline || t("html").on("click", o), s.opened = !1, s.open = function () {
                s.opened || (s.layout(), s.inline ? (s.container = t(s.pickerHTML), s.container.addClass("picker-modal-inline"), t(s.params.container).append(s.container), s.opened = !0) : (s.container = t(t.pickerModal(s.pickerHTML)), t(s.container).one("opened", function () {
                    s.opened = !0
                }).on("close", function () {
                    a()
                })), s.container[0].f7Picker = s, s.container.find(".picker-items-col").each(function () {
                    var t = !0;
                    (!s.initialized && s.params.value || s.initialized && s.value) && (t = !1), s.initPickerCol(this, t)
                }), s.initialized ? s.value && s.setValue(s.value, 0) : s.params.value && s.setValue(s.params.value, 0)), s.initialized = !0, s.params.onOpen && s.params.onOpen(s)
            }, s.close = function () {
                s.opened && !s.inline && t.closeModal(s.container)
            }, s.destroy = function () {
                s.close(), s.params.input && s.input.length > 0 && s.input.off("click", i), t("html").off("click", o), t(window).off("resize", n)
            }, s.inline && s.open(), s
        };
        t(document).on("click", ".close-picker", function () {
            var e = t(".picker-modal.modal-in");
            t.closeModal(e)
        }), t.fn.picker = function (n) {
            var i = arguments;
            return this.each(function () {
                if (this) {
                    var o = t(this),
                        a = o.data("picker");
                    if (!a) {
                        var s = t.extend({
                            input: this,
                            value: o.val() ? o.val().split(" ") : ""
                        }, n);
                        a = new e(s), o.data("picker", a)
                    }
                    "string" == typeof n && a[n].apply(a, Array.prototype.slice.call(i, 1))
                }
            })
        }
    }(Zepto), + function (t) {
        "use strict";
        var e = new Date,
            n = function (t) {
                for (var e = [], n = 1;
                    (t || 31) >= n; n++) e.push(10 > n ? "0" + n : n);
                return e
            },
            i = function (t, e) {
                var i = new Date(e, parseInt(t) + 1 - 1, 1),
                    o = new Date(i - 1);
                return n(o.getDate())
            },
            o = function (t) {
                return 10 > t ? "0" + t : t
            },
            a = "01 02 03 04 05 06 07 08 09 10 11 12".split(" "),
            s = function () {
                for (var t = [], e = 1950; 2030 >= e; e++) t.push(e);
                return t
            }(),
            r = {
                rotateEffect: !1,
                value: [e.getFullYear(), o(e.getMonth() + 1), o(e.getDate()), e.getHours(), o(e.getMinutes())],
                onChange: function (t, e, n) {
                    var o = i(t.cols[1].value, t.cols[0].value),
                        a = t.cols[2].value;
                    a > o.length && (a = o.length), t.cols[2].setValue(a)
                }, formatValue: function (t, e, n) {
                    return n[0] + "-" + e[1] + "-" + e[2] + " " + e[3] + ":" + e[4]
                }, cols: [{
                    values: s
                }, {
                    values: a
                }, {
                    values: n()
                }, {
                    divider: !0,
                    content: "  "
                }, {
                    values: function () {
                        for (var t = [], e = 0; 23 >= e; e++) t.push(e);
                        return t
                    }()
                }, {
                    divider: !0,
                    content: ":"
                }, {
                    values: function () {
                        for (var t = [], e = 0; 59 >= e; e++) t.push(10 > e ? "0" + e : e);
                        return t
                    }()
                }]
            };
        t.fn.datetimePicker = function (e) {
            return this.each(function () {
                if (this) {
                    var n = t.extend(r, e);
                    t(this).picker(n), e.value && t(this).val(n.formatValue(n, n.value, n.value))
                }
            })
        }
    }(Zepto), + function (t) {
        "use strict";

        function e(t, e) {
            this.wrapper = "string" == typeof t ? document.querySelector(t) : t, this.scroller = $(this.wrapper).find(".content-inner")[0], this.scrollerStyle = this.scroller && this.scroller.style, this.options = {
                resizeScrollbars: !0,
                mouseWheelSpeed: 20,
                snapThreshold: .334,
                startX: 0,
                startY: 0,
                scrollY: !0,
                directionLockThreshold: 5,
                momentum: !0,
                bounce: !0,
                bounceTime: 600,
                bounceEasing: "",
                preventDefault: !0,
                preventDefaultException: {
                    tagName: /^(INPUT|TEXTAREA|BUTTON|SELECT)$/
                },
                HWCompositing: !0,
                useTransition: !0,
                useTransform: !0,
                eventPassthrough: void 0
            };
            for (var n in e) this.options[n] = e[n];
            this.translateZ = this.options.HWCompositing && a.hasPerspective ? " translateZ(0)" : "", this.options.useTransition = a.hasTransition && this.options.useTransition, this.options.useTransform = a.hasTransform && this.options.useTransform, this.options.eventPassthrough = this.options.eventPassthrough === !0 ? "vertical" : this.options.eventPassthrough, this.options.preventDefault = !this.options.eventPassthrough && this.options.preventDefault, this.options.scrollY = "vertical" === this.options.eventPassthrough ? !1 : this.options.scrollY, this.options.scrollX = "horizontal" === this.options.eventPassthrough ? !1 : this.options.scrollX, this.options.freeScroll = this.options.freeScroll && !this.options.eventPassthrough, this.options.directionLockThreshold = this.options.eventPassthrough ? 0 : this.options.directionLockThreshold, this.options.bounceEasing = "string" == typeof this.options.bounceEasing ? a.ease[this.options.bounceEasing] || a.ease.circular : this.options.bounceEasing, this.options.resizePolling = void 0 === this.options.resizePolling ? 60 : this.options.resizePolling, this.options.tap === !0 && (this.options.tap = "tap"), "scale" === this.options.shrinkScrollbars && (this.options.useTransition = !1), this.options.invertWheelDirection = this.options.invertWheelDirection ? -1 : 1, 3 === this.options.probeType && (this.options.useTransition = !1), this.x = 0, this.y = 0, this.directionX = 0, this.directionY = 0, this._events = {}, this._init(), this.refresh(), this.scrollTo(this.options.startX, this.options.startY), this.enable()
        }

        function n(t, e, n) {
            var i = document.createElement("div"),
                o = document.createElement("div");
            return n === !0 && (i.style.cssText = "position:absolute;z-index:9999", o.style.cssText = "-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:absolute;background:rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.9);border-radius:3px"), o.className = "iScrollIndicator", "h" === t ? (n === !0 && (i.style.cssText += ";height:5px;left:2px;right:2px;bottom:0", o.style.height = "100%"), i.className = "iScrollHorizontalScrollbar") : (n === !0 && (i.style.cssText += ";width:5px;bottom:2px;top:2px;right:1px", o.style.width = "100%"), i.className = "iScrollVerticalScrollbar"), i.style.cssText += ";overflow:hidden", e || (i.style.pointerEvents = "none"), i.appendChild(o), i
        }

        function i(e, n) {
            this.wrapper = "string" == typeof n.el ? document.querySelector(n.el) : n.el, this.wrapperStyle = this.wrapper.style, this.indicator = this.wrapper.children[0], this.indicatorStyle = this.indicator.style, this.scroller = e, this.options = {
                listenX: !0,
                listenY: !0,
                interactive: !1,
                resize: !0,
                defaultScrollbars: !1,
                shrink: !1,
                fade: !1,
                speedRatioX: 0,
                speedRatioY: 0
            };
            for (var i in n) this.options[i] = n[i];
            this.sizeRatioX = 1, this.sizeRatioY = 1, this.maxPosX = 0, this.maxPosY = 0, this.options.interactive && (this.options.disableTouch || (a.addEvent(this.indicator, "touchstart", this), a.addEvent(t, "touchend", this)), this.options.disablePointer || (a.addEvent(this.indicator, a.prefixPointerEvent("pointerdown"), this), a.addEvent(t, a.prefixPointerEvent("pointerup"), this)), this.options.disableMouse || (a.addEvent(this.indicator, "mousedown", this), a.addEvent(t, "mouseup", this))), this.options.fade && (this.wrapperStyle[a.style.transform] = this.scroller.translateZ, this.wrapperStyle[a.style.transitionDuration] = a.isBadAndroid ? "0.001s" : "0ms", this.wrapperStyle.opacity = "0")
        }
        var o = t.requestAnimationFrame || t.webkitRequestAnimationFrame || t.mozRequestAnimationFrame || t.oRequestAnimationFrame || t.msRequestAnimationFrame || function (e) {
                t.setTimeout(e, 1e3 / 60)
            },
            a = function () {
                function e(t) {
                    return a === !1 ? !1 : "" === a ? t : a + t.charAt(0).toUpperCase() + t.substr(1)
                }
                var n = {},
                    i = document.createElement("div").style,
                    a = function () {
                        for (var t, e = ["t", "webkitT", "MozT", "msT", "OT"], n = 0, o = e.length; o > n; n++)
                            if (t = e[n] + "ransform", t in i) return e[n].substr(0, e[n].length - 1);
                        return !1
                    }();
                n.getTime = Date.now || function () {
                    return (new Date).getTime()
                }, n.extend = function (t, e) {
                    for (var n in e) t[n] = e[n]
                }, n.addEvent = function (t, e, n, i) {
                    t.addEventListener(e, n, !!i)
                }, n.removeEvent = function (t, e, n, i) {
                    t.removeEventListener(e, n, !!i)
                }, n.prefixPointerEvent = function (e) {
                    return t.MSPointerEvent ? "MSPointer" + e.charAt(9).toUpperCase() + e.substr(10) : e
                }, n.momentum = function (t, e, n, i, a, s, r) {
                    function l() {
                        +new Date - f > 50 && (r._execEvent("scroll"), f = +new Date), +new Date - u < h && o(l)
                    }
                    var c, h, p = t - e,
                        d = Math.abs(p) / n;
                    d /= 2, d = d > 1.5 ? 1.5 : d, s = void 0 === s ? 6e-4 : s, c = t + d * d / (2 * s) * (0 > p ? -1 : 1), h = d / s, i > c ? (c = a ? i - a / 2.5 * (d / 8) : i, p = Math.abs(c - t), h = p / d) : c > 0 && (c = a ? a / 2.5 * (d / 8) : 0, p = Math.abs(t) + c, h = p / d);
                    var u = +new Date,
                        f = u;
                    return o(l), {
                        destination: Math.round(c),
                        duration: h
                    }
                };
                var s = e("transform");
                return n.extend(n, {
                    hasTransform: s !== !1,
                    hasPerspective: e("perspective") in i,
                    hasTouch: "ontouchstart" in t,
                    hasPointer: t.PointerEvent || t.MSPointerEvent,
                    hasTransition: e("transition") in i
                }), n.isBadAndroid = /Android /.test(t.navigator.appVersion) && !/Chrome\/\d/.test(t.navigator.appVersion) && !1, n.extend(n.style = {}, {
                    transform: s,
                    transitionTimingFunction: e("transitionTimingFunction"),
                    transitionDuration: e("transitionDuration"),
                    transitionDelay: e("transitionDelay"),
                    transformOrigin: e("transformOrigin")
                }), n.hasClass = function (t, e) {
                    var n = new RegExp("(^|\\s)" + e + "(\\s|$)");
                    return n.test(t.className)
                }, n.addClass = function (t, e) {
                    if (!n.hasClass(t, e)) {
                        var i = t.className.split(" ");
                        i.push(e), t.className = i.join(" ")
                    }
                }, n.removeClass = function (t, e) {
                    if (n.hasClass(t, e)) {
                        var i = new RegExp("(^|\\s)" + e + "(\\s|$)", "g");
                        t.className = t.className.replace(i, " ")
                    }
                }, n.offset = function (t) {
                    for (var e = -t.offsetLeft, n = -t.offsetTop; t = t.offsetParent;) e -= t.offsetLeft, n -= t.offsetTop;
                    return {
                        left: e,
                        top: n
                    }
                }, n.preventDefaultException = function (t, e) {
                    for (var n in e)
                        if (e[n].test(t[n])) return !0;
                    return !1
                }, n.extend(n.eventType = {}, {
                    touchstart: 1,
                    touchmove: 1,
                    touchend: 1,
                    mousedown: 2,
                    mousemove: 2,
                    mouseup: 2,
                    pointerdown: 3,
                    pointermove: 3,
                    pointerup: 3,
                    MSPointerDown: 3,
                    MSPointerMove: 3,
                    MSPointerUp: 3
                }), n.extend(n.ease = {}, {
                    quadratic: {
                        style: "cubic-bezier(0.25, 0.46, 0.45, 0.94)",

                        fn: function (t) {
                            return t * (2 - t)
                        }
                    },
                    circular: {
                        style: "cubic-bezier(0.1, 0.57, 0.1, 1)",
                        fn: function (t) {
                            return Math.sqrt(1 - --t * t)
                        }
                    },
                    back: {
                        style: "cubic-bezier(0.175, 0.885, 0.32, 1.275)",
                        fn: function (t) {
                            var e = 4;
                            return (t -= 1) * t * ((e + 1) * t + e) + 1
                        }
                    },
                    bounce: {
                        style: "",
                        fn: function (t) {
                            return (t /= 1) < 1 / 2.75 ? 7.5625 * t * t : 2 / 2.75 > t ? 7.5625 * (t -= 1.5 / 2.75) * t + .75 : 2.5 / 2.75 > t ? 7.5625 * (t -= 2.25 / 2.75) * t + .9375 : 7.5625 * (t -= 2.625 / 2.75) * t + .984375
                        }
                    },
                    elastic: {
                        style: "",
                        fn: function (t) {
                            var e = .22,
                                n = .4;
                            return 0 === t ? 0 : 1 === t ? 1 : n * Math.pow(2, -10 * t) * Math.sin((t - e / 4) * (2 * Math.PI) / e) + 1
                        }
                    }
                }), n.tap = function (t, e) {
                    var n = document.createEvent("Event");
                    n.initEvent(e, !0, !0), n.pageX = t.pageX, n.pageY = t.pageY, t.target.dispatchEvent(n)
                }, n.click = function (t) {
                    var e, n = t.target;
                    /(SELECT|INPUT|TEXTAREA)/i.test(n.tagName) || (e = document.createEvent("MouseEvents"), e.initMouseEvent("click", !0, !0, t.view, 1, n.screenX, n.screenY, n.clientX, n.clientY, t.ctrlKey, t.altKey, t.shiftKey, t.metaKey, 0, null), e._constructed = !0, n.dispatchEvent(e))
                }, n
            }();
        e.prototype = {
            version: "5.1.3",
            _init: function () {
                this._initEvents(), (this.options.scrollbars || this.options.indicators) && this._initIndicators(), this.options.mouseWheel && this._initWheel(), this.options.snap && this._initSnap(), this.options.keyBindings && this._initKeys()
            }, destroy: function () {
                this._initEvents(!0), this._execEvent("destroy")
            }, _transitionEnd: function (t) {
                t.target === this.scroller && this.isInTransition && (this._transitionTime(), this.resetPosition(this.options.bounceTime) || (this.isInTransition = !1, this._execEvent("scrollEnd")))
            }, _start: function (t) {
                if ((1 === a.eventType[t.type] || 0 === t.button) && this.enabled && (!this.initiated || a.eventType[t.type] === this.initiated)) {
                    !this.options.preventDefault || a.isBadAndroid || a.preventDefaultException(t.target, this.options.preventDefaultException) || t.preventDefault();
                    var e, n = t.touches ? t.touches[0] : t;
                    this.initiated = a.eventType[t.type], this.moved = !1, this.distX = 0, this.distY = 0, this.directionX = 0, this.directionY = 0, this.directionLocked = 0, this._transitionTime(), this.startTime = a.getTime(), this.options.useTransition && this.isInTransition ? (this.isInTransition = !1, e = this.getComputedPosition(), this._translate(Math.round(e.x), Math.round(e.y)), this._execEvent("scrollEnd")) : !this.options.useTransition && this.isAnimating && (this.isAnimating = !1, this._execEvent("scrollEnd")), this.startX = this.x, this.startY = this.y, this.absStartX = this.x, this.absStartY = this.y, this.pointX = n.pageX, this.pointY = n.pageY, this._execEvent("beforeScrollStart")
                }
            }, _move: function (t) {
                if (this.enabled && a.eventType[t.type] === this.initiated) {
                    this.options.preventDefault && t.preventDefault();
                    var e, n, i, o, s = t.touches ? t.touches[0] : t,
                        r = s.pageX - this.pointX,
                        l = s.pageY - this.pointY,
                        c = a.getTime();
                    if (this.pointX = s.pageX, this.pointY = s.pageY, this.distX += r, this.distY += l, i = Math.abs(this.distX), o = Math.abs(this.distY), !(c - this.endTime > 300 && 10 > i && 10 > o)) {
                        if (this.directionLocked || this.options.freeScroll || (i > o + this.options.directionLockThreshold ? this.directionLocked = "h" : o >= i + this.options.directionLockThreshold ? this.directionLocked = "v" : this.directionLocked = "n"), "h" === this.directionLocked) {
                            if ("vertical" === this.options.eventPassthrough) t.preventDefault();
                            else if ("horizontal" === this.options.eventPassthrough) return void(this.initiated = !1);
                            l = 0
                        } else if ("v" === this.directionLocked) {
                            if ("horizontal" === this.options.eventPassthrough) t.preventDefault();
                            else if ("vertical" === this.options.eventPassthrough) return void(this.initiated = !1);
                            r = 0
                        }
                        r = this.hasHorizontalScroll ? r : 0, l = this.hasVerticalScroll ? l : 0, e = this.x + r, n = this.y + l, (e > 0 || e < this.maxScrollX) && (e = this.options.bounce ? this.x + r / 3 : e > 0 ? 0 : this.maxScrollX), (n > 0 || n < this.maxScrollY) && (n = this.options.bounce ? this.y + l / 3 : n > 0 ? 0 : this.maxScrollY), this.directionX = r > 0 ? -1 : 0 > r ? 1 : 0, this.directionY = l > 0 ? -1 : 0 > l ? 1 : 0, this.moved || this._execEvent("scrollStart"), this.moved = !0, this._translate(e, n), c - this.startTime > 300 && (this.startTime = c, this.startX = this.x, this.startY = this.y, 1 === this.options.probeType && this._execEvent("scroll")), this.options.probeType > 1 && this._execEvent("scroll")
                    }
                }
            }, _end: function (t) {
                if (this.enabled && a.eventType[t.type] === this.initiated) {
                    this.options.preventDefault && !a.preventDefaultException(t.target, this.options.preventDefaultException) && t.preventDefault();
                    var e, n, i = a.getTime() - this.startTime,
                        o = Math.round(this.x),
                        s = Math.round(this.y),
                        r = Math.abs(o - this.startX),
                        l = Math.abs(s - this.startY),
                        c = 0,
                        h = "";
                    if (this.isInTransition = 0, this.initiated = 0, this.endTime = a.getTime(), !this.resetPosition(this.options.bounceTime)) {
                        if (this.scrollTo(o, s), !this.moved) return this.options.tap && a.tap(t, this.options.tap), this.options.click && a.click(t), void this._execEvent("scrollCancel");
                        if (this._events.flick && 200 > i && 100 > r && 100 > l) return void this._execEvent("flick");
                        if (this.options.momentum && 300 > i && (e = this.hasHorizontalScroll ? a.momentum(this.x, this.startX, i, this.maxScrollX, this.options.bounce ? this.wrapperWidth : 0, this.options.deceleration, this) : {
                            destination: o,
                            duration: 0
                        }, n = this.hasVerticalScroll ? a.momentum(this.y, this.startY, i, this.maxScrollY, this.options.bounce ? this.wrapperHeight : 0, this.options.deceleration, this) : {
                            destination: s,
                            duration: 0
                        }, o = e.destination, s = n.destination, c = Math.max(e.duration, n.duration), this.isInTransition = 1), this.options.snap) {
                            var p = this._nearestSnap(o, s);
                            this.currentPage = p, c = this.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(o - p.x), 1e3), Math.min(Math.abs(s - p.y), 1e3)), 300), o = p.x, s = p.y, this.directionX = 0, this.directionY = 0, h = this.options.bounceEasing
                        }
                        return o !== this.x || s !== this.y ? ((o > 0 || o < this.maxScrollX || s > 0 || s < this.maxScrollY) && (h = a.ease.quadratic), void this.scrollTo(o, s, c, h)) : void this._execEvent("scrollEnd")
                    }
                }
            }, _resize: function () {
                var t = this;
                clearTimeout(this.resizeTimeout), this.resizeTimeout = setTimeout(function () {
                    t.refresh()
                }, this.options.resizePolling)
            }, resetPosition: function (e) {
                var n = this.x,
                    i = this.y;
                if (e = e || 0, !this.hasHorizontalScroll || this.x > 0 ? n = 0 : this.x < this.maxScrollX && (n = this.maxScrollX), !this.hasVerticalScroll || this.y > 0 ? i = 0 : this.y < this.maxScrollY && (i = this.maxScrollY), n === this.x && i === this.y) return !1;
                if (this.options.ptr && this.y > 44 && -1 * this.startY < $(t).height() && !this.ptrLock) {
                    i = this.options.ptrOffset || 44, this._execEvent("ptr"), this.ptrLock = !0;
                    var o = this;
                    setTimeout(function () {
                        o.ptrLock = !1
                    }, 500)
                }
                return this.scrollTo(n, i, e, this.options.bounceEasing), !0
            }, disable: function () {
                this.enabled = !1
            }, enable: function () {
                this.enabled = !0
            }, refresh: function () {
                this.wrapperWidth = this.wrapper.clientWidth, this.wrapperHeight = this.wrapper.clientHeight, this.scrollerWidth = this.scroller.offsetWidth, this.scrollerHeight = this.scroller.offsetHeight, this.maxScrollX = this.wrapperWidth - this.scrollerWidth, this.maxScrollY = this.wrapperHeight - this.scrollerHeight, this.hasHorizontalScroll = this.options.scrollX && this.maxScrollX < 0, this.hasVerticalScroll = this.options.scrollY && this.maxScrollY < 0, this.hasHorizontalScroll || (this.maxScrollX = 0, this.scrollerWidth = this.wrapperWidth), this.hasVerticalScroll || (this.maxScrollY = 0, this.scrollerHeight = this.wrapperHeight), this.endTime = 0, this.directionX = 0, this.directionY = 0, this.wrapperOffset = a.offset(this.wrapper), this._execEvent("refresh"), this.resetPosition()
            }, on: function (t, e) {
                this._events[t] || (this._events[t] = []), this._events[t].push(e)
            }, off: function (t, e) {
                if (this._events[t]) {
                    var n = this._events[t].indexOf(e);
                    n > -1 && this._events[t].splice(n, 1)
                }
            }, _execEvent: function (t) {
                if (this._events[t]) {
                    var e = 0,
                        n = this._events[t].length;
                    if (n)
                        for (; n > e; e++) this._events[t][e].apply(this, [].slice.call(arguments, 1))
                }
            }, scrollBy: function (t, e, n, i) {
                t = this.x + t, e = this.y + e, n = n || 0, this.scrollTo(t, e, n, i)
            }, scrollTo: function (t, e, n, i) {
                i = i || a.ease.circular, this.isInTransition = this.options.useTransition && n > 0, !n || this.options.useTransition && i.style ? (this._transitionTimingFunction(i.style), this._transitionTime(n), this._translate(t, e)) : this._animate(t, e, n, i.fn)
            }, scrollToElement: function (t, e, n, i, o) {
                if (t = t.nodeType ? t : this.scroller.querySelector(t)) {
                    var s = a.offset(t);
                    s.left -= this.wrapperOffset.left, s.top -= this.wrapperOffset.top, n === !0 && (n = Math.round(t.offsetWidth / 2 - this.wrapper.offsetWidth / 2)), i === !0 && (i = Math.round(t.offsetHeight / 2 - this.wrapper.offsetHeight / 2)), s.left -= n || 0, s.top -= i || 0, s.left = s.left > 0 ? 0 : s.left < this.maxScrollX ? this.maxScrollX : s.left, s.top = s.top > 0 ? 0 : s.top < this.maxScrollY ? this.maxScrollY : s.top, e = void 0 === e || null === e || "auto" === e ? Math.max(Math.abs(this.x - s.left), Math.abs(this.y - s.top)) : e, this.scrollTo(s.left, s.top, e, o)
                }
            }, _transitionTime: function (t) {
                if (t = t || 0, this.scrollerStyle[a.style.transitionDuration] = t + "ms", !t && a.isBadAndroid && (this.scrollerStyle[a.style.transitionDuration] = "0.001s"), this.indicators)
                    for (var e = this.indicators.length; e--;) this.indicators[e].transitionTime(t)
            }, _transitionTimingFunction: function (t) {
                if (this.scrollerStyle[a.style.transitionTimingFunction] = t, this.indicators)
                    for (var e = this.indicators.length; e--;) this.indicators[e].transitionTimingFunction(t)
            }, _translate: function (t, e) {
                if (this.options.useTransform ? this.scrollerStyle[a.style.transform] = "translate(" + t + "px," + e + "px)" + this.translateZ : (t = Math.round(t),
                    e = Math.round(e), this.scrollerStyle.left = t + "px", this.scrollerStyle.top = e + "px"), this.x = t, this.y = e, this.indicators)
                    for (var n = this.indicators.length; n--;) this.indicators[n].updatePosition()
            }, _initEvents: function (e) {
                var n = e ? a.removeEvent : a.addEvent,
                    i = this.options.bindToWrapper ? this.wrapper : t;
                n(t, "orientationchange", this), n(t, "resize", this), this.options.click && n(this.wrapper, "click", this, !0), this.options.disableMouse || (n(this.wrapper, "mousedown", this), n(i, "mousemove", this), n(i, "mousecancel", this), n(i, "mouseup", this)), a.hasPointer && !this.options.disablePointer && (n(this.wrapper, a.prefixPointerEvent("pointerdown"), this), n(i, a.prefixPointerEvent("pointermove"), this), n(i, a.prefixPointerEvent("pointercancel"), this), n(i, a.prefixPointerEvent("pointerup"), this)), a.hasTouch && !this.options.disableTouch && (n(this.wrapper, "touchstart", this), n(i, "touchmove", this), n(i, "touchcancel", this), n(i, "touchend", this)), n(this.scroller, "transitionend", this), n(this.scroller, "webkitTransitionEnd", this), n(this.scroller, "oTransitionEnd", this), n(this.scroller, "MSTransitionEnd", this)
            }, getComputedPosition: function () {
                var e, n, i = t.getComputedStyle(this.scroller, null);
                return this.options.useTransform ? (i = i[a.style.transform].split(")")[0].split(", "), e = +(i[12] || i[4]), n = +(i[13] || i[5])) : (e = +i.left.replace(/[^-\d.]/g, ""), n = +i.top.replace(/[^-\d.]/g, "")), {
                    x: e,
                    y: n
                }
            }, _initIndicators: function () {
                function t(t) {
                    for (var e = r.indicators.length; e--;) t.call(r.indicators[e])
                }
                var e, o = this.options.interactiveScrollbars,
                    a = "string" != typeof this.options.scrollbars,
                    s = [],
                    r = this;
                this.indicators = [], this.options.scrollbars && (this.options.scrollY && (e = {
                    el: n("v", o, this.options.scrollbars),
                    interactive: o,
                    defaultScrollbars: !0,
                    customStyle: a,
                    resize: this.options.resizeScrollbars,
                    shrink: this.options.shrinkScrollbars,
                    fade: this.options.fadeScrollbars,
                    listenX: !1
                }, this.wrapper.appendChild(e.el), s.push(e)), this.options.scrollX && (e = {
                    el: n("h", o, this.options.scrollbars),
                    interactive: o,
                    defaultScrollbars: !0,
                    customStyle: a,
                    resize: this.options.resizeScrollbars,
                    shrink: this.options.shrinkScrollbars,
                    fade: this.options.fadeScrollbars,
                    listenY: !1
                }, this.wrapper.appendChild(e.el), s.push(e))), this.options.indicators && (s = s.concat(this.options.indicators));
                for (var l = s.length; l--;) this.indicators.push(new i(this, s[l]));
                this.options.fadeScrollbars && (this.on("scrollEnd", function () {
                    t(function () {
                        this.fade()
                    })
                }), this.on("scrollCancel", function () {
                    t(function () {
                        this.fade()
                    })
                }), this.on("scrollStart", function () {
                    t(function () {
                        this.fade(1)
                    })
                }), this.on("beforeScrollStart", function () {
                    t(function () {
                        this.fade(1, !0)
                    })
                })), this.on("refresh", function () {
                    t(function () {
                        this.refresh()
                    })
                }), this.on("destroy", function () {
                    t(function () {
                        this.destroy()
                    }), delete this.indicators
                })
            }, _initWheel: function () {
                a.addEvent(this.wrapper, "wheel", this), a.addEvent(this.wrapper, "mousewheel", this), a.addEvent(this.wrapper, "DOMMouseScroll", this), this.on("destroy", function () {
                    a.removeEvent(this.wrapper, "wheel", this), a.removeEvent(this.wrapper, "mousewheel", this), a.removeEvent(this.wrapper, "DOMMouseScroll", this)
                })
            }, _wheel: function (t) {
                if (this.enabled) {
                    t.preventDefault(), t.stopPropagation();
                    var e, n, i, o, a = this;
                    if (void 0 === this.wheelTimeout && a._execEvent("scrollStart"), clearTimeout(this.wheelTimeout), this.wheelTimeout = setTimeout(function () {
                        a._execEvent("scrollEnd"), a.wheelTimeout = void 0
                    }, 400), "deltaX" in t) 1 === t.deltaMode ? (e = -t.deltaX * this.options.mouseWheelSpeed, n = -t.deltaY * this.options.mouseWheelSpeed) : (e = -t.deltaX, n = -t.deltaY);
                    else if ("wheelDeltaX" in t) e = t.wheelDeltaX / 120 * this.options.mouseWheelSpeed, n = t.wheelDeltaY / 120 * this.options.mouseWheelSpeed;
                    else if ("wheelDelta" in t) e = n = t.wheelDelta / 120 * this.options.mouseWheelSpeed;
                    else {
                        if (!("detail" in t)) return;
                        e = n = -t.detail / 3 * this.options.mouseWheelSpeed
                    } if (e *= this.options.invertWheelDirection, n *= this.options.invertWheelDirection, this.hasVerticalScroll || (e = n, n = 0), this.options.snap) return i = this.currentPage.pageX, o = this.currentPage.pageY, e > 0 ? i-- : 0 > e && i++, n > 0 ? o-- : 0 > n && o++, void this.goToPage(i, o);
                    i = this.x + Math.round(this.hasHorizontalScroll ? e : 0), o = this.y + Math.round(this.hasVerticalScroll ? n : 0), i > 0 ? i = 0 : i < this.maxScrollX && (i = this.maxScrollX), o > 0 ? o = 0 : o < this.maxScrollY && (o = this.maxScrollY), this.scrollTo(i, o, 0), this._execEvent("scroll")
                }
            }, _initSnap: function () {
                this.currentPage = {}, "string" == typeof this.options.snap && (this.options.snap = this.scroller.querySelectorAll(this.options.snap)), this.on("refresh", function () {
                    var t, e, n, i, o, a, s = 0,
                        r = 0,
                        l = 0,
                        c = this.options.snapStepX || this.wrapperWidth,
                        h = this.options.snapStepY || this.wrapperHeight;
                    if (this.pages = [], this.wrapperWidth && this.wrapperHeight && this.scrollerWidth && this.scrollerHeight) {

                        if (this.options.snap === !0)
                            for (n = Math.round(c / 2), i = Math.round(h / 2); l > -this.scrollerWidth;) {
                                for (this.pages[s] = [], t = 0, o = 0; o > -this.scrollerHeight;) this.pages[s][t] = {
                                    x: Math.max(l, this.maxScrollX),
                                    y: Math.max(o, this.maxScrollY),
                                    width: c,
                                    height: h,
                                    cx: l - n,
                                    cy: o - i
                                }, o -= h, t++;
                                l -= c, s++
                            } else
                                for (a = this.options.snap, t = a.length, e = -1; t > s; s++)(0 === s || a[s].offsetLeft <= a[s - 1].offsetLeft) && (r = 0, e++), this.pages[r] || (this.pages[r] = []), l = Math.max(-a[s].offsetLeft, this.maxScrollX), o = Math.max(-a[s].offsetTop, this.maxScrollY), n = l - Math.round(a[s].offsetWidth / 2), i = o - Math.round(a[s].offsetHeight / 2), this.pages[r][e] = {
                                    x: l,
                                    y: o,
                                    width: a[s].offsetWidth,
                                    height: a[s].offsetHeight,
                                    cx: n,
                                    cy: i
                                }, l > this.maxScrollX && r++;
                        this.goToPage(this.currentPage.pageX || 0, this.currentPage.pageY || 0, 0), this.options.snapThreshold % 1 === 0 ? (this.snapThresholdX = this.options.snapThreshold, this.snapThresholdY = this.options.snapThreshold) : (this.snapThresholdX = Math.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].width * this.options.snapThreshold), this.snapThresholdY = Math.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].height * this.options.snapThreshold))
                    }
                }), this.on("flick", function () {
                    var t = this.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(this.x - this.startX), 1e3), Math.min(Math.abs(this.y - this.startY), 1e3)), 300);
                    this.goToPage(this.currentPage.pageX + this.directionX, this.currentPage.pageY + this.directionY, t)
                })
            }, _nearestSnap: function (t, e) {
                if (!this.pages.length) return {
                    x: 0,
                    y: 0,
                    pageX: 0,
                    pageY: 0
                };
                var n = 0,
                    i = this.pages.length,
                    o = 0;
                if (Math.abs(t - this.absStartX) < this.snapThresholdX && Math.abs(e - this.absStartY) < this.snapThresholdY) return this.currentPage;
                for (t > 0 ? t = 0 : t < this.maxScrollX && (t = this.maxScrollX), e > 0 ? e = 0 : e < this.maxScrollY && (e = this.maxScrollY); i > n; n++)
                    if (t >= this.pages[n][0].cx) {
                        t = this.pages[n][0].x;
                        break
                    }
                for (i = this.pages[n].length; i > o; o++)
                    if (e >= this.pages[0][o].cy) {
                        e = this.pages[0][o].y;
                        break
                    }
                return n === this.currentPage.pageX && (n += this.directionX, 0 > n ? n = 0 : n >= this.pages.length && (n = this.pages.length - 1), t = this.pages[n][0].x), o === this.currentPage.pageY && (o += this.directionY, 0 > o ? o = 0 : o >= this.pages[0].length && (o = this.pages[0].length - 1), e = this.pages[0][o].y), {
                    x: t,
                    y: e,
                    pageX: n,
                    pageY: o
                }
            }, goToPage: function (t, e, n, i) {
                i = i || this.options.bounceEasing, t >= this.pages.length ? t = this.pages.length - 1 : 0 > t && (t = 0), e >= this.pages[t].length ? e = this.pages[t].length - 1 : 0 > e && (e = 0);
                var o = this.pages[t][e].x,
                    a = this.pages[t][e].y;
                n = void 0 === n ? this.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(o - this.x), 1e3), Math.min(Math.abs(a - this.y), 1e3)), 300) : n, this.currentPage = {
                    x: o,
                    y: a,
                    pageX: t,
                    pageY: e
                }, this.scrollTo(o, a, n, i)
            }, next: function (t, e) {
                var n = this.currentPage.pageX,
                    i = this.currentPage.pageY;
                n++, n >= this.pages.length && this.hasVerticalScroll && (n = 0, i++), this.goToPage(n, i, t, e)
            }, prev: function (t, e) {
                var n = this.currentPage.pageX,
                    i = this.currentPage.pageY;
                n--, 0 > n && this.hasVerticalScroll && (n = 0, i--), this.goToPage(n, i, t, e)
            }, _initKeys: function () {
                var e, n = {
                    pageUp: 33,
                    pageDown: 34,
                    end: 35,
                    home: 36,
                    left: 37,
                    up: 38,
                    right: 39,
                    down: 40
                };
                if ("object" == typeof this.options.keyBindings)
                    for (e in this.options.keyBindings) "string" == typeof this.options.keyBindings[e] && (this.options.keyBindings[e] = this.options.keyBindings[e].toUpperCase().charCodeAt(0));
                else this.options.keyBindings = {};
                for (e in n) this.options.keyBindings[e] = this.options.keyBindings[e] || n[e];
                a.addEvent(t, "keydown", this), this.on("destroy", function () {
                    a.removeEvent(t, "keydown", this)
                })
            }, _key: function (t) {
                if (this.enabled) {
                    var e, n = this.options.snap,
                        i = n ? this.currentPage.pageX : this.x,
                        o = n ? this.currentPage.pageY : this.y,
                        s = a.getTime(),
                        r = this.keyTime || 0,
                        l = .25;
                    switch (this.options.useTransition && this.isInTransition && (e = this.getComputedPosition(), this._translate(Math.round(e.x), Math.round(e.y)), this.isInTransition = !1), this.keyAcceleration = 200 > s - r ? Math.min(this.keyAcceleration + l, 50) : 0, t.keyCode) {
                    case this.options.keyBindings.pageUp:
                        this.hasHorizontalScroll && !this.hasVerticalScroll ? i += n ? 1 : this.wrapperWidth : o += n ? 1 : this.wrapperHeight;
                        break;
                    case this.options.keyBindings.pageDown:
                        this.hasHorizontalScroll && !this.hasVerticalScroll ? i -= n ? 1 : this.wrapperWidth : o -= n ? 1 : this.wrapperHeight;
                        break;
                    case this.options.keyBindings.end:
                        i = n ? this.pages.length - 1 : this.maxScrollX, o = n ? this.pages[0].length - 1 : this.maxScrollY;
                        break;
                    case this.options.keyBindings.home:
                        i = 0, o = 0;
                        break;
                    case this.options.keyBindings.left:
                        i += n ? -1 : 5 + this.keyAcceleration >> 0;
                        break;
                    case this.options.keyBindings.up:
                        o += n ? 1 : 5 + this.keyAcceleration >> 0;
                        break;
                    case this.options.keyBindings.right:
                        i -= n ? -1 : 5 + this.keyAcceleration >> 0;
                        break;
                    case this.options.keyBindings.down:
                        o -= n ? 1 : 5 + this.keyAcceleration >> 0;
                        break;
                    default:
                        return
                    }
                    if (n) return void this.goToPage(i, o);
                    i > 0 ? (i = 0, this.keyAcceleration = 0) : i < this.maxScrollX && (i = this.maxScrollX, this.keyAcceleration = 0), o > 0 ? (o = 0, this.keyAcceleration = 0) : o < this.maxScrollY && (o = this.maxScrollY, this.keyAcceleration = 0), this.scrollTo(i, o, 0), this.keyTime = s
                }
            }, _animate: function (t, e, n, i) {
                function s() {
                    var d, u, f, m = a.getTime();
                    return m >= p ? (r.isAnimating = !1, r._translate(t, e), void(r.resetPosition(r.options.bounceTime) || r._execEvent("scrollEnd"))) : (m = (m - h) / n, f = i(m), d = (t - l) * f + l, u = (e - c) * f + c, r._translate(d, u), r.isAnimating && o(s), void(3 === r.options.probeType && r._execEvent("scroll")))
                }
                var r = this,
                    l = this.x,
                    c = this.y,
                    h = a.getTime(),
                    p = h + n;
                this.isAnimating = !0, s()
            }, handleEvent: function (t) {
                switch (t.type) {
                case "touchstart":
                case "pointerdown":
                case "MSPointerDown":
                case "mousedown":
                    this._start(t);
                    break;
                case "touchmove":
                case "pointermove":
                case "MSPointerMove":
                case "mousemove":
                    this._move(t);
                    break;
                case "touchend":
                case "pointerup":
                case "MSPointerUp":
                case "mouseup":
                case "touchcancel":
                case "pointercancel":
                case "MSPointerCancel":
                case "mousecancel":
                    this._end(t);
                    break;
                case "orientationchange":
                case "resize":
                    this._resize();
                    break;
                case "transitionend":
                case "webkitTransitionEnd":
                case "oTransitionEnd":
                case "MSTransitionEnd":
                    this._transitionEnd(t);
                    break;
                case "wheel":
                case "DOMMouseScroll":
                case "mousewheel":
                    this._wheel(t);
                    break;
                case "keydown":
                    this._key(t);
                    break;
                case "click":
                    t._constructed || (t.preventDefault(), t.stopPropagation())
                }
            }
        }, i.prototype = {
            handleEvent: function (t) {
                switch (t.type) {
                case "touchstart":
                case "pointerdown":
                case "MSPointerDown":
                case "mousedown":
                    this._start(t);
                    break;
                case "touchmove":
                case "pointermove":
                case "MSPointerMove":
                case "mousemove":
                    this._move(t);
                    break;
                case "touchend":
                case "pointerup":
                case "MSPointerUp":
                case "mouseup":
                case "touchcancel":
                case "pointercancel":
                case "MSPointerCancel":
                case "mousecancel":
                    this._end(t)
                }
            }, destroy: function () {
                this.options.interactive && (a.removeEvent(this.indicator, "touchstart", this), a.removeEvent(this.indicator, a.prefixPointerEvent("pointerdown"), this), a.removeEvent(this.indicator, "mousedown", this), a.removeEvent(t, "touchmove", this), a.removeEvent(t, a.prefixPointerEvent("pointermove"), this), a.removeEvent(t, "mousemove", this), a.removeEvent(t, "touchend", this), a.removeEvent(t, a.prefixPointerEvent("pointerup"), this), a.removeEvent(t, "mouseup", this)), this.options.defaultScrollbars && this.wrapper.parentNode.removeChild(this.wrapper)
            }, _start: function (e) {
                var n = e.touches ? e.touches[0] : e;
                e.preventDefault(), e.stopPropagation(), this.transitionTime(), this.initiated = !0, this.moved = !1, this.lastPointX = n.pageX, this.lastPointY = n.pageY, this.startTime = a.getTime(), this.options.disableTouch || a.addEvent(t, "touchmove", this), this.options.disablePointer || a.addEvent(t, a.prefixPointerEvent("pointermove"), this), this.options.disableMouse || a.addEvent(t, "mousemove", this), this.scroller._execEvent("beforeScrollStart")
            }, _move: function (t) {
                var e, n, i, o, s = t.touches ? t.touches[0] : t,
                    r = a.getTime();
                this.moved || this.scroller._execEvent("scrollStart"), this.moved = !0, e = s.pageX - this.lastPointX, this.lastPointX = s.pageX, n = s.pageY - this.lastPointY, this.lastPointY = s.pageY, i = this.x + e, o = this.y + n, this._pos(i, o), 1 === this.scroller.options.probeType && r - this.startTime > 300 ? (this.startTime = r, this.scroller._execEvent("scroll")) : this.scroller.options.probeType > 1 && this.scroller._execEvent("scroll"), t.preventDefault(), t.stopPropagation()
            }, _end: function (e) {
                if (this.initiated) {
                    if (this.initiated = !1, e.preventDefault(), e.stopPropagation(), a.removeEvent(t, "touchmove", this), a.removeEvent(t, a.prefixPointerEvent("pointermove"), this), a.removeEvent(t, "mousemove", this), this.scroller.options.snap) {
                        var n = this.scroller._nearestSnap(this.scroller.x, this.scroller.y),
                            i = this.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(this.scroller.x - n.x), 1e3), Math.min(Math.abs(this.scroller.y - n.y), 1e3)), 300);
                        this.scroller.x === n.x && this.scroller.y === n.y || (this.scroller.directionX = 0, this.scroller.directionY = 0, this.scroller.currentPage = n, this.scroller.scrollTo(n.x, n.y, i, this.scroller.options.bounceEasing))
                    }
                    this.moved && this.scroller._execEvent("scrollEnd")
                }
            }, transitionTime: function (t) {
                t = t || 0, this.indicatorStyle[a.style.transitionDuration] = t + "ms", !t && a.isBadAndroid && (this.indicatorStyle[a.style.transitionDuration] = "0.001s")
            }, transitionTimingFunction: function (t) {
                this.indicatorStyle[a.style.transitionTimingFunction] = t
            }, refresh: function () {
                this.transitionTime(), this.options.listenX && !this.options.listenY ? this.indicatorStyle.display = this.scroller.hasHorizontalScroll ? "block" : "none" : this.options.listenY && !this.options.listenX ? this.indicatorStyle.display = this.scroller.hasVerticalScroll ? "block" : "none" : this.indicatorStyle.display = this.scroller.hasHorizontalScroll || this.scroller.hasVerticalScroll ? "block" : "none", this.scroller.hasHorizontalScroll && this.scroller.hasVerticalScroll ? (a.addClass(this.wrapper, "iScrollBothScrollbars"), a.removeClass(this.wrapper, "iScrollLoneScrollbar"), this.options.defaultScrollbars && this.options.customStyle && (this.options.listenX ? this.wrapper.style.right = "8px" : this.wrapper.style.bottom = "8px")) : (a.removeClass(this.wrapper, "iScrollBothScrollbars"), a.addClass(this.wrapper, "iScrollLoneScrollbar"), this.options.defaultScrollbars && this.options.customStyle && (this.options.listenX ? this.wrapper.style.right = "2px" : this.wrapper.style.bottom = "2px")), this.options.listenX && (this.wrapperWidth = this.wrapper.clientWidth, this.options.resize ? (this.indicatorWidth = Math.max(Math.round(this.wrapperWidth * this.wrapperWidth / (this.scroller.scrollerWidth || this.wrapperWidth || 1)), 8), this.indicatorStyle.width = this.indicatorWidth + "px") : this.indicatorWidth = this.indicator.clientWidth, this.maxPosX = this.wrapperWidth - this.indicatorWidth, "clip" === this.options.shrink ? (this.minBoundaryX = -this.indicatorWidth + 8, this.maxBoundaryX = this.wrapperWidth - 8) : (this.minBoundaryX = 0, this.maxBoundaryX = this.maxPosX), this.sizeRatioX = this.options.speedRatioX || this.scroller.maxScrollX && this.maxPosX / this.scroller.maxScrollX), this.options.listenY && (this.wrapperHeight = this.wrapper.clientHeight, this.options.resize ? (this.indicatorHeight = Math.max(Math.round(this.wrapperHeight * this.wrapperHeight / (this.scroller.scrollerHeight || this.wrapperHeight || 1)), 8), this.indicatorStyle.height = this.indicatorHeight + "px") : this.indicatorHeight = this.indicator.clientHeight, this.maxPosY = this.wrapperHeight - this.indicatorHeight, "clip" === this.options.shrink ? (this.minBoundaryY = -this.indicatorHeight + 8, this.maxBoundaryY = this.wrapperHeight - 8) : (this.minBoundaryY = 0, this.maxBoundaryY = this.maxPosY), this.maxPosY = this.wrapperHeight - this.indicatorHeight, this.sizeRatioY = this.options.speedRatioY || this.scroller.maxScrollY && this.maxPosY / this.scroller.maxScrollY), this.updatePosition()
            }, updatePosition: function () {
                var t = this.options.listenX && Math.round(this.sizeRatioX * this.scroller.x) || 0,
                    e = this.options.listenY && Math.round(this.sizeRatioY * this.scroller.y) || 0;
                this.options.ignoreBoundaries || (t < this.minBoundaryX ? ("scale" === this.options.shrink && (this.width = Math.max(this.indicatorWidth + t, 8), this.indicatorStyle.width = this.width + "px"), t = this.minBoundaryX) : t > this.maxBoundaryX ? "scale" === this.options.shrink ? (this.width = Math.max(this.indicatorWidth - (t - this.maxPosX), 8), this.indicatorStyle.width = this.width + "px", t = this.maxPosX + this.indicatorWidth - this.width) : t = this.maxBoundaryX : "scale" === this.options.shrink && this.width !== this.indicatorWidth && (this.width = this.indicatorWidth, this.indicatorStyle.width = this.width + "px"), e < this.minBoundaryY ? ("scale" === this.options.shrink && (this.height = Math.max(this.indicatorHeight + 3 * e, 8), this.indicatorStyle.height = this.height + "px"), e = this.minBoundaryY) : e > this.maxBoundaryY ? "scale" === this.options.shrink ? (this.height = Math.max(this.indicatorHeight - 3 * (e - this.maxPosY), 8), this.indicatorStyle.height = this.height + "px", e = this.maxPosY + this.indicatorHeight - this.height) : e = this.maxBoundaryY : "scale" === this.options.shrink && this.height !== this.indicatorHeight && (this.height = this.indicatorHeight, this.indicatorStyle.height = this.height + "px")), this.x = t, this.y = e, this.scroller.options.useTransform ? this.indicatorStyle[a.style.transform] = "translate(" + t + "px," + e + "px)" + this.scroller.translateZ : (this.indicatorStyle.left = t + "px", this.indicatorStyle.top = e + "px")
            }, _pos: function (t, e) {
                0 > t ? t = 0 : t > this.maxPosX && (t = this.maxPosX), 0 > e ? e = 0 : e > this.maxPosY && (e = this.maxPosY), t = this.options.listenX ? Math.round(t / this.sizeRatioX) : this.scroller.x, e = this.options.listenY ? Math.round(e / this.sizeRatioY) : this.scroller.y, this.scroller.scrollTo(t, e)
            }, fade: function (t, e) {
                if (!e || this.visible) {
                    clearTimeout(this.fadeTimeout), this.fadeTimeout = null;
                    var n = t ? 250 : 500,
                        i = t ? 0 : 300;
                    t = t ? "1" : "0", this.wrapperStyle[a.style.transitionDuration] = n + "ms", this.fadeTimeout = setTimeout(function (t) {
                        this.wrapperStyle.opacity = t, this.visible = +t
                    }.bind(this, t), i)
                }
            }
        }, e.utils = a, t.IScroll = e
    }(window), + function (t) {
        "use strict";

        function e(e) {
            var n = Array.apply(null, arguments);
            n.shift();
            var o;
            return this.each(function () {
                var a = t(this),
                    s = t.extend({}, a.dataset(), "object" == typeof e && e),
                    r = a.data("scroller");
                return r || a.data("scroller", r = new i(this, s)), "string" == typeof e && "function" == typeof r[e] && (o = r[e].apply(r, n), void 0 !== o) ? !1 : void 0
            }), void 0 !== o ? o : this
        }
        var n = {
            scrollTop: t.fn.scrollTop,
            scrollLeft: t.fn.scrollLeft
        };
        ! function () {
            t.extend(t.fn, {
                scrollTop: function (t, e) {
                    if (this.length) {
                        var i = this.data("scroller");
                        return i && i.scroller ? i.scrollTop(t, e) : n.scrollTop.apply(this, arguments)
                    }
                }
            }), t.extend(t.fn, {
                scrollLeft: function (t, e) {
                    if (this.length) {
                        var i = this.data("scroller");
                        return i && i.scroller ? i.scrollLeft(t, e) : n.scrollLeft.apply(this, arguments)
                    }
                }
            })
        }();
        var i = function (e, n) {
            var i = this.$pageContent = t(e);
            this.options = t.extend({}, this._defaults, n);
            var o = this.options.type,
                a = "js" === o || "auto" === o && t.device.android && t.compareVersion("4.4.0", t.device.osVersion) > -1 || "auto" === o && t.device.ios && t.compareVersion("6.0.0", t.device.osVersion) > -1;
            if (a) {
                var s = i.find(".content-inner");
                if (!s[0]) {
                    var r = i.children();
                    r.length < 1 ? i.children().wrapAll('<div class="content-inner"></div>') : i.html('<div class="content-inner">' + i.html() + "</div>")
                }
                if (i.hasClass("pull-to-refresh-content")) {
                    var l = t(window).height() + (i.prev().hasClass(".bar") ? 1 : 61);
                    i.find(".content-inner").css("min-height", l + "px")
                }
                var c = t(e).hasClass("pull-to-refresh-content"),
                    h = 0 === i.find(".fixed-tab").length,
                    p = {
                        probeType: 1,
                        mouseWheel: !0,
                        click: t.device.androidChrome,
                        useTransform: h,
                        scrollX: !0
                    };
                c && (p.ptr = !0, p.ptrOffset = 44), this.scroller = new IScroll(e, p), this._bindEventToDomWhenJs(), t.initPullToRefresh = t._pullToRefreshJSScroll.initPullToRefresh, t.pullToRefreshDone = t._pullToRefreshJSScroll.pullToRefreshDone, t.pullToRefreshTrigger = t._pullToRefreshJSScroll.pullToRefreshTrigger, t.destroyToRefresh = t._pullToRefreshJSScroll.destroyToRefresh, i.addClass("javascript-scroll"), h || i.find(".content-inner").css({
                    width: "100%",
                    position: "absolute"
                });
                var d = this.$pageContent[0].scrollTop;
                d && (this.$pageContent[0].scrollTop = 0, this.scrollTop(d))
            } else i.addClass("native-scroll")
        };
        i.prototype = {
            _defaults: {
                type: "native"
            },
            _bindEventToDomWhenJs: function () {
                if (this.scroller) {
                    var t = this;
                    this.scroller.on("scrollStart", function () {
                        t.$pageContent.trigger("scrollstart")
                    }), this.scroller.on("scroll", function () {
                        t.$pageContent.trigger("scroll")
                    }), this.scroller.on("scrollEnd", function () {
                        t.$pageContent.trigger("scrollend")
                    })
                }
            }, scrollTop: function (t, e) {
                return this.scroller ? void 0 === t ? -1 * this.scroller.getComputedPosition().y : (this.scroller.scrollTo(0, -1 * t, e), this) : this.$pageContent.scrollTop(t, e)
            }, scrollLeft: function (t, e) {
                return this.scroller ? void 0 === t ? -1 * this.scroller.getComputedPosition().x : (this.scroller.scrollTo(-1 * t, 0), this) : this.$pageContent.scrollTop(t, e)
            }, on: function (t, e) {
                return this.scroller ? this.scroller.on(t, function () {
                    e.call(this.wrapper)
                }) : this.$pageContent.on(t, e), this
            }, off: function (t, e) {
                return this.scroller ? this.scroller.off(t, e) : this.$pageContent.off(t, e), this
            }, refresh: function () {
                return this.scroller && this.scroller.refresh(), this
            }, scrollHeight: function () {
                return this.scroller ? this.scroller.scrollerHeight : this.$pageContent[0].scrollHeight
            }
        };
        var o = t.fn.scroller;
        t.fn.scroller = e, t.fn.scroller.Constructor = i, t.fn.scroller.noConflict = function () {
            return t.fn.scroller = o, this
        }, t(function () {
            t('[data-toggle="scroller"]').scroller()
        }), t.refreshScroller = function (e) {
            e ? t(e).scroller("refresh") : t(".javascript-scroll").each(function () {
                t(this).scroller("refresh")
            })
        }, t.initScroller = function (e) {
            this.options = t.extend({}, "object" == typeof e && e), t('[data-toggle="scroller"],.content').scroller(e)
        }, t.getScroller = function (e) {
            return e = e.hasClass("content") ? e : e.parents(".content"), e ? t(e).data("scroller") : t(".content.javascript-scroll").data("scroller")
        }, t.detectScrollerType = function (e) {
            return e ? t(e).data("scroller") && t(e).data("scroller").scroller ? "js" : "native" : void 0
        }
    }(Zepto), + function (t) {
        "use strict";
        var e = function (e, n, i) {
                var o = t(e);
                if (2 === arguments.length && "boolean" == typeof n && (i = n), 0 === o.length) return !1;
                if (o.hasClass("active")) return i && o.trigger("show"), !1;
                var a = o.parent(".tabs");
                if (0 === a.length) return !1;
                var s = a.children(".tab.active").removeClass("active");
                if (o.addClass("active"), o.trigger("show"), n ? n = t(n) : (n = t("string" == typeof e ? '.tab-link[href="' + e + '"]' : '.tab-link[href="#' + o.attr("id") + '"]'), (!n || n && 0 === n.length) && t("[data-tab]").each(function () {
                    o.is(t(this).attr("data-tab")) && (n = t(this))
                })), 0 !== n.length) {
                    var r;
                    if (s && s.length > 0) {
                        var l = s.attr("id");
                        l && (r = t('.tab-link[href="#' + l + '"]')), (!r || r && 0 === r.length) && t("[data-tab]").each(function () {
                            s.is(t(this).attr("data-tab")) && (r = t(this))
                        })
                    }
                    return n && n.length > 0 && n.addClass("active"), r && r.length > 0 && r.removeClass("active"), n.trigger("active"), !0
                }
            },
            n = t.showTab;
        t.showTab = e, t.showTab.noConflict = function () {
            return t.showTab = n, this
        }, t(document).on("click", ".tab-link", function (n) {
            n.preventDefault();
            var i = t(this);
            e(i.data("tab") || i.attr("href"), i)
        })
    }(Zepto), + function (t) {
        "use strict";

        function e(e) {
            var i = Array.apply(null, arguments);
            i.shift(), this.each(function () {
                var i = t(this),
                    o = t.extend({}, i.dataset(), "object" == typeof e && e),
                    a = i.data("fixedtab");
                a || i.data("fixedtab", a = new n(this, o))
            })
        }
        t.initFixedTab = function () {
            var e = t(".fixed-tab");
            0 !== e.length && t(".fixed-tab").fixedTab()
        };
        var n = function (e, n) {
            var i = this.$pageContent = t(e),
                o = i.clone(),
                a = i[0].getBoundingClientRect().top;
            o.css("visibility", "hidden"), this.options = t.extend({}, this._defaults, {
                fixedTop: a,
                shadow: o,
                offset: 0
            }, n), this._bindEvents()
        };
        n.prototype = {
            _defaults: {
                offset: 0
            },
            _bindEvents: function () {
                this.$pageContent.parents(".content").on("scroll", this._scrollHandler.bind(this)), this.$pageContent.on("active", ".tab-link", this._tabLinkHandler.bind(this))
            }, _tabLinkHandler: function (e) {
                var n = t(e.target).parents(".buttons-fixed").length > 0,
                    i = this.options.fixedTop,
                    o = this.options.offset;
                t.refreshScroller(), n && this.$pageContent.parents(".content").scrollTop(i - o)
            }, _scrollHandler: function (e) {
                var n = t(e.target),
                    i = this.$pageContent,
                    o = this.options.shadow,
                    a = this.options.offset,
                    s = this.options.fixedTop,
                    r = n.scrollTop(),
                    l = r >= s - a;
                l ? (o.insertAfter(i), i.addClass("buttons-fixed").css("top", a)) : (o.remove(), i.removeClass("buttons-fixed").css("top", 0))
            }
        }, t.fn.fixedTab = e, t.fn.fixedTab.Constructor = n, t(document).on("pageInit", function () {
            t.initFixedTab()
        })
    }(Zepto), + function (t) {
        "use strict";
        var e = 0,
            n = function (n) {
                function i() {
                    c.hasClass("refreshing") || (-1 * l.scrollTop() >= 44 ? c.removeClass("pull-down").addClass("pull-up") : c.removeClass("pull-up").addClass("pull-down"))
                }

                function o() {
                    c.hasClass("refreshing") || (c.removeClass("pull-down pull-up"), c.addClass("refreshing transitioning"), c.trigger("refresh"), e = +new Date)
                }

                function a() {
                    l.off("scroll", i), l.scroller.off("ptr", o)
                }
                var s = t(n);
                if (s.hasClass("pull-to-refresh-content") || (s = s.find(".pull-to-refresh-content")), s && 0 !== s.length) {
                    var r = s.hasClass("content") ? s : s.parents(".content"),
                        l = t.getScroller(r[0]);
                    if (l) {
                        var c = s;
                        l.on("scroll", i), l.scroller.on("ptr", o), s[0].destroyPullToRefresh = a
                    }
                }
            },
            i = function (n) {
                if (n = t(n), 0 === n.length && (n = t(".pull-to-refresh-content.refreshing")), 0 !== n.length) {
                    var i = +new Date - e,
                        o = i > 1e3 ? 0 : 1e3 - i,
                        a = t.getScroller(n);
                    setTimeout(function () {
                        a.refresh(), n.removeClass("refreshing"), n.transitionEnd(function () {
                            n.removeClass("transitioning")
                        })
                    }, o)
                }
            },
            o = function (e) {
                if (e = t(e), 0 === e.length && (e = t(".pull-to-refresh-content")), !e.hasClass("refreshing")) {
                    e.addClass("refreshing");
                    var n = t.getScroller(e);
                    n.scrollTop(45, 200), e.trigger("refresh")
                }
            },
            a = function (e) {
                e = t(e);
                var n = e.hasClass("pull-to-refresh-content") ? e : e.find(".pull-to-refresh-content");
                0 !== n.length && n[0].destroyPullToRefresh && n[0].destroyPullToRefresh()
            };
        t._pullToRefreshJSScroll = {
            initPullToRefresh: n,
            pullToRefreshDone: i,
            pullToRefreshTrigger: o,
            destroyPullToRefresh: a
        }
    }(Zepto), + function (t) {
        "use strict";
        t.initPullToRefresh = function (e) {
            function n(e) {
                if (r) {
                    if (!t.device.android) return;
                    if ("targetTouches" in e && e.targetTouches.length > 1) return
                }
                l = !1, r = !0, c = void 0, m = void 0, y.x = "touchstart" === e.type ? e.targetTouches[0].pageX : e.pageX, y.y = "touchstart" === e.type ? e.targetTouches[0].pageY : e.pageY, p = (new Date).getTime(), d = t(this)
            }

            function i(e) {
                if (r) {
                    var n = "touchmove" === e.type ? e.targetTouches[0].pageX : e.pageX,
                        i = "touchmove" === e.type ? e.targetTouches[0].pageY : e.pageY;
                    if ("undefined" == typeof c && (c = !!(c || Math.abs(i - y.y) > Math.abs(n - y.x))), !c) return void(r = !1);
                    if (f = d[0].scrollTop, "undefined" == typeof m && 0 !== f && (m = !0), !l) {
                        if (d.removeClass("transitioning"), f > d[0].offsetHeight) return void(r = !1);
                        g && (v = d.attr("data-ptr-distance"), v.indexOf("%") >= 0 && (v = d[0].offsetHeight * parseInt(v, 10) / 100)), T = d.hasClass("refreshing") ? v : 0, x = d[0].scrollHeight === d[0].offsetHeight || !t.device.ios, x = !0
                    }
                    return l = !0, h = i - y.y, h > 0 && 0 >= f || 0 > f ? (t.device.ios && parseInt(t.device.osVersion.split(".")[0], 10) > 7 && 0 === f && !m && (x = !0), x && (e.preventDefault(), u = Math.pow(h, .85) + T, d.transform("translate3d(0," + u + "px,0)")), x && Math.pow(h, .85) > v || !x && h >= 2 * v ? (w = !0, d.addClass("pull-up").removeClass("pull-down")) : (w = !1, d.removeClass("pull-up").addClass("pull-down")), void 0) : (d.removeClass("pull-up pull-down"), void(w = !1))
                }
            }

            function o() {
                if (!r || !l) return r = !1, void(l = !1);
                if (u && (d.addClass("transitioning"), u = 0), d.transform(""), w) {
                    if (d.hasClass("refreshing")) return;
                    d.addClass("refreshing"), d.trigger("refresh")
                } else d.removeClass("pull-down");
                r = !1, l = !1
            }

            function a() {
                s.off(t.touchEvents.start, n), s.off(t.touchEvents.move, i), s.off(t.touchEvents.end, o)
            }
            var s = t(e);
            if (s.hasClass("pull-to-refresh-content") || (s = s.find(".pull-to-refresh-content")), s && 0 !== s.length) {
                var r, l, c, h, p, d, u, f, m, v, g, y = {},
                    w = !1,
                    x = !1,
                    T = 0;
                d = s, d.attr("data-ptr-distance") ? g = !0 : v = 44, s.on(t.touchEvents.start, n), s.on(t.touchEvents.move, i), s.on(t.touchEvents.end, o), s[0].destroyPullToRefresh = a
            }
        }, t.pullToRefreshDone = function (e) {
            t(window).scrollTop(0), e = t(e), 0 === e.length && (e = t(".pull-to-refresh-content.refreshing")), e.removeClass("refreshing").addClass("transitioning"), e.transitionEnd(function () {
                e.removeClass("transitioning pull-up pull-down")
            })
        }, t.pullToRefreshTrigger = function (e) {
            e = t(e), 0 === e.length && (e = t(".pull-to-refresh-content")), e.hasClass("refreshing") || (e.addClass("transitioning refreshing"), e.trigger("refresh"))
        }, t.destroyPullToRefresh = function (e) {
            e = t(e);
            var n = e.hasClass("pull-to-refresh-content") ? e : e.find(".pull-to-refresh-content");
            0 !== n.length && n[0].destroyPullToRefresh && n[0].destroyPullToRefresh()
        }
    }(Zepto), + function (t) {
        "use strict";

        function e() {
            var e, n = t(this),
                i = t.getScroller(n),
                o = i.scrollTop(),
                a = i.scrollHeight(),
                s = n[0].offsetHeight,
                r = n[0].getAttribute("data-distance"),
                l = n.find(".virtual-list"),
                c = n.hasClass("infinite-scroll-top");
            if (r || (r = 50), "string" == typeof r && r.indexOf("%") >= 0 && (r = parseInt(r, 10) / 100 * s), r > s && (r = s), c) r > o && n.trigger("infinite");
            else if (o + s >= a - r) {
                if (l.length > 0 && (e = l[0].f7VirtualList, e && !e.reachEnd)) return;
                n.trigger("infinite")
            }
        }
        t.attachInfiniteScroll = function (n) {
            t.getScroller(n).on("scroll", e)
        }, t.detachInfiniteScroll = function (n) {
            t.getScroller(n).off("scroll", e)
        }, t.initInfiniteScroll = function (e) {
            function n() {
                t.detachInfiniteScroll(i), e.off("pageBeforeRemove", n)
            }
            e = t(e);
            var i = e.hasClass("infinite-scroll") ? e : e.find(".infinite-scroll");
            0 !== i.length && (t.attachInfiniteScroll(i), e.forEach(function (e) {
                if (t(e).hasClass("infinite-scroll-top")) {
                    var n = e.scrollHeight - e.clientHeight;
                    t(e).scrollTop(n)
                }
            }), e.on("pageBeforeRemove", n))
        }
    }(Zepto), + function (t) {
        "use strict";
        t(function () {
            t(document).on("focus", ".searchbar input", function (e) {
                var n = t(e.target);
                n.parents(".searchbar").addClass("searchbar-active")
            }), t(document).on("click", ".searchbar-cancel", function (e) {
                var n = t(e.target);
                n.parents(".searchbar").removeClass("searchbar-active")
            }), t(document).on("blur", ".searchbar input", function (e) {
                var n = t(e.target);
                n.parents(".searchbar").removeClass("searchbar-active")
            })
        })
    }(Zepto), + function (t) {
        "use strict";
        t.allowPanelOpen = !0, t.openPanel = function (e) {
            function n() {
                a.transitionEnd(function (i) {
                    i.target === a[0] ? (e.hasClass("active") ? e.trigger("opened") : e.trigger("closed"), t.allowPanelOpen = !0) : n()
                })
            }
            if (!t.allowPanelOpen) return !1;
            "left" !== e && "right" !== e || (e = ".panel-" + e), e = e ? t(e) : t(".panel").eq(0);
            var i = e.hasClass("panel-right") ? "right" : "left";
            if (0 === e.length || e.hasClass("active")) return !1;
            t.closePanel(), t.allowPanelOpen = !1;
            var o = e.hasClass("panel-reveal") ? "reveal" : "cover";
            e.css({
                display: "block"
            }).addClass("active"), e.trigger("open");
            var a = (e[0].clientLeft, "reveal" === o ? t(t.getCurrentPage()) : e);
            return n(), t(document.body).addClass("with-panel-" + i + "-" + o), !0
        }, t.closePanel = function () {
            var e = t(".panel.active");
            if (0 === e.length) return !1;
            var n = e.hasClass("panel-reveal") ? "reveal" : "cover",
                i = e.hasClass("panel-left") ? "left" : "right";
            e.removeClass("active");
            var o = "reveal" === n ? t(".page") : e;
            e.trigger("close"), t.allowPanelOpen = !1, o.transitionEnd(function () {
                e.hasClass("active") || (e.css({
                    display: ""
                }), e.trigger("closed"), t("body").removeClass("panel-closing"), t.allowPanelOpen = !0)
            }), t("body").addClass("panel-closing").removeClass("with-panel-" + i + "-" + n)
        }, t(document).on("click", ".open-panel", function (e) {
            var n = t(e.target).data("panel");
            t.openPanel(n)
        }), t(document).on("click", ".close-panel, .panel-overlay", function (e) {
            t.closePanel()
        }), t.initSwipePanels = function () {
            function e(e) {
                if (t.allowPanelOpen && (s || r) && !d && !(t(".modal-in, .photo-browser-in").length > 0) && (l || r || !(t(".panel.active").length > 0) || o.hasClass("active"))) {
                    if (b.x = "touchstart" === e.type ? e.targetTouches[0].pageX : e.pageX, b.y = "touchstart" === e.type ? e.targetTouches[0].pageY : e.pageY, l || r) {
                        if (t(".panel.active").length > 0) a = t(".panel.active").hasClass("panel-left") ? "left" : "right";
                        else {
                            if (r) return;
                            a = s
                        } if (!a) return
                    }
                    if (o = t(".panel.panel-" + a), o[0]) {
                        if (y = o.hasClass("active"), c && !y) {
                            if ("left" === a && b.x > c) return;
                            if ("right" === a && b.x < window.innerWidth - c) return
                        }
                        u = !1, d = !0, f = void 0, m = (new Date).getTime(), T = void 0
                    }
                }
            }

            function n(e) {
                if (d && o[0] && !e.f7PreventPanelSwipe) {
                    var n = "touchmove" === e.type ? e.targetTouches[0].pageX : e.pageX,
                        i = "touchmove" === e.type ? e.targetTouches[0].pageY : e.pageY;
                    if ("undefined" == typeof f && (f = !!(f || Math.abs(i - b.y) > Math.abs(n - b.x))), f) return void(d = !1);
                    if (!T && (T = n > b.x ? "to-right" : "to-left", "left" === a && "to-left" === T && !o.hasClass("active") || "right" === a && "to-right" === T && !o.hasClass("active"))) return void(d = !1);
                    if (p) {
                        var s = (new Date).getTime() - m;
                        return 300 > s && ("to-left" === T && ("right" === a && t.openPanel(a), "left" === a && o.hasClass("active") && t.closePanel()), "to-right" === T && ("left" === a && t.openPanel(a),
                            "right" === a && o.hasClass("active") && t.closePanel())), d = !1, console.log(3), void(u = !1)
                    }
                    u || (x = o.hasClass("panel-cover") ? "cover" : "reveal", y || (o.show(), C.show()), w = o[0].offsetWidth, o.transition(0)), u = !0, e.preventDefault();
                    var r = y ? 0 : -h;
                    "right" === a && (r = -r), v = n - b.x + r, "right" === a ? (g = v - (y ? w : 0), g > 0 && (g = 0), -w > g && (g = -w)) : (g = v + (y ? w : 0), 0 > g && (g = 0), g > w && (g = w)), "reveal" === x ? (k.transform("translate3d(" + g + "px,0,0)").transition(0), C.transform("translate3d(" + g + "px,0,0)")) : o.transform("translate3d(" + g + "px,0,0)").transition(0)
                }
            }

            function i(e) {
                if (!d || !u) return d = !1, void(u = !1);
                d = !1, u = !1;
                var n, i = (new Date).getTime() - m,
                    s = 0 === g || Math.abs(g) === w;
                if (n = y ? g === -w ? "reset" : 300 > i && Math.abs(g) >= 0 || i >= 300 && Math.abs(g) <= w / 2 ? "left" === a && g === w ? "reset" : "swap" : "reset" : 0 === g ? "reset" : 300 > i && Math.abs(g) > 0 || i >= 300 && Math.abs(g) >= w / 2 ? "swap" : "reset", "swap" === n && (t.allowPanelOpen = !0, y ? (t.closePanel(), s && (o.css({
                    display: ""
                }), t("body").removeClass("panel-closing"))) : t.openPanel(a), s && (t.allowPanelOpen = !0)), "reset" === n)
                    if (y) t.allowPanelOpen = !0, t.openPanel(a);
                    else if (t.closePanel(), s) t.allowPanelOpen = !0, o.css({
                    display: ""
                });
                else {
                    var r = "reveal" === x ? k : o;
                    t("body").addClass("panel-closing"), r.transitionEnd(function () {
                        t.allowPanelOpen = !0, o.css({
                            display: ""
                        }), t("body").removeClass("panel-closing")
                    })
                }
                "reveal" === x && (k.transition(""), k.transform("")), o.transition("").transform(""), C.css({
                    display: ""
                }).transform("")
            }
            var o, a, s = t.smConfig.swipePanel,
                r = t.smConfig.swipePanelOnlyClose,
                l = !0,
                c = !1,
                h = 2,
                p = !1;
            if (s || r) {
                var d, u, f, m, v, g, y, w, x, T, C = t(".panel-overlay"),
                    b = {},
                    k = t(".page");
                t(document).on(t.touchEvents.start, e), t(document).on(t.touchEvents.move, n), t(document).on(t.touchEvents.end, i)
            }
        }, t.initSwipePanels()
    }(Zepto), + function (t) {
        "use strict";

        function e(t) {
            for (var e = ["external", "tab-link", "open-popup", "close-popup", "open-panel", "close-panel"], n = e.length - 1; n >= 0; n--)
                if (t.hasClass(e[n])) return !0;
            var i = t.get(0),
                o = i.getAttribute("href"),
                a = ["http", "https"];
            return /^(\w+):/.test(o) && a.indexOf(RegExp.$1) < 0 ? !0 : !!i.hasAttribute("external")
        }

        function n(e) {
            var n = t.smConfig.routerFilter;
            if (t.isFunction(n)) {
                var i = n(e);
                if ("boolean" == typeof i) return i
            }
            return !0
        }

        function i() {
            if (t.smConfig.router && a.supportStorage()) {
                var i = t("." + s.pageClass);
                if (!i.length) {
                    var o = "Disable router function because of no .page elements";
                    return void(window.console && window.console.warn && console.warn(o))
                }
                var r = t.router = new c;
                t(document).on("click", "a", function (i) {
                    var o = t(i.currentTarget),
                        a = n(o);
                    if (a && !e(o))
                        if (i.preventDefault(), o.hasClass("back")) r.back();
                        else {
                            var s = o.attr("href");
                            if (!s || "#" === s) return;
                            var l = "true" === o.attr("data-no-cache");
                            r.load(s, l)
                        }
                })
            }
        }
        window.CustomEvent || (window.CustomEvent = function (t, e) {
            e = e || {
                bubbles: !1,
                cancelable: !1,
                detail: void 0
            };
            var n = document.createEvent("CustomEvent");
            return n.initCustomEvent(t, e.bubbles, e.cancelable, e.detail), n
        }, window.CustomEvent.prototype = window.Event.prototype);
        var o = {
                pageLoadStart: "pageLoadStart",
                pageLoadCancel: "pageLoadCancel",
                pageLoadError: "pageLoadError",
                pageLoadComplete: "pageLoadComplete",
                pageAnimationStart: "pageAnimationStart",
                pageAnimationEnd: "pageAnimationEnd",
                beforePageRemove: "beforePageRemove",
                pageRemoved: "pageRemoved",
                beforePageSwitch: "beforePageSwitch",
                pageInit: "pageInitInternal"
            },
            a = {
                getUrlFragment: function (t) {
                    var e = t.indexOf("#");
                    return -1 === e ? "" : t.slice(e + 1)
                }, getAbsoluteUrl: function (t) {
                    var e = document.createElement("a");
                    e.setAttribute("href", t);
                    var n = e.href;
                    return e = null, n
                }, getBaseUrl: function (t) {
                    var e = t.indexOf("#");
                    return -1 === e ? t.slice(0) : t.slice(0, e)
                }, toUrlObject: function (t) {
                    var e = this.getAbsoluteUrl(t),
                        n = this.getBaseUrl(e),
                        i = this.getUrlFragment(t);
                    return {
                        base: n,
                        full: e,
                        original: t,
                        fragment: i
                    }
                }, supportStorage: function () {
                    var t = "sm.router.storage.ability";
                    try {
                        return sessionStorage.setItem(t, t), sessionStorage.removeItem(t), !0
                    } catch (e) {
                        return !1
                    }
                }
            },
            s = {
                sectionGroupClass: "page-group",
                curPageClass: "page-current",
                visiblePageClass: "page-visible",
                pageClass: "page"
            },
            r = {
                leftToRight: "from-left-to-right",
                rightToLeft: "from-right-to-left"
            },
            l = window.history,
            c = function () {
                this.sessionNames = {
                    currentState: "sm.router.currentState",
                    maxStateId: "sm.router.maxStateId"
                }, this._init(), this.xhr = null, window.addEventListener("popstate", this._onPopState.bind(this))
            };
        c.prototype._init = function () {
            this.$view = t("body"), this.cache = {};
            var e = t(document),
                n = location.href;
            this._saveDocumentIntoCache(e, n);
            var i, o, r = a.toUrlObject(n),
                c = e.find("." + s.pageClass),
                h = e.find("." + s.curPageClass),
                p = h.eq(0);
            if (r.fragment && (o = e.find("#" + r.fragment)), o && o.length ? h = o.eq(0) : h.length || (h = c.eq(0)), h.attr("id") || h.attr("id", this._generateRandomId()), p.length && p.attr("id") !== h.attr("id") ? (p.removeClass(s.curPageClass), h.addClass(s.curPageClass)) : h.addClass(s.curPageClass), i = h.attr("id"), null === l.state) {
                var d = {
                    id: this._getNextStateId(),
                    url: a.toUrlObject(n),
                    pageId: i
                };
                l.replaceState(d, "", n), this._saveAsCurrentState(d), this._incMaxStateId()
            }
        }, c.prototype.load = function (e, n) {
            void 0 === n && (n = !1), this._isTheSameDocument(location.href, e) ? this._switchToSection(a.getUrlFragment(e)) : (this._saveDocumentIntoCache(t(document), location.href), this._switchToDocument(e, n))
        }, c.prototype.forward = function () {
            l.forward()
        }, c.prototype.back = function () {
            l.back()
        }, c.prototype.loadPage = c.prototype.load, c.prototype._switchToSection = function (e) {
            if (e) {
                var n = this._getCurrentSection(),
                    i = t("#" + e);
                n !== i && (this._animateSection(n, i, r.rightToLeft), this._pushNewState("#" + e, e))
            }
        }, c.prototype._switchToDocument = function (t, e, n, i) {
            var o = a.toUrlObject(t).base;
            e && delete this.cache[o];
            var s = this.cache[o],
                r = this;
            s ? this._doSwitchDocument(t, n, i) : this._loadDocument(t, {
                success: function (e) {
                    try {
                        r._parseDocument(t, e), r._doSwitchDocument(t, n, i)
                    } catch (o) {
                        location.href = t
                    }
                }, error: function () {
                    location.href = t
                }
            })
        }, c.prototype._doSwitchDocument = function (e, n, i) {
            "undefined" == typeof n && (n = !0);
            var r, l = a.toUrlObject(e),
                c = this.$view.find("." + s.sectionGroupClass),
                h = t(t("<div></div>").append(this.cache[l.base].$content).html()),
                p = h.find("." + s.pageClass),
                d = h.find("." + s.curPageClass);
            l.fragment && (r = h.find("#" + l.fragment)), r && r.length ? d = r.eq(0) : d.length || (d = p.eq(0)), d.attr("id") || d.attr("id", this._generateRandomId());
            var u = this._getCurrentSection();
            u.trigger(o.beforePageSwitch, [u.attr("id"), u]), p.removeClass(s.curPageClass), d.addClass(s.curPageClass), this.$view.prepend(h), this._animateDocument(c, h, d, i), n && this._pushNewState(e, d.attr("id"))
        }, c.prototype._isTheSameDocument = function (t, e) {
            return a.toUrlObject(t).base === a.toUrlObject(e).base
        }, c.prototype._loadDocument = function (e, n) {
            this.xhr && this.xhr.readyState < 4 && (this.xhr.onreadystatechange = function () {}, this.xhr.abort(), this.dispatch(o.pageLoadCancel)), this.dispatch(o.pageLoadStart), n = n || {};
            var i = this;
            this.xhr = t.ajax({
                url: e,
                success: t.proxy(function (e, i, o) {
                    var a = t("<html></html>");
                    a.append(e), n.success && n.success.call(null, a, i, o)
                }, this),
                error: function (t, e, a) {
                    n.error && n.error.call(null, t, e, a), i.dispatch(o.pageLoadError)
                }, complete: function (t, e) {
                    n.complete && n.complete.call(null, t, e), i.dispatch(o.pageLoadComplete)
                }
            })
        }, c.prototype._parseDocument = function (t, e) {
            var n = e.find("." + s.sectionGroupClass);
            if (!n.length) throw new Error("missing router view mark: " + s.sectionGroupClass);
            this._saveDocumentIntoCache(e, t)
        }, c.prototype._saveDocumentIntoCache = function (e, n) {
            var i = a.toUrlObject(n).base,
                o = t(e);
            this.cache[i] = {
                $doc: o,
                $content: o.find("." + s.sectionGroupClass)
            }
        }, c.prototype._getLastState = function () {
            var t = sessionStorage.getItem(this.sessionNames.currentState);
            try {
                t = JSON.parse(t)
            } catch (e) {
                t = null
            }
            return t
        }, c.prototype._saveAsCurrentState = function (t) {
            sessionStorage.setItem(this.sessionNames.currentState, JSON.stringify(t))
        }, c.prototype._getNextStateId = function () {
            var t = sessionStorage.getItem(this.sessionNames.maxStateId);
            return t ? parseInt(t, 10) + 1 : 1
        }, c.prototype._incMaxStateId = function () {
            sessionStorage.setItem(this.sessionNames.maxStateId, this._getNextStateId())
        }, c.prototype._animateDocument = function (e, n, i, a) {
            var r = i.attr("id"),
                l = e.find("." + s.curPageClass);
            l.addClass(s.visiblePageClass).removeClass(s.curPageClass), i.trigger(o.pageAnimationStart, [r, i]), this._animateElement(e, n, a), e.animationEnd(function () {
                l.removeClass(s.visiblePageClass), t(window).trigger(o.beforePageRemove, [e]), e.remove(), t(window).trigger(o.pageRemoved)
            }), n.animationEnd(function () {
                i.trigger(o.pageAnimationEnd, [r, i]), i.trigger(o.pageInit, [r, i])
            })
        }, c.prototype._animateSection = function (t, e, n) {
            var i = e.attr("id");
            t.trigger(o.beforePageSwitch, [t.attr("id"), t]), t.removeClass(s.curPageClass), e.addClass(s.curPageClass), e.trigger(o.pageAnimationStart, [i, e]), this._animateElement(t, e, n), e.animationEnd(function () {
                e.trigger(o.pageAnimationEnd, [i, e]), e.trigger(o.pageInit, [i, e])
            })
        }, c.prototype._animateElement = function (t, e, n) {
            "undefined" == typeof n && (n = r.rightToLeft);
            var i, o, a = ["page-from-center-to-left", "page-from-center-to-right", "page-from-right-to-center", "page-from-left-to-center"].join(" ");
            switch (n) {
            case r.rightToLeft:
                i = "page-from-center-to-left", o = "page-from-right-to-center";
                break;
            case r.leftToRight:
                i = "page-from-center-to-right", o = "page-from-left-to-center";
                break;
            default:
                i = "page-from-center-to-left", o = "page-from-right-to-center"
            }
            t.removeClass(a).addClass(i), e.removeClass(a).addClass(o), t.animationEnd(function () {
                t.removeClass(a)
            }), e.animationEnd(function () {
                e.removeClass(a)
            })
        }, c.prototype._getCurrentSection = function () {
            return this.$view.find("." + s.curPageClass).eq(0)
        }, c.prototype._back = function (e, n) {
            if (this._isTheSameDocument(e.url.full, n.url.full)) {
                var i = t("#" + e.pageId);
                if (i.length) {
                    var o = this._getCurrentSection();
                    this._animateSection(o, i, r.leftToRight), this._saveAsCurrentState(e)
                } else location.href = e.url.full
            } else this._saveDocumentIntoCache(t(document), n.url.full), this._switchToDocument(e.url.full, !1, !1, r.leftToRight), this._saveAsCurrentState(e)
        }, c.prototype._forward = function (e, n) {
            if (this._isTheSameDocument(e.url.full, n.url.full)) {
                var i = t("#" + e.pageId);
                if (i.length) {
                    var o = this._getCurrentSection();
                    this._animateSection(o, i, r.rightToLeft), this._saveAsCurrentState(e)
                } else location.href = e.url.full
            } else this._saveDocumentIntoCache(t(document), n.url.full), this._switchToDocument(e.url.full, !1, !1, r.rightToLeft), this._saveAsCurrentState(e)
        }, c.prototype._onPopState = function (t) {
            var e = t.state;
            if (e && e.pageId) {
                var n = this._getLastState();
                return n ? void(e.id !== n.id && (e.id < n.id ? this._back(e, n) : this._forward(e, n))) : void(console.error && console.error("Missing last state when backward or forward"))
            }
        }, c.prototype._pushNewState = function (t, e) {
            var n = {
                id: this._getNextStateId(),
                pageId: e,
                url: a.toUrlObject(t)
            };
            l.pushState(n, "", t), this._saveAsCurrentState(n), this._incMaxStateId()
        }, c.prototype._generateRandomId = function () {
            return "page-" + +new Date
        }, c.prototype.dispatch = function (t) {
            var e = new CustomEvent(t, {
                bubbles: !0,
                cancelable: !0
            });
            window.dispatchEvent(e)
        }, t.routerReload = i, t(i)
    }(Zepto), + function (t) {
        "use strict";
        t.lastPosition = function (e) {
            function n(e, n) {
                o.forEach(function (i, o) {
                    if (0 !== t(i).length) {
                        var a = e,
                            s = sessionStorage.getItem(a);
                        n.find(i).scrollTop(parseInt(s))
                    }
                })
            }

            function i(e, n) {
                var i = e;
                o.forEach(function (e, o) {
                    0 !== t(e).length && sessionStorage.setItem(i, n.find(e).scrollTop())
                })
            }
            if (sessionStorage) {
                var o = e.needMemoryClass || [];
                t(window).off("beforePageSwitch").on("beforePageSwitch", function (t, e, n) {
                    i(e, n)
                }), t(window).off("pageAnimationStart").on("pageAnimationStart", function (t, e, i) {
                    n(e, i)
                })
            }
        }
    }(Zepto), + function (t) {
        "use strict";
        var e = function () {
            var e = t(".page-current");
            return e[0] || (e = t(".page").addClass("page-current")), e
        };
        t.initPage = function (n) {
            var i = e();
            i[0] || (i = t(document.body));
            var o = i.hasClass("content") ? i : i.find(".content");
            o.scroller(), t.initPullToRefresh(o), t.initInfiniteScroll(o), t.initCalendar(o), t.initSwiper && t.initSwiper(o)
        }, t.smConfig.showPageLoadingIndicator && (t(window).on("pageLoadStart", function () {
            t.showIndicator()
        }), t(window).on("pageAnimationStart", function () {
            t.hideIndicator()
        }), t(window).on("pageLoadCancel", function () {
            t.hideIndicator()
        }), t(window).on("pageLoadComplete", function () {
            t.hideIndicator()
        }), t(window).on("pageLoadError", function () {
            t.hideIndicator(), t.toast("加载失败")
        })), t(window).on("pageAnimationStart", function (e, n, i) {
            t.closeModal(), t.closePanel(), t("body").removeClass("panel-closing"), t.allowPanelOpen = !0
        }), t(window).on("pageInit", function () {
            t.hideIndicator(), t.lastPosition({
                needMemoryClass: [".content"]
            })
        }), window.addEventListener("pageshow", function (t) {
            t.persisted && location.reload()
        }), t.init = function () {
            var n = e(),
                i = n[0].id;
            t.initPage(), n.trigger("pageInit", [i, n])
        }, t(function () {
            FastClick.attach(document.body), t.smConfig.autoInit && t.init(), t(document).on("pageInitInternal", function (e, n, i) {
                t.init()
            })
        })
    }(Zepto), + function (t) {
        "use strict";
        if (t.device.ios) {
            var e = function (t) {
                    var e, n;
                    t = t || document.querySelector(t), t && t.addEventListener("touchstart", function (i) {
                        e = i.touches[0].pageY, n = t.scrollTop, 0 >= n && (t.scrollTop = 1), n + t.offsetHeight >= t.scrollHeight && (t.scrollTop = t.scrollHeight - t.offsetHeight - 1)
                    }, !1)
                },
                n = function () {
                    var n = t(".page-current").length > 0 ? ".page-current " : "",
                        i = t(n + ".content");
                    new e(i[0])
                };
            t(document).on(t.touchEvents.move, ".page-current .bar", function () {
                event.preventDefault()
            }), t(document).on("pageLoadComplete", function () {
                n()
            }), t(document).on("pageAnimationEnd", function () {
                n()
            }), n()
        }
    }(Zepto);
! function () {
    "use strict";

    function e(e) {
        e.fn.swiper = function (a) {
            var r;
            return e(this).each(function () {
                var e = new t(this, a);
                r || (r = e)
            }), r
        }
    }
    var a, t = function (e, i) {
        function s(e) {
            return Math.floor(e)
        }

        function n() {
            b.autoplayTimeoutId = setTimeout(function () {
                b.params.loop ? (b.fixLoop(), b._slideNext(), b.emit("onAutoplay", b)) : b.isEnd ? i.autoplayStopOnLast ? b.stopAutoplay() : (b._slideTo(0), b.emit("onAutoplay", b)) : (b._slideNext(), b.emit("onAutoplay", b))
            }, b.params.autoplay)
        }

        function o(e, t) {
            var r = a(e.target);
            if (!r.is(t))
                if ("string" == typeof t) r = r.parents(t);
                else if (t.nodeType) {
                var i;
                return r.parents().each(function (e, a) {
                    a === t && (i = t)
                }), i ? t : void 0
            }
            if (0 !== r.length) return r[0]
        }

        function l(e, a) {
            a = a || {};
            var t = window.MutationObserver || window.WebkitMutationObserver,
                r = new t(function (e) {
                    e.forEach(function (e) {
                        b.onResize(!0), b.emit("onObserverUpdate", b, e)
                    })
                });
            r.observe(e, {
                attributes: "undefined" == typeof a.attributes ? !0 : a.attributes,
                childList: "undefined" == typeof a.childList ? !0 : a.childList,
                characterData: "undefined" == typeof a.characterData ? !0 : a.characterData
            }), b.observers.push(r)
        }

        function p(e) {
            e.originalEvent && (e = e.originalEvent);
            var a = e.keyCode || e.charCode;
            if (!b.params.allowSwipeToNext && (b.isHorizontal() && 39 === a || !b.isHorizontal() && 40 === a)) return !1;
            if (!b.params.allowSwipeToPrev && (b.isHorizontal() && 37 === a || !b.isHorizontal() && 38 === a)) return !1;
            if (!(e.shiftKey || e.altKey || e.ctrlKey || e.metaKey || document.activeElement && document.activeElement.nodeName && ("input" === document.activeElement.nodeName.toLowerCase() || "textarea" === document.activeElement.nodeName.toLowerCase()))) {
                if (37 === a || 39 === a || 38 === a || 40 === a) {
                    var t = !1;
                    if (b.container.parents(".swiper-slide").length > 0 && 0 === b.container.parents(".swiper-slide-active").length) return;
                    var r = {
                            left: window.pageXOffset,
                            top: window.pageYOffset
                        },
                        i = window.innerWidth,
                        s = window.innerHeight,
                        n = b.container.offset();
                    b.rtl && (n.left = n.left - b.container[0].scrollLeft);
                    for (var o = [
                        [n.left, n.top],
                        [n.left + b.width, n.top],
                        [n.left, n.top + b.height],
                        [n.left + b.width, n.top + b.height]
                    ], l = 0; l < o.length; l++) {
                        var p = o[l];
                        p[0] >= r.left && p[0] <= r.left + i && p[1] >= r.top && p[1] <= r.top + s && (t = !0)
                    }
                    if (!t) return
                }
                b.isHorizontal() ? (37 !== a && 39 !== a || (e.preventDefault ? e.preventDefault() : e.returnValue = !1), (39 === a && !b.rtl || 37 === a && b.rtl) && b.slideNext(), (37 === a && !b.rtl || 39 === a && b.rtl) && b.slidePrev()) : (38 !== a && 40 !== a || (e.preventDefault ? e.preventDefault() : e.returnValue = !1), 40 === a && b.slideNext(), 38 === a && b.slidePrev())
            }
        }

        function d(e) {
            e.originalEvent && (e = e.originalEvent);
            var a = b.mousewheel.event,
                t = 0,
                r = b.rtl ? -1 : 1;
            if ("mousewheel" === a)
                if (b.params.mousewheelForceToAxis)
                    if (b.isHorizontal()) {
                        if (!(Math.abs(e.wheelDeltaX) > Math.abs(e.wheelDeltaY))) return;
                        t = e.wheelDeltaX * r
                    } else {
                        if (!(Math.abs(e.wheelDeltaY) > Math.abs(e.wheelDeltaX))) return;
                        t = e.wheelDeltaY
                    } else t = Math.abs(e.wheelDeltaX) > Math.abs(e.wheelDeltaY) ? -e.wheelDeltaX * r : -e.wheelDeltaY;
            else if ("DOMMouseScroll" === a) t = -e.detail;
            else if ("wheel" === a)
                if (b.params.mousewheelForceToAxis)
                    if (b.isHorizontal()) {
                        if (!(Math.abs(e.deltaX) > Math.abs(e.deltaY))) return;
                        t = -e.deltaX * r
                    } else {
                        if (!(Math.abs(e.deltaY) > Math.abs(e.deltaX))) return;
                        t = -e.deltaY
                    } else t = Math.abs(e.deltaX) > Math.abs(e.deltaY) ? -e.deltaX * r : -e.deltaY; if (0 !== t) {
                if (b.params.mousewheelInvert && (t = -t), b.params.freeMode) {
                    var i = b.getWrapperTranslate() + t * b.params.mousewheelSensitivity,
                        s = b.isBeginning,
                        n = b.isEnd;
                    if (i >= b.minTranslate() && (i = b.minTranslate()), i <= b.maxTranslate() && (i = b.maxTranslate()), b.setWrapperTransition(0), b.setWrapperTranslate(i), b.updateProgress(), b.updateActiveIndex(), (!s && b.isBeginning || !n && b.isEnd) && b.updateClasses(), b.params.freeModeSticky ? (clearTimeout(b.mousewheel.timeout), b.mousewheel.timeout = setTimeout(function () {
                        b.slideReset()
                    }, 300)) : b.params.lazyLoading && b.lazy && b.lazy.load(), 0 === i || i === b.maxTranslate()) return
                } else {
                    if ((new window.Date).getTime() - b.mousewheel.lastScrollTime > 60)
                        if (0 > t)
                            if (b.isEnd && !b.params.loop || b.animating) {
                                if (b.params.mousewheelReleaseOnEdges) return !0
                            } else b.slideNext();
                    else if (b.isBeginning && !b.params.loop || b.animating) {
                        if (b.params.mousewheelReleaseOnEdges) return !0
                    } else b.slidePrev();
                    b.mousewheel.lastScrollTime = (new window.Date).getTime()
                }
                return b.params.autoplay && b.stopAutoplay(), e.preventDefault ? e.preventDefault() : e.returnValue = !1, !1
            }
        }

        function c(e, t) {
            e = a(e);
            var r, i, s, n = b.rtl ? -1 : 1;
            r = e.attr("data-swiper-parallax") || "0", i = e.attr("data-swiper-parallax-x"), s = e.attr("data-swiper-parallax-y"), i || s ? (i = i || "0", s = s || "0") : b.isHorizontal() ? (i = r, s = "0") : (s = r, i = "0"), i = i.indexOf("%") >= 0 ? parseInt(i, 10) * t * n + "%" : i * t * n + "px", s = s.indexOf("%") >= 0 ? parseInt(s, 10) * t + "%" : s * t + "px", e.transform("translate3d(" + i + ", " + s + ",0px)")
        }

        function u(e) {
            return 0 !== e.indexOf("on") && (e = e[0] !== e[0].toUpperCase() ? "on" + e[0].toUpperCase() + e.substring(1) : "on" + e), e
        }
        if (!(this instanceof t)) return new t(e, i);
        var m = {
                direction: "horizontal",
                touchEventsTarget: "container",
                initialSlide: 0,
                speed: 300,
                autoplay: !1,
                autoplayDisableOnInteraction: !0,
                autoplayStopOnLast: !1,
                iOSEdgeSwipeDetection: !1,
                iOSEdgeSwipeThreshold: 20,
                freeMode: !1,
                freeModeMomentum: !0,
                freeModeMomentumRatio: 1,
                freeModeMomentumBounce: !0,
                freeModeMomentumBounceRatio: 1,
                freeModeSticky: !1,
                freeModeMinimumVelocity: .02,
                autoHeight: !1,
                setWrapperSize: !1,
                virtualTranslate: !1,
                effect: "slide",
                coverflow: {
                    rotate: 50,
                    stretch: 0,
                    depth: 100,
                    modifier: 1,
                    slideShadows: !0
                },
                flip: {
                    slideShadows: !0,
                    limitRotation: !0
                },
                cube: {
                    slideShadows: !0,
                    shadow: !0,
                    shadowOffset: 20,
                    shadowScale: .94
                },
                fade: {
                    crossFade: !1
                },
                parallax: !1,
                scrollbar: null,
                scrollbarHide: !0,
                scrollbarDraggable: !1,
                scrollbarSnapOnRelease: !1,
                keyboardControl: !1,
                mousewheelControl: !1,
                mousewheelReleaseOnEdges: !1,
                mousewheelInvert: !1,
                mousewheelForceToAxis: !1,
                mousewheelSensitivity: 1,
                hashnav: !1,
                breakpoints: void 0,
                spaceBetween: 0,
                slidesPerView: 1,
                slidesPerColumn: 1,
                slidesPerColumnFill: "column",
                slidesPerGroup: 1,
                centeredSlides: !1,
                slidesOffsetBefore: 0,
                slidesOffsetAfter: 0,
                roundLengths: !1,
                touchRatio: 1,
                touchAngle: 45,
                simulateTouch: !0,
                shortSwipes: !0,
                longSwipes: !0,
                longSwipesRatio: .5,
                longSwipesMs: 300,
                followFinger: !0,
                onlyExternal: !1,
                threshold: 0,
                touchMoveStopPropagation: !0,
                uniqueNavElements: !0,
                pagination: null,
                paginationElement: "span",
                paginationClickable: !1,
                paginationHide: !1,
                paginationBulletRender: null,
                paginationProgressRender: null,
                paginationFractionRender: null,
                paginationCustomRender: null,
                paginationType: "bullets",
                resistance: !0,
                resistanceRatio: .85,
                nextButton: null,
                prevButton: null,
                watchSlidesProgress: !1,
                watchSlidesVisibility: !1,
                grabCursor: !1,
                preventClicks: !0,
                preventClicksPropagation: !0,
                slideToClickedSlide: !1,
                lazyLoading: !1,
                lazyLoadingInPrevNext: !1,
                lazyLoadingInPrevNextAmount: 1,
                lazyLoadingOnTransitionStart: !1,
                preloadImages: !0,
                updateOnImagesReady: !0,
                loop: !1,
                loopAdditionalSlides: 0,
                loopedSlides: null,
                control: void 0,
                controlInverse: !1,
                controlBy: "slide",
                allowSwipeToPrev: !0,
                allowSwipeToNext: !0,
                swipeHandler: null,
                noSwiping: !0,
                noSwipingClass: "swiper-no-swiping",
                slideClass: "swiper-slide",
                slideActiveClass: "swiper-slide-active",
                slideVisibleClass: "swiper-slide-visible",
                slideDuplicateClass: "swiper-slide-duplicate",
                slideNextClass: "swiper-slide-next",
                slidePrevClass: "swiper-slide-prev",
                wrapperClass: "swiper-wrapper",
                bulletClass: "swiper-pagination-bullet",
                bulletActiveClass: "swiper-pagination-bullet-active",
                buttonDisabledClass: "swiper-button-disabled",
                paginationCurrentClass: "swiper-pagination-current",
                paginationTotalClass: "swiper-pagination-total",
                paginationHiddenClass: "swiper-pagination-hidden",
                paginationProgressbarClass: "swiper-pagination-progressbar",
                observer: !1,
                observeParents: !1,
                a11y: !1,
                prevSlideMessage: "Previous slide",
                nextSlideMessage: "Next slide",
                firstSlideMessage: "This is the first slide",
                lastSlideMessage: "This is the last slide",
                paginationBulletMessage: "Go to slide {{index}}",
                runCallbacksOnInit: !0
            },
            h = i && i.virtualTranslate;
        i = i || {};
        var f = {};
        for (var g in i)
            if ("object" != typeof i[g] || null === i[g] || (i[g].nodeType || i[g] === window || i[g] === document || "undefined" != typeof r && i[g] instanceof r || "undefined" != typeof jQuery && i[g] instanceof jQuery)) f[g] = i[g];
            else {
                f[g] = {};
                for (var v in i[g]) f[g][v] = i[g][v]
            }
        for (var w in m)
            if ("undefined" == typeof i[w]) i[w] = m[w];
            else if ("object" == typeof i[w])
            for (var y in m[w]) "undefined" == typeof i[w][y] && (i[w][y] = m[w][y]);
        var b = this;
        if (b.params = i, b.originalParams = f, b.classNames = [], "undefined" != typeof a && "undefined" != typeof r && (a = r), ("undefined" != typeof a || (a = "undefined" == typeof r ? window.Dom7 || window.Zepto || window.jQuery : r)) && (b.$ = a, b.currentBreakpoint = void 0, b.getActiveBreakpoint = function () {
            if (!b.params.breakpoints) return !1;
            var e, a = !1,
                t = [];
            for (e in b.params.breakpoints) b.params.breakpoints.hasOwnProperty(e) && t.push(e);
            t.sort(function (e, a) {
                return parseInt(e, 10) > parseInt(a, 10)
            });
            for (var r = 0; r < t.length; r++) e = t[r], e >= window.innerWidth && !a && (a = e);
            return a || "max"
        }, b.setBreakpoint = function () {
            var e = b.getActiveBreakpoint();
            if (e && b.currentBreakpoint !== e) {
                var a = e in b.params.breakpoints ? b.params.breakpoints[e] : b.originalParams,
                    t = b.params.loop && a.slidesPerView !== b.params.slidesPerView;
                for (var r in a) b.params[r] = a[r];
                b.currentBreakpoint = e, t && b.destroyLoop && b.reLoop(!0)
            }
        }, b.params.breakpoints && b.setBreakpoint(), b.container = a(e), 0 !== b.container.length)) {
            if (b.container.length > 1) {
                var x = [];
                return b.container.each(function () {
                    x.push(new t(this, i))
                }), x
            }
            b.container[0].swiper = b, b.container.data("swiper", b), b.classNames.push("swiper-container-" + b.params.direction), b.params.freeMode && b.classNames.push("swiper-container-free-mode"), b.support.flexbox || (b.classNames.push("swiper-container-no-flexbox"), b.params.slidesPerColumn = 1), b.params.autoHeight && b.classNames.push("swiper-container-autoheight"), (b.params.parallax || b.params.watchSlidesVisibility) && (b.params.watchSlidesProgress = !0), ["cube", "coverflow", "flip"].indexOf(b.params.effect) >= 0 && (b.support.transforms3d ? (b.params.watchSlidesProgress = !0, b.classNames.push("swiper-container-3d")) : b.params.effect = "slide"), "slide" !== b.params.effect && b.classNames.push("swiper-container-" + b.params.effect), "cube" === b.params.effect && (b.params.resistanceRatio = 0, b.params.slidesPerView = 1, b.params.slidesPerColumn = 1, b.params.slidesPerGroup = 1, b.params.centeredSlides = !1, b.params.spaceBetween = 0, b.params.virtualTranslate = !0, b.params.setWrapperSize = !1), "fade" !== b.params.effect && "flip" !== b.params.effect || (b.params.slidesPerView = 1, b.params.slidesPerColumn = 1, b.params.slidesPerGroup = 1, b.params.watchSlidesProgress = !0, b.params.spaceBetween = 0, b.params.setWrapperSize = !1, "undefined" == typeof h && (b.params.virtualTranslate = !0)), b.params.grabCursor && b.support.touch && (b.params.grabCursor = !1), b.wrapper = b.container.children("." + b.params.wrapperClass), b.params.pagination && (b.paginationContainer = a(b.params.pagination), b.params.uniqueNavElements && "string" == typeof b.params.pagination && b.paginationContainer.length > 1 && 1 === b.container.find(b.params.pagination).length && (b.paginationContainer = b.container.find(b.params.pagination)), "bullets" === b.params.paginationType && b.params.paginationClickable ? b.paginationContainer.addClass("swiper-pagination-clickable") : b.params.paginationClickable = !1, b.paginationContainer.addClass("swiper-pagination-" + b.params.paginationType)), (b.params.nextButton || b.params.prevButton) && (b.params.nextButton && (b.nextButton = a(b.params.nextButton), b.params.uniqueNavElements && "string" == typeof b.params.nextButton && b.nextButton.length > 1 && 1 === b.container.find(b.params.nextButton).length && (b.nextButton = b.container.find(b.params.nextButton))), b.params.prevButton && (b.prevButton = a(b.params.prevButton), b.params.uniqueNavElements && "string" == typeof b.params.prevButton && b.prevButton.length > 1 && 1 === b.container.find(b.params.prevButton).length && (b.prevButton = b.container.find(b.params.prevButton)))), b.isHorizontal = function () {
                return "horizontal" === b.params.direction
            }, b.rtl = b.isHorizontal() && ("rtl" === b.container[0].dir.toLowerCase() || "rtl" === b.container.css("direction")), b.rtl && b.classNames.push("swiper-container-rtl"), b.rtl && (b.wrongRTL = "-webkit-box" === b.wrapper.css("display")), b.params.slidesPerColumn > 1 && b.classNames.push("swiper-container-multirow"), b.device.android && b.classNames.push("swiper-container-android"), b.container.addClass(b.classNames.join(" ")), b.translate = 0, b.progress = 0, b.velocity = 0, b.lockSwipeToNext = function () {
                b.params.allowSwipeToNext = !1
            }, b.lockSwipeToPrev = function () {
                b.params.allowSwipeToPrev = !1
            }, b.lockSwipes = function () {
                b.params.allowSwipeToNext = b.params.allowSwipeToPrev = !1
            }, b.unlockSwipeToNext = function () {
                b.params.allowSwipeToNext = !0
            }, b.unlockSwipeToPrev = function () {
                b.params.allowSwipeToPrev = !0
            }, b.unlockSwipes = function () {
                b.params.allowSwipeToNext = b.params.allowSwipeToPrev = !0
            }, b.params.grabCursor && (b.container[0].style.cursor = "move", b.container[0].style.cursor = "-webkit-grab", b.container[0].style.cursor = "-moz-grab", b.container[0].style.cursor = "grab"), b.imagesToLoad = [], b.imagesLoaded = 0, b.loadImage = function (e, a, t, r, i) {
                function s() {
                    i && i()
                }
                var n;
                e.complete && r ? s() : a ? (n = new window.Image, n.onload = s, n.onerror = s, t && (n.srcset = t), a && (n.src = a)) : s()
            }, b.preloadImages = function () {
                function e() {
                    "undefined" != typeof b && null !== b && (void 0 !== b.imagesLoaded && b.imagesLoaded++, b.imagesLoaded === b.imagesToLoad.length && (b.params.updateOnImagesReady && b.update(), b.emit("onImagesReady", b)))
                }
                b.imagesToLoad = b.container.find("img");
                for (var a = 0; a < b.imagesToLoad.length; a++) b.loadImage(b.imagesToLoad[a], b.imagesToLoad[a].currentSrc || b.imagesToLoad[a].getAttribute("src"), b.imagesToLoad[a].srcset || b.imagesToLoad[a].getAttribute("srcset"), !0, e)
            }, b.autoplayTimeoutId = void 0, b.autoplaying = !1, b.autoplayPaused = !1, b.startAutoplay = function () {
                return "undefined" != typeof b.autoplayTimeoutId ? !1 : b.params.autoplay ? b.autoplaying ? !1 : (b.autoplaying = !0, b.emit("onAutoplayStart", b), void n()) : !1
            }, b.stopAutoplay = function (e) {
                b.autoplayTimeoutId && (b.autoplayTimeoutId && clearTimeout(b.autoplayTimeoutId), b.autoplaying = !1, b.autoplayTimeoutId = void 0, b.emit("onAutoplayStop", b))
            }, b.pauseAutoplay = function (e) {
                b.autoplayPaused || (b.autoplayTimeoutId && clearTimeout(b.autoplayTimeoutId), b.autoplayPaused = !0, 0 === e ? (b.autoplayPaused = !1, n()) : b.wrapper.transitionEnd(function () {
                    b && (b.autoplayPaused = !1, b.autoplaying ? n() : b.stopAutoplay())
                }))
            }, b.minTranslate = function () {
                return -b.snapGrid[0]
            }, b.maxTranslate = function () {
                return -b.snapGrid[b.snapGrid.length - 1]
            }, b.updateAutoHeight = function () {
                var e = b.slides.eq(b.activeIndex)[0];
                if ("undefined" != typeof e) {
                    var a = e.offsetHeight;
                    a && b.wrapper.css("height", a + "px")
                }
            }, b.updateContainerSize = function () {
                var e, a;
                e = "undefined" != typeof b.params.width ? b.params.width : b.container[0].clientWidth, a = "undefined" != typeof b.params.height ? b.params.height : b.container[0].clientHeight, 0 === e && b.isHorizontal() || 0 === a && !b.isHorizontal() || (e = e - parseInt(b.container.css("padding-left"), 10) - parseInt(b.container.css("padding-right"), 10), a = a - parseInt(b.container.css("padding-top"), 10) - parseInt(b.container.css("padding-bottom"), 10), b.width = e, b.height = a, b.size = b.isHorizontal() ? b.width : b.height)
            }, b.updateSlidesSize = function () {
                b.slides = b.wrapper.children("." + b.params.slideClass), b.snapGrid = [], b.slidesGrid = [], b.slidesSizesGrid = [];
                var e, a = b.params.spaceBetween,
                    t = -b.params.slidesOffsetBefore,
                    r = 0,
                    i = 0;
                if ("undefined" != typeof b.size) {
                    "string" == typeof a && a.indexOf("%") >= 0 && (a = parseFloat(a.replace("%", "")) / 100 * b.size), b.virtualSize = -a, b.rtl ? b.slides.css({
                        marginLeft: "",
                        marginTop: ""
                    }) : b.slides.css({
                        marginRight: "",
                        marginBottom: ""
                    });
                    var n;
                    b.params.slidesPerColumn > 1 && (n = Math.floor(b.slides.length / b.params.slidesPerColumn) === b.slides.length / b.params.slidesPerColumn ? b.slides.length : Math.ceil(b.slides.length / b.params.slidesPerColumn) * b.params.slidesPerColumn, "auto" !== b.params.slidesPerView && "row" === b.params.slidesPerColumnFill && (n = Math.max(n, b.params.slidesPerView * b.params.slidesPerColumn)));
                    var o, l = b.params.slidesPerColumn,
                        p = n / l,
                        d = p - (b.params.slidesPerColumn * p - b.slides.length);
                    for (e = 0; e < b.slides.length; e++) {
                        o = 0;
                        var c = b.slides.eq(e);
                        if (b.params.slidesPerColumn > 1) {
                            var u, m, h;
                            "column" === b.params.slidesPerColumnFill ? (m = Math.floor(e / l), h = e - m * l, (m > d || m === d && h === l - 1) && ++h >= l && (h = 0, m++), u = m + h * n / l, c.css({
                                "-webkit-box-ordinal-group": u,
                                "-moz-box-ordinal-group": u,
                                "-ms-flex-order": u,
                                "-webkit-order": u,
                                order: u
                            })) : (h = Math.floor(e / p), m = e - h * p), c.css({
                                "margin-top": 0 !== h && b.params.spaceBetween && b.params.spaceBetween + "px"
                            }).attr("data-swiper-column", m).attr("data-swiper-row", h)
                        }
                        "none" !== c.css("display") && ("auto" === b.params.slidesPerView ? (o = b.isHorizontal() ? c.outerWidth(!0) : c.outerHeight(!0), b.params.roundLengths && (o = s(o))) : (o = (b.size - (b.params.slidesPerView - 1) * a) / b.params.slidesPerView, b.params.roundLengths && (o = s(o)), b.isHorizontal() ? b.slides[e].style.width = o + "px" : b.slides[e].style.height = o + "px"), b.slides[e].swiperSlideSize = o, b.slidesSizesGrid.push(o), b.params.centeredSlides ? (t = t + o / 2 + r / 2 + a, 0 === e && (t = t - b.size / 2 - a), Math.abs(t) < .001 && (t = 0), i % b.params.slidesPerGroup === 0 && b.snapGrid.push(t), b.slidesGrid.push(t)) : (i % b.params.slidesPerGroup === 0 && b.snapGrid.push(t), b.slidesGrid.push(t), t = t + o + a), b.virtualSize += o + a, r = o, i++)
                    }
                    b.virtualSize = Math.max(b.virtualSize, b.size) + b.params.slidesOffsetAfter;
                    var f;
                    if (b.rtl && b.wrongRTL && ("slide" === b.params.effect || "coverflow" === b.params.effect) && b.wrapper.css({
                        width: b.virtualSize + b.params.spaceBetween + "px"
                    }), b.support.flexbox && !b.params.setWrapperSize || (b.isHorizontal() ? b.wrapper.css({
                        width: b.virtualSize + b.params.spaceBetween + "px"
                    }) : b.wrapper.css({
                        height: b.virtualSize + b.params.spaceBetween + "px"
                    })), b.params.slidesPerColumn > 1 && (b.virtualSize = (o + b.params.spaceBetween) * n, b.virtualSize = Math.ceil(b.virtualSize / b.params.slidesPerColumn) - b.params.spaceBetween, b.wrapper.css({
                        width: b.virtualSize + b.params.spaceBetween + "px"
                    }), b.params.centeredSlides)) {
                        for (f = [], e = 0; e < b.snapGrid.length; e++) b.snapGrid[e] < b.virtualSize + b.snapGrid[0] && f.push(b.snapGrid[e]);
                        b.snapGrid = f
                    }
                    if (!b.params.centeredSlides) {
                        for (f = [], e = 0; e < b.snapGrid.length; e++) b.snapGrid[e] <= b.virtualSize - b.size && f.push(b.snapGrid[e]);
                        b.snapGrid = f, Math.floor(b.virtualSize - b.size) - Math.floor(b.snapGrid[b.snapGrid.length - 1]) > 1 && b.snapGrid.push(b.virtualSize - b.size)
                    }
                    0 === b.snapGrid.length && (b.snapGrid = [0]), 0 !== b.params.spaceBetween && (b.isHorizontal() ? b.rtl ? b.slides.css({
                        marginLeft: a + "px"
                    }) : b.slides.css({
                        marginRight: a + "px"
                    }) : b.slides.css({
                        marginBottom: a + "px"
                    })), b.params.watchSlidesProgress && b.updateSlidesOffset()
                }
            }, b.updateSlidesOffset = function () {
                for (var e = 0; e < b.slides.length; e++) b.slides[e].swiperSlideOffset = b.isHorizontal() ? b.slides[e].offsetLeft : b.slides[e].offsetTop
            }, b.updateSlidesProgress = function (e) {
                if ("undefined" == typeof e && (e = b.translate || 0), 0 !== b.slides.length) {
                    "undefined" == typeof b.slides[0].swiperSlideOffset && b.updateSlidesOffset();
                    var a = -e;
                    b.rtl && (a = e), b.slides.removeClass(b.params.slideVisibleClass);
                    for (var t = 0; t < b.slides.length; t++) {
                        var r = b.slides[t],
                            i = (a - r.swiperSlideOffset) / (r.swiperSlideSize + b.params.spaceBetween);
                        if (b.params.watchSlidesVisibility) {
                            var s = -(a - r.swiperSlideOffset),
                                n = s + b.slidesSizesGrid[t],
                                o = s >= 0 && s < b.size || n > 0 && n <= b.size || 0 >= s && n >= b.size;
                            o && b.slides.eq(t).addClass(b.params.slideVisibleClass)
                        }
                        r.progress = b.rtl ? -i : i
                    }
                }
            }, b.updateProgress = function (e) {
                "undefined" == typeof e && (e = b.translate || 0);
                var a = b.maxTranslate() - b.minTranslate(),
                    t = b.isBeginning,
                    r = b.isEnd;
                0 === a ? (b.progress = 0, b.isBeginning = b.isEnd = !0) : (b.progress = (e - b.minTranslate()) / a, b.isBeginning = b.progress <= 0, b.isEnd = b.progress >= 1), b.isBeginning && !t && b.emit("onReachBeginning", b), b.isEnd && !r && b.emit("onReachEnd", b), b.params.watchSlidesProgress && b.updateSlidesProgress(e), b.emit("onProgress", b, b.progress)
            }, b.updateActiveIndex = function () {
                var e, a, t, r = b.rtl ? b.translate : -b.translate;
                for (a = 0; a < b.slidesGrid.length; a++) "undefined" != typeof b.slidesGrid[a + 1] ? r >= b.slidesGrid[a] && r < b.slidesGrid[a + 1] - (b.slidesGrid[a + 1] - b.slidesGrid[a]) / 2 ? e = a : r >= b.slidesGrid[a] && r < b.slidesGrid[a + 1] && (e = a + 1) : r >= b.slidesGrid[a] && (e = a);
                (0 > e || "undefined" == typeof e) && (e = 0), t = Math.floor(e / b.params.slidesPerGroup), t >= b.snapGrid.length && (t = b.snapGrid.length - 1), e !== b.activeIndex && (b.snapIndex = t, b.previousIndex = b.activeIndex, b.activeIndex = e, b.updateClasses())
            }, b.updateClasses = function () {
                b.slides.removeClass(b.params.slideActiveClass + " " + b.params.slideNextClass + " " + b.params.slidePrevClass);
                var e = b.slides.eq(b.activeIndex);
                e.addClass(b.params.slideActiveClass);
                var t = e.next("." + b.params.slideClass).addClass(b.params.slideNextClass);
                b.params.loop && 0 === t.length && b.slides.eq(0).addClass(b.params.slideNextClass);
                var r = e.prev("." + b.params.slideClass).addClass(b.params.slidePrevClass);
                if (b.params.loop && 0 === r.length && b.slides.eq(-1).addClass(b.params.slidePrevClass), b.paginationContainer && b.paginationContainer.length > 0) {
                    var i, s = b.params.loop ? Math.ceil((b.slides.length - 2 * b.loopedSlides) / b.params.slidesPerGroup) : b.snapGrid.length;
                    if (b.params.loop ? (i = Math.ceil((b.activeIndex - b.loopedSlides) / b.params.slidesPerGroup), i > b.slides.length - 1 - 2 * b.loopedSlides && (i -= b.slides.length - 2 * b.loopedSlides), i > s - 1 && (i -= s), 0 > i && "bullets" !== b.params.paginationType && (i = s + i)) : i = "undefined" != typeof b.snapIndex ? b.snapIndex : b.activeIndex || 0, "bullets" === b.params.paginationType && b.bullets && b.bullets.length > 0 && (b.bullets.removeClass(b.params.bulletActiveClass), b.paginationContainer.length > 1 ? b.bullets.each(function () {
                        a(this).index() === i && a(this).addClass(b.params.bulletActiveClass)
                    }) : b.bullets.eq(i).addClass(b.params.bulletActiveClass)), "fraction" === b.params.paginationType && (b.paginationContainer.find("." + b.params.paginationCurrentClass).text(i + 1), b.paginationContainer.find("." + b.params.paginationTotalClass).text(s)), "progress" === b.params.paginationType) {
                        var n = (i + 1) / s,
                            o = n,
                            l = 1;
                        b.isHorizontal() || (l = n, o = 1), b.paginationContainer.find("." + b.params.paginationProgressbarClass).transform("translate3d(0,0,0) scaleX(" + o + ") scaleY(" + l + ")").transition(b.params.speed)
                    }
                    "custom" === b.params.paginationType && b.params.paginationCustomRender && (b.paginationContainer.html(b.params.paginationCustomRender(b, i + 1, s)), b.emit("onPaginationRendered", b, b.paginationContainer[0]))
                }
                b.params.loop || (b.params.prevButton && b.prevButton && b.prevButton.length > 0 && (b.isBeginning ? (b.prevButton.addClass(b.params.buttonDisabledClass), b.params.a11y && b.a11y && b.a11y.disable(b.prevButton)) : (b.prevButton.removeClass(b.params.buttonDisabledClass), b.params.a11y && b.a11y && b.a11y.enable(b.prevButton))), b.params.nextButton && b.nextButton && b.nextButton.length > 0 && (b.isEnd ? (b.nextButton.addClass(b.params.buttonDisabledClass), b.params.a11y && b.a11y && b.a11y.disable(b.nextButton)) : (b.nextButton.removeClass(b.params.buttonDisabledClass), b.params.a11y && b.a11y && b.a11y.enable(b.nextButton))))
            }, b.updatePagination = function () {
                if (b.params.pagination && b.paginationContainer && b.paginationContainer.length > 0) {
                    var e = "";
                    if ("bullets" === b.params.paginationType) {
                        for (var a = b.params.loop ? Math.ceil((b.slides.length - 2 * b.loopedSlides) / b.params.slidesPerGroup) : b.snapGrid.length, t = 0; a > t; t++) e += b.params.paginationBulletRender ? b.params.paginationBulletRender(t, b.params.bulletClass) : "<" + b.params.paginationElement + ' class="' + b.params.bulletClass + '"></' + b.params.paginationElement + ">";
                        b.paginationContainer.html(e), b.bullets = b.paginationContainer.find("." + b.params.bulletClass), b.params.paginationClickable && b.params.a11y && b.a11y && b.a11y.initPagination()
                    }
                    "fraction" === b.params.paginationType && (e = b.params.paginationFractionRender ? b.params.paginationFractionRender(b, b.params.paginationCurrentClass, b.params.paginationTotalClass) : '<span class="' + b.params.paginationCurrentClass + '"></span> / <span class="' + b.params.paginationTotalClass + '"></span>', b.paginationContainer.html(e)), "progress" === b.params.paginationType && (e = b.params.paginationProgressRender ? b.params.paginationProgressRender(b, b.params.paginationProgressbarClass) : '<span class="' + b.params.paginationProgressbarClass + '"></span>', b.paginationContainer.html(e)), "custom" !== b.params.paginationType && b.emit("onPaginationRendered", b, b.paginationContainer[0])
                }
            }, b.update = function (e) {
                function a() {
                    r = Math.min(Math.max(b.translate, b.maxTranslate()), b.minTranslate()), b.setWrapperTranslate(r), b.updateActiveIndex(), b.updateClasses()
                }
                if (b.updateContainerSize(), b.updateSlidesSize(), b.updateProgress(), b.updatePagination(), b.updateClasses(), b.params.scrollbar && b.scrollbar && b.scrollbar.set(), e) {
                    var t, r;
                    b.controller && b.controller.spline && (b.controller.spline = void 0), b.params.freeMode ? (a(), b.params.autoHeight && b.updateAutoHeight()) : (t = ("auto" === b.params.slidesPerView || b.params.slidesPerView > 1) && b.isEnd && !b.params.centeredSlides ? b.slideTo(b.slides.length - 1, 0, !1, !0) : b.slideTo(b.activeIndex, 0, !1, !0), t || a())
                } else b.params.autoHeight && b.updateAutoHeight()
            }, b.onResize = function (e) {
                b.params.breakpoints && b.setBreakpoint();
                var a = b.params.allowSwipeToPrev,
                    t = b.params.allowSwipeToNext;
                b.params.allowSwipeToPrev = b.params.allowSwipeToNext = !0, b.updateContainerSize(), b.updateSlidesSize(), ("auto" === b.params.slidesPerView || b.params.freeMode || e) && b.updatePagination(), b.params.scrollbar && b.scrollbar && b.scrollbar.set(), b.controller && b.controller.spline && (b.controller.spline = void 0);
                var r = !1;
                if (b.params.freeMode) {
                    var i = Math.min(Math.max(b.translate, b.maxTranslate()), b.minTranslate());
                    b.setWrapperTranslate(i), b.updateActiveIndex(), b.updateClasses(), b.params.autoHeight && b.updateAutoHeight()
                } else b.updateClasses(), r = ("auto" === b.params.slidesPerView || b.params.slidesPerView > 1) && b.isEnd && !b.params.centeredSlides ? b.slideTo(b.slides.length - 1, 0, !1, !0) : b.slideTo(b.activeIndex, 0, !1, !0);
                b.params.lazyLoading && !r && b.lazy && b.lazy.load(), b.params.allowSwipeToPrev = a, b.params.allowSwipeToNext = t
            };
            var T = ["mousedown", "mousemove", "mouseup"];
            window.navigator.pointerEnabled ? T = ["pointerdown", "pointermove", "pointerup"] : window.navigator.msPointerEnabled && (T = ["MSPointerDown", "MSPointerMove", "MSPointerUp"]), b.touchEvents = {
                start: b.support.touch || !b.params.simulateTouch ? "touchstart" : T[0],
                move: b.support.touch || !b.params.simulateTouch ? "touchmove" : T[1],
                end: b.support.touch || !b.params.simulateTouch ? "touchend" : T[2]
            }, (window.navigator.pointerEnabled || window.navigator.msPointerEnabled) && ("container" === b.params.touchEventsTarget ? b.container : b.wrapper).addClass("swiper-wp8-" + b.params.direction), b.initEvents = function (e) {
                var a = e ? "off" : "on",
                    t = e ? "removeEventListener" : "addEventListener",
                    r = "container" === b.params.touchEventsTarget ? b.container[0] : b.wrapper[0],
                    s = b.support.touch ? r : document,
                    n = !!b.params.nested;
                b.browser.ie ? (r[t](b.touchEvents.start, b.onTouchStart, !1), s[t](b.touchEvents.move, b.onTouchMove, n), s[t](b.touchEvents.end, b.onTouchEnd, !1)) : (b.support.touch && (r[t](b.touchEvents.start, b.onTouchStart, !1), r[t](b.touchEvents.move, b.onTouchMove, n), r[t](b.touchEvents.end, b.onTouchEnd, !1)), !i.simulateTouch || b.device.ios || b.device.android || (r[t]("mousedown", b.onTouchStart, !1), document[t]("mousemove", b.onTouchMove, n), document[t]("mouseup", b.onTouchEnd, !1))), window[t]("resize", b.onResize), b.params.nextButton && b.nextButton && b.nextButton.length > 0 && (b.nextButton[a]("click", b.onClickNext), b.params.a11y && b.a11y && b.nextButton[a]("keydown", b.a11y.onEnterKey)), b.params.prevButton && b.prevButton && b.prevButton.length > 0 && (b.prevButton[a]("click", b.onClickPrev), b.params.a11y && b.a11y && b.prevButton[a]("keydown", b.a11y.onEnterKey)), b.params.pagination && b.params.paginationClickable && (b.paginationContainer[a]("click", "." + b.params.bulletClass, b.onClickIndex), b.params.a11y && b.a11y && b.paginationContainer[a]("keydown", "." + b.params.bulletClass, b.a11y.onEnterKey)), (b.params.preventClicks || b.params.preventClicksPropagation) && r[t]("click", b.preventClicks, !0)
            }, b.attachEvents = function () {
                b.initEvents()
            }, b.detachEvents = function () {
                b.initEvents(!0)
            }, b.allowClick = !0, b.preventClicks = function (e) {
                b.allowClick || (b.params.preventClicks && e.preventDefault(), b.params.preventClicksPropagation && b.animating && (e.stopPropagation(), e.stopImmediatePropagation()))
            }, b.onClickNext = function (e) {
                e.preventDefault(), b.isEnd && !b.params.loop || b.slideNext()
            }, b.onClickPrev = function (e) {
                e.preventDefault(), b.isBeginning && !b.params.loop || b.slidePrev()
            }, b.onClickIndex = function (e) {
                e.preventDefault();
                var t = a(this).index() * b.params.slidesPerGroup;
                b.params.loop && (t += b.loopedSlides), b.slideTo(t)
            }, b.updateClickedSlide = function (e) {
                var t = o(e, "." + b.params.slideClass),
                    r = !1;
                if (t)
                    for (var i = 0; i < b.slides.length; i++) b.slides[i] === t && (r = !0);
                if (!t || !r) return b.clickedSlide = void 0, void(b.clickedIndex = void 0);
                if (b.clickedSlide = t, b.clickedIndex = a(t).index(), b.params.slideToClickedSlide && void 0 !== b.clickedIndex && b.clickedIndex !== b.activeIndex) {
                    var s, n = b.clickedIndex;
                    if (b.params.loop) {
                        if (b.animating) return;
                        s = a(b.clickedSlide).attr("data-swiper-slide-index"), b.params.centeredSlides ? n < b.loopedSlides - b.params.slidesPerView / 2 || n > b.slides.length - b.loopedSlides + b.params.slidesPerView / 2 ? (b.fixLoop(), n = b.wrapper.children("." + b.params.slideClass + '[data-swiper-slide-index="' + s + '"]:not(.swiper-slide-duplicate)').eq(0).index(), setTimeout(function () {
                            b.slideTo(n)
                        }, 0)) : b.slideTo(n) : n > b.slides.length - b.params.slidesPerView ? (b.fixLoop(), n = b.wrapper.children("." + b.params.slideClass + '[data-swiper-slide-index="' + s + '"]:not(.swiper-slide-duplicate)').eq(0).index(), setTimeout(function () {
                            b.slideTo(n)
                        }, 0)) : b.slideTo(n)
                    } else b.slideTo(n)
                }
            };
            var S, C, z, M, E, P, I, k, B, L, D = "input, select, textarea, button",
                H = Date.now(),
                O = [];
            b.animating = !1, b.touches = {
                startX: 0,
                startY: 0,
                currentX: 0,
                currentY: 0,
                diff: 0
            };
            var G, A;
            if (b.onTouchStart = function (e) {
                if (e.originalEvent && (e = e.originalEvent), G = "touchstart" === e.type, G || !("which" in e) || 3 !== e.which) {
                    if (b.params.noSwiping && o(e, "." + b.params.noSwipingClass)) return void(b.allowClick = !0);
                    if (!b.params.swipeHandler || o(e, b.params.swipeHandler)) {
                        var t = b.touches.currentX = "touchstart" === e.type ? e.targetTouches[0].pageX : e.pageX,
                            r = b.touches.currentY = "touchstart" === e.type ? e.targetTouches[0].pageY : e.pageY;
                        if (!(b.device.ios && b.params.iOSEdgeSwipeDetection && t <= b.params.iOSEdgeSwipeThreshold)) {
                            if (S = !0, C = !1, z = !0, E = void 0, A = void 0, b.touches.startX = t, b.touches.startY = r, M = Date.now(), b.allowClick = !0, b.updateContainerSize(), b.swipeDirection = void 0, b.params.threshold > 0 && (k = !1), "touchstart" !== e.type) {
                                var i = !0;
                                a(e.target).is(D) && (i = !1), document.activeElement && a(document.activeElement).is(D) && document.activeElement.blur(), i && e.preventDefault()
                            }
                            b.emit("onTouchStart", b, e)
                        }
                    }
                }
            }, b.onTouchMove = function (e) {
                if (e.originalEvent && (e = e.originalEvent), !G || "mousemove" !== e.type) {
                    if (e.preventedByNestedSwiper) return b.touches.startX = "touchmove" === e.type ? e.targetTouches[0].pageX : e.pageX, void(b.touches.startY = "touchmove" === e.type ? e.targetTouches[0].pageY : e.pageY);
                    if (b.params.onlyExternal) return b.allowClick = !1, void(S && (b.touches.startX = b.touches.currentX = "touchmove" === e.type ? e.targetTouches[0].pageX : e.pageX, b.touches.startY = b.touches.currentY = "touchmove" === e.type ? e.targetTouches[0].pageY : e.pageY, M = Date.now()));
                    if (G && document.activeElement && e.target === document.activeElement && a(e.target).is(D)) return C = !0, void(b.allowClick = !1);
                    if (z && b.emit("onTouchMove", b, e), !(e.targetTouches && e.targetTouches.length > 1)) {
                        if (b.touches.currentX = "touchmove" === e.type ? e.targetTouches[0].pageX : e.pageX, b.touches.currentY = "touchmove" === e.type ? e.targetTouches[0].pageY : e.pageY, "undefined" == typeof E) {
                            var t = 180 * Math.atan2(Math.abs(b.touches.currentY - b.touches.startY), Math.abs(b.touches.currentX - b.touches.startX)) / Math.PI;
                            E = b.isHorizontal() ? t > b.params.touchAngle : 90 - t > b.params.touchAngle
                        }
                        if (E && b.emit("onTouchMoveOpposite", b, e), "undefined" == typeof A && b.browser.ieTouch && (b.touches.currentX === b.touches.startX && b.touches.currentY === b.touches.startY || (A = !0)), S) {
                            if (E) return void(S = !1);
                            if (A || !b.browser.ieTouch) {
                                b.allowClick = !1, b.emit("onSliderMove", b, e), e.preventDefault(), b.params.touchMoveStopPropagation && !b.params.nested && e.stopPropagation(), C || (i.loop && b.fixLoop(), I = b.getWrapperTranslate(), b.setWrapperTransition(0), b.animating && b.wrapper.trigger("webkitTransitionEnd transitionend oTransitionEnd MSTransitionEnd msTransitionEnd"), b.params.autoplay && b.autoplaying && (b.params.autoplayDisableOnInteraction ? b.stopAutoplay() : b.pauseAutoplay()), L = !1, b.params.grabCursor && (b.container[0].style.cursor = "move", b.container[0].style.cursor = "-webkit-grabbing", b.container[0].style.cursor = "-moz-grabbin", b.container[0].style.cursor = "grabbing")), C = !0;
                                var r = b.touches.diff = b.isHorizontal() ? b.touches.currentX - b.touches.startX : b.touches.currentY - b.touches.startY;
                                r *= b.params.touchRatio, b.rtl && (r = -r), b.swipeDirection = r > 0 ? "prev" : "next", P = r + I;
                                var s = !0;
                                if (r > 0 && P > b.minTranslate() ? (s = !1, b.params.resistance && (P = b.minTranslate() - 1 + Math.pow(-b.minTranslate() + I + r, b.params.resistanceRatio))) : 0 > r && P < b.maxTranslate() && (s = !1, b.params.resistance && (P = b.maxTranslate() + 1 - Math.pow(b.maxTranslate() - I - r, b.params.resistanceRatio))),
                                    s && (e.preventedByNestedSwiper = !0), !b.params.allowSwipeToNext && "next" === b.swipeDirection && I > P && (P = I), !b.params.allowSwipeToPrev && "prev" === b.swipeDirection && P > I && (P = I), b.params.followFinger) {
                                    if (b.params.threshold > 0) {
                                        if (!(Math.abs(r) > b.params.threshold || k)) return void(P = I);
                                        if (!k) return k = !0, b.touches.startX = b.touches.currentX, b.touches.startY = b.touches.currentY, P = I, void(b.touches.diff = b.isHorizontal() ? b.touches.currentX - b.touches.startX : b.touches.currentY - b.touches.startY)
                                    }(b.params.freeMode || b.params.watchSlidesProgress) && b.updateActiveIndex(), b.params.freeMode && (0 === O.length && O.push({
                                        position: b.touches[b.isHorizontal() ? "startX" : "startY"],
                                        time: M
                                    }), O.push({
                                        position: b.touches[b.isHorizontal() ? "currentX" : "currentY"],
                                        time: (new window.Date).getTime()
                                    })), b.updateProgress(P), b.setWrapperTranslate(P)
                                }
                            }
                        }
                    }
                }
            }, b.onTouchEnd = function (e) {
                if (e.originalEvent && (e = e.originalEvent), z && b.emit("onTouchEnd", b, e), z = !1, S) {
                    b.params.grabCursor && C && S && (b.container[0].style.cursor = "move", b.container[0].style.cursor = "-webkit-grab", b.container[0].style.cursor = "-moz-grab", b.container[0].style.cursor = "grab");
                    var t = Date.now(),
                        r = t - M;
                    if (b.allowClick && (b.updateClickedSlide(e), b.emit("onTap", b, e), 300 > r && t - H > 300 && (B && clearTimeout(B), B = setTimeout(function () {
                        b && (b.params.paginationHide && b.paginationContainer.length > 0 && !a(e.target).hasClass(b.params.bulletClass) && b.paginationContainer.toggleClass(b.params.paginationHiddenClass), b.emit("onClick", b, e))
                    }, 300)), 300 > r && 300 > t - H && (B && clearTimeout(B), b.emit("onDoubleTap", b, e))), H = Date.now(), setTimeout(function () {
                        b && (b.allowClick = !0)
                    }, 0), !S || !C || !b.swipeDirection || 0 === b.touches.diff || P === I) return void(S = C = !1);
                    S = C = !1;
                    var i;
                    if (i = b.params.followFinger ? b.rtl ? b.translate : -b.translate : -P, b.params.freeMode) {
                        if (i < -b.minTranslate()) return void b.slideTo(b.activeIndex);
                        if (i > -b.maxTranslate()) return void(b.slides.length < b.snapGrid.length ? b.slideTo(b.snapGrid.length - 1) : b.slideTo(b.slides.length - 1));
                        if (b.params.freeModeMomentum) {
                            if (O.length > 1) {
                                var s = O.pop(),
                                    n = O.pop(),
                                    o = s.position - n.position,
                                    l = s.time - n.time;
                                b.velocity = o / l, b.velocity = b.velocity / 2, Math.abs(b.velocity) < b.params.freeModeMinimumVelocity && (b.velocity = 0), (l > 150 || (new window.Date).getTime() - s.time > 300) && (b.velocity = 0)
                            } else b.velocity = 0;
                            O.length = 0;
                            var p = 1e3 * b.params.freeModeMomentumRatio,
                                d = b.velocity * p,
                                c = b.translate + d;
                            b.rtl && (c = -c);
                            var u, m = !1,
                                h = 20 * Math.abs(b.velocity) * b.params.freeModeMomentumBounceRatio;
                            if (c < b.maxTranslate()) b.params.freeModeMomentumBounce ? (c + b.maxTranslate() < -h && (c = b.maxTranslate() - h), u = b.maxTranslate(), m = !0, L = !0) : c = b.maxTranslate();
                            else if (c > b.minTranslate()) b.params.freeModeMomentumBounce ? (c - b.minTranslate() > h && (c = b.minTranslate() + h), u = b.minTranslate(), m = !0, L = !0) : c = b.minTranslate();
                            else if (b.params.freeModeSticky) {
                                var f, g = 0;
                                for (g = 0; g < b.snapGrid.length; g += 1)
                                    if (b.snapGrid[g] > -c) {
                                        f = g;
                                        break
                                    }
                                c = Math.abs(b.snapGrid[f] - c) < Math.abs(b.snapGrid[f - 1] - c) || "next" === b.swipeDirection ? b.snapGrid[f] : b.snapGrid[f - 1], b.rtl || (c = -c)
                            }
                            if (0 !== b.velocity) p = b.rtl ? Math.abs((-c - b.translate) / b.velocity) : Math.abs((c - b.translate) / b.velocity);
                            else if (b.params.freeModeSticky) return void b.slideReset();
                            b.params.freeModeMomentumBounce && m ? (b.updateProgress(u), b.setWrapperTransition(p), b.setWrapperTranslate(c), b.onTransitionStart(), b.animating = !0, b.wrapper.transitionEnd(function () {
                                b && L && (b.emit("onMomentumBounce", b), b.setWrapperTransition(b.params.speed), b.setWrapperTranslate(u), b.wrapper.transitionEnd(function () {
                                    b && b.onTransitionEnd()
                                }))
                            })) : b.velocity ? (b.updateProgress(c), b.setWrapperTransition(p), b.setWrapperTranslate(c), b.onTransitionStart(), b.animating || (b.animating = !0, b.wrapper.transitionEnd(function () {
                                b && b.onTransitionEnd()
                            }))) : b.updateProgress(c), b.updateActiveIndex()
                        }
                        return void((!b.params.freeModeMomentum || r >= b.params.longSwipesMs) && (b.updateProgress(), b.updateActiveIndex()))
                    }
                    var v, w = 0,
                        y = b.slidesSizesGrid[0];
                    for (v = 0; v < b.slidesGrid.length; v += b.params.slidesPerGroup) "undefined" != typeof b.slidesGrid[v + b.params.slidesPerGroup] ? i >= b.slidesGrid[v] && i < b.slidesGrid[v + b.params.slidesPerGroup] && (w = v, y = b.slidesGrid[v + b.params.slidesPerGroup] - b.slidesGrid[v]) : i >= b.slidesGrid[v] && (w = v, y = b.slidesGrid[b.slidesGrid.length - 1] - b.slidesGrid[b.slidesGrid.length - 2]);
                    var x = (i - b.slidesGrid[w]) / y;
                    if (r > b.params.longSwipesMs) {
                        if (!b.params.longSwipes) return void b.slideTo(b.activeIndex);
                        "next" === b.swipeDirection && (x >= b.params.longSwipesRatio ? b.slideTo(w + b.params.slidesPerGroup) : b.slideTo(w)), "prev" === b.swipeDirection && (x > 1 - b.params.longSwipesRatio ? b.slideTo(w + b.params.slidesPerGroup) : b.slideTo(w))
                    } else {
                        if (!b.params.shortSwipes) return void b.slideTo(b.activeIndex);
                        "next" === b.swipeDirection && b.slideTo(w + b.params.slidesPerGroup), "prev" === b.swipeDirection && b.slideTo(w)
                    }
                }
            }, b._slideTo = function (e, a) {
                return b.slideTo(e, a, !0, !0)
            }, b.slideTo = function (e, a, t, r) {
                "undefined" == typeof t && (t = !0), "undefined" == typeof e && (e = 0), 0 > e && (e = 0), b.snapIndex = Math.floor(e / b.params.slidesPerGroup), b.snapIndex >= b.snapGrid.length && (b.snapIndex = b.snapGrid.length - 1);
                var i = -b.snapGrid[b.snapIndex];
                b.params.autoplay && b.autoplaying && (r || !b.params.autoplayDisableOnInteraction ? b.pauseAutoplay(a) : b.stopAutoplay()), b.updateProgress(i);
                for (var s = 0; s < b.slidesGrid.length; s++) - Math.floor(100 * i) >= Math.floor(100 * b.slidesGrid[s]) && (e = s);
                return !b.params.allowSwipeToNext && i < b.translate && i < b.minTranslate() ? !1 : !b.params.allowSwipeToPrev && i > b.translate && i > b.maxTranslate() && (b.activeIndex || 0) !== e ? !1 : ("undefined" == typeof a && (a = b.params.speed), b.previousIndex = b.activeIndex || 0, b.activeIndex = e, b.rtl && -i === b.translate || !b.rtl && i === b.translate ? (b.params.autoHeight && b.updateAutoHeight(), b.updateClasses(), "slide" !== b.params.effect && b.setWrapperTranslate(i), !1) : (b.updateClasses(), b.onTransitionStart(t), 0 === a ? (b.setWrapperTranslate(i), b.setWrapperTransition(0), b.onTransitionEnd(t)) : (b.setWrapperTranslate(i), b.setWrapperTransition(a), b.animating || (b.animating = !0, b.wrapper.transitionEnd(function () {
                    b && b.onTransitionEnd(t)
                }))), !0))
            }, b.onTransitionStart = function (e) {
                "undefined" == typeof e && (e = !0), b.params.autoHeight && b.updateAutoHeight(), b.lazy && b.lazy.onTransitionStart(), e && (b.emit("onTransitionStart", b), b.activeIndex !== b.previousIndex && (b.emit("onSlideChangeStart", b), b.activeIndex > b.previousIndex ? b.emit("onSlideNextStart", b) : b.emit("onSlidePrevStart", b)))
            }, b.onTransitionEnd = function (e) {
                b.animating = !1, b.setWrapperTransition(0), "undefined" == typeof e && (e = !0), b.lazy && b.lazy.onTransitionEnd(), e && (b.emit("onTransitionEnd", b), b.activeIndex !== b.previousIndex && (b.emit("onSlideChangeEnd", b), b.activeIndex > b.previousIndex ? b.emit("onSlideNextEnd", b) : b.emit("onSlidePrevEnd", b))), b.params.hashnav && b.hashnav && b.hashnav.setHash()
            }, b.slideNext = function (e, a, t) {
                if (b.params.loop) {
                    if (b.animating) return !1;
                    b.fixLoop();
                    b.container[0].clientLeft;
                    return b.slideTo(b.activeIndex + b.params.slidesPerGroup, a, e, t)
                }
                return b.slideTo(b.activeIndex + b.params.slidesPerGroup, a, e, t)
            }, b._slideNext = function (e) {
                return b.slideNext(!0, e, !0)
            }, b.slidePrev = function (e, a, t) {
                if (b.params.loop) {
                    if (b.animating) return !1;
                    b.fixLoop();
                    b.container[0].clientLeft;
                    return b.slideTo(b.activeIndex - 1, a, e, t)
                }
                return b.slideTo(b.activeIndex - 1, a, e, t)
            }, b._slidePrev = function (e) {
                return b.slidePrev(!0, e, !0)
            }, b.slideReset = function (e, a, t) {
                return b.slideTo(b.activeIndex, a, e)
            }, b.setWrapperTransition = function (e, a) {
                b.wrapper.transition(e), "slide" !== b.params.effect && b.effects[b.params.effect] && b.effects[b.params.effect].setTransition(e), b.params.parallax && b.parallax && b.parallax.setTransition(e), b.params.scrollbar && b.scrollbar && b.scrollbar.setTransition(e), b.params.control && b.controller && b.controller.setTransition(e, a), b.emit("onSetTransition", b, e)
            }, b.setWrapperTranslate = function (e, a, t) {
                var r = 0,
                    i = 0,
                    n = 0;
                b.isHorizontal() ? r = b.rtl ? -e : e : i = e, b.params.roundLengths && (r = s(r), i = s(i)), b.params.virtualTranslate || (b.support.transforms3d ? b.wrapper.transform("translate3d(" + r + "px, " + i + "px, " + n + "px)") : b.wrapper.transform("translate(" + r + "px, " + i + "px)")), b.translate = b.isHorizontal() ? r : i;
                var o, l = b.maxTranslate() - b.minTranslate();
                o = 0 === l ? 0 : (e - b.minTranslate()) / l, o !== b.progress && b.updateProgress(e), a && b.updateActiveIndex(), "slide" !== b.params.effect && b.effects[b.params.effect] && b.effects[b.params.effect].setTranslate(b.translate), b.params.parallax && b.parallax && b.parallax.setTranslate(b.translate), b.params.scrollbar && b.scrollbar && b.scrollbar.setTranslate(b.translate), b.params.control && b.controller && b.controller.setTranslate(b.translate, t), b.emit("onSetTranslate", b, b.translate)
            }, b.getTranslate = function (e, a) {
                var t, r, i, s;
                return "undefined" == typeof a && (a = "x"), b.params.virtualTranslate ? b.rtl ? -b.translate : b.translate : (i = window.getComputedStyle(e, null), window.WebKitCSSMatrix ? (r = i.transform || i.webkitTransform, r.split(",").length > 6 && (r = r.split(", ").map(function (e) {
                    return e.replace(",", ".")
                }).join(", ")), s = new window.WebKitCSSMatrix("none" === r ? "" : r)) : (s = i.MozTransform || i.OTransform || i.MsTransform || i.msTransform || i.transform || i.getPropertyValue("transform").replace("translate(", "matrix(1, 0, 0, 1,"), t = s.toString().split(",")), "x" === a && (r = window.WebKitCSSMatrix ? s.m41 : 16 === t.length ? parseFloat(t[12]) : parseFloat(t[4])), "y" === a && (r = window.WebKitCSSMatrix ? s.m42 : 16 === t.length ? parseFloat(t[13]) : parseFloat(t[5])), b.rtl && r && (r = -r), r || 0)
            }, b.getWrapperTranslate = function (e) {
                return "undefined" == typeof e && (e = b.isHorizontal() ? "x" : "y"), b.getTranslate(b.wrapper[0], e)
            }, b.observers = [], b.initObservers = function () {
                if (b.params.observeParents)
                    for (var e = b.container.parents(), a = 0; a < e.length; a++) l(e[a]);
                l(b.container[0], {
                    childList: !1
                }), l(b.wrapper[0], {
                    attributes: !1
                })
            }, b.disconnectObservers = function () {
                for (var e = 0; e < b.observers.length; e++) b.observers[e].disconnect();
                b.observers = []
            }, b.createLoop = function () {
                b.wrapper.children("." + b.params.slideClass + "." + b.params.slideDuplicateClass).remove();
                var e = b.wrapper.children("." + b.params.slideClass);
                "auto" !== b.params.slidesPerView || b.params.loopedSlides || (b.params.loopedSlides = e.length), b.loopedSlides = parseInt(b.params.loopedSlides || b.params.slidesPerView, 10), b.loopedSlides = b.loopedSlides + b.params.loopAdditionalSlides, b.loopedSlides > e.length && (b.loopedSlides = e.length);
                var t, r = [],
                    i = [];
                for (e.each(function (t, s) {
                    var n = a(this);
                    t < b.loopedSlides && i.push(s), t < e.length && t >= e.length - b.loopedSlides && r.push(s), n.attr("data-swiper-slide-index", t)
                }), t = 0; t < i.length; t++) b.wrapper.append(a(i[t].cloneNode(!0)).addClass(b.params.slideDuplicateClass));
                for (t = r.length - 1; t >= 0; t--) b.wrapper.prepend(a(r[t].cloneNode(!0)).addClass(b.params.slideDuplicateClass))
            }, b.destroyLoop = function () {
                b.wrapper.children("." + b.params.slideClass + "." + b.params.slideDuplicateClass).remove(), b.slides.removeAttr("data-swiper-slide-index")
            }, b.reLoop = function (e) {
                var a = b.activeIndex - b.loopedSlides;
                b.destroyLoop(), b.createLoop(), b.updateSlidesSize(), e && b.slideTo(a + b.loopedSlides, 0, !1)
            }, b.fixLoop = function () {
                var e;
                b.activeIndex < b.loopedSlides ? (e = b.slides.length - 3 * b.loopedSlides + b.activeIndex, e += b.loopedSlides, b.slideTo(e, 0, !1, !0)) : ("auto" === b.params.slidesPerView && b.activeIndex >= 2 * b.loopedSlides || b.activeIndex > b.slides.length - 2 * b.params.slidesPerView) && (e = -b.slides.length + b.activeIndex + b.loopedSlides, e += b.loopedSlides, b.slideTo(e, 0, !1, !0))
            }, b.appendSlide = function (e) {
                if (b.params.loop && b.destroyLoop(), "object" == typeof e && e.length)
                    for (var a = 0; a < e.length; a++) e[a] && b.wrapper.append(e[a]);
                else b.wrapper.append(e);
                b.params.loop && b.createLoop(), b.params.observer && b.support.observer || b.update(!0)
            }, b.prependSlide = function (e) {
                b.params.loop && b.destroyLoop();
                var a = b.activeIndex + 1;
                if ("object" == typeof e && e.length) {
                    for (var t = 0; t < e.length; t++) e[t] && b.wrapper.prepend(e[t]);
                    a = b.activeIndex + e.length
                } else b.wrapper.prepend(e);
                b.params.loop && b.createLoop(), b.params.observer && b.support.observer || b.update(!0), b.slideTo(a, 0, !1)
            }, b.removeSlide = function (e) {
                b.params.loop && (b.destroyLoop(), b.slides = b.wrapper.children("." + b.params.slideClass));
                var a, t = b.activeIndex;
                if ("object" == typeof e && e.length) {
                    for (var r = 0; r < e.length; r++) a = e[r], b.slides[a] && b.slides.eq(a).remove(), t > a && t--;
                    t = Math.max(t, 0)
                } else a = e, b.slides[a] && b.slides.eq(a).remove(), t > a && t--, t = Math.max(t, 0);
                b.params.loop && b.createLoop(), b.params.observer && b.support.observer || b.update(!0), b.params.loop ? b.slideTo(t + b.loopedSlides, 0, !1) : b.slideTo(t, 0, !1)
            }, b.removeAllSlides = function () {
                for (var e = [], a = 0; a < b.slides.length; a++) e.push(a);
                b.removeSlide(e)
            }, b.effects = {
                fade: {
                    setTranslate: function () {
                        for (var e = 0; e < b.slides.length; e++) {
                            var a = b.slides.eq(e),
                                t = a[0].swiperSlideOffset,
                                r = -t;
                            b.params.virtualTranslate || (r -= b.translate);
                            var i = 0;
                            b.isHorizontal() || (i = r, r = 0);
                            var s = b.params.fade.crossFade ? Math.max(1 - Math.abs(a[0].progress), 0) : 1 + Math.min(Math.max(a[0].progress, -1), 0);
                            a.css({
                                opacity: s
                            }).transform("translate3d(" + r + "px, " + i + "px, 0px)")
                        }
                    }, setTransition: function (e) {
                        if (b.slides.transition(e), b.params.virtualTranslate && 0 !== e) {
                            var a = !1;
                            b.slides.transitionEnd(function () {
                                if (!a && b) {
                                    a = !0, b.animating = !1;
                                    for (var e = ["webkitTransitionEnd", "transitionend", "oTransitionEnd", "MSTransitionEnd", "msTransitionEnd"], t = 0; t < e.length; t++) b.wrapper.trigger(e[t])
                                }
                            })
                        }
                    }
                },
                flip: {
                    setTranslate: function () {
                        for (var e = 0; e < b.slides.length; e++) {
                            var t = b.slides.eq(e),
                                r = t[0].progress;
                            b.params.flip.limitRotation && (r = Math.max(Math.min(t[0].progress, 1), -1));
                            var i = t[0].swiperSlideOffset,
                                s = -180 * r,
                                n = s,
                                o = 0,
                                l = -i,
                                p = 0;
                            if (b.isHorizontal() ? b.rtl && (n = -n) : (p = l, l = 0, o = -n, n = 0), t[0].style.zIndex = -Math.abs(Math.round(r)) + b.slides.length, b.params.flip.slideShadows) {
                                var d = b.isHorizontal() ? t.find(".swiper-slide-shadow-left") : t.find(".swiper-slide-shadow-top"),
                                    c = b.isHorizontal() ? t.find(".swiper-slide-shadow-right") : t.find(".swiper-slide-shadow-bottom");
                                0 === d.length && (d = a('<div class="swiper-slide-shadow-' + (b.isHorizontal() ? "left" : "top") + '"></div>'), t.append(d)), 0 === c.length && (c = a('<div class="swiper-slide-shadow-' + (b.isHorizontal() ? "right" : "bottom") + '"></div>'), t.append(c)), d.length && (d[0].style.opacity = Math.max(-r, 0)), c.length && (c[0].style.opacity = Math.max(r, 0))
                            }
                            t.transform("translate3d(" + l + "px, " + p + "px, 0px) rotateX(" + o + "deg) rotateY(" + n + "deg)")
                        }
                    }, setTransition: function (e) {
                        if (b.slides.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e), b.params.virtualTranslate && 0 !== e) {
                            var t = !1;
                            b.slides.eq(b.activeIndex).transitionEnd(function () {
                                if (!t && b && a(this).hasClass(b.params.slideActiveClass)) {
                                    t = !0, b.animating = !1;
                                    for (var e = ["webkitTransitionEnd", "transitionend", "oTransitionEnd", "MSTransitionEnd", "msTransitionEnd"], r = 0; r < e.length; r++) b.wrapper.trigger(e[r])
                                }
                            })
                        }
                    }
                },
                cube: {
                    setTranslate: function () {
                        var e, t = 0;
                        b.params.cube.shadow && (b.isHorizontal() ? (e = b.wrapper.find(".swiper-cube-shadow"), 0 === e.length && (e = a('<div class="swiper-cube-shadow"></div>'), b.wrapper.append(e)), e.css({
                            height: b.width + "px"
                        })) : (e = b.container.find(".swiper-cube-shadow"), 0 === e.length && (e = a('<div class="swiper-cube-shadow"></div>'), b.container.append(e))));
                        for (var r = 0; r < b.slides.length; r++) {
                            var i = b.slides.eq(r),
                                s = 90 * r,
                                n = Math.floor(s / 360);
                            b.rtl && (s = -s, n = Math.floor(-s / 360));
                            var o = Math.max(Math.min(i[0].progress, 1), -1),
                                l = 0,
                                p = 0,
                                d = 0;
                            r % 4 === 0 ? (l = 4 * -n * b.size, d = 0) : (r - 1) % 4 === 0 ? (l = 0, d = 4 * -n * b.size) : (r - 2) % 4 === 0 ? (l = b.size + 4 * n * b.size, d = b.size) : (r - 3) % 4 === 0 && (l = -b.size, d = 3 * b.size + 4 * b.size * n), b.rtl && (l = -l), b.isHorizontal() || (p = l, l = 0);
                            var c = "rotateX(" + (b.isHorizontal() ? 0 : -s) + "deg) rotateY(" + (b.isHorizontal() ? s : 0) + "deg) translate3d(" + l + "px, " + p + "px, " + d + "px)";
                            if (1 >= o && o > -1 && (t = 90 * r + 90 * o, b.rtl && (t = 90 * -r - 90 * o)), i.transform(c), b.params.cube.slideShadows) {
                                var u = b.isHorizontal() ? i.find(".swiper-slide-shadow-left") : i.find(".swiper-slide-shadow-top"),
                                    m = b.isHorizontal() ? i.find(".swiper-slide-shadow-right") : i.find(".swiper-slide-shadow-bottom");
                                0 === u.length && (u = a('<div class="swiper-slide-shadow-' + (b.isHorizontal() ? "left" : "top") + '"></div>'), i.append(u)), 0 === m.length && (m = a('<div class="swiper-slide-shadow-' + (b.isHorizontal() ? "right" : "bottom") + '"></div>'), i.append(m)), u.length && (u[0].style.opacity = Math.max(-o, 0)), m.length && (m[0].style.opacity = Math.max(o, 0))
                            }
                        }
                        if (b.wrapper.css({
                            "-webkit-transform-origin": "50% 50% -" + b.size / 2 + "px",
                            "-moz-transform-origin": "50% 50% -" + b.size / 2 + "px",
                            "-ms-transform-origin": "50% 50% -" + b.size / 2 + "px",
                            "transform-origin": "50% 50% -" + b.size / 2 + "px"
                        }), b.params.cube.shadow)
                            if (b.isHorizontal()) e.transform("translate3d(0px, " + (b.width / 2 + b.params.cube.shadowOffset) + "px, " + -b.width / 2 + "px) rotateX(90deg) rotateZ(0deg) scale(" + b.params.cube.shadowScale + ")");
                            else {
                                var h = Math.abs(t) - 90 * Math.floor(Math.abs(t) / 90),
                                    f = 1.5 - (Math.sin(2 * h * Math.PI / 360) / 2 + Math.cos(2 * h * Math.PI / 360) / 2),
                                    g = b.params.cube.shadowScale,
                                    v = b.params.cube.shadowScale / f,
                                    w = b.params.cube.shadowOffset;
                                e.transform("scale3d(" + g + ", 1, " + v + ") translate3d(0px, " + (b.height / 2 + w) + "px, " + -b.height / 2 / v + "px) rotateX(-90deg)")
                            }
                        var y = b.isSafari || b.isUiWebView ? -b.size / 2 : 0;
                        b.wrapper.transform("translate3d(0px,0," + y + "px) rotateX(" + (b.isHorizontal() ? 0 : t) + "deg) rotateY(" + (b.isHorizontal() ? -t : 0) + "deg)")
                    }, setTransition: function (e) {
                        b.slides.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e), b.params.cube.shadow && !b.isHorizontal() && b.container.find(".swiper-cube-shadow").transition(e)
                    }
                },
                coverflow: {
                    setTranslate: function () {
                        for (var e = b.translate, t = b.isHorizontal() ? -e + b.width / 2 : -e + b.height / 2, r = b.isHorizontal() ? b.params.coverflow.rotate : -b.params.coverflow.rotate, i = b.params.coverflow.depth, s = 0, n = b.slides.length; n > s; s++) {
                            var o = b.slides.eq(s),
                                l = b.slidesSizesGrid[s],
                                p = o[0].swiperSlideOffset,
                                d = (t - p - l / 2) / l * b.params.coverflow.modifier,
                                c = b.isHorizontal() ? r * d : 0,
                                u = b.isHorizontal() ? 0 : r * d,
                                m = -i * Math.abs(d),
                                h = b.isHorizontal() ? 0 : b.params.coverflow.stretch * d,
                                f = b.isHorizontal() ? b.params.coverflow.stretch * d : 0;
                            Math.abs(f) < .001 && (f = 0), Math.abs(h) < .001 && (h = 0), Math.abs(m) < .001 && (m = 0), Math.abs(c) < .001 && (c = 0), Math.abs(u) < .001 && (u = 0);
                            var g = "translate3d(" + f + "px," + h + "px," + m + "px)  rotateX(" + u + "deg) rotateY(" + c + "deg)";
                            if (o.transform(g), o[0].style.zIndex = -Math.abs(Math.round(d)) + 1, b.params.coverflow.slideShadows) {
                                var v = b.isHorizontal() ? o.find(".swiper-slide-shadow-left") : o.find(".swiper-slide-shadow-top"),
                                    w = b.isHorizontal() ? o.find(".swiper-slide-shadow-right") : o.find(".swiper-slide-shadow-bottom");
                                0 === v.length && (v = a('<div class="swiper-slide-shadow-' + (b.isHorizontal() ? "left" : "top") + '"></div>'), o.append(v)), 0 === w.length && (w = a('<div class="swiper-slide-shadow-' + (b.isHorizontal() ? "right" : "bottom") + '"></div>'), o.append(w)), v.length && (v[0].style.opacity = d > 0 ? d : 0), w.length && (w[0].style.opacity = -d > 0 ? -d : 0)
                            }
                        }
                        if (b.browser.ie) {
                            var y = b.wrapper[0].style;
                            y.perspectiveOrigin = t + "px 50%"
                        }
                    }, setTransition: function (e) {
                        b.slides.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e)
                    }
                }
            }, b.lazy = {
                initialImageLoaded: !1,
                loadImageInSlide: function (e, t) {
                    if ("undefined" != typeof e && ("undefined" == typeof t && (t = !0), 0 !== b.slides.length)) {
                        var r = b.slides.eq(e),
                            i = r.find(".swiper-lazy:not(.swiper-lazy-loaded):not(.swiper-lazy-loading)");
                        !r.hasClass("swiper-lazy") || r.hasClass("swiper-lazy-loaded") || r.hasClass("swiper-lazy-loading") || (i = i.add(r[0])), 0 !== i.length && i.each(function () {
                            var e = a(this);
                            e.addClass("swiper-lazy-loading");
                            var i = e.attr("data-background"),
                                s = e.attr("data-src"),
                                n = e.attr("data-srcset");
                            b.loadImage(e[0], s || i, n, !1, function () {
                                if (i ? (e.css("background-image", 'url("' + i + '")'), e.removeAttr("data-background")) : (n && (e.attr("srcset", n), e.removeAttr("data-srcset")), s && (e.attr("src", s), e.removeAttr("data-src"))), e.addClass("swiper-lazy-loaded").removeClass("swiper-lazy-loading"), r.find(".swiper-lazy-preloader, .preloader").remove(), b.params.loop && t) {
                                    var a = r.attr("data-swiper-slide-index");
                                    if (r.hasClass(b.params.slideDuplicateClass)) {
                                        var o = b.wrapper.children('[data-swiper-slide-index="' + a + '"]:not(.' + b.params.slideDuplicateClass + ")");
                                        b.lazy.loadImageInSlide(o.index(), !1)
                                    } else {
                                        var l = b.wrapper.children("." + b.params.slideDuplicateClass + '[data-swiper-slide-index="' + a + '"]');
                                        b.lazy.loadImageInSlide(l.index(), !1)
                                    }
                                }
                                b.emit("onLazyImageReady", b, r[0], e[0])
                            }), b.emit("onLazyImageLoad", b, r[0], e[0])
                        })
                    }
                }, load: function () {
                    var e;
                    if (b.params.watchSlidesVisibility) b.wrapper.children("." + b.params.slideVisibleClass).each(function () {
                        b.lazy.loadImageInSlide(a(this).index())
                    });
                    else if (b.params.slidesPerView > 1)
                        for (e = b.activeIndex; e < b.activeIndex + b.params.slidesPerView; e++) b.slides[e] && b.lazy.loadImageInSlide(e);
                    else b.lazy.loadImageInSlide(b.activeIndex); if (b.params.lazyLoadingInPrevNext)
                        if (b.params.slidesPerView > 1 || b.params.lazyLoadingInPrevNextAmount && b.params.lazyLoadingInPrevNextAmount > 1) {
                            var t = b.params.lazyLoadingInPrevNextAmount,
                                r = b.params.slidesPerView,
                                i = Math.min(b.activeIndex + r + Math.max(t, r), b.slides.length),
                                s = Math.max(b.activeIndex - Math.max(r, t), 0);
                            for (e = b.activeIndex + b.params.slidesPerView; i > e; e++) b.slides[e] && b.lazy.loadImageInSlide(e);
                            for (e = s; e < b.activeIndex; e++) b.slides[e] && b.lazy.loadImageInSlide(e)
                        } else {
                            var n = b.wrapper.children("." + b.params.slideNextClass);
                            n.length > 0 && b.lazy.loadImageInSlide(n.index());
                            var o = b.wrapper.children("." + b.params.slidePrevClass);
                            o.length > 0 && b.lazy.loadImageInSlide(o.index())
                        }
                }, onTransitionStart: function () {
                    b.params.lazyLoading && (b.params.lazyLoadingOnTransitionStart || !b.params.lazyLoadingOnTransitionStart && !b.lazy.initialImageLoaded) && b.lazy.load()
                }, onTransitionEnd: function () {
                    b.params.lazyLoading && !b.params.lazyLoadingOnTransitionStart && b.lazy.load()
                }
            }, b.scrollbar = {
                isTouched: !1,
                setDragPosition: function (e) {
                    var a = b.scrollbar,
                        t = b.isHorizontal() ? "touchstart" === e.type || "touchmove" === e.type ? e.targetTouches[0].pageX : e.pageX || e.clientX : "touchstart" === e.type || "touchmove" === e.type ? e.targetTouches[0].pageY : e.pageY || e.clientY,
                        r = t - a.track.offset()[b.isHorizontal() ? "left" : "top"] - a.dragSize / 2,
                        i = -b.minTranslate() * a.moveDivider,
                        s = -b.maxTranslate() * a.moveDivider;
                    i > r ? r = i : r > s && (r = s), r = -r / a.moveDivider, b.updateProgress(r), b.setWrapperTranslate(r, !0)
                }, dragStart: function (e) {
                    var a = b.scrollbar;
                    a.isTouched = !0, e.preventDefault(), e.stopPropagation(), a.setDragPosition(e), clearTimeout(a.dragTimeout), a.track.transition(0), b.params.scrollbarHide && a.track.css("opacity", 1), b.wrapper.transition(100), a.drag.transition(100), b.emit("onScrollbarDragStart", b)
                }, dragMove: function (e) {
                    var a = b.scrollbar;
                    a.isTouched && (e.preventDefault ? e.preventDefault() : e.returnValue = !1, a.setDragPosition(e), b.wrapper.transition(0), a.track.transition(0), a.drag.transition(0), b.emit("onScrollbarDragMove", b))
                }, dragEnd: function (e) {
                    var a = b.scrollbar;
                    a.isTouched && (a.isTouched = !1, b.params.scrollbarHide && (clearTimeout(a.dragTimeout), a.dragTimeout = setTimeout(function () {
                        a.track.css("opacity", 0), a.track.transition(400)
                    }, 1e3)), b.emit("onScrollbarDragEnd", b), b.params.scrollbarSnapOnRelease && b.slideReset())
                }, enableDraggable: function () {
                    var e = b.scrollbar,
                        t = b.support.touch ? e.track : document;
                    a(e.track).on(b.touchEvents.start, e.dragStart), a(t).on(b.touchEvents.move, e.dragMove), a(t).on(b.touchEvents.end, e.dragEnd)
                }, disableDraggable: function () {
                    var e = b.scrollbar,
                        t = b.support.touch ? e.track : document;
                    a(e.track).off(b.touchEvents.start, e.dragStart), a(t).off(b.touchEvents.move, e.dragMove), a(t).off(b.touchEvents.end, e.dragEnd)
                }, set: function () {
                    if (b.params.scrollbar) {
                        var e = b.scrollbar;
                        e.track = a(b.params.scrollbar), b.params.uniqueNavElements && "string" == typeof b.params.scrollbar && e.track.length > 1 && 1 === b.container.find(b.params.scrollbar).length && (e.track = b.container.find(b.params.scrollbar)), e.drag = e.track.find(".swiper-scrollbar-drag"), 0 === e.drag.length && (e.drag = a('<div class="swiper-scrollbar-drag"></div>'), e.track.append(e.drag)), e.drag[0].style.width = "", e.drag[0].style.height = "", e.trackSize = b.isHorizontal() ? e.track[0].offsetWidth : e.track[0].offsetHeight, e.divider = b.size / b.virtualSize, e.moveDivider = e.divider * (e.trackSize / b.size), e.dragSize = e.trackSize * e.divider, b.isHorizontal() ? e.drag[0].style.width = e.dragSize + "px" : e.drag[0].style.height = e.dragSize + "px", e.divider >= 1 ? e.track[0].style.display = "none" : e.track[0].style.display = "", b.params.scrollbarHide && (e.track[0].style.opacity = 0)
                    }
                }, setTranslate: function () {
                    if (b.params.scrollbar) {
                        var e, a = b.scrollbar,
                            t = (b.translate || 0, a.dragSize);
                        e = (a.trackSize - a.dragSize) * b.progress, b.rtl && b.isHorizontal() ? (e = -e, e > 0 ? (t = a.dragSize - e, e = 0) : -e + a.dragSize > a.trackSize && (t = a.trackSize + e)) : 0 > e ? (t = a.dragSize + e, e = 0) : e + a.dragSize > a.trackSize && (t = a.trackSize - e), b.isHorizontal() ? (b.support.transforms3d ? a.drag.transform("translate3d(" + e + "px, 0, 0)") : a.drag.transform("translateX(" + e + "px)"), a.drag[0].style.width = t + "px") : (b.support.transforms3d ? a.drag.transform("translate3d(0px, " + e + "px, 0)") : a.drag.transform("translateY(" + e + "px)"), a.drag[0].style.height = t + "px"), b.params.scrollbarHide && (clearTimeout(a.timeout), a.track[0].style.opacity = 1, a.timeout = setTimeout(function () {
                            a.track[0].style.opacity = 0, a.track.transition(400)
                        }, 1e3))
                    }
                }, setTransition: function (e) {
                    b.params.scrollbar && b.scrollbar.drag.transition(e)
                }
            }, b.controller = {
                LinearSpline: function (e, a) {
                    this.x = e, this.y = a, this.lastIndex = e.length - 1;
                    var t, r;
                    this.x.length;
                    this.interpolate = function (e) {
                        return e ? (r = i(this.x, e), t = r - 1, (e - this.x[t]) * (this.y[r] - this.y[t]) / (this.x[r] - this.x[t]) + this.y[t]) : 0
                    };
                    var i = function () {
                        var e, a, t;
                        return function (r, i) {
                            for (a = -1, e = r.length; e - a > 1;) r[t = e + a >> 1] <= i ? a = t : e = t;
                            return e
                        }
                    }()
                }, getInterpolateFunction: function (e) {
                    b.controller.spline || (b.controller.spline = b.params.loop ? new b.controller.LinearSpline(b.slidesGrid, e.slidesGrid) : new b.controller.LinearSpline(b.snapGrid, e.snapGrid))
                }, setTranslate: function (e, a) {
                    function r(a) {
                        e = a.rtl && "horizontal" === a.params.direction ? -b.translate : b.translate, "slide" === b.params.controlBy && (b.controller.getInterpolateFunction(a), s = -b.controller.spline.interpolate(-e)), s && "container" !== b.params.controlBy || (i = (a.maxTranslate() - a.minTranslate()) / (b.maxTranslate() - b.minTranslate()), s = (e - b.minTranslate()) * i + a.minTranslate()), b.params.controlInverse && (s = a.maxTranslate() - s), a.updateProgress(s), a.setWrapperTranslate(s, !1, b), a.updateActiveIndex()
                    }
                    var i, s, n = b.params.control;
                    if (b.isArray(n))
                        for (var o = 0; o < n.length; o++) n[o] !== a && n[o] instanceof t && r(n[o]);
                    else n instanceof t && a !== n && r(n)
                }, setTransition: function (e, a) {
                    function r(a) {
                        a.setWrapperTransition(e, b), 0 !== e && (a.onTransitionStart(), a.wrapper.transitionEnd(function () {
                            s && (a.params.loop && "slide" === b.params.controlBy && a.fixLoop(), a.onTransitionEnd())
                        }))
                    }
                    var i, s = b.params.control;
                    if (b.isArray(s))
                        for (i = 0; i < s.length; i++) s[i] !== a && s[i] instanceof t && r(s[i]);
                    else s instanceof t && a !== s && r(s)
                }
            }, b.hashnav = {
                init: function () {
                    if (b.params.hashnav) {
                        b.hashnav.initialized = !0;
                        var e = document.location.hash.replace("#", "");
                        if (e)
                            for (var a = 0, t = 0, r = b.slides.length; r > t; t++) {
                                var i = b.slides.eq(t),
                                    s = i.attr("data-hash");
                                if (s === e && !i.hasClass(b.params.slideDuplicateClass)) {
                                    var n = i.index();
                                    b.slideTo(n, a, b.params.runCallbacksOnInit, !0)
                                }
                            }
                    }
                }, setHash: function () {
                    b.hashnav.initialized && b.params.hashnav && (document.location.hash = b.slides.eq(b.activeIndex).attr("data-hash") || "")
                }
            }, b.disableKeyboardControl = function () {
                b.params.keyboardControl = !1, a(document).off("keydown", p)
            }, b.enableKeyboardControl = function () {
                b.params.keyboardControl = !0, a(document).on("keydown", p)
            }, b.mousewheel = {
                event: !1,
                lastScrollTime: (new window.Date).getTime()
            }, b.params.mousewheelControl) {
                try {
                    new window.WheelEvent("wheel"), b.mousewheel.event = "wheel"
                } catch (N) {
                    (window.WheelEvent || b.container[0] && "wheel" in b.container[0]) && (b.mousewheel.event = "wheel")
                }!b.mousewheel.event && window.WheelEvent, b.mousewheel.event || void 0 === document.onmousewheel || (b.mousewheel.event = "mousewheel"), b.mousewheel.event || (b.mousewheel.event = "DOMMouseScroll")
            }
            b.disableMousewheelControl = function () {
                return b.mousewheel.event ? (b.container.off(b.mousewheel.event, d), !0) : !1
            }, b.enableMousewheelControl = function () {
                return b.mousewheel.event ? (b.container.on(b.mousewheel.event, d), !0) : !1
            }, b.parallax = {
                setTranslate: function () {
                    b.container.children("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function () {
                        c(this, b.progress)
                    }), b.slides.each(function () {
                        var e = a(this);
                        e.find("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function () {
                            var a = Math.min(Math.max(e[0].progress, -1), 1);
                            c(this, a)
                        })
                    })
                }, setTransition: function (e) {
                    "undefined" == typeof e && (e = b.params.speed), b.container.find("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function () {
                        var t = a(this),
                            r = parseInt(t.attr("data-swiper-parallax-duration"), 10) || e;
                        0 === e && (r = 0), t.transition(r)
                    })
                }
            }, b._plugins = [];
            for (var R in b.plugins) {
                var Y = b.plugins[R](b, b.params[R]);
                Y && b._plugins.push(Y)
            }
            return b.callPlugins = function (e) {
                for (var a = 0; a < b._plugins.length; a++) e in b._plugins[a] && b._plugins[a][e](arguments[1], arguments[2], arguments[3], arguments[4], arguments[5])
            }, b.emitterEventListeners = {}, b.emit = function (e) {
                b.params[e] && b.params[e](arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);
                var a;
                if (b.emitterEventListeners[e])
                    for (a = 0; a < b.emitterEventListeners[e].length; a++) b.emitterEventListeners[e][a](arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);
                b.callPlugins && b.callPlugins(e, arguments[1], arguments[2], arguments[3], arguments[4], arguments[5])
            }, b.on = function (e, a) {
                return e = u(e), b.emitterEventListeners[e] || (b.emitterEventListeners[e] = []), b.emitterEventListeners[e].push(a), b
            }, b.off = function (e, a) {
                var t;
                if (e = u(e), "undefined" == typeof a) return b.emitterEventListeners[e] = [], b;
                if (b.emitterEventListeners[e] && 0 !== b.emitterEventListeners[e].length) {
                    for (t = 0; t < b.emitterEventListeners[e].length; t++) b.emitterEventListeners[e][t] === a && b.emitterEventListeners[e].splice(t, 1);
                    return b
                }
            }, b.once = function (e, a) {
                e = u(e);
                var t = function () {
                    a(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4]), b.off(e, t)
                };
                return b.on(e, t), b
            }, b.a11y = {
                makeFocusable: function (e) {
                    return e.attr("tabIndex", "0"), e
                }, addRole: function (e, a) {
                    return e.attr("role", a), e
                }, addLabel: function (e, a) {
                    return e.attr("aria-label", a), e
                }, disable: function (e) {
                    return e.attr("aria-disabled", !0), e
                }, enable: function (e) {
                    return e.attr("aria-disabled", !1), e
                }, onEnterKey: function (e) {
                    13 === e.keyCode && (a(e.target).is(b.params.nextButton) ? (b.onClickNext(e), b.isEnd ? b.a11y.notify(b.params.lastSlideMessage) : b.a11y.notify(b.params.nextSlideMessage)) : a(e.target).is(b.params.prevButton) && (b.onClickPrev(e), b.isBeginning ? b.a11y.notify(b.params.firstSlideMessage) : b.a11y.notify(b.params.prevSlideMessage)), a(e.target).is("." + b.params.bulletClass) && a(e.target)[0].click())
                }, liveRegion: a('<span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>'),
                notify: function (e) {
                    var a = b.a11y.liveRegion;
                    0 !== a.length && (a.html(""), a.html(e))
                }, init: function () {
                    b.params.nextButton && b.nextButton && b.nextButton.length > 0 && (b.a11y.makeFocusable(b.nextButton), b.a11y.addRole(b.nextButton, "button"), b.a11y.addLabel(b.nextButton, b.params.nextSlideMessage)), b.params.prevButton && b.prevButton && b.prevButton.length > 0 && (b.a11y.makeFocusable(b.prevButton), b.a11y.addRole(b.prevButton, "button"), b.a11y.addLabel(b.prevButton, b.params.prevSlideMessage)), a(b.container).append(b.a11y.liveRegion)
                }, initPagination: function () {
                    b.params.pagination && b.params.paginationClickable && b.bullets && b.bullets.length && b.bullets.each(function () {
                        var e = a(this);
                        b.a11y.makeFocusable(e), b.a11y.addRole(e, "button"), b.a11y.addLabel(e, b.params.paginationBulletMessage.replace(/{{index}}/, e.index() + 1))
                    })
                }, destroy: function () {
                    b.a11y.liveRegion && b.a11y.liveRegion.length > 0 && b.a11y.liveRegion.remove()
                }
            }, b.init = function () {
                b.params.loop && b.createLoop(), b.updateContainerSize(), b.updateSlidesSize(), b.updatePagination(), b.params.scrollbar && b.scrollbar && (b.scrollbar.set(), b.params.scrollbarDraggable && b.scrollbar.enableDraggable()), "slide" !== b.params.effect && b.effects[b.params.effect] && (b.params.loop || b.updateProgress(), b.effects[b.params.effect].setTranslate()), b.params.loop ? b.slideTo(b.params.initialSlide + b.loopedSlides, 0, b.params.runCallbacksOnInit) : (b.slideTo(b.params.initialSlide, 0, b.params.runCallbacksOnInit), 0 === b.params.initialSlide && (b.parallax && b.params.parallax && b.parallax.setTranslate(), b.lazy && b.params.lazyLoading && (b.lazy.load(), b.lazy.initialImageLoaded = !0))), b.attachEvents(), b.params.observer && b.support.observer && b.initObservers(), b.params.preloadImages && !b.params.lazyLoading && b.preloadImages(), b.params.autoplay && b.startAutoplay(), b.params.keyboardControl && b.enableKeyboardControl && b.enableKeyboardControl(), b.params.mousewheelControl && b.enableMousewheelControl && b.enableMousewheelControl(),
                    b.params.hashnav && b.hashnav && b.hashnav.init(), b.params.a11y && b.a11y && b.a11y.init(), b.emit("onInit", b)
            }, b.cleanupStyles = function () {
                b.container.removeClass(b.classNames.join(" ")).removeAttr("style"), b.wrapper.removeAttr("style"), b.slides && b.slides.length && b.slides.removeClass([b.params.slideVisibleClass, b.params.slideActiveClass, b.params.slideNextClass, b.params.slidePrevClass].join(" ")).removeAttr("style").removeAttr("data-swiper-column").removeAttr("data-swiper-row"), b.paginationContainer && b.paginationContainer.length && b.paginationContainer.removeClass(b.params.paginationHiddenClass), b.bullets && b.bullets.length && b.bullets.removeClass(b.params.bulletActiveClass), b.params.prevButton && a(b.params.prevButton).removeClass(b.params.buttonDisabledClass), b.params.nextButton && a(b.params.nextButton).removeClass(b.params.buttonDisabledClass), b.params.scrollbar && b.scrollbar && (b.scrollbar.track && b.scrollbar.track.length && b.scrollbar.track.removeAttr("style"), b.scrollbar.drag && b.scrollbar.drag.length && b.scrollbar.drag.removeAttr("style"))
            }, b.destroy = function (e, a) {
                b.detachEvents(), b.stopAutoplay(), b.params.scrollbar && b.scrollbar && b.params.scrollbarDraggable && b.scrollbar.disableDraggable(), b.params.loop && b.destroyLoop(), a && b.cleanupStyles(), b.disconnectObservers(), b.params.keyboardControl && b.disableKeyboardControl && b.disableKeyboardControl(), b.params.mousewheelControl && b.disableMousewheelControl && b.disableMousewheelControl(), b.params.a11y && b.a11y && b.a11y.destroy(), b.emit("onDestroy"), e !== !1 && (b = null)
            }, b.init(), b
        }
    };
    t.prototype = {
        isSafari: function () {
            var e = navigator.userAgent.toLowerCase();
            return e.indexOf("safari") >= 0 && e.indexOf("chrome") < 0 && e.indexOf("android") < 0
        }(),
        isUiWebView: /(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(navigator.userAgent),
        isArray: function (e) {
            return "[object Array]" === Object.prototype.toString.apply(e)
        }, browser: {
            ie: window.navigator.pointerEnabled || window.navigator.msPointerEnabled,
            ieTouch: window.navigator.msPointerEnabled && window.navigator.msMaxTouchPoints > 1 || window.navigator.pointerEnabled && window.navigator.maxTouchPoints > 1
        }, device: function () {
            var e = navigator.userAgent,
                a = e.match(/(Android);?[\s\/]+([\d.]+)?/),
                t = e.match(/(iPad).*OS\s([\d_]+)/),
                r = e.match(/(iPod)(.*OS\s([\d_]+))?/),
                i = !t && e.match(/(iPhone\sOS)\s([\d_]+)/);
            return {
                ios: t || i || r,
                android: a
            }
        }(),
        support: {
            touch: window.Modernizr && Modernizr.touch === !0 || function () {
                return !!("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch)
            }(),
            transforms3d: window.Modernizr && Modernizr.csstransforms3d === !0 || function () {
                var e = document.createElement("div").style;
                return "webkitPerspective" in e || "MozPerspective" in e || "OPerspective" in e || "MsPerspective" in e || "perspective" in e
            }(),
            flexbox: function () {
                for (var e = document.createElement("div").style, a = "alignItems webkitAlignItems webkitBoxAlign msFlexAlign mozBoxAlign webkitFlexDirection msFlexDirection mozBoxDirection mozBoxOrient webkitBoxDirection webkitBoxOrient".split(" "), t = 0; t < a.length; t++)
                    if (a[t] in e) return !0
            }(),
            observer: function () {
                return "MutationObserver" in window || "WebkitMutationObserver" in window
            }()
        },
        plugins: {}
    };
    for (var r = (function () {
        var e = function (e) {
                var a = this,
                    t = 0;
                for (t = 0; t < e.length; t++) a[t] = e[t];
                return a.length = e.length, this
            },
            a = function (a, t) {
                var r = [],
                    i = 0;
                if (a && !t && a instanceof e) return a;
                if (a)
                    if ("string" == typeof a) {
                        var s, n, o = a.trim();
                        if (o.indexOf("<") >= 0 && o.indexOf(">") >= 0) {
                            var l = "div";
                            for (0 === o.indexOf("<li") && (l = "ul"), 0 === o.indexOf("<tr") && (l = "tbody"), 0 !== o.indexOf("<td") && 0 !== o.indexOf("<th") || (l = "tr"), 0 === o.indexOf("<tbody") && (l = "table"), 0 === o.indexOf("<option") && (l = "select"), n = document.createElement(l), n.innerHTML = a, i = 0; i < n.childNodes.length; i++) r.push(n.childNodes[i])
                        } else
                            for (s = t || "#" !== a[0] || a.match(/[ .<>:~]/) ? (t || document).querySelectorAll(a) : [document.getElementById(a.split("#")[1])], i = 0; i < s.length; i++) s[i] && r.push(s[i])
                    } else if (a.nodeType || a === window || a === document) r.push(a);
                else if (a.length > 0 && a[0].nodeType)
                    for (i = 0; i < a.length; i++) r.push(a[i]);
                return new e(r)
            };
        return e.prototype = {
            addClass: function (e) {
                if ("undefined" == typeof e) return this;
                for (var a = e.split(" "), t = 0; t < a.length; t++)
                    for (var r = 0; r < this.length; r++) this[r].classList.add(a[t]);
                return this
            }, removeClass: function (e) {
                for (var a = e.split(" "), t = 0; t < a.length; t++)
                    for (var r = 0; r < this.length; r++) this[r].classList.remove(a[t]);
                return this
            }, hasClass: function (e) {
                return this[0] ? this[0].classList.contains(e) : !1
            }, toggleClass: function (e) {
                for (var a = e.split(" "), t = 0; t < a.length; t++)
                    for (var r = 0; r < this.length; r++) this[r].classList.toggle(a[t]);
                return this
            }, attr: function (e, a) {
                if (1 === arguments.length && "string" == typeof e) return this[0] ? this[0].getAttribute(e) : void 0;
                for (var t = 0; t < this.length; t++)
                    if (2 === arguments.length) this[t].setAttribute(e, a);
                    else
                        for (var r in e) this[t][r] = e[r], this[t].setAttribute(r, e[r]);
                return this
            }, removeAttr: function (e) {
                for (var a = 0; a < this.length; a++) this[a].removeAttribute(e);
                return this
            }, data: function (e, a) {
                if ("undefined" != typeof a) {
                    for (var t = 0; t < this.length; t++) {
                        var r = this[t];
                        r.dom7ElementDataStorage || (r.dom7ElementDataStorage = {}), r.dom7ElementDataStorage[e] = a
                    }
                    return this
                }
                if (this[0]) {
                    var i = this[0].getAttribute("data-" + e);
                    return i ? i : this[0].dom7ElementDataStorage && e in this[0].dom7ElementDataStorage ? this[0].dom7ElementDataStorage[e] : void 0
                }
            }, transform: function (e) {
                for (var a = 0; a < this.length; a++) {
                    var t = this[a].style;
                    t.webkitTransform = t.MsTransform = t.msTransform = t.MozTransform = t.OTransform = t.transform = e
                }
                return this
            }, transition: function (e) {
                "string" != typeof e && (e += "ms");
                for (var a = 0; a < this.length; a++) {
                    var t = this[a].style;
                    t.webkitTransitionDuration = t.MsTransitionDuration = t.msTransitionDuration = t.MozTransitionDuration = t.OTransitionDuration = t.transitionDuration = e
                }
                return this
            }, on: function (e, t, r, i) {
                function s(e) {
                    var i = e.target;
                    if (a(i).is(t)) r.call(i, e);
                    else
                        for (var s = a(i).parents(), n = 0; n < s.length; n++) a(s[n]).is(t) && r.call(s[n], e)
                }
                var n, o, l = e.split(" ");
                for (n = 0; n < this.length; n++)
                    if ("function" == typeof t || t === !1)
                        for ("function" == typeof t && (r = arguments[1], i = arguments[2] || !1), o = 0; o < l.length; o++) this[n].addEventListener(l[o], r, i);
                    else
                        for (o = 0; o < l.length; o++) this[n].dom7LiveListeners || (this[n].dom7LiveListeners = []), this[n].dom7LiveListeners.push({
                            listener: r,
                            liveListener: s
                        }), this[n].addEventListener(l[o], s, i);
                return this
            }, off: function (e, a, t, r) {
                for (var i = e.split(" "), s = 0; s < i.length; s++)
                    for (var n = 0; n < this.length; n++)
                        if ("function" == typeof a || a === !1) "function" == typeof a && (t = arguments[1], r = arguments[2] || !1), this[n].removeEventListener(i[s], t, r);
                        else if (this[n].dom7LiveListeners)
                    for (var o = 0; o < this[n].dom7LiveListeners.length; o++) this[n].dom7LiveListeners[o].listener === t && this[n].removeEventListener(i[s], this[n].dom7LiveListeners[o].liveListener, r);
                return this
            }, once: function (e, a, t, r) {
                function i(n) {
                    t(n), s.off(e, a, i, r)
                }
                var s = this;
                "function" == typeof a && (a = !1, t = arguments[1], r = arguments[2]), s.on(e, a, i, r)
            }, trigger: function (e, a) {
                for (var t = 0; t < this.length; t++) {
                    var r;
                    try {
                        r = new window.CustomEvent(e, {
                            detail: a,
                            bubbles: !0,
                            cancelable: !0
                        })
                    } catch (i) {
                        r = document.createEvent("Event"), r.initEvent(e, !0, !0), r.detail = a
                    }
                    this[t].dispatchEvent(r)
                }
                return this
            }, transitionEnd: function (e) {
                function a(s) {
                    if (s.target === this)
                        for (e.call(this, s), t = 0; t < r.length; t++) i.off(r[t], a)
                }
                var t, r = ["webkitTransitionEnd", "transitionend", "oTransitionEnd", "MSTransitionEnd", "msTransitionEnd"],
                    i = this;
                if (e)
                    for (t = 0; t < r.length; t++) i.on(r[t], a);
                return this
            }, width: function () {
                return this[0] === window ? window.innerWidth : this.length > 0 ? parseFloat(this.css("width")) : null
            }, outerWidth: function (e) {
                return this.length > 0 ? e ? this[0].offsetWidth + parseFloat(this.css("margin-right")) + parseFloat(this.css("margin-left")) : this[0].offsetWidth : null
            }, height: function () {
                return this[0] === window ? window.innerHeight : this.length > 0 ? parseFloat(this.css("height")) : null
            }, outerHeight: function (e) {
                return this.length > 0 ? e ? this[0].offsetHeight + parseFloat(this.css("margin-top")) + parseFloat(this.css("margin-bottom")) : this[0].offsetHeight : null
            }, offset: function () {
                if (this.length > 0) {
                    var e = this[0],
                        a = e.getBoundingClientRect(),
                        t = document.body,
                        r = e.clientTop || t.clientTop || 0,
                        i = e.clientLeft || t.clientLeft || 0,
                        s = window.pageYOffset || e.scrollTop,
                        n = window.pageXOffset || e.scrollLeft;
                    return {
                        top: a.top + s - r,
                        left: a.left + n - i
                    }
                }
                return null
            }, css: function (e, a) {
                var t;
                if (1 === arguments.length) {
                    if ("string" != typeof e) {
                        for (t = 0; t < this.length; t++)
                            for (var r in e) this[t].style[r] = e[r];
                        return this
                    }
                    if (this[0]) return window.getComputedStyle(this[0], null).getPropertyValue(e)
                }
                if (2 === arguments.length && "string" == typeof e) {
                    for (t = 0; t < this.length; t++) this[t].style[e] = a;
                    return this
                }
                return this
            }, each: function (e) {
                for (var a = 0; a < this.length; a++) e.call(this[a], a, this[a]);
                return this
            }, html: function (e) {
                if ("undefined" == typeof e) return this[0] ? this[0].innerHTML : void 0;
                for (var a = 0; a < this.length; a++) this[a].innerHTML = e;
                return this
            }, text: function (e) {
                if ("undefined" == typeof e) return this[0] ? this[0].textContent.trim() : null;
                for (var a = 0; a < this.length; a++) this[a].textContent = e;
                return this
            }, is: function (t) {
                if (!this[0]) return !1;
                var r, i;
                if ("string" == typeof t) {
                    var s = this[0];
                    if (s === document) return t === document;
                    if (s === window) return t === window;
                    if (s.matches) return s.matches(t);
                    if (s.webkitMatchesSelector) return s.webkitMatchesSelector(t);
                    if (s.mozMatchesSelector) return s.mozMatchesSelector(t);
                    if (s.msMatchesSelector) return s.msMatchesSelector(t);
                    for (r = a(t), i = 0; i < r.length; i++)
                        if (r[i] === this[0]) return !0;
                    return !1
                }
                if (t === document) return this[0] === document;
                if (t === window) return this[0] === window;
                if (t.nodeType || t instanceof e) {
                    for (r = t.nodeType ? [t] : t, i = 0; i < r.length; i++)
                        if (r[i] === this[0]) return !0;
                    return !1
                }
                return !1
            }, index: function () {
                if (this[0]) {
                    for (var e = this[0], a = 0; null !== (e = e.previousSibling);) 1 === e.nodeType && a++;
                    return a
                }
            }, eq: function (a) {
                if ("undefined" == typeof a) return this;
                var t, r = this.length;
                return a > r - 1 ? new e([]) : 0 > a ? (t = r + a, new e(0 > t ? [] : [this[t]])) : new e([this[a]])
            }, append: function (a) {
                var t, r;
                for (t = 0; t < this.length; t++)
                    if ("string" == typeof a) {
                        var i = document.createElement("div");
                        for (i.innerHTML = a; i.firstChild;) this[t].appendChild(i.firstChild)
                    } else if (a instanceof e)
                    for (r = 0; r < a.length; r++) this[t].appendChild(a[r]);
                else this[t].appendChild(a);
                return this
            }, prepend: function (a) {
                var t, r;
                for (t = 0; t < this.length; t++)
                    if ("string" == typeof a) {
                        var i = document.createElement("div");
                        for (i.innerHTML = a, r = i.childNodes.length - 1; r >= 0; r--) this[t].insertBefore(i.childNodes[r], this[t].childNodes[0])
                    } else if (a instanceof e)
                    for (r = 0; r < a.length; r++) this[t].insertBefore(a[r], this[t].childNodes[0]);
                else this[t].insertBefore(a, this[t].childNodes[0]);
                return this
            }, insertBefore: function (e) {
                for (var t = a(e), r = 0; r < this.length; r++)
                    if (1 === t.length) t[0].parentNode.insertBefore(this[r], t[0]);
                    else if (t.length > 1)
                    for (var i = 0; i < t.length; i++) t[i].parentNode.insertBefore(this[r].cloneNode(!0), t[i])
            }, insertAfter: function (e) {
                for (var t = a(e), r = 0; r < this.length; r++)
                    if (1 === t.length) t[0].parentNode.insertBefore(this[r], t[0].nextSibling);
                    else if (t.length > 1)
                    for (var i = 0; i < t.length; i++) t[i].parentNode.insertBefore(this[r].cloneNode(!0), t[i].nextSibling)
            }, next: function (t) {
                return new e(this.length > 0 ? t ? this[0].nextElementSibling && a(this[0].nextElementSibling).is(t) ? [this[0].nextElementSibling] : [] : this[0].nextElementSibling ? [this[0].nextElementSibling] : [] : [])
            }, nextAll: function (t) {
                var r = [],
                    i = this[0];
                if (!i) return new e([]);
                for (; i.nextElementSibling;) {
                    var s = i.nextElementSibling;
                    t ? a(s).is(t) && r.push(s) : r.push(s), i = s
                }
                return new e(r)
            }, prev: function (t) {
                return new e(this.length > 0 ? t ? this[0].previousElementSibling && a(this[0].previousElementSibling).is(t) ? [this[0].previousElementSibling] : [] : this[0].previousElementSibling ? [this[0].previousElementSibling] : [] : [])
            }, prevAll: function (t) {
                var r = [],
                    i = this[0];
                if (!i) return new e([]);
                for (; i.previousElementSibling;) {
                    var s = i.previousElementSibling;
                    t ? a(s).is(t) && r.push(s) : r.push(s), i = s
                }
                return new e(r)
            }, parent: function (e) {
                for (var t = [], r = 0; r < this.length; r++) e ? a(this[r].parentNode).is(e) && t.push(this[r].parentNode) : t.push(this[r].parentNode);
                return a(a.unique(t))
            }, parents: function (e) {
                for (var t = [], r = 0; r < this.length; r++)
                    for (var i = this[r].parentNode; i;) e ? a(i).is(e) && t.push(i) : t.push(i), i = i.parentNode;
                return a(a.unique(t))
            }, find: function (a) {
                for (var t = [], r = 0; r < this.length; r++)
                    for (var i = this[r].querySelectorAll(a), s = 0; s < i.length; s++) t.push(i[s]);
                return new e(t)
            }, children: function (t) {
                for (var r = [], i = 0; i < this.length; i++)
                    for (var s = this[i].childNodes, n = 0; n < s.length; n++) t ? 1 === s[n].nodeType && a(s[n]).is(t) && r.push(s[n]) : 1 === s[n].nodeType && r.push(s[n]);
                return new e(a.unique(r))
            }, remove: function () {
                for (var e = 0; e < this.length; e++) this[e].parentNode && this[e].parentNode.removeChild(this[e]);
                return this
            }, add: function () {
                var e, t, r = this;
                for (e = 0; e < arguments.length; e++) {
                    var i = a(arguments[e]);
                    for (t = 0; t < i.length; t++) r[r.length] = i[t], r.length++
                }
                return r
            }
        }, a.fn = e.prototype, a.unique = function (e) {
            for (var a = [], t = 0; t < e.length; t++) - 1 === a.indexOf(e[t]) && a.push(e[t]);
            return a
        }, a
    }()), i = ["jQuery", "Zepto", "Dom7"], s = 0; s < i.length; s++) window[i[s]] && e(window[i[s]]);
    var n;
    n = "undefined" == typeof r ? window.Dom7 || window.Zepto || window.jQuery : r, n && ("transitionEnd" in n.fn || (n.fn.transitionEnd = function (e) {
        function a(s) {
            if (s.target === this)
                for (e.call(this, s), t = 0; t < r.length; t++) i.off(r[t], a)
        }
        var t, r = ["webkitTransitionEnd", "transitionend", "oTransitionEnd", "MSTransitionEnd", "msTransitionEnd"],
            i = this;
        if (e)
            for (t = 0; t < r.length; t++) i.on(r[t], a);
        return this
    }), "transform" in n.fn || (n.fn.transform = function (e) {
        for (var a = 0; a < this.length; a++) {
            var t = this[a].style;
            t.webkitTransform = t.MsTransform = t.msTransform = t.MozTransform = t.OTransform = t.transform = e
        }
        return this
    }), "transition" in n.fn || (n.fn.transition = function (e) {
        "string" != typeof e && (e += "ms");
        for (var a = 0; a < this.length; a++) {
            var t = this[a].style;
            t.webkitTransitionDuration = t.MsTransitionDuration = t.msTransitionDuration = t.MozTransitionDuration = t.OTransitionDuration = t.transitionDuration = e
        }
        return this
    })), Zepto.Swiper = t
}(Zepto), + function (e) {
    "use strict";
    e.swiper = function (a, t) {
        return new e.Swiper(a, t)
    }, e.fn.swiper = function (a) {
        return new e.Swiper(this, a)
    }, e.initSwiper = function (a) {
        function t(e) {
            function a() {
                e.destroy(), r.off("pageBeforeRemove", a)
            }
            r.on("pageBeforeRemove", a)
        }
        var r = e(a || document.body),
            i = r.find(".swiper-container");
        if (0 !== i.length)
            for (var s = 0; s < i.length; s++) {
                var n, o = i.eq(s);
                if (o.data("swiper")) o.data("swiper").update(!0);
                else {
                    n = o.dataset();
                    var l = e.swiper(o[0], n);
                    t(l)
                }
            }
    }, e.reinitSwiper = function (a) {
        var t = e(a || ".page-current"),
            r = t.find(".swiper-container");
        if (0 !== r.length)
            for (var i = 0; i < r.length; i++) {
                var s = r[0].swiper;
                s && s.update(!0)
            }
    }
}(Zepto), + function (e) {
    "use strict";
    var a = function (a) {
        var t, r = this,
            i = this.defaults;
        a = a || {};
        for (var s in i) "undefined" == typeof a[s] && (a[s] = i[s]);
        r.params = a;
        var n = r.params.navbarTemplate || '<header class="bar bar-nav"><a class="icon icon-left pull-left photo-browser-close-link' + ("popup" === r.params.type ? " close-popup" : "") + '"></a><h1 class="title"><div class="center sliding"><span class="photo-browser-current"></span> <span class="photo-browser-of">' + r.params.ofText + '</span> <span class="photo-browser-total"></span></div></h1></header>',
            o = r.params.toolbarTemplate || '<nav class="bar bar-tab"><a class="tab-item photo-browser-prev" href="#"><i class="icon icon-prev"></i></a><a class="tab-item photo-browser-next" href="#"><i class="icon icon-next"></i></a></nav>',
            l = r.params.template || '<div class="photo-browser photo-browser-' + r.params.theme + '">{{navbar}}{{toolbar}}<div data-page="photo-browser-slides" class="content">{{captions}}<div class="photo-browser-swiper-container swiper-container"><div class="photo-browser-swiper-wrapper swiper-wrapper">{{photos}}</div></div></div></div>',
            p = r.params.lazyLoading ? r.params.photoLazyTemplate || '<div class="photo-browser-slide photo-browser-slide-lazy swiper-slide"><div class="preloader' + ("dark" === r.params.theme ? " preloader-white" : "") + '"></div><span class="photo-browser-zoom-container"><img data-src="{{url}}" class="swiper-lazy"></span></div>' : r.params.photoTemplate || '<div class="photo-browser-slide swiper-slide"><span class="photo-browser-zoom-container"><img src="{{url}}"></span></div>',
            d = r.params.captionsTheme || r.params.theme,
            c = r.params.captionsTemplate || '<div class="photo-browser-captions photo-browser-captions-' + d + '">{{captions}}</div>',
            u = r.params.captionTemplate || '<div class="photo-browser-caption" data-caption-index="{{captionIndex}}">{{caption}}</div>',
            m = r.params.objectTemplate || '<div class="photo-browser-slide photo-browser-object-slide swiper-slide">{{html}}</div>',
            h = "",
            f = "";
        for (t = 0; t < r.params.photos.length; t++) {
            var g = r.params.photos[t],
                v = "";
            "string" == typeof g || g instanceof String ? v = g.indexOf("<") >= 0 || g.indexOf(">") >= 0 ? m.replace(/{{html}}/g, g) : p.replace(/{{url}}/g, g) : "object" == typeof g && (g.hasOwnProperty("html") && g.html.length > 0 ? v = m.replace(/{{html}}/g, g.html) : g.hasOwnProperty("url") && g.url.length > 0 && (v = p.replace(/{{url}}/g, g.url)), g.hasOwnProperty("caption") && g.caption.length > 0 ? f += u.replace(/{{caption}}/g, g.caption).replace(/{{captionIndex}}/g, t) : v = v.replace(/{{caption}}/g, "")), h += v
        }
        var w = l.replace("{{navbar}}", r.params.navbar ? n : "").replace("{{noNavbar}}", r.params.navbar ? "" : "no-navbar").replace("{{photos}}", h).replace("{{captions}}", c.replace(/{{captions}}/g, f)).replace("{{toolbar}}", r.params.toolbar ? o : "");
        r.activeIndex = r.params.initialSlide, r.openIndex = r.activeIndex, r.opened = !1, r.open = function (a) {
            return "undefined" == typeof a && (a = r.activeIndex), a = parseInt(a, 10), r.opened && r.swiper ? void r.swiper.slideTo(a) : (r.opened = !0, r.openIndex = a, "standalone" === r.params.type && e(r.params.container).append(w), "popup" === r.params.type && (r.popup = e.popup('<div class="popup photo-browser-popup">' + w + "</div>"), e(r.popup).on("closed", r.onPopupClose)), "page" === r.params.type ? (e(document).on("pageBeforeInit", r.onPageBeforeInit), e(document).on("pageBeforeRemove", r.onPageBeforeRemove), r.params.view || (r.params.view = e.mainView), void r.params.view.loadContent(w)) : (r.layout(r.openIndex), void(r.params.onOpen && r.params.onOpen(r))))
        }, r.close = function () {
            r.opened = !1, r.swiperContainer && 0 !== r.swiperContainer.length && (r.params.onClose && r.params.onClose(r), r.attachEvents(!0), "standalone" === r.params.type && r.container.removeClass("photo-browser-in").addClass("photo-browser-out").transitionEnd(function () {
                r.container.remove()
            }), r.swiper.destroy(), r.swiper = r.swiperContainer = r.swiperWrapper = r.slides = y = b = x = void 0)
        }, r.onPopupClose = function () {
            r.close(), e(r.popup).off("pageBeforeInit", r.onPopupClose)
        }, r.onPageBeforeInit = function (a) {
            "photo-browser-slides" === a.detail.page.name && r.layout(r.openIndex), e(document).off("pageBeforeInit", r.onPageBeforeInit)
        }, r.onPageBeforeRemove = function (a) {
            "photo-browser-slides" === a.detail.page.name && r.close(), e(document).off("pageBeforeRemove", r.onPageBeforeRemove)
        }, r.onSliderTransitionStart = function (a) {
            r.activeIndex = a.activeIndex;
            var t = a.activeIndex + 1,
                i = a.slides.length;
            if (r.params.loop && (i -= 2, t -= a.loopedSlides, 1 > t && (t = i + t), t > i && (t -= i)), r.container.find(".photo-browser-current").text(t), r.container.find(".photo-browser-total").text(i), e(".photo-browser-prev, .photo-browser-next").removeClass("photo-browser-link-inactive"), a.isBeginning && !r.params.loop && e(".photo-browser-prev").addClass("photo-browser-link-inactive"), a.isEnd && !r.params.loop && e(".photo-browser-next").addClass("photo-browser-link-inactive"), r.captions.length > 0) {
                r.captionsContainer.find(".photo-browser-caption-active").removeClass("photo-browser-caption-active");
                var s = r.params.loop ? a.slides.eq(a.activeIndex).attr("data-swiper-slide-index") : r.activeIndex;
                r.captionsContainer.find('[data-caption-index="' + s + '"]').addClass("photo-browser-caption-active")
            }
            var n = a.slides.eq(a.previousIndex).find("video");
            n.length > 0 && "pause" in n[0] && n[0].pause(), r.params.onSlideChangeStart && r.params.onSlideChangeStart(a)
        }, r.onSliderTransitionEnd = function (e) {
            r.params.zoom && y && e.previousIndex !== e.activeIndex && (b.transform("translate3d(0,0,0) scale(1)"), x.transform("translate3d(0,0,0)"), y = b = x = void 0, T = S = 1), r.params.onSlideChangeEnd && r.params.onSlideChangeEnd(e)
        }, r.layout = function (a) {
            "page" === r.params.type ? r.container = e(".photo-browser-swiper-container").parents(".view") : r.container = e(".photo-browser"), "standalone" === r.params.type && r.container.addClass("photo-browser-in"), r.swiperContainer = r.container.find(".photo-browser-swiper-container"), r.swiperWrapper = r.container.find(".photo-browser-swiper-wrapper"), r.slides = r.container.find(".photo-browser-slide"), r.captionsContainer = r.container.find(".photo-browser-captions"), r.captions = r.container.find(".photo-browser-caption");
            var t = {
                nextButton: r.params.nextButton || ".photo-browser-next",
                prevButton: r.params.prevButton || ".photo-browser-prev",
                indexButton: r.params.indexButton,
                initialSlide: a,
                spaceBetween: r.params.spaceBetween,
                speed: r.params.speed,
                loop: r.params.loop,
                lazyLoading: r.params.lazyLoading,
                lazyLoadingInPrevNext: r.params.lazyLoadingInPrevNext,
                lazyLoadingOnTransitionStart: r.params.lazyLoadingOnTransitionStart,
                preloadImages: !r.params.lazyLoading,
                onTap: function (e, a) {
                    r.params.onTap && r.params.onTap(e, a)
                }, onClick: function (e, a) {
                    r.params.exposition && r.toggleExposition(), r.params.onClick && r.params.onClick(e, a)
                }, onDoubleTap: function (a, t) {
                    r.toggleZoom(e(t.target).parents(".photo-browser-slide")), r.params.onDoubleTap && r.params.onDoubleTap(a, t)
                }, onTransitionStart: function (e) {
                    r.onSliderTransitionStart(e)
                }, onTransitionEnd: function (e) {
                    r.onSliderTransitionEnd(e)
                }, onLazyImageLoad: function (e, a, t) {
                    r.params.onLazyImageLoad && r.params.onLazyImageLoad(r, a, t)
                }, onLazyImageReady: function (a, t, i) {
                    e(t).removeClass("photo-browser-slide-lazy"), r.params.onLazyImageReady && r.params.onLazyImageReady(r, t, i)
                }
            };
            r.params.swipeToClose && "page" !== r.params.type && (t.onTouchStart = r.swipeCloseTouchStart, t.onTouchMoveOpposite = r.swipeCloseTouchMove, t.onTouchEnd = r.swipeCloseTouchEnd), r.swiper = e.swiper(r.swiperContainer, t), 0 === a && r.onSliderTransitionStart(r.swiper), r.attachEvents()
        }, r.attachEvents = function (e) {
            var a = e ? "off" : "on";
            if (r.params.zoom) {
                var t = r.params.loop ? r.swiper.slides : r.slides;
                t[a]("gesturestart", r.onSlideGestureStart), t[a]("gesturechange", r.onSlideGestureChange), t[a]("gestureend", r.onSlideGestureEnd), t[a]("touchstart", r.onSlideTouchStart), t[a]("touchmove", r.onSlideTouchMove), t[a]("touchend", r.onSlideTouchEnd)
            }
            r.container.find(".photo-browser-close-link")[a]("click", r.close)
        }, r.exposed = !1, r.toggleExposition = function () {
            r.container && r.container.toggleClass("photo-browser-exposed"), r.params.expositionHideCaptions && r.captionsContainer.toggleClass("photo-browser-captions-exposed"), r.exposed = !r.exposed
        }, r.enableExposition = function () {
            r.container && r.container.addClass("photo-browser-exposed"), r.params.expositionHideCaptions && r.captionsContainer.addClass("photo-browser-captions-exposed"), r.exposed = !0
        }, r.disableExposition = function () {
            r.container && r.container.removeClass("photo-browser-exposed"), r.params.expositionHideCaptions && r.captionsContainer.removeClass("photo-browser-captions-exposed"), r.exposed = !1
        };
        var y, b, x, T = 1,
            S = 1,
            C = !1;
        r.onSlideGestureStart = function () {
            return y || (y = e(this), b = y.find("img, svg, canvas"), x = b.parent(".photo-browser-zoom-container"), 0 !== x.length) ? (b.transition(0), void(C = !0)) : void(b = void 0)
        }, r.onSlideGestureChange = function (e) {
            b && 0 !== b.length && (T = e.scale * S, T > r.params.maxZoom && (T = r.params.maxZoom - 1 + Math.pow(T - r.params.maxZoom + 1, .5)), T < r.params.minZoom && (T = r.params.minZoom + 1 - Math.pow(r.params.minZoom - T + 1, .5)), b.transform("translate3d(0,0,0) scale(" + T + ")"))
        }, r.onSlideGestureEnd = function () {
            b && 0 !== b.length && (T = Math.max(Math.min(T, r.params.maxZoom), r.params.minZoom), b.transition(r.params.speed).transform("translate3d(0,0,0) scale(" + T + ")"), S = T, C = !1, 1 === T && (y = void 0))
        }, r.toggleZoom = function () {
            y || (y = r.swiper.slides.eq(r.swiper.activeIndex), b = y.find("img, svg, canvas"), x = b.parent(".photo-browser-zoom-container")), b && 0 !== b.length && (x.transition(300).transform("translate3d(0,0,0)"), T && 1 !== T ? (T = S = 1, b.transition(300).transform("translate3d(0,0,0) scale(1)"), y = void 0) : (T = S = r.params.maxZoom, b.transition(300).transform("translate3d(0,0,0) scale(" + T + ")")))
        };
        var z, M, E, P, I, k, B, L, D, H, O, G, A, N, R, Y, X, W = {},
            V = {};
        r.onSlideTouchStart = function (a) {
            b && 0 !== b.length && (z || ("android" === e.device.os && a.preventDefault(), z = !0, W.x = "touchstart" === a.type ? a.targetTouches[0].pageX : a.pageX, W.y = "touchstart" === a.type ? a.targetTouches[0].pageY : a.pageY))
        }, r.onSlideTouchMove = function (a) {
            if (b && 0 !== b.length && (r.swiper.allowClick = !1, z && y)) {
                M || (D = b[0].offsetWidth, H = b[0].offsetHeight, O = e.getTranslate(x[0], "x") || 0, G = e.getTranslate(x[0], "y") || 0, x.transition(0));
                var t = D * T,
                    i = H * T;
                if (!(t < r.swiper.width && i < r.swiper.height)) {
                    if (I = Math.min(r.swiper.width / 2 - t / 2, 0), B = -I, k = Math.min(r.swiper.height / 2 - i / 2, 0), L = -k, V.x = "touchmove" === a.type ? a.targetTouches[0].pageX : a.pageX, V.y = "touchmove" === a.type ? a.targetTouches[0].pageY : a.pageY, !M && !C && (Math.floor(I) === Math.floor(O) && V.x < W.x || Math.floor(B) === Math.floor(O) && V.x > W.x)) return void(z = !1);
                    a.preventDefault(), a.stopPropagation(), M = !0, E = V.x - W.x + O, P = V.y - W.y + G, I > E && (E = I + 1 - Math.pow(I - E + 1, .8)), E > B && (E = B - 1 + Math.pow(E - B + 1, .8)), k > P && (P = k + 1 - Math.pow(k - P + 1, .8)), P > L && (P = L - 1 + Math.pow(P - L + 1, .8)), A || (A = V.x), Y || (Y = V.y), N || (N = Date.now()), R = (V.x - A) / (Date.now() - N) / 2, X = (V.y - Y) / (Date.now() - N) / 2, Math.abs(V.x - A) < 2 && (R = 0), Math.abs(V.y - Y) < 2 && (X = 0), A = V.x, Y = V.y, N = Date.now(), x.transform("translate3d(" + E + "px, " + P + "px,0)")
                }
            }
        }, r.onSlideTouchEnd = function () {
            if (b && 0 !== b.length) {
                if (!z || !M) return z = !1, void(M = !1);
                z = !1, M = !1;
                var e = 300,
                    a = 300,
                    t = R * e,
                    i = E + t,
                    s = X * a,
                    n = P + s;
                0 !== R && (e = Math.abs((i - E) / R)), 0 !== X && (a = Math.abs((n - P) / X));
                var o = Math.max(e, a);
                E = i, P = n;
                var l = D * T,
                    p = H * T;
                I = Math.min(r.swiper.width / 2 - l / 2, 0), B = -I, k = Math.min(r.swiper.height / 2 - p / 2, 0), L = -k, E = Math.max(Math.min(E, B), I), P = Math.max(Math.min(P, L), k), x.transition(o).transform("translate3d(" + E + "px, " + P + "px,0)")
            }
        };
        var q, F, Z, j, K, _ = !1,
            U = !0,
            Q = !1;
        return r.swipeCloseTouchStart = function () {
            U && (_ = !0)
        }, r.swipeCloseTouchMove = function (e, a) {
            if (_) {
                Q || (Q = !0, F = "touchmove" === a.type ? a.targetTouches[0].pageY : a.pageY, j = r.swiper.slides.eq(r.swiper.activeIndex), K = (new Date).getTime()), a.preventDefault(), Z = "touchmove" === a.type ? a.targetTouches[0].pageY : a.pageY, q = F - Z;
                var t = 1 - Math.abs(q) / 300;
                j.transform("translate3d(0," + -q + "px,0)"), r.swiper.container.css("opacity", t).transition(0)
            }
        }, r.swipeCloseTouchEnd = function () {
            if (_ = !1, !Q) return void(Q = !1);
            Q = !1, U = !1;
            var a = Math.abs(q),
                t = (new Date).getTime() - K;
            return 300 > t && a > 20 || t >= 300 && a > 100 ? void setTimeout(function () {
                "standalone" === r.params.type && r.close(), "popup" === r.params.type && e.closeModal(r.popup), r.params.onSwipeToClose && r.params.onSwipeToClose(r), U = !0
            }, 0) : (0 !== a ? j.addClass("transitioning").transitionEnd(function () {
                U = !0, j.removeClass("transitioning")
            }) : U = !0, r.swiper.container.css("opacity", "").transition(""), void j.transform(""))
        }, r
    };
    a.prototype = {
        defaults: {
            photos: [],
            container: "body",
            initialSlide: 0,
            spaceBetween: 20,
            speed: 300,
            zoom: !0,
            maxZoom: 3,
            minZoom: 1,
            exposition: !0,
            expositionHideCaptions: !1,
            type: "standalone",
            navbar: !0,
            toolbar: !0,
            theme: "light",
            swipeToClose: !0,
            backLinkText: "Close",
            ofText: "of",
            loop: !1,
            lazyLoading: !1,
            lazyLoadingInPrevNext: !1,
            lazyLoadingOnTransitionStart: !1
        }
    }, e.photoBrowser = function (t) {
        return e.extend(t, e.photoBrowser.prototype.defaults), new a(t)
    }, e.photoBrowser.prototype = {
        defaults: {}
    }
}(Zepto);
! function () {
    function e(e) {
        return e.replace(y, "").replace(w, ",").replace(b, "").replace(x, "").replace(T, "").split(E)
    }

    function n(e) {
        return "'" + e.replace(/('|\\)/g, "\\$1").replace(/\r/g, "\\r").replace(/\n/g, "\\n") + "'"
    }

    function t(t, r) {
        function a(e) {
            return p += e.split(/\n/).length - 1, s && (e = e.replace(/\s+/g, " ").replace(/<!--[\w\W]*?-->/g, "")), e && (e = v[1] + n(e) + v[2] + "\n"), e
        }

        function i(n) {
            var t = p;
            if (u ? n = u(n, r) : o && (n = n.replace(/\n/g, function () {
                return p++, "$line=" + p + ";"
            })), 0 === n.indexOf("=")) {
                var a = f && !/^=[=#]/.test(n);
                if (n = n.replace(/^=[=#]?|[\s;]*$/g, ""), a) {
                    var i = n.replace(/\s*\([^\)]+\)/, "");
                    $[i] || /^(include|print)$/.test(i) || (n = "$escape(" + n + ")")
                } else n = "$string(" + n + ")";
                n = v[1] + n + v[2]
            }
            return o && (n = "$line=" + t + ";" + n), h(e(n), function (e) {
                if (e && !d[e]) {
                    var n;
                    n = "print" === e ? w : "include" === e ? b : $[e] ? "$utils." + e : g[e] ? "$helpers." + e : "$data." + e, x += e + "=" + n + ",", d[e] = !0
                }
            }), n + "\n"
        }
        var o = r.debug,
            c = r.openTag,
            l = r.closeTag,
            u = r.parser,
            s = r.compress,
            f = r.escape,
            p = 1,
            d = {
                $data: 1,
                $filename: 1,
                $utils: 1,
                $helpers: 1,
                $out: 1,
                $line: 1
            },
            m = "".trim,
            v = m ? ["$out='';", "$out+=", ";", "$out"] : ["$out=[];", "$out.push(", ");", "$out.join('')"],
            y = m ? "$out+=text;return $out;" : "$out.push(text);",
            w = "function(){var text=''.concat.apply('',arguments);" + y + "}",
            b = "function(filename,data){data=data||$data;var text=$utils.$include(filename,data,$filename);" + y + "}",
            x = "'use strict';var $utils=this,$helpers=$utils.$helpers," + (o ? "$line=0," : ""),
            T = v[0],
            E = "return new String(" + v[3] + ");";
        h(t.split(c), function (e) {
            e = e.split(l);
            var n = e[0],
                t = e[1];
            1 === e.length ? T += a(n) : (T += i(n), t && (T += a(t)))
        });
        var j = x + T + E;
        o && (j = "try{" + j + "}catch(e){throw {filename:$filename,name:'Render Error',message:e.message,line:$line,source:" + n(t) + ".split(/\\n/)[$line-1].replace(/^\\s+/,'')};}");
        try {
            var S = new Function("$data", "$filename", j);
            return S.prototype = $, S
        } catch (W) {
            throw W.temp = "function anonymous($data,$filename) {" + j + "}", W
        }
    }
    var r = function (e, n) {
        return "string" == typeof n ? m(n, {
            filename: e
        }) : o(e, n)
    };
    r.version = "3.0.0", r.config = function (e, n) {
        a[e] = n
    };
    var a = r.defaults = {
            openTag: "<%",
            closeTag: "%>",
            escape: !0,
            cache: !0,
            compress: !1,
            parser: null
        },
        i = r.cache = {};
    r.render = function (e, n) {
        return m(e, n)
    };
    var o = r.renderFile = function (e, n) {
        var t = r.get(e) || d({
            filename: e,
            name: "Render Error",
            message: "Template not found"
        });
        return n ? t(n) : t
    };
    r.get = function (e) {
        var n;
        if (i[e]) n = i[e];
        else if ("object" == typeof document) {
            var t = document.getElementById(e);
            if (t) {
                var r = (t.value || t.innerHTML).replace(/^\s*|\s*$/g, "");
                n = m(r, {
                    filename: e
                })
            }
        }
        return n
    };
    var c = function (e, n) {
            return "string" != typeof e && (n = typeof e, "number" === n ? e += "" : e = "function" === n ? c(e.call(e)) : ""), e
        },
        l = {
            "<": "&#60;",
            ">": "&#62;",
            '"': "&#34;",
            "'": "&#39;",
            "&": "&#38;"
        },
        u = function (e) {
            return l[e]
        },
        s = function (e) {
            return c(e).replace(/&(?![\w#]+;)|[<>"']/g, u)
        },
        f = Array.isArray || function (e) {
            return "[object Array]" === {}.toString.call(e)
        },
        p = function (e, n) {
            var t, r;
            if (f(e))
                for (t = 0, r = e.length; r > t; t++) n.call(e, e[t], t, e);
            else
                for (t in e) n.call(e, e[t], t)
        },
        $ = r.utils = {
            $helpers: {},
            $include: o,
            $string: c,
            $escape: s,
            $each: p
        };
    r.helper = function (e, n) {
        g[e] = n
    };
    var g = r.helpers = $.$helpers;
    r.onerror = function (e) {
        var n = "Template Error\n\n";
        for (var t in e) n += "<" + t + ">\n" + e[t] + "\n\n";
        "object" == typeof console && console.error(n)
    };
    var d = function (e) {
            return r.onerror(e),
                function () {
                    return "{Template Error}"
                }
        },
        m = r.compile = function (e, n) {
            function r(t) {
                try {
                    return new l(t, c) + ""
                } catch (r) {
                    return n.debug ? d(r)() : (n.debug = !0, m(e, n)(t))
                }
            }
            n = n || {};
            for (var o in a) void 0 === n[o] && (n[o] = a[o]);
            var c = n.filename;
            try {
                var l = t(e, n)
            } catch (u) {
                return u.filename = c || "anonymous", u.name = "Syntax Error", d(u)
            }
            return r.prototype = l.prototype, r.toString = function () {
                return l.toString()
            }, c && n.cache && (i[c] = r), r
        },
        h = $.$each,
        v = "break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if,in,instanceof,new,null,return,switch,this,throw,true,try,typeof,var,void,while,with,abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto,implements,import,int,interface,long,native,package,private,protected,public,short,static,super,synchronized,throws,transient,volatile,arguments,let,yield,undefined",
        y = /\/\*[\w\W]*?\*\/|\/\/[^\n]*\n|\/\/[^\n]*$|"(?:[^"\\]|\\[\w\W])*"|'(?:[^'\\]|\\[\w\W])*'|\s*\.\s*[$\w\.]+/g,
        w = /[^\w$]+/g,
        b = new RegExp(["\\b" + v.replace(/,/g, "\\b|\\b") + "\\b"].join("|"), "g"),
        x = /^\d[^,]*|,\d[^,]*/g,
        T = /^,+|,+$/g,
        E = /^$|,+/;
    "function" == typeof define ? define(function () {
        return r
    }) : "undefined" != typeof exports ? module.exports = r : this.template = r
}();
! function (e, r) {
    function t(e) {
        return function (r) {
            return {}.toString.call(r) == "[object " + e + "]"
        }
    }

    function n() {
        return x++
    }

    function i(e) {
        return e.match(S)[0]
    }

    function a(e) {
        for (e = e.replace(q, "/"); e.match(C);) e = e.replace(C, "/");
        return e = e.replace(I, "$1/")
    }

    function s(e) {
        var r = e.length - 1,
            t = e.charAt(r);
        return "#" === t ? e.substring(0, r) : ".js" === e.substring(r - 2) || e.indexOf("?") > 0 || ".css" === e.substring(r - 3) || "/" === t ? e : e + ".js"
    }

    function o(e) {
        var r = A.alias;
        return r && T(r[e]) ? r[e] : e
    }

    function u(e) {
        var r, t = A.paths;
        return t && (r = e.match(j)) && T(t[r[1]]) && (e = t[r[1]] + r[2]), e
    }

    function c(e) {
        var r = A.vars;
        return r && e.indexOf("{") > -1 && (e = e.replace(G, function (e, t) {
            return T(r[t]) ? r[t] : e
        })), e
    }

    function f(e) {
        var r = A.map,
            t = e;
        if (r)
            for (var n = 0, i = r.length; i > n; n++) {
                var a = r[n];
                if (t = w(a) ? a(e) || e : e.replace(a[0], a[1]), t !== e) break
            }
        return t
    }

    function l(e, r) {
        var t, n = e.charAt(0);
        if (R.test(e)) t = e;
        else if ("." === n) t = a((r ? i(r) : A.cwd) + e);
        else if ("/" === n) {
            var s = A.cwd.match(L);
            t = s ? s[0] + e.substring(1) : e
        } else t = A.base + e;
        return 0 === t.indexOf("//") && (t = location.protocol + t), t
    }

    function d(e, r) {
        if (!e) return "";
        e = o(e), e = u(e), e = c(e), e = s(e);
        var t = l(e, r);
        return t = f(t)
    }

    function v(e) {
        return e.hasAttribute ? e.src : e.getAttribute("src", 4)
    }

    function h(e, r, t, n) {
        var i = P.test(e),
            a = $.createElement(i ? "link" : "script");
        t && (a.charset = t), U(n) || a.setAttribute("crossorigin", n), p(a, r, i, e), i ? (a.rel = "stylesheet", a.href = e) : (a.async = !0, a.src = e), V = a, M ? K.insertBefore(a, M) : K.appendChild(a), V = null
    }

    function p(e, t, n, i) {
        function a() {
            e.onload = e.onerror = e.onreadystatechange = null, n || A.debug || K.removeChild(e), e = null, t()
        }
        var s = "onload" in e;
        return !n || !W && s ? (s ? (e.onload = a, e.onerror = function () {
            O("error", {
                uri: i,
                node: e
            }), a()
        }) : e.onreadystatechange = function () {
            /loaded|complete/.test(e.readyState) && a()
        }, r) : (setTimeout(function () {
            g(e, t)
        }, 1), r)
    }

    function g(e, r) {
        var t, n = e.sheet;
        if (W) n && (t = !0);
        else if (n) try {
            n.cssRules && (t = !0)
        } catch (i) {
            "NS_ERROR_DOM_SECURITY_ERR" === i.name && (t = !0)
        }
        setTimeout(function () {
            t ? r() : g(e, r)
        }, 20)
    }

    function E() {
        if (V) return V;
        if (H && "interactive" === H.readyState) return H;
        for (var e = K.getElementsByTagName("script"), r = e.length - 1; r >= 0; r--) {
            var t = e[r];
            if ("interactive" === t.readyState) return H = t
        }
    }

    function m(e) {
        var r = [];
        return e.replace(J, "").replace(z, function (e, t, n) {
            n && r.push(n)
        }), r
    }

    function y(e, r) {
        this.uri = e, this.dependencies = r || [], this.exports = null, this.status = 0, this._waitings = {}, this._remain = 0
    }
    if (!e.seajs) {
        var b = e.seajs = {
                version: "2.2.3"
            },
            A = b.data = {},
            _ = t("Object"),
            T = t("String"),
            D = Array.isArray || t("Array"),
            w = t("Function"),
            U = t("Undefined"),
            x = 0,
            N = A.events = {};
        b.on = function (e, r) {
            var t = N[e] || (N[e] = []);
            return t.push(r), b
        }, b.off = function (e, r) {
            if (!e && !r) return N = A.events = {}, b;
            var t = N[e];
            if (t)
                if (r)
                    for (var n = t.length - 1; n >= 0; n--) t[n] === r && t.splice(n, 1);
                else delete N[e];
            return b
        };
        var O = b.emit = function (e, r) {
                var t, n = N[e];
                if (n)
                    for (n = n.slice(); t = n.shift();) t(r);
                return b
            },
            S = /[^?#]*\//,
            q = /\/\.\//g,
            C = /\/[^\/]+\/\.\.\//,
            I = /([^:\/])\/\//g,
            j = /^([^\/:]+)(\/.+)$/,
            G = /{([^{]+)}/g,
            R = /^\/\/.|:\//,
            L = /^.*?\/\/.*?\//,
            $ = document,
            k = i($.URL),
            X = $.scripts,
            B = $.getElementById("seajsnode") || X[X.length - 1],
            F = i(v(B) || k);
        b.resolve = d;
        var V, H, K = $.head || $.getElementsByTagName("head")[0] || $.documentElement,
            M = K.getElementsByTagName("base")[0],
            P = /\.css(?:\?|$)/i,
            W = +navigator.userAgent.replace(/.*(?:AppleWebKit|AndroidWebKit)\/(\d+).*/, "$1") < 536;
        b.request = h;
        var Y, z = /"(?:\\"|[^"])*"|'(?:\\'|[^'])*'|\/\*[\S\s]*?\*\/|\/(?:\\\/|[^\/\r\n])+\/(?=[^\/])|\/\/.*|\.\s*require|(?:^|[^$])\brequire\s*\(\s*(["'])(.+?)\1\s*\)/g,
            J = /\\\\/g,
            Q = b.cache = {},
            Z = {},
            ee = {},
            re = {},
            te = y.STATUS = {
                FETCHING: 1,
                SAVED: 2,
                LOADING: 3,
                LOADED: 4,
                EXECUTING: 5,
                EXECUTED: 6
            };
        y.prototype.resolve = function () {
            for (var e = this, r = e.dependencies, t = [], n = 0, i = r.length; i > n; n++) t[n] = y.resolve(r[n], e.uri);
            return t
        }, y.prototype.load = function () {
            var e = this;
            if (!(e.status >= te.LOADING)) {
                e.status = te.LOADING;
                var t = e.resolve();
                O("load", t);
                for (var n, i = e._remain = t.length, a = 0; i > a; a++) n = y.get(t[a]), n.status < te.LOADED ? n._waitings[e.uri] = (n._waitings[e.uri] || 0) + 1 : e._remain--;
                if (0 === e._remain) return e.onload(), r;
                var s = {};
                for (a = 0; i > a; a++) n = Q[t[a]], n.status < te.FETCHING ? n.fetch(s) : n.status === te.SAVED && n.load();
                for (var o in s) s.hasOwnProperty(o) && s[o]()
            }
        }, y.prototype.onload = function () {
            var e = this;
            e.status = te.LOADED, e.callback && e.callback();
            var r, t, n = e._waitings;
            for (r in n) n.hasOwnProperty(r) && (t = Q[r], t._remain -= n[r], 0 === t._remain && t.onload());
            delete e._waitings, delete e._remain
        }, y.prototype.fetch = function (e) {
            function t() {
                b.request(s.requestUri, s.onRequest, s.charset, s.crossorigin)
            }

            function n() {
                delete Z[o], ee[o] = !0, Y && (y.save(a, Y), Y = null);
                var e, r = re[o];
                for (delete re[o]; e = r.shift();) e.load()
            }
            var i = this,
                a = i.uri;
            i.status = te.FETCHING;
            var s = {
                uri: a
            };
            O("fetch", s);
            var o = s.requestUri || a;
            return !o || ee[o] ? (i.load(), r) : Z[o] ? (re[o].push(i), r) : (Z[o] = !0, re[o] = [i], O("request", s = {
                uri: a,
                requestUri: o,
                onRequest: n,
                charset: w(A.charset) ? A.charset(o) : A.charset,
                crossorigin: w(A.crossorigin) ? A.crossorigin(o) : A.crossorigin
            }), s.requested || (e ? e[s.requestUri] = t : t()), r)
        }, y.prototype.exec = function () {
            function e(r) {
                return y.get(e.resolve(r)).exec()
            }
            var t = this;
            if (t.status >= te.EXECUTING) return t.exports;
            t.status = te.EXECUTING;
            var i = t.uri;
            e.resolve = function (e) {
                return y.resolve(e, i)
            }, e.async = function (r, t) {
                return y.use(r, t, i + "_async_" + n()), e
            };
            var a = t.factory,
                s = w(a) ? a(e, t.exports = {}, t) : a;
            return s === r && (s = t.exports), delete t.factory, t.exports = s, t.status = te.EXECUTED, O("exec", t), s
        }, y.resolve = function (e, r) {
            var t = {
                id: e,
                refUri: r
            };
            return O("resolve", t), t.uri || b.resolve(t.id, r)
        }, y.define = function (e, t, n) {
            var i = arguments.length;
            1 === i ? (n = e, e = r) : 2 === i && (n = t, D(e) ? (t = e, e = r) : t = r), !D(t) && w(n) && (t = m("" + n));
            var a = {
                id: e,
                uri: y.resolve(e),
                deps: t,
                factory: n
            };
            if (!a.uri && $.attachEvent) {
                var s = E();
                s && (a.uri = s.src)
            }
            O("define", a), a.uri ? y.save(a.uri, a) : Y = a
        }, y.save = function (e, r) {
            var t = y.get(e);
            t.status < te.SAVED && (t.id = r.id || e, t.dependencies = r.deps || [], t.factory = r.factory, t.status = te.SAVED)
        }, y.get = function (e, r) {
            return Q[e] || (Q[e] = new y(e, r))
        }, y.use = function (r, t, n) {
            var i = y.get(n, D(r) ? r : [r]);
            i.callback = function () {
                for (var r = [], n = i.resolve(), a = 0, s = n.length; s > a; a++) r[a] = Q[n[a]].exec();
                t && t.apply(e, r), delete i.callback
            }, i.load()
        }, y.preload = function (e) {
            var r = A.preload,
                t = r.length;
            t ? y.use(r, function () {
                r.splice(0, t), y.preload(e)
            }, A.cwd + "_preload_" + n()) : e()
        }, b.use = function (e, r) {
            return y.preload(function () {
                y.use(e, r, A.cwd + "_use_" + n())
            }), b
        }, y.define.cmd = {}, e.define = y.define, b.Module = y, A.fetchedList = ee, A.cid = n, b.require = function (e) {
            var r = y.get(y.resolve(e));
            return r.status < te.EXECUTING && (r.onload(), r.exec()), r.exports
        };
        var ne = /^(.+?\/)(\?\?)?(seajs\/)+/;
        A.base = (F.match(ne) || ["", F])[1], A.dir = F, A.cwd = k, A.charset = "utf-8", A.preload = function () {
            var e = [],
                r = location.search.replace(/(seajs-\w+)(&|$)/g, "$1=1$2");
            return r += " " + $.cookie, r.replace(/(seajs-\w+)=1/g, function (r, t) {
                e.push(t)
            }), e
        }(), b.config = function (e) {
            for (var r in e) {
                var t = e[r],
                    n = A[r];
                if (n && _(n))
                    for (var i in t) n[i] = t[i];
                else D(n) ? t = n.concat(t) : "base" === r && ("/" !== t.slice(-1) && (t += "/"), t = l(t)), A[r] = t
            }
            return O("config", e), b
        }
    }
}(this);
! function (t) {
    var e = {};
    e.data = {
        set: function (e, n) {
            return t.localStorage.setItem(e, n)
        }, get: function (e) {
            return t.localStorage.getItem(e)
        }, remove: function (e) {
            return t.localStorage.removeItem(e)
        }
    }, e.date = {
        now: function (t) {
            var e = new Date;
            if (!t) return e.getFullYear() + "-" + (e.getMonth() + 1) + "-" + e.getDate();
            switch (t) {
            case "d":
                return e.getDate();
            case "m":
                return e.getMonth() + 1;
            case "y":
                return e.getFullYear()
            }
        }, format: function (t, e) {
            var n = new Date(1e3 * t),
                r = {
                    "M+": n.getMonth() + 1,
                    "d+": n.getDate(),
                    "h+": n.getHours(),
                    "m+": n.getMinutes(),
                    "s+": n.getSeconds(),
                    "q+": Math.floor((n.getMonth() + 3) / 3),
                    S: n.getMilliseconds()
                };
            /(y+)/.test(e) && (e = e.replace(RegExp.$1, (n.getFullYear() + "").substr(4 - RegExp.$1.length)));
            for (var o in r) new RegExp("(" + o + ")").test(e) && (e = e.replace(RegExp.$1, 1 == RegExp.$1.length ? r[o] : ("00" + r[o]).substr(("" + r[o]).length)));
            return e
        }
    }, t.alert = function (t, e, n) {
        return $.alert(t, e, n)
    }, t.confirm = function (t, e, n, r) {
        return $.confirm(t, e, n, r)
    }, t.prompt = function (t, e, n) {
        return $.prompt(t, e, n)
    };
    var n = function (t, e, n, r, o) {
        var a = {};
        return "function" == typeof n ? (r = n, o = r) : a = n, o || (o = function () {
            $.toast("获取数据失败，请检查您的网络设置")
        }), $.ajax({
            url: t,
            data: a,
            type: e,
            dataType: "json",
            success: function () {
                r.apply(this, arguments)
            }, error: o
        })
    };
    n.get = function (t, e, r, o) {
        return n(t, "get", e, r, o)
    }, n.post = function (t, e, r, o) {
        return n(t, "post", e, r, o)
    }, window._ = e, window.$http = n
}(window);