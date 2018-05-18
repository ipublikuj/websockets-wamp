/** @license AutobahnJS - http://autobahn.ws
 *
 * Copyright (C) 2011-2014 Tavendo GmbH.
 * Licensed under the MIT License.
 * See license text at http://www.opensource.org/licenses/mit-license.php
 *
 * AutobahnJS includes code from:
 *
 * when - http://cujojs.com
 *
 * (c) copyright B Cavalier & J Hann
 * Licensed under the MIT License at:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Crypto-JS - http://code.google.com/p/crypto-js/
 *
 * (c) 2009-2012 by Jeff Mott. All rights reserved.
 * Licensed under the New BSD License at:
 * http://code.google.com/p/crypto-js/wiki/License
 *
 * console-normalizer - https://github.com/Zenovations/console-normalizer
 *
 * (c) 2012 by Zenovations.
 * Licensed under the MIT License at:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */

// needed to load when.js in legacy environments
// https://github.com/cujojs/when
if (!window.define) {
    window.define = function (factory) {
        try {
            delete window.define;
        }
        catch (e) {
            window.define = void 0;
        } // IE
        window.when = factory();
    };
    window.define.amd = {};
}

(function (console) {
    /*********************************************************************************************
     * Make sure console exists because IE blows up if it's not open and you attempt to access it
     * Create some dummy functions if we need to, so we don't have to if/else everything
     *********************************************************************************************/
    console || (console = window.console = {
        // all this "a, b, c, d, e" garbage is to make the IDEs happy, since they can't do variable argument lists
        /**
         * @param a
         * @param [b]
         * @param [c]
         * @param [d]
         * @param [e]
         */
        log: function (a, b, c, d, e) {
        },
        /**
         * @param a
         * @param [b]
         * @param [c]
         * @param [d]
         * @param [e]
         */
        info: function (a, b, c, d, e) {
        },
        /**
         * @param a
         * @param [b]
         * @param [c]
         * @param [d]
         * @param [e]
         */
        warn: function (a, b, c, d, e) {
        },
        /**
         * @param a
         * @param [b]
         * @param [c]
         * @param [d]
         * @param [e]
         */
        error: function (a, b, c, d, e) {
        }
    });

    // le sigh, IE, oh IE, how we fight... fix Function.prototype.bind as needed
    if (!Function.prototype.bind) {
        //credits: taken from bind_even_never in this discussion: https://prototype.lighthouseapp.com/projects/8886/tickets/215-optimize-bind-bindaseventlistener#ticket-215-9
        Function.prototype.bind = function (context) {
            var fn = this, args = Array.prototype.slice.call(arguments, 1);
            return function () {
                return fn.apply(context, Array.prototype.concat.apply(args, arguments));
            };
        };
    }

    // IE 9 won't allow us to call console.log.apply (WTF IE!) It also reports typeof(console.log) as 'object' (UNH!)
    // but together, those two errors can be useful in allowing us to fix stuff so it works right
    if (typeof(console.log) === 'object') {
        // Array.forEach doesn't work in IE 8 so don't try that :(
        console.log = Function.prototype.call.bind(console.log, console);
        console.info = Function.prototype.call.bind(console.info, console);
        console.warn = Function.prototype.call.bind(console.warn, console);
        console.error = Function.prototype.call.bind(console.error, console);
    }

    /**
     * Support group and groupEnd functions
     */
    ('group' in console) ||
    (console.group = function (msg) {
        console.info("\n--- " + msg + " ---\n");
    });
    ('groupEnd' in console) ||
    (console.groupEnd = function () {
        console.log("\n");
    });

    /**
     * Support time and timeEnd functions
     */
    ('time' in console) ||
    (function () {
        var trackedTimes = {};
        console.time = function (msg) {
            trackedTimes[msg] = new Date().getTime();
        };
        console.timeEnd = function (msg) {
            var end = new Date().getTime(), time = (msg in trackedTimes) ? end - trackedTimes[msg] : 0;
            console.info(msg + ': ' + time + 'ms')
        }
    }());

})(window.console);

/*
 MIT License (c) copyright 2011-2013 original author or authors */
