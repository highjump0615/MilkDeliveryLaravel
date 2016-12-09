<?php

namespace App\Model\SystemModel;

use Illuminate\Database\Eloquent\Model;

/**
 * 系统日志模型
 * @package App\Model\SystemModel
 */
class SysLog extends Model
{
    public $table = 'systemlog';

    protected $fillable = [
        'user_id',
        'ipaddress',
        'page',
        'operation'
    ];

    const SYSLOG_OPERATION_ADD      = 1;
    const SYSLOG_OPERATION_REMOVE   = 2;
    const SYSLOG_OPERATION_EDIT     = 3;
    const SYSLOG_OPERATION_VIEW     = 4;
    const SYSLOG_OPERATION_INPUT    = 5;

    const SYSLOG_OPERATION_IMPORT   = 10;
    const SYSLOG_OPERATION_EXPORT   = 11;
    const SYSLOG_OPERATION_PRINT    = 12;

    const SYSLOG_OPERATION_CLEAR    = 20;
    const SYSLOG_OPERATION_STOPUSE  = 21;

    const SYSLOG_OPERATION_FINANCE  = 100;
    const SYSLOG_OPERATION_CHECK    = 200;  // 审核

    // 生产
    const SYSLOG_OPERATION_PRODUCE_OK       = 300;
    const SYSLOG_OPERATION_PRODUCE_CANCEL   = 301;
    const SYSLOG_OPERATION_SUBMIT_PLAN      = 302;

    /**
     * 用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\Model\UserModel\User');
    }

    public function getOperationName() {
        $strRes = '';

        if ($this->operation == SysLog::SYSLOG_OPERATION_ADD) {
            $strRes = '添加';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_REMOVE) {
            $strRes = '删除';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_EDIT) {
            $strRes = '修改';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_VIEW) {
            $strRes = '查看';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_INPUT) {
            $strRes = '录入';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_IMPORT) {
            $strRes = '导入';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_EXPORT) {
            $strRes = '导出';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_PRINT) {
            $strRes = '打印';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_CLEAR) {
            $strRes = '清理';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_FINANCE) {
            $strRes = '财务操作';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_CHECK) {
            $strRes = '审核';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_STOPUSE) {
            $strRes = '停用';
        }
        else if ($this->operation == SysLog::SYSLOG_OPERATION_SUBMIT_PLAN) {
            $strRes = '提交计划';
        }

        return $strRes;
    }
}