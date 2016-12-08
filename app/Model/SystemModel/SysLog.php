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
    const SYSLOG_OPERATION_IMPORT   = 4;
    const SYSLOG_OPERATION_EXPORT   = 5;
    const SYSLOG_OPERATION_PRINT    = 6;
    const SYSLOG_OPERATION_CLEAR    = 7;

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

        return $strRes;
    }
}