(function (c) {
    c(function (c) {
        function a(a, b, e, c) {
            return (a instanceof d ? a : h(a)).then(b, e, c)
        }

        function b(a) {
            return new d(a, B.PromiseStatus && B.PromiseStatus())
        }

        function d(a, b) {
            function d(a) {
                if (m) {
                    var c = m;
                    m = w;
                    p(function () {
                        q = e(l, a);
                        b && A(q, b);
                        f(c, q)
                    })
                }
            }

            function c(a) {
                d(new k(a))
            }

            function h(a) {
                if (m) {
                    var b = m;
                    p(function () {
                        f(b, new z(a))
                    })
                }
            }

            var l, q, m = [];
            l = this;
            this._status = b;
            this.inspect = function () {
                return q ? q.inspect() : {
                    state: "pending"
                }
            };
            this._when = function (a, b, e, d, c) {
                function f(h) {
                    h._when(a, b, e, d, c)
                }

                m ? m.push(f) :
                    p(function () {
                        f(q)
                    })
            };
            try {
                a(d, c, h)
            } catch (n) {
                c(n)
            }
        }

        function h(a) {
            return b(function (b) {
                b(a)
            })
        }

        function f(a, b) {
            for (var e = 0; e < a.length; e++) a[e](b)
        }

        function e(a, b) {
            if (b === a) return new k(new TypeError);
            if (b instanceof d) return b;
            try {
                var e = b === Object(b) && b.then;
                return "function" === typeof e ? l(e, b) : new t(b)
            } catch (c) {
                return new k(c)
            }
        }

        function l(a, e) {
            return b(function (b, d) {
                G(a, e, b, d)
            })
        }

        function t(a) {
            this.value = a
        }

        function k(a) {
            this.value = a
        }

        function z(a) {
            this.value = a
        }

        function A(a, b) {
            a.then(function () {
                    b.fulfilled()
                },
                function (a) {
                    b.rejected(a)
                })
        }

        function q(a) {
            return a && "function" === typeof a.then
        }

        function m(e, d, c, f, h) {
            return a(e, function (e) {
                return b(function (b, c, f) {
                    function h(a) {
                        n(a)
                    }

                    function A(a) {
                        k(a)
                    }

                    var l, q, D, m, k, n, t, g;
                    t = e.length >>> 0;
                    l = Math.max(0, Math.min(d, t));
                    D = [];
                    q = t - l + 1;
                    m = [];
                    if (l) {
                        n = function (a) {
                            m.push(a);
                            --q || (k = n = s, c(m))
                        };
                        k = function (a) {
                            D.push(a);
                            --l || (k = n = s, b(D))
                        };
                        for (g = 0; g < t; ++g) g in e && a(e[g], A, h, f)
                    } else b(D)
                }).then(c, f, h)
            })
        }

        function n(a, b, e, d) {
            return u(a, s).then(b, e, d)
        }

        function u(b, e, c) {
            return a(b, function (b) {
                return new d(function (d,
                                       f, h) {
                    function A(b, q) {
                        a(b, e, c).then(function (a) {
                            l[q] = a;
                            --k || d(l)
                        }, f, h)
                    }

                    var l, q, k, m;
                    k = q = b.length >>> 0;
                    l = [];
                    if (k)
                        for (m = 0; m < q; m++) m in b ? A(b[m], m) : --k;
                    else d(l)
                })
            })
        }

        function y(a) {
            return {
                state: "fulfilled",
                value: a
            }
        }

        function x(a) {
            return {
                state: "rejected",
                reason: a
            }
        }

        function p(a) {
            1 === E.push(a) && C(v)
        }

        function v() {
            f(E);
            E = []
        }

        function s(a) {
            return a
        }

        function K(a) {
            "function" === typeof B.reportUnhandled ? B.reportUnhandled() : p(function () {
                throw a;
            });
            throw a;
        }

        a.promise = b;
        a.resolve = h;
        a.reject = function (b) {
            return a(b, function (a) {
                return new k(a)
            })
        };
        a.defer = function () {
            var a, e, d;
            a = {
                promise: w,
                resolve: w,
                reject: w,
                notify: w,
                resolver: {
                    resolve: w,
                    reject: w,
                    notify: w
                }
            };
            a.promise = e = b(function (b, c, f) {
                a.resolve = a.resolver.resolve = function (a) {
                    if (d) return h(a);
                    d = !0;
                    b(a);
                    return e
                };
                a.reject = a.resolver.reject = function (a) {
                    if (d) return h(new k(a));
                    d = !0;
                    c(a);
                    return e
                };
                a.notify = a.resolver.notify = function (a) {
                    f(a);
                    return a
                }
            });
            return a
        };
        a.join = function () {
            return u(arguments, s)
        };
        a.all = n;
        a.map = function (a, b) {
            return u(a, b)
        };
        a.reduce = function (b, e) {
            var d = G(H, arguments, 1);
            return a(b,
                function (b) {
                    var c;
                    c = b.length;
                    d[0] = function (b, d, f) {
                        return a(b, function (b) {
                            return a(d, function (a) {
                                return e(b, a, f, c)
                            })
                        })
                    };
                    return I.apply(b, d)
                })
        };
        a.settle = function (a) {
            return u(a, y, x)
        };
        a.any = function (a, b, e, d) {
            return m(a, 1, function (a) {
                return b ? b(a[0]) : a[0]
            }, e, d)
        };
        a.some = m;
        a.isPromise = q;
        a.isPromiseLike = q;
        r = d.prototype;
        r.then = function (a, b, e) {
            var c = this;
            return new d(function (d, f, h) {
                c._when(d, h, a, b, e)
            }, this._status && this._status.observed())
        };
        r["catch"] = r.otherwise = function (a) {
            return this.then(w, a)
        };
        r["finally"] =
            r.ensure = function (a) {
                function b() {
                    return h(a())
                }

                return "function" === typeof a ? this.then(b, b).yield(this) : this
            };
        r.done = function (a, b) {
            this.then(a, b)["catch"](K)
        };
        r.yield = function (a) {
            return this.then(function () {
                return a
            })
        };
        r.tap = function (a) {
            return this.then(a).yield(this)
        };
        r.spread = function (a) {
            return this.then(function (b) {
                return n(b, function (b) {
                    return a.apply(w, b)
                })
            })
        };
        r.always = function (a, b) {
            return this.then(a, a, b)
        };
        F = Object.create || function (a) {
            function b() {
            }

            b.prototype = a;
            return new b
        };
        t.prototype = F(r);
        t.prototype.inspect = function () {
            return y(this.value)
        };
        t.prototype._when = function (a, b, e) {
            try {
                a("function" === typeof e ? e(this.value) : this.value)
            } catch (d) {
                a(new k(d))
            }
        };
        k.prototype = F(r);
        k.prototype.inspect = function () {
            return x(this.value)
        };
        k.prototype._when = function (a, b, e, d) {
            try {
                a("function" === typeof d ? d(this.value) : this)
            } catch (c) {
                a(new k(c))
            }
        };
        z.prototype = F(r);
        z.prototype._when = function (a, b, e, d, c) {
            try {
                b("function" === typeof c ? c(this.value) : this.value)
            } catch (f) {
                b(f)
            }
        };
        var r, F, I, H, G, C, E, B, J, w;
        E = [];
        B = "undefined" !==
        typeof console ? console : a;
        if ("object" === typeof process && process.nextTick) C = process.nextTick;
        else if (r = "function" === typeof MutationObserver && MutationObserver || "function" === typeof WebKitMutationObserver && WebKitMutationObserver) C = function (a, b, e) {
            var d = a.createElement("div");
            (new b(e)).observe(d, {
                attributes: !0
            });
            return function () {
                d.setAttribute("x", "x")
            }
        }(document, r, v);
        else try {
                C = c("vertx").runOnLoop || c("vertx").runOnContext
            } catch (L) {
                J = setTimeout, C = function (a) {
                    J(a, 0)
                }
            }
        c = Function.prototype;
        r = c.call;
        G = c.bind ?
            r.bind(r) : function (a, b) {
                return a.apply(b, H.call(arguments, 2))
            };
        c = [];
        H = c.slice;
        I = c.reduce || function (a) {
            var b, e, d, c, f;
            f = 0;
            b = Object(this);
            c = b.length >>> 0;
            e = arguments;
            if (1 >= e.length)
                for (; ;) {
                    if (f in b) {
                        d = b[f++];
                        break
                    }
                    if (++f >= c) throw new TypeError;
                } else d = e[1];
            for (; f < c; ++f) f in b && (d = a(d, b[f], f, b));
            return d
        };
        return a
    })
})("function" === typeof define && define.amd ? define : function (c) {
    module.exports = c(require)
});
var CryptoJS = CryptoJS || function (c, g) {
    var a = {},
        b = a.lib = {},
        d = b.Base = function () {
            function a() {
            }

            return {
                extend: function (b) {
                    a.prototype = this;
                    var e = new a;
                    b && e.mixIn(b);
                    e.hasOwnProperty("init") || (e.init = function () {
                        e.$super.init.apply(this, arguments)
                    });
                    e.init.prototype = e;
                    e.$super = this;
                    return e
                },
                create: function () {
                    var a = this.extend();
                    a.init.apply(a, arguments);
                    return a
                },
                init: function () {
                },
                mixIn: function (a) {
                    for (var b in a) a.hasOwnProperty(b) && (this[b] = a[b]);
                    a.hasOwnProperty("toString") && (this.toString = a.toString)
                },
                clone: function () {
                    return this.init.prototype.extend(this)
                }
            }
        }(),
        h = b.WordArray = d.extend({
            init: function (a, b) {
                a = this.words = a || [];
                this.sigBytes = b != g ? b : 4 * a.length
            },
            toString: function (a) {
                return (a || e).stringify(this)
            },
            concat: function (a) {
                var b = this.words,
                    e = a.words,
                    d = this.sigBytes;
                a = a.sigBytes;
                this.clamp();
                if (d % 4)
                    for (var c = 0; c < a; c++) b[d + c >>> 2] |= (e[c >>> 2] >>> 24 - 8 * (c % 4) & 255) << 24 - 8 * ((d + c) % 4);
                else if (65535 < e.length)
                    for (c = 0; c < a; c += 4) b[d + c >>> 2] = e[c >>> 2];
                else b.push.apply(b, e);
                this.sigBytes += a;
                return this
            },
            clamp: function () {
                var a =
                        this.words,
                    b = this.sigBytes;
                a[b >>> 2] &= 4294967295 << 32 - 8 * (b % 4);
                a.length = c.ceil(b / 4)
            },
            clone: function () {
                var a = d.clone.call(this);
                a.words = this.words.slice(0);
                return a
            },
            random: function (a) {
                for (var b = [], e = 0; e < a; e += 4) b.push(4294967296 * c.random() | 0);
                return new h.init(b, a)
            }
        }),
        f = a.enc = {},
        e = f.Hex = {
            stringify: function (a) {
                var b = a.words;
                a = a.sigBytes;
                for (var e = [], d = 0; d < a; d++) {
                    var c = b[d >>> 2] >>> 24 - 8 * (d % 4) & 255;
                    e.push((c >>> 4).toString(16));
                    e.push((c & 15).toString(16))
                }
                return e.join("")
            },
            parse: function (a) {
                for (var b = a.length,
                         e = [], d = 0; d < b; d += 2) e[d >>> 3] |= parseInt(a.substr(d, 2), 16) << 24 - 4 * (d % 8);
                return new h.init(e, b / 2)
            }
        },
        l = f.Latin1 = {
            stringify: function (a) {
                var b = a.words;
                a = a.sigBytes;
                for (var e = [], d = 0; d < a; d++) e.push(String.fromCharCode(b[d >>> 2] >>> 24 - 8 * (d % 4) & 255));
                return e.join("")
            },
            parse: function (a) {
                for (var b = a.length, e = [], d = 0; d < b; d++) e[d >>> 2] |= (a.charCodeAt(d) & 255) << 24 - 8 * (d % 4);
                return new h.init(e, b)
            }
        },
        t = f.Utf8 = {
            stringify: function (a) {
                try {
                    return decodeURIComponent(escape(l.stringify(a)))
                } catch (b) {
                    throw Error("Malformed UTF-8 data");
                }
            },
            parse: function (a) {
                return l.parse(unescape(encodeURIComponent(a)))
            }
        },
        k = b.BufferedBlockAlgorithm = d.extend({
            reset: function () {
                this._data = new h.init;
                this._nDataBytes = 0
            },
            _append: function (a) {
                "string" == typeof a && (a = t.parse(a));
                this._data.concat(a);
                this._nDataBytes += a.sigBytes
            },
            _process: function (a) {
                var b = this._data,
                    e = b.words,
                    d = b.sigBytes,
                    f = this.blockSize,
                    l = d / (4 * f),
                    l = a ? c.ceil(l) : c.max((l | 0) - this._minBufferSize, 0);
                a = l * f;
                d = c.min(4 * a, d);
                if (a) {
                    for (var k = 0; k < a; k += f) this._doProcessBlock(e, k);
                    k = e.splice(0, a);
                    b.sigBytes -=
                        d
                }
                return new h.init(k, d)
            },
            clone: function () {
                var a = d.clone.call(this);
                a._data = this._data.clone();
                return a
            },
            _minBufferSize: 0
        });
    b.Hasher = k.extend({
        cfg: d.extend(),
        init: function (a) {
            this.cfg = this.cfg.extend(a);
            this.reset()
        },
        reset: function () {
            k.reset.call(this);
            this._doReset()
        },
        update: function (a) {
            this._append(a);
            this._process();
            return this
        },
        finalize: function (a) {
            a && this._append(a);
            return this._doFinalize()
        },
        blockSize: 16,
        _createHelper: function (a) {
            return function (b, e) {
                return (new a.init(e)).finalize(b)
            }
        },
        _createHmacHelper: function (a) {
            return function (b,
                             e) {
                return (new z.HMAC.init(a, e)).finalize(b)
            }
        }
    });
    var z = a.algo = {};
    return a
}(Math);
(function () {
    var c = CryptoJS,
        g = c.lib.WordArray;
    c.enc.Base64 = {
        stringify: function (a) {
            var b = a.words,
                d = a.sigBytes,
                c = this._map;
            a.clamp();
            a = [];
            for (var f = 0; f < d; f += 3)
                for (var e = (b[f >>> 2] >>> 24 - 8 * (f % 4) & 255) << 16 | (b[f + 1 >>> 2] >>> 24 - 8 * ((f + 1) % 4) & 255) << 8 | b[f + 2 >>> 2] >>> 24 - 8 * ((f + 2) % 4) & 255, l = 0; 4 > l && f + 0.75 * l < d; l++) a.push(c.charAt(e >>> 6 * (3 - l) & 63));
            if (b = c.charAt(64))
                for (; a.length % 4;) a.push(b);
            return a.join("")
        },
        parse: function (a) {
            var b = a.length,
                d = this._map,
                c = d.charAt(64);
            c && (c = a.indexOf(c), -1 != c && (b = c));
            for (var c = [], f = 0, e = 0; e <
            b; e++)
                if (e % 4) {
                    var l = d.indexOf(a.charAt(e - 1)) << 2 * (e % 4),
                        t = d.indexOf(a.charAt(e)) >>> 6 - 2 * (e % 4);
                    c[f >>> 2] |= (l | t) << 24 - 8 * (f % 4);
                    f++
                }
            return g.create(c, f)
        },
        _map: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/="
    }
})();
(function () {
    var c = CryptoJS,
        g = c.enc.Utf8;
    c.algo.HMAC = c.lib.Base.extend({
        init: function (a, b) {
            a = this._hasher = new a.init;
            "string" == typeof b && (b = g.parse(b));
            var d = a.blockSize,
                c = 4 * d;
            b.sigBytes > c && (b = a.finalize(b));
            b.clamp();
            for (var f = this._oKey = b.clone(), e = this._iKey = b.clone(), l = f.words, t = e.words, k = 0; k < d; k++) l[k] ^= 1549556828, t[k] ^= 909522486;
            f.sigBytes = e.sigBytes = c;
            this.reset()
        },
        reset: function () {
            var a = this._hasher;
            a.reset();
            a.update(this._iKey)
        },
        update: function (a) {
            this._hasher.update(a);
            return this
        },
        finalize: function (a) {
            var b =
                this._hasher;
            a = b.finalize(a);
            b.reset();
            return b.finalize(this._oKey.clone().concat(a))
        }
    })
})();
(function (c) {
    var g = CryptoJS,
        a = g.lib,
        b = a.WordArray,
        d = a.Hasher,
        a = g.algo,
        h = [],
        f = [];
    (function () {
        function a(b) {
            for (var e = c.sqrt(b), d = 2; d <= e; d++)
                if (!(b % d)) return !1;
            return !0
        }

        function b(a) {
            return 4294967296 * (a - (a | 0)) | 0
        }

        for (var e = 2, d = 0; 64 > d;) a(e) && (8 > d && (h[d] = b(c.pow(e, 0.5))), f[d] = b(c.pow(e, 1 / 3)), d++), e++
    })();
    var e = [],
        a = a.SHA256 = d.extend({
            _doReset: function () {
                this._hash = new b.init(h.slice(0))
            },
            _doProcessBlock: function (a, b) {
                for (var d = this._hash.words, c = d[0], h = d[1], g = d[2], m = d[3], n = d[4], u = d[5], y = d[6], x = d[7], p =
                    0; 64 > p; p++) {
                    if (16 > p) e[p] = a[b + p] | 0;
                    else {
                        var v = e[p - 15],
                            s = e[p - 2];
                        e[p] = ((v << 25 | v >>> 7) ^ (v << 14 | v >>> 18) ^ v >>> 3) + e[p - 7] + ((s << 15 | s >>> 17) ^ (s << 13 | s >>> 19) ^ s >>> 10) + e[p - 16]
                    }
                    v = x + ((n << 26 | n >>> 6) ^ (n << 21 | n >>> 11) ^ (n << 7 | n >>> 25)) + (n & u ^ ~n & y) + f[p] + e[p];
                    s = ((c << 30 | c >>> 2) ^ (c << 19 | c >>> 13) ^ (c << 10 | c >>> 22)) + (c & h ^ c & g ^ h & g);
                    x = y;
                    y = u;
                    u = n;
                    n = m + v | 0;
                    m = g;
                    g = h;
                    h = c;
                    c = v + s | 0
                }
                d[0] = d[0] + c | 0;
                d[1] = d[1] + h | 0;
                d[2] = d[2] + g | 0;
                d[3] = d[3] + m | 0;
                d[4] = d[4] + n | 0;
                d[5] = d[5] + u | 0;
                d[6] = d[6] + y | 0;
                d[7] = d[7] + x | 0
            },
            _doFinalize: function () {
                var a = this._data,
                    b = a.words,
                    d = 8 * this._nDataBytes,
                    e = 8 * a.sigBytes;
                b[e >>> 5] |= 128 << 24 - e % 32;
                b[(e + 64 >>> 9 << 4) + 14] = c.floor(d / 4294967296);
                b[(e + 64 >>> 9 << 4) + 15] = d;
                a.sigBytes = 4 * b.length;
                this._process();
                return this._hash
            },
            clone: function () {
                var a = d.clone.call(this);
                a._hash = this._hash.clone();
                return a
            }
        });
    g.SHA256 = d._createHelper(a);
    g.HmacSHA256 = d._createHmacHelper(a)
})(Math);
(function () {
    var c = CryptoJS,
        g = c.lib,
        a = g.Base,
        b = g.WordArray,
        g = c.algo,
        d = g.HMAC,
        h = g.PBKDF2 = a.extend({
            cfg: a.extend({
                keySize: 4,
                hasher: g.SHA1,
                iterations: 1
            }),
            init: function (a) {
                this.cfg = this.cfg.extend(a)
            },
            compute: function (a, e) {
                for (var c = this.cfg, h = d.create(c.hasher, a), g = b.create(), z = b.create([1]), A = g.words, q = z.words, m = c.keySize, c = c.iterations; A.length < m;) {
                    var n = h.update(e).finalize(z);
                    h.reset();
                    for (var u = n.words, y = u.length, x = n, p = 1; p < c; p++) {
                        x = h.finalize(x);
                        h.reset();
                        for (var v = x.words, s = 0; s < y; s++) u[s] ^= v[s]
                    }
                    g.concat(n);
                    q[0]++
                }
                g.sigBytes = 4 * m;
                return g
            }
        });
    c.PBKDF2 = function (a, b, d) {
        return h.create(d).compute(a, b)
    }
})();

