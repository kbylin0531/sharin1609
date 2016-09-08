L.P.modal = (function () {
    return {
        /**
         * 创建一个Modal对象,会将HTML中指定的内容作为自己的一部分拐走
         * @param selector 要把哪些东西添加到modal中的选择器
         * @param opt modal配置
         * @returns object
         */
        create: function (selector, opt) {
            var config = {
                title: "Window",
                confirmText: '提交',
                cancelText: '取消',
                //确认和取消的回调函数
                confirm: null,
                cancel: null,

                show: null,//即将显示
                shown: null,//显示完毕
                hide: null,//即将隐藏
                hidden: null,//隐藏完毕

                backdrop: "static",
                keyboard: true
            };
            opt && L.init(opt,config);

            var instance = L.NS(this),
                id = 'modal_' + L.guid(),
                modal = $('<div class="modal fade" id="' + id + '" aria-hidden="true" role="dialog"></div>'),
                dialog = $('<div class="modal-dialog"></div>'),
                header, content,body;

            if (typeof config['backdrop'] !== "string") config['backdrop'] = config['backdrop'] ? 'true' : 'false';
            $("body").append(modal.attr('data-backdrop', config['backdrop']).attr('data-keyboard', config['keyboard'] ? 'true' : 'false')) ;

            modal.append(dialog.append(content = $('<div class="modal-content"></div>')));

            //set header and body
            content.append(header = $('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>'))
                .append(body = $('<div class="modal-body"></div>').append(L.jq(selector).removeClass('hidden')));//suggest selector has class 'hidden'

            //设置足部
            content.append($('<div class="modal-footer"></div>').append(
                $('<button type="button" class="btn btn-sm _cancel" data-dismiss="modal">' + config['cancelText'] + '</button>').click(instance.cancel)
            ).append(
                $('<button type="button" class="btn btn-sm _confirm">' + config['confirmText'] + '</button>').click(instance.confirm)
            ));

            //确认和取消事件注册
            instance.target = modal.modal('hide');

            config['title'] && instance.title(config['title']);
            //事件注册
            U.each(['show', 'shown', 'hide', 'hidden'], function (eventname) {
                modal.on(eventname + '.bs.modal', function () {
                    //handle the element size change while window resizedntname,config[eventname]);
                    config[eventname] && (config[eventname])();
                });
            });
            return instance;
        },
        //get the element of this.target while can not found in global jquery selector
        getElement: function (selector){
            return this.target.find(selector);
        },
        onConfirm: function (callback){
            this.target.find("._confirm").unbind("click").click(callback);
            return this;
        },
        onCancel: function (callback){
            this.target.find("._cancel").unbind("click").click(callback);
            return this;
        },
        //update title
        title: function (newtitle) {
            var title = this.target.find(".modal-title");
            if (!title.length) {
                var h = L.NE('h4.modal-title');
                h.innerHTML = newtitle;
                this.target.find(".modal-header").append(h);
            }
            title.text(newtitle);
            return this;
        },
        show: function () {
            this.target.modal('show');
            return this;
        },
        hide: function () {
            this.target.modal('hide');
            return this;
        }
    };
})();