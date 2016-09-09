/**
 * Created by linzh on 2016/6/30.
 * 不支持IE8及以下的浏览器
 *  ① querySelector() 方法仅仅返回匹配指定选择器的第一个元素。如果你需要返回所有的元素，请使用 querySelectorAll() 方法替代。
 */
/*!art-template - Template Engine | http://aui.github.com/artTemplate/*/
window.L = (function (loadone) {/* loadone 方法是在全部的ready家在完毕之后的回调 */
    "use strict";/* save time  */
    var options = {
        //公共资源的URL路径
        public_url: ''
    };
    var ReadyStack = {
        heap:[],/*fifo*/
        stack:[]/*folo*/
    };

    //传递给loadone方法的
    var Pass = {
        plugins:[] /*插件加载队列*/
    };

    /**
     * 標記頁面是否家在完畢
     * @type {boolean}
     */
    var pagedone = false;

    var _headTag = null;

    //常见的兼容性问题处理
    (function () {
        //处理console对象缺失
        window.console || (window.console = (function () {
            var c = {};
            c.log = c.warn = c.debug = c.info = c.error = c.time = c.dir = c.profile = c.clear = c.exception = c.trace = c.assert = function () {
            };
            return c;
        })());
        //解决IE8不支持indexOf方法的问题
        if (!Array.prototype.indexOf) {
            Array.prototype.indexOf = function (elt) {
                var len = this.length >>> 0;
                var from = Number(arguments[1]) || 0;
                from = (from < 0) ? Math.ceil(from) : Math.floor(from);
                if (from < 0) from += len;
                for (; from < len; from++) {
                    if (from in this && this[from] === elt) return from;
                }
                return -1;
            };
        }
        if (!Array.prototype.max) Array.prototype.max = function () { return Math.max.apply({}, this);};
        if (!Array.prototype.min) Array.prototype.min = function () { return Math.min.apply({}, this); };

        if (!String.prototype.trim)  String.prototype.trim = function () { return this.replace(/(^\s*)|(\s*$)/g, '');};
        if (!String.prototype.ltrim) String.prototype.ltrim = function () { return this.replace(/(^\s*)/g, ''); };
        if (!String.prototype.rtrim)  String.prototype.rtrim = function () { return this.replace(/(\s*$)/g, ''); };
        if (!String.prototype.beginWith) String.prototype.beginWith = function (chars) { return this.indexOf(chars) === 0; };
    })();

    //script library
    var ScriptLib = {
        _: {},
        posNm: function (name) {/*parse name*/
            if (name.indexOf('/') >= 0) {
                name = name.split('/');
                name = name[name.length - 1];
            }
            return name;
        },
        has: function (name) {
            return this.posNm(name) in this._;
        },
        add: function (name) {
            this._[this.posNm(name)] = true;
            return this;
        }
    };
    /**
     * clone an object
     * Handle the 3 simple types, and null or undefined
     *  "number," "string," "boolean," "object," "function," 和 "undefined"
     * @param obj
     * @returns {*}
     */
    var clone = function (obj) {
        //null 本身就是一个空的对象
        if (!obj || "object" !== typeof obj) return obj;
        var copy = null;
        // Handle Date
        if (obj instanceof Date) {
            copy = new Date();
            copy.setTime(obj.getTime());
            return copy;
        }
        // Handle Array
        if (obj instanceof Array) {
            copy = [];
            var len = obj.length;
            for (var i = 0; i < len; ++i) {
                copy[i] = clone(obj[i]);
            }
            return copy;
        }

        // Handle Object
        if (obj instanceof Object) {
            copy = {};
            for (var attr in obj) {
                if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
            }
            return copy;
        }

        throw new Error("Unable to copy obj! Its type isn't supported.");
    };
    var _path = function (path) {
        if ((path.length > 4) && (path.substr(0, 4) !== 'http')) {
            if (!options['public_url']) options['public_url'] = '/';//throw "Public uri not defined!";
            path = options['public_url'] + path;
        }
        return path;
    };
    var guid = function () {
        var s = [];
        var hexDigits = "0123456789abcdef";
        for (var i = 0; i < 36; i++) {
            s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
        }
        s[14] = "4";  // bits 12-15 of the time_hi_and_version field to 0010
        s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);  // bits 6-7 of the clock_seq_hi_and_reserved to 01
        s[8] = s[13] = s[18] = s[23] = "-";
        return s.join("");
    };
    var jq = function (selector) {
        if (typeof selector == "undefined") {
            //get version of jquery,it will return 0 if not exist
            return (typeof jQuery == "undefined") ? 0 : $().jquery;
        }
        return (selector instanceof $) ? selector : $(selector);
    };
    var cookie = {
        set: function (name, value, expire, path) {
            path = ";path=" + (path ? path : '/');// all will access if not set the path
            var cookie;
            if (undefined === expire || false === expire) {
                //set or modified the cookie, and it will be remove while leave from browser
                cookie = name + "=" + value;
            } else if (!isNaN(expire)) {// is numeric
                var _date = new Date();//current time
                if (expire > 0) {
                    _date.setTime(_date.getTime() + expire);//count as millisecond
                } else if (expire === 0) {
                    _date.setDate(_date.getDate() + 365);//expire after an year
                } else {
                    //delete cookie while expire < 0
                    _date.setDate(_date.getDate() - 1);//expire after an year
                }
                cookie = name + "=" + value + ";expires=" + _date.toUTCString();
            } else {
                console.log([name, value, expire, path], "expect 'expire' to be false/undefined/numeric !");
            }
            document.cookie = cookie + path;
        },
        //get a cookie with a name
        get: function (name,dft) {
            if (document.cookie.length > 0) {
                var cstart = document.cookie.indexOf(name + "=");
                if (cstart >= 0) {
                    cstart = cstart + name.length + 1;
                    var cend = document.cookie.indexOf(';', cstart);//begin from the index of param 2
                    (-1 === cend) && (cend = document.cookie.length);
                    return document.cookie.substring(cstart, cend);
                }
            }
            return dft || "";
        }
    };
    //environment
    var E = {
        /**
         * get the hash of uri
         * @returns {string}
         */
        hash: function () {
            if (!location.hash) return "";
            var hash = location.hash;
            var index = hash.indexOf('#');
            if (index >= 0) hash = hash.substring(index + 1);
            return "" + decodeURI(hash);
        },
        /**
         * get script path
         * there are some diffrence between domain access(virtual machine) and ip access of href
         * domian   :http://192.168.1.29:8085/edu/Public/admin.php/Admin/System/Menu/PageManagement#dsds
         * ip       :http://edu.kbylin.com:8085/admin.php/Admin/System/Menu/PageManagement#dsds
         * what we should do is SPLIT '.php' from href
         * ps:location.hash
         */
        base: function () {
            var href = location.href;
            var index = href.indexOf('.php');
            if (index > 0) {//exist
                return href.substring(0, index + 4);
            } else {
                if (location.origin) {
                    return location.origin;
                } else {
                    return location.protocol + "//" + location.host;//default 80 port
                }
            }
        },
        /**
         * 跳转到指定的链接地址
         * 增加检查url是否合法
         * @param url
         */
        redirect: function (url) {
            location.href = url;
        },
        //获得可视区域的大小
        viewport: function () {
            var win = window;
            var type = 'inner';
            if (!('innerWidth' in window)) {
                type = 'client';
                win = document.documentElement ? document.documentElement : document.body;
            }
            return {
                width: win[type + 'Width'],
                height: win[type + 'Height']
            };
        },
        getBrowser: function () { /* get the name and version of client like :Object {type: "Chrome", version: "50.0.2661.94"} */
            var v, tp = {}, res = {}; //用户返回的对象
            var ua = navigator.userAgent.toLowerCase();
            (v = ua.match(/msie ([\d.]+)/)) ? tp.ie = v[1] :
                (v = ua.match(/firefox\/([\d.]+)/)) ? tp.firefox = v[1] :
                    (v = ua.match(/chrome\/([\d.]+)/)) ? tp.chrome = v[1] :
                        (v = ua.match(/opera.([\d.]+)/)) ? tp.opera = v[1] :
                            (v = ua.match(/version\/([\d.]+).*safari/)) ? tp.safari = v[1] : 0;
            if (tp.ie) {
                res.type = "ie";
                res.version = parseInt(tp.ie);
            } else if (tp.firefox) {
                res.type = "firefox";
                res.version = parseInt(tp.firefox);
            } else if (tp.chrome) {
                res.type = "chrome";
                res.version = parseInt(tp.chrome);
            } else if (tp.opera) {
                res.type = "opera";
                res.version = parseInt(tp.opera);
            } else if (tp.safari) {
                res.type = "safari";
                res.version = parseInt(tp.safari);
            } else {
                res.type = "unknown";
                res.version = 0;
            }
            return res;
        },
        ie: function () {/* get the version of ie */
            var version;
            return ((version = navigator.userAgent.toLowerCase().match(/msie ([\d.]+)/))?parseInt(version[1]):12);//如果是其他浏览器，默认判断为版本12
        },
        /**
         * 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符,年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
         * @param fmt
         * @returns {*}
         */
        date: function (fmt) { //author: meizz
            if (!fmt) fmt = "yyyy-MM-dd hh:mm:ss.S";//2006-07-02 08:09:04.423
            var o = {
                "M+": this.getMonth() + 1,                 //月份
                "d+": this.getDate(),                    //日
                "h+": this.getHours(),                   //小时
                "m+": this.getMinutes(),                 //分
                "s+": this.getSeconds(),                 //秒
                "q+": Math.floor((this.getMonth() + 3) / 3), //季度
                "S": this.getMilliseconds()             //毫秒
            };
            if (/(y+)/.test(fmt))
                fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
            for (var k in o) {
                if (!o.hasOwnProperty(k)) continue;
                if (new RegExp("(" + k + ")").test(fmt))
                    fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            }
            return fmt;
        }
    };
    /**
     * Object
     * @type {{}}
     */
    var O = {
        /**
         * check if key exist and the value is not empty
         * @param optname property name
         * @param obj target object to check
         * @param dft default if not exist
         * @returns {*}
         */
        notempty:function(optname,obj,dft){
            return obj?(obj.hasOwnProperty(optname) && obj[optname]):(dft || false);
        },
        /**
         * get the type of variable
         * @param o
         * @returns string :"number" "string" "boolean" "object" "function" 和 "undefined"
         */
        gettype: function (o) {
            if (o === null) return "null";
            if (o === undefined) return "undefined";
            return Object.prototype.toString.call(o).slice(8, -1).toLowerCase();
        },
        isObj: function (obj) {
            return this.gettype(obj) === "object";
        },
        //注意安全性问题,并不推荐使用
        toObj: function (s) {
            return (s instanceof Object)?s:eval("(" + s + ")");/* 已经是对象的清空下直接返回,TIP:将括号内的表达式转化为对象而不是作为语句来处理 */
        },
        /**
         * 判断一个元素是否是数组
         * @param el
         * @returns {boolean}
         */
        isArr: function (el) { return Array.isArray?Array.isArray(el):(this.gettype(el) === "array"); },
        isStr:function (el) {return this.gettype(el) === "string"; },
        isFunc: function (el) {return this.gettype(el) === "function";},
        /**
         * 检查对象是否有指定的属性
         * @param obj {{}}
         * @param prop 属性数组
         * @return int 返回1表示全部属性都拥有,返回0表示全部都没有,部分有的情况下返回-1
         */
        prop: function (obj, prop) {
            var count = 0;
            if(!this.isArr(prop)) prop = [prop];
            for (var i = 0; i < prop.length; i++)if (obj.hasOwnProperty(prop[i])) count++;
            return count === prop.length?1:(count === 0?0:-1);
        }
    };
    /**
     * Utils
     * @type object
     */
    var U = {
        parseUrl: function (s) {
            var o = {};
            if (s) {
                s = decodeURI(s);
                var arr = s.split("&");
                for (var i = 0; i < arr.length; i++) {
                    var d = arr[i].split("=");
                    o[d[0]] = d[1] ? d[1] : '';
                }
            }
            return o;
        },
        /**
         * 遍历对象
         * @param obj {{}|[]} 待遍历的对象或者数组
         * @param call 返回
         * @param meta other data
         */
        each: function (obj, call, meta) {
            var result = undefined;
            if (O.isArr(obj)) {
                for (var i = 0; i < obj.length; i++) {
                    result = call(obj[i], i, meta);
                    if (result === '[break]') break;
                    if (result === '[continue]') continue;
                    if (result !== undefined) return result;
                }
            } else if (O.isObj(obj)) {
                for (var key in obj) {
                    if (!obj.hasOwnProperty(key)) continue;
                    result = call(obj[key], key, meta);
                    if (result === '[break]') break;
                    if (result === '[continue]') continue;
                    if (result !== undefined) return result;
                }
            }else{
                console.log(obj);
                throw "expect param 1 tobe array/object";
            }
        },
        /**
         * 停止事件冒泡
         * 如果提供了事件对象，则这是一个非IE浏览器,因此它支持W3C的stopPropagation()方法
         * 否则，我们需要使用IE的方式来取消事件冒泡
         * @param e
         */
        stopBubble: function (e) {
            if (e && e.stopPropagation) {
                e.stopPropagation();
            } else {
                window.event.cancelBubble = true;
            }
        },
        /**
         * 阻止事件默认行为
         * 阻止默认浏览器动作(W3C)
         * IE中阻止函数器默认动作的方式
         * @param e
         * @returns {boolean}
         */
        stopDefault: function (e) {
            if (e && e.preventDefault) {
                e.preventDefault();
            } else {
                window.event.returnValue = false;
            }
            return false;
        }
    };
    /**
     * DOM
     * @type {{}}
     */
    var D = {
        /**
         * 检查dom对象是否存在指定的类名称
         * @param obj
         * @param cls
         * @returns {Array|{index: number, input: string}}
         */
        hasClass: function (obj, cls) {
            return obj.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
        },
        /**
         * 添加类
         * @param obj
         * @param cls
         */
        addClass: function (obj, cls) {
            if (!this.hasClass(obj, cls)) obj.className += " " + cls;
        },
        /**
         * 删除类
         * @param obj
         * @param cls
         */
        removeClass: function (obj, cls) {
            if (this.hasClass(obj, cls)) {
                var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
                obj.className = obj.className.replace(reg, ' ');
            }
        },
        /**
         * 逆转类
         * @param obj
         * @param cls
         */
        toggleClass: function (obj, cls) {
            if (this.hasClass(obj, cls)) {
                this.removeClass(obj, cls);
            } else {
                this.addClass(obj, cls);
            }
        },
        //支持多个类名的查找 http://www.cnblogs.com/rubylouvre/archive/2009/07/24/1529640.html
        getElementsByClassName: function (cls, ele) {
            var list = (ele || document).getElementsByTagName('*');
            var set = [];

            for (var i = 0; i < list.length; i++) {
                var child = list[i];
                var classNames = child.className.split(' ');
                for (var j = 0; j < classNames.length; j++) {
                    if (classNames[j] == cls) {
                        set.push(child);
                        break;
                    }
                }
            }
            return set;
        }
    };

    //监听窗口状态变化
    document.onreadystatechange = function () {
        if (document.readyState === "complete" || document.readyState === "loaded"){
            document.onreadystatechange = null;
            var i ;
            for (i = 0; i < ReadyStack.heap.length; i++) (ReadyStack.heap[i])();
            for (i = ReadyStack.stack.length -1; i >= 0; i--) (ReadyStack.stack[i])();
            pagedone = true;
            O.isFunc(loadone) && loadone(Pass);
        }
    };

    return {
        jq: jq,
        guid: guid,//随机获取一个GUID
        clone: clone,
            getRstype:function (path) {/* 获取资源类型 */
            var type = path.substring(path.length - 3);
            switch (type) {
                case 'css':
                    type = 'css';
                    break;
                case '.js':
                    type = 'js';
                    break;
                case 'ico':
                    type = 'ico';
                    break;
                default:
                    throw "wrong type'" + t + "',it must be[css,js,ico]";
            }
            return type;
        },
        /**
         * load resource for page
         * @param path like '/js/XXX.YY' which oppo to public_url
         * @param type file type
         * @param call callback
         * @returns {Window.L}
         */
        load: function (path, type, call) {
            if (O.isArr(path)) {
                var env = this;
                var len = path.length;
                //同一个组合中也按照顺序加载
                if(len > 1){
                    var loadItem = function (i,c) {
                        var type = L.getRstype(path[i]);
                        if(i == (len -1)){
                            //last one
                            env.load(path[i],type,c);
                        }else{
                            env.load(path[i],type,function () {
                                loadItem(1+i,c);
                            });
                        }
                    };
                    loadItem(0,call);
                }else{
                    env.load(path[1],null,call);
                }
            } else {
                if (!type) type = this.getRstype(path);
                if(ScriptLib.has(path)){
                    /* 本页面加载过将不再重新载入
                     * 如果库在之前定义过(那么制定到这里的时候一定是加载过的，因为之后加在完成才能执行回调序列)
                     * 可以直接视为加在完毕
                     */
                    call.call();
                }else{
                    //现仅仅支持css,js,ico的类型
                    //注意的是，直接使用document.write('<link .....>') 可能導致html頁面混亂。。。
                    switch (type) {
                        case 'css':
                            L.loadStyle( _path(path));
                            call.call();/* style资源可有可无，可以视为立即加载完毕 */
                            break;
                        case 'js':
                            L.loadScript(_path(path),call);
                            break;
                        case 'ico':
                            L.loadIcon(_path(path));
                            call.call();/* ico资源可有可无，可以视为立即加载完毕 */
                            break;
                    }
                    ScriptLib.add(path);
                }
            }
            return this;
        },
        /*  name : div#maindv.hello,justr or div#maindv.hello.justr (class attr is behind the id and id is behind the tagname) */
        newEle:function (name,opts,ih) {
            var clses, id;
            if (name.indexOf('.') > 0) {
                clses = name.split(".");
                name = clses.shift();
            }
            if (name.indexOf("#") > 0) {
                var tempid = name.split("#");
                name = tempid[0];
                id = tempid[1];
            }

            var el = document.createElement(name);
            id && el.setAttribute('id', id);
            if (clses) {
                var ct = '';
                U.each(clses,function (v) {
                    ct += v+" ";
                });
                el.setAttribute('class', ct);
            }

            opts && U.each(opts,function (v,k) {
                el[k] = v;
            });
            if (ih) el.innerHTML = ih;
            return el;
        },
        attach2Head:function (ele) {
            if(!_headTag) _headTag = document.getElementsByTagName("head")[0];
            _headTag.appendChild(ele);
            return ele;
        },
        /* ps:icon是否加在成功无关紧要 */
        loadIcon:function(path){
            this.attach2Head(this.newEle("link",{
                href:path,
                rel:"shortcut icon"
            }));
        },
        /* ps:样式表是否加在成功无关紧要 */
        loadStyle:function (path) {
            this.attach2Head(this.newEle("link",{
                href:path,
                rel:"stylesheet",
                type:"text/css"
            }));
        },
        loadScript: function (url, callback){
            this.readyNext(this.attach2Head(this.newEle("script",{
                src:url,
                type:"text/javascript"
            })),callback);
        },
        readyNext:function (ele,callback) {
            if (ele.readyState){ //IE
                ele.onreadystatechange = function(){
                    if (ele.readyState == "loaded" || ele.readyState == "complete"){
                        ele.onreadystatechange = null;
                        callback && callback();
                    }
                };
            } else { //Others
                if(callback) ele.onload = callback;
            }
        },
        cookie: cookie,
        //init self or used as an common tool
        init: function (config, target,cover) {
            if (!target) target = options;
            U.each(config, function (item, key) {
                if(cover || (cover === undefined) ||  target.hasOwnProperty(key)){
                    target[key] = item;
                }
            });
            return this;
        },
        E: E,//environment
        U: U,//utils
        D: D,//dom
        O: O,
        /**
         * new element
         * @param exp express
         * @param ih innerHTML
         * @returns {Element}
         * @constructor
         */
        NE: function (exp, ih) {
            var tagname = exp, clses, id;
            if (exp.indexOf('.') > 0) {
                clses = exp.split(".");
                exp = clses.shift();
            }
            if (exp.indexOf("#") > 0) {
                var tempid = exp.split("#");
                tagname = tempid[0];
                id = tempid[1];
            } else {
                tagname = exp
            }

            var element = document.createElement(tagname);
            id && element.setAttribute('id', id);
            if (clses) {
                var ct = '';
                for (var i = 0; i < clses.length; i++) {
                    ct += clses[i];
                    if (i !== clses.length - 1)  ct += ",";
                }
                element.setAttribute('class', ct);
            }
            if (ih) element.innerHTML = ih;
            return element;
        },//新建一个DOM元素
        //new self
        NS: function (context) {
            var Y = function () {
                return {target: null};
            };
            var instance = new Y();
            if (context) {
                U.each(context, function (item, key) {
                    instance[key] = item;
                });
            }
            return instance;
        },//获取一个单例的操作对象作为上下文环境的深度拷贝
        ready: function (c,prepend) {
            prepend?ReadyStack.stack.push(c):ReadyStack.heap.push(c);
        },
        //plugins
        P: {
            _jq:null, //jquery object
            JsMap:{},//plugin autoload start
            /**
             * import plugins
             * @param option
             * @returns {*}
             */
            import:function (option) {
                L.init(option,this.JsMap,true);
            },
            get:function (name,dft) {
                return name?(O.notempty(name,this.JsMap) ? this.JsMap[name] : (dft || false)):this.JsMap;
            },
            load:function(pnm,call){/* plugin name, callback */
                if(pnm in this.JsMap) pnm = this.JsMap[pnm];
                if(pagedone){
                    /* it will not put into quene if page has load done！ */
                    L.load(pnm,null,call);
                }else{
                    Pass.plugins.push([pnm,call]);
                }
                return this;
            },
            /**
             * @param selector
             * @param options
             * @param functionName
             * @param pluginName
             * @param callback callback while on loaded
             */
            initlize:function(selector,options,functionName,pluginName,callback){
                pluginName = pluginName?pluginName:functionName;
                var jq = this._jq?this._jq:(this._jq = $());
                L.load(this.JsMap[pluginName],null,function () {
                    if(!L.O.isObj(selector) || (selector instanceof jQuery)){
                        selector = $(selector);
                        options || (options = {});
                        (functionName in jq) && (jq[functionName]).apply(selector,O.isArr(options)?options:[options]);
                        callback && callback(selector);
                    }else{
                        var list = [];
                        L.U.each(selector,function (params,k) {
                           list.push( k = $(k));
                            (functionName in jq) && (jq[functionName]).apply(k,O.isArr(params)?params:[params]);
                        });
                        callback && callback(list);
                    }
                });
            }
        },
        //variable
        V: {}//constant or config// judge

    };
})(function (pass) {
    //插件加载(按序進行)
    var len = pass.plugins.length;
    var loadQuene = function (i) {
        if(i < len){
            L.load(pass.plugins[i][0],null,function () {
                var call = pass.plugins[i][1];
                call && call();
                loadQuene(++i);
            });
        }
    };
    loadQuene(0);
});