/** @license MIT License (c) 2011-2013 Copyright Tavendo GmbH. */

/**
 * AutobahnJS - http://autobahn.ws
 *
 * A lightweight implementation of
 *
 *   WAMP (The WebSocket Application Messaging Protocol) - http://wamp.ws
 *
 * Provides asynchronous RPC/PubSub over WebSocket.
 *
 * Copyright (C) 2011-2014 Tavendo GmbH. Licensed under the MIT License.
 * See license text at http://www.opensource.org/licenses/mit-license.php
 */

/* global console: false, MozWebSocket: false, when: false, CryptoJS: false */

/**
 * @define {string}
 */
var AUTOBAHNJS_VERSION = '0.8.2';
var global = this;

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['when'], function (when) {
            // Also create a global in case some scripts
            // that are loaded still are looking for
            // a global even when an AMD loader is in use.
            return (root.ab = factory(root, when));
        });

    } else if (typeof exports !== 'undefined') {
        // Support Node.js specific `module.exports` (which can be a function)
        if (typeof module != 'undefined' && module.exports) {
            exports = module.exports = factory(root, root.when);
        }
        // But always support CommonJS module 1.1.1 spec (`exports` cannot be a function)
        //exports.ab = exports;

    } else {
        // Browser globals
        root.ab = factory(root, root.when);
    }
}(global, function (root, when) {

    "use strict";

    var ab = {};
    ab._version = AUTOBAHNJS_VERSION;

    /**
     * Fallbacks for browsers lacking
     *
     *    Array.prototype.indexOf
     *    Array.prototype.forEach
     *
     * most notably MSIE8.
     *
     * Source:
     *    https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/indexOf
     *    https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/forEach
     */
    (function () {
        if (!Array.prototype.indexOf) {
            Array.prototype.indexOf = function (searchElement /*, fromIndex */) {
                "use strict";
                if (this === null) {
                    throw new TypeError();
                }
                var t = new Object(this);
                var len = t.length >>> 0;
                if (len === 0) {
                    return -1;
                }
                var n = 0;
                if (arguments.length > 0) {
                    n = Number(arguments[1]);
                    if (n !== n) { // shortcut for verifying if it's NaN
                        n = 0;
                    } else if (n !== 0 && n !== Infinity && n !== -Infinity) {
                        n = (n > 0 || -1) * Math.floor(Math.abs(n));
                    }
                }
                if (n >= len) {
                    return -1;
                }
                var k = n >= 0 ? n : Math.max(len - Math.abs(n), 0);
                for (; k < len; k++) {
                    if (k in t && t[k] === searchElement) {
                        return k;
                    }
                }
                return -1;
            };
        }

        if (!Array.prototype.forEach) {

            Array.prototype.forEach = function (callback, thisArg) {

                var T, k;

                if (this === null) {
                    throw new TypeError(" this is null or not defined");
                }

                // 1. Let O be the result of calling ToObject passing the |this| value as the argument.
                var O = new Object(this);

                // 2. Let lenValue be the result of calling the Get internal method of O with the argument "length".
                // 3. Let len be ToUint32(lenValue).
                var len = O.length >>> 0; // Hack to convert O.length to a UInt32

                // 4. If IsCallable(callback) is false, throw a TypeError exception.
                // See: http://es5.github.com/#x9.11
                if ({}.toString.call(callback) !== "[object Function]") {
                    throw new TypeError(callback + " is not a function");
                }

                // 5. If thisArg was supplied, let T be thisArg; else let T be undefined.
                if (thisArg) {
                    T = thisArg;
                }

                // 6. Let k be 0
                k = 0;

                // 7. Repeat, while k < len
                while (k < len) {

                    var kValue;

                    // a. Let Pk be ToString(k).
                    //   This is implicit for LHS operands of the in operator
                    // b. Let kPresent be the result of calling the HasProperty internal method of O with argument Pk.
                    //   This step can be combined with c
                    // c. If kPresent is true, then
                    if (k in O) {

                        // i. Let kValue be the result of calling the Get internal method of O with argument Pk.
                        kValue = O[k];

                        // ii. Call the Call internal method of callback with T as the this value and
                        // argument list containing kValue, k, and O.
                        callback.call(T, kValue, k, O);
                    }
                    // d. Increase k by 1.
                    k++;
                }
                // 8. return undefined
            };
        }

    })();


    // Helper to slice out browser / version from userAgent
    ab._sliceUserAgent = function (str, delim, delim2) {
        var ver = [];
        var ua = navigator.userAgent;
        var i = ua.indexOf(str);
        var j = ua.indexOf(delim, i);
        if (j < 0) {
            j = ua.length;
        }
        var agent = ua.slice(i, j).split(delim2);
        var v = agent[1].split('.');
        for (var k = 0; k < v.length; ++k) {
            ver.push(parseInt(v[k], 10));
        }
        return {name: agent[0], version: ver};
    };

    /**
     * Detect browser and browser version.
     */
    ab.getBrowser = function () {

        var ua = navigator.userAgent;
        if (ua.indexOf("Chrome") > -1) {
            return ab._sliceUserAgent("Chrome", " ", "/");
        } else if (ua.indexOf("Safari") > -1) {
            return ab._sliceUserAgent("Safari", " ", "/");
        } else if (ua.indexOf("Firefox") > -1) {
            return ab._sliceUserAgent("Firefox", " ", "/");
        } else if (ua.indexOf("MSIE") > -1) {
            return ab._sliceUserAgent("MSIE", ";", " ");
        } else {
            return null;
        }
    };


    ab.getServerUrl = function (wsPath, fallbackUrl) {
        if (root.location.protocol === "file:") {
            if (fallbackUrl) {
                return fallbackUrl;
            } else {
                return "ws://127.0.0.1/ws";
            }
        } else {
            var scheme = root.location.protocol === 'https:' ? 'wss://' : 'ws://';
            var port = root.location.port !== "" ? ':' + root.location.port : '';
            var path = wsPath ? wsPath : 'ws';
            return scheme + root.location.hostname + port + "/" + path;
        }
    };


    // Logging message for unsupported browser.
    ab.browserNotSupportedMessage = "Browser does not support WebSockets (RFC6455)";


    // PBKDF2-base key derivation function for salted WAMP-CRA
    ab.deriveKey = function (secret, extra) {
        if (extra && extra.salt) {
            var salt = extra.salt;
            var keylen = extra.keylen || 32;
            var iterations = extra.iterations || 10000;
            var key = CryptoJS.PBKDF2(secret, salt, {keySize: keylen / 4, iterations: iterations, hasher: CryptoJS.algo.SHA256});
            return key.toString(CryptoJS.enc.Base64);
        } else {
            return secret;
        }
    };


    ab._idchars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    ab._idlen = 16;
    ab._subprotocol = "wamp";

    ab._newid = function () {
        var id = "";
        for (var i = 0; i < ab._idlen; i += 1) {
            id += ab._idchars.charAt(Math.floor(Math.random() * ab._idchars.length));
        }
        return id;
    };

    ab._newidFast = function () {
        return Math.random().toString(36);
    };

    ab.log = function () {
        //console.log.apply(console, !!arguments.length ? arguments : [this]);
        if (arguments.length > 1) {
            console.group("Log Item");
            for (var i = 0; i < arguments.length; i += 1) {
                console.log(arguments[i]);
            }
            console.groupEnd();
        } else {
            console.log(arguments[0]);
        }
    };

    ab._debugrpc = false;
    ab._debugpubsub = false;
    ab._debugws = false;
    ab._debugconnect = false;

    ab.debug = function (debugWamp, debugWs, debugConnect) {
        if ("console" in root) {
            ab._debugrpc = debugWamp;
            ab._debugpubsub = debugWamp;
            ab._debugws = debugWs;
            ab._debugconnect = debugConnect;
        } else {
            throw "browser does not support console object";
        }
    };

    ab.version = function () {
        return ab._version;
    };

    ab.PrefixMap = function () {

        var self = this;
        self._index = {};
        self._rindex = {};
    };

    ab.PrefixMap.prototype.get = function (prefix) {

        var self = this;
        return self._index[prefix];
    };

    ab.PrefixMap.prototype.set = function (prefix, uri) {

        var self = this;
        self._index[prefix] = uri;
        self._rindex[uri] = prefix;
    };

    ab.PrefixMap.prototype.setDefault = function (uri) {

        var self = this;
        self._index[""] = uri;
        self._rindex[uri] = "";
    };

    ab.PrefixMap.prototype.remove = function (prefix) {

        var self = this;
        var uri = self._index[prefix];
        if (uri) {
            delete self._index[prefix];
            delete self._rindex[uri];
        }
    };

    ab.PrefixMap.prototype.resolve = function (curie, pass) {

        var self = this;

        // skip if not a CURIE
        var i = curie.indexOf(":");
        if (i >= 0) {
            var prefix = curie.substring(0, i);
            if (self._index[prefix]) {
                return self._index[prefix] + curie.substring(i + 1);
            }
        }

        // either pass-through or null
        if (pass === true) {
            return curie;
        } else {
            return null;
        }
    };

    ab.PrefixMap.prototype.shrink = function (uri, pass) {

        var self = this;

        for (var i = uri.length; i > 0; i -= 1) {
            var u = uri.substring(0, i);
            var p = self._rindex[u];
            if (p) {
                return p + ":" + uri.substring(i);
            }
        }

        // either pass-through or null
        if (pass === true) {
            return uri;
        } else {
            return null;
        }
    };


    ab._MESSAGE_TYPEID_WELCOME = 0;
    ab._MESSAGE_TYPEID_PREFIX = 1;
    ab._MESSAGE_TYPEID_CALL = 2;
    ab._MESSAGE_TYPEID_CALL_RESULT = 3;
    ab._MESSAGE_TYPEID_CALL_ERROR = 4;
    ab._MESSAGE_TYPEID_SUBSCRIBE = 5;
    ab._MESSAGE_TYPEID_UNSUBSCRIBE = 6;
    ab._MESSAGE_TYPEID_PUBLISH = 7;
    ab._MESSAGE_TYPEID_EVENT = 8;

    ab.CONNECTION_CLOSED = 0;
    ab.CONNECTION_LOST = 1;
    ab.CONNECTION_RETRIES_EXCEEDED = 2;
    ab.CONNECTION_UNREACHABLE = 3;
    ab.CONNECTION_UNSUPPORTED = 4;
    ab.CONNECTION_UNREACHABLE_SCHEDULED_RECONNECT = 5;
    ab.CONNECTION_LOST_SCHEDULED_RECONNECT = 6;

    ab.Deferred = when.defer;
    //ab.Deferred = jQuery.Deferred;

    ab._construct = function (url, protocols) {
        if ("WebSocket" in root) {
            // Chrome, MSIE, newer Firefox
            if (protocols) {
                return new WebSocket(url, protocols);
            } else {
                return new WebSocket(url);
            }
        } else if ("MozWebSocket" in root) {
            // older versions of Firefox prefix the WebSocket object
            if (protocols) {
                return new MozWebSocket(url, protocols);
            } else {
                return new MozWebSocket(url);
            }
        } else {
            return null;
        }
    };

    ab.Session = function (wsuri, onopen, onclose, options) {

        var self = this;

        self._wsuri = wsuri;
        self._options = options;
        self._websocket_onopen = onopen;
        self._websocket_onclose = onclose;

        self._websocket = null;
        self._websocket_connected = false;

        self._session_id = null;
        self._wamp_version = null;
        self._server = null;

        self._calls = {};
        self._subscriptions = {};
        self._prefixes = new ab.PrefixMap();

        self._txcnt = 0;
        self._rxcnt = 0;

        if (self._options && self._options.skipSubprotocolAnnounce) {
            self._websocket = ab._construct(self._wsuri);
        } else {
            self._websocket = ab._construct(self._wsuri, [ab._subprotocol]);
        }

        if (!self._websocket) {
            if (onclose !== undefined) {
                onclose(ab.CONNECTION_UNSUPPORTED);
                return;
            } else {
                throw ab.browserNotSupportedMessage;
            }
        }

        self._websocket.onmessage = function (e) {
            if (ab._debugws) {
                self._rxcnt += 1;
                console.group("WS Receive");
                console.info(self._wsuri + "  [" + self._session_id + "]");
                console.log(self._rxcnt);
                console.log(e.data);
                console.groupEnd();
            }

            var o = JSON.parse(e.data);
            if (o[1] in self._calls) {
                if (o[0] === ab._MESSAGE_TYPEID_CALL_RESULT) {

                    var dr = self._calls[o[1]];
                    var r = o[2];

                    if (ab._debugrpc && dr._ab_callobj !== undefined) {
                        console.group("WAMP Call", dr._ab_callobj[2]);
                        console.timeEnd(dr._ab_tid);
                        console.group("Arguments");
                        for (var i = 3; i < dr._ab_callobj.length; i += 1) {
                            var arg = dr._ab_callobj[i];
                            if (arg !== undefined) {
                                console.log(arg);
                            } else {
                                break;
                            }
                        }
                        console.groupEnd();
                        console.group("Result");
                        console.log(r);
                        console.groupEnd();
                        console.groupEnd();
                    }

                    dr.resolve(r);
                }
                else if (o[0] === ab._MESSAGE_TYPEID_CALL_ERROR) {

                    var de = self._calls[o[1]];
                    var uri_ = o[2];
                    var desc_ = o[3];
                    var detail_ = o[4];

                    if (ab._debugrpc && de._ab_callobj !== undefined) {
                        console.group("WAMP Call", de._ab_callobj[2]);
                        console.timeEnd(de._ab_tid);
                        console.group("Arguments");
                        for (var j = 3; j < de._ab_callobj.length; j += 1) {
                            var arg2 = de._ab_callobj[j];
                            if (arg2 !== undefined) {
                                console.log(arg2);
                            } else {
                                break;
                            }
                        }
                        console.groupEnd();
                        console.group("Error");
                        console.log(uri_);
                        console.log(desc_);
                        if (detail_ !== undefined) {
                            console.log(detail_);
                        }
                        console.groupEnd();
                        console.groupEnd();
                    }

                    if (detail_ !== undefined) {
                        de.reject({uri: uri_, desc: desc_, detail: detail_});
                    } else {
                        de.reject({uri: uri_, desc: desc_});
                    }
                }
                delete self._calls[o[1]];
            }
            else if (o[0] === ab._MESSAGE_TYPEID_EVENT) {
                var subid = self._prefixes.resolve(o[1], true);
                if (subid in self._subscriptions) {

                    var uri2 = o[1];
                    var val = o[2];

                    if (ab._debugpubsub) {
                        console.group("WAMP Event");
                        console.info(self._wsuri + "  [" + self._session_id + "]");
                        console.log(uri2);
                        console.log(val);
                        console.groupEnd();
                    }

                    self._subscriptions[subid].forEach(function (callback) {

                        callback(uri2, val);
                    });
                }
                else {
                    // ignore unsolicited event!
                }
            }
            else if (o[0] === ab._MESSAGE_TYPEID_WELCOME) {
                if (self._session_id === null) {
                    self._session_id = o[1];
                    self._wamp_version = o[2];
                    self._server = o[3];

                    if (ab._debugrpc || ab._debugpubsub) {
                        console.group("WAMP Welcome");
                        console.info(self._wsuri + "  [" + self._session_id + "]");
                        console.log(self._wamp_version);
                        console.log(self._server);
                        console.groupEnd();
                    }

                    // only now that we have received the initial server-to-client
                    // welcome message, fire application onopen() hook
                    if (self._websocket_onopen !== null) {
                        self._websocket_onopen();
                    }
                } else {
                    throw "protocol error (welcome message received more than once)";
                }
            }
        };

        self._websocket.onopen = function (e) {
            // check if we can speak WAMP!
            if (self._websocket.protocol !== ab._subprotocol) {

                if (typeof self._websocket.protocol === 'undefined') {
                    // i.e. Safari does subprotocol negotiation (broken), but then
                    // does NOT set the protocol attribute of the websocket object (broken)
                    //
                    if (ab._debugws) {
                        console.group("WS Warning");
                        console.info(self._wsuri);
                        console.log("WebSocket object has no protocol attribute: WAMP subprotocol check skipped!");
                        console.groupEnd();
                    }
                }
                else if (self._options && self._options.skipSubprotocolCheck) {
                    // WAMP subprotocol check disabled by session option
                    //
                    if (ab._debugws) {
                        console.group("WS Warning");
                        console.info(self._wsuri);
                        console.log("Server does not speak WAMP, but subprotocol check disabled by option!");
                        console.log(self._websocket.protocol);
                        console.groupEnd();
                    }
                } else {
                    // we only speak WAMP .. if the server denied us this, we bail out.
                    //
                    self._websocket.close(1000, "server does not speak WAMP");
                    throw "server does not speak WAMP (but '" + self._websocket.protocol + "' !)";
                }
            }
            if (ab._debugws) {
                console.group("WAMP Connect");
                console.info(self._wsuri);
                console.log(self._websocket.protocol);
                console.groupEnd();
            }
            self._websocket_connected = true;
        };

        self._websocket.onerror = function (e) {
            // FF fires this upon unclean closes
            // Chrome does not fire this
        };

        self._websocket.onclose = function (e) {
            if (ab._debugws) {
                if (self._websocket_connected) {
                    console.log("Autobahn connection to " + self._wsuri + " lost (code " + e.code + ", reason '" + e.reason + "', wasClean " + e.wasClean + ").");
                } else {
                    console.log("Autobahn could not connect to " + self._wsuri + " (code " + e.code + ", reason '" + e.reason + "', wasClean " + e.wasClean + ").");
                }
            }

            // fire app callback
            if (self._websocket_onclose !== undefined) {
                if (self._websocket_connected) {
                    if (e.wasClean) {
                        // connection was closed cleanly (closing HS was performed)
                        self._websocket_onclose(ab.CONNECTION_CLOSED, "WS-" + e.code + ": " + e.reason);
                    } else {
                        // connection was closed uncleanly (lost without closing HS)
                        self._websocket_onclose(ab.CONNECTION_LOST);
                    }
                } else {
                    // connection could not be established in the first place
                    self._websocket_onclose(ab.CONNECTION_UNREACHABLE);
                }
            }

            // cleanup - reconnect requires a new session object!
            self._websocket_connected = false;
            self._wsuri = null;
            self._websocket_onopen = null;
            self._websocket_onclose = null;
            self._websocket = null;
        };

        self.log = function () {
            if (self._options && 'sessionIdent' in self._options) {
                console.group("WAMP Session '" + self._options.sessionIdent + "' [" + self._session_id + "]");
            } else {
                console.group("WAMP Session " + "[" + self._session_id + "]");
            }
            for (var i = 0; i < arguments.length; ++i) {
                console.log(arguments[i]);
            }
            console.groupEnd();
        };
    };


    ab.Session.prototype._send = function (msg) {

        var self = this;

        if (!self._websocket_connected) {
            throw "Autobahn not connected";
        }

        var rmsg;
        switch (true) {
            // In the event that prototype library is in existance run the toJSON method prototype provides
            // else run the standard JSON.stringify
            // this is a very clever problem that causes json to be double-quote-encoded.
            case root.Prototype && typeof top.root.__prototype_deleted === 'undefined':
            case typeof msg.toJSON === 'function':
                rmsg = msg.toJSON();
                break;

            // we could do instead
            // msg.toJSON = function(){return msg};
            // rmsg = JSON.stringify(msg);
            default:
                rmsg = JSON.stringify(msg);
        }

        self._websocket.send(rmsg);
        self._txcnt += 1;

        if (ab._debugws) {
            console.group("WS Send");
            console.info(self._wsuri + "  [" + self._session_id + "]");
            console.log(self._txcnt);
            console.log(rmsg);
            console.groupEnd();
        }
    };


    ab.Session.prototype.close = function () {

        var self = this;

        if (self._websocket_connected) {
            self._websocket.close();
        } else {
            //throw "Autobahn not connected";
        }
    };


    ab.Session.prototype.sessionid = function () {

        var self = this;
        return self._session_id;
    };


    ab.Session.prototype.wsuri = function () {

        var self = this;
        return self._wsuri;
    };


    ab.Session.prototype.shrink = function (uri, pass) {

        var self = this;
        if (pass === undefined) pass = true;
        return self._prefixes.shrink(uri, pass);
    };


    ab.Session.prototype.resolve = function (curie, pass) {

        var self = this;
        if (pass === undefined) pass = true;
        return self._prefixes.resolve(curie, pass);
    };


    ab.Session.prototype.prefix = function (prefix, uri) {

        var self = this;

        /*
           if (self._prefixes.get(prefix) !== undefined) {
              throw "prefix '" + prefix + "' already defined";
           }
        */

        self._prefixes.set(prefix, uri);

        if (ab._debugrpc || ab._debugpubsub) {
            console.group("WAMP Prefix");
            console.info(self._wsuri + "  [" + self._session_id + "]");
            console.log(prefix);
            console.log(uri);
            console.groupEnd();
        }

        var msg = [ab._MESSAGE_TYPEID_PREFIX, prefix, uri];
        self._send(msg);
    };


    ab.Session.prototype.call = function () {

        var self = this;

        var d = new ab.Deferred();
        var callid;
        while (true) {
            callid = ab._newidFast();
            if (!(callid in self._calls)) {
                break;
            }
        }
        self._calls[callid] = d;

        var procuri = self._prefixes.shrink(arguments[0], true);
        var obj = [ab._MESSAGE_TYPEID_CALL, callid, procuri];
        for (var i = 1; i < arguments.length; i += 1) {
            obj.push(arguments[i]);
        }

        self._send(obj);

        if (ab._debugrpc) {
            d._ab_callobj = obj;
            d._ab_tid = self._wsuri + "  [" + self._session_id + "][" + callid + "]";
            console.time(d._ab_tid);
            console.info();
        }

        if (d.promise.then) {
            // whenjs has the actual user promise in an attribute
            return d.promise;
        } else {
            return d;
        }
    };


    ab.Session.prototype.subscribe = function (topicuri, callback) {

        var self = this;

        // subscribe by sending WAMP message when topic not already subscribed
        //
        var rtopicuri = self._prefixes.resolve(topicuri, true);
        if (!(rtopicuri in self._subscriptions)) {

            if (ab._debugpubsub) {
                console.group("WAMP Subscribe");
                console.info(self._wsuri + "  [" + self._session_id + "]");
                console.log(topicuri);
                console.log(callback);
                console.groupEnd();
            }

            var msg = [ab._MESSAGE_TYPEID_SUBSCRIBE, topicuri];
            self._send(msg);

            self._subscriptions[rtopicuri] = [];
        }

        // add callback to event listeners list if not already in list
        //
        var i = self._subscriptions[rtopicuri].indexOf(callback);
        if (i === -1) {
            self._subscriptions[rtopicuri].push(callback);
        }
        else {
            throw "callback " + callback + " already subscribed for topic " + rtopicuri;
        }
    };


    ab.Session.prototype.unsubscribe = function (topicuri, callback) {

        var self = this;

        var rtopicuri = self._prefixes.resolve(topicuri, true);
        if (!(rtopicuri in self._subscriptions)) {
            throw "not subscribed to topic " + rtopicuri;
        }
        else {
            var removed;
            if (callback !== undefined) {
                var idx = self._subscriptions[rtopicuri].indexOf(callback);
                if (idx !== -1) {
                    removed = callback;
                    self._subscriptions[rtopicuri].splice(idx, 1);
                }
                else {
                    throw "no callback " + callback + " subscribed on topic " + rtopicuri;
                }
            }
            else {
                removed = self._subscriptions[rtopicuri].slice();
                self._subscriptions[rtopicuri] = [];
            }

            if (self._subscriptions[rtopicuri].length === 0) {

                delete self._subscriptions[rtopicuri];

                if (ab._debugpubsub) {
                    console.group("WAMP Unsubscribe");
                    console.info(self._wsuri + "  [" + self._session_id + "]");
                    console.log(topicuri);
                    console.log(removed);
                    console.groupEnd();
                }

                var msg = [ab._MESSAGE_TYPEID_UNSUBSCRIBE, topicuri];
                self._send(msg);
            }
        }
    };


    ab.Session.prototype.publish = function () {

        var self = this;

        var topicuri = arguments[0];
        var event = arguments[1];

        var excludeMe = null;
        var exclude = null;
        var eligible = null;

        var msg = null;

        if (arguments.length > 3) {

            if (!(arguments[2] instanceof Array)) {
                throw "invalid argument type(s)";
            }
            if (!(arguments[3] instanceof Array)) {
                throw "invalid argument type(s)";
            }

            exclude = arguments[2];
            eligible = arguments[3];
            msg = [ab._MESSAGE_TYPEID_PUBLISH, topicuri, event, exclude, eligible];

        } else if (arguments.length > 2) {

            if (typeof(arguments[2]) === 'boolean') {

                excludeMe = arguments[2];
                msg = [ab._MESSAGE_TYPEID_PUBLISH, topicuri, event, excludeMe];

            } else if (arguments[2] instanceof Array) {

                exclude = arguments[2];
                msg = [ab._MESSAGE_TYPEID_PUBLISH, topicuri, event, exclude];

            } else {
                throw "invalid argument type(s)";
            }

        } else {

            msg = [ab._MESSAGE_TYPEID_PUBLISH, topicuri, event];
        }

        if (ab._debugpubsub) {
            console.group("WAMP Publish");
            console.info(self._wsuri + "  [" + self._session_id + "]");
            console.log(topicuri);
            console.log(event);

            if (excludeMe !== null) {
                console.log(excludeMe);
            } else {
                if (exclude !== null) {
                    console.log(exclude);
                    if (eligible !== null) {
                        console.log(eligible);
                    }
                }
            }
            console.groupEnd();
        }

        self._send(msg);
    };


    // allow both 2-party and 3-party authentication/authorization
    // for 3-party: let C sign, but let both the B and C party authorize

    ab.Session.prototype.authreq = function (appkey, extra) {
        return this.call("http://api.wamp.ws/procedure#authreq", appkey, extra);
    };

    ab.Session.prototype.authsign = function (challenge, secret) {
        if (!secret) {
            secret = "";
        }

        return CryptoJS.HmacSHA256(challenge, secret).toString(CryptoJS.enc.Base64);
    };

    ab.Session.prototype.auth = function (signature) {
        return this.call("http://api.wamp.ws/procedure#auth", signature);
    };


    ab._connect = function (peer) {

        // establish session to WAMP server
        var sess = new ab.Session(peer.wsuri,

            // fired when session has been opened
            function () {

                peer.connects += 1;
                peer.retryCount = 0;

                // we are connected .. do awesome stuff!
                peer.onConnect(sess);
            },

            // fired when session has been closed
            function (code, reason) {

                var stop = null;

                switch (code) {

                    case ab.CONNECTION_CLOSED:
                        // the session was closed by the app
                        peer.onHangup(code, "Connection was closed properly [" + reason + "]");
                        break;

                    case ab.CONNECTION_UNSUPPORTED:
                        // fatal: we miss our WebSocket object!
                        peer.onHangup(code, "Browser does not support WebSocket.");
                        break;

                    case ab.CONNECTION_UNREACHABLE:

                        peer.retryCount += 1;

                        if (peer.connects === 0) {

                            // the connection could not be established in the first place
                            // which likely means invalid server WS URI or such things
                            peer.onHangup(code, "Connection could not be established.");

                        } else {

                            // the connection was established at least once successfully,
                            // but now lost .. sane thing is to try automatic reconnects
                            if (peer.retryCount <= peer.options.maxRetries) {

                                // notify the app of scheduled reconnect
                                stop = peer.onHangup(ab.CONNECTION_UNREACHABLE_SCHEDULED_RECONNECT,
                                    "Connection unreachable - scheduled reconnect to occur in " + (peer.options.retryDelay / 1000) + " second(s) - attempt " + peer.retryCount + " of " + peer.options.maxRetries + ".",
                                    {
                                        delay: peer.options.retryDelay,
                                        retries: peer.retryCount,
                                        maxretries: peer.options.maxRetries
                                    });

                                if (!stop) {
                                    if (ab._debugconnect) {
                                        console.log("Connection unreachable - retrying (" + peer.retryCount + ") ..");
                                    }
                                    root.setTimeout(function () {
                                        ab._connect(peer);
                                    }, peer.options.retryDelay);
                                } else {
                                    if (ab._debugconnect) {
                                        console.log("Connection unreachable - retrying stopped by app");
                                    }
                                    peer.onHangup(ab.CONNECTION_RETRIES_EXCEEDED, "Number of connection retries exceeded.");
                                }

                            } else {
                                peer.onHangup(ab.CONNECTION_RETRIES_EXCEEDED, "Number of connection retries exceeded.");
                            }
                        }
                        break;

                    case ab.CONNECTION_LOST:

                        peer.retryCount += 1;

                        if (peer.retryCount <= peer.options.maxRetries) {

                            // notify the app of scheduled reconnect
                            stop = peer.onHangup(ab.CONNECTION_LOST_SCHEDULED_RECONNECT,
                                "Connection lost - scheduled " + peer.retryCount + "th reconnect to occur in " + (peer.options.retryDelay / 1000) + " second(s).",
                                {
                                    delay: peer.options.retryDelay,
                                    retries: peer.retryCount,
                                    maxretries: peer.options.maxRetries
                                });

                            if (!stop) {
                                if (ab._debugconnect) {
                                    console.log("Connection lost - retrying (" + peer.retryCount + ") ..");
                                }
                                root.setTimeout(function () {
                                    ab._connect(peer);
                                }, peer.options.retryDelay);
                            } else {
                                if (ab._debugconnect) {
                                    console.log("Connection lost - retrying stopped by app");
                                }
                                peer.onHangup(ab.CONNECTION_RETRIES_EXCEEDED, "Connection lost.");
                            }
                        } else {
                            peer.onHangup(ab.CONNECTION_RETRIES_EXCEEDED, "Connection lost.");
                        }
                        break;

                    default:
                        throw "unhandled close code in ab._connect";
                }
            },

            peer.options // forward options to session class for specific WS/WAMP options
        );
    };


    ab.connect = function (wsuri, onconnect, onhangup, options) {

        var peer = {};
        peer.wsuri = wsuri;

        if (!options) {
            peer.options = {};
        } else {
            peer.options = options;
        }

        if (peer.options.retryDelay === undefined) {
            peer.options.retryDelay = 5000;
        }

        if (peer.options.maxRetries === undefined) {
            peer.options.maxRetries = 10;
        }

        if (peer.options.skipSubprotocolCheck === undefined) {
            peer.options.skipSubprotocolCheck = false;
        }

        if (peer.options.skipSubprotocolAnnounce === undefined) {
            peer.options.skipSubprotocolAnnounce = false;
        }

        if (!onconnect) {
            throw "onConnect handler required!";
        } else {
            peer.onConnect = onconnect;
        }

        if (!onhangup) {
            peer.onHangup = function (code, reason, detail) {
                if (ab._debugconnect) {
                    console.log(code, reason, detail);
                }
            };
        } else {
            peer.onHangup = onhangup;
        }

        peer.connects = 0; // total number of successful connects
        peer.retryCount = 0; // number of retries since last successful connect

        ab._connect(peer);
    };


    ab.launch = function (appConfig, onOpen, onClose) {

        function Rpc(session, uri) {
            return function () {
                var args = [uri];
                for (var j = 0; j < arguments.length; ++j) {
                    args.push(arguments[j]);
                }
                //arguments.unshift(uri);
                return ab.Session.prototype.call.apply(session, args);
            };
        }

        function createApi(session, perms) {
            session.api = {};
            for (var i = 0; i < perms.rpc.length; ++i) {
                var uri = perms.rpc[i].uri;

                var _method = uri.split("#")[1];
                var _class = uri.split("#")[0].split("/");
                _class = _class[_class.length - 1];

                if (!(_class in session.api)) {
                    session.api[_class] = {};
                }

                session.api[_class][_method] = new Rpc(session, uri);
            }
        }

        ab.connect(appConfig.wsuri,

            // connection established handler
            function (session) {
                if (!appConfig.appkey || appConfig.appkey === "") {
                    // Authenticate as anonymous ..
                    session.authreq().then(function () {
                        session.auth().then(function (permissions) {
                            //createApi(session, permissions);
                            if (onOpen) {
                                onOpen(session);
                            } else if (ab._debugconnect) {
                                session.log('Session opened.');
                            }
                        }, session.log);
                    }, session.log);
                } else {
                    // Authenticate as appkey ..
                    session.authreq(appConfig.appkey, appConfig.appextra).then(function (challenge) {

                        var signature = null;

                        if (typeof(appConfig.appsecret) === 'function') {
                            signature = appConfig.appsecret(challenge);
                        } else {
                            // derive secret if salted WAMP-CRA
                            var secret = ab.deriveKey(appConfig.appsecret, JSON.parse(challenge).authextra);

                            // direct sign
                            signature = session.authsign(challenge, secret);
                        }

                        session.auth(signature).then(function (permissions) {
                            //createApi(session, permissions);
                            if (onOpen) {
                                onOpen(session);
                            } else if (ab._debugconnect) {
                                session.log('Session opened.');
                            }
                        }, session.log);
                    }, session.log);
                }
            },

            // connection lost handler
            function (code, reason, detail) {
                if (onClose) {
                    onClose(code, reason, detail);
                } else if (ab._debugconnect) {
                    ab.log('Session closed.', code, reason, detail);
                }
            },

            // WAMP session config
            appConfig.sessionConfig
        );
    };

    return ab;
}));

