/*
Copyright (c) 2008-2011, www.redips.net All rights reserved.
Code licensed under the BSD License: http://www.redips.net/license/
http://www.redips.net/javascript/drag-and-drop-table-content/
Version 5.1.0
Mar 06, 2015.
*/
var REDIPS = REDIPS || {};
REDIPS.drag = function() {
    var u, F, M, Ea, Qa, Ra, ga, ha, ma, Fa, Ga, Y, na, Ha, T, oa, ba, Ia, G, y, N, pa, qa, ra, Ja, sa, Ka, I, B, La, ia, ja, ta, Sa, Ta, Ma, ua, va, wa, ka, Na, Ua, xa, Va, r = null,
        J = 0,
        K = 0,
        ya = null,
        za = null,
        O = [],
        w = null,
        P = 0,
        Q = 0,
        R = 0,
        S = 0,
        U = 0,
        V = 0,
        ca, f = [],
        da, Aa, t, W = [],
        q = [],
        C = null,
        H = null,
        Z = 0,
        aa = 0,
        Wa = 0,
        Xa = 0,
        la = !1,
        Oa = !1,
        ea = !1,
        Ba = [],
        Ca, l = null,
        x = null,
        D = null,
        h = null,
        z = null,
        L = null,
        m = null,
        E = null,
        X = null,
        k = !1,
        p = !1,
        v = "cell",
        Da = {
            div: [],
            cname: "redips-only",
            other: "deny"
        },
        Ya = {
            action: "deny",
            cname: "redips-mark",
            exception: []
        },
        n = {},
        Za = {
            keyDiv: !1,
            keyRow: !1,
            sendBack: !1,
            drop: !1
        };
    M = function() {
        return !1
    };
		
    u = function(a) {
        var b, c, d, e, g;
        f.length = 0;
        e = void 0 === a ? C.getElementsByTagName("table") : document.querySelectorAll(a);
        for (b = a = 0; a < e.length; a++)
            if (!("redips_clone" === e[a].parentNode.id || -1 < e[a].className.indexOf("redips-nolayout"))) {
                c = e[a].parentNode;
                d = 0;
                do "TD" === c.nodeName && d++, c = c.parentNode; while (c && c !== C);
                f[b] = e[a];
                f[b].redips || (f[b].redips = {});
                f[b].redips.container = C;
                f[b].redips.nestedLevel = d;
                f[b].redips.idx = b;
                Ba[b] = 0;
                d = f[b].getElementsByTagName("td");
                c =
                    0;
                for (g = !1; c < d.length; c++)
                    if (1 < d[c].rowSpan) {
                        g = !0;
                        break
                    }
                f[b].redips.rowspan = g;
                b++
            }
        a = 0;
        for (e = da = 1; a < f.length; a++)
            if (0 === f[a].redips.nestedLevel) {
                f[a].redips.nestedGroup = e;
                f[a].redips.sort = 100 * da;
                c = f[a].getElementsByTagName("table");
                for (b = 0; b < c.length; b++) - 1 < c[b].className.indexOf("redips-nolayout") || (c[b].redips.nestedGroup = e, c[b].redips.sort = 100 * da + c[b].redips.nestedLevel);
                e++;
                da++
            }
    };
    Ea = function(a) {
        var b = a || window.event,
            c, d;
        if (!0 === this.redips.animated) return !0;
        b.cancelBubble = !0;
        b.stopPropagation &&
            b.stopPropagation();
        Oa = b.shiftKey;
        a = b.which ? b.which : b.button;
        if (Ka(b) || !b.touches && 1 !== a) return !0;
        if (window.getSelection) window.getSelection().removeAllRanges();
        else if (document.selection && "Text" === document.selection.type) try {
            document.selection.empty()
        } catch (e) {}
        b.touches ? (a = Z = b.touches[0].clientX, d = aa = b.touches[0].clientY) : (a = Z = b.clientX, d = aa = b.clientY);
        Wa = a;
        Xa = d;
        la = !1;
        REDIPS.drag.objOld = p = k || this;
        REDIPS.drag.obj = k = this;
        ea = -1 < k.className.indexOf("redips-clone");
        REDIPS.drag.tableSort && Ra(k);
        C !== k.redips.container &&
            (C = k.redips.container, u()); - 1 === k.className.indexOf("row") ? REDIPS.drag.mode = v = "cell" : (REDIPS.drag.mode = v = "row", REDIPS.drag.obj = k = ka(k));
        y();
        ea || "cell" !== v || (k.style.zIndex = 999);
        l = h = m = null;
        T();
        D = x = l;
        L = z = h;
        X = E = m;
        REDIPS.drag.td.source = n.source = B("TD", k);
        REDIPS.drag.td.current = n.current = n.source;
        REDIPS.drag.td.previous = n.previous = n.source;
        "cell" === v ? REDIPS.drag.event.clicked(n.current) : REDIPS.drag.event.rowClicked(n.current);
        if (null === l || null === h || null === m)
            if (T(), D = x = l, L = z = h, X = E = m, null === l || null === h ||
                null === m) return !0;
        Aa = t = !1;
        REDIPS.event.add(document, "mousemove", ha);
        REDIPS.event.add(document, "touchmove", ha);
        REDIPS.event.add(document, "mouseup", ga);
        REDIPS.event.add(document, "touchend", ga);
        k.setCapture && k.setCapture();
        null !== l && null !== h && null !== m && (ca = Ia(l, h, m));
        c = I(f[D], "position");
        "fixed" !== c && (c = I(f[D].parentNode, "position"));
        c = G(k, c);
        r = [d - c[0], c[1] - a, c[2] - d, a - c[3]];
        C.onselectstart = function(a) {
            b = a || window.event;
            if (!Ka(b)) return b.shiftKey && document.selection.clear(), !1
        };
        return !1
    };
    Qa = function(a) {
        REDIPS.drag.event.dblClicked()
    };
    Ra = function(a) {
        var b;
        b = B("TABLE", a).redips.nestedGroup;
        for (a = 0; a < f.length; a++) f[a].redips.nestedGroup === b && (f[a].redips.sort = 100 * da + f[a].redips.nestedLevel);
        f.sort(function(a, b) {
            return b.redips.sort - a.redips.sort
        });
        da++
    };
    ka = function(a, b) {
        var c, d, e, g, f, A;
        if ("DIV" === a.nodeName) return g = a, a = B("TR", a), void 0 === a.redips && (a.redips = {}), a.redips.div = g, a;
        d = a;
        void 0 === d.redips && (d.redips = {});
        a = B("TABLE", a);
        ea && t && (g = d.redips.div, g.className = xa(g.className.replace("redips-clone", "")));
        c = a.cloneNode(!0);
        ea &&
            t && (g.className += " redips-clone");
        e = c.rows.length - 1;
        g = "animated" === b ? 0 === e : !0;
        for (f = e; 0 <= f; f--)
            if (f !== d.rowIndex) {
                if (!0 === g && void 0 === b)
                    for (e = c.rows[f], A = 0; A < e.cells.length; A++)
                        if (-1 < e.cells[A].className.indexOf("redips-rowhandler")) {
                            g = !1;
                            break
                        }
                c.deleteRow(f)
            }
        t || (d.redips.emptyRow = g);
        c.redips = {};
        c.redips.container = a.redips.container;
        c.redips.sourceRow = d;
        Ua(d, c.rows[0]);
        Ja(d, c.rows[0]);
        document.getElementById("redips_clone").appendChild(c);
        d = G(d, "fixed");
        c.style.position = "fixed";
        c.style.top = d[0] + "px";
        c.style.left = d[3] + "px";
        c.style.width = d[1] - d[3] + "px";
        return c
    };
    Na = function(a, b, c) {
        var d = !1,
            e, g, Pa, A, h, m, fa, q;
        q = function(a) {
            var b;
            void 0 !== a.redips && a.redips.emptyRow ? wa(a, "empty", REDIPS.drag.style.rowEmptyColor) : (b = B("TABLE", a), b.deleteRow(a.rowIndex))
        };
        void 0 === c ? c = k : d = !0;
        e = c.redips.sourceRow;
        g = e.rowIndex;
        Pa = B("TABLE", e);
        A = e.parentNode;
        a = f[a];
        b > a.rows.length - 1 && (b = a.rows.length - 1);
        h = a.rows[b];
        m = b;
        fa = h.parentNode;
        b = c.getElementsByTagName("tr")[0];
        c.parentNode.removeChild(c);
        !1 !== REDIPS.drag.event.rowDroppedBefore(Pa,
            g) && (!d && -1 < n.target.className.indexOf(REDIPS.drag.trash.className) ? t ? REDIPS.drag.event.rowDeleted() : REDIPS.drag.trash.questionRow ? confirm(REDIPS.drag.trash.questionRow) ? (q(e), REDIPS.drag.event.rowDeleted()) : (delete p.redips.emptyRow, REDIPS.drag.event.rowUndeleted()) : (q(e), REDIPS.drag.event.rowDeleted()) : (m < a.rows.length ? l === D ? g > m ? fa.insertBefore(b, h) : fa.insertBefore(b, h.nextSibling) : "after" === REDIPS.drag.rowDropMode ? fa.insertBefore(b, h.nextSibling) : fa.insertBefore(b, h) : (fa.appendChild(b), h = a.rows[0]),
            h && h.redips && h.redips.emptyRow ? a.deleteRow(h.rowIndex) : "overwrite" === REDIPS.drag.rowDropMode ? q(h) : "switch" !== REDIPS.drag.rowDropMode || t || (A.insertBefore(h, e), void 0 !== e.redips && delete e.redips.emptyRow), !d && t || q(e), delete b.redips.emptyRow, d || REDIPS.drag.event.rowDropped(b, Pa, g)), 0 < b.getElementsByTagName("table").length && u())
    };
    Ua = function(a, b) {
        var c, d, e, g = [],
            f = [];
        g[0] = a.getElementsByTagName("input");
        g[1] = a.getElementsByTagName("textarea");
        g[2] = a.getElementsByTagName("select");
        f[0] = b.getElementsByTagName("input");
        f[1] = b.getElementsByTagName("textarea");
        f[2] = b.getElementsByTagName("select");
        for (c = 0; c < g.length; c++)
            for (d = 0; d < g[c].length; d++) switch (e = g[c][d].type, e) {
                case "text":
                case "textarea":
                case "password":
                    f[c][d].value = g[c][d].value;
                    break;
                case "radio":
                case "checkbox":
                    f[c][d].checked = g[c][d].checked;
                    break;
                case "select-one":
                    f[c][d].selectedIndex = g[c][d].selectedIndex;
                    break;
                case "select-multiple":
                    for (e = 0; e < g[c][d].options.length; e++) f[c][d].options[e].selected = g[c][d].options[e].selected
            }
    };
    ga = function(a) {
        var b =
            a || window.event,
            c, d, e;
        a = b.clientX;
        e = b.clientY;
        U = V = 0;
        k.releaseCapture && k.releaseCapture();
        REDIPS.event.remove(document, "mousemove", ha);
        REDIPS.event.remove(document, "touchmove", ha);
        REDIPS.event.remove(document, "mouseup", ga);
        REDIPS.event.remove(document, "touchend", ga);
        C.onselectstart = null;
        Ga(k);
        ya = document.documentElement.scrollWidth;
        za = document.documentElement.scrollHeight;
        U = V = 0;
        if (!t || "cell" !== v || null !== l && null !== h && null !== m)
            if (null === l || null === h || null === m) REDIPS.drag.event.notMoved();
            else {
                l < f.length ?
                    (b = f[l], REDIPS.drag.td.target = n.target = b.rows[h].cells[m], ba(l, h, m, ca), c = l, d = h) : null === x || null === z || null === E ? (b = f[D], REDIPS.drag.td.target = n.target = b.rows[L].cells[X], ba(D, L, X, ca), c = D, d = L) : (b = f[x], REDIPS.drag.td.target = n.target = b.rows[z].cells[E], ba(x, z, E, ca), c = x, d = z);
                if ("row" === v)
                    if (Aa)
                        if (D === c && L === d) {
                            b = k.getElementsByTagName("tr")[0];
                            p.style.backgroundColor = b.style.backgroundColor;
                            for (a = 0; a < b.cells.length; a++) p.cells[a].style.backgroundColor = b.cells[a].style.backgroundColor;
                            k.parentNode.removeChild(k);
                            delete p.redips.emptyRow;
                            t ? REDIPS.drag.event.rowNotCloned() : REDIPS.drag.event.rowDroppedSource(n.target)
                        } else Na(c, d);
                else REDIPS.drag.event.rowNotMoved();
                else if (t || la)
                    if (t && D === l && L === h && X === m) k.parentNode.removeChild(k), --W[p.id], REDIPS.drag.event.notCloned();
                    else if (t && !1 === REDIPS.drag.clone.drop && (a < b.redips.offset[3] || a > b.redips.offset[1] || e < b.redips.offset[0] || e > b.redips.offset[2])) k.parentNode.removeChild(k), --W[p.id], REDIPS.drag.event.notCloned();
                else if (-1 < n.target.className.indexOf(REDIPS.drag.trash.className)) k.parentNode.removeChild(k),
                    REDIPS.drag.trash.question ? setTimeout(function() {
                        confirm(REDIPS.drag.trash.question) ? Fa() : (t || (f[D].rows[L].cells[X].appendChild(k), y()), REDIPS.drag.event.undeleted())
                    }, 20) : Fa();
                else if ("switch" === REDIPS.drag.dropMode)
                    if (a = REDIPS.drag.event.droppedBefore(n.target), !1 === a) ma(!1);
                    else {
                        k.parentNode.removeChild(k);
                        b = n.target.getElementsByTagName("div");
                        c = b.length;
                        for (a = 0; a < c; a++) void 0 !== b[0] && (REDIPS.drag.objOld = p = b[0], n.source.appendChild(p), Y(p));
                        ma();
                        c && REDIPS.drag.event.switched()
                    } else "overwrite" ===
                    REDIPS.drag.dropMode ? (a = REDIPS.drag.event.droppedBefore(n.target), !1 !== a && ja(n.target)) : a = REDIPS.drag.event.droppedBefore(n.target), ma(a);
                else REDIPS.drag.event.notMoved();
                "cell" === v && 0 < k.getElementsByTagName("table").length && u();
                y();
                REDIPS.drag.event.finish()
            } else k.parentNode.removeChild(k), --W[p.id], REDIPS.drag.event.notCloned();
        x = z = E = null
    };
    ma = function(a) {
        var b = null,
            c;
        if (!1 !== a) {
            if (!0 === Za.sendBack) {
                a = n.target.getElementsByTagName("DIV");
                for (c = 0; c < a.length; c++)
                    if (k !== a[c] && 0 === k.id.indexOf(a[c].id)) {
                        b =
                            a[c];
                        break
                    }
                if (b) {
                    sa(b, 1);
                    k.parentNode.removeChild(k);
                    return
                }
            }
            "shift" !== REDIPS.drag.dropMode || !Va(n.target) && "always" !== REDIPS.drag.shift.after || ta(n.source, n.target);
            "top" === REDIPS.drag.multipleDrop && n.target.hasChildNodes() ? n.target.insertBefore(k, n.target.firstChild) : n.target.appendChild(k);
            Y(k);
            REDIPS.drag.event.dropped(n.target);
            t && (REDIPS.drag.event.clonedDropped(n.target), sa(p, -1))
        } else t && k.parentNode && k.parentNode.removeChild(k)
    };
    Y = function(a, b) {
        !1 === b ? (a.onmousedown = null, a.ontouchstart = null,
            a.ondblclick = null) : (a.onmousedown = Ea, a.ontouchstart = Ea, a.ondblclick = Qa)
    };
    Ga = function(a) {
        a.style.top = "";
        a.style.left = "";
        a.style.position = "";
        a.style.zIndex = ""
    };
    Fa = function() {
        var a;
        t && sa(p, -1);
        if ("shift" === REDIPS.drag.dropMode && ("delete" === REDIPS.drag.shift.after || "always" === REDIPS.drag.shift.after)) {
            switch (REDIPS.drag.shift.mode) {
                case "vertical2":
                    a = "lastInColumn";
                    break;
                case "horizontal2":
                    a = "lastInRow";
                    break;
                default:
                    a = "last"
            }
            ta(n.source, La(a, n.source)[2])
        }
        REDIPS.drag.event.deleted(t)
    };
    ha = function(a) {
        a =
            a || window.event;
        var b = REDIPS.drag.scroll.bound,
            c, d, e, g;
        a.touches ? (d = Z = a.touches[0].clientX, e = aa = a.touches[0].clientY) : (d = Z = a.clientX, e = aa = a.clientY);
        c = Math.abs(Wa - d);
        g = Math.abs(Xa - e);
        if (!Aa) {
            if ("cell" === v && (ea || !0 === REDIPS.drag.clone.keyDiv && Oa)) REDIPS.drag.objOld = p = k, REDIPS.drag.obj = k = ra(k, !0), t = !0, REDIPS.drag.event.cloned();
            else {
                if ("row" === v) {
                    if (ea || !0 === REDIPS.drag.clone.keyRow && Oa) t = !0;
                    REDIPS.drag.objOld = p = k;
                    REDIPS.drag.obj = k = ka(k);
                    k.style.zIndex = 999
                }
                k.setCapture && k.setCapture();
                k.style.position =
                    "fixed";
                y();
                T();
                "row" === v && (t ? REDIPS.drag.event.rowCloned() : REDIPS.drag.event.rowMoved())
            }
            oa();
            d > J - r[1] && (k.style.left = J - (r[1] + r[3]) + "px");
            e > K - r[2] && (k.style.top = K - (r[0] + r[2]) + "px")
        }
        Aa = !0;
        "cell" === v && (7 < c || 7 < g) && !la && (la = !0, oa(), REDIPS.drag.event.moved(t));
        d > r[3] && d < J - r[1] && (k.style.left = d - r[3] + "px");
        e > r[0] && e < K - r[2] && (k.style.top = e - r[0] + "px");
        d < H[1] && d > H[3] && e < H[2] && e > H[0] && 0 === U && 0 === V && (q.containTable || d < q[3] || d > q[1] || e < q[0] || e > q[2]) && (T(), na());
        if (REDIPS.drag.scroll.enable)
            for (P = b - (J / 2 > d ? d - r[3] :
                    J - d - r[1]), 0 < P ? (P > b && (P = b), c = N()[0], P *= d < J / 2 ? -1 : 1, 0 > P && 0 >= c || 0 < P && c >= ya - J || 0 !== U++ || (REDIPS.event.remove(window, "scroll", y), pa(window))) : P = 0, Q = b - (K / 2 > e ? e - r[0] : K - e - r[2]), 0 < Q ? (Q > b && (Q = b), c = N()[1], Q *= e < K / 2 ? -1 : 1, 0 > Q && 0 >= c || 0 < Q && c >= za - K || 0 !== V++ || (REDIPS.event.remove(window, "scroll", y), qa(window))) : Q = 0, g = 0; g < O.length; g++)
                if (c = O[g], c.autoscroll && d < c.offset[1] && d > c.offset[3] && e < c.offset[2] && e > c.offset[0]) {
                    R = b - (c.midstX > d ? d - r[3] - c.offset[3] : c.offset[1] - d - r[1]);
                    0 < R ? (R > b && (R = b), R *= d < c.midstX ? -1 : 1, 0 === U++ && (REDIPS.event.remove(c.div,
                        "scroll", y), pa(c.div))) : R = 0;
                    S = b - (c.midstY > e ? e - r[0] - c.offset[0] : c.offset[2] - e - r[2]);
                    0 < S ? (S > b && (S = b), S *= e < c.midstY ? -1 : 1, 0 === V++ && (REDIPS.event.remove(c.div, "scroll", y), qa(c.div))) : S = 0;
                    break
                } else R = S = 0;
        a.cancelBubble = !0;
        a.stopPropagation && a.stopPropagation()
    };
    na = function() {
        l < f.length && (l !== x || h !== z || m !== E) && (null !== x && null !== z && null !== E && (ba(x, z, E, ca), REDIPS.drag.td.previous = n.previous = f[x].rows[z].cells[E], REDIPS.drag.td.current = n.current = f[l].rows[h].cells[m], "switching" === REDIPS.drag.dropMode && "cell" ===
            v && (ia(n.current, n.previous), y(), T()), "cell" === v ? REDIPS.drag.event.changed(n.current) : "row" !== v || l === x && h === z || REDIPS.drag.event.rowChanged(n.current)), oa())
    };
    Ha = function() {
        "number" === typeof window.innerWidth ? (J = window.innerWidth, K = window.innerHeight) : document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight) ? (J = document.documentElement.clientWidth, K = document.documentElement.clientHeight) : document.body && (document.body.clientWidth || document.body.clientHeight) &&
            (J = document.body.clientWidth, K = document.body.clientHeight);
        ya = document.documentElement.scrollWidth;
        za = document.documentElement.scrollHeight;
        y()
    };
    T = function() {
        var a, b, c, d, e, g;
        c = [];
        a = function() {
            null !== x && null !== z && null !== E && (l = x, h = z, m = E)
        };
        b = Z;
        g = aa;
        for (l = 0; l < f.length; l++)
            if (!1 !== f[l].redips.enabled && (c[0] = f[l].redips.offset[0], c[1] = f[l].redips.offset[1], c[2] = f[l].redips.offset[2], c[3] = f[l].redips.offset[3], void 0 !== f[l].sca && (c[0] = c[0] > f[l].sca.offset[0] ? c[0] : f[l].sca.offset[0], c[1] = c[1] < f[l].sca.offset[1] ?
                    c[1] : f[l].sca.offset[1], c[2] = c[2] < f[l].sca.offset[2] ? c[2] : f[l].sca.offset[2], c[3] = c[3] > f[l].sca.offset[3] ? c[3] : f[l].sca.offset[3]), c[3] < b && b < c[1] && c[0] < g && g < c[2])) {
                c = f[l].redips.row_offset;
                for (h = 0; h < c.length - 1; h++)
                    if (void 0 !== c[h]) {
                        q[0] = c[h][0];
                        if (void 0 !== c[h + 1]) q[2] = c[h + 1][0];
                        else
                            for (d = h + 2; d < c.length; d++)
                                if (void 0 !== c[d]) {
                                    q[2] = c[d][0];
                                    break
                                } if (g <= q[2]) break
                    }
                d = h;
                h === c.length - 1 && (q[0] = c[h][0], q[2] = f[l].redips.offset[2]);
                do
                    for (m = e = f[l].rows[h].cells.length - 1; 0 <= m && !(q[3] = c[h][3] + f[l].rows[h].cells[m].offsetLeft,
                            q[1] = q[3] + f[l].rows[h].cells[m].offsetWidth, q[3] <= b && b <= q[1]); m--); while (f[l].redips.rowspan && -1 === m && 0 < h--);
                0 > h || 0 > m ? a() : h !== d && (q[0] = c[h][0], q[2] = q[0] + f[l].rows[h].cells[m].offsetHeight, (g < q[0] || g > q[2]) && a());
                b = f[l].rows[h].cells[m];
                q.containTable = 0 < b.childNodes.length && 0 < b.getElementsByTagName("table").length;
                if (-1 === b.className.indexOf(REDIPS.drag.trash.className))
                    if (g = -1 < b.className.indexOf(REDIPS.drag.only.cname), !0 === g) {
                        if (-1 === b.className.indexOf(Da.div[k.id])) {
                            a();
                            break
                        }
                    } else if (void 0 !==
                    Da.div[k.id] && "deny" === Da.other) {
                    a();
                    break
                } else if (g = -1 < b.className.indexOf(REDIPS.drag.mark.cname), (!0 === g && "deny" === REDIPS.drag.mark.action || !1 === g && "allow" === REDIPS.drag.mark.action) && -1 === b.className.indexOf(Ya.exception[k.id])) {
                    a();
                    break
                }
                g = -1 < b.className.indexOf("redips-single");
                if ("cell" === v) {
                    if (("single" === REDIPS.drag.dropMode || g) && 0 < b.childNodes.length) {
                        if (1 === b.childNodes.length && 3 === b.firstChild.nodeType) break;
                        g = !0;
                        for (d = b.childNodes.length - 1; 0 <= d; d--)
                            if (b.childNodes[d].className && -1 < b.childNodes[d].className.indexOf("redips-drag")) {
                                g = !1;
                                break
                            }
                        if (!g && null !== x && null !== z && null !== E && (D !== l || L !== h || X !== m)) {
                            a();
                            break
                        }
                    }
                    if (-1 < b.className.indexOf("redips-rowhandler")) {
                        a();
                        break
                    }
                    if (b.parentNode.redips && b.parentNode.redips.emptyRow) {
                        a();
                        break
                    }
                }
                break
            }
    };
    oa = function() {
        l < f.length && null !== l && null !== h && null !== m && (ca = Ia(l, h, m), ba(l, h, m), x = l, z = h, E = m)
    };
    ba = function(a, b, c, d) {
			
      //  if ("cell" === v && la) c = f[a].rows[b].cells[c].style, c.backgroundColor = void 0 === d ? REDIPS.drag.hover.colorTd : d.color[0].toString(), void 0 !== REDIPS.drag.hover.borderTd && (void 0 === d ? c.border =
      //    REDIPS.drag.hover.borderTd : (c.borderTopWidth = d.top[0][0], c.borderTopStyle = d.top[0][1], c.borderTopColor = d.top[0][2], c.borderRightWidth = d.right[0][0], c.borderRightStyle = d.right[0][1], c.borderRightColor = d.right[0][2], c.borderBottomWidth = d.bottom[0][0], c.borderBottomStyle = d.bottom[0][1], c.borderBottomColor = d.bottom[0][2], c.borderLeftWidth = d.left[0][0], c.borderLeftStyle = d.left[0][1], c.borderLeftColor = d.left[0][2]));
      //  else if ("row" === v)
      //      for (a = f[a].rows[b], b = 0; b < a.cells.length; b++) c = a.cells[b].style, c.backgroundColor =
      //          void 0 === d ? REDIPS.drag.hover.colorTr : d.color[b].toString(), void 0 !== REDIPS.drag.hover.borderTr && (void 0 === d ? l === D ? h < L ? c.borderTop = REDIPS.drag.hover.borderTr : c.borderBottom = REDIPS.drag.hover.borderTr : "before" === REDIPS.drag.rowDropMode ? c.borderTop = REDIPS.drag.hover.borderTr : c.borderBottom = REDIPS.drag.hover.borderTr : (c.borderTopWidth = d.top[b][0], c.borderTopStyle = d.top[b][1], c.borderTopColor = d.top[b][2], c.borderBottomWidth = d.bottom[b][0], c.borderBottomStyle = d.bottom[b][1], c.borderBottomColor = d.bottom[b][2]))
    
		};
    Ia = function(a, b, c) {
        var d = {
                color: [],
                top: [],
                right: [],
                bottom: [],
                left: []
            },
            e = function(a, b) {
                var c = "border" + b + "Style",
                    d = "border" + b + "Color";
                return [I(a, "border" + b + "Width"), I(a, c), I(a, d)]
            };
        if ("cell" === v) c = f[a].rows[b].cells[c], d.color[0] = c.style.backgroundColor, void 0 !== REDIPS.drag.hover.borderTd && (d.top[0] = e(c, "Top"), d.right[0] = e(c, "Right"), d.bottom[0] = e(c, "Bottom"), d.left[0] = e(c, "Left"));
        else
            for (a = f[a].rows[b], b = 0; b < a.cells.length; b++) c = a.cells[b], d.color[b] = c.style.backgroundColor, void 0 !== REDIPS.drag.hover.borderTr &&
                (d.top[b] = e(c, "Top"), d.bottom[b] = e(c, "Bottom"));
        return d
    };
    G = function(a, b, c) {
        var d = 0,
            e = 0,
            g = a;
        "fixed" !== b && (d = 0 - Ca[0], e = 0 - Ca[1]);
        if (void 0 === c || !0 === c) {
            do d += a.offsetLeft - a.scrollLeft, e += a.offsetTop - a.scrollTop, a = a.offsetParent; while (a && "BODY" !== a.nodeName)
        } else {
            do d += a.offsetLeft, e += a.offsetTop, a = a.offsetParent; while (a && "BODY" !== a.nodeName)
        }
        return [e, d + g.offsetWidth, e + g.offsetHeight, d]
    };
    y = function() {
        var a, b, c, d;
        Ca = N();
        for (a = 0; a < f.length; a++) {
            c = [];
            d = I(f[a], "position");
            "fixed" !== d && (d = I(f[a].parentNode,
                "position"));
            for (b = f[a].rows.length - 1; 0 <= b; b--) "none" !== f[a].rows[b].style.display && (c[b] = G(f[a].rows[b], d));
            f[a].redips.offset = G(f[a], d);
            f[a].redips.row_offset = c
        }
        H = G(C);
        for (a = 0; a < O.length; a++) d = I(O[a].div, "position"), b = G(O[a].div, d, !1), O[a].offset = b, O[a].midstX = (b[1] + b[3]) / 2, O[a].midstY = (b[0] + b[2]) / 2
    };
    N = function() {
        var a, b;
        "number" === typeof window.pageYOffset ? (a = window.pageXOffset, b = window.pageYOffset) : document.body && (document.body.scrollLeft || document.body.scrollTop) ? (a = document.body.scrollLeft,
            b = document.body.scrollTop) : document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop) ? (a = document.documentElement.scrollLeft, b = document.documentElement.scrollTop) : a = b = 0;
        return [a, b]
    };
    pa = function(a) {
        var b, c;
        b = Z;
        c = aa;
        0 < U && (y(), T(), b < H[1] && b > H[3] && c < H[2] && c > H[0] && na());
        "object" === typeof a && (w = a);
        w === window ? (a = N()[0], b = ya - J, c = P) : (a = w.scrollLeft, b = w.scrollWidth - w.clientWidth, c = R);
        0 < U && (0 > c && 0 < a || 0 < c && a < b) ? (w === window ? (window.scrollBy(c, 0), N(), a = parseInt(k.style.left,
            10), isNaN(a)) : w.scrollLeft += c, setTimeout(pa, REDIPS.drag.scroll.speed)) : (REDIPS.event.add(w, "scroll", y), U = 0, q = [0, 0, 0, 0])
    };
    qa = function(a) {
        var b, c;
        b = Z;
        c = aa;
        0 < V && (y(), T(), b < H[1] && b > H[3] && c < H[2] && c > H[0] && na());
        "object" === typeof a && (w = a);
        w === window ? (a = N()[1], b = za - K, c = Q) : (a = w.scrollTop, b = w.scrollHeight - w.clientHeight, c = S);
        0 < V && (0 > c && 0 < a || 0 < c && a < b) ? (w === window ? (window.scrollBy(0, c), N(), a = parseInt(k.style.top, 10), isNaN(a)) : w.scrollTop += c, setTimeout(qa, REDIPS.drag.scroll.speed)) : (REDIPS.event.add(w, "scroll",
            y), V = 0, q = [0, 0, 0, 0])
    };
    ra = function(a, b) {
        var c = a.cloneNode(!0),
            d = c.className,
            e, g;
        !0 === b && (document.getElementById("redips_clone").appendChild(c), c.style.zIndex = 999, c.style.position = "fixed", e = G(a), g = G(c), c.style.top = e[0] - g[0] + "px", c.style.left = e[3] - g[3] + "px");
        c.setCapture && c.setCapture();
        d = d.replace("redips-clone", "");
        d = d.replace(/climit(\d)_(\d+)/, "");
        c.className = xa(d);
        void 0 === W[a.id] && (W[a.id] = 0);
        c.id = a.id + "c" + W[a.id];
        W[a.id] += 1;
        Ja(a, c);
        return c
    };
    Ja = function(a, b) {
        var c = [],
            d;
        c[0] = function(a, b) {
            a.redips &&
                (b.redips = {}, b.redips.enabled = a.redips.enabled, b.redips.container = a.redips.container, a.redips.enabled && Y(b))
        };
        c[1] = function(a, b) {
            a.redips && (b.redips = {}, b.redips.emptyRow = a.redips.emptyRow)
        };
        d = function(d) {
            var g, f, A;
            f = ["DIV", "TR"];
            g = a.getElementsByTagName(f[d]);
            f = b.getElementsByTagName(f[d]);
            for (A = 0; A < f.length; A++) c[d](g[A], f[A])
        };
        if ("DIV" === a.nodeName) c[0](a, b);
        else if ("TR" === a.nodeName) c[1](a, b);
        d(0);
        d(1)
    };
    sa = function(a, b) {
        var c, d, e;
        e = a.className;
        c = e.match(/climit(\d)_(\d+)/);
        null !== c && (d = parseInt(c[1],
            10), c = parseInt(c[2], 10), 0 === c && 1 === b && (e += " redips-clone", 2 === d && F(!0, a)), c += b, e = e.replace(/climit\d_\d+/g, "climit" + d + "_" + c), 0 >= c && (e = e.replace("redips-clone", ""), 2 === d ? (F(!1, a), REDIPS.drag.event.clonedEnd2()) : REDIPS.drag.event.clonedEnd1()), a.className = xa(e))
    };
    Ka = function(a) {
        var b = !1;
        a.srcElement ? (b = a.srcElement.nodeName, a = a.srcElement.className) : (b = a.target.nodeName, a = a.target.className);
        switch (b) {
            case "A":
            case "INPUT":
            case "SELECT":
            case "OPTION":
            case "TEXTAREA":
                b = !0;
                break;
            default:
                b = /\bredips-nodrag\b/i.test(a)
        }
        return b
    };
    F = function(a, b) {
        var c, d, e, g = [],
            f = [],
            A, k, h, l, n = /\bredips-drag\b/i,
            m = /\bredips-noautoscroll\b/i;
        k = REDIPS.drag.style.opacityDisabled;
        !0 === a || "init" === a ? (A = REDIPS.drag.style.borderEnabled, h = "move", l = !0) : (A = REDIPS.drag.style.borderDisabled, h = "auto", l = !1);
        void 0 === b ? g = C.getElementsByTagName("div") : "string" === typeof b ? g = document.querySelectorAll(b) : "object" !== typeof b || "DIV" === b.nodeName && -1 !== b.className.indexOf("redips-drag") ? g[0] = b : g = b.getElementsByTagName("div");
        for (d = c = 0; c < g.length; c++)
            if (n.test(g[c].className)) "init" ===
                a || void 0 === g[c].redips ? (g[c].redips = {}, g[c].redips.container = C) : !0 === a && "number" === typeof k ? (g[c].style.opacity = "", g[c].style.filter = "") : !1 === a && "number" === typeof k && (g[c].style.opacity = k / 100, g[c].style.filter = "alpha(opacity=" + k + ")"), Y(g[c], l), g[c].style.borderStyle = A, g[c].style.cursor = h, g[c].redips.enabled = l;
            else if ("init" === a && (e = I(g[c], "overflow"), "visible" !== e)) {
            REDIPS.event.add(g[c], "scroll", y);
            e = I(g[c], "position");
            f = G(g[c], e, !1);
            e = !m.test(g[c].className);
            O[d] = {
                div: g[c],
                offset: f,
                midstX: (f[1] +
                    f[3]) / 2,
                midstY: (f[0] + f[2]) / 2,
                autoscroll: e
            };
            f = g[c].getElementsByTagName("table");
            for (e = 0; e < f.length; e++) f[e].sca = O[d];
            d++
        }
    };
    I = function(a, b) {
        var c;
        a && a.currentStyle ? c = a.currentStyle[b] : a && window.getComputedStyle && (c = document.defaultView.getComputedStyle(a, null)[b]);
        return c
    };
    B = function(a, b, c) {
        b = b.parentNode;
        for (void 0 === c && (c = 0); b;) {
            if (b.nodeName === a)
                if (0 < c) c--;
                else break;
            b = b.parentNode
        }
        return b
    };
    La = function(a, b) {
        var c = B("TABLE", b),
            d, e;
        switch (a) {
            case "firstInColumn":
                d = 0;
                e = b.cellIndex;
                break;
            case "firstInRow":
                d =
                    b.parentNode.rowIndex;
                e = 0;
                break;
            case "lastInColumn":
                d = c.rows.length - 1;
                e = b.cellIndex;
                break;
            case "lastInRow":
                d = b.parentNode.rowIndex;
                e = c.rows[d].cells.length - 1;
                break;
            case "last":
                d = c.rows.length - 1;
                e = c.rows[d].cells.length - 1;
                break;
            default:
                d = e = 0
        }
        return [d, e, c.rows[d].cells[e]]
    };
    ia = function(a, b, c) {
        var d, e, g;
        d = function(a, b) {
            REDIPS.drag.event.relocateBefore(a, b);
            var c = REDIPS.drag.getPosition(b);
            REDIPS.drag.moveObject({
                obj: a,
                target: c,
                callback: function(a) {
                    var c = REDIPS.drag.findParent("TABLE", a),
                        d = c.redips.idx;
                    REDIPS.drag.event.relocateAfter(a, b);
                    Ba[d]--;
                    0 === Ba[d] && (REDIPS.drag.event.relocateEnd(), REDIPS.drag.enableTable(!0, c))
                }
            })
        };
        if (a !== b && "object" === typeof a && "object" === typeof b)
            if (g = a.childNodes.length, "animation" === c) {
                if (0 < g)
                    for (c = B("TABLE", b), e = c.redips.idx, REDIPS.drag.enableTable(!1, c), c = 0; c < g; c++) 1 === a.childNodes[c].nodeType && "DIV" === a.childNodes[c].nodeName && (Ba[e]++, d(a.childNodes[c], b))
            } else
                for (d = c = 0; c < g; c++) 1 === a.childNodes[d].nodeType && "DIV" === a.childNodes[d].nodeName ? (e = a.childNodes[d], REDIPS.drag.event.relocateBefore(e,
                    b), b.appendChild(e), e.redips && !1 !== e.redips.enabled && Y(e), REDIPS.drag.event.relocateAfter(e)) : d++
    };
    ja = function(a, b) {
        var c, d = [],
            e;
        if ("TD" === a.nodeName) {
            c = a.childNodes.length;
            if ("test" === b) return c = n.source === a ? void 0 : 0 === a.childNodes.length || 1 === a.childNodes.length && 3 === a.firstChild.nodeType;
            for (e = 0; e < c; e++) d.push(a.childNodes[0]), a.removeChild(a.childNodes[0]);
            return d
        }
    };
    ta = function(a, b) {
        var c, d, e, g, f, k, h, l, m, q, p, r, t = !1,
            u, v;
        u = function(a, b) {
            REDIPS.drag.shift.animation ? ia(a, b, "animation") : ia(a, b)
        };
        v = function(a) {
            "delete" === REDIPS.drag.shift.overflow ? ja(a) : "source" === REDIPS.drag.shift.overflow ? u(a, n.source) : "object" === typeof REDIPS.drag.shift.overflow && u(a, REDIPS.drag.shift.overflow);
            t = !1;
            REDIPS.drag.event.shiftOverflow(a)
        };
        if (a !== b) {
            f = REDIPS.drag.shift.mode;
            c = B("TABLE", a);
            d = B("TABLE", b);
            k = Sa(d);
            e = c === d ? [a.redips.rowIndex, a.redips.cellIndex] : [-1, -1];
            g = [b.redips.rowIndex, b.redips.cellIndex];
            p = d.rows.length;
            r = Ta(d);
            switch (f) {
                case "vertical2":
                    c = c === d && a.redips.cellIndex === b.redips.cellIndex ? e : [p,
                        b.redips.cellIndex
                    ];
                    break;
                case "horizontal2":
                    c = c === d && a.parentNode.rowIndex === b.parentNode.rowIndex ? e : [b.redips.rowIndex, r];
                    break;
                default:
                    c = c === d ? e : [p, r]
            }
            "vertical1" === f || "vertical2" === f ? (f = 1E3 * c[1] + c[0] < 1E3 * g[1] + g[0] ? 1 : -1, d = p, p = 0, r = 1) : (f = 1E3 * c[0] + c[1] < 1E3 * g[0] + g[1] ? 1 : -1, d = r, p = 1, r = 0);
            for (c[0] !== e[0] && c[1] !== e[1] && (t = !0); c[0] !== g[0] || c[1] !== g[1];) h = k[c[0] + "-" + c[1]], c[p] += f, 0 > c[p] ? (c[p] = d, c[r]--) : c[p] > d && (c[p] = 0, c[r]++), e = k[c[0] + "-" + c[1]], void 0 !== e && (l = e), void 0 !== h && (m = h), void 0 !== e && void 0 !== m ||
                void 0 !== l && void 0 !== h ? (e = -1 === l.className.indexOf(REDIPS.drag.mark.cname) ? 0 : 1, h = -1 === m.className.indexOf(REDIPS.drag.mark.cname) ? 0 : 1, t && 0 === e && 1 === h && v(l), 1 === e ? 0 === h && (q = m) : (0 === e && 1 === h && (m = q), u(l, m))) : t && void 0 !== l && void 0 === m && (e = -1 === l.className.indexOf(REDIPS.drag.mark.cname) ? 0 : 1, 0 === e && v(l))
        }
    };
    Sa = function(a) {
        var b = [],
            c, d = {},
            e, g, f, k, h, l, m, n;
        k = a.rows;
        for (h = 0; h < k.length; h++)
            for (l = 0; l < k[h].cells.length; l++) {
                c = k[h].cells[l];
                a = c.parentNode.rowIndex;
                e = c.rowSpan || 1;
                g = c.colSpan || 1;
                b[a] = b[a] || [];
                for (m =
                    0; m < b[a].length + 1; m++)
                    if ("undefined" === typeof b[a][m]) {
                        f = m;
                        break
                    }
                d[a + "-" + f] = c;
                void 0 === c.redips && (c.redips = {});
                c.redips.rowIndex = a;
                c.redips.cellIndex = f;
                for (m = a; m < a + e; m++)
                    for (b[m] = b[m] || [], c = b[m], n = f; n < f + g; n++) c[n] = "x"
            }
        return d
    };
    Ta = function(a) {
        "string" === typeof a && (a = document.getElementById(a));
        a = a.rows;
        var b, c = 0,
            d, e;
        for (d = 0; d < a.length; d++) {
            for (e = b = 0; e < a[d].cells.length; e++) b += a[d].cells[e].colSpan || 1;
            b > c && (c = b)
        }
        return c
    };
    Ma = function(a, b) {
        var c = (b.k1 - b.k2 * a) * (b.k1 - b.k2 * a),
            d;
        a += REDIPS.drag.animation.step *
            (4 - 3 * c) * b.direction;
        d = b.m * a + b.b;
        "horizontal" === b.type ? (b.obj.style.left = a + "px", b.obj.style.top = d + "px") : (b.obj.style.left = d + "px", b.obj.style.top = a + "px");
        a < b.last && 0 < b.direction || a > b.last && 0 > b.direction ? setTimeout(function() {
            Ma(a, b)
        }, REDIPS.drag.animation.pause * c) : (Ga(b.obj), b.obj.redips && (b.obj.redips.animated = !1), "cell" === b.mode ? (!0 === b.overwrite && ja(b.targetCell), b.targetCell.appendChild(b.obj), b.obj.redips && !1 !== b.obj.redips.enabled && Y(b.obj)) : Na(ua(b.target[0]), b.target[1], b.obj), "function" ===
            typeof b.callback && b.callback(b.obj))
    };
    va = function(a) {
        var b, c, d;
        b = [];
        b = c = d = -1;
        if (void 0 === a) b = l < f.length ? f[l].redips.idx : null === x || null === z || null === E ? f[D].redips.idx : f[x].redips.idx, c = f[D].redips.idx, b = [b, h, m, c, L, X];
        else {
            if (a = "string" === typeof a ? document.getElementById(a) : a) "TD" !== a.nodeName && (a = B("TD", a)), a && "TD" === a.nodeName && (b = a.cellIndex, c = a.parentNode.rowIndex, a = B("TABLE", a), d = a.redips.idx);
            b = [d, c, b]
        }
        return b
    };
    ua = function(a) {
        var b;
        for (b = 0; b < f.length && f[b].redips.idx !== a; b++);
        return b
    };
    xa = function(a) {
        void 0 !==
            a && (a = a.replace(/^\s+|\s+$/g, "").replace(/\s{2,}/g, " "));
        return a
    };
    Va = function(a) {
        var b;
        for (b = 0; b < a.childNodes.length; b++)
            if (1 === a.childNodes[b].nodeType) return !0;
        return !1
    };
    wa = function(a, b, c) {
        var d, e;
        "string" === typeof a && (a = document.getElementById(a), a = B("TABLE", a));
        if ("TR" === a.nodeName)
            for (a = a.getElementsByTagName("td"), d = 0; d < a.length; d++)
                if (a[d].style.backgroundColor = c ? c : "", "empty" === b) a[d].innerHTML = "";
                else
                    for (e = 0; e < a[d].childNodes.length; e++) 1 === a[d].childNodes[e].nodeType && (a[d].childNodes[e].style.opacity =
                        b / 100, a[d].childNodes[e].style.filter = "alpha(opacity=" + b + ")");
        else a.style.opacity = b / 100, a.style.filter = "alpha(opacity=" + b + ")", a.style.backgroundColor = c ? c : ""
    };
    return {
        obj: k,
        objOld: p,
        mode: v,
        td: n,
        hover: {
            colorTd: "#E7AB83",
            colorTr: "#E7AB83"
        },
        scroll: {
            enable: !0,
            bound: 25,
            speed: 20
        },
        only: Da,
        mark: Ya,
        style: {
            borderEnabled: "solid",
            borderDisabled: "dotted",
            opacityDisabled: "",
            rowEmptyColor: "white"
        },
        trash: {
            className: "redips-trash",
            question: null,
            questionRow: null
        },
        saveParamName: "p",
        dropMode: "multiple",
        multipleDrop: "bottom",
        clone: Za,
        animation: {
            pause: 20,
            step: 2,
            shift: !1
        },
        shift: {
            mode: "horizontal1",
            after: "default",
            overflow: "bunch"
        },
        rowDropMode: "before",
        tableSort: !0,
        init: function(a) {
            var b;
            if (void 0 === a || "string" !== typeof a) a = "redips-drag";
            C = document.getElementById(a);
            if (null === C) throw "REDIPS.drag - Drag container is not set!";
            Ca = N();
            document.getElementById("redips_clone") || (a = document.createElement("div"), a.id = "redips_clone", a.style.width = a.style.height = "1px", C.appendChild(a));
            F("init");
            u();
            Ha();
            REDIPS.event.add(window, "resize",
                Ha);
            b = C.getElementsByTagName("img");
            for (a = 0; a < b.length; a++) REDIPS.event.add(b[a], "mousemove", M), REDIPS.event.add(b[a], "touchmove", M);
            REDIPS.event.add(window, "scroll", y)
        },
        initTables: u,
        enableDrag: F,
        enableTable: function(a, b) {
            var c;
            if ("object" === typeof b && "TABLE" === b.nodeName) b.redips.enabled = a;
            else
                for (c = 0; c < f.length; c++) - 1 < f[c].className.indexOf(b) && (f[c].redips.enabled = a)
        },
        cloneObject: ra,
        saveContent: function(a, b) {
            var c = "",
                d, e, f, h, k, l, m, n = [],
                p = REDIPS.drag.saveParamName;
            "string" === typeof a && (a = document.getElementById(a));
            if (void 0 !== a && "object" === typeof a && "TABLE" === a.nodeName) {
                d = a.rows.length;
                for (k = 0; k < d; k++)
                    for (e = a.rows[k].cells.length, l = 0; l < e; l++)
                        if (f = a.rows[k].cells[l], 0 < f.childNodes.length)
                            for (m = 0; m < f.childNodes.length; m++) h = f.childNodes[m], "DIV" === h.nodeName && -1 < h.className.indexOf("redips-drag") && (c += p + "[]=" + h.id + "_" + k + "_" + l + "&", n.push([h.id, k, l]));
                c = "json" === b && 0 < n.length ? JSON.stringify(n) : c.substring(0, c.length - 1)
            }
            return c
        },
        relocate: ia,
        emptyCell: ja,
        moveObject: function(a) {
            var b = {
                    direction: 1
                },
                c, d, e, g, k, h;
            b.callback =
                a.callback;
            b.overwrite = a.overwrite;
            "string" === typeof a.id ? b.obj = b.objOld = document.getElementById(a.id) : "object" === typeof a.obj && "DIV" === a.obj.nodeName && (b.obj = b.objOld = a.obj);
            if ("row" === a.mode) {
                b.mode = "row";
                h = ua(a.source[0]);
                k = a.source[1];
                p = b.objOld = f[h].rows[k];
                if (p.redips && !0 === p.redips.emptyRow) return !1;
                b.obj = ka(b.objOld, "animated")
            } else if (b.obj && -1 < b.obj.className.indexOf("redips-row")) {
                b.mode = "row";
                b.obj = b.objOld = p = B("TR", b.obj);
                if (p.redips && !0 === p.redips.emptyRow) return !1;
                b.obj = ka(b.objOld,
                    "animated")
            } else b.mode = "cell";
            if ("object" === typeof b.obj && null !== b.obj) return b.obj.style.zIndex = 999, b.obj.redips && C !== b.obj.redips.container && (C = b.obj.redips.container, u()), h = G(b.obj), e = h[1] - h[3], g = h[2] - h[0], c = h[3], d = h[0], !0 === a.clone && "cell" === b.mode && (b.obj = ra(b.obj, !0), REDIPS.drag.event.cloned(b.obj)), void 0 === a.target ? a.target = va() : "object" === typeof a.target && "TD" === a.target.nodeName && (a.target = va(a.target)), b.target = a.target, h = ua(a.target[0]), k = a.target[1], a = a.target[2], k > f[h].rows.length -
                1 && (k = f[h].rows.length - 1), b.targetCell = f[h].rows[k].cells[a], "cell" === b.mode ? (h = G(b.targetCell), k = h[1] - h[3], a = h[2] - h[0], e = h[3] + (k - e) / 2, g = h[0] + (a - g) / 2) : (h = G(f[h].rows[k]), e = h[3], g = h[0]), h = e - c, a = g - d, b.obj.style.position = "fixed", Math.abs(h) > Math.abs(a) ? (b.type = "horizontal", b.m = a / h, b.b = d - b.m * c, b.k1 = (c + e) / (c - e), b.k2 = 2 / (c - e), c > e && (b.direction = -1), h = c, b.last = e) : (b.type = "vertical", b.m = h / a, b.b = c - b.m * d, b.k1 = (d + g) / (d - g), b.k2 = 2 / (d - g), d > g && (b.direction = -1), h = d, b.last = g), b.obj.redips && (b.obj.redips.animated = !0),
                Ma(h, b), [b.obj, b.objOld]
        },
        shiftCells: ta,
        deleteObject: function(a) {
            "object" === typeof a && "DIV" === a.nodeName ? a.parentNode.removeChild(a) : "string" === typeof a && (a = document.getElementById(a)) && a.parentNode.removeChild(a)
        },
        getPosition: va,
        rowOpacity: wa,
        rowEmpty: function(a, b, c) {
            a = document.getElementById(a).rows[b];
            void 0 === c && (c = REDIPS.drag.style.rowEmptyColor);
            void 0 === a.redips && (a.redips = {});
            a.redips.emptyRow = !0;
            wa(a, "empty", c)
        },
        getScrollPosition: N,
        getStyle: I,
        findParent: B,
        findCell: La,
        event: {
            changed: function() {},
            clicked: function() {},
            cloned: function() {},
            clonedDropped: function() {},
            clonedEnd1: function() {},
            clonedEnd2: function() {},
            dblClicked: function() {},
            deleted: function() {},
            dropped: function() {},
            droppedBefore: function() {},
            finish: function() {},
            moved: function() {},
            notCloned: function() {},
            notMoved: function() {},
            shiftOverflow: function() {},
            relocateBefore: function() {},
            relocateAfter: function() {},
            relocateEnd: function() {},
            rowChanged: function() {},
            rowClicked: function() {},
            rowCloned: function() {},
            rowDeleted: function() {},
            rowDropped: function() {},
            rowDroppedBefore: function() {},
            rowDroppedSource: function() {},
            rowMoved: function() {},
            rowNotCloned: function() {},
            rowNotMoved: function() {},
            rowUndeleted: function() {},
            switched: function() {},
            undeleted: function() {}
        }
    }
}();
REDIPS.event || (REDIPS.event = function() {
    return {
        add: function(u, F, M) {
            u.addEventListener ? u.addEventListener(F, M, !1) : u.attachEvent ? u.attachEvent("on" + F, M) : u["on" + F] = M
        },
        remove: function(u, F, M) {
            u.removeEventListener ? u.removeEventListener(F, M, !1) : u.detachEvent ? u.detachEvent("on" + F, M) : u["on" + F] = null
        }
    }
}());