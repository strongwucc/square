<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class EditScore
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
        
        var point = 0;
        var id = '';

        $('.grid-edit-score').on('click', function () {
            
            id = $(this).data('id');
            
            $.ajax({
                method: 'post',
                url: 'members/score',
                data: {
                    id:id,
                    _method:'post',
                    _token:LA.token,
                },
                success: function (data) {
                    if (data.status === true) {
                        point = parseInt(data.data.point);
                        $('#editScoreModal .member-point').text(point);
                        $('#editScoreModal').modal();
                    } else {
                        swal('积分查询失败', '', 'error');
                    }
                }
            });
            
//            $('#editScoreModal').modal();
//            console.log(id)
        
        });
        
        $('#submitEditScore').on('click', function () {
            var edit_score = parseInt($('#editScoreModal #inputScore').val());
            if (point + edit_score < 0) {
                swal('积分扣除不能超过已有积分', '', 'error');
                return false;
            }
            
            var mark = $('#editScoreModal #inputMark').val();
            
            $.ajax({
                method: 'post',
                url: 'members/score_edit',
                data: {
                    id:id,
                    score:edit_score,
                    mark:mark,
                    _method:'post',
                    _token:LA.token,
                },
                success: function (data) {
                    if (data.status === true) {
                        $('#editScoreModal').modal('hide');
                        $.pjax.reload('#pjax-container');
                    } else {
                        swal('积分修改失败', '', 'error');
                    }
                }
            });
        })

SCRIPT;
    }

    protected function render()
    {
//        Admin::html($this->html());
        Admin::script($this->script());

        $html = <<<HTML
            &nbsp;&nbsp;<button class='btn btn-xs btn-primary grid-edit-score' data-id='{$this->id}' data-action='lock'>修改会员积分</button>
            <div class="modal fade" id="editScoreModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">修改会员积分</h4>
                  </div>
                  <div class="modal-body">
                    <form class="form-horizontal">
                      <div class="form-group">
                        <label class="col-sm-4 control-label">会员原有积分</label>
                        <div class="col-sm-8">
                          <p class="form-control-static member-point"></p>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputScore" class="col-sm-4 control-label">调整积分(增加/减少)</label>
                        <div class="col-sm-8">
                          <input type="number" class="form-control" id="inputScore" placeholder="输入负值即可减少积分">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputMark" class="col-sm-4 control-label">备注</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="inputMark" placeholder="请输入备注">
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button id="submitEditScore" type="button" class="btn btn-primary">提交</button>
                  </div>
                </div>
              </div>
            </div>
HTML;


        return $html;

    }

    public function __toString()
    {
        return $this->render();
    }
}