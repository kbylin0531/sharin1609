<extend file="../Common/base.html" />
<block name="body">
    <div class="container">
        <br>
        <div class="toolbar">
            <button type="button" id="publish" class="btn btn-default">publish</button>

        </div>
        <br>
        <table id="dtable" class="table table-striped table-bordered" cellspacing="0" width="100%"></table>
    </div>
</block>
<block name="script">
    <script>
        L.P.load('jq').load('bs').load('dt',function () {
            var dtable = L.P.datatables.create('#dtable',{
                columns: [
                    {
                        title:'product id',
                        data:'pid'
                    },
                    {
                        title:'name',
                        data:'name'
                    },
                    {
                        title:'describe',
                        data:'describe'
                    },
                    {
                        title:'keywork',
                        data:'keywork'
                    },
                    {
                        title:'image',
                        data:function (row) {
                            return '<img src="'+row.image+'" />';
                        }
                    },
                    {
                        title:'operation',
                        data:function () {
                            return '<a href="javascript:void;" class="publink">publish</a>';
                        }
                    }
                ]
            }).load(L.O.toObj('{$data}'));

            $(".publink").click(function () {
                var row = dtable.data($(this).closest("tr"));
//                console.log(row);return ;
                $.post('__ACTION__',row,function (data) {
//                    console.log(data);
                    alert(data.message);
                });
            });

            $("#publish").click(function () {

            });
        });
    </script>
</block>