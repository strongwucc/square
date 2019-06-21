<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class UserLock
{
    protected $id;
    protected $user;

    public function __construct($id,$user)
    {
        $this->id = $id;
        $this->user = $user;
    }

    protected function script()
    {
        return <<<SCRIPT

        $('.grid-user-lock').on('click', function () {
            
            var id = $(this).data('id');
            var action = $(this).data('action');
            var title = action === 'lock' ? '确定冻结该会员？' : '确定解冻该会员';

            swal({
                title: title,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确定",
                showLoaderOnConfirm: true,
                cancelButtonText: "取消",
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        $.ajax({
                            method: 'post',
                            url: 'members/lock',
                            data: {
                                id:id,
                                action:action,
                                _method:'post',
                                _token:LA.token,
                            },
                            success: function (data) {
                                $.pjax.reload('#pjax-container');
        
                                resolve(data);
                            }
                        });
                    });
                }
            }).then(function(result) {
                var data = result.value;
                if (typeof data === 'object') {
                    if (data.status) {
                        swal(data.message, '', 'success');
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            });
        
        });

SCRIPT;
    }

    protected function render()
    {
//        Admin::html($this->html());
        Admin::script($this->script());

        $user = $this->user;
        if ($user['disabled'] == 'true') {
            return "<button class='btn btn-xs btn-warning grid-user-lock' data-id='{$this->id}' data-action='unlock'>取消冻结</button>";
        }

        return "<button class='btn btn-xs btn-warning grid-user-lock' data-id='{$this->id}' data-action='lock'>冻结会员</button>";

    }

    public function __toString()
    {
        return $this->render();
    }
}