<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Pay_period;

class payslip extends Model
{
    protected $fillable = [
        'employee_id', 'pay_period_id', 'basic_pay', 'allowance', 'ot_num_hours', 'ot_pay', 'night_differential',
        'refund', 'vl_conversion', 'adjustment', 'thirteen_month_pay',
        'incentives', 'total_gross_pay', 'num_of_days_absent', 'absent_deduction', 'num_of_minute_late',
        'late_deduction', 'uniform', 'sss', 'phic', 'hdmf', 'wtx', 'sss_loan', 'hdmf_loan',
        'cellphone_charges', 'cash_advance', 'internet_charges', 'other_deduction', 'total_deduction', 'net_pay'
    ];


    public function pay_period()
    {
        return $this->hasOne(Pay_period::class, 'id', 'pay_period_id');
    }

    //para sa relationship (payslip)
    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
}
