<template>
  <div class="row">
    <div class="column">
      <table>
        <tr>
          <th>GROSS PAY</th>
          <th>AMOUNT</th>
        </tr>
        <tr>
          <td>BASIC</td>
          <td>{{payslip.basic_pay}}</td>
        </tr>
        <tr>
          <td>Allowance</td>
          <td>
            <span v-if="payslip.allowance > 0">{{payslip.allowance}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>
            <b>OVERTIME</b>
          </td>
          <td>
            <span>-</span>
          </td>
        </tr>
        <tr>
          <td>No. of Hour/s</td>
          <td>
            <span v-if="payslip.ot_num_hours > 0">{{payslip.ot_num_hours}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Hour/s Pay</td>
          <td>
            <span v-if="payslip.ot_pay > 0">{{payslip.ot_pay}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Night Differential</td>
          <td>
            <span v-if="payslip.night_differential > 0">{{payslip.night_differential}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Refund</td>
          <td>
            <span v-if="payslip.refund > 0">{{payslip.refund}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>VL Conversion</td>
          <td>
            <span v-if="payslip.vl_conversion > 0">{{payslip.vl_conversion}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Adjustment</td>
          <td>
            <span v-if="payslip.adjustment > 0">{{payslip.adjustment}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>13th Month Pay</td>
          <td>
            <span v-if="payslip.thirteen_month_pay > 0">{{payslip.thirteen_month_pay}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Incentives</td>
          <td>
            <span v-if="payslip.incentives > 0">{{payslip.incentives}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td>TOTAL GROSS PAY</td>
          <td>
            <span v-if="payslip.total_gross_pay > 0">{{payslip.total_gross_pay}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td></td>
        </tr>
      </table>
    </div>
    <div class="column">
      <table>
        <tr>
          <th>DEDUCTION</th>
          <th>AMOUNT</th>
        </tr>
        <tr>
          <td>No. of Days Absent</td>
          <td>
            <span v-if="payslip.num_of_days_absent > 0">{{payslip.num_of_days_absent}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Absent Deduction</td>
          <td>
            <span v-if="payslip.absent_deduction > 0">{{payslip.absent_deduction}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>No. of minute/s late</td>
          <td>
            <span v-if="payslip.num_of_minute_late > 0">{{payslip.num_of_minute_late}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Late/Penalty</td>
          <td>
            <span v-if="payslip.late_deduction > 0">{{payslip.late_deduction}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Uniform</td>
          <td>
            <span v-if="payslip.uniform > 0">{{payslip.uniform}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>SSS</td>
          <td>
            <span v-if="payslip.sss > 0">{{payslip.sss}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>PHIC</td>
          <td>
            <span v-if="payslip.phic > 0">{{payslip.phic}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>HDMF</td>
          <td>
            <span v-if="payslip.hdmf > 0">{{payslip.hdmf}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>WTX (tax due)</td>
          <td>
            <span v-if="payslip.wtx > 0">{{payslip.wtx}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>SSS Loan</td>
          <td>
            <span v-if="payslip.sss_loan > 0">{{payslip.sss_loan}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>HDMF Loan</td>
          <td>
            <span v-if="payslip.hdmf_loan > 0">{{payslip.hdmf_loan}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Cellphone Charges</td>
          <td>
            <span v-if="payslip.cellphone_charges > 0">{{payslip.cellphone_charges}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Cash Advance</td>
          <td>
            <span v-if="payslip.cash_advance > 0">{{payslip.cash_advance}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Internet Charges</td>
          <td>
            <span v-if="payslip.internet_charges > 0">{{payslip.internet_charges}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>Other Deductions</td>
          <td>
            <span v-if="payslip.other_deduction > 0">{{payslip.other_deduction}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td>TOTAL DEDUCTION</td>
          <td>
            <span v-if="payslip.total_deduction > 0">{{payslip.total_deduction}}</span>
            <span v-else>-</span>
          </td>
        </tr>
        <tr>
          <td>
            <b>NET PAY</b>
          </td>
          <td>
            <span v-if="payslip.net_pay > 0">{{payslip.net_pay}}</span>
            <span v-else>-</span>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>
<script>
export default {
  props: ["payslip"],
  data() {
    return {};
  },

  created() {},
  mounted() {},
  updated() {},
  methods: {}
};
</script>
<style scoped>
.textLabel {
  margin-top: 9px;
  font-size: 15px;
}
.rowFields {
  margin-top: 15px;
}

table,
th,
td {
  text-align: left;
  padding: 8px;
}
tr:nth-child(even) {
  background-color: #f2f2f2;
}

th {
  background-color: #4caf50;
  color: white;
}

table {
  width: 100%;
  border-collapse: collapse;
}

.row {
  margin-left: -5px;
  margin-right: -5px;
}

.column {
  float: left;
  width: 50%;
  padding: 5px;
}

/* Clearfix (clear floats) */
.row::after {
  content: "";
  clear: both;
  display: table;
}
</style>