ab._UA_FIREFOX = new RegExp(".*Firefox/([0-9+]*).*")
ab._UA_CHROME = new RegExp(".*Chrome/([0-9+]*).*")
ab._UA_CHROMEFRAME = new RegExp(".*chromeframe/([0-9]*).*")
ab._UA_WEBKIT = new RegExp(".*AppleWebKit/([0-9+\.]*)\w*.*")
ab._UA_WEBOS = new RegExp(".*webOS/([0-9+\.]*)\w*.*")

ab._matchRegex = function (s, r) {
    var m = r.exec(s)
    if (m) return m[1]
    return m
};

ab.lookupWsSupport = function () {
    var ua = navigator.userAgent;

    // Internet Explorer
    if (ua.indexOf("MSIE") > -1) {
        if (ua.indexOf("MSIE 10") > -1)
            return [true, true, true]
        if (ua.indexOf("chromeframe") > -1) {
            var v = parseInt(ab._matchRegex(ua, ab._UA_CHROMEFRAME))
            if (v >= 14)
                return [true, false, true]
            return [false, false, false]
        }
        if (ua.indexOf("MSIE 8") > -1 || ua.indexOf("MSIE 9") > -1)
            return [true, true, true]
        return [false, false, false]
    }

    // Firefox
    else if (ua.indexOf("Firefox") > -1) {
        var v = parseInt(ab._matchRegex(ua, ab._UA_FIREFOX))
        if (v) {
            if (v >= 7)
                return [true, false, true]
            if (v >= 3)
                return [true, true, true]
            return [false, false, true]
        }
        return [false, false, true]

    }

    // Safari
    else if (ua.indexOf("Safari") > -1 && ua.indexOf("Chrome") == -1) {
        var v = ab._matchRegex(ua, ab._UA_WEBKIT)
        if (v) {
            if (ua.indexOf("Windows") > -1 && v == "534+") // Not sure about this test ~RMH
                return [true, false, true]
            if (ua.indexOf("Macintosh") > -1) {
                v = v.replace("+", "").split(".")
                if ((parseInt(v[0]) == 535 && parseInt(v[1]) >= 24) || parseInt(v[0]) > 535)
                    return [true, false, true]
            }
            if (ua.indexOf("webOS") > -1) {
                v = ab._matchRegex(ua, ab._UA_WEBOS).split(".")
                if (parseInt(v[0]) == 2)
                    return [false, true, true]
                return [false, false, false]
            }
            return [true, true, true]
        }
        return [false, false, false]
    }

    // Chrome
    else if (ua.indexOf("Chrome") > -1) {
        var v = parseInt(ab._matchRegex(ua, ab._UA_CHROME))
        if (v) {
            if (v >= 14)
                return [true, false, true]
            if (v >= 4)
                return [true, true, true]
            return [false, false, true]
        }
        return [false, false, false]
    }

    // Android
    else if (ua.indexOf("Android") > -1) {
        // Firefox Mobile
        if (ua.indexOf("Firefox") > -1)
            return [true, false, true]
        // Chrome for Android
        else if (ua.indexOf("CrMo") > -1)
            return [true, false, true]
        // Opera Mobile
        else if (ua.indexOf("Opera") > -1)
            return [false, false, true]
        // Android Browser
        else if (ua.indexOf("CrMo") > -1)
            return [true, true, true]
        return [false, false, false]
    }

    // iOS
    else if (ua.indexOf("iPhone") > -1 || ua.indexOf("iPad") > -1 || ua.indexOf("iPod") > -1)
        return [false, false, true]

    // Unidentified
    return [false, false, false]
};