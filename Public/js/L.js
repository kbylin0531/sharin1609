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
                    if (result !== undefined) return result;//如果返回了什么东西解释实际返回了，当然除了命令外
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
                U.each(path,function (p,i) {
                    if(len == (i+1)){/* callback if last done */
                        env.load(p,null,call);
                    }else{
                        env.load(p);
                    }
                });
            } else {
                if (!type) {//auto get the type
                    var t = path.substring(path.length - 3);
                    switch (t) {
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
                }
                //本页面加载过将不再重新载入
                if(ScriptLib.has(path)){return this;}
                //现仅仅支持css,js,ico的类型
                //注意的是，直接使用document.write('<link .....>') 可能導致html頁面混亂。。。
                switch (type) {
                    case 'css':
                        L.loadStyle( _path(path));
                        break;
                    case 'js':
                        L.loadScript(_path(path),call);
                        break;
                    case 'ico':
                        L.loadIcon(_path(path) );
                        break;
                }
                ScriptLib.add(path);
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
            console.log(el,opts);
            if (ih) el.innerHTML = ih;
            return el;
        },
        attach2Head:function (ele) {
            if(!_headTag) _headTag = document.getElementsByTagName("head")[0];
            _headTag.appendChild(ele);
            return ele;
        },
        loadIcon:function(path){
            this.attach2Head(this.newEle("link",{
                href:path,
                rel:"shortcut icon"
            }));
        },
        loadStyle:function (path) {
            this.attach2Head(this.newEle("link",{
                href:path,
                rel:"stylesheet",
                type:"text/css"
            }));
        },
        loadScript: function (url, callback){
            var script = this.attach2Head(this.newEle("script",{
                src:url,
                type:"text/javascript"
            }));
            if (script.readyState){ //IE
                script.onreadystatechange = function(){
                    if (script.readyState == "loaded" || script.readyState == "complete"){
                        script.onreadystatechange = null;
                        callback && callback();
                    }
                };
            } else { //Others
                if(callback) script.onload = callback;
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
                if(pnm in this.JsMap){
                    pnm = this.JsMap[pnm];
                }
                if(pagedone){
                    /* it will not put into quene if page has load done！ */
                    L.load(pnm,null,call);
                }else{
                    Pass.plugins.push([pnm,call]);
                }
                return L.P;
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
// 加密测试
// console.log(L.md5(L.sha1('123456')) === 'd93a5def7511da3d0f2d171d9c344e91');
console.log('如果有好的建议和想法请发邮件到我的邮箱，linzhv@qq.com');/**
 * 模板引擎
 */
!(function () {
    /**
     * 模板引擎
     * @param filename 模板名
     * @param content 数据。如果为字符串则编译并缓存编译结果
     * @returns {*} 渲染好的HTML字符串或者渲染方法
     */
    var template = function (filename, content) {
        return typeof content === 'string'
            ?   compile(content, {
            filename: filename
        })
            :   renderFile(filename, content);
    };
    template.version = '3.0.0';

    /**
     * 设置全局配置
     * @param name 名称
     * @param value 值
     */
    template.config = function (name, value) {
        defaults[name] = value;
    };

    var defaults = template.defaults = {
        openTag: '<%',    // 逻辑语法开始标签
        closeTag: '%>',   // 逻辑语法结束标签
        escape: true,     // 是否编码输出变量的 HTML 字符
        cache: true,      // 是否开启缓存（依赖 options 的 filename 字段）
        compress: false,  // 是否压缩输出
        parser: null      // 自定义语法格式器 @see: template-syntax.js
    };    var cacheStore = template.cache = {};    /**
     * 渲染模板
     * @param source 模板
     * @param options 数据
     * @returns {*} 渲染好的字符串
     */
    template.render = function (source, options) {
        return compile(source, options);
    };    /**
     * 渲染模板(根据模板名)
     * @name    template.render
     * @param   {String}    模板名
     * @param   {Object}    数据
     * @return  {String}    渲染好的字符串
     */
    var renderFile = template.renderFile = function (filename, data) {
        var fn = template.get(filename) || showDebugInfo({
                filename: filename,
                name: 'Render Error',
                message: 'Template not found'
            });
        return data ? fn(data) : fn;
    };    /**
     * 获取编译缓存（可由外部重写此方法）
     * @param filename 模板名
     * @returns {*}
     */
    template.get = function (filename) {

        var cache;

        if (cacheStore[filename]) {
            // 使用内存缓存
            cache = cacheStore[filename];
        } else if (typeof document === 'object') {
            // 加载模板并编译
            var elem = document.getElementById(filename);

            if (elem) {
                var source = (elem.value || elem.innerHTML)
                    .replace(/^\s*|\s*$/g, '');
                cache = compile(source, {
                    filename: filename
                });
            }
        }

        return cache;
    };    var toString = function (value, type) {

        if (typeof value !== 'string') {

            type = typeof value;
            if (type === 'number') {
                value += '';
            } else if (type === 'function') {
                value = toString(value.call(value));
            } else {
                value = '';
            }
        }

        return value;

    };    var escapeMap = {
        "<": "&#60;",
        ">": "&#62;",
        '"': "&#34;",
        "'": "&#39;",
        "&": "&#38;"
    };    var escapeFn = function (s) {
        return escapeMap[s];
    };

    var escapeHTML = function (content) {
        return toString(content)
            .replace(/&(?![\w#]+;)|[<>"']/g, escapeFn);
    };    var isArray = L.O.isArr;    var each = function (data, callback) {
        var i, len;
        if (isArray(data)) {
            for (i = 0, len = data.length; i < len; i++) {
                callback.call(data, data[i], i, data);
            }
        } else {
            for (i in data) {
                callback.call(data, data[i], i);
            }
        }
    };    var utils = template.utils = {

        $helpers: {},

        $include: renderFile,

        $string: toString,

        $escape: escapeHTML,

        $each: each

    };
    /**
     * 添加模板辅助方法
     * @param name 名称
     * @param helper 方法
     */
    template.helper = function (name, helper) {
        helpers[name] = helper;
    };

    var helpers = template.helpers = utils.$helpers;

    /**
     * 模板错误事件（可由外部重写此方法）
     * @param e
     */
    template.onerror = function (e) {
        var message = 'Template Error\n\n';
        for (var name in e) {
            message += '<' + name + '>\n' + e[name] + '\n\n';
        }

        if (typeof console === 'object') {
            console.error(message);
        }
    };// 模板调试器
    var showDebugInfo = function (e) {

        template.onerror(e);

        return function () {
            return '{Template Error}';
        };
    };    /**
     * 编译模板
     * 2012-6-6 @TooBug: define 方法名改为 compile，与 Node Express 保持一致
     * @name    template.compile
     * @param   {String}    模板字符串
     * @param   {Object}    编译选项
     *
     *      - openTag       {String}
     *      - closeTag      {String}
     *      - filename      {String}
     *      - escape        {Boolean}
     *      - compress      {Boolean}
     *      - debug         {Boolean}
     *      - cache         {Boolean}
     *      - parser        {Function}
     *
     * @return  {Function}  渲染方法
     */
    var compile = template.compile = function (source, options) {

        // 合并默认配置
        options = options || {};
        for (var name in defaults) {
            if (options[name] === undefined) {
                options[name] = defaults[name];
            }
        }        var filename = options.filename;        try {

            var Render = compiler(source, options);

        } catch (e) {

            e.filename = filename || 'anonymous';
            e.name = 'Syntax Error';

            return showDebugInfo(e);
        }        // 对编译结果进行一次包装

        var render = function(data) {
            try {
                return new Render(data, filename) + '';
            } catch (e) {
                // 运行时出错后自动开启调试模式重新编译
                if (!options.debug) {
                    options.debug = true;
                    return compile(source, options)(data);
                }
                return showDebugInfo(e)();
            }
        };
        render.prototype = Render.prototype;
        render.toString = function () {
            return Render.toString();
        };
        if (filename && options.cache) {
            cacheStore[filename] = render;
        }
        return render;
    };

// 数组迭代
    var forEach = utils.$each;// 静态分析模板变量
    var KEYWORDS =
        // 关键字
        'break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if,in,instanceof,'+
        +'new,null,return,switch,this,throw,true,try,typeof,var,void,while,with'
        // 保留字
        + ',abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto,int,interface,long,native'
        + ',implements,import,package,private,protected,public,short,static,super,synchronized,throws,transient,volatile'
        // ECMA 5 - use strict
        + ',arguments,let,yield,undefined';
    var REMOVE_RE = /\/\*[\w\W]*?\*\/|\/\/[^\n]*\n|\/\/[^\n]*$|"(?:[^"\\]|\\[\w\W])*"|'(?:[^'\\]|\\[\w\W])*'|\s*\.\s*[$\w\.]+/g;
    var SPLIT_RE = /[^\w$]+/g;
    var KEYWORDS_RE = new RegExp(["\\b" + KEYWORDS.replace(/,/g, '\\b|\\b') + "\\b"].join('|'), 'g');
    var NUMBER_RE = /^\d[^,]*|,\d[^,]*/g;
    var BOUNDARY_RE = /^,+|,+$/g;
    var SPLIT2_RE = /^$|,+/;

// 获取变量
    function getVariable (code) {
        return code
            .replace(REMOVE_RE, '')
            .replace(SPLIT_RE, ',')
            .replace(KEYWORDS_RE, '')
            .replace(NUMBER_RE, '')
            .replace(BOUNDARY_RE, '')
            .split(SPLIT2_RE);
    }// 字符串转义
    function stringify (code) {
        return "'" + code
            // 单引号与反斜杠转义
                .replace(/('|\\)/g, '\\$1')
                // 换行符转义(windows + linux)
                .replace(/\r/g, '\\r')
                .replace(/\n/g, '\\n') + "'";
    }    function compiler (source, options) {

        var debug = options.debug;
        var openTag = options.openTag;
        var closeTag = options.closeTag;
        var parser = options.parser;
        var compress = options.compress;
        var escape = options.escape;
        var line = 1;
        var uniq = {$data:1,$filename:1,$utils:1,$helpers:1,$out:1,$line:1};
        var isNewEngine = ''.trim;// '__proto__' in {}
        var replaces = isNewEngine
            ? ["$out='';", "$out+=", ";", "$out"]
            : ["$out=[];", "$out.push(", ");", "$out.join('')"];

        var concat = isNewEngine
            ? "$out+=text;return $out;"
            : "$out.push(text);";

        var print = "function(){"
            +      "var text=''.concat.apply('',arguments);"
            +       concat
            +  "}";

        var include = "function(filename,data){"
            +      "data=data||$data;"
            +      "var text=$utils.$include(filename,data,$filename);"
            +       concat
            +   "}";

        var headerCode = "'use strict';"
            + "var $utils=this,$helpers=$utils.$helpers,"
            + (debug ? "$line=0," : "");

        var mainCode = replaces[0];

        var footerCode = "return new String(" + replaces[3] + ");";

        // html与逻辑语法分离
        forEach(source.split(openTag), function (code) {
            code = code.split(closeTag);

            var $0 = code[0];
            var $1 = code[1];

            // code: [html]
            if (code.length === 1) {

                mainCode += html($0);

                // code: [logic, html]
            } else {

                mainCode += logic($0);

                if ($1) {
                    mainCode += html($1);
                }
            }        });

        var code = headerCode + mainCode + footerCode;

        // 调试语句
        if (debug) {
            code = "try{" + code + "}catch(e){"
                +       "throw {"
                +           "filename:$filename,"
                +           "name:'Render Error',"
                +           "message:e.message,"
                +           "line:$line,"
                +           "source:" + stringify(source)
                +           ".split(/\\n/)[$line-1].replace(/^\\s+/,'')"
                +       "};"
                + "}";
        }

        try {
            var Render = new Function("$data", "$filename", code);
            Render.prototype = utils;
            return Render;
        } catch (e) {
            e.temp = "function anonymous($data,$filename) {" + code + "}";
            throw e;
        }

        // 处理 HTML 语句
        function html (code) {

            // 记录行号
            line += code.split(/\n/).length - 1;

            // 压缩多余空白与注释
            if (compress) {
                code = code
                    .replace(/\s+/g, ' ')
                    .replace(/<!--[\w\W]*?-->/g, '');
            }

            if (code) {
                code = replaces[1] + stringify(code) + replaces[2] + "\n";
            }

            return code;
        }        // 处理逻辑语句
        function logic (code) {

            var thisLine = line;

            if (parser) {

                // 语法转换插件钩子
                code = parser(code, options);

            } else if (debug) {

                // 记录行号
                code = code.replace(/\n/g, function () {
                    line ++;
                    return "$line=" + line +  ";";
                });

            }
            // 输出语句. 编码: <%=value%> 不编码:<%=#value%>
            // <%=#value%> 等同 v2.0.3 之前的 <%==value%>
            if (code.indexOf('=') === 0) {
                var escapeSyntax = escape && !/^=[=#]/.test(code);
                code = code.replace(/^=[=#]?|[\s;]*$/g, '');
                // 对内容编码
                if (escapeSyntax) {

                    var name = code.replace(/\s*\([^\)]+\)/, '');

                    // 排除 utils.* | include | print

                    if (!utils[name] && !/^(include|print)$/.test(name)) {
                        code = "$escape(" + code + ")";
                    }

                    // 不编码
                } else {
                    code = "$string(" + code + ")";
                }                code = replaces[1] + code + replaces[2];

            }

            if (debug) {
                code = "$line=" + thisLine + ";" + code;
            }

            // 提取模板中的变量名
            forEach(getVariable(code), function (name) {

                // name 值可能为空，在安卓低版本浏览器下
                if (!name || uniq[name]) {
                    return;
                }

                var value;

                // 声明模板变量
                // 赋值优先级:
                // [include, print] > utils > helpers > data
                if (name === 'print') {
                    value = print;
                } else if (name === 'include') {
                    value = include;
                } else if (utils[name]) {
                    value = "$utils." + name;
                } else if (helpers[name]) {
                    value = "$helpers." + name;
                } else {
                    value = "$data." + name;
                }

                headerCode += name + "=" + value + ",";
                uniq[name] = true;            });
            return code + "\n";
        }
    }
    //原先還有nidejs和seajs的支持
    this.template = template;
})();
