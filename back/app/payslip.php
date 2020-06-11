<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class payslip extends Model
{
    protected $fillable = [
        'employee_id', 'pay_period_id', 'month_pay', 'tax_refund', 'vl_conversion', 'incentive',
        'other_bunos', 'uniform', 'sss', 'phic', 'hdmf', 'wtx', 'sss_loan', 'hdmf_loan',
        'cp_charge', 'cash_advance', 'other_deduction'
    ];
}
