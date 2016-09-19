<extend file="../Common/base.html" />
<block name="body">
    <div class="container">
        <br>
        <table id="dtable" class="table table-striped table-bordered" cellspacing="0" width="100%"></table>
    </div>
</block>
<block name="style">
    <style>
        .img { height:100px; width: 100px; }
    </style>
</block>
<block name="script">
    <script>
        var date = new Date();
        L.P.load('jq').load('bs').load('dt',function () {
            var dtable = L.P.datatables.create('#dtable',{
                columns: [
                    {
                        title:'product id',
                        data:'pid'
                    },
                    {
                        title:'pname',
                        data:'name'
                    },
                    {
                        title:'url',
                        data:function (row) {
                            return '<a target="_blank" href="'+row.url+'" >'+row.url+'</a>';
                        }
                    },
                    {
                        title:'atime',
                        data:function (row) {
                            return date.format("yyyy-MM-dd",row.atime);
                        }
                    },
                    {
                        title:'platform',
                        data:'platform'
                    },
                    {
                        title:'type',
                        data:function (row) {
                            return parseInt(row.type)?'成功':'失败';
                        }
                    },
                    {
                        title:'uname',
                        data:'uname'
                    },
                    {
                        title:'image',
                        data:function (row) {
                            return '<img src="'+row.image+'" class="img"/>';
                        }
                    }
                ]
            }).load(L.O.toObj('{$data}'));
        });
    </script>
</block>